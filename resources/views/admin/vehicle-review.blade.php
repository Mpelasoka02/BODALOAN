@extends('layouts.app')
@section('title', 'Review Bodaboda — ' . $motorcycle->plate_number)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('admin.vehicles') }}" style="color:var(--text-secondary);text-decoration:none;">Bodabodas</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">{{ $motorcycle->plate_number }}</li>
    </ol>
</nav>

@if(session('success'))
    <div class="alert alert-success" style="border-radius:10px;font-size:0.85rem;">{{ session('success') }}</div>
@endif

<div class="d-flex justify-content-between align-items-start mb-3">
    <div class="d-flex align-items-center gap-2">
        <h4 class="mb-0 fw-bold">{{ $motorcycle->plate_number }}</h4>
        <span class="badge-status {{ $motorcycle->verification_status }}">{{ ucfirst(str_replace('_', ' ', $motorcycle->verification_status)) }}</span>
        <span class="badge-status {{ $motorcycle->status }}">{{ ucfirst($motorcycle->status) }}</span>
    </div>
    @if($motorcycle->verification_status === 'pending_verification')
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.vehicles.verify', $motorcycle) }}">
                @csrf
                <button type="submit" class="btn btn-emerald" onclick="return confirm('Verify this bodaboda?')">
                    <i class="bi bi-check-circle me-1"></i> Verify Bodaboda
                </button>
            </form>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-1"></i> Reject
            </button>
        </div>
    @endif
</div>

