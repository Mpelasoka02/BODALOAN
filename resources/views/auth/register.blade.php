<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — BodaLink</title>
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

        .auth-brand svg {
            margin-bottom: 16px;
        }

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

        .role-tabs {
            display: flex;
            background: var(--page-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 3px;
            margin-bottom: 22px;
        }

        .role-tab {
            flex: 1;
            padding: 10px 8px;
            text-align: center;
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            background: none;
            border: none;
            border-radius: 8px;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .role-tab:hover { color: var(--text); }

        .role-tab.active {
            color: #fff;
            background: var(--navy-900);
            box-shadow: 0 2px 6px rgba(15,27,45,0.2);
        }

        .field { margin-bottom: 16px; }

        .field label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .field-input {
            position: relative;
        }

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
            margin-top: 6px;
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

        .alert-msg {
            border-radius: 10px;
            font-size: 0.82rem;
            padding: 11px 14px;
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            line-height: 1.4;
        }

        .alert-msg i { margin-top: 1px; flex-shrink: 0; }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-left: 4px solid #DC2626;
            color: #991B1B;
        }

        .alert-success {
            background: var(--emerald-100);
            border: 1px solid #A7F3D0;
            border-left: 4px solid var(--emerald-600);
            color: #065F46;
        }

        .auth-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

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
        <h2>Create your account</h2>
        <div class="subtitle">Join BodaLink as a driver or bodaboda owner</div>

        <div class="role-tabs" id="roleTabs">
            <button type="button" class="role-tab active" data-role="driver" onclick="switchTab(this)">
                <i class="bi bi-bicycle me-1"></i> Driver
            </button>
            <button type="button" class="role-tab" data-role="owner" onclick="switchTab(this)">
                <i class="bi bi-building me-1"></i> Owner
            </button>
        </div>

        @if($errors->any())
            <div class="alert-msg alert-error">
                <i class="bi bi-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="alert-msg alert-success">
                <i class="bi bi-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="role" id="regRole" value="{{ old('role', 'driver') }}">

            <div class="field">
                <label for="name">Full Name</label>
                <div class="field-input">
                    <i class="bi bi-person"></i>
                    <input type="text" name="name" id="name" placeholder="John Mwangi" value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <div class="field-input">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="you@gmail.com" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="field">
                <label for="phone">Phone Number</label>
                <div class="field-input">
                    <i class="bi bi-phone"></i>
                    <input type="text" name="phone" id="phone" placeholder="+255 700 000 000" value="{{ old('phone') }}" required>
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="field-input">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Min 8 characters" required>
                </div>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <div class="field-input">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-enter password" required>
                </div>
            </div>

            <div id="ownerLocationFields" style="display:none;">
                <div class="field" style="margin-top:4px;">
                    <label>Your Location</label>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin-bottom:10px;">Drivers need to find you to pick up the bodaboda. Set your location on the map or enter coordinates manually.</p>
                    <div style="display:flex;gap:8px;margin-bottom:10px;">
                        <button type="button" class="btn-primary" style="width:auto;margin:0;padding:10px 16px;font-size:0.82rem;" onclick="getMyLocation()">
                            <i class="bi bi-geo-alt"></i> Use My Location
                        </button>
                        <span id="locStatus" style="font-size:0.78rem;color:var(--emerald-600);display:none;align-items:center;gap:4px;"><i class="bi bi-check-circle-fill"></i> Location set</span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <div>
                            <input type="number" step="any" name="latitude" id="regLat" placeholder="Latitude (e.g. -6.7924)" class="form-input-sm" style="width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:0.88rem;font-family:'Inter',sans-serif;">
                        </div>
                        <div>
                            <input type="number" step="any" name="longitude" id="regLng" placeholder="Longitude (e.g. 39.2083)" class="form-input-sm" style="width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:0.88rem;font-family:'Inter',sans-serif;">
                        </div>
                    </div>
                    <div style="margin-top:8px;">
                        <input type="text" name="location_name" placeholder="Location name (e.g. Kariakoo Market)" class="form-input-sm" style="width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:0.88rem;font-family:'Inter',sans-serif;">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="bi bi-person-plus"></i> Register
            </button>
        </form>

        <div class="auth-links">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>

    <div class="auth-footer">
        &copy; {{ date('Y') }} BodaLink. All rights reserved.
    </div>

    <script>
    function switchTab(el) {
        document.querySelectorAll('.role-tab').forEach(function(t) { t.classList.remove('active'); });
        el.classList.add('active');
        document.getElementById('regRole').value = el.dataset.role;
        var isOwner = el.dataset.role === 'owner';
        document.getElementById('ownerLocationFields').style.display = isOwner ? 'block' : 'none';
    }
    function getMyLocation() {
        var status = document.getElementById('locStatus');
        status.style.display = 'none';
        if (!navigator.geolocation) { alert('Geolocation not supported by your browser.'); return; }
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('regLat').value = pos.coords.latitude.toFixed(6);
            document.getElementById('regLng').value = pos.coords.longitude.toFixed(6);
            status.style.display = 'inline-flex';
        }, function(err) {
            alert('Could not get location: ' + err.message);
        }, { enableHighAccuracy: true, timeout: 10000 });
    }
    </script>
</body>
</html>
