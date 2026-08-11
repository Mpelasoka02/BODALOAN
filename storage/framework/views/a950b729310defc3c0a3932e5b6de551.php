<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BodaLink — Own Your BodaBoda</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.svg')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{font-family:'Inter',sans-serif;color:#1e293b;background:#fff;-webkit-font-smoothing:antialiased}

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

        .hero{background:linear-gradient(135deg,#1B3358 0%,#3E5C85 40%,#C9962C 100%);padding:clamp(56px,9vw,110px) clamp(20px,4vw,48px) clamp(40px,6vw,80px);color:#fff;position:relative;overflow:hidden}
        .hero::before{content:'';position:absolute;top:-200px;right:-100px;width:600px;height:600px;background:radial-gradient(circle,rgba(255,255,255,.07) 0%,transparent 70%);border-radius:50%}
        .hero::after{content:'';position:absolute;bottom:-150px;left:5%;width:500px;height:500px;background:radial-gradient(circle,rgba(255,255,255,.04) 0%,transparent 70%);border-radius:50%}
        .hero-grid{position:relative;z-index:1;max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}
        .hero-text h1{font-size:clamp(2.2rem,5vw,3.5rem);font-weight:900;line-height:1.06;margin-bottom:20px;letter-spacing:-.8px}
        .hero-text h1 span{color:#C9962C}
        .hero-text p{font-size:clamp(0.95rem,1.4vw,1.1rem);opacity:.85;line-height:1.7;margin-bottom:36px;max-width:480px}
        .hero-btns{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:44px}
        .hero-btns a{padding:14px 28px;border-radius:12px;font-weight:700;font-size:0.92rem;text-decoration:none;transition:all .25s;display:inline-flex;align-items:center;gap:8px}
        .hero-btns .btn-white{background:#fff;color:#1B3358;box-shadow:0 4px 20px rgba(0,0,0,.1)}
        .hero-btns .btn-white:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.15)}
        .hero-btns .btn-outline{border:2px solid rgba(255,255,255,.3);color:#fff;background:transparent}
        .hero-btns .btn-outline:hover{border-color:rgba(255,255,255,.6);background:rgba(255,255,255,.08)}
        .hero-trust{display:flex;gap:32px;flex-wrap:wrap}
        .hero-trust span{font-size:.85rem;opacity:.9;display:flex;align-items:center;gap:8px}
        .hero-trust i{color:#C9962C}
        .hero-visual{display:flex;justify-content:center;align-items:center;position:relative}
        .hero-card{background:rgba(255,255,255,.1);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.15);border-radius:20px;padding:28px;width:100%;max-width:380px}
        .hero-card-title{font-size:.78rem;text-transform:uppercase;letter-spacing:1px;opacity:.7;margin-bottom:16px;font-weight:700}
        .hero-stat{display:flex;align-items:center;gap:16px;margin-bottom:20px}
        .hero-stat-icon{width:48px;height:48px;background:rgba(255,255,255,.15);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
        .hero-stat-num{font-size:1.6rem;font-weight:800;line-height:1}
        .hero-stat-label{font-size:.78rem;opacity:.7;margin-top:2px}
        .hero-mini{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:4px}
        .hero-mini-card{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:14px;text-align:center}
        .hero-mini-card .num{font-size:1.3rem;font-weight:800}
        .hero-mini-card .lbl{font-size:.7rem;opacity:.65;margin-top:2px}

        .stats{background:#fff;border-bottom:1px solid #e2e8f0;padding:0 clamp(20px,4vw,48px)}
        .stats-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:0}
        .stat{padding:24px 20px;text-align:center;border-right:1px solid #f1f5f9}
        .stat:last-child{border-right:none}
        .stat .num{font-size:1.5rem;font-weight:800;color:#1B3358}
        .stat .lbl{font-size:.78rem;color:#94a3b8;margin-top:4px;font-weight:500}

        .section{padding:clamp(48px,7vw,88px) clamp(20px,4vw,48px)}
        .section-label{text-align:center;font-size:.72rem;font-weight:800;color:#1B3358;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:10px}
        .section-title{text-align:center;font-size:clamp(1.6rem,3vw,2rem);font-weight:800;margin-bottom:10px;letter-spacing:-.3px}
        .section-sub{text-align:center;font-size:.95rem;color:#64748b;margin-bottom:clamp(36px,5vw,56px);max-width:520px;margin-left:auto;margin-right:auto;line-height:1.6}
        .section.bg-gray{background:#f8fafc}

        .how-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0;max-width:1000px;margin:0 auto;position:relative}
        .how-step{text-align:center;padding:32px 28px;position:relative}
        .how-step::after{content:'';position:absolute;top:52px;right:-16px;width:32px;height:2px;background:linear-gradient(90deg,#cbd5e1,#e2e8f0)}
        .how-step:last-child::after{display:none}
        .how-icon{width:68px;height:68px;border-radius:18px;display:inline-flex;align-items:center;justify-content:center;font-size:1.6rem;margin-bottom:20px;transition:transform .3s}
        .how-step:hover .how-icon{transform:scale(1.08)}
        .how-icon.blue{background:#E8EDF4;color:#1B3358}
        .how-icon.green{background:#E3F9EF;color:#0E9F6E}
        .how-icon.orange{background:#FBF3E2;color:#C9962C}
        .how-num{font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:14px}
        .how-step h3{font-size:1.05rem;font-weight:700;margin-bottom:8px}
        .how-step p{font-size:.85rem;color:#64748b;line-height:1.6}

        .services-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:1000px;margin:0 auto}
        .service-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:32px 28px;transition:all .3s;position:relative;overflow:hidden}
        .service-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;transform:scaleX(0);transition:transform .3s;transform-origin:left}
        .service-card:nth-child(1)::before{background:#1B3358}
        .service-card:nth-child(2)::before{background:#0E9F6E}
        .service-card:nth-child(3)::before{background:#3E5C85}
        .service-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.06);border-color:transparent}
        .service-card:hover::before{transform:scaleX(1)}
        .service-icon{width:52px;height:52px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:18px}
        .service-icon.blue{background:#E8EDF4;color:#1B3358}
        .service-icon.green{background:#E3F9EF;color:#0E9F6E}
        .service-icon.purple{background:#E8EDF4;color:#3E5C85}
        .service-card h3{font-size:1.05rem;font-weight:700;margin-bottom:8px}
        .service-card p{font-size:.85rem;color:#64748b;line-height:1.65}

        .listings-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:12px}
        .listings-title{font-size:1.4rem;font-weight:800}

        .filters{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:28px}
        .pill{padding:8px 18px;border-radius:20px;border:1.5px solid #e2e8f0;background:#fff;color:#475569;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none}
        .pill:hover{border-color:#1B3358;color:#1B3358}
        .pill.active{background:#1B3358;color:#fff;border-color:#1B3358;box-shadow:0 2px 8px rgba(27,51,88,.2)}
        .sort-select{padding:8px 14px;border-radius:10px;border:1.5px solid #e2e8f0;background:#fff;font-size:.82rem;color:#1e293b;font-weight:500;outline:none;margin-left:auto;font-family:'Inter',sans-serif}
        .results-text{font-size:.85rem;color:#64748b;margin-left:8px}
        .results-text strong{color:#1e293b}

        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
        .card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;transition:all .35s cubic-bezier(.4,0,.2,1);cursor:pointer;text-decoration:none;color:inherit;display:block;position:relative}
        .card::after{content:'';position:absolute;inset:0;border-radius:16px;opacity:0;transition:opacity .35s;box-shadow:0 20px 50px rgba(27,51,88,.1);pointer-events:none}
        .card:hover{transform:translateY(-6px);border-color:#C9962C}
        .card:hover::after{opacity:1}
        .card-img{position:relative;width:100%;padding-top:60%;background:#f1f5f9;overflow:hidden}
        .card-img::after{content:'';position:absolute;inset:0;background:linear-gradient(0deg,rgba(0,0,0,.05) 0%,transparent 40%);z-index:1;pointer-events:none}
        .card-img img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .5s cubic-bezier(.4,0,.2,1)}
        .card:hover .card-img img{transform:scale(1.08)}
        .card-badge{position:absolute;top:12px;right:12px;background:rgba(22,163,74,.92);color:#fff;padding:5px 12px;border-radius:8px;font-size:.7rem;font-weight:700;backdrop-filter:blur(4px);z-index:2;display:flex;align-items:center;gap:4px}
        .card-cc{position:absolute;top:12px;left:12px;background:rgba(0,0,0,.55);color:#fff;padding:4px 10px;border-radius:8px;font-size:.7rem;font-weight:700;backdrop-filter:blur(4px);z-index:2}
        .card-body{padding:18px 20px}
        .card-name{font-size:1.05rem;font-weight:700;margin-bottom:4px;transition:color .2s}
        .card:hover .card-name{color:#1B3358}
        .card-detail{font-size:.8rem;color:#94a3b8;margin-bottom:12px}
        .card-price{display:flex;align-items:baseline;gap:5px;margin-bottom:14px}
        .card-price .currency{font-size:.78rem;color:#1B3358;font-weight:600}
        .card-price .amount{font-size:1.35rem;font-weight:800;color:#1B3358}
        .card-specs{display:flex;gap:16px;padding-top:12px;border-top:1px solid #f1f5f9}
        .card-spec{display:flex;align-items:center;gap:5px;font-size:.78rem;color:#64748b}
        .card-spec i{color:#94a3b8;font-size:.8rem}

        .pager{display:flex;justify-content:center;margin-top:36px}

        .cta-section{background:linear-gradient(135deg,#1B3358 0%,#3E5C85 50%,#C9962C 100%);padding:clamp(56px,7vw,88px) clamp(20px,4vw,48px);text-align:center;color:#fff;position:relative;overflow:hidden}
        .cta-section::before{content:'';position:absolute;top:-100px;right:-60px;width:400px;height:400px;background:rgba(255,255,255,.04);border-radius:50%}
        .cta-section h2{font-size:clamp(1.6rem,3vw,2.2rem);font-weight:800;margin-bottom:12px;letter-spacing:-.3px;position:relative}
        .cta-section p{font-size:1rem;opacity:.85;margin-bottom:32px;max-width:480px;margin-left:auto;margin-right:auto;position:relative}
        .cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;position:relative}
        .cta-btns a{padding:14px 32px;border-radius:12px;font-weight:700;font-size:.92rem;text-decoration:none;transition:all .25s;display:inline-flex;align-items:center;gap:8px}
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

        @media(max-width:1024px){.hero-grid{grid-template-columns:1fr}.hero-visual{display:none}.stats-inner{grid-template-columns:repeat(2,1fr)}.stat:nth-child(2){border-right:none}.footer-grid{grid-template-columns:1fr 1fr}}
        @media(max-width:768px){.grid{grid-template-columns:repeat(2,1fr)}.how-step::after{display:none}}
        @media(max-width:640px){.nav-center{display:none}.grid{grid-template-columns:1fr}.how-grid,.services-grid{grid-template-columns:1fr}.hero-btns{flex-direction:column}.hero-btns a{text-align:center;justify-content:center}.stats-inner{grid-template-columns:1fr}.stat{border-right:none;border-bottom:1px solid #f1f5f9}.stat:last-child{border-bottom:none}.footer-grid{grid-template-columns:1fr}}

        @keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
        .card{animation:fadeUp .5s ease both}
        .grid .card:nth-child(1){animation-delay:0s}.grid .card:nth-child(2){animation-delay:.06s}.grid .card:nth-child(3){animation-delay:.12s}
        .grid .card:nth-child(4){animation-delay:.18s}.grid .card:nth-child(5){animation-delay:.24s}.grid .card:nth-child(6){animation-delay:.30s}
    </style>
</head>
<body>

<nav class="nav">
    <a href="<?php echo e(url('/')); ?>" class="nav-brand"><i class="bi bi-motorcycle"></i> BodaLink</a>
    <div class="nav-center">
        <a href="#browse">Browse</a>
    </div>
    <div class="nav-actions">
        <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('dashboard')); ?>" class="btn-ghost"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline"><?php echo csrf_field(); ?><button type="submit" class="btn-fill"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
        <?php else: ?>
            <a href="<?php echo e(route('login')); ?>" class="btn-ghost">Login</a>
            <a href="<?php echo e(route('register')); ?>" class="btn-fill">Register</a>
        <?php endif; ?>
    </div>
</nav>

<section class="hero" id="top">
    <div class="hero-grid">
        <div class="hero-text">
            <h1>Own Your BodaBoda.<br>Start <span>Earning</span> Today.</h1>
            <p>Get verified motorcycles through flexible hire-purchase plans. Low weekly payments, full ownership at the end. Built for Tanzania's boda-boda riders.</p>
        </div>
        <div class="hero-visual">
            <div class="hero-card">
                <div class="hero-card-title">Platform Overview</div>
                <div class="hero-stat">
                    <div class="hero-stat-icon"><i class="bi bi-motorcycle"></i></div>
                    <div>
                        <div class="hero-stat-num"><?php echo e(\App\Models\Motorcycle::where('verification_status','verified')->where('status','available')->count()); ?>+</div>
                        <div class="hero-stat-label">Verified BodaBodas</div>
                    </div>
                </div>
                <div class="hero-mini">
                    <div class="hero-mini-card">
                        <div class="num"><?php echo e(\App\Models\User::where('role','driver')->count()); ?>+</div>
                        <div class="lbl">Active Drivers</div>
                    </div>
                    <div class="hero-mini-card">
                        <div class="num"><?php echo e(\App\Models\Loan::where('status','active')->count()); ?>+</div>
                        <div class="lbl">Active Loans</div>
                    </div>
                </div>
            </div>
    </div>
</div>
</section>

<section class="section bg-gray" id="browse">
    <div class="section-label">Marketplace</div>
    <h2 class="section-title">Available BodaBodas</h2>
    <p class="section-sub">Browse verified boda bodas ready for hire-purchase</p>

    <div class="filters">
        <a href="<?php echo e(route('home')); ?>" class="pill <?php echo e(!request('min_price') && !request('max_price') ? 'active' : ''); ?>">All</a>
        <a href="<?php echo e(route('home', array_merge(request()->query(), ['max_price' => 500000]))); ?>" class="pill <?php echo e(request('max_price') == 500000 ? 'active' : ''); ?>">Under 500K</a>
        <a href="<?php echo e(route('home', array_merge(request()->query(), ['min_price' => 500000, 'max_price' => 1500000]))); ?>" class="pill <?php echo e(request('min_price') == 500000 ? 'active' : ''); ?>">500K – 1.5M</a>
        <a href="<?php echo e(route('home', array_merge(request()->query(), ['min_price' => 1500000, 'max_price' => 2500000]))); ?>" class="pill <?php echo e(request('min_price') == 1500000 ? 'active' : ''); ?>">1.5M – 2.5M</a>
        <a href="<?php echo e(route('home', array_merge(request()->query(), ['min_price' => 2500000]))); ?>" class="pill <?php echo e(request('min_price') == 2500000 ? 'active' : ''); ?>">2.5M+</a>
        <span class="results-text"><strong><?php echo e($motorcycles->total()); ?></strong> boda bodas</span>
        <select class="sort-select" onchange="window.location.href=this.value">
            <option value="<?php echo e(route('home', array_merge(request()->query(), ['sort' => 'newest']))); ?>" <?php echo e(request('sort', 'newest') == 'newest' ? 'selected' : ''); ?>>Newest</option>
            <option value="<?php echo e(route('home', array_merge(request()->query(), ['sort' => 'price_low']))); ?>" <?php echo e(request('sort') == 'price_low' ? 'selected' : ''); ?>>Price: Low to High</option>
            <option value="<?php echo e(route('home', array_merge(request()->query(), ['sort' => 'price_high']))); ?>" <?php echo e(request('sort') == 'price_high' ? 'selected' : ''); ?>>Price: High to Low</option>
        </select>
    </div>

    <?php if($motorcycles->count() > 0): ?>
        <div class="grid">
            <?php $__currentLoopData = $motorcycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('marketplace.show', $m)); ?>" class="card">
                    <div class="card-img">
                        <?php if($m->photo): ?>
                            <img src="<?php echo e(asset('storage/' . $m->photo)); ?>" alt="<?php echo e($m->make); ?> <?php echo e($m->model); ?>" loading="lazy">
                        <?php else: ?>
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#E8EDF4,#e0e7ff);color:#3b82f6">
                                <i class="bi bi-bicycle" style="font-size:3rem;opacity:.25"></i>
                            </div>
                        <?php endif; ?>
                        <?php if($m->engine_cc): ?><div class="card-cc"><?php echo e($m->engine_cc); ?>cc</div><?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="card-name"><?php echo e($m->make); ?> <?php echo e($m->model); ?></div>
                        <div class="card-detail"><?php echo e($m->plate_number); ?></div>
                        <?php if($m->loan_amount): ?>
                            <div class="card-price">
                                <span class="currency">TZS</span>
                                <span class="amount"><?php echo e(number_format($m->loan_amount)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="card-specs">
                            <div class="card-spec"><i class="bi bi-calendar-week"></i> <?php echo e($m->loan_duration_weeks); ?> weeks</div>
                            <div class="card-spec"><i class="bi bi-cash"></i> TZS <?php echo e($m->weekly_amount ? number_format($m->weekly_amount) : '—'); ?>/wk</div>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="pager"><?php echo e($motorcycles->withQueryString()->links()); ?></div>
    <?php else: ?>
        <div style="text-align:center;padding:64px 20px">
            <div style="width:80px;height:80px;border-radius:50%;background:#f1f5f9;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;color:#94a3b8;margin-bottom:16px"><i class="bi bi-search"></i></div>
            <h5 style="font-weight:700;margin-bottom:6px">No boda bodas found</h5>
            <p style="font-size:.88rem;color:#64748b;margin-bottom:20px">Check back later — new boda bodas are added daily.</p>
            <a href="<?php echo e(route('home')); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:10px 22px;border-radius:10px;background:#1B3358;color:#fff;text-decoration:none;font-weight:600;font-size:.88rem;transition:all .2s">Clear Filters</a>
        </div>
    <?php endif; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/marketplace/index.blade.php ENDPATH**/ ?>