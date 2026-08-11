@extends('layouts.app')
@section('title', 'Drivers')
@section('page-title', 'Drivers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--text);">Drivers</h4>
        <p class="mb-0 mt-1" style="font-size:0.82rem;color:var(--text-secondary);">Manage registered drivers</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('drivers.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg"></i> Add Driver</a>
        <a href="{{ route('drivers.index', ['export' => 'csv']) }}" class="btn btn-outline btn-sm"><i class="bi bi-download"></i> Export</a>
    </div>
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
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="approval_status" class="form-select">
                    <option value="">All Status</option>
                    <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="suspended" {{ request('approval_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Motorcycle</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $d)
                    <tr>
                        <td class="fw-semibold">{{ $d->name }}</td>
                        <td><small style="color:var(--text-secondary);">{{ $d->email }}</small></td>
                        <td>{{ $d->phone ?? '-' }}</td>
                        <td>
                            @if($d->assignedMotorcycle)
                                <span class="badge-status active">{{ $d->assignedMotorcycle->plate_number }}</span>
                            @else
                                <span style="color:var(--text-secondary);">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status {{ $d->approval_status }}">{{ ucfirst($d->approval_status) }}</span>
                            @if(!$d->is_active)
                                <span class="badge-status disabled">Disabled</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('drivers.show', $d) }}" class="btn btn-icon"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('drivers.edit', $d) }}" class="btn btn-icon"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                                <h5>No drivers found</h5>
                                <p>No drivers match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <span style="font-size:0.82rem;color:var(--text-secondary);">{{ $drivers->firstItem() ?? 0 }} to {{ $drivers->lastItem() ?? 0 }} of {{ $drivers->total() }}</span>
    {{ $drivers->withQueryString()->links() }}
</div>
@endsection
