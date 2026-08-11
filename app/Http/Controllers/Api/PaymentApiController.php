<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentApiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Payment::with('loan.driver', 'loan.motorcycle');

        if ($user->isOwner()) {
            $query->whereHas('loan', fn($q) => $q->where('owner_id', $user->id));
        }

        if ($user->isDriver()) {
            $query->whereHas('loan', fn($q) => $q->where('driver_id', $user->id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('loan.motorcycle', fn($q) => $q->where('plate_number', 'like', "%{$search}%"));
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'method' => 'required|in:cash,mpesa,bank',
        ]);

        $loan = Loan::findOrFail($validated['loan_id']);
        $this->authorizeAccess($loan);

        $payment = Payment::create([
            'loan_id' => $loan->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'method' => $validated['method'],
            'status' => 'verified',
        ]);

        $loan->amount_paid += $validated['amount'];
        $loan->next_payment_date = now()->parse($validated['payment_date'])->addWeek();

        if ($loan->amount_paid >= $loan->total_amount) {
            $loan->status = 'completed';
            $loan->save();

            $motorcycle = $loan->motorcycle;
            $motorcycle->owner_id = $loan->driver_id;
            $motorcycle->status = 'completed';
            $motorcycle->save();
        } else {
            $loan->save();
        }

        return response()->json([
            'payment' => $payment,
            'loan' => $loan->fresh(),
            'message' => 'Payment recorded successfully.',
        ], 201);
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
