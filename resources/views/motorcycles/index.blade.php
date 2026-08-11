@extends('layouts.app')
@section('title', 'Motorcycles')
@section('page-title', 'Motorcycles')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--text);">Motorcycles</h4>
        <p class="mb-0 mt-1" style="font-size:0.82rem;color:var(--text-secondary);">Manage registered motorcycles</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('owner.vehicles.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg"></i> Register New</a>
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
                <input type="text" name="search" class="form-control" placeholder="Plate, make, model..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['available','assigned','pending','suspended','completed'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Owner</label>
                <select name="owner_id" class="form-select">
                    <option value="">All Owners</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                    @endforeach
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
                    <th>Plate #</th>
                    <th>Make / Model</th>
                    <th>Year</th>
                    <th>Owner</th>
                    <th>Driver</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($motorcycles as $m)
                    <tr>
                        <td class="fw-semibold">{{ $m->plate_number }}</td>
                        <td>{{ $m->make }} {{ $m->model }}</td>
                        <td>{{ $m->year }}</td>
                        <td><small style="color:var(--text-secondary);">{{ $m->owner->name ?? '-' }}</small></td>
                        <td><small style="color:var(--text-secondary);">{{ $m->driver->name ?? 'Unassigned' }}</small></td>
                        <td><span class="badge-status {{ $m->status }}">{{ ucfirst($m->status) }}</span></td>
                        <td>
                            <a href="{{ route('motorcycles.show', $m) }}" class="btn btn-icon"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('motorcycles.edit', $m) }}" class="btn btn-icon"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-bicycle"></i></div>
                                <h5>No motorcycles found</h5>
                                <p>No motorcycles match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <span style="font-size:0.82rem;color:var(--text-secondary);">{{ $motorcycles->firstItem() ?? 0 }} to {{ $motorcycles->lastItem() ?? 0 }} of {{ $motorcycles->total() }}</span>
    {{ $motorcycles->withQueryString()->links() }}
</div>
@endsection
