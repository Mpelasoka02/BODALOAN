@extends('layouts.app')
@section('title', 'Browse Bodabodas')
@section('page-title', 'Available Bodabodas')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <form method="GET" class="d-flex gap-2" style="max-width:400px;width:100%;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by make, model, plate..." class="form-control" style="flex:1;font-size:0.88rem;">
        <button type="submit" class="btn btn-navy btn-sm"><i class="bi bi-search me-1"></i>Search</button>
    </form>
    <span style="color:var(--text-secondary);font-size:0.88rem;">{{ $motorcycles->total() }} bodabodas available</span>
</div>

@if($motorcycles->count())
<div class="row g-4">
    @foreach($motorcycles as $m)
    <div class="col-lg-4 col-md-6">
        <a href="{{ route('driver.marketplace.show', $m) }}" style="text-decoration:none;color:inherit;display:block;" class="card" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.08)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 1px 3px rgba(15,27,45,0.04)'">
            <div style="position:relative;padding-top:60%;background:var(--page-bg);overflow:hidden;">
                @if($m->photo)
                    <img src="{{ asset('storage/' . $m->photo) }}" alt="{{ $m->make }} {{ $m->model }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                @else
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);opacity:0.3;">
                        <i class="bi bi-motorcycle" style="font-size:3rem;"></i>
                    </div>
                @endif
                <span class="badge-status verified" style="position:absolute;top:10px;right:10px;"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                @if($m->engine_cc)
                    <span style="position:absolute;top:10px;left:10px;background:var(--navy-900);color:#fff;padding:4px 10px;border-radius:6px;font-size:0.72rem;font-weight:700;">{{ $m->engine_cc }}cc</span>
                @endif
                <span style="position:absolute;bottom:10px;left:10px;background:var(--navy-900);color:#fff;padding:4px 10px;border-radius:6px;font-size:0.72rem;font-weight:700;">{{ $m->plate_number }}</span>
            </div>
            <div style="padding:20px;">
                <div style="font-size:1rem;font-weight:700;color:var(--text);margin-bottom:4px;">{{ $m->make }} {{ $m->model }}</div>
                <div style="font-size:0.8rem;color:var(--text-secondary);margin-bottom:12px;">{{ $m->year }} · {{ $m->color }}</div>
                <div style="display:flex;align-items:baseline;gap:4px;margin-bottom:12px;">
                    <span style="font-size:0.78rem;color:var(--navy-700);font-weight:600;">TZS</span>
                    <span style="font-size:1.2rem;font-weight:800;color:var(--navy-900);">{{ number_format($m->loan_amount) }}</span>
                </div>
                <div style="display:flex;gap:16px;padding-top:12px;border-top:1px solid var(--border);font-size:0.78rem;color:var(--text-secondary);">
                    <span><i class="bi bi-calendar-week me-1"></i>{{ $m->loan_duration_weeks }} wks</span>
                    <span><i class="bi bi-cash me-1"></i>TZS {{ $m->weekly_amount ? number_format($m->weekly_amount) : '—' }}/wk</span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
<div class="mt-4 d-flex justify-content-center">{{ $motorcycles->withQueryString()->links() }}</div>
@else
<div class="empty-state">
    <div class="empty-state-icon"><i class="bi bi-search"></i></div>
    <h5>No bodabodas available right now</h5>
    <p>Check back later or adjust your search.</p>
</div>
@endif
@endsection