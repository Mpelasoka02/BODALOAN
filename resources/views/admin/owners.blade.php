@extends('layouts.app')
@section('title', 'Owner Management')
@section('page-title', 'Owners')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <a href="{{ route('admin.owners', ['approval_status' => 'pending']) }}" class="btn btn-sm {{ request('approval_status', 'pending') == 'pending' ? 'btn-gold' : 'btn-outline' }}">
            Pending
            @php $pendingCount = \App\Models\User::where('role', 'owner')->where('approval_status', 'pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="badge rounded-pill ms-1" style="background:rgba(255,255,255,0.2);font-size:0.7rem;">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.owners', ['approval_status' => 'approved']) }}" class="btn btn-sm {{ request('approval_status') == 'approved' ? 'btn-emerald' : 'btn-outline' }}">Approved</a>
        <a href="{{ route('admin.owners', ['approval_status' => 'suspended']) }}" class="btn btn-sm {{ request('approval_status') == 'suspended' ? 'btn-danger' : 'btn-outline' }}">Suspended</a>
    </div>
    @if(request('approval_status'))
        <a href="{{ route('admin.owners') }}" class="btn btn-sm btn-outline">Clear Filter</a>
    @endif
</div>

@if(session('success'))
    <div class="alert-banner green mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

<div class="card" style="border-radius:var(--radius-lg);">
    @if($owners->count())
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>NIDA</th>
                    <th>Bodabodas</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($owners as $owner)
                <tr>
                    <td style="font-weight:600;">{{ $owner->name }}</td>
                    <td style="color:var(--text-secondary);">{{ $owner->email }}</td>
                    <td style="color:var(--text-secondary);">{{ $owner->phone ?? '—' }}</td>
                    <td style="color:var(--text-secondary);font-size:0.82rem;">{{ $owner->nida ?? '—' }}</td>
                    <td>
                        <span class="badge-status active">{{ $owner->motorcycles_count ?? 0 }}</span>
                    </td>
                    <td style="color:var(--text-secondary);">{{ $owner->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($owner->approval_status === 'pending')
                            <span class="badge-status pending">Pending</span>
                        @elseif($owner->approval_status === 'approved')
                            <span class="badge-status approved">Approved</span>
                        @else
                            <span class="badge-status suspended">Suspended</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 align-items-center flex-wrap">
                            @if($owner->approval_status === 'pending')
                                <form method="POST" action="{{ route('admin.owners.approve', $owner) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-emerald"><i class="bi bi-check-lg me-1"></i>Approve</button>
                                </form>
                                <button type="button" class="btn btn-xs btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectOwner{{ $owner->id }}">
                                    <i class="bi bi-x-lg me-1"></i>Reject
                                </button>
                            @elseif($owner->approval_status === 'approved')
                                <form method="POST" action="{{ route('admin.owners.reject', $owner) }}">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" value="Suspended by admin">
                                    <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Suspend this owner?')">Suspend</button>
                                </form>
                            @elseif($owner->approval_status === 'suspended')
                                <form method="POST" action="{{ route('admin.owners.approve', $owner) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-emerald">Re-approve</button>
                                </form>
                            @endif
                            <a href="{{ route('chat.start.direct', $owner) }}" class="btn btn-xs btn-outline" title="Chat with owner"><i class="bi bi-chat-dots"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-person-workspace"></i></div>
        <h5>No owners found</h5>
        <p>No owners match the current filter.</p>
    </div>
    @endif
</div>

<div class="mt-3 d-flex justify-content-between align-items-center">
    <span class="pagination-info">Showing {{ $owners->firstItem() ?? 0 }} to {{ $owners->lastItem() ?? 0 }} of {{ $owners->total() }} entries</span>
    {{ $owners->withQueryString()->links() }}
</div>

@foreach($owners as $owner)
    @if($owner->approval_status === 'pending')
    <div class="modal fade" id="rejectOwner{{ $owner->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:12px;">
                <form method="POST" action="{{ route('admin.owners.reject', $owner) }}">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Reject Owner — {{ $owner->name }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3" style="font-size:0.85rem;">This owner will be suspended and cannot use the platform.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Explain why this owner is being rejected..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Owner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection
