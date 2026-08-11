<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Loan;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = User::where('role', 'driver')
            ->with('assignedMotorcycle');

        $pendingApplications = collect();

        if ($user->isOwner()) {
            $ownerMotorcycleIds = $user->motorcycles()->pluck('id');
            $pendingApplications = Application::whereIn('motorcycle_id', $ownerMotorcycleIds)
                ->where('status', 'pending')
                ->with(['driver', 'motorcycle'])
                ->get();

            $ownerLoanIds = Loan::where('owner_id', $user->id)->pluck('driver_id')->unique();
            $assignedDriverIds = $user->motorcycles()->whereNotNull('driver_id')->pluck('driver_id')->unique();
            $applicantIds = $pendingApplications->pluck('driver_id')->unique();
            $driverIds = $ownerLoanIds->merge($assignedDriverIds)->merge($applicantIds)->unique()->filter();
            $query->whereIn('id', $driverIds)
                ->with(['loans' => function ($q) use ($user) {
                    $q->where('owner_id', $user->id)->with('motorcycle', 'payments');
                }]);
        } else {
            $query->withCount('loans');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $drivers = $query->paginate(10)->withQueryString();

        return view('drivers.index', compact('drivers', 'pendingApplications'));
    }

    public function approveApplication(Application $application)
    {
        $user = auth()->user();
        if (!$user->isOwner() || $application->motorcycle->owner_id !== $user->id) {
            abort(403);
        }

        $application->update(['status' => 'approved']);

        $motorcycle = $application->motorcycle;
        $motorcycle->update([
            'driver_id' => $application->driver_id,
            'status' => 'assigned',
        ]);

        UserNotification::createNotification(
            $application->driver_id,
            'application_approved',
            'Application Approved',
            "Your application for motorcycle {$motorcycle->plate_number} has been approved.",
            ['motorcycle_id' => $motorcycle->id]
        );

        return redirect()->route('drivers.index')->with('success', "Application from {$application->driver->name} approved.");
    }

    public function rejectApplication(Request $request, Application $application)
    {
        $user = auth()->user();
        if (!$user->isOwner() || $application->motorcycle->owner_id !== $user->id) {
            abort(403);
        }

        $request->validate(['reason' => 'nullable|string|max:500']);

        $application->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason,
        ]);

        UserNotification::createNotification(
            $application->driver_id,
            'application_rejected',
            'Application Rejected',
            "Your application for {$application->motorcycle->plate_number} has been rejected." . ($request->reason ? " Reason: {$request->reason}" : ''),
            ['motorcycle_id' => $application->motorcycle_id]
        );

        return redirect()->route('drivers.index')->with('error', "Application from {$application->driver->name} rejected.");
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'driver',
            'approval_status' => 'approved',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('drivers.index')->with('success', 'Driver added successfully.');
    }

    public function show(User $driver)
    {
        $driver->load('assignedMotorcycle.loan', 'loans.payments', 'loans.motorcycle');

        return view('drivers.show', compact('driver'));
    }

    public function edit(User $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, User $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $driver->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(User $driver)
    {
        if ($driver->assignedMotorcycle) {
            return redirect()->back()->with('error', 'Cannot remove a driver with an assigned motorcycle. Unassign them first.');
        }

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver removed successfully.');
    }
}
