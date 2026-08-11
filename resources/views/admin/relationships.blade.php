@extends('layouts.app')
@section('title', 'Relationships')
@section('page-title', 'Relationships')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">Relationship View</h5>
    <a href="{{ route('admin.relationships', ['export' => 'csv']) }}" class="btn btn-outline btn-sm">
        <i class="bi bi-download"></i> Export
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Plate number, driver, owner..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Loan Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="defaulted" {{ request('status') === 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.relationships') }}" class="btn btn-outline w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Driver</th>
                    <th>Bodaboda</th>
                    <th>Owner</th>
                    <th>Loan Status</th>
                    <th>Progress</th>
                    <th>Balance</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($relationships as $m)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width:32px;height:32px;font-size:0.7rem;">{{ substr($m->driver->name ?? '?', 0, 1) }}</div>
                                <div>
                                    <div class="fw-semibold">{{ $m->driver->name ?? 'Unassigned' }}</div>
                                    <small class="text-muted">{{ $m->driver->phone ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="fw-semibold">{{ $m->plate_number }}</span><br><small class="text-muted">{{ $m->make }} {{ $m->model }}</small></td>
                        <td>{{ $m->owner->name ?? 'N/A' }}<br><small class="text-muted">{{ $m->owner->phone ?? '' }}</small></td>
                        <td>
                            @if($m->loan)
                                <span class="badge-status {{ $m->loan->status }}">{{ ucfirst(str_replace('_', ' ', $m->loan->status)) }}</span>
                            @else
                                <span class="badge-status disabled">No Loan</span>
                            @endif
                        </td>
                        <td>
                            @if($m->loan)
                                <div class="d-flex align-items-center gap-2" style="min-width:100px;">
                                    <div class="flex-grow-1" style="height:6px;background:var(--body-bg);border-radius:4px;overflow:hidden;">
                                        <div style="width:{{ $m->loan->progress }}%;height:100%;background:var(--primary);border-radius:4px;"></div>
                                    </div>
                                    <small class="fw-semibold" style="font-size:0.75rem;">{{ $m->loan->progress }}%</small>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($m->loan)
                                <span class="fw-semibold" style="font-size:0.85rem;">TZS {{ number_format($m->loan->balance) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('motorcycles.show', $m) }}" class="btn btn-sm btn-outline" title="View"><i class="bi bi-eye"></i></a>
                            @if($m->loan)
                                <a href="{{ route('loans.show', $m->loan) }}" class="btn btn-sm btn-outline" title="Loan"><i class="bi bi-wallet2"></i></a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4"><div style="color:var(--text-muted);"><i class="bi bi-link-45deg" style="font-size:2rem;"></i><br><span class="mt-2 d-block">No relationships found.</span></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="pagination-info">Showing {{ $relationships->firstItem() ?? 0 }} to {{ $relationships->lastItem() ?? 0 }} of {{ $relationships->total() }} entries</span>
        {{ $relationships->withQueryString()->links() }}
    </div>
</div>
@endsection