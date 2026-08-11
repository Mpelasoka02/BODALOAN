@extends('layouts.app')
@section('title', 'Driver Approval')
@section('page-title', 'Pending Drivers')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <a href="{{ route('admin.drivers', ['approval_status' => 'pending']) }}" class="btn btn-sm {{ request('approval_status', 'pending') == 'pending' ? 'btn-gold' : 'btn-outline' }}">Pending</a>
        <a href="{{ route('admin.drivers', ['approval_status' => 'approved']) }}" class="btn btn-sm {{ request('approval_status') == 'approved' ? 'btn-emerald' : 'btn-outline' }}">Approved</a>
        <a href="{{ route('admin.drivers', ['approval_status' => 'suspended']) }}" class="btn btn-sm {{ request('approval_status') == 'suspended' ? 'btn-danger' : 'btn-outline' }}">Suspended</a>
    </div>
</div>

<div class="card" style="border-radius:var(--radius-lg);">
    @if($drivers->count())
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Phone</th><th>Submitted</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($drivers as $driver)
                <tr>
                    <td style="font-weight:600;">{{ $driver->name }}</td>
                    <td style="color:var(--text-secondary);">{{ $driver->email }}</td>
                    <td style="color:var(--text-secondary);">{{ $driver->phone ?? '—' }}</td>
                    <td style="color:var(--text-secondary);">{{ $driver->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($driver->approval_status === 'pending')
                            <span class="badge-status pending">Pending</span>
                        @elseif($driver->approval_status === 'approved')
                            <span class="badge-status approved">Approved</span>
                        @else
                            <span class="badge-status suspended">Suspended</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2 flex-wrap">
                            <form method="POST" action="{{ route('admin.drivers.approve', $driver) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-emerald">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.drivers.reject', $driver) }}" class="d-flex gap-1">
                                @csrf
                                <input type="text" name="rejection_reason" placeholder="Reason..." required class="form-control" style="padding:5px 10px;font-size:0.78rem;width:140px;">
                                <button type="submit" class="btn btn-xs btn-danger">Reject</button>
                            </form>
                            <a href="{{ route('chat.start.direct', $driver) }}" class="btn btn-xs btn-outline" title="Chat with driver"><i class="bi bi-chat-dots"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-people"></i></div>
        <h5>No drivers found</h5>
        <p>No drivers match the current filter.</p>
    </div>
    @endif
</div>

<div class="mt-3">{{ $drivers->withQueryString()->links() }}</div>
@endsection
