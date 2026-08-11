<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BodaBoda Marketplace') - BodaLink</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; min-height: 100vh; }

        /* ── NAVBAR ── */
        .pub-nav {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 48px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .pub-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #064180;
            font-weight: 800;
            font-size: 1.2rem;
        }
        .pub-logo i { font-size: 1.4rem; }
        .pub-nav-links { display: flex; align-items: center; gap: 8px; }
        .pub-nav-links a {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s;
        }
        .pub-nav-links a:hover { color: #064180; background: #f1f5f9; }
        .pub-nav-links .btn-login {
            background: #064180;
            color: #fff;
            border-radius: 10px;
            padding: 8px 20px;
        }
        .pub-nav-links .btn-login:hover { background: #053060; color: #fff; }

        /* ── CONTENT ── */
        .pub-content { min-height: calc(100vh - 64px); }

        /* ── FOOTER ── */
        .pub-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 40px 48px;
            margin-top: 60px;
        }
        .pub-footer-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .pub-footer-brand {
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
        }
        .pub-footer-links { display: flex; gap: 20px; }
        .pub-footer-links a { color: #94a3b8; text-decoration: none; font-size: 0.82rem; }
        .pub-footer-links a:hover { color: #fff; }

        @media (max-width: 768px) {
            .pub-nav { padding: 0 20px; }
            .pub-footer { padding: 32px 20px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="pub-nav">
        <a href="{{ url('/') }}" class="pub-logo">
            <i class="bi bi-motorcycle"></i> BodaLink
        </a>
        <div class="pub-nav-links">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('home') }}">Marketplace</a>
            <a href="{{ route('login') }}" class="btn-login">Login</a>
        </div>
    </nav>

    <div class="pub-content">
        @yield('content')
    </div>

    <footer class="pub-footer">
        <div class="pub-footer-inner">
            <div class="pub-footer-brand">BodaLink</div>
            <div class="pub-footer-links">
                <a href="{{ url('/#how') }}">How It Works</a>
                <a href="{{ url('/#services') }}">Services</a>
                <a href="{{ route('login') }}">Login</a>
            </div>
            <div style="font-size:0.78rem;">&copy; {{ date('Y') }} BodaLink. All rights reserved.</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
