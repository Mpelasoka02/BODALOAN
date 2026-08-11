@extends('layouts.app')
@section('title', 'Bodaboda Verification')
@section('page-title', 'Bodaboda Verification')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">Bodaboda Verification</h5>
    <a href="{{ route('admin.vehicles', ['export' => 'csv']) }}" class="btn btn-outline btn-sm">
        <i class="bi bi-download"></i> Export
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="border-radius:10px;font-size:0.85rem;">{{ session('success') }}</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Plate number, make, model..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Verification Status</label>
                <select name="verification_status" class="form-select">
                    <option value="pending_verification" {{ request('verification_status', 'pending_verification') === 'pending_verification' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.vehicles') }}" class="btn btn-outline w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Plate Number</th>
                    <th>Make / Model</th>
                    <th>Owner</th>
                    <th>Loan Amount</th>
                    <th>Loan Progress</th>
                    <th>Verification</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                    <tr>
                        <td class="fw-semibold">{{ $vehicle->plate_number }}</td>
                        <td>{{ $vehicle->make }} {{ $vehicle->model }} <small class="text-muted">({{ $vehicle->year }})</small></td>
                        <td>{{ $vehicle->owner->name ?? 'N/A' }}</td>
                        <td>TZS {{ number_format($vehicle->loan_amount ?? 0) }}</td>
                        <td style="min-width:140px;">
                            @if($vehicle->loan)
                                <div style="height:8px;background:#e9ecef;border-radius:4px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $vehicle->loan->progress }}%;background:linear-gradient(90deg,var(--emerald-600),#34d399);border-radius:4px;"></div>
                                </div>
                                <div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;">{{ $vehicle->loan->progress }}% · TZS {{ number_format($vehicle->loan->amount_paid) }}</div>
                            @else
                                <span style="font-size:0.78rem;color:var(--text-muted);">No loan</span>
                            @endif
                        </td>
                        <td>
                            @if($vehicle->verification_status === 'pending_verification')
                                <span class="badge-status pending">Pending</span>
                            @elseif($vehicle->verification_status === 'verified')
                                <span class="badge-status verified">Verified</span>
                            @else
                                <span class="badge-status rejected">Rejected</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                @if($vehicle->verification_status === 'pending_verification')
                                    <form method="POST" action="{{ route('admin.vehicles.verify', $vehicle) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-emerald" title="Verify"><i class="bi bi-check-lg"></i> Verify</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $vehicle->id }}" title="Reject"><i class="bi bi-x-lg"></i> Reject</button>
                                @endif
                                <a href="{{ route('admin.vehicles.review', $vehicle) }}" class="btn btn-sm btn-outline" title="View"><i class="bi bi-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4"><div style="color:var(--text-muted);"><i class="bi bi-check-circle" style="font-size:2rem;"></i><br><span class="mt-2 d-block">No bodabodas found.</span></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="pagination-info">Showing {{ $vehicles->firstItem() ?? 0 }} to {{ $vehicles->lastItem() ?? 0 }} of {{ $vehicles->total() }} entries</span>
        {{ $vehicles->withQueryString()->links() }}
    </div>
</div>

@foreach($vehicles as $vehicle)
    @if($vehicle->verification_status === 'pending_verification')
    <div class="modal fade" id="rejectModal{{ $vehicle->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.vehicles.reject', $vehicle) }}" class="modal-content" style="border-radius:12px;">
                @csrf
                <div class="modal-header"><h6 class="modal-title fw-bold">Reject Bodaboda</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-start">
                    <p class="mb-2" style="font-size:0.85rem;color:var(--text-secondary);">Rejecting <strong>{{ $vehicle->plate_number }}</strong></p>
                    <label class="form-label">Rejection Reason</label>
                    <textarea name="verification_notes" class="form-control" rows="3" required placeholder="Explain why..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger">Reject Bodaboda</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach
@endsection