@extends('layouts.app')
@section('title', 'Dashboard')

@php $currentUser = auth()->user(); @endphp

@if(!$currentUser->isAdmin() && !$currentUser->hasVerificationDocuments())
    @section('page-title', 'Dashboard')
@elseif(!$currentUser->isAdmin() && $currentUser->hasSubmittedVerification() && $currentUser->isPending())
    @section('page-title', 'Dashboard')
@elseif($currentUser->isAdmin())
    @section('page-title', 'Admin Dashboard')
@elseif($currentUser->isOwner())
    @section('page-title', 'Owner Dashboard')
@else
    @section('page-title', 'Driver Dashboard')
@endif

@section('content')

@if(!$currentUser->isAdmin() && !$currentUser->hasVerificationDocuments())
    <div class="alert-banner gold">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-exclamation" style="color:var(--gold-500);font-size:1.2rem;"></i>
            <div>
                <strong style="color:var(--text);">Account Verification Required</strong><br>
                <span style="color:var(--text-muted);">Submit your NIDA, profile photo, and ID photo to access all services.</span>
            </div>
        </div>
        <a href="{{ route('verification.form') }}" class="btn btn-sm" style="background:var(--gold-500);color:#fff;white-space:nowrap;font-weight:700;">
            <i class="bi bi-shield-check me-1"></i> Verify Now
        </a>
    </div>
@elseif(!$currentUser->isAdmin() && $currentUser->hasSubmittedVerification() && $currentUser->isPending())
    <div class="alert-banner blue">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-hourglass-split" style="color:var(--status-assigned-text);font-size:1.2rem;"></i>
            <div>
                <strong style="color:var(--text);">Verification Under Review</strong><br>
                <span style="color:var(--text-muted);">Your documents are being reviewed. You will be notified once approved.</span>
            </div>
        </div>
        <a href="{{ route('verification.form') }}" class="btn btn-sm btn-outline">
            <i class="bi bi-eye me-1"></i> View Status
        </a>
    </div>
@endif

