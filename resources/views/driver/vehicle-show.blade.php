@extends('layouts.app')
@section('title', $motorcycle->make . ' ' . $motorcycle->model)
@section('page-title', $motorcycle->make . ' ' . $motorcycle->model)
@section('content')

<a href="{{ route('driver.marketplace') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-secondary);text-decoration:none;font-size:0.88rem;font-weight:600;margin-bottom:20px;"><i class="bi bi-arrow-left"></i> Back to Browse</a>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card" style="overflow:hidden;">
            <div style="position:relative;padding-top:50%;background:var(--page-bg);">
                @if($motorcycle->photo)
                    <img src="{{ asset('storage/' . $motorcycle->photo) }}" alt="{{ $motorcycle->make }} {{ $motorcycle->model }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                @else
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);opacity:0.3;">
                        <i class="bi bi-motorcycle" style="font-size:4rem;"></i>
                    </div>
                @endif
            </div>
            <div style="padding:24px;">
                <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
                    <div>
                        <h3 style="font-size:1.3rem;font-weight:800;color:var(--text);margin:0;">{{ $motorcycle->make }} {{ $motorcycle->model }}</h3>
                        <span style="color:var(--text-secondary);font-size:0.88rem;">{{ $motorcycle->plate_number }} · {{ $motorcycle->color }} · {{ $motorcycle->year }}</span>
                    </div>
                    <span class="badge-status verified"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-3"><div style="background:var(--page-bg);border-radius:10px;padding:14px;text-align:center;"><div style="font-size:1.1rem;font-weight:700;color:var(--text);">{{ $motorcycle->engine_cc }}cc</div><div style="font-size:0.72rem;color:var(--text-secondary);">Engine</div></div></div>
                    <div class="col-3"><div style="background:var(--page-bg);border-radius:10px;padding:14px;text-align:center;"><div style="font-size:1.1rem;font-weight:700;color:var(--text);">{{ $motorcycle->loan_duration_weeks }}</div><div style="font-size:0.72rem;color:var(--text-secondary);">Weeks</div></div></div>
                    <div class="col-3"><div style="background:var(--page-bg);border-radius:10px;padding:14px;text-align:center;"><div style="font-size:1.1rem;font-weight:700;color:var(--text);">TZS {{ $motorcycle->weekly_amount ? number_format($motorcycle->weekly_amount) : '—' }}</div><div style="font-size:0.72rem;color:var(--text-secondary);">Per Week</div></div></div>
                    <div class="col-3"><div style="background:var(--page-bg);border-radius:10px;padding:14px;text-align:center;"><div style="font-size:1.1rem;font-weight:700;color:var(--text);">{{ $motorcycle->color }}</div><div style="font-size:0.72rem;color:var(--text-secondary);">Color</div></div></div>
                </div>
                <div style="font-size:0.9rem;color:var(--text-secondary);line-height:1.7;">
                    This {{ $motorcycle->year }} <strong>{{ $motorcycle->make }} {{ $motorcycle->model }}</strong> ({{ $motorcycle->color }}) is available for hire-purchase through BodaLink. Pay <strong>TZS {{ $motorcycle->weekly_amount ? number_format($motorcycle->weekly_amount) : '—' }}</strong> per week for <strong>{{ $motorcycle->loan_duration_weeks }} weeks</strong> and own the bodaboda.
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card" style="padding:24px;position:sticky;top:84px;">
            <div style="text-align:center;padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid var(--border);">
                <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;font-weight:700;">Loan Amount</div>
                <div style="font-size:2rem;font-weight:900;color:var(--navy-900);">TZS {{ number_format($motorcycle->loan_amount) }}</div>
                <div style="font-size:0.82rem;color:var(--text-secondary);margin-top:4px;">{{ $motorcycle->loan_duration_weeks }} weeks · TZS {{ $motorcycle->weekly_amount ? number_format($motorcycle->weekly_amount) : '—' }}/week</div>
            </div>
            @if($motorcycle->latitude && $motorcycle->longitude)
                <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:12px;padding:14px 16px;margin-bottom:14px;">
                    <div style="font-size:0.75rem;font-weight:700;color:#1D4ED8;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;"><i class="bi bi-geo-alt-fill me-1"></i>Bodaboda Location</div>
                    <div style="font-size:0.88rem;font-weight:600;color:var(--text);">{{ $motorcycle->location_name ?: 'Set by owner' }}</div>
                    <a href="https://www.google.com/maps?q={{ $motorcycle->latitude }},{{ $motorcycle->longitude }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;color:#1D4ED8;font-size:0.82rem;font-weight:600;text-decoration:none;">
                        <i class="bi bi-box-arrow-up-right"></i> Open in Google Maps
                    </a>
                </div>
            @endif
            @if($motorcycle->owner && ($motorcycle->owner->latitude && $motorcycle->owner->longitude))
                <div style="background:var(--emerald-100,#E3F9EF);border:1px solid #A7F3D0;border-radius:12px;padding:14px 16px;margin-bottom:14px;">
                    <div style="font-size:0.75rem;font-weight:700;color:var(--emerald-600);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;"><i class="bi bi-geo-alt-fill me-1"></i>Pickup Location</div>
                    <div style="font-size:0.88rem;font-weight:600;color:var(--text);">{{ $motorcycle->owner->location_name ?: $motorcycle->owner->address ?: 'Owner location' }}</div>
                    <a href="https://www.google.com/maps?q={{ $motorcycle->owner->latitude }},{{ $motorcycle->owner->longitude }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;color:var(--emerald-600);font-size:0.82rem;font-weight:600;text-decoration:none;">
                        <i class="bi bi-box-arrow-up-right"></i> Open in Google Maps
                    </a>
                </div>
            @elseif($motorcycle->latitude && $motorcycle->longitude)
                <div style="background:var(--emerald-100,#E3F9EF);border:1px solid #A7F3D0;border-radius:12px;padding:14px 16px;margin-bottom:14px;">
                    <div style="font-size:0.75rem;font-weight:700;color:var(--emerald-600);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;"><i class="bi bi-geo-alt-fill me-1"></i>Pickup Location</div>
                    <div style="font-size:0.88rem;font-weight:600;color:var(--text);">{{ $motorcycle->location_name ?: 'Motorcycle location' }}</div>
                    <a href="https://www.google.com/maps?q={{ $motorcycle->latitude }},{{ $motorcycle->longitude }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;color:var(--emerald-600);font-size:0.82rem;font-weight:600;text-decoration:none;">
                        <i class="bi bi-box-arrow-up-right"></i> Open in Google Maps
                    </a>
                    <div style="font-size:0.72rem;color:var(--text-muted);margin-top:6px;">Navigate here to pick up the bodaboda</div>
                </div>
            @endif
            @if($existingApplication)
                <div class="alert-banner gold"><i class="bi bi-hourglass-split me-1"></i> Application Pending</div>
            @else
                <a href="{{ route('marketplace.apply', $motorcycle) }}" class="btn btn-gold w-100" style="padding:14px;"><i class="bi bi-hand-index-thumb me-1"></i> Apply Now</a>
            @endif
            @if($motorcycle->owner)
                <a href="{{ route('chat.start.direct', $motorcycle->owner_id) }}" class="btn btn-outline-navy w-100 mt-2" style="padding:12px;"><i class="bi bi-chat-dots me-1"></i> Chat with Owner</a>
            @endif
            <a href="{{ route('driver.marketplace') }}" class="btn btn-outline-navy w-100 mt-2" style="padding:12px;"><i class="bi bi-arrow-left me-1"></i> Browse More</a>
        </div>
    </div>
</div>
@endsection