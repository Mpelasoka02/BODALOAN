<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> — BodaLink</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.svg')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<div style="display:flex;">

    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-brand">
            <img src="<?php echo e(asset('images/logo-full-dark.svg')); ?>" alt="BodaLink" height="32">
        </a>

        <div class="sidebar-nav">
            <div class="sidebar-label">Main</div>
            <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <?php if(auth()->user()->isDriver()): ?>
            <a href="<?php echo e(route('marketplace')); ?>" class="sidebar-link <?php echo e(request()->routeIs('marketplace') ? 'active' : ''); ?>">
                <i class="bi bi-shop"></i> Marketplace
            </a>
            <?php endif; ?>

            <?php if(auth()->user()->isAdmin()): ?>
                <div class="sidebar-label">Approvals</div>
                <a href="<?php echo e(route('admin.drivers')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.drivers*') ? 'active' : ''); ?>">
                    <i class="bi bi-person-badge"></i> Drivers
                </a>
                <a href="<?php echo e(route('admin.owners')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.owners*') ? 'active' : ''); ?>">
                    <i class="bi bi-person-workspace"></i> Owners
                </a>
                <a href="<?php echo e(route('admin.vehicles')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.vehicles*') ? 'active' : ''); ?>">
                    <i class="bi bi-shield-check"></i> Bodabodas
                </a>
                <a href="<?php echo e(route('admin.applications')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.applications*') ? 'active' : ''); ?>">
                    <i class="bi bi-file-earmark-check"></i> Applications
                </a>

                <div class="sidebar-label">Management</div>
                <a href="<?php echo e(route('admin.relationships')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.relationships*') ? 'active' : ''); ?>">
                    <i class="bi bi-diagram-3"></i> Relationships
                </a>
                <a href="<?php echo e(route('admin.payments')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.payments*') ? 'active' : ''); ?>">
                    <i class="bi bi-credit-card-2-front-fill"></i> Payments
                </a>
                <a href="<?php echo e(route('admin.overdue')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.overdue*') ? 'active' : ''); ?>">
                    <i class="bi bi-exclamation-triangle-fill"></i> Overdue
                </a>

                <div class="sidebar-label">Communication</div>
                <a href="<?php echo e(route('chat.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('chat.*') ? 'active' : ''); ?>">
                    <i class="bi bi-chat-dots-fill"></i> Messages
                    <?php $unreadMsgs = auth()->user()->unreadMessagesCount(); ?>
                    <?php if($unreadMsgs > 0): ?>
                        <span class="sidebar-badge"><?php echo e($unreadMsgs > 9 ? '9+' : $unreadMsgs); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('admin.map')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.map*') ? 'active' : ''); ?>">
                    <i class="bi bi-geo-alt-fill"></i> GPS Map
                </a>
                <a href="<?php echo e(route('admin.users')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.users*') ? 'active' : ''); ?>">
                    <i class="bi bi-people-fill"></i> Users
                </a>
                <a href="<?php echo e(route('notifications.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>">
                    <i class="bi bi-bell-fill"></i> Notifications
                    <?php $unreadCount = auth()->user()->unreadNotificationsCount(); ?>
                    <?php if($unreadCount > 0): ?>
                        <span class="sidebar-badge"><?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
                    <i class="bi bi-gear-fill"></i> Settings
                </a>
            <?php endif; ?>

            <?php if(auth()->user()->isOwner()): ?>
                <div class="sidebar-label">Fleet</div>
                <a href="<?php echo e(route('owner.vehicles')); ?>" class="sidebar-link <?php echo e(request()->routeIs('owner.vehicles*') ? 'active' : ''); ?>">
                    <i class="bi bi-bicycle"></i> My Bodabodas
                </a>
                <a href="<?php echo e(route('owner.contracts')); ?>" class="sidebar-link <?php echo e(request()->routeIs('owner.contracts*') ? 'active' : ''); ?>">
                    <i class="bi bi-file-earmark-text"></i> Contracts
                </a>
                <a href="<?php echo e(route('loans.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('loans.*') ? 'active' : ''); ?>">
                    <i class="bi bi-wallet2"></i> Loans
                </a>
                <a href="<?php echo e(route('payments.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('payments.*') ? 'active' : ''); ?>">
                    <i class="bi bi-credit-card-2-front-fill"></i> Payments
                </a>
                <a href="<?php echo e(route('reports.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                    <i class="bi bi-bar-chart-line-fill"></i> Reports
                </a>
                <a href="<?php echo e(route('owner.map')); ?>" class="sidebar-link <?php echo e(request()->routeIs('owner.map*') ? 'active' : ''); ?>">
                    <i class="bi bi-geo-alt-fill"></i> GPS Map
                </a>
                <a href="<?php echo e(route('chat.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('chat.*') ? 'active' : ''); ?>">
                    <i class="bi bi-chat-dots-fill"></i> Messages
                    <?php $unreadMsgs = auth()->user()->unreadMessagesCount(); ?>
                    <?php if($unreadMsgs > 0): ?>
                        <span class="sidebar-badge"><?php echo e($unreadMsgs > 9 ? '9+' : $unreadMsgs); ?></span>
                    <?php endif; ?>
                </a>
                <div class="sidebar-label">Account</div>
                <a href="<?php echo e(route('notifications.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>">
                    <i class="bi bi-bell-fill"></i> Notifications
                    <?php $unreadCount = auth()->user()->unreadNotificationsCount(); ?>
                    <?php if($unreadCount > 0): ?>
                        <span class="sidebar-badge"><?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
                    <i class="bi bi-gear-fill"></i> Settings
                </a>
            <?php endif; ?>

            <?php if(auth()->user()->isDriver()): ?>
                <div class="sidebar-label">Ride</div>
                <a href="<?php echo e(route('driver.apps')); ?>" class="sidebar-link <?php echo e(request()->routeIs('driver.apps*') ? 'active' : ''); ?>">
                    <i class="bi bi-file-text"></i> My Applications
                </a>
                <a href="<?php echo e(route('driver.contracts')); ?>" class="sidebar-link <?php echo e(request()->routeIs('driver.contracts*') ? 'active' : ''); ?>">
                    <i class="bi bi-file-earmark-text"></i> Contracts
                </a>
                <?php if(auth()->user()->loans()->where('status', 'active')->exists()): ?>
                <a href="<?php echo e(route('loans.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('loans.*') ? 'active' : ''); ?>">
                    <i class="bi bi-wallet2"></i> My Loan
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('driver.gps')); ?>" class="sidebar-link <?php echo e(request()->routeIs('driver.gps*') ? 'active' : ''); ?>">
                    <i class="bi bi-geo-alt-fill"></i> GPS Tracking
                </a>
                <a href="<?php echo e(route('chat.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('chat.*') ? 'active' : ''); ?>">
                    <i class="bi bi-chat-dots-fill"></i> Messages
                    <?php $unreadMsgs = auth()->user()->unreadMessagesCount(); ?>
                    <?php if($unreadMsgs > 0): ?>
                        <span class="sidebar-badge"><?php echo e($unreadMsgs > 9 ? '9+' : $unreadMsgs); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('payments.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('payments.*') ? 'active' : ''); ?>">
                    <i class="bi bi-credit-card-2-front-fill"></i> Payments
                </a>
                <div class="sidebar-label">Account</div>
                <a href="<?php echo e(route('notifications.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>">
                    <i class="bi bi-bell-fill"></i> Notifications
                    <?php $unreadCount = auth()->user()->unreadNotificationsCount(); ?>
                    <?php if($unreadCount > 0): ?>
                        <span class="sidebar-badge"><?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
                    <i class="bi bi-gear-fill"></i> Settings
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Main content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="topbar">
            <span class="topbar-title"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></span>
            <div class="topbar-actions">
                <form action="<?php echo e(route('search')); ?>" method="GET" class="topbar-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Search..." value="<?php echo e(request('q')); ?>">
                </form>

                <a href="<?php echo e(route('notifications.index')); ?>" class="topbar-icon-btn" title="Notifications">
                    <i class="bi bi-bell" style="font-size:1.1rem;"></i>
                    <?php $unreadCount = auth()->user()->unreadNotificationsCount(); ?>
                    <?php if($unreadCount > 0): ?>
                        <span class="dot"></span>
                    <?php endif; ?>
                </a>

                <div class="dropdown">
                    <button class="user-pill dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar"><?php echo e(substr(auth()->user()->name, 0, 1)); ?></div>
                        <div>
                            <div class="user-name"><?php echo e(auth()->user()->name); ?></div>
                            <div class="user-role"><?php echo e(ucfirst(auth()->user()->role)); ?></div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 180px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('profile.edit')); ?>">
                                <i class="bi bi-person"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" style="margin: 4px 0;"></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2" style="color:var(--status-overdue-text);">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-area">
            <?php if(session('success')): ?>
                <div class="alert-banner green d-flex align-items-center" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
                        <span><?php echo e(session('success')); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert-banner red d-flex align-items-center" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill" style="color:var(--status-overdue-text);"></i>
                        <span><?php echo e(session('error')); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="alert-banner red" role="alert">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>

        <footer class="app-footer text-center py-3" style="font-size:0.78rem;color:var(--text-muted);">
            &copy; <?php echo e(date('Y')); ?> BodaLink. All rights reserved.
        </footer>
    </div>

    <?php $user = auth()->user(); ?>
    <!-- Mobile Bottom Nav -->
    <nav class="mobile-nav">
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        <?php if($user->isAdmin()): ?>
            <a href="<?php echo e(route('admin.payments')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.payments*') ? 'active' : ''); ?>">
                <i class="bi bi-credit-card-2-front-fill"></i>
                <span>Payments</span>
            </a>
            <a href="<?php echo e(route('admin.applications')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.applications*') ? 'active' : ''); ?>">
                <i class="bi bi-file-earmark-check"></i>
                <span>Apps</span>
            </a>
            <a href="<?php echo e(route('admin.vehicles')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.vehicles*') ? 'active' : ''); ?>">
                <i class="bi bi-shield-check"></i>
                <span>Bodabodas</span>
            </a>
        <?php elseif($user->isOwner()): ?>
            <a href="<?php echo e(route('owner.vehicles')); ?>" class="nav-item <?php echo e(request()->routeIs('owner.vehicles*') ? 'active' : ''); ?>">
                <i class="bi bi-bicycle"></i>
                <span>Bodabodas</span>
            </a>
            <a href="<?php echo e(route('payments.index')); ?>" class="nav-item <?php echo e(request()->routeIs('payments.*') ? 'active' : ''); ?>">
                <i class="bi bi-credit-card-2-front-fill"></i>
                <span>Payments</span>
            </a>
            <a href="<?php echo e(route('loans.index')); ?>" class="nav-item <?php echo e(request()->routeIs('loans.*') ? 'active' : ''); ?>">
                <i class="bi bi-wallet2"></i>
                <span>Loans</span>
            </a>
        <?php elseif($user->isDriver()): ?>
            <a href="<?php echo e(route('driver.marketplace')); ?>" class="nav-item <?php echo e(request()->routeIs('driver.marketplace*') ? 'active' : ''); ?>">
                <i class="bi bi-shop"></i>
                <span>Market</span>
            </a>
            <a href="<?php echo e(route('driver.apps')); ?>" class="nav-item <?php echo e(request()->routeIs('driver.apps*') ? 'active' : ''); ?>">
                <i class="bi bi-file-text"></i>
                <span>Apps</span>
            </a>
            <a href="<?php echo e(route('payments.index')); ?>" class="nav-item <?php echo e(request()->routeIs('payments.*') ? 'active' : ''); ?>">
                <i class="bi bi-credit-card-2-front-fill"></i>
                <span>Payments</span>
            </a>
        <?php endif; ?>
        <a href="<?php echo e(route('notifications.index')); ?>" class="nav-item <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>">
            <i class="bi bi-bell-fill"></i>
            <span>Alerts</span>
        </a>
    </nav>
</div>

<div id="firebase-toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if(config('firebase.fcm_enabled') || config('firebase.realtime_db_enabled')): ?>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
<?php if(config('firebase.fcm_enabled')): ?>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>
<?php endif; ?>
<?php if(config('firebase.realtime_db_enabled')): ?>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-database-compat.js"></script>
<?php endif; ?>
<script>
    const firebaseConfig = <?php echo json_encode(\app(\App\Services\FirebaseService::class)->getClientConfig(), 15, 512) ?>;
    if (firebaseConfig.apiKey) {
        firebase.initializeApp(firebaseConfig);
        window.firebaseEnabled = true;
    } else {
        window.firebaseEnabled = false;
    }
</script>
<?php endif; ?>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/layouts/app.blade.php ENDPATH**/ ?>