@extends('layouts.app')
@section('title', 'Loan Progress')
@section('page-title', 'Loan Progress')
@section('content')

@if(session('success'))
    <div class="alert-banner green mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="{{ route('admin.loans.progress', ['status' => 'active']) }}" class="btn btn-sm {{ request('status', 'active') == 'active' ? 'btn-emerald' : 'btn-outline' }}">
        <i class="bi bi-play-circle me-1"></i> Active
    </a>
    <a href="{{ route('admin.loans.progress', ['status' => 'overdue']) }}" class="btn btn-sm {{ request('status') == 'overdue' ? 'btn-gold' : 'btn-outline' }}">
        <i class="bi bi-exclamation-triangle me-1"></i> Overdue
    </a>
    <a href="{{ route('admin.loans.progress', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-outline-navy' : 'btn-outline' }}">
        <i class="bi bi-clock me-1"></i> Pending
    </a>
    <a href="{{ route('admin.loans.progress') }}" class="btn btn-sm btn-outline">All</a>
</div>

<div class="card" style="border-radius:var(--radius-lg);">
    @if($loans->count())
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Loan</th>
                    <th>Driver</th>
                    <th>Bodaboda</th>
                    <th>Owner</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                <tr>
                    <td class="fw-semibold">#{{ $loan->id }}</td>
                    <td>{{ $loan->driver->name ?? '—' }}</td>
                    <td>
                        <span class="fw-semibold">{{ $loan->motorcycle->plate_number ?? '—' }}</span><br>
                        <small class="text-muted">{{ $loan->motorcycle->make ?? '' }} {{ $loan->motorcycle->model ?? '' }}</small>
                    </td>
                    <td>{{ $loan->owner->name ?? '—' }}</td>
                    <td>TZS {{ number_format($loan->total_amount) }}</td>
                    <td style="color:var(--emerald-600);font-weight:600;">TZS {{ number_format($loan->amount_paid) }}</td>
                    <td style="min-width:120px;">
                        <div class="progress-track" style="height:6px;">
                            <div class="progress-fill emerald" style="width:{{ $loan->progress }}%;"></div>
                        </div>
                        <small class="text-muted">{{ $loan->progress }}%</small>
                    </td>
                    <td>
                        <span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-outline" title="View"><i class="bi bi-eye"></i></a>
                            @if(in_array($loan->status, ['active', 'overdue']))
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#forceStop{{ $loan->id }}" title="Force Stop"><i class="bi bi-stop-circle"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state py-5">
        <div class="empty-state-icon" style="width:64px;height:64px;font-size:1.5rem;"><i class="bi bi-check-circle"></i></div>
        <h5 style="font-size:1rem;">No loans found</h5>
        <p class="text-muted" style="font-size:0.85rem;">No loans match the selected filter.</p>
    </div>
    @endif
</div>

@if($loans->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="pagination-info">Showing {{ $loans->firstItem() ?? 0 }} to {{ $loans->lastItem() ?? 0 }} of {{ $loans->total() }} entries</span>
        {{ $loans->withQueryString()->links() }}
    </div>
@endif

@foreach($loans as $loan)
    @if(in_array($loan->status, ['active', 'overdue']))
    <div class="modal fade" id="forceStop{{ $loan->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:12px;">
                <form method="POST" action="{{ route('admin.loans.forceStop', $loan) }}">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Force Stop Loan #{{ $loan->id }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3" style="font-size:0.85rem;">This will mark the loan as completed and release the bodaboda. Please provide a reason.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required placeholder="Explain why this loan is being force-stopped..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to force-stop this loan?')">Force Stop</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection
