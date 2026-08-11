@extends('layouts.app')
@section('title', 'Payment Verification')
@section('page-title', 'Payment Verification')
@section('content')

<div class="d-flex gap-2 mb-4">
    <a href="{{ route('admin.payments', ['status' => 'pending_verification']) }}" class="btn btn-sm {{ request('status', 'pending_verification') == 'pending_verification' ? 'btn-gold' : 'btn-outline' }}">
        Pending
        @php $pendingCount = \App\Models\Payment::where('status', 'pending_verification')->count(); @endphp
        @if($pendingCount > 0)
            <span class="badge rounded-pill ms-1" style="background:rgba(255,255,255,0.2);font-size:0.7rem;">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('admin.payments', ['status' => 'verified']) }}" class="btn btn-sm {{ request('status') == 'verified' ? 'btn-emerald' : 'btn-outline' }}">Verified</a>
    <a href="{{ route('admin.payments', ['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline' }}">Rejected</a>
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
    @if($payments->count())
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Driver</th>
                    <th>Bodaboda</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Receipt</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td style="font-weight:600;">{{ $p->loan->driver->name ?? '—' }}</td>
                    <td>
                        <span class="fw-semibold">{{ $p->loan->motorcycle->plate_number ?? '—' }}</span>
                    </td>
                    <td style="font-weight:700;color:var(--text);">TZS {{ number_format($p->amount) }}</td>
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
                                <a href="{{ Storage::url($p->receipt_path) }}" target="_blank" class="btn btn-xs btn-outline" style="font-size:0.75rem;">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            @else
                                <a href="#" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $p->id }}" style="text-decoration:none;">
                                    <img src="{{ Storage::url($p->receipt_path) }}" alt="Receipt" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;">
                                </a>
                            @endif
                        @else
                            <span style="color:var(--text-muted);font-size:0.8rem;">No receipt</span>
                        @endif
                    </td>
                    <td style="color:var(--text-secondary);font-size:0.85rem;">{{ $p->reference_number ?? '—' }}</td>
                    <td style="color:var(--text-secondary);">{{ $p->payment_date->format('M d, Y') }}</td>
                    <td>
                        @if($p->status === 'pending_verification')
                            <span class="badge-status pending">Pending</span>
                        @elseif($p->status === 'verified')
                            <span class="badge-status verified">Verified</span>
                        @else
                            <span class="badge-status rejected">Rejected</span>
                        @endif
                    </td>
                    <td>
                        @if($p->status === 'pending_verification')
                        <div class="d-flex gap-1">
                            <a href="{{ route('payments.show', $p) }}" class="btn btn-xs btn-outline" title="Review">
                                <i class="bi bi-eye"></i> Review
                            </a>
                            <form method="POST" action="{{ route('payments.verify', $p) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-emerald" title="Verify" onclick="return confirm('Approve this payment? The loan balance will be updated.')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <button class="btn btn-xs btn-danger" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        @else
                        <a href="{{ route('payments.show', $p) }}" class="btn btn-xs btn-outline" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endif
                    </td>
                </tr>

                {{-- Receipt Image Modal --}}
                @if($p->receipt_path && !str_ends_with($p->receipt_path, '.pdf'))
                <div class="modal fade" id="receiptModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content" style="background:transparent;border:none;">
                            <div class="modal-header" style="background:var(--navy-900);color:#fff;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-receipt me-2"></i>Payment Receipt — {{ $p->loan->driver->name ?? 'Driver' }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-0 text-center" style="background:#f8fafc;border-radius:0 0 var(--radius-lg) var(--radius-lg);">
                                <img src="{{ Storage::url($p->receipt_path) }}" alt="Payment Receipt" style="max-width:100%;max-height:70vh;">
                                <div class="p-3">
                                    <small class="text-muted">TZS {{ number_format($p->amount) }} · {{ ucfirst(str_replace('_', ' ', $p->method)) }} · {{ $p->payment_date->format('M d, Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Reject Modal --}}
                @if($p->status === 'pending_verification')
                <div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('payments.reject', $p) }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Reject Payment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3 p-3" style="background:#f8fafc;border-radius:var(--radius);">
                                        <small class="text-muted d-block">Driver</small>
                                        <strong>{{ $p->loan->driver->name ?? '—' }}</strong>
                                        <br><small class="text-muted">Amount: TZS {{ number_format($p->amount) }}</small>
                                    </div>
                                    <label class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g., Receipt unclear, amount mismatch, duplicate payment..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-credit-card"></i></div>
        <h5>No payments found</h5>
        <p>No payments match the current filter.</p>
    </div>
    @endif
</div>
<div class="mt-3">{{ $payments->withQueryString()->links() }}</div>
@endsection
