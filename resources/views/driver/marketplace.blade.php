@extends('layouts.public')
@section('title', 'Bodaboda Marketplace')

@push('styles')
<style>
    .mkt-hero {
        background: linear-gradient(135deg, var(--navy-900), var(--navy-700), #1a5fd6);
        border-radius: 0;
        margin: -24px -24px 32px -24px;
        padding: 60px 48px 52px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .mkt-hero::before {
        content: '';
        position: absolute;
        top: -80px; right: -40px;
        width: 400px; height: 400px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .mkt-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: 20%;
        width: 250px; height: 250px;
        background: rgba(255,255,255,0.03);
        border-radius: 50%;
    }
    .mkt-hero-inner { position: relative; z-index: 1; max-width: 600px; }
    .mkt-hero h1 { font-size: 2.2rem; font-weight: 800; margin-bottom: 10px; line-height: 1.15; }
    .mkt-hero p { font-size: 1rem; opacity: 0.88; margin-bottom: 30px; line-height: 1.6; }

    .mkt-search {
        display: flex;
        gap: 0;
        max-width: 540px;
        background: rgba(255,255,255,0.15);
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.2);
        overflow: hidden;
    }
    .mkt-search input {
        flex: 1;
        border: none;
        padding: 16px 20px;
        font-size: 0.95rem;
        background: transparent;
        color: #fff;
        outline: none;
    }
    .mkt-search input::placeholder { color: rgba(255,255,255,0.55); }
    .mkt-search button {
        background: #fff;
        color: var(--navy-900);
        border: none;
        padding: 16px 28px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .mkt-search button:hover { background: #f0f4ff; }

    .mkt-trust {
        display: flex;
        gap: 28px;
        margin-top: 28px;
        flex-wrap: wrap;
    }
    .mkt-trust span {
        font-size: 0.82rem;
        opacity: 0.85;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .mkt-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .mkt-pills { display: flex; gap: 8px; flex-wrap: wrap; }
    .mkt-pill {
        padding: 8px 18px;
        border-radius: 24px;
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text-secondary);
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .mkt-pill:hover { border-color: var(--navy-900); color: var(--navy-900); }
    .mkt-pill.active { background: var(--navy-900); color: #fff; border-color: var(--navy-900); }

    .mkt-sort {
        padding: 9px 14px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: #fff;
        font-size: 0.82rem;
        color: var(--text);
        font-weight: 500;
        outline: none;
        cursor: pointer;
    }
    .mkt-count { font-size: 0.85rem; color: var(--text-secondary); }
    .mkt-count strong { color: var(--text); }

    .mkt-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    @media (max-width: 1024px) { .mkt-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .mkt-grid { grid-template-columns: 1fr; } }

    .mkt-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(15,27,45,0.04);
    }
    .mkt-card:hover {
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        transform: translateY(-4px);
        border-color: var(--border);
    }

    .mkt-card-img {
        position: relative;
        width: 100%;
        padding-top: 56%;
        background: var(--page-bg);
        overflow: hidden;
    }
    .mkt-card-img img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .mkt-card:hover .mkt-card-img img { transform: scale(1.05); }

    .mkt-card-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        background: var(--emerald-600);
        color: #fff;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 700;
        backdrop-filter: blur(4px);
    }

    .mkt-card-body { padding: 20px; }
    .mkt-card-name { font-size: 1.15rem; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .mkt-card-detail { font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 16px; }

    .mkt-card-price {
        display: flex;
        align-items: baseline;
        gap: 4px;
        margin-bottom: 16px;
    }
    .mkt-card-price .currency { font-size: 0.82rem; color: var(--navy-700); font-weight: 600; }
    .mkt-card-price .amount { font-size: 1.4rem; font-weight: 800; color: var(--navy-900); }

    .mkt-card-offers {
        display: flex;
        gap: 16px;
        padding-top: 14px;
        border-top: 1px solid var(--border);
    }
    .mkt-offer {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }
    .mkt-offer i { color: var(--text-secondary); font-size: 0.85rem; }

    .mkt-card-action {
        padding: 16px 20px;
        border-top: 1px solid var(--border);
    }
    .mkt-card-action .btn {
        width: 100%;
        border-radius: 12px;
        padding: 12px;
        font-weight: 700;
        font-size: 0.88rem;
        justify-content: center;
    }

    .mkt-empty {
        text-align: center;
        padding: 80px 24px;
    }
    .mkt-empty-icon {
        width: 88px; height: 88px;
        border-radius: 50%;
        background: var(--page-bg);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        color: var(--text-secondary);
        margin-bottom: 20px;
    }
    .mkt-empty h5 { font-weight: 700; color: var(--text); margin-bottom: 8px; }
    .mkt-empty p { font-size: 0.9rem; color: var(--text-secondary); max-width: 380px; margin: 0 auto 20px; }

    .mkt-pager { display: flex; justify-content: center; margin-top: 36px; }
</style>
@endpush

@section('content')
@php
    $totalAvailable = \App\Models\Motorcycle::where('verification_status','verified')->where('status','available')->whereNull('driver_id')->count();
@endphp

<div class="mkt-hero">
    <div class="mkt-hero-inner">
        <h1>BodaBoda Marketplace</h1>
        <p>Browse available motorcycles for hire-purchase. Pick one, apply, and start riding while you pay weekly.</p>
        <form method="GET" class="mkt-search">
            <input type="text" name="search" placeholder="Search by make, model, or plate number..." value="{{ request('search') }}">
            <button type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="mkt-trust">
            <span><i class="bi bi-patch-check-fill text-warning"></i> All bikes verified</span>
            <span><i class="bi bi-shield-check"></i> Secure payments</span>
            <span><i class="bi bi-cash-stack"></i> Flexible weekly plans</span>
        </div>
    </div>
</div>

<div class="mkt-toolbar">
    <div class="mkt-pills">
        <a href="{{ route('driver.marketplace') }}" class="mkt-pill {{ !request('min_price') && !request('max_price') ? 'active' : '' }}">All</a>
        <a href="{{ route('driver.marketplace', array_merge(request()->query(), ['max_price' => 500000])) }}" class="mkt-pill {{ request('max_price') == 500000 ? 'active' : '' }}">Under 500K</a>
        <a href="{{ route('driver.marketplace', array_merge(request()->query(), ['min_price' => 500000, 'max_price' => 1500000])) }}" class="mkt-pill {{ request('min_price') == 500000 ? 'active' : '' }}">500K - 1.5M</a>
        <a href="{{ route('driver.marketplace', array_merge(request()->query(), ['min_price' => 1500000, 'max_price' => 2500000])) }}" class="mkt-pill {{ request('min_price') == 1500000 ? 'active' : '' }}">1.5M - 2.5M</a>
        <a href="{{ route('driver.marketplace', array_merge(request()->query(), ['min_price' => 2500000])) }}" class="mkt-pill {{ request('min_price') == 2500000 ? 'active' : '' }}">2.5M+</a>
    </div>
    <div style="display:flex;align-items:center;gap:16px;">
        <span class="mkt-count"><strong>{{ $motorcycles->total() }}</strong> results</span>
        <select class="mkt-sort" onchange="window.location.href=this.value">
            <option value="{{ route('driver.marketplace', array_merge(request()->query(), ['sort' => 'newest'])) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="{{ route('driver.marketplace', array_merge(request()->query(), ['sort' => 'price_low'])) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="{{ route('driver.marketplace', array_merge(request()->query(), ['sort' => 'price_high'])) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
        </select>
    </div>
</div>

@if($motorcycles->count() > 0)
    <div class="mkt-grid">
        @foreach($motorcycles as $m)
            <div class="mkt-card" onclick="window.location='{{ route('driver.marketplace.show', $m) }}'">
                <div class="mkt-card-img">
                    @if($m->photo)
                        <img src="{{ asset('storage/' . $m->photo) }}" alt="{{ $m->make }} {{ $m->model }}" loading="lazy">
                    @else
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:var(--page-bg);color:var(--text-secondary);opacity:0.3;">
                            <i class="bi bi-bicycle" style="font-size:3rem;"></i>
                        </div>
                    @endif
                    @if($m->engine_cc)
                        <span style="position:absolute;top:14px;left:14px;background:var(--navy-900);color:#fff;padding:4px 10px;border-radius:6px;font-size:0.72rem;font-weight:700;">{{ $m->engine_cc }}cc</span>
                    @endif
                </div>

                <div class="mkt-card-body">
                    <div class="mkt-card-name">{{ $m->make }} {{ $m->model }}</div>
                    <div class="mkt-card-detail">{{ $m->plate_number }}</div>

                    @if($m->loan_amount)
                        <div class="mkt-card-price">
                            <span class="currency">TZS</span>
                            <span class="amount">{{ number_format($m->loan_amount) }}</span>
                        </div>
                    @endif

                    <div class="mkt-card-offers">
                        <div class="mkt-offer"><i class="bi bi-calendar-week"></i> {{ $m->loan_duration_weeks }} weeks</div>
                        <div class="mkt-offer"><i class="bi bi-cash"></i> TZS {{ $m->weekly_amount ? number_format($m->weekly_amount) : '—' }} /week</div>
                    </div>
                </div>

                <div class="mkt-card-action">
                    <a href="{{ route('marketplace.apply', $m) }}" class="btn btn-gold" onclick="event.stopPropagation();"><i class="bi bi-hand-index-thumb me-1"></i> Apply Now</a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mkt-pager">
        {{ $motorcycles->withQueryString()->links() }}
    </div>
@else
    <div class="mkt-empty">
        <div class="mkt-empty-icon"><i class="bi bi-search"></i></div>
        <h5>No motorcycles found</h5>
        <p>Check back later — new bodabodas are added daily.</p>
        <a href="{{ route('home') }}" class="btn btn-gold"><i class="bi bi-search me-1"></i>Clear Filters</a>
    </div>
@endif
@endsection