@if($currentUser->isDriver())

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- DRIVER DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════ --}}

    <div class="dash-welcome">
        <h4>Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ $currentUser->name }}</h4>
        <p>Here's your loan overview at a glance.</p>
    </div>

    @if(!$loan)
        <div class="card text-center" style="padding:60px 24px;">
            <div style="width:72px;height:72px;border-radius:50%;background:var(--status-assigned-bg);display:inline-flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                <i class="bi bi-bicycle" style="font-size:2rem;color:var(--status-assigned-text);"></i>
            </div>
            <h5 class="fw-bold mb-2" style="color:var(--text);">No Active Loan</h5>
            <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:24px;">Browse available bodabodas and apply to start your hire-purchase journey.</p>
            <a href="{{ route('driver.marketplace') }}" class="btn btn-gold">
                <i class="bi bi-shop me-1"></i> Browse Bodabodas
            </a>
        </div>
    @else
        {{-- Overdue warning --}}
        @if($loan->status === 'overdue')
            <div class="overdue-strip">
                <div class="od-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div>
                    <div class="od-title">Payment Overdue</div>
                    <div class="od-text">Your loan payment is past due. Please make a payment to avoid further charges.</div>
                </div>
                <a href="{{ route('payments.create') }}" class="btn btn-danger btn-sm ms-auto" style="white-space:nowrap;">Pay Now</a>
            </div>
        @endif

        {{-- Main stat cards --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--status-assigned-bg);color:var(--status-assigned-text);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="stat-value" style="font-size:1.3rem;">TZS {{ number_format($loan->total_amount) }}</div>
                    <div class="stat-label">Total Loan</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--status-verified-bg);color:var(--status-verified-text);">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-value" style="font-size:1.3rem;">TZS {{ number_format($totalPaid) }}</div>
                    <div class="stat-label">Paid So Far</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--status-overdue-bg);color:var(--status-overdue-text);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-value" style="font-size:1.3rem;">TZS {{ number_format($loan->balance) }}</div>
                    <div class="stat-label">Balance Left</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--gold-100);color:var(--gold-500);">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="stat-value" style="font-size:1.3rem;">{{ $nextPayment ? $nextPayment->format('M d') : 'N/A' }}</div>
                    <div class="stat-label">Next Due Date</div>
                </div>
            </div>
        </div>

        {{-- Loan progress --}}
        <div class="card mb-4">
            <div class="card-body" style="padding:24px;">
                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                    <div>
                        <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);font-weight:700;margin-bottom:2px;">Repayment Progress</div>
                        <h5 class="fw-bold mb-0" style="color:var(--text);">
                            <i class="bi bi-bicycle me-2" style="color:var(--gold-500);"></i>{{ $motorcycle->make ?? '' }} {{ $motorcycle->model ?? '' }}
                            <span style="font-weight:400;color:var(--text-muted);font-size:0.85rem;">({{ $motorcycle->plate_number ?? '-' }})</span>
                        </h5>
                    </div>
                    <div class="text-end">
                        <div style="font-size:1.8rem;font-weight:800;color:var(--gold-500);line-height:1;">{{ $loan->progress ?? 0 }}%</div>
                        <span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span>
                    </div>
                </div>

                <div class="progress-track" style="height:10px;margin:16px 0;">
                    <div class="progress-fill" style="width:{{ $loan->progress ?? 0 }}%;"></div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-4 text-center">
                        <div style="font-size:0.75rem;color:var(--text-muted);font-weight:500;">Weeks Paid</div>
                        <div class="fw-bold" style="color:var(--status-verified-text);">{{ $weeksPaid }} / {{ $totalWeeks }}</div>
                    </div>
                    <div class="col-4 text-center">
                        <div style="font-size:0.75rem;color:var(--text-muted);font-weight:500;">Weekly Payment</div>
                        <div class="fw-bold" style="color:var(--text);">TZS {{ number_format($loan->weekly_installment) }}</div>
                    </div>
                    <div class="col-4 text-center">
                        <div style="font-size:0.75rem;color:var(--text-muted);font-weight:500;">Weeks Remaining</div>
                        <div class="fw-bold" style="color:var(--gold-500);">{{ $weeksRemaining }}</div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    @if($loan->status === 'active' || $loan->status === 'overdue')
                    <a href="{{ route('payments.create') }}" class="btn btn-gold">
                        <i class="bi bi-credit-card-2-front me-1"></i> Make Payment
                    </a>
                    @endif
                    <a href="{{ route('loans.index') }}" class="btn btn-outline-navy btn-sm">
                        <i class="bi bi-eye me-1"></i> Loan Details
                    </a>
                </div>
            </div>
        </div>

        {{-- Assigned vehicle + owner --}}
        @if($motorcycle)
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card" style="height:100%;">
                    <div class="card-header"><strong style="color:var(--text);font-size:0.9rem;"><i class="bi bi-bicycle me-2" style="color:var(--gold-500);"></i>Assigned Bodaboda</strong></div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Plate Number</span>
                            <span class="info-value">{{ $motorcycle->plate_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Make / Model</span>
                            <span class="info-value">{{ $motorcycle->make }} {{ $motorcycle->model }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Year</span>
                            <span class="info-value">{{ $motorcycle->year ?? '-' }}</span>
                        </div>
                        @if($motorcycle->engine_cc)
                        <div class="info-row">
                            <span class="info-label">Engine CC</span>
                            <span class="info-value">{{ $motorcycle->engine_cc }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @if($owner)
            <div class="col-md-6">
                <div class="card" style="height:100%;">
                    <div class="card-header"><strong style="color:var(--text);font-size:0.9rem;"><i class="bi bi-person-fill me-2" style="color:var(--status-assigned-text);"></i>Bodaboda Owner</strong></div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Name</span>
                            <span class="info-value">{{ $owner->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone</span>
                            <span class="info-value">{{ $owner->phone ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $owner->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Recent Payments --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong style="color:var(--text);font-size:0.9rem;">Recent Payments</strong>
                <a href="{{ route('payments.index') }}" style="font-size:0.8rem;color:var(--status-assigned-text);font-weight:600;text-decoration:none;">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="fw-semibold">TZS {{ number_format($payment->amount) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</td>
                                <td>
                                    <span class="badge-status {{ $payment->status === 'verified' ? 'verified' : ($payment->status === 'pending_verification' ? 'pending_verification' : 'rejected') }}">
                                        {{ $payment->status === 'pending_verification' ? 'Pending' : ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color:var(--text-muted);font-size:0.85rem;">
                                    <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                                    No payments yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="dash-section-title">Quick Actions</div>
        <div class="row g-3">
            <div class="col-sm-4">
                <a href="{{ route('driver.apps') }}" class="quick-link-card">
                    <div class="ql-icon" style="background:var(--status-assigned-bg);color:var(--status-assigned-text);">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div>
                        <div class="ql-label">My Applications</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
                </a>
            </div>
            <div class="col-sm-4">
                <a href="{{ route('chat.index') }}" class="quick-link-card">
                    <div class="ql-icon" style="background:var(--status-verified-bg);color:var(--status-verified-text);">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div>
                        <div class="ql-label">Messages</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
                </a>
            </div>
            <div class="col-sm-4">
                <a href="{{ route('driver.marketplace') }}" class="quick-link-card">
                    <div class="ql-icon" style="background:var(--gold-100);color:var(--gold-500);">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div>
                        <div class="ql-label">Browse Bodabodas</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
                </a>
            </div>
            @php $adminUser = \App\Models\User::where('role','admin')->first(); @endphp
            @if($adminUser)
            <div class="col-sm-4">
                <a href="{{ route('chat.start.direct', $adminUser->id) }}" class="quick-link-card">
                    <div class="ql-icon" style="background:var(--emerald-100,#E3F9EF);color:var(--emerald-600,#059669);">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div>
                        <div class="ql-label">Chat with Admin</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
                </a>
            </div>
            @endif
        </div>
    @endif


{{-- ═══════════════════════════════════════════════ --}}
{{-- ADMIN DASHBOARD --}}
{{-- ═══════════════════════════════════════════════ --}}
@elseif($currentUser->isAdmin())

    <div class="dash-welcome">
        <h4>Welcome back, {{ $currentUser->name }}</h4>
        <p>Here's what's happening across the platform today.</p>
    </div>

    {{-- Overdue alert --}}
    @if($overdueLoans > 0)
        <div class="overdue-strip">
            <div class="od-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="od-title">{{ $overdueLoans }} Overdue Loan{{ $overdueLoans !== 1 ? 's' : '' }}</div>
                <div class="od-text">Some drivers have missed their payment deadlines. Review and take action.</div>
            </div>
            <a href="{{ route('admin.overdue') }}" class="btn btn-danger btn-sm ms-auto" style="white-space:nowrap;">Review</a>
        </div>
    @endif

    {{-- Top stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--status-verified-bg);color:var(--status-verified-text);">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="stat-value" style="font-size:1.3rem;">TZS {{ number_format($totalRevenue) }}</div>
                <div class="stat-label">Total Collected</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--status-assigned-bg);color:var(--status-assigned-text);">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="stat-value" style="font-size:1.3rem;">{{ number_format($activeLoans) }}</div>
                <div class="stat-label">Active Loans</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--gold-100);color:var(--gold-500);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-value" style="font-size:1.3rem;">{{ number_format($totalOwners + $totalDrivers) }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--status-overdue-bg);color:var(--status-overdue-text);">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-value" style="font-size:1.3rem;">{{ number_format($overdueLoans) }}</div>
                <div class="stat-label">Overdue</div>
            </div>
        </div>
    </div>

    {{-- Secondary stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card" style="text-align:center;">
                <div class="stat-value" style="color:var(--status-assigned-text);font-size:1.5rem;">{{ $collectionRate }}%</div>
                <div class="stat-label">Collection Rate</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card" style="text-align:center;">
                <div class="stat-value" style="color:var(--status-verified-text);font-size:1.3rem;">TZS {{ number_format($weeklyCollections) }}</div>
                <div class="stat-label">This Week</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card" style="text-align:center;">
                <div class="stat-value" style="color:var(--gold-500);font-size:1.3rem;">TZS {{ number_format($monthlyCollections) }}</div>
                <div class="stat-label">This Month</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card" style="text-align:center;">
                <div class="stat-value" style="font-size:1.3rem;">TZS {{ number_format($totalPendingAmount) }}</div>
                <div class="stat-label">Pending Receipts ({{ $pendingPayments }})</div>
            </div>
        </div>
    </div>

    {{-- Pending queues --}}
    <div class="dash-section-title">Pending Actions</div>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.drivers') }}" class="quick-link-card" style="border-left:3px solid var(--gold-500);">
                <div class="ql-icon" style="background:var(--status-pending-bg);color:var(--gold-500);">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <div class="ql-count">{{ number_format($pendingUsers) }}</div>
                    <div class="ql-label">Pending Drivers</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.payments') }}" class="quick-link-card" style="border-left:3px solid var(--status-assigned-text);">
                <div class="ql-icon" style="background:var(--status-assigned-bg);color:var(--status-assigned-text);">
                    <i class="bi bi-credit-card-2-front-fill"></i>
                </div>
                <div>
                    <div class="ql-count">{{ number_format($pendingPayments) }}</div>
                    <div class="ql-label">Pending Payments</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.applications') }}" class="quick-link-card" style="border-left:3px solid var(--status-verified-text);">
                <div class="ql-icon" style="background:var(--status-verified-bg);color:var(--status-verified-text);">
                    <i class="bi bi-file-earmark-check"></i>
                </div>
                <div>
                    <div class="ql-count">{{ number_format($pendingApplications) }}</div>
                    <div class="ql-label">Pending Applications</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
    </div>

    {{-- Loan distribution + Revenue mini chart --}}
    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <div class="card" style="height:100%;">
                <div class="card-header"><strong style="color:var(--text);font-size:0.9rem;">Loan Distribution</strong></div>
                <div class="card-body">
                    @php $totalLoans = max(array_sum($loansByStatus), 1); @endphp
                    @foreach($loansByStatus as $status => $count)
                        @if($count > 0)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="badge-status {{ $status }}" style="font-size:0.72rem;">{{ ucfirst($status) }}</span>
                                <span style="font-size:0.8rem;font-weight:700;color:var(--text);">{{ $count }} <span style="font-weight:400;color:var(--text-muted);">({{ round(($count / $totalLoans) * 100) }}%)</span></span>
                            </div>
                            <div class="progress-track" style="height:6px;">
                                <div class="progress-fill @if($status === 'completed') emerald @endif" style="width:{{ ($count / $totalLoans) * 100 }}%;"></div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    @if(array_sum($loansByStatus) === 0)
                        <div class="text-center py-3" style="color:var(--text-muted);font-size:0.85rem;">
                            <i class="bi bi-inbox" style="font-size:1.2rem;display:block;margin-bottom:4px;"></i> No loans yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card" style="height:100%;">
                <div class="card-header"><strong style="color:var(--text);font-size:0.9rem;">Revenue Trend (12 months)</strong></div>
                <div class="card-body">
                    @if($revenueByMonth->isNotEmpty())
                        @php $maxRevenue = $revenueByMonth->max('total'); @endphp
                        <div class="mini-chart" style="height:100px;">
                            @foreach($revenueByMonth as $m)
                                <div class="bar" style="height:{{ $maxRevenue > 0 ? max(($m['total'] / $maxRevenue) * 100, 4) : 4 }}%;" title="{{ $m['month'] }}: TZS {{ number_format($m['total']) }}"></div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between mt-2" style="font-size:0.65rem;color:var(--text-muted);">
                            <span>{{ $revenueByMonth->first()['month'] }}</span>
                            <span>{{ $revenueByMonth->last()['month'] }}</span>
                        </div>
                    @else
                        <div class="text-center py-4" style="color:var(--text-muted);font-size:0.85rem;">
                            <i class="bi bi-bar-chart" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i> No revenue data yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick nav --}}
    <div class="dash-section-title">Management</div>
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.vehicles') }}" class="quick-link-card">
                <div class="ql-icon" style="background:var(--status-assigned-bg);color:var(--status-assigned-text);">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="ql-count">{{ number_format($pendingVehicles) }}</div>
                    <div class="ql-label">Pending Bodabodas</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.overdue') }}" class="quick-link-card">
                <div class="ql-icon" style="background:var(--status-overdue-bg);color:var(--status-overdue-text);">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <div class="ql-count">{{ number_format($overdueLoans) }}</div>
                    <div class="ql-label">Overdue</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.users') }}" class="quick-link-card">
                <div class="ql-icon" style="background:var(--gold-100);color:var(--gold-500);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="ql-count">{{ number_format($totalOwners + $totalDrivers) }}</div>
                    <div class="ql-label">All Users</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.loans.progress') }}" class="quick-link-card">
                <div class="ql-icon" style="background:var(--status-verified-bg);color:var(--status-verified-text);">
                    <i class="bi bi-kanban"></i>
                </div>
                <div>
                    <div class="ql-label">Loan Progress</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong style="color:var(--text);font-size:0.9rem;">Recent Payments</strong>
            <a href="{{ route('admin.payments') }}" style="font-size:0.8rem;color:var(--status-assigned-text);font-weight:600;text-decoration:none;">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Driver</th>
                        <th>Bodaboda</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="fw-semibold">{{ $payment->loan->driver->name ?? '-' }}</td>
                            <td>{{ $payment->loan->motorcycle->plate_number ?? '-' }}</td>
                            <td class="fw-semibold">TZS {{ number_format($payment->amount) }}</td>
                            <td>
                                <span class="badge-status {{ $payment->status === 'verified' ? 'verified' : ($payment->status === 'pending_verification' ? 'pending_verification' : 'rejected') }}">
                                    {{ $payment->status === 'pending_verification' ? 'Pending' : ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:var(--text-muted);font-size:0.85rem;">
                                <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                                No payments yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong style="color:var(--text);font-size:0.9rem;">Recent Users</strong>
            <a href="{{ route('admin.users') }}" style="font-size:0.8rem;color:var(--status-assigned-text);font-weight:600;text-decoration:none;">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Joined</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                        <tr>
                            <td>{{ $u->created_at->format('M d, Y') }}</td>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td>{{ $u->phone ?? '-' }}</td>
                            <td>
                                <span class="badge-status {{ $u->role === 'driver' ? 'assigned' : ($u->role === 'owner' ? 'verified' : 'pending') }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-status {{ $u->approval_status }}">{{ ucfirst($u->approval_status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:var(--text-muted);font-size:0.85rem;">
                                <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                                No users yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


{{-- ═══════════════════════════════════════════════ --}}
{{-- OWNER DASHBOARD --}}
{{-- ═══════════════════════════════════════════════ --}}
@elseif($currentUser->isOwner())

    <div class="dash-welcome">
        <h4>Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ $currentUser->name }}</h4>
        <p>Manage your fleet and track collections in real time.</p>
    </div>

    {{-- Overdue alert --}}
    @if($overdueLoans > 0)
        <div class="overdue-strip">
            <div class="od-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="od-title">{{ $overdueLoans }} Overdue Loan{{ $overdueLoans !== 1 ? 's' : '' }}</div>
                <div class="od-text">Some of your drivers have overdue payments. Follow up promptly.</div>
            </div>
            <a href="{{ route('owner.vehicles') }}" class="btn btn-danger btn-sm ms-auto" style="white-space:nowrap;">View Fleet</a>
        </div>
    @endif

    {{-- Fleet summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--status-assigned-bg);color:var(--status-assigned-text);">
                    <i class="bi bi-bicycle"></i>
                </div>
                <div class="stat-value" style="font-size:1.3rem;">{{ number_format($totalMotorcycles) }}</div>
                    <div class="stat-label">Total Bodabodas</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--status-verified-bg);color:var(--status-verified-text);">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div class="stat-value" style="font-size:1.3rem;">{{ number_format($assignedDrivers) }}</div>
                <div class="stat-label">Assigned Drivers</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--gold-100);color:var(--gold-500);">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="stat-value" style="font-size:1.3rem;">{{ number_format($activeLoans) }}</div>
                <div class="stat-label">Active Loans</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--status-overdue-bg);color:var(--status-overdue-text);">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-value" style="font-size:1.3rem;">TZS {{ number_format($outstandingBalance) }}</div>
                <div class="stat-label">Outstanding Balance</div>
            </div>
        </div>
    </div>

    {{-- Money cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card" style="text-align:center;">
                <div class="stat-value" style="font-size:1.3rem;color:var(--status-verified-text);">TZS {{ number_format($weeklyCollections) }}</div>
                <div class="stat-label">Collected This Week</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card" style="text-align:center;">
                <div class="stat-value" style="font-size:1.3rem;color:var(--gold-500);">TZS {{ number_format($monthlyCollections) }}</div>
                <div class="stat-label">Collected This Month</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card" style="text-align:center;">
                <div class="stat-value" style="font-size:1.3rem;">TZS {{ number_format($totalCollected) }}</div>
                <div class="stat-label">Total Collected All Time</div>
            </div>
        </div>
    </div>

    {{-- Fleet health + Pending payments --}}
    <div class="row g-3 mb-4">
        <div class="col-md-7">
            <div class="card" style="height:100%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong style="color:var(--text);font-size:0.9rem;">Fleet Health</strong>
                    <a href="{{ route('owner.vehicles') }}" style="font-size:0.8rem;color:var(--status-assigned-text);font-weight:600;text-decoration:none;">View All <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    @php $totalM = max($totalMotorcycles, 1); @endphp
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-size:0.8rem;color:var(--text-secondary);">{{ $totalMotorcycles }} bodaboda{{ $totalMotorcycles !== 1 ? 's' : '' }} registered</span>
                        <a href="{{ route('owner.vehicles.create') }}" class="btn btn-gold btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add Bodaboda
                        </a>
                    </div>

                    {{-- Health bar --}}
                    <div class="health-bar mb-3">
                        @if($motorcyclesByStatus['assigned'] > 0)
                            <div class="segment" style="width:{{ ($motorcyclesByStatus['assigned'] / $totalM) * 100 }}%;background:var(--status-assigned-text);"></div>
                        @endif
                        @if($motorcyclesByStatus['available'] > 0)
                            <div class="segment" style="width:{{ ($motorcyclesByStatus['available'] / $totalM) * 100 }}%;background:var(--status-verified-text);"></div>
                        @endif
                        @if($motorcyclesByStatus['completed'] > 0)
                            <div class="segment" style="width:{{ ($motorcyclesByStatus['completed'] / $totalM) * 100 }}%;background:var(--text-muted);"></div>
                        @endif
                    </div>
                    <div class="d-flex gap-3 mb-3" style="font-size:0.75rem;">
                        <div class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:2px;background:var(--status-assigned-text);"></span> Assigned ({{ $motorcyclesByStatus['assigned'] }})</div>
                        <div class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:2px;background:var(--status-verified-text);"></span> Available ({{ $motorcyclesByStatus['available'] }})</div>
                        <div class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:2px;background:var(--text-muted);"></span> Completed ({{ $motorcyclesByStatus['completed'] }})</div>
                    </div>

                    {{-- Loan status breakdown --}}
                    @php $totalOLoans = max($totalOwnerLoans, 1); @endphp
                    @if($totalOwnerLoans > 0)
                    <div class="mt-2">
                        <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);font-weight:700;margin-bottom:10px;">Loan Status</div>
                        @foreach($loansByStatus as $status => $count)
                            @if($count > 0)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between" style="font-size:0.8rem;">
                                    <span class="badge-status {{ $status }}" style="font-size:0.7rem;padding:2px 8px;">{{ ucfirst($status) }}</span>
                                    <span style="font-weight:600;color:var(--text);">{{ $count }}</span>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card" style="height:100%;">
                <div class="card-header"><strong style="color:var(--text);font-size:0.9rem;">Pending Payments</strong></div>
                <div class="card-body">
                    @if($pendingPayments > 0)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:48px;height:48px;border-radius:var(--radius);background:var(--status-pending-bg);display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-hourglass-split" style="font-size:1.2rem;color:var(--gold-500);"></i>
                            </div>
                            <div>
                                <div style="font-size:1.5rem;font-weight:800;color:var(--text);">{{ $pendingPayments }}</div>
                                <div style="font-size:0.8rem;color:var(--text-secondary);">Receipts awaiting review</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Pending Amount</span>
                            <span class="info-value" style="color:var(--gold-500);">TZS {{ number_format($pendingPaymentsTotal) }}</span>
                        </div>
                        <a href="{{ route('payments.index') }}" class="btn btn-gold btn-sm mt-3">
                            <i class="bi bi-eye me-1"></i> Review Payments
                        </a>
                    @else
                        <div class="text-center py-4" style="color:var(--text-muted);font-size:0.85rem;">
                            <i class="bi bi-check-circle" style="font-size:1.5rem;display:block;margin-bottom:6px;color:var(--status-verified-text);"></i>
                            All payments are up to date.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent payments table --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong style="color:var(--text);font-size:0.9rem;">Recent Payments</strong>
            <a href="{{ route('payments.index') }}" style="font-size:0.8rem;color:var(--status-assigned-text);font-weight:600;text-decoration:none;">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Driver</th>
                        <th>Plate</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td>{{ $payment->loan->driver->name ?? '-' }}</td>
                            <td>{{ $payment->loan->motorcycle->plate_number ?? '-' }}</td>
                            <td class="fw-semibold">TZS {{ number_format($payment->amount) }}</td>
                            <td>
                                <span class="badge-status {{ $payment->status === 'verified' ? 'verified' : ($payment->status === 'pending_verification' ? 'pending_verification' : 'rejected') }}">
                                    {{ $payment->status === 'pending_verification' ? 'Pending' : ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:var(--text-muted);font-size:0.85rem;">
                                <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                                No payments yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Driver status list --}}
    @if($driversWithLoans->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header"><strong style="color:var(--text);font-size:0.9rem;">Driver Loan Status</strong></div>
        <div class="card-body">
            @foreach($driversWithLoans as $drv)
                @php $drvLoan = $drv->loans->first(); @endphp
                @if($drvLoan)
                <div class="driver-row">
                    <div class="drv-avatar">{{ strtoupper(substr($drv->name, 0, 2)) }}</div>
                    <div style="flex:1;">
                        <div class="drv-name">{{ $drv->name }}</div>
                        <div class="drv-meta">{{ $drvLoan->motorcycle->plate_number ?? '-' }} &middot; TZS {{ number_format($drvLoan->balance) }} remaining</div>
                    </div>
                    <span class="badge-status {{ $drvLoan->status }}">{{ ucfirst($drvLoan->status) }}</span>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recent Bodabodas --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
                    <strong style="color:var(--text);font-size:0.9rem;">My Bodabodas</strong>
            <a href="{{ route('owner.vehicles') }}" style="font-size:0.8rem;color:var(--status-assigned-text);font-weight:600;text-decoration:none;">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Plate</th>
                        <th>Make / Model</th>
                        <th>Driver</th>
                        <th>Status</th>
                        <th>Loan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentMotorcycles as $moto)
                        <tr>
                            <td class="fw-bold">{{ $moto->plate_number }}</td>
                            <td>{{ $moto->make }} {{ $moto->model }}</td>
                            <td>{{ $moto->driver->name ?? 'Unassigned' }}</td>
                            <td>
                                <span class="badge-status {{ $moto->status }}">{{ ucfirst(str_replace('_', ' ', $moto->status)) }}</span>
                            </td>
                            <td>
                                @if($moto->loan)
                                    <span class="badge-status {{ $moto->loan->status }}">{{ ucfirst($moto->loan->status) }}</span>
                                @else
                                    <span style="color:var(--text-muted);font-size:0.8rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:var(--text-muted);font-size:0.85rem;">
                                <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                                No Bodabodas yet. Add your first one!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-sm-4">
            <a href="{{ route('chat.index') }}" class="quick-link-card">
                <div class="ql-icon" style="background:var(--status-verified-bg);color:var(--status-verified-text);">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <div><div class="ql-label">Messages</div></div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
        @php $adminUser = \App\Models\User::where('role','admin')->first(); @endphp
        @if($adminUser)
        <div class="col-sm-4">
            <a href="{{ route('chat.start.direct', $adminUser->id) }}" class="quick-link-card">
                <div class="ql-icon" style="background:var(--emerald-100,#E3F9EF);color:var(--emerald-600,#059669);">
                    <i class="bi bi-headset"></i>
                </div>
                <div><div class="ql-label">Chat with Admin</div></div>
                <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);"></i>
            </a>
        </div>
        @endif
    </div>

@endif
@endsection
