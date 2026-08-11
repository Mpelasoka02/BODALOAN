@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card" style="padding:24px;">
            <h6 class="mb-3 fw-bold" style="color:var(--text);"><i class="bi bi-person me-2" style="color:var(--navy-900);"></i>Account</h6>
            @if($user->profile_photo_url)
            <div class="text-center mb-3">
                <img src="{{ $user->profile_photo_url }}" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:3px solid var(--border);">
            </div>
            @endif
            <table class="table table-sm mb-0">
                <tr><th style="width:35%;color:var(--text-secondary);">Name</th><td class="fw-semibold">{{ $user->name }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Email</th><td>{{ $user->email }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Phone</th><td>{{ $user->phone ?? '-' }}</td></tr>
                <tr><th style="color:var(--text-secondary);">NIDA</th><td>{{ $user->nida ?? '-' }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Status</th><td><span class="badge-status {{ $user->approval_status }}">{{ ucfirst($user->approval_status) }}</span></td></tr>
            </table>
            @if($user->id_photo_url)
            <div class="mt-3">
                <small style="color:var(--text-secondary);display:block;margin-bottom:6px;font-weight:600;">ID Photo</small>
                <img src="{{ $user->id_photo_url }}" style="width:100%;max-height:120px;object-fit:cover;border:2px solid var(--border);border-radius:10px;">
            </div>
            @endif
        </div>

        @if($user->assignedMotorcycle)
        <div class="card mt-3" style="padding:24px;">
            <h6 class="mb-3 fw-bold" style="color:var(--text);"><i class="bi bi-motorcycle me-2" style="color:var(--emerald-600);"></i>Assigned Motorcycle</h6>
            <table class="table table-sm mb-0">
                <tr><th style="width:35%;color:var(--text-secondary);">Plate</th><td class="fw-bold" style="color:var(--navy-900);">{{ $user->assignedMotorcycle->plate_number }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Make / Model</th><td>{{ $user->assignedMotorcycle->make }} {{ $user->assignedMotorcycle->model }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Year</th><td>{{ $user->assignedMotorcycle->year }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Owner</th><td>{{ $user->assignedMotorcycle->owner->name ?? '-' }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Owner Phone</th><td>{{ $user->assignedMotorcycle->owner->phone ?? '-' }}</td></tr>
            </table>
        </div>
        @endif

        @if($completedLoans > 0)
        <div class="card mt-3" style="padding:20px;text-align:center;">
            <span class="fw-bold" style="font-size:1.5rem;color:var(--navy-900);">{{ $completedLoans }}</span>
            <small style="color:var(--text-secondary);display:block;">Loan{{ $completedLoans > 1 ? 's' : '' }} Completed</small>
        </div>
        @endif
    </div>

    <div class="col-lg-7">
        @if($loan)
        <div class="card" style="padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h6 class="mb-0 fw-bold" style="color:var(--text);"><i class="bi bi-wallet2 me-2" style="color:var(--gold-500);"></i>Active Loan</h6>
                <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-navy">Details</a>
            </div>
            <div class="mb-3">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <small class="fw-semibold" style="color:var(--text-secondary);">Progress</small>
                    <small class="fw-semibold" style="color:var(--navy-900);">{{ $loan->progress }}%</small>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width:{{ $loan->progress }}%;"></div>
                </div>
            </div>
            <table class="table table-sm mb-0">
                <tr><th style="width:35%;color:var(--text-secondary);">Total</th><td>TZS {{ number_format($loan->total_amount) }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Paid</th><td style="color:var(--emerald-600);">TZS {{ number_format($loan->amount_paid) }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Balance</th><td class="fw-bold">TZS {{ number_format($loan->balance) }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Weekly</th><td>TZS {{ number_format($loan->weekly_installment) }}</td></tr>
                <tr><th style="color:var(--text-secondary);">Status</th><td><span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span></td></tr>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon"><i class="bi bi-wallet2"></i></div>
            <h5>No active loan</h5>
            <p>Apply for a bodaboda and start paying weekly.</p>
        </div>
        @endif

        <div class="mt-3">
            <a href="{{ route('profile.edit') }}" class="btn btn-outline btn-sm"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
        </div>
    </div>
</div>
@endsection