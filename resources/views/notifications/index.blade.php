@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Notifications</h5>
    </div>
    <div>
        <form method="POST" action="{{ route('notifications.markAllRead') }}">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm"><i class="bi bi-check-all me-1"></i>Mark All Read</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="border-radius:10px;font-size:0.85rem;">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $n)
                    <tr style="{{ $n->read_at ? '' : 'background:var(--primary-light);' }}">
                        <td>{{ $n->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @php
                                $icon = match($n->type) {'payment' => 'bi-credit-card', 'loan' => 'bi-wallet2', 'contract' => 'bi-file-earmark-text', 'system' => 'bi-gear', default => 'bi-bell'};
                                $color = match($n->type) {'payment' => 'var(--success)', 'loan' => 'var(--info)', 'contract' => 'var(--accent)', 'system' => 'var(--warning)', default => 'var(--text-muted)'};
                            @endphp
                            <i class="bi {{ $icon }}" style="color:{{ $color }};"></i>
                            <span class="ms-1">{{ ucfirst($n->type) }}</span>
                        </td>
                        <td>{{ $n->message }}</td>
                        <td>
                            @if($n->read_at)
                                <span class="badge-status disabled">Read</span>
                            @else
                                <span class="badge-status active">New</span>
                            @endif
                        </td>
                        <td>
                            @if(!$n->read_at)
                                <form method="POST" action="{{ route('notifications.markAsRead', $n) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-icon"><i class="bi bi-check"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No notifications.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="pagination-info">{{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }}</span>
    {{ $notifications->withQueryString()->links() }}
</div>
@endsection