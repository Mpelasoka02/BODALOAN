@extends('layouts.app')
@section('title', 'Loans')
@section('page-title', 'Loans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--text);">Loans</h4>
        <p class="mb-0 mt-1" style="font-size:0.82rem;color:var(--text-secondary);">Manage and track all loan agreements</p>
    </div>
    <div></div>
</div>

@if(session('success'))
    <div class="alert-banner green">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="defaulted" {{ request('status') === 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-navy w-100"><i class="bi bi-search"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Driver</th>
                    <th>Motorcycle</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                    <tr>
                        <td class="fw-semibold">#{{ $loan->id }}</td>
                        <td>{{ $loan->driver->name ?? '-' }}</td>
                        <td><small class="text-muted">{{ $loan->motorcycle->plate_number ?? '-' }}</small></td>
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
                        <td><a href="{{ route('loans.show', $loan) }}" class="btn btn-icon"><i class="bi bi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-wallet2"></i></div>
                                <h5>No loans found</h5>
                                <p>No loan agreements match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <span style="font-size:0.82rem;color:var(--text-secondary);">{{ $loans->firstItem() ?? 0 }} to {{ $loans->lastItem() ?? 0 }} of {{ $loans->total() }}</span>
    {{ $loans->withQueryString()->links() }}
</div>
@endsection
