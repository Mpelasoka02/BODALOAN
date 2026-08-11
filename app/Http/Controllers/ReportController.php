<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminReports();
        }

        return $this->ownerReports($user);
    }

    private function adminReports()
    {
        $totalDisbursed = Loan::sum('total_amount');
        $totalCollected = Payment::where('status', 'verified')->sum('amount');
        $activeLoans = Loan::where('status', 'active')->count();
        $completedLoans = Loan::where('status', 'completed')->count();
        $overdueLoans = Loan::where('status', 'overdue')->count();
        $pendingPayments = Payment::where('status', 'pending_verification')->count();
        $registeredMotorcycles = Motorcycle::count();

        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $motorcyclesByStatus = Motorcycle::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $loansByStatus = Loan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthlyCollections = Payment::where('status', 'verified')
            ->where('payment_date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("strftime('%Y-%m', payment_date) as month_key, SUM(amount) as total, COUNT(*) as count, MIN(payment_date) as sort_date")
            ->groupBy('month_key')
            ->orderBy('sort_date')
            ->get()
            ->map(fn($item) => [
                'month' => \Carbon\Carbon::parse($item->month_key)->format('M Y'),
                'total' => (float) $item->total,
                'count' => (int) $item->count,
                'sort_date' => $item->sort_date,
            ]);

        $ownerPerformance = User::where('role', 'owner')
            ->withCount(['loans as total_loans_count'])
            ->withCount(['loans as active_loans_count' => fn($q) => $q->where('status', 'active')])
            ->withCount(['loans as completed_loans_count' => fn($q) => $q->where('status', 'completed')])
            ->get()
            ->map(function ($owner) {
                $revenue = Payment::whereHas('loan', fn($q) => $q->where('owner_id', $owner->id))
                    ->where('status', 'verified')
                    ->sum('amount');
                $owner->total_revenue = $revenue;
                return $owner;
            })
            ->sortByDesc('total_revenue')
            ->values();

        $defaulters = Loan::where('status', 'overdue')
            ->with('driver', 'motorcycle')
            ->get();

        return view('reports.index', compact(
            'totalDisbursed',
            'totalCollected',
            'activeLoans',
            'completedLoans',
            'overdueLoans',
            'pendingPayments',
            'registeredMotorcycles',
            'usersByRole',
            'motorcyclesByStatus',
            'loansByStatus',
            'monthlyCollections',
            'ownerPerformance',
            'defaulters'
        ));
    }

    private function ownerReports($user)
    {
        $loanQuery = Loan::where('owner_id', $user->id);

        $totalDisbursed = (clone $loanQuery)->sum('total_amount');
        $totalCollected = Payment::whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->where('status', 'verified')
            ->sum('amount');
        $activeLoans = (clone $loanQuery)->where('status', 'active')->count();
        $completedLoans = (clone $loanQuery)->where('status', 'completed')->count();
        $overdueLoans = (clone $loanQuery)->where('status', 'overdue')->count();
        $outstandingBalance = (clone $loanQuery)
            ->whereIn('status', ['active', 'overdue'])
            ->get()
            ->sum('balance');
        $pendingPayments = Payment::whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->where('status', 'pending_verification')
            ->count();

        $motorcyclesByStatus = Motorcycle::where('owner_id', $user->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $loansByStatus = (clone $loanQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $paymentHistory = Payment::whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->with('loan.motorcycle', 'loan.driver')
            ->latest('payment_date')
            ->limit(20)
            ->get();

        $monthlyCollections = Payment::whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->where('status', 'verified')
            ->where('payment_date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("strftime('%Y-%m', payment_date) as month_key, SUM(amount) as total, COUNT(*) as count, MIN(payment_date) as sort_date")
            ->groupBy('month_key')
            ->orderBy('sort_date')
            ->get()
            ->map(fn($item) => [
                'month' => \Carbon\Carbon::parse($item->month_key)->format('M Y'),
                'total' => (float) $item->total,
                'count' => (int) $item->count,
                'sort_date' => $item->sort_date,
            ]);

        $weeklyCollections = Payment::whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->where('status', 'verified')
            ->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');

        $monthlyCollectionsAll = Payment::whereHas('loan', fn($q) => $q->where('owner_id', $user->id))
            ->where('status', 'verified')
            ->selectRaw("strftime('%Y-%m', payment_date) as month_key, SUM(amount) as total, COUNT(*) as count, MIN(payment_date) as sort_date")
            ->groupBy('month_key')
            ->orderBy('sort_date')
            ->get()
            ->map(fn($item) => [
                'month' => \Carbon\Carbon::parse($item->month_key)->format('M Y'),
                'total' => (float) $item->total,
                'count' => (int) $item->count,
                'sort_date' => $item->sort_date,
            ]);

        $defaulters = (clone $loanQuery)
            ->where('status', 'overdue')
            ->with('driver', 'motorcycle')
            ->get();

        return view('reports.index', compact(
            'totalDisbursed',
            'totalCollected',
            'activeLoans',
            'completedLoans',
            'overdueLoans',
            'outstandingBalance',
            'pendingPayments',
            'motorcyclesByStatus',
            'loansByStatus',
            'paymentHistory',
            'monthlyCollections',
            'weeklyCollections',
            'monthlyCollectionsAll',
            'defaulters'
        ));
    }
}
