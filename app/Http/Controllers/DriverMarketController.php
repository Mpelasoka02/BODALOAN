<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DriverMarketController extends Controller
{
    public function browse(Request $request)
    {
        $query = Motorcycle::where('verification_status', 'verified')
            ->where('status', 'available')
            ->whereNull('driver_id')
            ->with('owner');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('max_price')) {
            $query->where('loan_amount', '<=', $request->max_price);
        }

        $motorcycles = $query->latest()->paginate(12)->withQueryString();

        return view('driver.browse', compact('motorcycles'));
    }

    public function viewVehicle(Motorcycle $motorcycle)
    {
        if ($motorcycle->verification_status !== 'verified' || $motorcycle->status !== 'available' || $motorcycle->driver_id) {
            return redirect()->route('driver.marketplace')->with('error', 'This bodaboda is no longer available.');
        }

        $motorcycle->load('owner');
        $existingApplication = Application::where('motorcycle_id', $motorcycle->id)
            ->where('driver_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        return view('driver.vehicle-show', compact('motorcycle', 'existingApplication'));
    }

    public function showApplyForm(Motorcycle $motorcycle)
    {
        if ($motorcycle->verification_status !== 'verified' || $motorcycle->status !== 'available' || $motorcycle->driver_id) {
            return redirect()->route('driver.marketplace')->with('error', 'This bodaboda is no longer available.');
        }

        if (Loan::driverHasActiveLoan(Auth::id())) {
            return redirect()->route('driver.marketplace')->with('error', 'You already have an active loan. Complete it before applying for another bodaboda.');
        }

        $existingApplication = Application::where('driver_id', Auth::id())
            ->where('motorcycle_id', '!=', $motorcycle->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingApplication) {
            return redirect()->route('driver.marketplace')->with('error', 'You already have a pending application. Wait for it to be processed before applying for another bodaboda.');
        }

        $myApplication = Application::where('motorcycle_id', $motorcycle->id)
            ->where('driver_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        return view('driver.apply', compact('motorcycle', 'existingApplication'));
    }

    public function submitApplication(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->verification_status !== 'verified' || $motorcycle->status !== 'available' || $motorcycle->driver_id) {
            return redirect()->route('driver.marketplace')->with('error', 'This bodaboda is no longer available.');
        }

        if (Loan::driverHasActiveLoan(Auth::id())) {
            return redirect()->route('driver.marketplace')->with('error', 'You already have an active loan. Complete it before applying for another bodaboda.');
        }

        $pendingElsewhere = Application::where('driver_id', Auth::id())
            ->where('motorcycle_id', '!=', $motorcycle->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingElsewhere) {
            return redirect()->route('driver.marketplace')->with('error', 'You already have a pending application on another bodaboda. You can only apply for one bodaboda at a time.');
        }

        $existing = Application::where('motorcycle_id', $motorcycle->id)
            ->where('driver_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->route('driver.marketplace')->with('error', 'You already have an application for this bodaboda.');
        }

        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'nida' => 'required|string|max:20',
        ]);

        $driver = Auth::user();
        $driver->update([
            'phone' => $validated['phone'],
            'nida' => $validated['nida'],
        ]);

        Application::create([
            'motorcycle_id' => $motorcycle->id,
            'driver_id' => $driver->id,
            'id_number' => $validated['nida'],
            'license_number' => '',
            'guarantor_name' => '',
            'guarantor_phone' => '',
            'status' => 'pending',
        ]);

        UserNotification::createNotification(
            $driver->id,
            'application_submitted',
            'Application Submitted',
            "Your application for {$motorcycle->make} {$motorcycle->model} ({$motorcycle->plate_number}) has been submitted. The owner will review it shortly.",
            ['motorcycle_id' => $motorcycle->id]
        );

        $owner = $motorcycle->owner;
        if ($owner) {
            UserNotification::createNotification(
                $owner->id,
                'application_received',
                'New Application Received',
                "Driver {$driver->name} has applied for your bodaboda {$motorcycle->plate_number} ({$motorcycle->make} {$motorcycle->model}). Review the application and accept or reject.",
                ['motorcycle_id' => $motorcycle->id, 'driver_id' => $driver->id]
            );
        }

        return redirect()->route('driver.apps')->with('success', 'Application submitted! The owner will review it shortly.');
    }

    public function apps()
    {
        $applications = Application::where('driver_id', Auth::id())
            ->with('motorcycle.owner')
            ->latest()
            ->paginate(10);

        return view('driver.apps', compact('applications'));
    }

    public function profile()
    {
        $user = Auth::user()->load('assignedMotorcycle.owner');
        $loan = Loan::where('driver_id', $user->id)
            ->whereIn('status', ['active', 'pending', 'overdue'])
            ->latest()
            ->first();

        $completedLoans = Loan::where('driver_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return view('driver.profile', compact('user', 'loan', 'completedLoans'));
    }
}
