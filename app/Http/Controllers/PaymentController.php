<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\UserNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $user = Auth::user();

        $query = Payment::with('loan.driver', 'loan.motorcycle', 'loan');

        if ($user->isOwner()) {
            $query->whereHas('loan', fn($q) => $q->where('owner_id', $user->id));
        }

        if ($user->isDriver()) {
            $query->whereHas('loan', fn($q) => $q->where('driver_id', $user->id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('loan.motorcycle', function ($mq) use ($search) {
                        $mq->where('plate_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('loan.driver', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        $sort = $request->get('sort', 'payment_date');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $payments = $query->paginate(10)->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Payment::class);

        $user = Auth::user();

        if ($user->isDriver()) {
            $loan = Loan::where('driver_id', $user->id)
                ->whereIn('status', ['active', 'overdue'])
                ->latest()
                ->firstOrFail();

            return view('payments.create', compact('loan'));
        }

        $loans = Loan::with('motorcycle', 'driver')
            ->when($user->isOwner(), fn($q) => $q->where('owner_id', $user->id))
            ->whereIn('status', ['active', 'overdue'])
            ->get();

        $selectedLoanId = $request->get('loan_id');

        return view('payments.create', compact('loans', 'selectedLoanId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'method' => 'required|in:cash,mpesa,tigo_pesa,airmoney,halopesa,bank',
            'reference_number' => 'nullable|string|max:100',
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        $loan = Loan::findOrFail($validated['loan_id']);
        $this->authorize('view', $loan);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $payment = Payment::create([
            'loan_id' => $loan->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'method' => $validated['method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'receipt_path' => $receiptPath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending_verification',
        ]);

        UserNotification::createNotification(
            $loan->owner_id,
            'payment_submitted',
            'Payment Submitted',
            "Driver {$loan->driver->name} submitted a payment of TZS " . number_format($payment->amount) . " for motorcycle {$loan->motorcycle->plate_number}.",
            ['payment_id' => $payment->id, 'loan_id' => $loan->id]
        );

        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            UserNotification::createNotification(
                $admin->id,
                'payment_submitted',
                'New Payment to Verify',
                "Driver {$loan->driver->name} submitted TZS " . number_format($payment->amount) . " for {$loan->motorcycle->plate_number}. Receipt attached — please review.",
                ['payment_id' => $payment->id, 'loan_id' => $loan->id]
            );
        }

        if ($loan->owner && $loan->owner->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendPaymentSubmitted($loan->owner->phone, $loan->driver->name, $payment->amount, $loan->motorcycle->plate_number);
            } catch (\Exception $e) {
                Log::error('Failed to send payment submitted SMS', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('payments.index')->with('success', 'Payment submitted for verification.');
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['loan.motorcycle', 'loan.driver', 'loan.owner']);

        return view('payments.show', compact('payment'));
    }

    public function verify(Payment $payment)
    {
        $this->authorize('verify', $payment);

        if ($payment->status !== 'pending_verification') {
            return back()->with('error', 'This payment is not pending verification.');
        }

        $payment->update(['status' => 'verified']);

        $loan = $payment->loan;
        $loan->amount_paid += $payment->amount;
        $loan->next_payment_date = now()->parse($payment->payment_date)->addWeek();

        if ($loan->amount_paid >= $loan->total_amount) {
            $loan->status = 'completed';
            $loan->save();

            $motorcycle = $loan->motorcycle;
            $motorcycle->update(['status' => 'completed']);

            UserNotification::createNotification(
                $loan->driver_id,
                'loan_completed',
                'Loan Completed',
                "Congratulations! Your loan for motorcycle {$motorcycle->plate_number} has been fully paid. You now own this motorcycle!",
                ['loan_id' => $loan->id]
            );
        } else {
            $loan->save();
        }

        UserNotification::createNotification(
            $payment->loan->driver_id,
            'payment_verified',
            'Payment Verified',
            "Your payment of TZS " . number_format($payment->amount) . " for motorcycle {$loan->motorcycle->plate_number} has been verified.",
            ['payment_id' => $payment->id]
        );

        UserNotification::createNotification(
            $loan->owner_id,
            'payment_verified',
            'Payment Verified',
            "A payment of TZS " . number_format($payment->amount) . " from {$loan->driver->name} for motorcycle {$loan->motorcycle->plate_number} has been verified. Loan balance updated.",
            ['payment_id' => $payment->id, 'loan_id' => $loan->id]
        );

        $driver = $loan->driver;
        if ($driver && $driver->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendPaymentVerified($driver->phone, $driver->name, $payment->amount, $loan->motorcycle->plate_number);
            } catch (\Exception $e) {
                Log::error('Failed to send payment verified SMS', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->back()->with('success', 'Payment verified and balance updated.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $this->authorize('reject', $payment);

        if ($payment->status !== 'pending_verification') {
            return back()->with('error', 'This payment is not pending verification.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        UserNotification::createNotification(
            $payment->loan->driver_id,
            'payment_rejected',
            'Payment Rejected',
            "Your payment of TZS " . number_format($payment->amount) . " for motorcycle {$payment->loan->motorcycle->plate_number} was rejected. Reason: {$request->rejection_reason}",
            ['payment_id' => $payment->id, 'rejection_reason' => $request->rejection_reason]
        );

        $driver = $payment->loan->driver;
        if ($driver && $driver->phone) {
            try {
                $sms = app(SmsService::class);
                $sms->sendPaymentRejected($driver->phone, $driver->name, $payment->amount, $payment->loan->motorcycle->plate_number, $request->rejection_reason);
            } catch (\Exception $e) {
                Log::error('Failed to send payment rejected SMS', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->back()->with('success', 'Payment rejected.');
    }

    public function adminIndex(Request $request)
    {
        $query = Payment::with('loan.driver', 'loan.motorcycle', 'loan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending_verification');
        }

        $payments = $query->latest('payment_date')->paginate(15)->withQueryString();
        return view('admin.payments', compact('payments'));
    }
}
