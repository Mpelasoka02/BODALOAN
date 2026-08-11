@extends('layouts.app')
@section('title', 'Loan Details')
@section('page-title', 'Loan Details')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.index') }}" style="color:var(--text-secondary);text-decoration:none;">Loans</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">Loan #{{ $loan->id ?? 'N/A' }}</li>
    </ol>
</nav>

@if(!$loan)
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon"><i class="bi bi-wallet2"></i></div>
            <h5>No Loan Found</h5>
            <p>Contact your motorcycle owner to create a loan agreement.</p>
        </div>
    </div>
@else

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--text);">Loan #{{ $loan->id }}</h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span>
            @if($loan->contract)
                @php $cs = $loan->contract->status; @endphp
                <span class="badge-status {{ in_array($cs, ['approved', 'fully_signed']) ? 'approved' : ($cs === 'rejected' ? 'rejected' : ($cs === 'partially_signed' ? 'pending' : 'pending')) }}">
                    {{ $cs === 'fully_signed' ? 'Active' : ucfirst(str_replace('_', ' ', $cs)) }}
                </span>
            @else
                <span class="badge-status pending">No Contract</span>
            @endif
            <small style="color:var(--text-secondary);">Created {{ $loan->created_at->format('M d, Y') }}</small>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if(auth()->user()->isDriver() && $loan->status === 'pending' && !$loan->agreement_accepted_at && !$loan->contract)
            <form method="POST" action="{{ route('loans.accept', $loan) }}">
                @csrf
                <button type="submit" class="btn btn-emerald btn-sm">
                    <i class="bi bi-check-circle me-1"></i>Start Contract Process
                </button>
            </form>
        @endif
        @if(!auth()->user()->isDriver() && $loan->isActive() && $loan->balance <= 0)
            <form method="POST" action="{{ route('loans.complete', $loan) }}">
                @csrf
                <button type="submit" class="btn btn-emerald btn-sm" onclick="return confirm('Mark this loan as completed and transfer ownership?')">
                    <i class="bi bi-check-circle me-1"></i>Complete Loan
                </button>
            </form>
        @endif
        @if(auth()->user()->isDriver() && $loan->isActive())
            <a href="{{ route('payments.create') }}" class="btn btn-gold btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Make Payment
            </a>
        @endif
        @if(!auth()->user()->isDriver() && in_array($loan->status, ['active', 'overdue']))
            <a href="{{ route('payments.create', ['loan_id' => $loan->id]) }}" class="btn btn-gold btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Record Payment
            </a>
        @endif
        @if(in_array($loan->status, ['pending', 'active', 'overdue']))
            <a href="{{ route('contracts.show', $loan) }}" class="btn btn-outline-navy btn-sm">
                <i class="bi bi-file-earmark-text me-1"></i>Contract
            </a>
            <a href="{{ route('contracts.print', $loan) }}" class="btn btn-outline btn-sm" target="_blank">
                <i class="bi bi-printer me-1"></i>Print Contract
            </a>
        @endif
        @if(!auth()->user()->isDriver() && in_array($loan->status, ['active', 'overdue']))
            <a href="{{ route('locations.track', $loan) }}" class="btn btn-outline btn-sm">
                <i class="bi bi-geo-alt-fill me-1"></i>Track
            </a>
        @endif
        @if(auth()->user()->isDriver() && $loan->owner)
            <a href="{{ route('chat.start.direct', $loan->owner_id) }}" class="btn btn-outline btn-sm">
                <i class="bi bi-chat-dots me-1"></i>Chat with Owner
            </a>
        @endif
        @if(!auth()->user()->isDriver() && $loan->driver)
            <a href="{{ route('chat.start.direct', $loan->driver_id) }}" class="btn btn-outline btn-sm">
                <i class="bi bi-chat-dots me-1"></i>Chat with Driver
            </a>
        @endif
        @php $adminChat = \App\Models\User::where('role','admin')->first(); @endphp
        @if($adminChat)
            <a href="{{ route('chat.start.direct', $adminChat->id) }}" class="btn btn-outline btn-sm">
                <i class="bi bi-headset me-1"></i>Chat with Admin
            </a>
        @endif
        @if(!auth()->user()->isDriver() && $loan->status === 'overdue')
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#abscondedModal">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>Report Absconded
            </button>
        @endif
        @if(!auth()->user()->isDriver() && $loan->status === 'defaulted' && $loan->absconded_at)
            <button type="button" class="btn btn-emerald btn-sm" data-bs-toggle="modal" data-bs-target="#recoverModal">
                <i class="bi bi-arrow-return-left me-1"></i>Recover Vehicle
            </button>
            <a href="{{ route('locations.track', $loan) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-geo-alt-fill me-1"></i>Track Stolen
            </a>
        @endif
    </div>
