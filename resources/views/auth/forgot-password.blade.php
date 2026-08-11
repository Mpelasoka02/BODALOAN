<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — BodaLink</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --navy-900: #0F1B2D;
            --navy-700: #1B3358;
            --gold-500: #C9962C;
            --emerald-600: #0E9F6E;
            --emerald-100: #E3F9EF;
            --gold-100: #FBF3E2;
            --page-bg: #F5F7FA;
            --card-bg: #FFFFFF;
            --border: #E2E5EA;
            --text: #1A2233;
            --text-muted: #6B7684;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            min-height: 100vh;
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-brand svg { margin-bottom: 16px; }

        .auth-brand h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--navy-900);
            margin-bottom: 4px;
        }

        .auth-brand p {
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
        }

        .auth-card h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .auth-card .subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .field { margin-bottom: 16px; }

        .field label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .field-input { position: relative; }

        .field-input i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .field-input input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 11px 14px 11px 42px;
            font-size: 0.9rem;
            color: var(--text);
            background: #fff;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field-input input:focus {
            outline: none;
            border-color: var(--navy-700);
            box-shadow: 0 0 0 3px rgba(27,51,88,0.08);
        }

        .btn-primary {
            width: 100%;
            background: var(--gold-500);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        }

        .btn-primary:hover {
            background: #B8872A;
            box-shadow: 0 4px 14px rgba(201,150,44,0.3);
            transform: translateY(-1px);
        }

        .auth-links {
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .auth-links a {
            color: var(--gold-500);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-links a:hover { text-decoration: underline; }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-left: 4px solid #DC2626;
            border-radius: 10px;
            font-size: 0.82rem;
            padding: 11px 14px;
            margin-bottom: 18px;
            color: #991B1B;
        }

        .alert-success {
            background: var(--emerald-100);
            border: 1px solid #A7F3D0;
            border-left: 4px solid var(--emerald-600);
            border-radius: 10px;
            font-size: 0.82rem;
            padding: 11px 14px;
            margin-bottom: 18px;
            color: #065F46;
        }

        .auth-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .auth-footer a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 20px; }
            .auth-brand h1 { font-size: 1.3rem; }
        }
    </style>
</head>
<body>

    <div class="auth-brand">
        <img src="{{ asset('images/logo-stacked.svg') }}" alt="BodaLink" height="100">
    </div>

    <div class="auth-card">
        <h2>Forgot password?</h2>
        <div class="subtitle">Enter your email and we'll send you a reset link</div>

        @if(session('status'))
            <div class="alert-success"><i class="bi bi-check-circle me-1"></i> {{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="alert-error"><i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field">
                <label>Email</label>
                <div class="field-input">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" placeholder="you@gmail.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="bi bi-send"></i> Send Reset Link
            </button>
        </form>

        <div class="auth-links">
            Remember your password? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>

    <div class="auth-footer">
        &copy; {{ date('Y') }} BodaLink. All rights reserved.
    </div>
</body>
</html>