<div class="row g-3">
    <div class="col-lg-8">
        @if($motorcycle->photo)
        <div class="card mb-3" style="padding:16px;">
            <div style="font-size:0.85rem;font-weight:700;color:var(--navy-900);margin-bottom:12px;"><i class="bi bi-camera me-2"></i>Bodaboda Photo</div>
            <div style="max-width:400px;border-radius:10px;overflow:hidden;">
                <img src="{{ asset('storage/' . $motorcycle->photo) }}" alt="{{ $motorcycle->plate_number }}" style="width:100%;object-fit:cover;">
            </div>
        </div>
        @endif

        <div class="card mb-3" style="padding:20px;">
            <div style="font-size:0.85rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-bicycle" style="color:var(--navy-700);"></i> Bodaboda Information
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Plate Number</div>
                    <div style="font-size:1rem;font-weight:700;color:var(--text);margin-top:2px;">{{ $motorcycle->plate_number }}</div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Make</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $motorcycle->make }}</div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Model</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $motorcycle->model }}</div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Year</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $motorcycle->year }}</div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Color</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $motorcycle->color }}</div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Engine CC</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $motorcycle->engine_cc }}</div>
                </div>
                @if($motorcycle->engine_number)
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Engine Number</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $motorcycle->engine_number }}</div>
                </div>
                @endif
                @if($motorcycle->chassis_number)
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Chassis Number</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $motorcycle->chassis_number }}</div>
                </div>
                @endif
                @if($motorcycle->gps_device_id)
                <div class="col-sm-6 col-md-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">GPS Tracker ID</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--emerald-600);margin-top:2px;"><i class="bi bi-geo-alt me-1"></i>{{ $motorcycle->gps_device_id }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="card mb-3" style="padding:20px;">
            <div style="font-size:0.85rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-cash-stack" style="color:var(--navy-700);"></i> Loan Terms
            </div>
            <div class="row g-3">
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Loan Amount</div>
                    <div style="font-size:1rem;font-weight:700;color:var(--text);margin-top:2px;">TZS {{ number_format($motorcycle->loan_amount) }}</div>
                </div>
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Weekly Payment</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">TZS {{ number_format($motorcycle->weekly_amount ?? 0) }}</div>
                </div>
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Duration</div>
                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $motorcycle->loan_duration_weeks }} weeks</div>
                </div>
            </div>
        </div>

        @if($motorcycle->registration_card || $motorcycle->insurance)
        <div class="card mb-3" style="padding:20px;">
            <div style="font-size:0.85rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-file-earmark me-2" style="color:var(--navy-700);"></i> Documents
            </div>
            <div class="row g-3">
                @if($motorcycle->registration_card)
                <div class="col-sm-6">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:6px;">Registration Card</div>
                    <a href="{{ asset('storage/' . $motorcycle->registration_card) }}" target="_blank" class="btn btn-sm btn-outline-navy">
                        <i class="bi bi-eye me-1"></i> View Document
                    </a>
                </div>
                @endif
                @if($motorcycle->insurance)
                <div class="col-sm-6">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:6px;">Insurance</div>
                    <a href="{{ asset('storage/' . $motorcycle->insurance) }}" target="_blank" class="btn btn-sm btn-outline-navy">
                        <i class="bi bi-eye me-1"></i> View Document
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-3" style="padding:20px;">
            <div style="font-size:0.85rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-person" style="color:var(--navy-700);"></i> Owner
            </div>
            @if($motorcycle->owner)
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:40px;height:40px;border-radius:50%;background:var(--navy-900);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;">{{ substr($motorcycle->owner->name, 0, 1) }}</div>
                <div>
                    <div style="font-weight:700;color:var(--text);">{{ $motorcycle->owner->name }}</div>
                    <div style="font-size:0.78rem;color:var(--text-muted);">{{ $motorcycle->owner->email }}</div>
                </div>
            </div>
            @if($motorcycle->owner->phone)
            <div style="font-size:0.82rem;"><span style="color:var(--text-muted);">Phone:</span> {{ $motorcycle->owner->phone }}</div>
            @endif
            @else
            <span style="color:var(--text-muted);">No owner</span>
            @endif
        </div>

        @if($motorcycle->driver)
        <div class="card mb-3" style="padding:20px;">
            <div style="font-size:0.85rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-person-badge" style="color:var(--navy-700);"></i> Driver
            </div>
            <div class="d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;border-radius:50%;background:var(--emerald-600);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;">{{ substr($motorcycle->driver->name, 0, 1) }}</div>
                <div>
                    <div style="font-weight:700;color:var(--text);">{{ $motorcycle->driver->name }}</div>
                    <div style="font-size:0.78rem;color:var(--text-muted);">{{ $motorcycle->driver->email }}</div>
                    @if($motorcycle->driver->phone)
                    <div style="font-size:0.78rem;color:var(--text-muted);">{{ $motorcycle->driver->phone }}</div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($loan)
        <div class="card mb-3" style="padding:20px;border:2px solid var(--primary);">
            <div style="font-size:0.85rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-wallet2" style="color:var(--navy-700);"></i> Loan Progress
            </div>
            <div style="margin-bottom:12px;">
                <div class="d-flex justify-content-between mb-1">
                    <span style="font-size:0.78rem;color:var(--text-muted);">Paid</span>
                    <span style="font-size:0.82rem;font-weight:700;color:var(--emerald-600);">TZS {{ number_format($loan->amount_paid) }}</span>
                </div>
                <div style="height:10px;background:#e9ecef;border-radius:5px;overflow:hidden;">
                    <div style="height:100%;width:{{ $loan->progress }}%;background:linear-gradient(90deg,var(--emerald-600),#34d399);border-radius:5px;transition:width .3s;"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span style="font-size:0.72rem;color:var(--text-muted);">{{ $loan->progress }}% complete</span>
                    <span style="font-size:0.72rem;color:var(--text-muted);">of TZS {{ number_format($loan->total_amount) }}</span>
                </div>
            </div>
            <div class="row g-2" style="font-size:0.82rem;">
                <div class="col-6"><span style="color:var(--text-muted);">Status:</span> <span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span></div>
                <div class="col-6"><span style="color:var(--text-muted);">Balance:</span> <strong>TZS {{ number_format($loan->balance) }}</strong></div>
                <div class="col-6"><span style="color:var(--text-muted);">Weekly:</span> <strong>TZS {{ number_format($loan->weekly_installment) }}</strong></div>
                <div class="col-6"><span style="color:var(--text-muted);">Next Due:</span> <strong>{{ $loan->next_payment_date?->format('M d') ?? '—' }}</strong></div>
            </div>
        </div>
        @endif

        <div class="card mb-3" style="padding:20px;">
            <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:8px;">Submission Details</div>
            <div style="font-size:0.82rem;color:var(--text);">Registered: {{ $motorcycle->created_at->format('M d, Y \a\t h:i A') }}</div>
            @if($motorcycle->verification_notes)
            <div class="mt-2 p-2" style="background:#fef2f2;border-radius:8px;font-size:0.82rem;color:#991b1b;">
                <strong>Rejection reason:</strong> {{ $motorcycle->verification_notes }}
            </div>
            @endif
        </div>
    </div>
</div>

@if($motorcycle->verification_status === 'pending_verification')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.vehicles.reject', $motorcycle) }}" class="modal-content" style="border-radius:12px;">
            @csrf
            <div class="modal-header"><h6 class="modal-title fw-bold">Reject Bodaboda</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-start">
                <p class="mb-2" style="font-size:0.85rem;color:var(--text-secondary);">Rejecting <strong>{{ $motorcycle->plate_number }}</strong></p>
                <label class="form-label">Rejection Reason</label>
                <textarea name="verification_notes" class="form-control" rows="3" required placeholder="Explain why this bodaboda is being rejected..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-danger">Reject Bodaboda</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
