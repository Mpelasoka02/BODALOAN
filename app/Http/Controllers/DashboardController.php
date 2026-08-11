<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isOwner()) {
            return $this->ownerDashboard($user);
        }

        return $this->driverDashboard($user);
    }

    private function adminDashboard()
    {
        $totalOwners = User::where('role', 'owner')->count();
        $totalDrivers = User::where('role', 'driver')->count();
        $totalMotorcycles = Motorcycle::count();
        $activeLoans = Loan::where('status', 'active')->count();
        $completedLoans = Loan::where('status', 'completed')->count();
        $overdueLoans = Loan::where('status', 'overdue')->count();
        $pendingUsers = User::where('approval_status', 'pending')->count();
        $pendingVehicles = Motorcycle::where('verification_status', 'pending')->count();
        $pendingApplications = Application::where('status', 'pending')->count();

        $totalLoanAmount = Loan::sum('total_amount');
        $totalAmountPaid = Payment::where('status', 'verified')->sum('amount');
        $collectionRate = $totalLoanAmount > 0 ? round(($totalAmountPaid / $totalLoanAmount) * 100, 1) : 0;

        $weeklyCollections = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');

        $monthlyCollections = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $totalRevenue = Payment::where('status', 'verified')->sum('amount');

        $recentPayments = Payment::where('status', 'verified')
            ->with('loan.driver', 'loan.motorcycle')
            ->latest('payment_date')
            ->limit(8)
            ->get();

        $recentUsers = User::latest()->limit(5)->get();

        $pendingPayments = Payment::where('status', 'pending_verification')->count();

        $loansByStatus = [
            'active' => Loan::where('status', 'active')->count(),
            'completed' => Loan::where('status', 'completed')->count(),
            'overdue' => Loan::where('status', 'overdue')->count(),
            'defaulted' => Loan::where('status', 'defaulted')->count(),
        ];

        $totalLoans = array_sum($loansByStatus);

        $revenueByMonth = Payment::where('status', 'verified')
            ->where('payment_date', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("strftime('%Y-%m', payment_date) as month_key, SUM(amount) as total")
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->map(fn($item) => [
                'month' => \Carbon\Carbon::parse($item->month_key)->format('M Y'),
                'total' => (float) $item->total,
            ])
            ->values();

        $totalPendingAmount = Payment::where('status', 'pending_verification')->sum('amount');

        $overdueLoansList = Loan::where('status', 'overdue')
            ->with('driver', 'motorcycle')
            ->latest()
            ->limit(5)
            ->get();

        $recentApplications = Application::where('status', 'pending')
            ->with('driver', 'motorcycle')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalOwners',
            'totalDrivers',
            'totalMotorcycles',
            'activeLoans',
            'completedLoans',
            'overdueLoans',
            'pendingUsers',
            'pendingVehicles',
            'pendingApplications',
            'totalLoanAmount',
            'collectionRate',
            'weeklyCollections',
            'monthlyCollections',
            'totalRevenue',
            'recentPayments',
            'recentUsers',
            'pendingPayments',
            'loansByStatus',
            'totalLoans',
            'revenueByMonth',
            'totalPendingAmount',
            'overdueLoansList',
            'recentApplications'
        ));
    }

    private function ownerDashboard($user)
    {
        $totalMotorcycles = Motorcycle::where('owner_id', $user->id)->count();
        $assignedDrivers = Motorcycle::where('owner_id', $user->id)->whereNotNull('driver_id')->count();
        $activeLoans = Loan::where('owner_id', $user->id)->where('status', 'active')->count();
        $outstandingBalance = Loan::where('owner_id', $user->id)
            ->whereIn('status', ['active', 'overdue'])
            ->get()
            ->sum('balance');

        $weeklyCollections = Payment::where('status', 'verified')
            ->whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');

        $monthlyCollections = Payment::where('status', 'verified')
            ->whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $totalCollected = Payment::where('status', 'verified')
            ->whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->sum('amount');

        $pendingPayments = Payment::where('status', 'pending_verification')
            ->whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->count();

        $pendingPaymentsTotal = Payment::where('status', 'pending_verification')
            ->whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->sum('amount');

        $recentPayments = Payment::whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->with('loan.driver', 'loan.motorcycle')
            ->latest('payment_date')
            ->limit(8)
            ->get();

        $recentMotorcycles = Motorcycle::where('owner_id', $user->id)
            ->with('driver')
            ->latest()
            ->limit(5)
            ->get();

        $overdueLoans = Loan::where('owner_id', $user->id)->where('status', 'overdue')->count();
        $completedLoans = Loan::where('owner_id', $user->id)->where('status', 'completed')->count();

        $motorcyclesByStatus = [
            'available' => Motorcycle::where('owner_id', $user->id)->where('status', 'available')->count(),
            'assigned' => Motorcycle::where('owner_id', $user->id)->where('status', 'assigned')->count(),
            'completed' => Motorcycle::where('owner_id', $user->id)->where('status', 'completed')->count(),
        ];

        $loansByStatus = [
            'active' => Loan::where('owner_id', $user->id)->where('status', 'active')->count(),
            'completed' => Loan::where('owner_id', $user->id)->where('status', 'completed')->count(),
            'overdue' => Loan::where('owner_id', $user->id)->where('status', 'overdue')->count(),
            'defaulted' => Loan::where('owner_id', $user->id)->where('status', 'defaulted')->count(),
        ];

        $totalOwnerLoans = array_sum($loansByStatus);

        $overdueLoansList = Loan::where('owner_id', $user->id)
            ->where('status', 'overdue')
            ->with('driver', 'motorcycle')
            ->latest()
            ->limit(5)
            ->get();

        $driversWithLoans = User::where('role', 'driver')
            ->whereHas('loans', fn($q) => $q->where('owner_id', $user->id)->whereIn('status', ['active', 'overdue']))
            ->with(['loans' => fn($q) => $q->where('owner_id', $user->id)->whereIn('status', ['active', 'overdue'])->with('motorcycle')])
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalMotorcycles',
            'assignedDrivers',
            'activeLoans',
            'outstandingBalance',
            'weeklyCollections',
            'monthlyCollections',
            'totalCollected',
            'pendingPayments',
            'pendingPaymentsTotal',
            'recentPayments',
            'recentMotorcycles',
            'overdueLoans',
            'completedLoans',
            'motorcyclesByStatus',
            'loansByStatus',
            'totalOwnerLoans',
            'overdueLoansList',
            'driversWithLoans'
        ));
    }

    private function driverDashboard($user)
    {
        $loan = Loan::where('driver_id', $user->id)
            ->whereIn('status', ['active', 'pending', 'overdue'])
            ->latest()
            ->first();

        $motorcycle = $user->assignedMotorcycle;
        $owner = $motorcycle ? $motorcycle->owner : null;

        $recentPayments = $loan
            ? $loan->payments()->latest('payment_date')->limit(5)->get()
            : collect();

        $nextPayment = $loan ? $loan->next_payment_date : null;

        $totalPaid = $loan ? ($loan->total_amount - $loan->balance) : 0;
        $weeksRemaining = $loan && $loan->weekly_installment > 0 ? ceil($loan->balance / $loan->weekly_installment) : 0;
        $totalWeeks = $loan && $loan->weekly_installment > 0 ? ceil($loan->total_amount / $loan->weekly_installment) : 0;
        $weeksPaid = $totalWeeks - $weeksRemaining;

        $completedPayments = $loan ? $loan->payments()->where('status', 'verified')->count() : 0;

        return view('dashboard', compact('loan', 'motorcycle', 'owner', 'recentPayments', 'nextPayment', 'totalPaid', 'weeksRemaining', 'totalWeeks', 'weeksPaid', 'completedPayments'));
    }

    public function marketplace()
    {
        return redirect()->route('home', request()->query());
    }
}
