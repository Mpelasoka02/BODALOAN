<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — BodaLink</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div style="display:flex;">

    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('images/logo-full-dark.svg') }}" alt="BodaLink" height="32">
        </a>

        <div class="sidebar-nav">
            <div class="sidebar-label">Main</div>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            @if(auth()->user()->isDriver())
            <a href="{{ route('marketplace') }}" class="sidebar-link {{ request()->routeIs('marketplace') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> Marketplace
            </a>
            @endif

            @if(auth()->user()->isAdmin())
                <div class="sidebar-label">Approvals</div>
                <a href="{{ route('admin.drivers') }}" class="sidebar-link {{ request()->routeIs('admin.drivers*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Drivers
                </a>
                <a href="{{ route('admin.owners') }}" class="sidebar-link {{ request()->routeIs('admin.owners*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i> Owners
                </a>
                <a href="{{ route('admin.vehicles') }}" class="sidebar-link {{ request()->routeIs('admin.vehicles*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i> Bodabodas
                </a>
                <a href="{{ route('admin.applications') }}" class="sidebar-link {{ request()->routeIs('admin.applications*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check"></i> Applications
                </a>

                <div class="sidebar-label">Management</div>
                <a href="{{ route('admin.relationships') }}" class="sidebar-link {{ request()->routeIs('admin.relationships*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i> Relationships
                </a>
                <a href="{{ route('admin.payments') }}" class="sidebar-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front-fill"></i> Payments
                </a>
                <a href="{{ route('admin.overdue') }}" class="sidebar-link {{ request()->routeIs('admin.overdue*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle-fill"></i> Overdue
                </a>

                <div class="sidebar-label">Communication</div>
                <a href="{{ route('chat.index') }}" class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots-fill"></i> Messages
                    @php $unreadMsgs = auth()->user()->unreadMessagesCount(); @endphp
                    @if($unreadMsgs > 0)
                        <span class="sidebar-badge">{{ $unreadMsgs > 9 ? '9+' : $unreadMsgs }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.map') }}" class="sidebar-link {{ request()->routeIs('admin.map*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt-fill"></i> GPS Map
                </a>
                <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Users
                </a>
                <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-bell-fill"></i> Notifications
                    @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                    @if($unreadCount > 0)
                        <span class="sidebar-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Settings
                </a>
            @endif

            @if(auth()->user()->isOwner())
                <div class="sidebar-label">Fleet</div>
                <a href="{{ route('owner.vehicles') }}" class="sidebar-link {{ request()->routeIs('owner.vehicles*') ? 'active' : '' }}">
                    <i class="bi bi-bicycle"></i> My Bodabodas
                </a>
                <a href="{{ route('owner.contracts') }}" class="sidebar-link {{ request()->routeIs('owner.contracts*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Contracts
                </a>
                <a href="{{ route('loans.index') }}" class="sidebar-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> Loans
                </a>
                <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front-fill"></i> Payments
                </a>
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill"></i> Reports
                </a>
                <a href="{{ route('owner.map') }}" class="sidebar-link {{ request()->routeIs('owner.map*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt-fill"></i> GPS Map
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots-fill"></i> Messages
                    @php $unreadMsgs = auth()->user()->unreadMessagesCount(); @endphp
                    @if($unreadMsgs > 0)
                        <span class="sidebar-badge">{{ $unreadMsgs > 9 ? '9+' : $unreadMsgs }}</span>
                    @endif
                </a>
                <div class="sidebar-label">Account</div>
                <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-bell-fill"></i> Notifications
                    @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                    @if($unreadCount > 0)
                        <span class="sidebar-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Settings
                </a>
            @endif

            @if(auth()->user()->isDriver())
                <div class="sidebar-label">Ride</div>
                <a href="{{ route('driver.apps') }}" class="sidebar-link {{ request()->routeIs('driver.apps*') ? 'active' : '' }}">
                    <i class="bi bi-file-text"></i> My Applications
                </a>
                <a href="{{ route('driver.contracts') }}" class="sidebar-link {{ request()->routeIs('driver.contracts*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Contracts
                </a>
                @if(auth()->user()->loans()->where('status', 'active')->exists())
                <a href="{{ route('loans.index') }}" class="sidebar-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> My Loan
                </a>
                @endif
                <a href="{{ route('driver.gps') }}" class="sidebar-link {{ request()->routeIs('driver.gps*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt-fill"></i> GPS Tracking
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots-fill"></i> Messages
                    @php $unreadMsgs = auth()->user()->unreadMessagesCount(); @endphp
                    @if($unreadMsgs > 0)
                        <span class="sidebar-badge">{{ $unreadMsgs > 9 ? '9+' : $unreadMsgs }}</span>
                    @endif
                </a>
                <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front-fill"></i> Payments
                </a>
                <div class="sidebar-label">Account</div>
                <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-bell-fill"></i> Notifications
                    @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                    @if($unreadCount > 0)
                        <span class="sidebar-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Settings
                </a>
            @endif
        </div>
    </aside>

    <!-- Main content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="topbar">
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            <div class="topbar-actions">
                <form action="{{ route('search') }}" method="GET" class="topbar-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Search..." value="{{ request('q') }}">
                </form>

                <a href="{{ route('notifications.index') }}" class="topbar-icon-btn" title="Notifications">
                    <i class="bi bi-bell" style="font-size:1.1rem;"></i>
                    @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                    @if($unreadCount > 0)
                        <span class="dot"></span>
                    @endif
                </a>

                <div class="dropdown">
                    <button class="user-pill dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div>
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 180px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" style="margin: 4px 0;"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
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
            @if(session('success'))
                <div class="alert-banner green d-flex align-items-center" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-banner red d-flex align-items-center" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill" style="color:var(--status-overdue-text);"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="alert-banner red" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="app-footer text-center py-3" style="font-size:0.78rem;color:var(--text-muted);">
            &copy; {{ date('Y') }} BodaLink. All rights reserved.
        </footer>
    </div>

    @php $user = auth()->user(); @endphp
    <!-- Mobile Bottom Nav -->
    <nav class="mobile-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        @if($user->isAdmin())
            <a href="{{ route('admin.payments') }}" class="nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front-fill"></i>
                <span>Payments</span>
            </a>
            <a href="{{ route('admin.applications') }}" class="nav-item {{ request()->routeIs('admin.applications*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check"></i>
                <span>Apps</span>
            </a>
            <a href="{{ route('admin.vehicles') }}" class="nav-item {{ request()->routeIs('admin.vehicles*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span>Bodabodas</span>
            </a>
        @elseif($user->isOwner())
            <a href="{{ route('owner.vehicles') }}" class="nav-item {{ request()->routeIs('owner.vehicles*') ? 'active' : '' }}">
                <i class="bi bi-bicycle"></i>
                <span>Bodabodas</span>
            </a>
            <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front-fill"></i>
                <span>Payments</span>
            </a>
            <a href="{{ route('loans.index') }}" class="nav-item {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Loans</span>
            </a>
        @elseif($user->isDriver())
            <a href="{{ route('driver.marketplace') }}" class="nav-item {{ request()->routeIs('driver.marketplace*') ? 'active' : '' }}">
                <i class="bi bi-shop"></i>
                <span>Market</span>
            </a>
            <a href="{{ route('driver.apps') }}" class="nav-item {{ request()->routeIs('driver.apps*') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i>
                <span>Apps</span>
            </a>
            <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front-fill"></i>
                <span>Payments</span>
            </a>
        @endif
        <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell-fill"></i>
            <span>Alerts</span>
        </a>
    </nav>
</div>

<div id="firebase-toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@if(config('firebase.fcm_enabled') || config('firebase.realtime_db_enabled'))
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
@if(config('firebase.fcm_enabled'))
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>
@endif
@if(config('firebase.realtime_db_enabled'))
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-database-compat.js"></script>
@endif
<script>
    const firebaseConfig = @json(\app(\App\Services\FirebaseService::class)->getClientConfig());
    if (firebaseConfig.apiKey) {
        firebase.initializeApp(firebaseConfig);
        window.firebaseEnabled = true;
    } else {
        window.firebaseEnabled = false;
    }
</script>
@endif

@stack('scripts')
</body>
</html>
