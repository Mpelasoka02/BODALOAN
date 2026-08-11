@extends('layouts.app')
@section('title', 'Marketplace')
@section('page-title', 'Marketplace')

@section('styles')
<style>
    .mp-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px}
    .mp-stat{background:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:18px 20px}
    .mp-stat .num{font-size:1.5rem;font-weight:800;color:var(--navy-900)}
    .mp-stat .lbl{font-size:.78rem;color:var(--text-muted);margin-top:2px;font-weight:500}
    .mp-stat.gold .num{color:var(--gold-500)}
    .mp-stat.green .num{color:var(--emerald-600)}
    .mp-stat.blue .num{color:var(--navy-700)}

    .mp-toolbar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:24px}
    .mp-search{position:relative;flex:1;min-width:200px}
    .mp-search i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.9rem}
    .mp-search input{width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px 10px 40px;font-size:.88rem;color:var(--text);font-family:'Inter',sans-serif;background:var(--card-bg);transition:border-color .2s}
    .mp-search input:focus{outline:none;border-color:var(--navy-700);box-shadow:0 0 0 3px rgba(27,51,88,.08)}
    .mp-filter{padding:9px 16px;border-radius:10px;border:1.5px solid var(--border);background:var(--card-bg);font-size:.84rem;font-weight:600;color:var(--text-muted);cursor:pointer;text-decoration:none;transition:all .2s;font-family:'Inter',sans-serif;white-space:nowrap}
    .mp-filter:hover{border-color:var(--navy-700);color:var(--navy-700)}
    .mp-filter.active{background:var(--navy-900);color:#fff;border-color:var(--navy-900)}
    .mp-sort{padding:9px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--card-bg);font-size:.84rem;color:var(--text);font-weight:500;font-family:'Inter',sans-serif;outline:none;cursor:pointer;margin-left:auto}
    .mp-count{font-size:.85rem;color:var(--text-muted);font-weight:500}

    .mp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
    .mp-card{background:var(--card-bg);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:all .3s;text-decoration:none;color:inherit;display:block}
    .mp-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(15,27,45,.08);border-color:var(--gold-500)}
    .mp-card-img{position:relative;width:100%;padding-top:58%;background:#f1f5f9;overflow:hidden}
    .mp-card-img img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .4s}
    .mp-card:hover .mp-card-img img{transform:scale(1.05)}
    .mp-card-img .placeholder{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#dbeafe,#e0e7ff);color:#3b82f6}
    .mp-card-img .placeholder i{font-size:2.5rem;opacity:.2}
    .mp-card-badge{position:absolute;top:10px;right:10px;padding:4px 10px;border-radius:6px;font-size:.68rem;font-weight:700;display:flex;align-items:center;gap:4px;backdrop-filter:blur(4px)}
    .mp-card-badge.verified{background:rgba(14,159,110,.9);color:#fff}
    .mp-card-badge.pending{background:rgba(201,150,44,.9);color:#fff}
    .mp-card-badge.rejected{background:rgba(220,38,38,.9);color:#fff}
    .mp-card-badge.available{position:absolute;top:10px;left:10px;background:rgba(37,99,235,.9);color:#fff}
    .mp-card-badge.assigned{position:absolute;top:10px;left:10px;background:rgba(107,114,128,.9);color:#fff}
    .mp-card-cc{position:absolute;top:10px;left:10px;background:rgba(0,0,0,.55);color:#fff;padding:3px 8px;border-radius:6px;font-size:.68rem;font-weight:700;backdrop-filter:blur(4px)}
    .mp-card-body{padding:16px 18px}
    .mp-card-name{font-size:1rem;font-weight:700;margin-bottom:3px;transition:color .2s}
    .mp-card:hover .mp-card-name{color:var(--navy-700)}
    .mp-card-detail{font-size:.78rem;color:var(--text-muted);margin-bottom:10px}
    .mp-card-price{display:flex;align-items:baseline;gap:4px;margin-bottom:12px}
    .mp-card-price .currency{font-size:.74rem;color:var(--navy-700);font-weight:600}
    .mp-card-price .amount{font-size:1.25rem;font-weight:800;color:var(--navy-900)}
    .mp-card-specs{display:flex;gap:14px;padding-top:10px;border-top:1px solid var(--border)}
    .mp-card-spec{display:flex;align-items:center;gap:4px;font-size:.74rem;color:var(--text-muted)}
    .mp-card-spec i{color:var(--text-muted);font-size:.78rem}
    .mp-card-owner{display:flex;align-items:center;gap:6px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border);font-size:.78rem;color:var(--text-muted)}
    .mp-card-owner .avatar{width:22px;height:22px;border-radius:50%;background:var(--navy-700);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;flex-shrink:0}
    .mp-card-actions{display:flex;gap:8px;margin-top:12px}
    .mp-card-btn{flex:1;padding:8px 12px;border-radius:8px;font-size:.78rem;font-weight:600;text-align:center;text-decoration:none;transition:all .2s;border:none;cursor:pointer;font-family:'Inter',sans-serif}
    .mp-card-btn.primary{background:var(--gold-500);color:#fff}
    .mp-card-btn.primary:hover{background:#B8872A}
    .mp-card-btn.secondary{background:var(--page-bg);color:var(--text);border:1px solid var(--border)}
    .mp-card-btn.secondary:hover{border-color:var(--navy-700);color:var(--navy-700)}
    .mp-card-btn.verify{background:var(--emerald-600);color:#fff}
    .mp-card-btn.reject{background:#DC2626;color:#fff}

    .mp-empty{text-align:center;padding:64px 20px}
    .mp-empty-icon{width:80px;height:80px;border-radius:50%;background:var(--page-bg);display:inline-flex;align-items:center;justify-content:center;font-size:2rem;color:var(--text-muted);margin-bottom:16px}
    .mp-empty h5{font-weight:700;margin-bottom:6px}
    .mp-empty p{font-size:.88rem;color:var(--text-muted);margin-bottom:20px;max-width:360px;margin-left:auto;margin-right:auto}

    .mp-pager{display:flex;justify-content:center;margin-top:28px}

    @media(max-width:1024px){.mp-grid{grid-template-columns:repeat(2,1fr)}.mp-stats{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:640px){.mp-grid{grid-template-columns:1fr}.mp-stats{grid-template-columns:1fr}.mp-toolbar{flex-direction:column}}
</style>
@endsection

@section('content')
<div class="mp-stats">
    <div class="mp-stat blue"><div class="num">{{ $stats['total'] }}</div><div class="lbl">Total Motorcycles</div></div>
    <div class="mp-stat green"><div class="num">{{ $stats['available'] }}</div><div class="lbl">Available</div></div>
    <div class="mp-stat"><div class="num">{{ $stats['assigned'] }}</div><div class="lbl">Assigned</div></div>
    <div class="mp-stat gold"><div class="num">{{ $stats['verified'] }}</div><div class="lbl">Verified</div></div>
</div>

<div class="mp-toolbar">
    <form class="mp-search" method="GET" action="{{ route('marketplace') }}">
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="Search by make, model, or plate..." value="{{ request('q') }}">
    </form>
    <a href="{{ route('marketplace', array_merge(request()->query(), ['max_price' => 500000])) }}" class="mp-filter {{ request('max_price') == 500000 ? 'active' : '' }}">Under 500K</a>
    <a href="{{ route('marketplace', array_merge(request()->query(), ['min_price' => 500000, 'max_price' => 1500000])) }}" class="mp-filter {{ request('min_price') == 500000 ? 'active' : '' }}">500K – 1.5M</a>
    <a href="{{ route('marketplace', array_merge(request()->query(), ['min_price' => 1500000, 'max_price' => 2500000])) }}" class="mp-filter {{ request('min_price') == 1500000 ? 'active' : '' }}">1.5M – 2.5M</a>
    <a href="{{ route('marketplace', array_merge(request()->query(), ['min_price' => 2500000])) }}" class="mp-filter {{ request('min_price') == 2500000 ? 'active' : '' }}">2.5M+</a>
    @if(request('q') || request('min_price') || request('max_price'))
        <a href="{{ route('marketplace') }}" class="mp-filter"><i class="bi bi-x-lg me-1"></i> Clear</a>
    @endif
    <span class="mp-count"><strong>{{ $motorcycles->total() }}</strong> results</span>
    <select class="mp-sort" onchange="window.location.href=this.value">
        <option value="{{ route('marketplace', array_merge(request()->query(), ['sort' => 'newest'])) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
        <option value="{{ route('marketplace', array_merge(request()->query(), ['sort' => 'price_low'])) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
        <option value="{{ route('marketplace', array_merge(request()->query(), ['sort' => 'price_high'])) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
    </select>
</div>

@if(auth()->user()->isOwner())
    <div style="margin-bottom:20px;">
        <a href="{{ route('owner.vehicles.create') }}" class="btn btn-gold"><i class="bi bi-plus-lg me-1"></i> List a Bodaboda</a>
    </div>
@endif

@if($motorcycles->count() > 0)
    <div class="mp-grid">
        @foreach($motorcycles as $m)
            <div class="mp-card">
                <div class="mp-card-img">
                    @if($m->photo)
                        <img src="{{ asset('storage/' . $m->photo) }}" alt="{{ $m->make }} {{ $m->model }}" loading="lazy">
                    @else
                        <div class="placeholder"><i class="bi bi-bicycle"></i></div>
                    @endif

                    @if($m->verification_status === 'verified')
                        <div class="mp-card-badge verified"><i class="bi bi-patch-check-fill"></i> Verified</div>
                    @elseif($m->verification_status === 'pending')
                        <div class="mp-card-badge pending"><i class="bi bi-clock-fill"></i> Pending</div>
                    @else
                        <div class="mp-card-badge rejected"><i class="bi bi-x-circle-fill"></i> {{ ucfirst($m->verification_status) }}</div>
                    @endif

                    @if($m->engine_cc && !$m->photo)
                        <div class="mp-card-cc">{{ $m->engine_cc }}cc</div>
                    @endif

                    <div class="mp-card-badge {{ $m->status }}">{{ ucfirst($m->status) }}</div>
                </div>
                <div class="mp-card-body">
                    <div class="mp-card-name">{{ $m->make }} {{ $m->model }}</div>
                    <div class="mp-card-detail">{{ $m->year }} &middot; {{ $m->color }} &middot; {{ $m->plate_number }}</div>
                    @if($m->loan_amount)
                        <div class="mp-card-price">
                            <span class="currency">TZS</span>
                            <span class="amount">{{ number_format($m->loan_amount) }}</span>
                        </div>
                    @endif
                    <div class="mp-card-specs">
                        <div class="mp-card-spec"><i class="bi bi-calendar-week"></i> {{ $m->loan_duration_weeks }}wks</div>
                        <div class="mp-card-spec"><i class="bi bi-cash"></i> TZS {{ $m->weekly_amount ? number_format($m->weekly_amount) : '—' }}/wk</div>
                    </div>

                    @if(!auth()->user()->isOwner())
                    <div class="mp-card-owner">
                        <div class="avatar">{{ $m->owner ? strtoupper(substr($m->owner->name, 0, 1)) : '?' }}</div>
                        {{ $m->owner ? $m->owner->name : 'Unknown' }}
                    </div>
                    @endif

                    <div class="mp-card-actions">
                        @if(auth()->user()->isDriver())
                            @if($m->status === 'available' && $m->isVerified())
                                <a href="{{ route('marketplace.apply', $m) }}" class="mp-card-btn primary"><i class="bi bi-hand-index-thumb me-1"></i> Apply</a>
                            @endif
                            <a href="{{ route('driver.marketplace.show', $m) }}" class="mp-card-btn secondary">View Details</a>
                        @elseif(auth()->user()->isAdmin())
                            @if($m->verification_status === 'pending')
                                <form method="POST" action="{{ route('admin.vehicles.verify', $m) }}" style="flex:1">@csrf<button type="submit" class="mp-card-btn verify"><i class="bi bi-check-lg me-1"></i> Verify</button></form>
                                <form method="POST" action="{{ route('admin.vehicles.reject', $m) }}" style="flex:1">@csrf<button type="submit" class="mp-card-btn reject"><i class="bi bi-x-lg me-1"></i> Reject</button></form>
                            @else
                                <a href="{{ route('admin.vehicles') }}" class="mp-card-btn secondary">Manage</a>
                            @endif
                        @elseif(auth()->user()->isOwner())
                            <a href="{{ route('owner.vehicles.show', $m) }}" class="mp-card-btn secondary">View</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mp-pager">{{ $motorcycles->links() }}</div>
@else
    <div class="mp-empty">
        <div class="mp-empty-icon"><i class="bi bi-search"></i></div>
        <h5>No motorcycles found</h5>
        <p>@if(auth()->user()->isOwner()) List your first bodaboda to get started. @else Try adjusting your search or filters. @endif</p>
        @if(auth()->user()->isOwner())
            <a href="{{ route('owner.vehicles.create') }}" class="btn btn-gold"><i class="bi bi-plus-lg me-1"></i> List a Bodaboda</a>
        @else
            <a href="{{ route('marketplace') }}" class="btn btn-navy"><i class="bi bi-arrow-counterclockwise me-1"></i> Clear Filters</a>
        @endif
    </div>
@endif
@endsection
