<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BodaLink — Hire-Purchase Financing for Boda Riders</title>
    <meta name="description" content="Pick a bodaboda, apply for hire-purchase, and start riding. Pay weekly until it's yours. No bank loans needed.">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.svg')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --navy-900: #0F1B2D;
            --navy-800: #15253C;
            --navy-700: #1B3358;
            --navy-600: #2A4A72;
            --navy-400: #3E5C85;
            --navy-200: #7A96BD;
            --navy-50: #E8EDF4;
            --gold-600: #B8872A;
            --gold-500: #C9962C;
            --gold-400: #D4A83E;
            --gold-100: #FBF3E2;
            --gold-50: #FEF9F0;
            --emerald-600: #0E9F6E;
            --emerald-100: #E3F9EF;
            --page-bg: #F5F7FA;
            --card-bg: #FFFFFF;
            --border: #E2E5EA;
            --border-light: #F0F2F5;
            --text: #1A2233;
            --text-secondary: #6B7684;
            --text-muted: #9CA3AF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: var(--text); background: #fff; -webkit-font-smoothing: antialiased; overflow-x: hidden; }
        .container { max-width: 1120px; margin: 0 auto; padding: 0 32px; }

        /* ── NAV ── */
        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; background: rgba(255,255,255,0.92); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid var(--border-light); height: 64px; display: flex; align-items: center; }
        .nav .container { display: flex; align-items: center; justify-content: space-between; }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-links { display: flex; align-items: center; gap: 2px; }
        .nav-links a { text-decoration: none; font-size: 0.88rem; font-weight: 500; color: var(--text-secondary); padding: 8px 14px; border-radius: 8px; transition: all 0.15s; }
        .nav-links a:hover { color: var(--text); background: var(--page-bg); }
        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .nav-actions a { text-decoration: none; font-size: 0.88rem; font-weight: 600; padding: 8px 18px; border-radius: 8px; transition: all 0.15s; }
        .btn-ghost { color: var(--text); }
        .btn-ghost:hover { background: var(--page-bg); }
        .btn-navy { background: var(--navy-900); color: #fff; }
        .btn-navy:hover { background: var(--navy-700); }
        .hamburger { display: none; background: none; border: none; font-size: 1.5rem; color: var(--navy-900); cursor: pointer; padding: 4px; }

        /* ── HERO ── */
        .hero { padding: 140px 0 80px; background: linear-gradient(170deg, #0F1B2D 0%, #1B3358 100%); position: relative; overflow: hidden; }
        .hero::before { content: ''; position: absolute; top: -200px; right: -150px; width: 600px; height: 600px; background: radial-gradient(circle, rgba(201,150,44,0.1) 0%, transparent 70%); pointer-events: none; }
        .hero::after { content: ''; position: absolute; bottom: -150px; left: -100px; width: 400px; height: 400px; background: radial-gradient(circle, rgba(62,92,133,0.2) 0%, transparent 70%); pointer-events: none; }
        .hero-inner { position: relative; z-index: 1; max-width: 720px; margin: 0 auto; text-align: center; }
        .hero-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(201,150,44,0.15); color: var(--gold-400); font-size: 0.78rem; font-weight: 600; padding: 6px 16px; border-radius: 100px; margin-bottom: 28px; border: 1px solid rgba(201,150,44,0.2); }
        .hero h1 { font-size: clamp(2.4rem, 5.5vw, 3.8rem); font-weight: 900; line-height: 1.06; color: #fff; letter-spacing: -0.04em; margin-bottom: 20px; }
        .hero h1 em { font-style: normal; color: var(--gold-400); }
        .hero-sub { font-size: 1.1rem; color: rgba(255,255,255,0.55); line-height: 1.7; margin-bottom: 40px; max-width: 520px; margin-left: auto; margin-right: auto; }
        .hero-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; background: var(--gold-500); color: #fff; padding: 14px 28px; border-radius: 12px; font-size: 0.95rem; font-weight: 700; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; font-family: 'Inter', sans-serif; }
        .btn-primary:hover { background: var(--gold-600); box-shadow: 0 8px 30px rgba(201,150,44,0.3); transform: translateY(-1px); }
        .btn-outline { display: inline-flex; align-items: center; gap: 8px; background: transparent; color: #fff; padding: 14px 28px; border-radius: 12px; font-size: 0.95rem; font-weight: 700; text-decoration: none; border: 1.5px solid rgba(255,255,255,0.2); transition: all 0.2s; }
        .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }

        /* ── SECTIONS ── */
        .section { padding: 96px 0; }
        .section-alt { background: var(--page-bg); }
        .section-header { text-align: center; margin-bottom: 56px; }
        .section-tag { display: inline-flex; align-items: center; gap: 5px; font-size: 0.72rem; font-weight: 700; color: var(--gold-500); text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 14px; }
        .section-tag::before { content: ''; width: 16px; height: 2px; background: var(--gold-500); border-radius: 2px; }
        .section-header h2 { font-size: 2.2rem; font-weight: 800; color: var(--navy-900); margin-bottom: 12px; letter-spacing: -0.025em; }
        .section-header p { font-size: 1rem; color: var(--text-secondary); max-width: 500px; margin: 0 auto; line-height: 1.6; }

        /* ── STEPS ── */
        .steps-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; max-width: 960px; margin: 0 auto; position: relative; }
        .steps-row::before { content: ''; position: absolute; top: 36px; left: 16.6%; right: 16.6%; height: 2px; background: var(--border-light); z-index: 0; }
        .step-card { text-align: center; padding: 32px 24px; position: relative; z-index: 1; }
        .step-dot { width: 72px; height: 72px; background: #fff; border: 2px solid var(--border-light); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px; transition: all 0.3s; }
        .step-card:hover .step-dot { border-color: var(--gold-500); background: var(--gold-50); }
        .step-dot i { font-size: 1.5rem; color: var(--navy-700); transition: color 0.3s; }
        .step-card:hover .step-dot i { color: var(--gold-500); }
        .step-card h3 { font-size: 1.05rem; font-weight: 700; color: var(--navy-900); margin-bottom: 8px; }
        .step-card p { font-size: 0.88rem; color: var(--text-secondary); line-height: 1.55; max-width: 260px; margin: 0 auto; }

        /* ── FEATURES ── */
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; max-width: 1040px; margin: 0 auto; }
        .feature-card { background: #fff; border: 1px solid var(--border-light); border-radius: 16px; padding: 32px 28px; transition: all 0.3s; position: relative; overflow: hidden; }
        .feature-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; transform: scaleX(0); transition: transform 0.3s; transform-origin: left; }
        .feature-card:nth-child(1)::before, .feature-card:nth-child(4)::before { background: var(--navy-700); }
        .feature-card:nth-child(2)::before, .feature-card:nth-child(5)::before { background: var(--gold-500); }
        .feature-card:nth-child(3)::before, .feature-card:nth-child(6)::before { background: var(--emerald-600); }
        .feature-card:hover { border-color: transparent; box-shadow: 0 12px 40px rgba(0,0,0,0.06); transform: translateY(-3px); }
        .feature-card:hover::before { transform: scaleX(1); }
        .feature-icon { width: 48px; height: 48px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.15rem; margin-bottom: 18px; }
        .feature-icon.gold { background: var(--gold-100); color: var(--gold-500); }
        .feature-icon.navy { background: var(--navy-50); color: var(--navy-700); }
        .feature-icon.green { background: var(--emerald-100); color: var(--emerald-600); }
        .feature-card h3 { font-size: 1rem; font-weight: 700; color: var(--navy-900); margin-bottom: 8px; }
        .feature-card p { font-size: 0.86rem; color: var(--text-secondary); line-height: 1.6; }

        /* ── SHOWCASE ── */
        .showcase { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
        .showcase-content .section-tag { justify-content: flex-start; }
        .showcase-content h2 { font-size: 2rem; font-weight: 800; color: var(--navy-900); margin-bottom: 16px; letter-spacing: -0.02em; }
        .showcase-content > p { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.65; margin-bottom: 28px; }
        .showcase-list { list-style: none; display: flex; flex-direction: column; gap: 14px; margin-bottom: 32px; }
        .showcase-list li { display: flex; align-items: flex-start; gap: 12px; font-size: 0.9rem; color: var(--text); line-height: 1.5; }
        .showcase-list li i { color: var(--emerald-600); font-size: 1rem; margin-top: 2px; flex-shrink: 0; }
        .showcase-visual { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .sv-card { background: #fff; border: 1px solid var(--border-light); border-radius: 16px; padding: 24px 20px; text-align: center; transition: all 0.3s; }
        .sv-card:hover { border-color: var(--border); box-shadow: 0 8px 32px rgba(0,0,0,0.04); transform: translateY(-2px); }
        .sv-icon { width: 48px; height: 48px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 14px; }
        .sv-card h4 { font-size: 0.9rem; font-weight: 700; color: var(--navy-900); margin-bottom: 4px; }
        .sv-card p { font-size: 0.78rem; color: var(--text-secondary); line-height: 1.4; }
        .showcase-alt .showcase-visual { order: -1; }

        /* ── FAQ ── */
        .faq-grid { max-width: 700px; margin: 0 auto; }
        .faq-item { border-bottom: 1px solid var(--border-light); }
        .faq-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 22px 0; background: none; border: none; text-align: left; cursor: pointer; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 600; color: var(--navy-900); transition: color 0.15s; }
        .faq-btn:hover { color: var(--gold-500); }
        .faq-btn i { transition: transform 0.25s; color: var(--text-muted); font-size: 0.85rem; flex-shrink: 0; margin-left: 16px; }
        .faq-item.open .faq-btn i { transform: rotate(180deg); color: var(--gold-500); }
        .faq-body { display: none; padding: 0 0 22px; font-size: 0.88rem; color: var(--text-secondary); line-height: 1.7; }
        .faq-item.open .faq-body { display: block; }

        /* ── CTA ── */
        .cta { background: var(--navy-900); padding: 88px 0; text-align: center; position: relative; overflow: hidden; }
        .cta::before { content: ''; position: absolute; top: -120px; right: -100px; width: 360px; height: 360px; background: rgba(201,150,44,0.06); border-radius: 50%; pointer-events: none; }
        .cta-inner { position: relative; z-index: 1; }
        .cta-inner h2 { font-size: 2.2rem; font-weight: 800; color: #fff; margin-bottom: 12px; letter-spacing: -0.02em; }
        .cta-inner p { font-size: 1rem; color: rgba(255,255,255,0.5); margin-bottom: 36px; }

        /* ── FOOTER ── */
        .footer { background: var(--navy-900); border-top: 1px solid rgba(255,255,255,0.05); padding: 56px 0 32px; color: rgba(255,255,255,0.5); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand p { font-size: 0.85rem; line-height: 1.6; margin-top: 12px; max-width: 280px; }
        .footer h4 { font-size: 0.78rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 16px; }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { font-size: 0.85rem; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.15s; }
        .footer-links a:hover { color: #fff; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.06); padding-top: 24px; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; }
        .footer-socials { display: flex; gap: 16px; }
        .footer-socials a { color: rgba(255,255,255,0.3); text-decoration: none; font-size: 1rem; transition: color 0.15s; }
        .footer-socials a:hover { color: #fff; }

        /* ── RESPONSIVE ── */
        @media (max-width: 960px) {
            .showcase { grid-template-columns: 1fr; gap: 48px; }
            .showcase-alt .showcase-visual { order: 0; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
            .steps-row::before { display: none; }
        }
        @media (max-width: 768px) {
            .container { padding: 0 20px; }
            .nav-links { display: none; }
            .nav-links.open { display: flex; flex-direction: column; position: absolute; top: 64px; left: 0; right: 0; background: #fff; border-bottom: 1px solid var(--border); padding: 12px 20px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); z-index: 50; }
            .nav-links.open a { width: 100%; padding: 12px; text-align: center; }
            .hamburger { display: block; }
            .hero { padding: 120px 0 64px; }
            .hero h1 { font-size: 2.2rem; }
            .section { padding: 64px 0; }
            .section-header h2 { font-size: 1.6rem; }
            .steps-row, .features-grid { grid-template-columns: 1fr; max-width: 400px; margin: 0 auto; }
            .cta { padding: 56px 0; }
            .cta-inner h2 { font-size: 1.6rem; }
            .footer-grid { grid-template-columns: 1fr; gap: 28px; }
            .footer-bottom { flex-direction: column; gap: 12px; text-align: center; }
        }
        @media (max-width: 480px) {
            .hero h1 { font-size: 1.8rem; }
            .hero-btns { flex-direction: column; }
            .btn-primary, .btn-outline { width: 100%; justify-content: center; }
        }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .step-card, .feature-card, .sv-card { animation: fadeUp 0.5s ease both; }
        .step-card:nth-child(1) { animation-delay: 0.05s; }
        .step-card:nth-child(2) { animation-delay: 0.12s; }
        .step-card:nth-child(3) { animation-delay: 0.19s; }
        .feature-card:nth-child(1) { animation-delay: 0.05s; }
        .feature-card:nth-child(2) { animation-delay: 0.1s; }
        .feature-card:nth-child(3) { animation-delay: 0.15s; }
        .feature-card:nth-child(4) { animation-delay: 0.2s; }
        .feature-card:nth-child(5) { animation-delay: 0.25s; }
        .feature-card:nth-child(6) { animation-delay: 0.3s; }
        .sv-card:nth-child(1) { animation-delay: 0.05s; }
        .sv-card:nth-child(2) { animation-delay: 0.1s; }
        .sv-card:nth-child(3) { animation-delay: 0.15s; }
        .sv-card:nth-child(4) { animation-delay: 0.2s; }
    </style>
</head>
<body>

    <nav class="nav">
        <div class="container">
            <a href="/" class="nav-brand">
                <span style="font-size:1.15rem;font-weight:800;color:var(--navy-900);letter-spacing:-.02em;">BodaLink</span>
            </a>
            <button class="hamburger" onclick="document.querySelector('.nav-links').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>
            <div class="nav-links">
                <a href="#how">How It Works</a>
                <a href="#features">Features</a>
                <a href="#drivers">For Drivers</a>
                <a href="#owners">For Owners</a>
                <a href="#faq">FAQ</a>
            </div>
            <div class="nav-actions">
                <a href="<?php echo e(route('login')); ?>" class="btn-ghost">Login</a>
                <a href="<?php echo e(route('register')); ?>" class="btn-navy">Register</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="hero-inner">
                <h1>Own your <em>bodaboda</em><br>without the upfront cost</h1>
                <p class="hero-sub">Pick a verified bodaboda, apply in minutes, and start earning. Pay weekly until it's yours. No bank loans required.</p>
                <div class="hero-btns">
                    <a href="<?php echo e(route('register')); ?>" class="btn-primary"><i class="bi bi-person-plus"></i> Register Now</a>
                    <a href="<?php echo e(route('login')); ?>" class="btn-outline"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-alt" id="how">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">How It Works</div>
                <h2>Three steps to ownership</h2>
                <p>No banks, no brokers. Just a simple process from application to ownership.</p>
            </div>
            <div class="steps-row">
                <div class="step-card">
                    <div class="step-dot"><i class="bi bi-search"></i></div>
                    <h3>Browse & Apply</h3>
                    <p>Explore verified bodabodas from trusted owners. Choose one and submit your application in minutes.</p>
                </div>
                <div class="step-card">
                    <div class="step-dot"><i class="bi bi-clipboard-check"></i></div>
                    <h3>Get Approved</h3>
                    <p>The vehicle owner reviews your application. Sign the digital hire-purchase contract and get assigned the bodaboda.</p>
                </div>
                <div class="step-card">
                    <div class="step-dot"><i class="bi bi-trophy"></i></div>
                    <h3>Pay Weekly & Own</h3>
                    <p>Make affordable weekly payments through the platform. Once fully paid, ownership transfers to you.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="features">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">Features</div>
                <h2>Everything you need in one place</h2>
                <p>Simple tools for riders, owners, and the platform.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon navy"><i class="bi bi-shield-check"></i></div>
                     <h3>Verified Bodabodas</h3>
                    <p>Every bodaboda is inspected and verified by our admin team before it goes live on the platform.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon gold"><i class="bi bi-file-earmark-text"></i></div>
                    <h3>Digital Contracts</h3>
                    <p>Hire-purchase agreements you can access online. What you sign is what you get.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green"><i class="bi bi-graph-up-arrow"></i></div>
                    <h3>Payment Tracking</h3>
                    <p>Real-time payment history and progress charts. Both rider and owner see the same data.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon navy"><i class="bi bi-geo-alt"></i></div>
                    <h3>GPS Location</h3>
                    <p>Bodaboda location tracking for added security. Know where your asset is at all times.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon gold"><i class="bi bi-wallet2"></i></div>
                    <h3>Flexible Payments</h3>
                    <p>Pay via M-Pesa, Tigo Pesa, Airtel Money, HaloPesa, cash, or bank transfer. Choose what works for you.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green"><i class="bi bi-award"></i></div>
                    <h3>Ownership Certificate</h3>
                    <p>Once you've paid in full, you get a certificate. The bodaboda is yours.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-alt" id="drivers">
        <div class="container">
            <div class="showcase">
                <div class="showcase-content">
                    <div class="section-tag">For Drivers</div>
                    <h2>Ride now, pay later</h2>
                    <p>Pick a bodaboda you like, apply, and start riding. You pay weekly from your earnings — no big deposit required.</p>
                    <ul class="showcase-list">
                        <li><i class="bi bi-check-circle-fill"></i> Choose from verified bodabodas listed by owners</li>
                        <li><i class="bi bi-check-circle-fill"></i> Apply online — hear back within 48 hours</li>
                        <li><i class="bi bi-check-circle-fill"></i> See all your payments in one place</li>
                        <li><i class="bi bi-check-circle-fill"></i> Get a certificate when you're done paying</li>
                        <li><i class="bi bi-check-circle-fill"></i> Pay via M-Pesa, cash, bank, or mobile money</li>
                    </ul>
                    <a href="<?php echo e(route('register')); ?>" class="btn-primary"><i class="bi bi-bicycle"></i> Register as Driver</a>
                </div>
                <div class="showcase-visual">
                    <div class="sv-card">
                        <div class="sv-icon" style="background:var(--gold-100);color:var(--gold-500);"><i class="bi bi-search"></i></div>
                        <h4>Browse</h4>
                        <p>Find the right bodaboda for you</p>
                    </div>
                    <div class="sv-card">
                        <div class="sv-icon" style="background:var(--navy-50);color:var(--navy-700);"><i class="bi bi-file-earmark-check"></i></div>
                        <h4>Apply</h4>
                        <p>Submit your application online</p>
                    </div>
                    <div class="sv-card">
                        <div class="sv-icon" style="background:var(--emerald-100);color:var(--emerald-600);"><i class="bi bi-cash-stack"></i></div>
                        <h4>Pay Weekly</h4>
                        <p>Affordable weekly installments</p>
                    </div>
                    <div class="sv-card">
                        <div class="sv-icon" style="background:var(--gold-100);color:var(--gold-500);"><i class="bi bi-award"></i></div>
                        <h4>Own It</h4>
                        <p>Full ownership when paid off</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="owners">
        <div class="container">
            <div class="showcase showcase-alt">
                <div class="showcase-content">
                    <div class="section-tag">For Owners</div>
                    <h2>List your bodabodas, get paid weekly</h2>
                    <p>Add your bodabodas to the platform, pick a verified driver, and collect payments every week until the contract ends.</p>
                    <ul class="showcase-list">
                        <li><i class="bi bi-check-circle-fill"></i> Add multiple bodabodas with photos and details</li>
                        <li><i class="bi bi-check-circle-fill"></i> Approve or reject driver applications</li>
                        <li><i class="bi bi-check-circle-fill"></i> Track all payments from one dashboard</li>
                        <li><i class="bi bi-check-circle-fill"></i> GPS tracking on every assigned bodaboda</li>
                        <li><i class="bi bi-check-circle-fill"></i> Only admin-verified drivers can apply</li>
                    </ul>
                    <a href="<?php echo e(route('register')); ?>" class="btn-primary"><i class="bi bi-building"></i> Register as Owner</a>
                </div>
                <div class="showcase-visual">
                    <div class="sv-card">
                        <div class="sv-icon" style="background:var(--navy-50);color:var(--navy-700);"><i class="bi bi-plus-circle"></i></div>
                        <h4>List Bodaboda</h4>
                        <p>Add your bodaboda in minutes</p>
                    </div>
                    <div class="sv-card">
                        <div class="sv-icon" style="background:var(--gold-100);color:var(--gold-500);"><i class="bi bi-person-check"></i></div>
                        <h4>Approve Driver</h4>
                        <p>Review and pick your driver</p>
                    </div>
                    <div class="sv-card">
                        <div class="sv-icon" style="background:var(--emerald-100);color:var(--emerald-600);"><i class="bi bi-wallet2"></i></div>
                        <h4>Get Paid</h4>
                        <p>Receive weekly payments</p>
                    </div>
                    <div class="sv-card">
                        <div class="sv-icon" style="background:var(--navy-50);color:var(--navy-700);"><i class="bi bi-geo-alt"></i></div>
                        <h4>Track Bodaboda</h4>
                        <p>GPS on every assignment</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-alt" id="faq">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">FAQ</div>
                <h2>Frequently asked questions</h2>
            </div>
            <div class="faq-grid">
                <div class="faq-item">
                    <button class="faq-btn" onclick="this.parentElement.classList.toggle('open')">
                        What is BodaLink?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-body">BodaLink is a platform that matches bodaboda riders with bodaboda owners. You pick a bodaboda, sign a hire-purchase contract, pay weekly installments, and own the bodaboda once you've paid it off.</div>
                </div>
                <div class="faq-item">
                    <button class="faq-btn" onclick="this.parentElement.classList.toggle('open')">
                        How do I register as a driver?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-body">Click "Register" and select "Driver". Fill in your details, verify your email, and once approved by our admin team you can start browsing and applying for bodabodas.</div>
                </div>
                <div class="faq-item">
                    <button class="faq-btn" onclick="this.parentElement.classList.toggle('open')">
                        How do owners get paid?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-body">Owners receive weekly payments from their assigned drivers through the platform. All payments are tracked in real time and visible in the owner dashboard.</div>
                </div>
                <div class="faq-item">
                    <button class="faq-btn" onclick="this.parentElement.classList.toggle('open')">
                        What happens when the loan is fully paid?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-body">Once the hire-purchase period ends and all payments are complete, ownership of the bodaboda transfers to the driver. You receive a digital ownership certificate as proof.</div>
                </div>
                <div class="faq-item">
                    <button class="faq-btn" onclick="this.parentElement.classList.toggle('open')">
                        Is there a fee to use BodaLink?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-body">Creating an account is free. The platform facilitates the hire-purchase agreement and payment tracking between riders and owners.</div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <div class="cta-inner">
                <h2>Ready to get started?</h2>
                <p>Join BodaLink and start your hire-purchase journey today</p>
                <div class="hero-btns">
                    <a href="<?php echo e(route('register')); ?>" class="btn-primary"><i class="bi bi-person-plus"></i> Register Now</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="nav-brand" style="margin-bottom:4px;">
                        <span style="font-size:1.15rem;font-weight:800;color:#fff;letter-spacing:-.02em;">BodaLink</span>
                    </a>
                    <p>Hire-purchase platform for boda riders in Tanzania. Riders pay weekly, own the bodaboda at the end. Owners get paid on time, every time.</p>
                </div>
                <div>
                    <h4>Platform</h4>
                    <ul class="footer-links">
                        <li><a href="#how">How It Works</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="<?php echo e(route('login')); ?>">Login</a></li>
                        <li><a href="<?php echo e(route('register')); ?>">Register</a></li>
                    </ul>
                </div>
                <div>
                    <h4>For</h4>
                    <ul class="footer-links">
                        <li><a href="#drivers">Drivers</a></li>
                        <li><a href="#owners">Owners</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Support</h4>
                    <ul class="footer-links">
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="mailto:support@bodalink.co.tz">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; <?php echo e(date('Y')); ?> BodaLink. All rights reserved.</span>
                <div class="footer-socials">
                    <a href="https://wa.me/255000000000" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a href="mailto:support@bodalink.co.tz" title="Email Us"><i class="bi bi-envelope-fill"></i></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
<?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/landing.blade.php ENDPATH**/ ?>