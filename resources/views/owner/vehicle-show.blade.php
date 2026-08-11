@extends('layouts.app')
@section('title', 'Bodaboda Details')
@section('page-title', $motorcycle->make . ' ' . $motorcycle->model)

@section('content')

<div class="mb-3">
    <a href="{{ route('owner.vehicles') }}" class="btn btn-sm btn-outline" style="border-radius:8px;">
        <i class="bi bi-arrow-left me-1"></i> Back to My Bodabodas
    </a>
</div>

<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge-status {{ $motorcycle->verification_status }}" style="font-size:0.8rem;padding:5px 14px;">{{ ucfirst(str_replace('_', ' ', $motorcycle->verification_status)) }}</span>
                <span class="badge-status {{ $motorcycle->status }}" style="font-size:0.8rem;padding:5px 14px;">{{ ucfirst($motorcycle->status) }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.vehicles.edit', $motorcycle) }}" class="btn btn-sm btn-outline-navy"><i class="bi bi-pencil me-1"></i> Edit</a>
                @if($motorcycle->status !== 'assigned' && !$motorcycle->loan)
                <form action="{{ route('owner.vehicles.destroy', $motorcycle) }}" method="POST" onsubmit="return confirm('Remove this bodaboda?');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i> Remove</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT COLUMN: Photo + Details --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div style="padding:24px;">
                @if($motorcycle->photo)
                    <div style="width:100%;border-radius:12px;overflow:hidden;background:var(--page-bg);margin-bottom:24px;">
                        <img src="{{ asset('storage/' . $motorcycle->photo) }}" alt="{{ $motorcycle->plate_number }}" style="width:100%;height:auto;display:block;max-height:400px;object-fit:cover;">
                    </div>
                @endif

                <h6 style="font-size:0.82rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);">
                    <i class="bi bi-bicycle me-1" style="color:var(--navy-700);"></i> Vehicle Information
                </h6>

                <div class="row g-4">
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Plate Number</div>
                        <div class="detail-value">{{ $motorcycle->plate_number }}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Make</div>
                        <div class="detail-value">{{ $motorcycle->make }}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Model</div>
                        <div class="detail-value">{{ $motorcycle->model }}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Year</div>
                        <div class="detail-value">{{ $motorcycle->year }}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Color</div>
                        <div class="detail-value">{{ $motorcycle->color ?: '—' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Engine CC</div>
                        <div class="detail-value">{{ $motorcycle->engine_cc ?: '—' }}</div>
                    </div>
                    @if($motorcycle->engine_number)
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Engine Number</div>
                        <div class="detail-value">{{ $motorcycle->engine_number }}</div>
                    </div>
                    @endif
                    @if($motorcycle->chassis_number)
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Chassis Number</div>
                        <div class="detail-value">{{ $motorcycle->chassis_number }}</div>
                    </div>
                    @endif
                    @if($motorcycle->location_name || ($motorcycle->latitude && $motorcycle->longitude))
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Pickup Location</div>
                        <div class="detail-value">{{ $motorcycle->location_name ?: 'Set' }}</div>
                        @if($motorcycle->latitude && $motorcycle->longitude)
                            <a href="https://www.google.com/maps?q={{ $motorcycle->latitude }},{{ $motorcycle->longitude }}" target="_blank" style="font-size:0.78rem;color:var(--navy-700);font-weight:600;text-decoration:none;">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open in Maps
                            </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN: Loan / Driver --}}
    <div class="col-lg-4">
        @if($motorcycle->loan)
            <div class="card border-0 shadow-sm mb-4">
                <div style="padding:24px;">
                    <h6 style="font-size:0.82rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);">
                        <i class="bi bi-wallet2 me-1" style="color:var(--navy-700);"></i> Loan & Driver
                    </h6>

                    <div style="margin-bottom:20px;">
                        <div class="detail-label">Assigned Driver</div>
                        @if($motorcycle->driver)
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div style="width:40px;height:40px;border-radius:10px;background:var(--navy-900);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;">{{ substr($motorcycle->driver->name, 0, 1) }}</div>
                                <div>
                                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);">{{ $motorcycle->driver->name }}</div>
                                    <div style="font-size:0.78rem;color:var(--text-secondary);">{{ $motorcycle->driver->phone ?? 'No phone' }}</div>
                                </div>
                            </div>
                        @else
                            <div style="font-size:0.88rem;color:var(--text-muted);margin-top:4px;">No driver assigned</div>
                        @endif
                    </div>

                    <div style="margin-bottom:16px;">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="detail-label">Loan Amount</div>
                                <div class="detail-value" style="font-size:0.95rem;">TZS {{ number_format($motorcycle->loan->total_amount) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Paid</div>
                                <div class="detail-value" style="font-size:0.95rem;color:var(--emerald-600);">TZS {{ number_format($motorcycle->loan->amount_paid) }}</div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:0.78rem;color:var(--text-secondary);">Progress</span>
                            <span style="font-size:0.78rem;font-weight:700;color:var(--text);">{{ $motorcycle->loan->progress }}%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width:{{ $motorcycle->loan->progress }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size:0.72rem;color:var(--text-muted);">TZS {{ number_format($motorcycle->loan->amount_paid) }}</span>
                            <span style="font-size:0.72rem;color:var(--text-muted);">TZS {{ number_format($motorcycle->loan->total_amount) }}</span>
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <span class="badge-status {{ $motorcycle->loan->status }}">{{ ucfirst(str_replace('_', ' ', $motorcycle->loan->status)) }}</span>
                    </div>

                    <a href="{{ route('loans.show', $motorcycle->loan) }}" class="btn btn-navy w-100" style="padding:10px;">
                        <i class="bi bi-calendar-week me-1"></i> View Loan Details
                    </a>
                    @if($motorcycle->driver)
                    <a href="{{ route('owner.vehicles.track', $motorcycle) }}" class="btn btn-gold w-100 mt-2" style="padding:10px;">
                        <i class="bi bi-geo-alt-fill me-1"></i> Track GPS
                    </a>
                    @endif
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm mb-4">
                <div style="padding:24px;text-align:center;">
                    <h6 style="font-size:0.82rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);text-align:left;">
                        <i class="bi bi-wallet2 me-1" style="color:var(--navy-700);"></i> Loan Status
                    </h6>
                    <div style="padding:20px 0;">
                        <div style="width:56px;height:56px;border-radius:14px;background:var(--page-bg);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="bi bi-wallet2" style="font-size:1.4rem;color:var(--text-secondary);"></i>
                        </div>
                        <div style="font-size:0.9rem;color:var(--text-secondary);font-weight:500;">No active loan</div>
                        <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Apply or assign a driver to start</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- APPLICATIONS SECTION --}}
@if($applications->count() && !$motorcycle->loan)
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div style="padding:20px 24px;border-bottom:1px solid var(--border);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--navy-900);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-people-fill" style="font-size:1.1rem;color:#fff;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color:var(--text);font-size:1rem;">Driver Applications</h6>
                            <div style="font-size:0.78rem;color:var(--text-secondary);">{{ $applications->where('status','pending')->count() }} pending · {{ $applications->where('status','approved')->count() }} accepted · {{ $applications->where('status','rejected')->count() }} rejected</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding:20px 24px;">
                @foreach($applications as $app)
                    @php
                        $driver = $app->driver;
                        $isPending = $app->status === 'pending';
                        $isAccepted = $app->status === 'approved';
                        $isRejected = $app->status === 'rejected';
                    @endphp

                    <div style="border:1px solid {{ $isPending ? '#F59E0B' : ($isAccepted ? '#10B981' : '#D1D5DB') }};border-radius:12px;margin-bottom:{{ $loop->last ? '0' : '16px' }};overflow:hidden;">
                        <div style="background:{{ $isPending ? '#FFFBEB' : ($isAccepted ? '#ECFDF5' : '#F9FAFB') }};padding:16px 20px;border-bottom:1px solid {{ $isPending ? '#FDE68A' : ($isAccepted ? '#A7F3D0' : '#D1D5DB') }};">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:48px;height:48px;border-radius:50%;background:{{ $isPending ? '#F59E0B' : ($isAccepted ? '#10B981' : '#6B7280') }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0;">{{ substr($driver->name ?? 'D', 0, 1) }}</div>
                                    <div>
                                        <div style="font-weight:700;font-size:1rem;color:var(--text);">{{ $driver->name ?? 'Unknown' }}</div>
                                        <div style="font-size:0.82rem;color:var(--text-secondary);">Applied {{ $app->created_at->format('d M Y \a\t g:i A') }}</div>
                                    </div>
                                </div>
                                <div>
                                    @if($isPending)
                                        <span style="background:#FEF3C7;color:#92400E;padding:6px 14px;border-radius:20px;font-size:0.78rem;font-weight:700;">Pending Review</span>
                                    @elseif($isAccepted)
                                        <span style="background:#D1FAE5;color:#065F46;padding:6px 14px;border-radius:20px;font-size:0.78rem;font-weight:700;">Accepted</span>
                                    @else
                                        <span style="background:#FEE2E2;color:#991B1B;padding:6px 14px;border-radius:20px;font-size:0.78rem;font-weight:700;">Rejected</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="padding:20px;">
                            <div style="font-size:0.78rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;">Applicant Details</div>
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Full Name</div>
                                    <div class="detail-value">{{ $driver->name ?? '—' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value" style="word-break:break-all;">{{ $driver->email ?? '—' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Phone</div>
                                    <div class="detail-value">{{ $driver->phone ?? '—' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">NIDA Number</div>
                                    <div class="detail-value">{{ $driver->nida ?? '—' }}</div>
                                </div>
                                @if($driver->address)
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Address</div>
                                    <div class="detail-value">{{ $driver->address }}</div>
                                </div>
                                @endif
                                @if($driver->birthdate)
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Date of Birth</div>
                                    <div class="detail-value">{{ $driver->birthdate->format('d M Y') }}</div>
                                </div>
                                @endif
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Account Status</div>
                                    <div class="detail-value">
                                        @if($driver->approval_status === 'approved')
                                            <span style="color:#059669;">Approved</span>
                                        @elseif($driver->approval_status === 'pending')
                                            <span style="color:#D97706;">Pending</span>
                                        @else
                                            <span style="color:#DC2626;">{{ ucfirst($driver->approval_status) }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($app->license_number)
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">License Number</div>
                                    <div class="detail-value">{{ $app->license_number }}</div>
                                </div>
                                @endif
                                @if($app->guarantor_name)
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Guarantor</div>
                                    <div class="detail-value">{{ $app->guarantor_name }}</div>
                                </div>
                                @endif
                                @if($app->guarantor_phone)
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Guarantor Phone</div>
                                    <div class="detail-value">{{ $app->guarantor_phone }}</div>
                                </div>
                                @endif
                                @if($app->notes)
                                <div class="col-12">
                                    <div class="detail-label">Driver's Note</div>
                                    <div style="font-size:0.88rem;color:var(--text);background:var(--page-bg);padding:12px 16px;border-radius:8px;margin-top:4px;">{{ $app->notes }}</div>
                                </div>
                                @endif
                            </div>

                            @if($isPending)
                            <div style="border-top:1px solid var(--border);margin-top:20px;padding-top:16px;" class="d-flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('owner.vehicles.accept', [$motorcycle, $app]) }}" onsubmit="return confirm('Accept {{ $driver->name }}? A contract and loan will be created.');">
                                    @csrf
                                    <button type="submit" class="btn" style="background:#059669;color:#fff;padding:10px 28px;font-weight:700;font-size:0.88rem;border-radius:8px;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Accept Driver
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('owner.vehicles.reject', [$motorcycle, $app]) }}" onsubmit="return confirm('Reject {{ $driver->name }}?');">
                                    @csrf
                                    <button type="submit" class="btn" style="background:#FEE2E2;color:#991B1B;padding:10px 28px;font-weight:700;font-size:0.88rem;border-radius:8px;">
                                        <i class="bi bi-x-circle-fill me-1"></i> Reject
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<style>
.detail-label { font-size:0.72rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
.detail-value { font-size:0.92rem; color:var(--text); font-weight:600; line-height:1.4; }
</style>

@endsection
