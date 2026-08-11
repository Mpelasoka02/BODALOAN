@extends('layouts.app')
@section('title', 'Overdue Loans')
@section('page-title', 'Overdue & Defaulted Loans')
@section('content')

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Overdue</div>
            <div class="stat-value" style="color:var(--status-overdue-text);">{{ \App\Models\Loan::where('status','overdue')->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Defaulted</div>
            <div class="stat-value" style="color:var(--status-overdue-text);">{{ \App\Models\Loan::where('status','defaulted')->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Total Outstanding</div>
            <div class="stat-value">TZS {{ number_format(\App\Models\Loan::whereIn('status',['overdue','defaulted'])->sum('total_amount') - \App\Models\Loan::whereIn('status',['overdue','defaulted'])->sum('amount_paid')) }}</div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mb-3">
    <a href="{{ route('admin.overdue') }}" class="btn btn-sm {{ !request('status') ? 'btn-danger' : 'btn-outline' }}">All</a>
    <a href="{{ route('admin.overdue', ['status' => 'overdue']) }}" class="btn btn-sm {{ request('status') == 'overdue' ? 'btn-gold' : 'btn-outline' }}">Overdue</a>
    <a href="{{ route('admin.overdue', ['status' => 'defaulted']) }}" class="btn btn-sm {{ request('status') == 'defaulted' ? 'btn-danger' : 'btn-outline' }}">Defaulted</a>
</div>

<div class="card" style="border-radius:var(--radius-lg);">
    @if($loans->count())
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Driver</th><th>Bodaboda</th><th>Loan Amount</th><th>Paid</th><th>Balance</th><th>Weeks Overdue</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                <tr>
                    <td style="font-weight:600;">{{ $loan->driver->name ?? '—' }}</td>
                    <td>{{ $loan->motorcycle->plate_number ?? '—' }}</td>
                    <td>TZS {{ number_format($loan->total_amount) }}</td>
                    <td style="color:var(--emerald-600);">TZS {{ number_format($loan->amount_paid) }}</td>
                    <td style="font-weight:700;color:var(--status-overdue-text);">TZS {{ number_format($loan->balance) }}</td>
                    <td style="font-weight:600;">{{ $loan->next_payment_date ? now()->diffInWeeks($loan->next_payment_date) : '—' }}</td>
                    <td>
                        @if($loan->status === 'overdue')
                            <span class="badge-status overdue">Overdue</span>
                        @else
                            <span class="badge-status overdue">Defaulted</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-check-circle"></i></div>
        <h5>All clear!</h5>
        <p>No overdue or defaulted loans.</p>
    </div>
    @endif
</div>
<div class="mt-3">{{ $loans->withQueryString()->links() }}</div>
@endsection
