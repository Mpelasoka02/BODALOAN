<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BodaLink</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.svg')); ?>">
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

        .field-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 0.82rem;
        }

        .field-row label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: var(--text);
            font-weight: 500;
        }

        .field-row a {
            color: var(--navy-700);
            font-weight: 600;
            text-decoration: none;
        }

        .field-row a:hover { text-decoration: underline; }

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

        .admin-toggle {
            text-align: center;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }

        .admin-toggle button {
            background: var(--navy-900);
            border: none;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            padding: 9px 20px;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .admin-toggle button:hover {
            background: var(--navy-700);
            box-shadow: 0 2px 8px rgba(15,27,45,0.25);
        }

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

        .alert-info {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-left: 4px solid #2563EB;
            color: #1E40AF;
        }

        .alert-warning {
            background: var(--gold-100);
            border: 1px solid #FDE68A;
            border-left: 4px solid var(--gold-500);
            color: #92400E;
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
        <img src="<?php echo e(asset('images/logo-stacked.svg')); ?>" alt="BodaLink" height="100">
    </div>

    <div class="auth-card">
        <h2>Welcome back</h2>
        <div class="subtitle">Login to your account</div>

        <?php $prevRole = old('login_role', 'owner'); ?>

        <div class="role-tabs" id="roleTabs" style="<?php echo e($prevRole === 'admin' ? 'display:none' : ''); ?>">
            <button type="button" class="role-tab <?php echo e($prevRole !== 'driver' ? 'active' : ''); ?>" data-role="owner" onclick="switchTab(this)">
                <i class="bi bi-building me-1"></i> Owner
            </button>
            <button type="button" class="role-tab <?php echo e($prevRole === 'driver' ? 'active' : ''); ?>" data-role="driver" onclick="switchTab(this)">
                <i class="bi bi-bicycle me-1"></i> Driver
            </button>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert-msg alert-error">
                <i class="bi bi-exclamation-circle"></i>
                <span><?php echo e($errors->first()); ?></span>
            </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
            <div class="alert-msg alert-success">
                <i class="bi bi-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if(session('message')): ?>
            <div class="alert-msg alert-info">
                <i class="bi bi-info-circle"></i>
                <span><?php echo e(session('message')); ?></span>
            </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
            <div class="alert-msg alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                <span><?php echo e(session('warning')); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="login_role" id="loginRole" value="<?php echo e(old('login_role', 'owner')); ?>">

            <div class="field">
                <label for="email">Email</label>
                <div class="field-input">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="you@gmail.com" value="<?php echo e(old('email')); ?>" required autofocus>
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="field-input">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="field-row">
                <label>
                    <input type="checkbox" name="remember" style="accent-color: var(--gold-500);"> Remember me
                </label>
                <a href="<?php echo e(route('password.request')); ?>">Forgot password?</a>
            </div>

            <button type="submit" class="btn-primary">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>

        <div class="auth-links" id="authFooter">
            Don't have an account? <a href="<?php echo e(route('register')); ?>">Register here</a>
        </div>

        <div class="admin-toggle">
            <button type="button" onclick="toggleAdmin(this)">
                <i class="bi bi-shield-lock me-1"></i> Admin Login
            </button>
        </div>
    </div>

    <div class="auth-footer">
        &copy; <?php echo e(date('Y')); ?> BodaLink. All rights reserved.
    </div>

    <script>
    function switchTab(el) {
        document.querySelectorAll('.role-tab').forEach(function(t) { t.classList.remove('active'); });
        el.classList.add('active');
        document.getElementById('loginRole').value = el.dataset.role;
    }

    function toggleAdmin(btn) {
        var tabs = document.getElementById('roleTabs');
        var footer = document.getElementById('authFooter');
        var roleInput = document.getElementById('loginRole');

        if (tabs.style.display === 'none') {
            tabs.style.display = 'flex';
            footer.style.display = '';
            btn.innerHTML = '<i class="bi bi-shield-lock me-1"></i> Admin Login';
            var activeTab = document.querySelector('.role-tab.active');
            roleInput.value = activeTab ? activeTab.dataset.role : 'owner';
        } else {
            tabs.style.display = 'none';
            footer.style.display = 'none';
            btn.innerHTML = '<i class="bi bi-building me-1"></i> Back to Owner / Driver';
            roleInput.value = 'admin';
        }
    }
    </script>
</body>
</html>
<?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/auth/login.blade.php ENDPATH**/ ?>