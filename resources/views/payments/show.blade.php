@extends('layouts.app')
@section('title', 'Payment Details')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('payments.index') }}" style="color:var(--text-secondary);text-decoration:none;">Payments</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">Payment #{{ $payment->id }}</li>
    </ol>
</nav>

@if(session('success'))
    <div class="alert-banner green mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff me-2" style="color:var(--gold-500);"></i>Payment Receipt</h6>
                <span class="badge-status {{ $payment->status === 'verified' ? 'verified' : ($payment->status === 'pending_verification' ? 'pending_verification' : 'rejected') }}">
                    {{ $payment->status === 'pending_verification' ? 'Pending Verification' : ucfirst($payment->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Amount Paid</small>
                        <span class="fs-4 fw-bold" style="color:var(--emerald-600);">TZS {{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Payment Date</small>
                        <span class="fs-5 fw-semibold" style="color:var(--text);">{{ $payment->payment_date->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Payment Method</small>
                        <span class="badge-status active">
                            @switch($payment->method)
                                @case('cash') <i class="bi bi-cash me-1"></i> Cash @break
                                @case('mpesa') <i class="bi bi-phone me-1"></i> M-Pesa @break
                                @case('tigo_pesa') <i class="bi bi-phone me-1"></i> Tigo Pesa @break
                                @case('airmoney') <i class="bi bi-phone me-1"></i> Airtel Money @break
                                @case('halopesa') <i class="bi bi-phone me-1"></i> HaloPesa @break
                                @case('bank') <i class="bi bi-bank me-1"></i> Bank Transfer @break
                                @default {{ ucfirst($payment->method) }}
                            @endswitch
                        </span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Reference Number</small>
                        <span style="color:var(--text);">{{ $payment->reference_number ?: '—' }}</span>
                    </div>
                </div>

                @if($payment->notes)
                    <div class="mt-3">
                        <small class="text-muted d-block">Notes</small>
                        <p class="mb-0" style="color:var(--text);">{{ $payment->notes }}</p>
                    </div>
                @endif

                @if($payment->rejection_reason)
                    <div class="mt-3 p-3" style="background:rgba(239,68,68,0.05);border-radius:var(--radius);border-left:3px solid var(--red-500);">
                        <small class="d-block fw-bold" style="color:var(--red-500);">Rejection Reason</small>
                        <span style="color:var(--text);">{{ $payment->rejection_reason }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2" style="color:var(--gold-500);"></i>Loan Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Motorcycle</small>
                    <div class="fw-semibold" style="color:var(--text);">{{ $payment->loan->motorcycle->plate_number ?? '—' }}</div>
                    <small style="color:var(--text-secondary);">{{ $payment->loan->motorcycle->make ?? '' }} {{ $payment->loan->motorcycle->model ?? '' }}</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Driver</small>
                    <div class="fw-semibold" style="color:var(--text);">{{ $payment->loan->driver->name ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Owner</small>
                    <div class="fw-semibold" style="color:var(--text);">{{ $payment->loan->owner->name ?? '—' }}</div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted d-block">Total Loan</small>
                        <span class="fw-bold" style="color:var(--text);">TZS {{ number_format($payment->loan->total_amount) }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Amount Paid</small>
                        <span class="fw-bold" style="color:var(--emerald-600);">TZS {{ number_format($payment->loan->amount_paid) }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Balance</small>
                        <span class="fw-bold" style="color:var(--text);">TZS {{ number_format($payment->loan->balance) }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge-status {{ $payment->loan->status }}">{{ ucfirst($payment->loan->status) }}</span>
                    </div>
                </div>
                @php
                    $progress = $payment->loan->total_amount > 0 ? round(($payment->loan->amount_paid / $payment->loan->total_amount) * 100) : 0;
                @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Progress</small>
                        <small class="fw-bold">{{ $progress }}%</small>
                    </div>
                    <div style="height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $progress }}%;background:linear-gradient(135deg,var(--gold-500),var(--gold-600));border-radius:4px;transition:width 0.3s;"></div>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->receipt_path)
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-image me-2" style="color:var(--gold-500);"></i>Payment Receipt</h6>
            </div>
            <div class="card-body text-center">
                @if(str_ends_with($payment->receipt_path, '.pdf'))
                    <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank" class="btn btn-outline btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i> View PDF Receipt
                    </a>
                @else
                    <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank">
                        <img src="{{ Storage::url($payment->receipt_path) }}" alt="Payment Receipt" style="max-width:100%;border-radius:var(--radius);border:1px solid #e2e8f0;">
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@if(!auth()->user()->isDriver() && $payment->status === 'pending_verification')
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header">
        <h6 class="mb-0 fw-bold"><i class="bi bi-check2-square me-2" style="color:var(--gold-500);"></i>Verify Payment</h6>
    </div>
    <div class="card-body">
        <div class="d-flex gap-3">
            <form method="POST" action="{{ route('payments.verify', $payment) }}">
                @csrf
                <button type="submit" class="btn btn-emerald" onclick="return confirm('Verify this payment and update the loan balance?')">
                    <i class="bi bi-check-lg me-1"></i> Approve & Update Loan
                </button>
            </form>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-lg me-1"></i> Reject
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('payments.reject', $payment) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Reject Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Provide a reason for rejecting this payment. The driver will be notified.</p>
                    <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g., Receipt is blurry, amount doesn't match..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="mt-3">
    <a href="{{ route('payments.index') }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left me-1"></i> Back to Payments</a>
</div>
@endsection