</div>

@if($loan->status === 'pending' && auth()->user()->isDriver() && !$loan->contract)
    <div class="alert-banner blue">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--status-assigned-text);"></i>
            <span>Click "Start Contract Process" to generate the hire-purchase agreement.</span>
        </div>
    </div>
@endif

@if($loan->status === 'defaulted' && $loan->absconded_at)
    <div class="alert-banner" style="background:#FEF2F2;border:1px solid #FECACA;border-left:4px solid #DC2626;margin-bottom:16px;border-radius:10px;padding:14px 18px;">
        <div class="d-flex align-items-start gap-3">
            <div style="width:40px;height:40px;border-radius:50%;background:#FEE2E2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-exclamation-triangle-fill" style="color:#DC2626;font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-weight:700;color:#991B1B;font-size:0.95rem;">Vehicle Reported Stolen</div>
                <div style="font-size:0.85rem;color:#991B1B;margin-top:4px;">
                    <strong>{{ $loan->driver->name ?? 'Driver' }}</strong> absconded with <strong>{{ $loan->motorcycle->plate_number ?? 'this vehicle' }}</strong>.
                    Reported {{ $loan->absconded_at->diffForHumans() }}.
                    <br><span style="color:#7F1D1D;">Reason: {{ $loan->absconded_reason }}</span>
                </div>
                @if(!auth()->user()->isDriver())
                    <a href="{{ route('locations.track', $loan) }}" class="btn btn-sm btn-danger mt-2" style="font-size:0.8rem;">
                        <i class="bi bi-geo-alt-fill me-1"></i>Track on GPS
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif

