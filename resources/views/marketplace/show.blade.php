<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $motorcycle->make }} {{ $motorcycle->model }} — BodaLink</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{font-family:'Inter',sans-serif;background:#f8fafc;color:#1e293b;-webkit-font-smoothing:antialiased}

        .nav{background:rgba(255,255,255,.92);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-bottom:1px solid #e2e8f0;padding:0 clamp(20px,4vw,48px);height:64px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;color:#1B3358;font-weight:800;font-size:1.25rem}
        .nav-brand i{font-size:1.5rem}
        .nav-center{display:flex;align-items:center;gap:2px}
        .nav-center a{padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.85rem;font-weight:600;color:#64748b;transition:all .2s}
        .nav-center a:hover{color:#1B3358;background:#f1f5f9}
        .nav-actions{display:flex;align-items:center;gap:10px}
        .nav-actions a,.nav-actions button{padding:9px 20px;border-radius:10px;text-decoration:none;font-size:0.85rem;font-weight:600;transition:all .2s;font-family:'Inter',sans-serif;display:inline-flex;align-items:center;gap:6px}
        .btn-ghost{color:#475569;background:none;border:none;cursor:pointer}
        .btn-ghost:hover{color:#1B3358;background:#f1f5f9}
        .btn-fill{background:#1B3358;color:#fff;border:none;cursor:pointer}
        .btn-fill:hover{background:#0F1B2D;box-shadow:0 4px 12px rgba(27,51,88,.2)}

        .hero-img{width:100%;height:clamp(320px,45vw,520px);background:#0F1B2D;overflow:hidden;position:relative}
        .hero-img img{width:100%;height:100%;object-fit:cover;transition:transform .8s cubic-bezier(.4,0,.2,1)}
        .hero-img::after{content:'';position:absolute;inset:0;background:linear-gradient(0deg,rgba(15,27,45,.55) 0%,rgba(15,27,45,.15) 30%,transparent 55%);pointer-events:none}
        .hero-img-overlay{position:absolute;bottom:0;left:0;right:0;padding:clamp(20px,4vw,48px);z-index:2;display:flex;align-items:flex-end;justify-content:space-between;gap:20px;flex-wrap:wrap}
        .hero-img-title{color:#fff;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:900;letter-spacing:-.5px;line-height:1.1}
        .hero-img-sub{color:rgba(255,255,255,.75);font-size:.88rem;margin-top:6px}
        .hero-img-badge{background:rgba(22,163,74,.92);color:#fff;padding:6px 14px;border-radius:8px;font-size:.72rem;font-weight:700;backdrop-filter:blur(4px);display:inline-flex;align-items:center;gap:5px}
        .hero-img-back{position:absolute;top:20px;left:clamp(20px,4vw,48px);z-index:3}
        .hero-img-back a{display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.85);text-decoration:none;font-size:.85rem;font-weight:600;background:rgba(0,0,0,.3);padding:8px 16px;border-radius:10px;backdrop-filter:blur(8px);transition:all .2s}
        .hero-img-back a:hover{background:rgba(0,0,0,.5);color:#fff}

        .detail-wrap{max-width:1100px;margin:0 auto;padding:clamp(20px,4vw,40px) clamp(16px,3vw,24px)}
        .detail-grid{display:grid;grid-template-columns:1fr 380px;gap:32px;align-items:start}

        .detail-main{background:#fff;border:1px solid #e2e8f0;border-radius:20px;padding:clamp(24px,3vw,36px)}
        .detail-section{margin-bottom:32px}
        .detail-section:last-child{margin-bottom:0}
        .detail-section-title{font-size:.72rem;font-weight:800;color:#1B3358;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:14px;display:flex;align-items:center;gap:8px}
        .detail-section-title i{font-size:.85rem}

        .specs-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        .spec-tile{background:#f8fafc;border:1px solid #f1f5f9;border-radius:14px;padding:18px 16px;display:flex;align-items:center;gap:14px;transition:all .25s}
        .spec-tile:hover{border-color:#E8EDF4;background:#fff;transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.04)}
        .spec-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0}
        .spec-icon.blue{background:#E8EDF4;color:#1B3358}
        .spec-icon.green{background:#E3F9EF;color:#0E9F6E}
        .spec-icon.orange{background:#FBF3E2;color:#C9962C}
        .spec-icon.purple{background:#E8EDF4;color:#3E5C85}
        .spec-val{font-size:.95rem;font-weight:700;line-height:1.2}
        .spec-lbl{font-size:.72rem;color:#94a3b8;margin-top:2px}

        .detail-desc{font-size:.92rem;color:#475569;line-height:1.75}

        .owner-card{background:#f8fafc;border:1px solid #f1f5f9;border-radius:14px;padding:20px;display:flex;align-items:center;gap:16px}
        .owner-avatar{width:48px;height:48px;border-radius:50%;background:#E8EDF4;color:#1B3358;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;flex-shrink:0}
        .owner-name{font-size:.95rem;font-weight:700}
        .owner-label{font-size:.72rem;color:#94a3b8;margin-top:2px}

        .detail-sidebar{position:sticky;top:84px}

        .price-card{background:#fff;border:1px solid #e2e8f0;border-radius:20px;padding:28px;margin-bottom:20px}
        .price-tag{text-align:center;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #f1f5f9}
        .price-tag .label{font-size:.72rem;color:#94a3b8;text-transform:uppercase;font-weight:700;letter-spacing:.6px}
        .price-tag .price{font-size:2rem;font-weight:900;color:#1B3358;margin-top:4px;line-height:1}
        .price-tag .sub{font-size:.82rem;color:#64748b;margin-top:6px}
        .price-breakdown{display:flex;flex-direction:column;gap:12px;margin-bottom:24px}
        .price-row{display:flex;justify-content:space-between;align-items:center;font-size:.88rem}
        .price-row .lbl{color:#64748b}
        .price-row .val{font-weight:700;color:#1e293b}
        .price-row.total{padding-top:12px;border-top:1px solid #f1f5f9}
        .price-row.total .val{color:#1B3358;font-size:1.05rem}
        .btn-apply{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:16px;border-radius:14px;font-weight:700;font-size:.95rem;text-decoration:none;transition:all .25s;border:none;cursor:pointer;font-family:'Inter',sans-serif}
        .btn-apply.primary{background:#1B3358;color:#fff}
        .btn-apply.primary:hover{background:#0F1B2D;box-shadow:0 8px 24px rgba(27,51,88,.2);transform:translateY(-2px)}
        .btn-apply.outline{background:#fff;color:#1B3358;border:2px solid #e2e8f0;margin-top:10px}
        .btn-apply.outline:hover{border-color:#1B3358;background:#f8fafc}

        .trust-card{background:#fff;border:1px solid #e2e8f0;border-radius:20px;padding:24px}
        .trust-item{display:flex;align-items:flex-start;gap:12px;padding:10px 0}
        .trust-item:not(:last-child){border-bottom:1px solid #f1f5f9}
        .trust-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0}
        .trust-icon.blue{background:#E8EDF4;color:#1B3358}
        .trust-icon.green{background:#E3F9EF;color:#0E9F6E}
        .trust-icon.orange{background:#FBF3E2;color:#C9962C}
        .trust-title{font-size:.85rem;font-weight:700}
        .trust-desc{font-size:.75rem;color:#64748b;margin-top:2px;line-height:1.5}

        .cta{background:linear-gradient(135deg,#1B3358 0%,#3E5C85 50%,#C9962C 100%);padding:clamp(48px,6vw,72px) clamp(20px,4vw,48px);text-align:center;color:#fff;position:relative;overflow:hidden}
        .cta::before{content:'';position:absolute;top:-100px;right:-60px;width:400px;height:400px;background:rgba(255,255,255,.04);border-radius:50%}
        .cta h2{font-size:clamp(1.5rem,3vw,2rem);font-weight:800;margin-bottom:10px;letter-spacing:-.3px;position:relative}
        .cta p{font-size:.95rem;opacity:.85;margin-bottom:28px;max-width:480px;margin-left:auto;margin-right:auto;position:relative}
        .cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;position:relative}
        .cta-btns a{padding:13px 28px;border-radius:12px;font-weight:700;font-size:.92rem;text-decoration:none;transition:all .25s;display:inline-flex;align-items:center;gap:8px}
        .cta-btns .btn-white{background:#fff;color:#1B3358;box-shadow:0 4px 20px rgba(0,0,0,.1)}
        .cta-btns .btn-white:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.15)}
        .cta-btns .btn-outline-w{border:2px solid rgba(255,255,255,.3);color:#fff}
        .cta-btns .btn-outline-w:hover{border-color:rgba(255,255,255,.6);background:rgba(255,255,255,.08)}

        .footer{background:#0F1B2D;color:#94a3b8;padding:clamp(40px,5vw,56px) clamp(20px,4vw,48px) clamp(24px,3vw,32px)}
        .footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:40px;max-width:1200px;margin:0 auto;padding-bottom:36px;border-bottom:1px solid #1e293b}
        .footer-brand{font-weight:800;font-size:1.15rem;color:#fff;display:flex;align-items:center;gap:8px;margin-bottom:14px}
        .footer-brand i{font-size:1.3rem}
        .footer-brand-col p{font-size:.82rem;line-height:1.65;max-width:280px}
        .footer-social{display:flex;gap:10px;margin-top:18px}
        .footer-social a{width:36px;height:36px;border-radius:10px;background:#1e293b;color:#94a3b8;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all .2s;font-size:.9rem}
        .footer-social a:hover{background:#1B3358;color:#fff}
        .footer-col h4{font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin-bottom:16px}
        .footer-col a{display:block;color:#94a3b8;text-decoration:none;font-size:.85rem;padding:5px 0;transition:color .2s}
        .footer-col a:hover{color:#fff}
        .footer-bottom{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding-top:28px;max-width:1200px;margin:0 auto;font-size:.78rem}

        @media(max-width:1024px){.detail-grid{grid-template-columns:1fr}.detail-sidebar{position:static}.footer-grid{grid-template-columns:1fr 1fr}}
        @media(max-width:640px){.nav-center{display:none}.hero-img{height:280px}.hero-img-overlay{flex-direction:column;align-items:flex-start}.specs-grid{grid-template-columns:1fr}.footer-grid{grid-template-columns:1fr}.hero-img-back{top:12px;left:16px}}

        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .detail-main{animation:fadeUp .5s ease both}
        .price-card{animation:fadeUp .5s ease .1s both}
        .trust-card{animation:fadeUp .5s ease .2s both}
        .spec-tile:nth-child(1){animation:fadeUp .4s ease .05s both}
        .spec-tile:nth-child(2){animation:fadeUp .4s ease .1s both}
        .spec-tile:nth-child(3){animation:fadeUp .4s ease .15s both}
        .spec-tile:nth-child(4){animation:fadeUp .4s ease .2s both}
    </style>
</head>
<body>

<nav class="nav">
    <a href="{{ url('/') }}" class="nav-brand"><i class="bi bi-motorcycle"></i> BodaLink</a>
    <div class="nav-center">
        <a href="{{ url('/') }}#how">How It Works</a>
        <a href="{{ url('/') }}#services">Services</a>
        <a href="{{ url('/') }}#browse">Browse</a>
    </div>
    <div class="nav-actions">
        @auth
            <a href="{{ route('dashboard') }}" class="btn-ghost"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit" class="btn-fill"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
        @else
            <a href="{{ route('login') }}" class="btn-ghost">Login</a>
            <a href="{{ route('register') }}" class="btn-fill">Register</a>
        @endauth
    </div>
</nav>

<div class="hero-img">
    @if($motorcycle->photo)
        <img src="{{ asset('storage/' . $motorcycle->photo) }}" alt="{{ $motorcycle->make }} {{ $motorcycle->model }}">
    @else
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#E8EDF4,#FBF5E8);color:#1B3358">
            <i class="bi bi-bicycle" style="font-size:5rem;opacity:.2"></i>
        </div>
    @endif
    <div class="hero-img-back">
        <a href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> Back to Marketplace</a>
    </div>
    <div class="hero-img-overlay">
        <div>
            <div class="hero-img-title">{{ $motorcycle->make }} {{ $motorcycle->model }}</div>
            <div class="hero-img-sub">{{ $motorcycle->plate_number }} &middot; {{ $motorcycle->color }} &middot; {{ $motorcycle->year }}</div>
        </div>
        <div class="hero-img-badge"><i class="bi bi-patch-check-fill"></i> Verified</div>
    </div>
</div>

<div class="detail-wrap">
    <div class="detail-grid">

        <div class="detail-main">
            <div class="detail-section">
                <div class="detail-section-title"><i class="bi bi-speedometer2"></i> Specifications</div>
                <div class="specs-grid">
                    <div class="spec-tile">
                        <div class="spec-icon blue"><i class="bi bi-speedometer2"></i></div>
                        <div><div class="spec-val">{{ $motorcycle->engine_cc }}cc</div><div class="spec-lbl">Engine Capacity</div></div>
                    </div>
                    <div class="spec-tile">
                        <div class="spec-icon green"><i class="bi bi-calendar-week"></i></div>
                        <div><div class="spec-val">{{ $motorcycle->loan_duration_weeks }} weeks</div><div class="spec-lbl">Loan Duration</div></div>
                    </div>
                    <div class="spec-tile">
                        <div class="spec-icon orange"><i class="bi bi-cash"></i></div>
                        <div><div class="spec-val">TZS {{ $motorcycle->weekly_amount ? number_format($motorcycle->weekly_amount) : '—' }}</div><div class="spec-lbl">Weekly Payment</div></div>
                    </div>
                    <div class="spec-tile">
                        <div class="spec-icon purple"><i class="bi bi-palette"></i></div>
                        <div><div class="spec-val">{{ $motorcycle->color }}</div><div class="spec-lbl">Color</div></div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <div class="detail-section-title"><i class="bi bi-info-circle"></i> About This Motorcycle</div>
                <div class="detail-desc">
                    This {{ $motorcycle->year }} <strong>{{ $motorcycle->make }} {{ $motorcycle->model }}</strong> ({{ $motorcycle->color }}) is available for hire-purchase through BodaLink.
                    Pay <strong>TZS {{ $motorcycle->weekly_amount ? number_format($motorcycle->weekly_amount) : '—' }}</strong> per week for
                    <strong>{{ $motorcycle->loan_duration_weeks }} weeks</strong> and own the motorcycle.
                    The total loan amount is <strong>TZS {{ number_format($motorcycle->loan_amount) }}</strong>.
                    All payments are tracked on the platform — cash, M-Pesa, or bank transfer accepted.
                </div>
            </div>

            @if($motorcycle->owner)
            <div class="detail-section">
                <div class="detail-section-title"><i class="bi bi-person"></i> Listed By</div>
                <div class="owner-card">
                    <div class="owner-avatar">{{ strtoupper(substr($motorcycle->owner->name, 0, 1)) }}</div>
                    <div>
                        <div class="owner-name">{{ $motorcycle->owner->name }}</div>
                        @if($motorcycle->owner->phone)
                            <div style="font-size:0.82rem;color:var(--text-secondary);margin-top:2px;"><i class="bi bi-telephone me-1"></i>{{ $motorcycle->owner->phone }}</div>
                        @endif
                        <div class="owner-label">Verified Owner</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="detail-sidebar">
            <div class="price-card">
                <div class="price-tag">
                    <div class="label">Loan Amount</div>
                    <div class="price">TZS {{ number_format($motorcycle->loan_amount) }}</div>
                    <div class="sub">{{ $motorcycle->loan_duration_weeks }} weeks &middot; TZS {{ $motorcycle->weekly_amount ? number_format($motorcycle->weekly_amount) : '—' }}/week</div>
                </div>
                <div class="price-breakdown">
                    <div class="price-row"><span class="lbl">Weekly Payment</span><span class="val">TZS {{ $motorcycle->weekly_amount ? number_format($motorcycle->weekly_amount) : '—' }}</span></div>
                    <div class="price-row"><span class="lbl">Duration</span><span class="val">{{ $motorcycle->loan_duration_weeks }} weeks</span></div>
                    <div class="price-row"><span class="lbl">Engine</span><span class="val">{{ $motorcycle->engine_cc }}cc</span></div>
                    <div class="price-row total"><span class="lbl">Total Loan</span><span class="val">TZS {{ number_format($motorcycle->loan_amount) }}</span></div>
                </div>
                @auth
                    @if(auth()->user()->isDriver())
                        <a href="{{ route('marketplace.apply', $motorcycle) }}" class="btn-apply primary"><i class="bi bi-hand-index-thumb"></i> Apply Now</a>
                    @endif
                    @if($motorcycle->owner && auth()->id() !== $motorcycle->owner_id)
                        <a href="{{ route('chat.start.direct', $motorcycle->owner_id) }}" class="btn-apply outline"><i class="bi bi-chat-dots"></i> Chat with Owner</a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn-apply primary"><i class="bi bi-box-arrow-in-right"></i> Login to Apply</a>
                @endauth
                <a href="{{ route('home') }}" class="btn-apply outline"><i class="bi bi-search"></i> Browse More Motorcycles</a>
            </div>

            <div class="trust-card">
                <div class="detail-section-title" style="margin-bottom:10px"><i class="bi bi-shield-check"></i> Why BodaLink?</div>
                <div class="trust-item">
                    <div class="trust-icon blue"><i class="bi bi-patch-check-fill"></i></div>
                    <div><div class="trust-title">Verified Motorcycle</div><div class="trust-desc">Inspected and approved by our team before listing</div></div>
                </div>
                <div class="trust-item">
                    <div class="trust-icon green"><i class="bi bi-cash-stack"></i></div>
                    <div><div class="trust-title">Flexible Payments</div><div class="trust-desc">Pay weekly via M-Pesa, cash, or bank transfer</div></div>
                </div>
                <div class="trust-item">
                    <div class="trust-icon orange"><i class="bi bi-file-earmark-text"></i></div>
                    <div><div class="trust-title">Transparent Contract</div><div class="trust-desc">Clear terms — own the bike when you finish paying</div></div>
                </div>
            </div>
        </div>

    </div>
</div>

<section class="cta">
    @auth
        <h2>Ready to Apply?</h2>
        <p>You're signed in. Apply for this motorcycle or continue browsing the marketplace.</p>
        <div class="cta-btns">
            <a href="{{ route('marketplace.apply', $motorcycle) }}" class="btn-white"><i class="bi bi-hand-index-thumb"></i> Apply Now</a>
            <a href="{{ route('home') }}" class="btn-outline-w"><i class="bi bi-search"></i> Browse More</a>
        </div>
    @else
        <h2>Ready to Start Earning?</h2>
        <p>Create a free account and apply for your boda-boda today. It only takes a few minutes.</p>
        <div class="cta-btns">
            <a href="{{ route('register') }}" class="btn-white"><i class="bi bi-person-plus"></i> Register</a>
            <a href="{{ route('login') }}" class="btn-outline-w"><i class="bi bi-box-arrow-in-right"></i> Login</a>
        </div>
    @endauth
</section>

<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand-col">
            <div class="footer-brand"><i class="bi bi-motorcycle"></i> BodaLink</div>
                    <p>Affordable boda-boda hire-purchase. Pay weekly, own the bike at the end. Built for Tanzania's riders.</p>
            <div class="footer-social">
                <a href="https://wa.me/255000000000" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                <a href="mailto:support@bodalink.co.tz" title="Email Us"><i class="bi bi-envelope-fill"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Platform</h4>
            <a href="{{ url('/') }}#how">How It Works</a>
            <a href="{{ url('/') }}#services">Services</a>
            <a href="{{ url('/') }}#browse">Browse Motorcycles</a>
        </div>
        <div class="footer-col">
            <h4>Account</h4>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('profile.edit') }}">Settings</a>
            @else
                <a href="{{ route('register') }}">Register</a>
                <a href="{{ route('login') }}">Login</a>
            @endauth
        </div>
        <div class="footer-col">
            <h4>Support</h4>
            @auth
                <a href="{{ route('chat.start.direct', \App\Models\User::where('role','admin')->first()?->id ?? 1) }}">Help Center</a>
                <a href="{{ route('chat.start.direct', \App\Models\User::where('role','admin')->first()?->id ?? 1) }}">Contact Us</a>
            @else
                <a href="{{ route('login') }}">Help Center</a>
                <a href="{{ route('login') }}">Contact Us</a>
            @endauth
            <a href="{{ url('/#how') }}">How It Works</a>
            <a href="{{ url('/#services') }}">Services</a>
        </div>
    </div>
    <div class="footer-bottom">
        <div>&copy; {{ date('Y') }} BodaLink. All rights reserved.</div>
        <div>Made with <i class="bi bi-heart-fill" style="color:#ef4444"></i> in Tanzania</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
