<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Loan::class);

        $user = Auth::user();

        if ($user->isDriver()) {
            $loan = Loan::where('driver_id', $user->id)->latest()->first();

            if (!$loan) {
                return view('loans.show', ['loan' => null, 'schedule' => []]);
            }

            return redirect()->route('loans.show', $loan);
        }

        $query = Loan::with('motorcycle', 'driver');

        if ($user->isOwner()) {
            $query->where('owner_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('motorcycle', function ($mq) use ($search) {
                    $mq->where('plate_number', 'like', "%{$search}%");
                })->orWhereHas('driver', function ($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $loans = $query->paginate(10)->withQueryString();

        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $this->authorize('create', Loan::class);

        $user = Auth::user();

        $motorcycles = Motorcycle::where('status', 'assigned')
            ->whereDoesntHave('loan', function ($q) {
                $q->whereIn('status', ['active', 'pending', 'overdue']);
            })
            ->when($user->isOwner(), fn($q) => $q->where('owner_id', $user->id))
            ->with('driver')
            ->get();

        return view('loans.create', compact('motorcycles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Loan::class);

        $validated = $request->validate([
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'total_amount' => 'required|numeric|min:1',
            'weekly_installment' => 'required|numeric|min:1',
            'duration_weeks' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        $motorcycle = Motorcycle::findOrFail($validated['motorcycle_id']);

        if (!$motorcycle->driver_id) {
            return back()->withErrors(['motorcycle_id' => 'This motorcycle has no driver assigned yet.'])->withInput();
        }

        if (Loan::driverHasActiveLoan($motorcycle->driver_id)) {
            return back()->withErrors(['motorcycle_id' => 'This driver already has an active loan. Complete it first.'])->withInput();
        }

        $loan = Loan::create([
            'motorcycle_id' => $motorcycle->id,
            'owner_id' => $motorcycle->owner_id,
            'driver_id' => $motorcycle->driver_id,
            'total_amount' => $validated['total_amount'],
            'weekly_installment' => $validated['weekly_installment'],
            'amount_paid' => 0,
            'duration_weeks' => $validated['duration_weeks'],
            'start_date' => $validated['start_date'],
            'end_date' => now()->parse($validated['start_date'])->addWeeks($validated['duration_weeks']),
            'next_payment_date' => now()->parse($validated['start_date'])->addWeek(),
            'status' => 'pending',
        ]);

        $contract = $motorcycle->contract;
        if ($contract) {
            $contract->update(['loan_id' => $loan->id]);
        }

        UserNotification::createNotification(
            $motorcycle->driver_id,
            'loan_created',
            'New Loan Agreement',
            "A new loan agreement has been created for motorcycle {$motorcycle->plate_number}. Please review and accept the agreement.",
            ['loan_id' => $loan->id]
        );

        $driver = $motorcycle->driver;
        if ($driver && $driver->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendLoanCreated($driver->phone, $driver->name, $motorcycle->plate_number);
            } catch (\Exception $e) {
                Log::error('Failed to send loan created SMS', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('loans.index')->with('success', 'Loan created successfully. The driver must accept the agreement before it becomes active.');
    }

    public function show(Loan $loan)
    {
        $this->authorize('view', $loan);

        $loan->load(['motorcycle', 'driver', 'owner', 'contract', 'payments' => function ($q) {
            $q->latest('payment_date');
        }]);

        $schedule = [];
        if ($loan->start_date) {
            $date = $loan->start_date->copy();
            $paidWeeks = $loan->payments()->where('status', 'verified')->count();
            for ($week = 1; $week <= $loan->duration_weeks; $week++) {
                $schedule[] = [
                    'week' => $week,
                    'due_date' => $date->copy(),
                    'amount' => $loan->weekly_installment,
                    'status' => $week <= $paidWeeks ? 'paid' : ($date->isPast() ? 'overdue' : 'upcoming'),
                ];
                $date->addWeek();
            }
        }

        return view('loans.show', compact('loan', 'schedule'));
    }

    public function acceptAgreement(Loan $loan)
    {
        $this->authorize('accept', $loan);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'This loan agreement is not pending acceptance.');
        }

        $contract = app(ContractController::class)->generate($loan);

        UserNotification::createNotification(
            $loan->owner_id,
            'agreement_accepted',
            'Contract Ready',
            "A contract has been generated for {$loan->motorcycle->plate_number}. Please review and sign.",
            ['loan_id' => $loan->id]
        );

        return redirect()->route('contracts.show', $loan)->with('success', 'Contract generated. Both parties must upload signed copies and an admin must approve.');
    }

    public function completeLoan(Loan $loan)
    {
        $this->authorize('complete', $loan);

        if ($loan->status !== 'active') {
            return back()->with('error', 'Only active loans can be completed.');
        }

        if ($loan->balance > 0) {
            return back()->with('error', 'This loan still has an outstanding balance.');
        }

        $loan->update([
            'status' => 'completed',
            'ownership_certificate_generated' => true,
        ]);

        $motorcycle = $loan->motorcycle;
        $motorcycle->update([
            'status' => 'completed',
        ]);

        UserNotification::createNotification(
            $loan->driver_id,
            'loan_completed',
            'Loan Completed',
            "Congratulations! Your loan for motorcycle {$motorcycle->plate_number} has been completed. You now own this motorcycle.",
            ['loan_id' => $loan->id]
        );

        $driver = $loan->driver;
        if ($driver && $driver->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendLoanCompleted($driver->phone, $driver->name, $motorcycle->plate_number);
            } catch (\Exception $e) {
                Log::error('Failed to send loan completed SMS to driver', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);
            }
        }

        UserNotification::createNotification(
            $loan->owner_id,
            'loan_completed',
            'Loan Completed',
            "The loan for motorcycle {$motorcycle->plate_number} assigned to driver {$driver->name} has been completed. Ownership certificate is ready.",
            ['loan_id' => $loan->id]
        );

        $owner = $loan->owner;
        if ($owner && $owner->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendLoanCompleted($owner->phone, $owner->name, $motorcycle->plate_number);
            } catch (\Exception $e) {
                Log::error('Failed to send loan completed SMS to owner', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('loans.show', $loan)->with('success', 'Loan completed. Motorcycle ownership transferred.');
    }

    public function ownershipCertificate(Loan $loan)
    {
        $this->authorize('viewCertificate', $loan);

        if (!$loan->isCompleted()) {
            return back()->with('error', 'Ownership certificate is only available for completed loans.');
        }

        $loan->load(['motorcycle', 'driver', 'owner', 'payments' => function ($q) {
            $q->where('status', 'verified')->latest('payment_date');
        }]);

        return view('loans.certificate', compact('loan'));
    }

    public function reportAbsconded(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'absconded_reason' => 'required|string|max:1000',
        ]);

        $loan->update([
            'status' => 'defaulted',
            'absconded_at' => now(),
            'absconded_by' => $user->id,
            'absconded_reason' => $request->absconded_reason,
        ]);

        $motorcycle = $loan->motorcycle;
        if ($motorcycle) {
            $motorcycle->update([
                'status' => 'stolen',
                'stolen_at' => now(),
                'stolen_notes' => "Reported by {$user->name}: {$request->absconded_reason}",
            ]);
        }

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            UserNotification::createNotification(
                $admin->id,
                'vehicle_stolen',
                'URGENT: Bodaboda Stolen',
                "Driver {$loan->driver->name} ({$loan->driver->phone}) has absconded with {$motorcycle->plate_number} ({$motorcycle->make} {$motorcycle->model}). Reported by {$user->name}. Reason: {$request->absconded_reason}",
                ['loan_id' => $loan->id, 'motorcycle_id' => $motorcycle->id, 'absconded_by' => $user->id]
            );
        }

        if ($loan->owner_id !== $user->id) {
            UserNotification::createNotification(
                $loan->owner_id,
                'vehicle_stolen',
                'URGENT: Your Bodaboda Stolen',
                "Driver {$loan->driver->name} ({$loan->driver->phone}) has absconded with your {$motorcycle->plate_number} ({$motorcycle->make} {$motorcycle->model}). Reason: {$request->absconded_reason}. Track the vehicle on the GPS map.",
                ['loan_id' => $loan->id, 'motorcycle_id' => $motorcycle->id]
            );
        }

        if ($loan->driver_id) {
            UserNotification::createNotification(
                $loan->driver_id,
                'loan_defaulted',
                'Loan Defaulted — Vehicle Reported Stolen',
                "Your loan for {$motorcycle->plate_number} has been reported as defaulted. The vehicle has been marked as stolen. Contact the platform immediately to resolve this matter.",
                ['loan_id' => $loan->id]
            );
        }

        try {
            $sms = app(\App\Services\SmsService::class);
            if ($loan->driver->phone) {
                $sms->sendMessage($loan->driver->phone, "URGENT: Your loan for {$motorcycle->plate_number} has been defaulted and the vehicle reported stolen. Contact BodaLink immediately.");
            }
        } catch (\Exception $e) {
            Log::error('Failed to send absconded SMS', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', "Vehicle {$motorcycle->plate_number} reported as stolen. GPS tracking is active. All admins and the owner have been notified.");
    }

    public function recoverVehicle(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'recovery_notes' => 'required|string|max:1000',
        ]);

        $loan->update([
            'status' => 'overdue',
            'recovered_at' => now(),
            'recovery_notes' => $request->recovery_notes,
            'absconded_at' => null,
            'absconded_by' => null,
            'absconded_reason' => null,
        ]);

        $motorcycle = $loan->motorcycle;
        if ($motorcycle) {
            $motorcycle->update([
                'status' => 'assigned',
                'stolen_at' => null,
                'stolen_notes' => null,
            ]);
        }

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            UserNotification::createNotification(
                $admin->id,
                'vehicle_recovered',
                'Bodaboda Recovered',
                "{$motorcycle->plate_number} has been recovered. Reported by {$user->name}. Notes: {$request->recovery_notes}",
                ['loan_id' => $loan->id, 'motorcycle_id' => $motorcycle->id]
            );
        }

        if ($loan->owner_id !== $user->id) {
            UserNotification::createNotification(
                $loan->owner_id,
                'vehicle_recovered',
                'Your Bodaboda Recovered',
                "Your {$motorcycle->plate_number} has been recovered. Notes: {$request->recovery_notes}",
                ['loan_id' => $loan->id, 'motorcycle_id' => $motorcycle->id]
            );
        }

        return redirect()->back()->with('success', "Vehicle {$motorcycle->plate_number} marked as recovered. Loan is back to overdue status.");
    }
}