@if($loan->status === 'pending' && auth()->user()->isDriver() && $loan->contract && in_array($loan->contract->status, ['fully_signed', 'approved']))
    <div class="alert-banner green">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
            <span>Contract signed by both parties! Your loan is now active.</span>
        </div>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><strong style="font-size:0.85rem;">Driver Information</strong></div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="user-avatar" style="width: 44px; height: 44px; font-size: 1rem;">
                        {{ substr($loan->driver->name ?? 'D', 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $loan->driver->name ?? '-' }}</h6>
                        <small style="color:var(--text-secondary);">{{ $loan->driver->email ?? '-' }}</small>
                    </div>
                </div>
                <div class="small">
                    <p class="mb-1"><strong>Phone:</strong> {{ $loan->driver->phone ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>NIDA:</strong> {{ $loan->driver->nida ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Role:</strong> Driver</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><strong style="font-size:0.85rem;">Bodaboda Owner</strong></div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="user-avatar" style="width: 44px; height: 44px; font-size: 1rem;">
                        {{ substr($loan->owner->name ?? 'O', 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $loan->owner->name ?? '-' }}</h6>
                        <small style="color:var(--text-secondary);">{{ $loan->owner->email ?? '-' }}</small>
                    </div>
                </div>
                <div class="small">
                    <p class="mb-1"><strong>Phone:</strong> {{ $loan->owner->phone ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>NIDA:</strong> {{ $loan->owner->nida ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Bodaboda:</strong> {{ $loan->motorcycle->plate_number ?? '-' }} ({{ $loan->motorcycle->make ?? '' }} {{ $loan->motorcycle->model ?? '' }})</p>
                    <p class="mb-1"><strong>Year:</strong> {{ $loan->motorcycle->year ?? '-' }} · <strong>Engine:</strong> {{ $loan->motorcycle->engine_cc ?? '-' }}cc</p>
                    @if(auth()->user()->isDriver() && $loan->owner && $loan->owner->latitude && $loan->owner->longitude)
                        <p class="mb-0">
                            <strong><i class="bi bi-geo-alt-fill" style="color:var(--emerald-600);"></i> Pickup:</strong>
                            {{ $loan->owner->location_name ?: $loan->owner->address ?: '' }}
                            <a href="https://www.google.com/maps?q={{ $loan->owner->latitude }},{{ $loan->owner->longitude }}" target="_blank" style="color:var(--emerald-600);font-weight:600;text-decoration:none;margin-left:4px;">
                                <i class="bi bi-box-arrow-up-right"></i> Maps
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center py-4">
                <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Total Amount</small>
                <span class="fw-bold" style="font-size:1.15rem;">TZS {{ number_format($loan->total_amount) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center py-4">
                <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Amount Paid</small>
                <span class="fw-bold" style="font-size:1.15rem;color:var(--emerald-600);">TZS {{ number_format($loan->amount_paid) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center py-4">
                <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Balance</small>
                <span class="fw-bold" style="font-size:1.15rem;color:var(--gold-500);">TZS {{ number_format($loan->balance) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center py-4">
                <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Repaid</small>
                <span class="fw-bold" style="font-size:1.15rem;">{{ $loan->progress }}%</span>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <span class="fw-bold" style="font-size: 0.85rem;">Repayment Progress</span>
            <span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span>
        </div>
        <div class="progress-track" style="height: 10px;">
            <div class="progress-fill emerald" style="width: {{ $loan->progress }}%;"></div>
        </div>
        <div class="d-flex justify-content-between mt-2">
            <small style="color:var(--text-secondary);">TZS {{ number_format($loan->amount_paid) }} paid</small>
            <small style="color:var(--text-secondary);">TZS {{ number_format($loan->total_amount) }} total</small>
        </div>
    </div>
</div>

<ul class="nav nav-tabs" id="loanTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#schedule-tab" type="button">
            <i class="bi bi-calendar-week me-1"></i>Payment Schedule
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#payments-tab" type="button">
            <i class="bi bi-credit-card me-1"></i>Payment History
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#agreement-tab" type="button">
            <i class="bi bi-file-earmark-text me-1"></i>Agreement
        </button>
    </li>
</ul>

<div class="tab-content" style="border: 1px solid var(--border); border-top: 0; background: var(--card-bg); border-radius: 0 0 var(--radius-lg) var(--radius-lg);">
    <div class="tab-pane fade show active" id="schedule-tab">
        <div class="p-3">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Week</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedule as $row)
                        <tr>
                            <td class="fw-semibold">Week {{ $row['week'] }}</td>
                            <td>{{ $row['due_date']->format('M d, Y') }}</td>
                            <td>TZS {{ number_format($row['amount']) }}</td>
                            <td>
                                <span class="badge-status {{ $row['status'] === 'paid' ? 'completed' : ($row['status'] === 'overdue' ? 'overdue' : 'pending') }}">
                                    {{ ucfirst($row['status']) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">
                            <div class="empty-state py-4">
                                <div class="empty-state-icon" style="width:56px;height:56px;font-size:1.4rem;"><i class="bi bi-calendar-x"></i></div>
                                <h5 style="font-size:0.95rem;">No schedule data</h5>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade" id="payments-tab">
        <div class="p-3">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loan->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="fw-semibold" style="color: var(--emerald-600);">TZS {{ number_format($payment->amount) }}</td>
                            <td><span class="badge-status active">{{ ucfirst($payment->method) }}</span></td>
                            <td>
                                <span class="badge-status {{ $payment->status === 'verified' ? 'verified' : ($payment->status === 'pending_verification' ? 'pending_verification' : 'rejected') }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                </span>
                            </td>
                            <td><small style="color:var(--text-secondary);">{{ $payment->reference_number ?? '-' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">
                            <div class="empty-state py-4">
                                <div class="empty-state-icon" style="width:56px;height:56px;font-size:1.4rem;"><i class="bi bi-credit-card"></i></div>
                                <h5 style="font-size:0.95rem;">No payments recorded</h5>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade" id="agreement-tab">
        <div class="p-4">
            <div class="text-center mb-4">
                <h5 class="fw-bold" style="color: var(--navy-900);">LOAN AGREEMENT CONTRACT</h5>
                <p style="font-size: 0.85rem;color:var(--text-secondary);">BodaLink Management System</p>
            </div>

            @if($loan->agreement_accepted_at)
                <div class="alert-banner green mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
                        <span>Agreement accepted by {{ $loan->driver->name }} on {{ $loan->agreement_accepted_at->format('M d, Y \a\t h:i A') }}</span>
                    </div>
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold" style="color: var(--navy-900);">PARTIES</h6>
                    <p class="mb-1"><strong>Owner (Lender):</strong> {{ $loan->owner->name ?? '-' }}</p>
                    <p class="small mb-2" style="color:var(--text-secondary);">Phone: {{ $loan->owner->phone ?? 'N/A' }} | Email: {{ $loan->owner->email ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Driver (Borrower):</strong> {{ $loan->driver->name ?? '-' }}</p>
                    <p class="small mb-0" style="color:var(--text-secondary);">Phone: {{ $loan->driver->phone ?? 'N/A' }} | Email: {{ $loan->driver->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold" style="color: var(--status-assigned-text);">BODABODA DETAILS</h6>
                    <table class="table table-sm mb-0">
                        <tr><th style="width: 40%;">Plate Number</th><td>{{ $loan->motorcycle->plate_number ?? '-' }}</td></tr>
                        <tr><th>Make / Model</th><td>{{ $loan->motorcycle->make ?? '-' }} {{ $loan->motorcycle->model ?? '-' }}</td></tr>
                        <tr><th>Year</th><td>{{ $loan->motorcycle->year ?? '-' }}</td></tr>
                        <tr><th>Engine CC</th><td>{{ $loan->motorcycle->engine_cc ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <h6 class="fw-bold" style="color: var(--emerald-600);">LOAN TERMS</h6>
            <table class="table table-bordered mb-3">
                <tr><th style="width: 40%;">Total Loan Amount</th><td>TZS {{ number_format($loan->total_amount) }}</td></tr>
                <tr><th>Weekly Installment</th><td>TZS {{ number_format($loan->weekly_installment) }}</td></tr>
                <tr><th>Duration</th><td>{{ $loan->duration_weeks }} weeks</td></tr>
                <tr><th>Start Date</th><td>{{ $loan->start_date->format('M d, Y') }}</td></tr>
                <tr><th>End Date</th><td>{{ $loan->end_date->format('M d, Y') }}</td></tr>
                <tr><th>Amount Paid</th><td style="color: var(--emerald-600);">TZS {{ number_format($loan->amount_paid) }}</td></tr>
                <tr><th>Outstanding Balance</th><td style="color: var(--gold-500);">TZS {{ number_format($loan->balance) }}</td></tr>
            </table>

            <h6 class="fw-bold" style="color: var(--gold-500);">TERMS AND CONDITIONS</h6>
            <ol class="small" style="color: var(--text-secondary);">
                <li>The Owner agrees to provide the bodaboda described above to the Driver for bodaboda operations.</li>
                <li>The Driver agrees to pay weekly installments of <strong>TZS {{ number_format($loan->weekly_installment) }}</strong> until the total loan of <strong>TZS {{ number_format($loan->total_amount) }}</strong> is fully repaid.</li>
                <li>Payments shall be made every week starting from <strong>{{ $loan->start_date->format('M d, Y') }}</strong>.</li>
                <li>The loan duration is <strong>{{ $loan->duration_weeks }} weeks</strong>, ending on <strong>{{ $loan->end_date->format('M d, Y') }}</strong>.</li>
                <li>Upon full repayment, ownership of the motorcycle shall be transferred to the Driver.</li>
                <li>The Driver is responsible for all operational costs including fuel, maintenance, and insurance.</li>
                <li>Late or missed payments may result in penalties as agreed between both parties.</li>
                <li>Either party may terminate this agreement with written notice of at least two (2) weeks.</li>
            </ol>

            <div class="row mt-5">
                <div class="col-md-6 text-center">
                    <hr style="width: 60%; margin: 0 auto; border-color: var(--border);">
                    <p class="small mt-2 mb-0 fw-bold">{{ $loan->owner->name ?? 'Owner' }}</p>
                    <p class="small" style="color:var(--text-secondary);">Owner / Lender</p>
                </div>
                <div class="col-md-6 text-center">
                    <hr style="width: 60%; margin: 0 auto; border-color: var(--border);">
                    <p class="small mt-2 mb-0 fw-bold">{{ $loan->driver->name ?? 'Driver' }}</p>
                    <p class="small" style="color:var(--text-secondary);">Driver / Borrower</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('loans.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left me-1"></i>Back to Loans</a>
</div>

@if(!auth()->user()->isDriver() && $loan->status === 'overdue')
<div class="modal fade" id="abscondedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form method="POST" action="{{ route('loans.reportAbsconded', $loan) }}">
                @csrf
                <div class="modal-header" style="background:#FEF2F2;border-bottom:1px solid #FECACA;">
                    <h6 class="modal-title fw-bold" style="color:#991B1B;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Report Vehicle Stolen</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:0.85rem;color:#991B1B;margin-bottom:12px;">
                        You are about to report that <strong>{{ $loan->driver->name ?? 'the driver' }}</strong> has absconded with <strong>{{ $loan->motorcycle->plate_number ?? 'this vehicle' }}</strong>.
                        This will:
                    </p>
                    <ul style="font-size:0.82rem;color:#666;padding-left:18px;margin-bottom:16px;">
                        <li>Mark the loan as <strong>defaulted</strong></li>
                        <li>Mark the bodaboda as <strong>stolen</strong></li>
                        <li>Notify all admins and the owner</li>
                        <li>Enable GPS tracking for recovery</li>
                        <li>Send an SMS to the driver</li>
                    </ul>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason / Details <span class="text-danger">*</span></label>
                        <textarea name="absconded_reason" class="form-control" rows="3" required placeholder="Describe the situation — when did the driver last make contact, when was the last payment, any other details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This will mark the vehicle as STOLEN.')">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Report Stolen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(!auth()->user()->isDriver() && $loan->status === 'defaulted' && $loan->absconded_at)
<div class="modal fade" id="recoverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form method="POST" action="{{ route('loans.recover', $loan) }}">
                @csrf
                <div class="modal-header" style="background:#E3F9EF;border-bottom:1px solid #A7F3D0;">
                    <h6 class="modal-title fw-bold" style="color:#065F46;"><i class="bi bi-arrow-return-left me-2"></i>Recover Vehicle</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:0.85rem;color:#065F46;margin-bottom:12px;">
                        You are marking <strong>{{ $loan->motorcycle->plate_number ?? 'this vehicle' }}</strong> as recovered. This will:
                    </p>
                    <ul style="font-size:0.82rem;color:#666;padding-left:18px;margin-bottom:16px;">
                        <li>Set the loan status back to <strong>overdue</strong></li>
                        <li>Remove the stolen status from the bodaboda</li>
                        <li>Notify all admins and the owner</li>
                    </ul>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recovery Notes <span class="text-danger">*</span></label>
                        <textarea name="recovery_notes" class="form-control" rows="3" required placeholder="How was the vehicle recovered? Any notes about its condition..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald" onclick="return confirm('Confirm vehicle recovery?')">
                        <i class="bi bi-check-circle me-1"></i> Confirm Recovery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endif
@endsection
