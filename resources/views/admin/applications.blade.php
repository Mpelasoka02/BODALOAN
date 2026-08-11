@extends('layouts.app')
@section('title', 'Driver Applications')
@section('page-title', 'Driver Applications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">Driver Applications</h5>
    <a href="{{ route('admin.applications', ['export' => 'csv']) }}" class="btn btn-outline btn-sm">
        <i class="bi bi-download"></i> Export
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="border-radius:10px;font-size:0.85rem;">{{ session('success') }}</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.applications') }}" class="btn btn-outline w-100">Reset</a>
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
                    <th>License</th>
                    <th>Guarantor</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width:32px;height:32px;font-size:0.7rem;">{{ substr($app->driver->name, 0, 1) }}</div>
                                <div>
                                    <div class="fw-semibold">{{ $app->driver->name }}</div>
                                    <small class="text-muted">{{ $app->driver->phone }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $app->motorcycle->plate_number }}<br><small class="text-muted">{{ $app->motorcycle->make }} {{ $app->motorcycle->model }}</small></td>
                        <td>{{ $app->motorcycle->owner->name ?? 'N/A' }}</td>
                        <td>
                            @if($app->id_number || $app->license_number)
                                <small class="d-block">ID: {{ $app->id_number ?? '-' }}</small>
                                <small>License: {{ $app->license_number ?? '-' }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($app->guarantor_name)
                                <small class="d-block">{{ $app->guarantor_name }}</small>
                                <small class="text-muted">{{ $app->guarantor_phone }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($app->status === 'pending')
                                <span class="badge-status pending">Pending</span>
                            @elseif($app->status === 'approved')
                                <span class="badge-status approved">Approved</span>
                            @else
                                <span class="badge-status rejected">Rejected</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($app->status === 'pending')
                                <button type="button" class="btn btn-sm btn-emerald" data-bs-toggle="modal" data-bs-target="#approveModal{{ $app->id }}">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $app->id }}">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            @else
                                @if($app->admin_notes)
                                    <button type="button" class="btn btn-sm btn-outline" data-bs-toggle="tooltip" title="{{ $app->admin_notes }}">
                                        <i class="bi bi-chat-dots"></i>
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4"><div style="color:var(--text-muted);"><i class="bi bi-inbox" style="font-size:2rem;"></i><br><span class="mt-2 d-block">No applications found.</span></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="pagination-info">Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} entries</span>
        {{ $applications->withQueryString()->links() }}
    </div>
</div>

@foreach($applications as $app)
    @if($app->status === 'pending')
    <div class="modal fade" id="approveModal{{ $app->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.applications.review', $app) }}" class="modal-content" style="border-radius:12px;">
                @csrf
                <input type="hidden" name="action" value="approved">
                <div class="modal-header"><h6 class="modal-title fw-bold">Approve Application</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-start">
                    <p style="font-size:0.85rem;color:var(--text-secondary);">
                        Approving <strong>{{ $app->driver->name }}</strong> for <strong>{{ $app->motorcycle->plate_number }}</strong>.
                        @if($app->motorcycle->loan_amount && $app->motorcycle->loan_duration_weeks)
                            A loan will be auto-created (TZS {{ number_format($app->motorcycle->loan_amount) }} / {{ $app->motorcycle->loan_duration_weeks }} weeks).
                        @else
                            <br><span class="text-warning">No loan terms set. Create the loan manually.</span>
                        @endif
                    </p>
                    @if($app->notes)
                        <div class="mb-3 p-3" style="background:var(--body-bg);border-radius:8px;">
                            <small class="text-muted d-block mb-1">Driver's note:</small>
                            <p class="mb-0" style="font-size:0.85rem;">{{ $app->notes }}</p>
                        </div>
                    @endif
                    <label class="form-label">Admin Notes (optional)</label>
                    <textarea name="admin_notes" class="form-control" rows="2" placeholder="Any notes for the driver..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-emerald">Approve & Assign</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="rejectModal{{ $app->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.applications.review', $app) }}" class="modal-content" style="border-radius:12px;">
                @csrf
                <input type="hidden" name="action" value="rejected">
                <div class="modal-header"><h6 class="modal-title fw-bold">Reject Application</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-start">
                    <p style="font-size:0.85rem;color:var(--text-secondary);">Rejecting <strong>{{ $app->driver->name }}</strong> for <strong>{{ $app->motorcycle->plate_number }}</strong></p>
                    <label class="form-label">Reason for rejection</label>
                    <textarea name="admin_notes" class="form-control" rows="3" required placeholder="Explain why..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach
@endsection