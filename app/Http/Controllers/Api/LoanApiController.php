<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanApiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isDriver()) {
            $loan = Loan::where('driver_id', $user->id)->latest()->first();
            return response()->json($loan ? $loan->load(['motorcycle', 'driver', 'owner', 'payments']) : null);
        }

        $query = Loan::with('motorcycle', 'driver');

        if ($user->isOwner()) {
            $query->where('owner_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($loans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'total_amount' => 'required|numeric|min:1',
            'weekly_installment' => 'required|numeric|min:1',
            'duration_weeks' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        $motorcycle = Motorcycle::findOrFail($validated['motorcycle_id']);

        if (!$motorcycle->driver_id) {
            return response()->json(['error' => 'This motorcycle has no driver assigned yet.'], 422);
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
            'status' => 'active',
        ]);

        return response()->json($loan->load(['motorcycle', 'driver', 'owner']), 201);
    }

    public function show(Loan $loan)
    {
        $this->authorizeAccess($loan);

        $loan->load(['motorcycle', 'driver', 'owner', 'payments' => function ($q) {
            $q->latest('payment_date');
        }]);

        $schedule = [];
        if ($loan->start_date) {
            $date = $loan->start_date->copy();
            for ($week = 1; $week <= $loan->duration_weeks; $week++) {
                $schedule[] = [
                    'week' => $week,
                    'due_date' => $date->copy()->toDateString(),
                    'amount' => $loan->weekly_installment,
                ];
                $date->addWeek();
            }
        }

        return response()->json([
            'loan' => $loan,
            'schedule' => $schedule,
            'balance' => $loan->balance,
            'progress' => $loan->progress,
        ]);
    }

    private function authorizeAccess(Loan $loan)
    {
        $user = Auth::user();

        if ($user->isOwner() && $loan->owner_id !== $user->id) {
            abort(403);
        }

        if ($user->isDriver() && $loan->driver_id !== $user->id) {
            abort(403);
        }
    }
}
