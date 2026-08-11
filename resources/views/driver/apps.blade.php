@extends('layouts.app')
@section('title', 'My Applications')
@section('page-title', 'My Applications')

@section('content')
@if($applications->count())
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Motorcycle</th><th>Owner</th><th>Amount</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($applications as $app)
                        <tr>
                            <td class="fw-semibold">{{ $app->motorcycle->plate_number ?? '-' }}<br><small style="color:var(--text-secondary);">{{ $app->motorcycle->make ?? '' }} {{ $app->motorcycle->model ?? '' }}</small></td>
                            <td>{{ $app->motorcycle->owner->name ?? '-' }}</td>
                            <td>TZS {{ number_format($app->motorcycle->loan_amount ?? 0) }}</td>
                            <td>{{ $app->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge-status {{ $app->status }}">{{ ucfirst($app->status) }}</span>
                                @if($app->isRejected() && $app->admin_notes)
                                    <br><small style="color:var(--text-secondary);">{{ $app->admin_notes }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $applications->links() }}</div>
@else
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-file-earmark-text"></i></div>
        <h5>No applications yet</h5>
        <p>You haven't applied for any motorcycle yet.</p>
        <a href="{{ route('home') }}" class="btn btn-gold"><i class="bi bi-search me-1"></i>Browse Marketplace</a>
    </div>
@endif
@endsection