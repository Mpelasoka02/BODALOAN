<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function approve(User $user)
    {
        $this->authorize('approve', $user);

        $user->update(['approval_status' => 'approved', 'rejection_reason' => null]);

        UserNotification::createNotification(
            $user->id,
            'account_approved',
            'Account Approved',
            'Your account has been approved. You can now access the system.'
        );

        if ($user->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendAccountApproved($user->phone, $user->name);
            } catch (\Exception $e) {
                Log::error('Failed to send approval SMS', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->back()->with('success', "Account for {$user->name} has been approved.");
    }

    public function suspend(Request $request, User $user)
    {
        $this->authorize('suspend', $user);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user->update([
            'approval_status' => 'suspended',
            'rejection_reason' => $request->rejection_reason,
        ]);

        UserNotification::createNotification(
            $user->id,
            'account_suspended',
            'Account Suspended',
            'Your account has been suspended. Reason: ' . $request->rejection_reason
        );

        if ($user->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendAccountSuspended($user->phone, $user->name, $request->rejection_reason);
            } catch (\Exception $e) {
                Log::error('Failed to send suspension SMS', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->back()->with('success', "Account for {$user->name} has been suspended.");
    }

    public function toggleActive(User $user)
    {
        $this->authorize('update', $user);

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot disable admin accounts.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'enabled' : 'disabled';

        UserNotification::createNotification(
            $user->id,
            'account_' . $status,
            'Account ' . ucfirst($status),
            "Your account has been {$status}."
        );

        return redirect()->back()->with('success', "Account for {$user->name} has been {$status}.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect()->back()->with('success', "Password reset for {$user->name}.");
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete admin accounts.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,owner,driver',
            'password' => 'required|min:8',
            'approval_status' => 'required|in:pending,approved',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'approval_status' => $validated['approval_status'],
            'email_verified_at' => now(),
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users')->with('success', "User {$user->name} has been created.");
    }

    // =============================================
    // VEHICLE VERIFICATION
    // =============================================

    public function vehicles(Request $request)
    {
        $query = Motorcycle::with('owner', 'loan');

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        } else {
            $query->where('verification_status', 'pending_verification');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();

        return view('admin.vehicles', compact('vehicles'));
    }

    public function vehicleReview(Motorcycle $motorcycle)
    {
        $motorcycle->load('owner', 'driver', 'loan.payments');
        $loan = $motorcycle->loan;

        return view('admin.vehicle-review', compact('motorcycle', 'loan'));
    }

    public function verifyVehicle(Motorcycle $motorcycle)
    {
        $motorcycle->update([
            'verification_status' => 'verified',
            'verification_notes' => null,
        ]);

        UserNotification::createNotification(
            $motorcycle->owner_id,
            'vehicle_verified',
            'Bodaboda Imethibitishwa',
            "Bodaboda yako {$motorcycle->plate_number} imekaguliwa na kuthibitishwa. Sasa inaonekana kwa madereva.",
            ['motorcycle_id' => $motorcycle->id]
        );

        return redirect()->back()->with('success', "Vehicle {$motorcycle->plate_number} verified successfully.");
    }

    public function rejectVehicle(Request $request, Motorcycle $motorcycle)
    {
        $request->validate([
            'verification_notes' => 'required|string|max:1000',
        ]);

        $motorcycle->update([
            'verification_status' => 'rejected',
            'verification_notes' => $request->verification_notes,
        ]);

        UserNotification::createNotification(
            $motorcycle->owner_id,
            'vehicle_rejected',
            'Bodaboda Imekataliwa',
            "Bodaboda yako {$motorcycle->plate_number} imekataliwa. Sababu: {$request->verification_notes}. Tafadhali rekebisha na wasilisha tena.",
            ['motorcycle_id' => $motorcycle->id]
        );

        return redirect()->back()->with('success', "Vehicle {$motorcycle->plate_number} rejected.");
    }

    // =============================================
    // DRIVER APPLICATIONS
    // =============================================

    public function applications(Request $request)
    {
        $query = Application::with('motorcycle', 'driver');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        return view('admin.applications', compact('applications'));
    }

    public function reviewApplication(Request $request, Application $application)
    {
        $request->validate([
            'action' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status' => $request->action,
            'admin_notes' => $request->admin_notes,
        ]);

        if ($request->action === 'approved') {
            $motorcycle = $application->motorcycle;
            $driver = $application->driver;

            if (Loan::driverHasActiveLoan($driver->id)) {
                return redirect()->back()->with('error', 'This driver already has an active loan on another bodaboda. They must complete it first.');
            }

            $motorcycle->update([
                'driver_id' => $driver->id,
                'status' => 'assigned',
            ]);

            Application::where('driver_id', $driver->id)
                ->where('id', '!=', $application->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            UserNotification::createNotification(
                $driver->id,
                'application_approved',
                'Application Approved',
                "Your application for motorcycle {$motorcycle->plate_number} has been approved.",
                ['motorcycle_id' => $motorcycle->id]
            );

            if ($motorcycle->loan_amount && $motorcycle->loan_duration_weeks) {
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

                UserNotification::createNotification(
                    $driver->id,
                    'loan_created',
                    'Mkataba Mpya wa Mkopo',
                    "Mkopo wa TZS " . number_format($motorcycle->loan_amount) . " kwa muda wa wiki {$motorcycle->loan_duration_weeks} umeundwa kwa bodaboda {$motorcycle->plate_number}. Malipo ya kila wiki: TZS " . number_format($weeklyInstallment) . ". Tafadhali kagua na ukubali mkataba.",
                    ['loan_id' => $loan->id]
                );

                if ($driver->phone) {
                    try {
                        $sms = app(SmsService::class);
                        $sms->sendLoanCreated($driver->phone, $driver->name, $motorcycle->plate_number);
                    } catch (\Exception $e) {
                        Log::error('Failed to send loan created SMS', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);
                    }
                }
            }
        } else {
            UserNotification::createNotification(
                $application->driver_id,
                'application_rejected',
                'Maombi Yamekataliwa',
                "Ombi lako la bodaboda {$application->motorcycle->plate_number} limekataliwa. Sababu: " . ($request->admin_notes ?? 'Hakuna sababu iliyotolewa.'),
                ['motorcycle_id' => $application->motorcycle_id]
            );
        }

        return redirect()->back()->with('success', "Application " . ($request->action === 'approved' ? 'approved' : 'rejected') . ".");
    }

    // =============================================
    // RELATIONSHIPS (Driver ↔ Vehicle ↔ Owner ↔ Loan)
    // =============================================

    public function relationships(Request $request)
    {
        $query = Motorcycle::with('owner', 'driver', 'loan')
            ->whereNotNull('driver_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhereHas('driver', fn($dq) => $dq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('owner', fn($oq) => $oq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('loan', fn($lq) => $lq->where('status', $request->status));
        }

        $relationships = $query->paginate(15)->withQueryString();

        return view('admin.relationships', compact('relationships'));
    }

    // =============================================
    // USER VERIFICATION
    // =============================================

    public function reviewVerification(Request $request, User $user)
    {
        $this->authorize('approve', $user);

        $user->load(['motorcycles', 'loans' => function ($q) {
            $q->with(['motorcycle', 'owner'])->latest();
        }, 'ownerLoans' => function ($q) {
            $q->with(['motorcycle', 'driver'])->latest();
        }]);

        $totalPayments = \App\Models\Payment::where('loan_id', $user->loans->pluck('id'))
            ->orWhere('loan_id', $user->ownerLoans->pluck('id'))
            ->count();

        return view('admin.verify-user', compact('user', 'totalPayments'));
    }

    public function approveVerification(User $user)
    {
        $this->authorize('approve', $user);

        if (!$user->hasVerificationDocuments()) {
            return redirect()->back()->with('error', 'User has not submitted verification documents.');
        }

        $user->update([
            'verification_submitted_at' => null,
            'rejection_reason' => null,
        ]);

        UserNotification::createNotification(
            $user->id,
            'verification_approved',
            'Account Verified',
            'Your account has been verified. You now have full access to all services.'
        );

        if ($user->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendAccountApproved($user->phone, $user->name);
            } catch (\Exception $e) {
                Log::error('Failed to send verification approval SMS', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('admin.users')->with('success', "Verification for {$user->name} has been approved. User now has full access.");
    }

    public function rejectVerification(Request $request, User $user)
    {
        $this->authorize('approve', $user);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($user->profile_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
        }
        if ($user->id_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->id_photo);
        }

        $user->update([
            'verification_submitted_at' => null,
            'rejection_reason' => $request->rejection_reason,
            'profile_photo' => null,
            'id_photo' => null,
        ]);

        UserNotification::createNotification(
            $user->id,
            'verification_rejected',
            'Verification Rejected',
            'Your verification documents were rejected. Reason: ' . $request->rejection_reason . '. Please resubmit your documents.'
        );

        if ($user->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendAccountSuspended($user->phone, $user->name, $request->rejection_reason);
            } catch (\Exception $e) {
                Log::error('Failed to send verification rejection SMS', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('admin.users')->with('success', "Verification for {$user->name} has been rejected. User must resubmit documents.");
    }

    // ── PENDING DRIVERS ──

    public function pendingDrivers(Request $request)
    {
        $query = User::where('role', 'driver');

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        } else {
            $query->where('approval_status', 'pending');
        }

        $drivers = $query->latest()->paginate(15)->withQueryString();
        return view('admin.drivers', compact('drivers'));
    }

    public function approveDriver(User $user)
    {
        $user->update(['approval_status' => 'approved']);

        UserNotification::createNotification(
            $user->id,
            'account_approved',
            'Account Approved',
            'Your driver account has been approved. You can now browse and apply for bodabodas.'
        );

        return redirect()->back()->with('success', "Driver {$user->name} approved.");
    }

    public function rejectDriver(Request $request, User $user)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $user->update([
            'approval_status' => 'suspended',
            'rejection_reason' => $request->rejection_reason,
        ]);

        UserNotification::createNotification(
            $user->id,
            'account_suspended',
            'Account Rejected',
            "Your driver application was rejected. Reason: {$request->rejection_reason}"
        );

        return redirect()->back()->with('success', "Driver {$user->name} rejected.");
    }

    // ── OWNERS ──

    public function owners(Request $request)
    {
        $query = User::where('role', 'owner');

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        } else {
            $query->where('approval_status', 'pending');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $owners = $query->withCount('motorcycles')->latest()->paginate(15)->withQueryString();
        return view('admin.owners', compact('owners'));
    }

    public function approveOwner(User $user)
    {
        $user->update(['approval_status' => 'approved']);

        UserNotification::createNotification(
            $user->id,
            'account_approved',
            'Account Approved',
            'Your owner account has been approved. You can now list bodabodas on the platform.'
        );

        return redirect()->back()->with('success', "Owner {$user->name} approved.");
    }

    public function rejectOwner(Request $request, User $user)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $user->update([
            'approval_status' => 'suspended',
            'rejection_reason' => $request->rejection_reason,
        ]);

        UserNotification::createNotification(
            $user->id,
            'account_suspended',
            'Account Rejected',
            "Your owner application was rejected. Reason: {$request->rejection_reason}"
        );

        return redirect()->back()->with('success', "Owner {$user->name} rejected.");
    }

    // ── OVERDUE LOANS ──

    public function overdueLoans(Request $request)
    {
        $query = Loan::whereIn('status', ['overdue', 'defaulted'])
            ->with('driver', 'motorcycle', 'owner');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest('next_payment_date')->paginate(15)->withQueryString();
        return view('admin.overdue', compact('loans'));
    }

    public function loansProgress(Request $request)
    {
        $query = Loan::with('driver', 'motorcycle', 'owner', 'payments');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['active', 'overdue', 'pending']);
        }

        $loans = $query->latest()->paginate(15)->withQueryString();
        return view('admin.loans-progress', compact('loans'));
    }

    public function forceStopLoan(Request $request, Loan $loan)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $loan->update([
            'status' => 'completed',
        ]);

        if ($loan->motorcycle) {
            $loan->motorcycle->update([
                'status' => 'available',
                'driver_id' => null,
                'verification_status' => 'verified',
            ]);
        }

        UserNotification::createNotification(
            $loan->driver_id,
            'loan_completed',
            'Loan Force-Stopped by Admin',
            "Your loan for motorcycle {$loan->motorcycle->plate_number} has been stopped by an admin. Reason: {$request->reason}",
            ['loan_id' => $loan->id, 'reason' => $request->reason]
        );

        UserNotification::createNotification(
            $loan->owner_id,
            'loan_completed',
            'Loan Force-Stopped by Admin',
            "The loan for motorcycle {$loan->motorcycle->plate_number} (driver: {$loan->driver->name}) has been stopped by an admin. Reason: {$request->reason}",
            ['loan_id' => $loan->id, 'reason' => $request->reason]
        );

        return redirect()->back()->with('success', 'Loan force-stopped. Motorcycle is now available again.');
    }
}
