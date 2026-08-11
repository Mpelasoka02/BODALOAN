@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--text);">Payments</h4>
        <p class="mb-0 mt-1" style="font-size:0.82rem;color:var(--text-secondary);">Track and verify loan payments</p>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->isDriver())
            <a href="{{ route('payments.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg"></i> Make Payment</a>
        @else
            <a href="{{ route('payments.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg"></i> Record Payment</a>
        @endif
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
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Method</label>
                <select name="method" class="form-select">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="mpesa" {{ request('method') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                    <option value="tigo_pesa" {{ request('method') === 'tigo_pesa' ? 'selected' : '' }}>Tigo Pesa</option>
                    <option value="airmoney" {{ request('method') === 'airmoney' ? 'selected' : '' }}>Airtel Money</option>
                    <option value="halopesa" {{ request('method') === 'halopesa' ? 'selected' : '' }}>HaloPesa</option>
                    <option value="bank" {{ request('method') === 'bank' ? 'selected' : '' }}>Bank</option>
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
                    <th>Date</th>
                    <th>Driver</th>
                    <th>Motorcycle</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Receipt</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td>{{ $p->payment_date->format('M d, Y') }}</td>
                        <td class="fw-semibold">{{ $p->loan?->driver?->name ?? '-' }}</td>
                        <td><small style="color:var(--text-secondary);">{{ $p->loan?->motorcycle?->plate_number ?? '-' }}</small></td>
                        <td class="fw-semibold" style="color:var(--emerald-600);">TZS {{ number_format($p->amount) }}</td>
                        <td>
                            <span class="badge-status active">
                                @switch($p->method)
                                    @case('cash') <i class="bi bi-cash me-1"></i> Cash @break
                                    @case('mpesa') <i class="bi bi-phone me-1"></i> M-Pesa @break
                                    @case('tigo_pesa') <i class="bi bi-phone me-1"></i> Tigo Pesa @break
                                    @case('airmoney') <i class="bi bi-phone me-1"></i> Airtel Money @break
                                    @case('halopesa') <i class="bi bi-phone me-1"></i> HaloPesa @break
                                    @case('bank') <i class="bi bi-bank me-1"></i> Bank @break
                                    @default {{ ucfirst($p->method) }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            @if($p->receipt_path)
                                @if(str_ends_with($p->receipt_path, '.pdf'))
                                    <a href="{{ Storage::url($p->receipt_path) }}" target="_blank" class="btn btn-xs btn-outline" style="font-size:0.72rem;"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                                @else
                                    <a href="{{ Storage::url($p->receipt_path) }}" target="_blank" style="text-decoration:none;">
                                        <img src="{{ Storage::url($p->receipt_path) }}" alt="Receipt" style="width:32px;height:32px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;">
                                    </a>
                                @endif
                            @else
                                <span style="color:var(--text-muted);font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status {{ $p->status === 'verified' ? 'verified' : ($p->status === 'pending_verification' ? 'pending_verification' : 'rejected') }}">
                                {{ $p->status === 'pending_verification' ? 'Pending' : ucfirst($p->status) }}
                            </span>
                        </td>
                        <td>
                            @if(!auth()->user()->isDriver() && $p->status === 'pending_verification')
                                <div class="d-flex gap-1">
                                    <a href="{{ route('payments.show', $p) }}" class="btn btn-xs btn-outline" title="Review"><i class="bi bi-eye"></i></a>
                                    <form method="POST" action="{{ route('payments.verify', $p) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-icon" style="color:var(--emerald-600);" title="Verify"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                    <button class="btn btn-icon" style="color:var(--status-overdue-text);" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}"><i class="bi bi-x-lg"></i></button>
                                </div>
                                <div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('payments.reject', $p) }}">
                                                @csrf
                                                <div class="modal-header"><h5 class="modal-title">Reject Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <div class="modal-body">
                                                    <label class="form-label">Reason</label>
                                                    <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Reject</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('payments.show', $p) }}" class="btn btn-xs btn-outline" title="View"><i class="bi bi-eye"></i></a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-credit-card"></i></div>
                                <h5>No payments found</h5>
                                <p>No payments match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <span style="font-size:0.82rem;color:var(--text-secondary);">{{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}</span>
    {{ $payments->withQueryString()->links() }}
</div>
@endsection
