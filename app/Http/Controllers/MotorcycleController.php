<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MotorcycleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Motorcycle::class);

        $query = Motorcycle::with('owner', 'driver', 'loan');

        if (Auth::user()->isOwner()) {
            $query->where('owner_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhereHas('driver', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('owner', function ($oq) use ($search) {
                        $oq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $motorcycles = $query->paginate(10)->withQueryString();

        $owners = \App\Models\User::where('role', 'owner')->orderBy('name')->get();

        return view('motorcycles.index', compact('motorcycles', 'owners'));
    }

    public function create()
    {
        $this->authorize('create', Motorcycle::class);

        $drivers = User::where('role', 'driver')->where('approval_status', 'approved')->get();
        $owners = Auth::user()->isAdmin() ? User::where('role', 'owner')->where('approval_status', 'approved')->get() : collect();

        return view('motorcycles.create', compact('drivers', 'owners'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Motorcycle::class);

        $validated = $request->validate([
            'plate_number' => 'required|string|unique:motorcycles,plate_number',
            'model' => 'nullable|string|max:100',
            'make' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'color' => 'nullable|string|max:50',
            'engine_cc' => 'nullable|string|max:10',
            'engine_number' => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'weekly_amount' => 'nullable|numeric|min:0',
            'loan_amount' => 'nullable|numeric|min:0',
            'loan_duration_weeks' => 'nullable|integer|min:1',
            'registration_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'insurance' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $validated['owner_id'] = Auth::user()->isOwner() ? Auth::id() : $validated['owner_id'];
        $validated['status'] = 'available';
        $validated['verification_status'] = 'verified';

        foreach (['registration_card', 'insurance', 'photo'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('motorcycles', 'public');
            }
        }

        $motorcycle = Motorcycle::create($validated);

        $contract = Contract::create([
            'motorcycle_id' => $motorcycle->id,
            'contract_number' => 'CTR-TMP-' . strtoupper(Str::random(8)),
            'status' => 'draft',
        ]);

        $contract->update([
            'contract_number' => 'CTR-' . now()->format('Y') . '-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT),
        ]);

        return redirect()->route('motorcycles.index')->with('success', 'Motorcycle registered successfully.');
    }

    public function show(Motorcycle $motorcycle)
    {
        $this->authorize('view', $motorcycle);
        $motorcycle->load(['owner', 'driver', 'loan.payments']);

        return view('motorcycles.show', compact('motorcycle'));
    }

    public function edit(Motorcycle $motorcycle)
    {
        $this->authorize('update', $motorcycle);

        $drivers = User::where('role', 'driver')->where('approval_status', 'approved')->get();

        return view('motorcycles.edit', compact('motorcycle', 'drivers'));
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        $this->authorize('update', $motorcycle);

        $validated = $request->validate([
            'model' => 'required|string|max:100',
            'make' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'color' => 'required|string|max:50',
            'engine_cc' => 'required|string|max:10',
            'engine_number' => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'weekly_amount' => 'nullable|numeric|min:0',
            'loan_amount' => 'nullable|numeric|min:0',
            'loan_duration_weeks' => 'nullable|integer|min:1',
            'status' => 'required|in:available,assigned,completed,suspended',
            'registration_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'insurance' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        foreach (['registration_card', 'insurance', 'photo'] as $field) {
            if ($request->hasFile($field)) {
                if ($motorcycle->$field) {
                    Storage::disk('public')->delete($motorcycle->$field);
                }
                $validated[$field] = $request->file($field)->store('motorcycles', 'public');
            }
        }

        $motorcycle->update($validated);

        return redirect()->route('motorcycles.index')->with('success', 'Motorcycle updated successfully.');
    }

    public function destroy(Motorcycle $motorcycle)
    {
        $this->authorize('delete', $motorcycle);

        foreach (['registration_card', 'insurance', 'photo'] as $field) {
            if ($motorcycle->$field) {
                Storage::disk('public')->delete($motorcycle->$field);
            }
        }

        $motorcycle->delete();

        return redirect()->route('motorcycles.index')->with('success', 'Motorcycle deleted successfully.');
    }

    public function assign(Motorcycle $motorcycle, Request $request)
    {
        $this->authorize('assign', $motorcycle);

        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $driver = User::findOrFail($validated['driver_id']);

        if ($driver->assignedMotorcycle && $driver->assignedMotorcycle->id !== $motorcycle->id) {
            return back()->withErrors(['driver_id' => 'This driver is already assigned to another motorcycle.'])->withInput();
        }

        $motorcycle->update([
            'driver_id' => $validated['driver_id'],
            'status' => 'assigned',
        ]);

        UserNotification::createNotification(
            $validated['driver_id'],
            'motorcycle_assigned',
            'Motorcycle Assigned',
            "You have been assigned motorcycle {$motorcycle->plate_number}.",
            ['motorcycle_id' => $motorcycle->id]
        );

        if ($driver->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendLoanCreated($driver->phone, $driver->name, $motorcycle->plate_number);
            } catch (\Exception $e) {
                Log::error('Failed to send assignment SMS', [
                    'driver_id' => $driver->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($motorcycle->loan_amount && $motorcycle->loan_duration_weeks) {
            if (Loan::driverHasActiveLoan($driver->id)) {
                return back()->withErrors(['driver_id' => 'This driver already has an active loan.'])->withInput();
            }

            $weeklyInstallment = round($motorcycle->loan_amount / $motorcycle->loan_duration_weeks, 2);
            $startDate = now()->toDateString();
            $endDate = now()->addWeeks($motorcycle->loan_duration_weeks)->toDateString();

            $loan = Loan::create([
                'motorcycle_id' => $motorcycle->id,
                'owner_id' => $motorcycle->owner_id,
                'driver_id' => $driver->id,
                'total_amount' => $motorcycle->loan_amount,
                'weekly_installment' => $weeklyInstallment,
                'amount_paid' => 0,
                'duration_weeks' => $motorcycle->loan_duration_weeks,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'next_payment_date' => now()->addWeek()->toDateString(),
                'status' => 'pending',
            ]);

            $contract = $motorcycle->contract;
            if ($contract) {
                $contract->update(['loan_id' => $loan->id]);
            }

            UserNotification::createNotification(
                $driver->id,
                'loan_created',
                'New Loan Agreement',
                "A loan of TZS " . number_format($motorcycle->loan_amount) . " over {$motorcycle->loan_duration_weeks} weeks has been created for motorcycle {$motorcycle->plate_number}. Weekly installment: TZS " . number_format($weeklyInstallment) . ". Please review and accept the agreement.",
                ['loan_id' => $loan->id]
            );

            if ($driver->phone) {
                try {
                    $sms = app(SmsService::class);
                    $sms->sendLoanCreated($driver->phone, $driver->name, $motorcycle->plate_number);
                } catch (\Exception $e) {
                    Log::error('Failed to send loan created SMS', [
                        'loan_id' => $loan->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return redirect()->route('motorcycles.show', $motorcycle)->with('success', 'Driver assigned successfully.');
    }

    // ── Owner-specific methods ──

    public function ownerIndex(Request $request)
    {
        $user = Auth::user();
        $query = Motorcycle::where('owner_id', $user->id)->with('driver', 'loan', 'applications', 'contract');

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        $motorcycles = $query->latest()->paginate(12);
        return view('owner.vehicles', compact('motorcycles'));
    }

    public function ownerCreate()
    {
        return view('owner.create-vehicle');
    }

    public function ownerStore(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:motorcycles,plate_number',
            'make' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'color' => 'nullable|string|max:50',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_name' => 'required|string|max:255',
            'loan_amount' => 'required|numeric|min:1',
            'weekly_amount' => 'required|numeric|min:1',
            'loan_duration_weeks' => 'required|integer|min:1',
            'photo' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $validated['owner_id'] = Auth::id();
        $validated['model'] = $validated['make'];
        $validated['status'] = 'available';
        $validated['verification_status'] = 'verified';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('motorcycles', 'public');
        }

        Motorcycle::create($validated);

        return redirect()->route('owner.vehicles')->with('success', 'Bodaboda registered and now visible on the marketplace.');
    }

    public function ownerShow(Motorcycle $motorcycle)
    {
        if ($motorcycle->owner_id !== Auth::id()) {
            abort(403);
        }
        $motorcycle->load('driver', 'loan.payments');
        $loan = $motorcycle->loan;
        $applications = \App\Models\Application::where('motorcycle_id', $motorcycle->id)
            ->with('driver')
            ->latest()
            ->get();

        return view('owner.vehicle-show', compact('motorcycle', 'loan', 'applications'));
    }

    public function ownerEdit(Motorcycle $motorcycle)
    {
        if ($motorcycle->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('owner.edit-vehicle', compact('motorcycle'));
    }

    public function ownerUpdate(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'plate_number' => 'required|string|unique:motorcycles,plate_number,' . $motorcycle->id,
            'make' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'color' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'engine_cc' => 'nullable|string|max:10',
            'engine_number' => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'gps_device_id' => 'nullable|string|max:100',
            'weekly_amount' => 'nullable|numeric|min:0',
            'loan_amount' => 'nullable|numeric|min:0',
            'loan_duration_weeks' => 'nullable|integer|min:1',
            'registration_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'insurance' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        foreach (['registration_card', 'insurance', 'photo'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('motorcycles', 'public');
            }
        }

        $motorcycle->update($validated);

        return redirect()->route('owner.vehicles.show', $motorcycle)->with('success', 'Bodaboda updated successfully.');
    }

    public function ownerDestroy(Motorcycle $motorcycle)
    {
        if ($motorcycle->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($motorcycle->status === 'assigned' || $motorcycle->status === 'completed') {
            return redirect()->route('owner.vehicles')->with('error', 'Cannot delete a bodaboda that is assigned to a driver or completed.');
        }

        $hasSignedContract = \App\Models\Contract::where('motorcycle_id', $motorcycle->id)
            ->whereNotNull('owner_signed_at')
            ->exists();

        if ($hasSignedContract) {
            return redirect()->route('owner.vehicles')->with('error', 'Cannot delete a bodaboda with a signed contract.');
        }

        $motorcycle->delete();

        return redirect()->route('owner.vehicles')->with('success', 'Bodaboda removed successfully.');
    }

    public function ownerAcceptApplication(Motorcycle $motorcycle, \App\Models\Application $application)
    {
        if ($motorcycle->owner_id !== Auth::id()) {
            abort(403);
        }
        if ($application->motorcycle_id !== $motorcycle->id) {
            abort(404);
        }
        if ($application->status !== 'pending') {
            return redirect()->route('owner.vehicles.show', $motorcycle)->with('error', 'This application has already been processed.');
        }

        if (\App\Models\Loan::driverHasActiveLoan($application->driver_id)) {
            return redirect()->route('owner.vehicles.show', $motorcycle)->with('error', 'This driver already has an active loan on another bodaboda. They must complete it first.');
        }

        $application->update(['status' => 'approved']);

        $driver = $application->driver;
        $motorcycle->update([
            'driver_id' => $driver->id,
            'status' => 'assigned',
        ]);

        Application::where('driver_id', $driver->id)
            ->where('id', '!=', $application->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        if ($motorcycle->loan_amount && $motorcycle->loan_duration_weeks) {
            $weeklyInstallment = round($motorcycle->loan_amount / $motorcycle->loan_duration_weeks, 2);
            $startDate = now()->toDateString();
            $endDate = now()->addWeeks($motorcycle->loan_duration_weeks)->toDateString();

            $loan = \App\Models\Loan::create([
                'motorcycle_id' => $motorcycle->id,
                'owner_id' => $motorcycle->owner_id,
                'driver_id' => $driver->id,
                'total_amount' => $motorcycle->loan_amount,
                'weekly_installment' => $weeklyInstallment,
                'amount_paid' => 0,
                'duration_weeks' => $motorcycle->loan_duration_weeks,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'next_payment_date' => now()->addWeek()->toDateString(),
                'status' => 'pending',
            ]);

            $contractController = new \App\Http\Controllers\ContractController();
            $contractController->generate($loan);

            \App\Models\UserNotification::createNotification(
                $driver->id,
                'application_approved',
                'Application Accepted!',
                "Your application for {$motorcycle->make} {$motorcycle->model} ({$motorcycle->plate_number}) has been accepted by the owner. A contract has been created — review and sign it to start your loan.",
                ['loan_id' => $loan->id, 'motorcycle_id' => $motorcycle->id]
            );

            \App\Models\UserNotification::createNotification(
                $motorcycle->owner_id,
                'application_accepted',
                'Driver Accepted',
                "You have accepted driver {$driver->name} for {$motorcycle->plate_number}. A contract has been created for both parties to sign.",
                ['loan_id' => $loan->id, 'driver_id' => $driver->id]
            );
        }

        return redirect()->route('owner.vehicles.show', $motorcycle)->with('success', "Application from {$driver->name} accepted! Contract has been generated.");
    }

    public function ownerRejectApplication(Motorcycle $motorcycle, \App\Models\Application $application)
    {
        if ($motorcycle->owner_id !== Auth::id()) {
            abort(403);
        }
        if ($application->motorcycle_id !== $motorcycle->id) {
            abort(404);
        }
        if ($application->status !== 'pending') {
            return redirect()->route('owner.vehicles.show', $motorcycle)->with('error', 'This application has already been processed.');
        }

        $application->update(['status' => 'rejected']);

        \App\Models\UserNotification::createNotification(
            $application->driver_id,
            'application_rejected',
            'Application Not Accepted',
            "Your application for {$motorcycle->make} {$motorcycle->model} ({$motorcycle->plate_number}) was not accepted by the owner.",
            ['motorcycle_id' => $motorcycle->id]
        );

        return redirect()->route('owner.vehicles.show', $motorcycle)->with('success', 'Application rejected.');
    }
}
