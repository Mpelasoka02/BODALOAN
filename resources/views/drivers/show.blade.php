@extends('layouts.app')
@section('title', $driver->name)
@section('page-title', $driver->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}" style="color:var(--text-secondary);text-decoration:none;">Drivers</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">{{ $driver->name }}</li>
    </ol>
</nav>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><strong style="font-size:0.85rem;"><i class="bi bi-person-badge me-2" style="color:var(--gold-500);"></i>Driver Profile</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th style="width:35%">Name</th><td class="fw-semibold">{{ $driver->name }}</td></tr>
                    <tr><th>Email</th><td>{{ $driver->email }}</td></tr>
                    <tr><th>Phone</th><td>{{ $driver->phone ?? '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge-status {{ $driver->approval_status }}">{{ ucfirst($driver->approval_status) }}</span></td></tr>
                    <tr><th>Motorcycle</th>
                        <td>
                            @if($driver->assignedMotorcycle)
                                <span class="badge-status active">{{ $driver->assignedMotorcycle->plate_number }}</span>
                            @else
                                <span style="color:var(--text-secondary);">Unassigned</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><strong style="font-size:0.85rem;"><i class="bi bi-cash-coin me-2" style="color:var(--emerald-600);"></i>Loan History</strong></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Motorcycle</th><th>Total</th><th>Paid</th><th>Balance</th><th>Progress</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($driver->loans as $loan)
                            <tr>
                                <td>{{ $loan->motorcycle->plate_number ?? '-' }}</td>
                                <td>TZS {{ number_format($loan->total_amount) }}</td>
                                <td style="color:var(--emerald-600);">TZS {{ number_format($loan->amount_paid) }}</td>
                                <td>TZS {{ number_format($loan->balance) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress-track flex-grow-1">
                                            <div class="progress-fill emerald" style="width:{{ $loan->progress }}%"></div>
                                        </div>
                                        <small style="color:var(--text-secondary);">{{ $loan->progress }}%</small>
                                    </div>
                                </td>
                                <td><span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-4">
                                        <div class="empty-state-icon" style="width:56px;height:56px;font-size:1.4rem;"><i class="bi bi-wallet2"></i></div>
                                        <h5 style="font-size:0.95rem;">No loans yet</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-outline btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    <a href="{{ route('drivers.index') }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
@endsection
