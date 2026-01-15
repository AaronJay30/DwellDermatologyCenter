<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dwell Dermatology Center') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        :root {
            --primary-color: #197a8c;
            --accent-color: #ffd700;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --light-text: #6c757d;
            --sidebar-bg: #197a8c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Sidebar layout */
        .layout {
            display: flex;
            min-height: 100vh;
            overflow: hidden;
        }

        .content-wrapper {
            margin-left: 230px;
            flex: 1;
            width: calc(100% - 230px);
            position: relative;
            z-index: 1;
        }

        .sidebar {
            background: var(--sidebar-bg);
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.08);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 2rem 0.5rem;
            border-radius: 0 15px 15px 0;
            overflow: hidden !important;
            overflow-y: hidden !important;
            overflow-x: hidden !important;
            z-index: 10;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin-bottom: 2.75rem;
            flex-shrink: 0;
        }

        .hamburger {
            display: none;
            cursor: pointer;
            background: transparent;
            border: 0;
            color: white;
            font-size: 1.5rem;
            padding: 0.5rem;
            transition: transform 0.3s ease;
        }

        .hamburger:hover {
            opacity: 0.8;
        }

        .mobile-menu-toggle {
            display: none;
            cursor: pointer;
            background: transparent;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            color: var(--dark-text);
            font-size: 1.25rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: #f8fafc;
            border-color: #dde2e7;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        .sidebar-content {
            display: flex;
            flex-direction: column;
            gap: .6rem;
            padding: 0;
            overflow: hidden !important;
            overflow-y: hidden !important;
            overflow-x: hidden !important;
            flex: 1 1 0;
            min-height: 0;
            max-height: calc(100vh - 200px);
        }

        .sidebar .nav-links {
            display: flex;
            flex-direction: column;
            gap: .6rem;
            overflow: hidden !important;
            overflow-y: hidden !important;
            overflow-x: hidden !important;
            flex-shrink: 1;
            min-height: 0;
        }

        .sidebar .nav-links a {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            padding: 0.35rem 0.5rem;
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            font-weight: 400;
            letter-spacing: 0.3px;
            font-size: 14px;
            white-space: nowrap;
            text-transform: capitalize;
            position: relative;
            border-radius: 6px;
            transition: all .3s ease-in-out;
        }

        /* Icon styling (Feather/Lucide) */
        .sidebar .nav-links a svg {
            width: 18px;
            height: 18px;
            stroke: #ffffff;
            stroke-width: 2px;
        }

        /* Left accent bar + hover tint */
        .sidebar .nav-links a::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #ffffff;
            transform: scaleY(0);
            transform-origin: top;
            transition: transform .3s ease-in-out;
            border-radius: 0 3px 3px 0;
        }

        .sidebar .nav-links a:hover {
            background: rgba(255,255,255,0.10);
        }

        .sidebar .nav-links a:hover::before {
            transform: scaleY(1);
        }

        /* Active state keeps the accent bar + tint */
        .sidebar .nav-links a.active {
            background: rgba(255,255,255,0.10);
        }
        .sidebar .nav-links a.active::before {
            transform: scaleY(1);
        }
        .sidebar .nav-links a.active {
            color: #ffffff;
            font-weight: 700;
        }
        .sidebar .nav-links a:hover {
            color: #ffffff;
        }

        .sidebar .user-info {
            background-color: transparent;
            border-radius: 0;
            padding: .25rem .5rem;
            color: #ffffff;
            letter-spacing: .3px;
            font-weight: 500;
        }

        .logo {
            font-size: .9rem;
            font-weight: 500;
            color: #ffffff;
            letter-spacing: .3px;
            text-align: left;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        
        .logo .brand-mark { height: 60px; width: auto; display: inline-block; }
        .logo span { font-weight: 700; }

        .content-wrapper {
            display: block;
        }

        /* Top Navbar */
        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            margin-left: 0;
        }
        .top-navbar .top-navbar__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .75rem 1rem;
        }
        .top-navbar__left {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .top-navbar__title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark-text);
            letter-spacing: .2px;
        }
        .top-navbar__right {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .icon-button {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #e9ecef;
            background: #ffffff;
            cursor: pointer;
            transition: background .2s ease, border-color .2s ease;
            text-decoration: none;
            color: inherit;
        }
        .icon-button:hover {
            background: #f8fafc;
            border-color: #dde2e7;
        }
        .icon-button svg {
            width: 18px;
            height: 18px;
        }
        .crud-alert-indicator {
            display: none;
            align-items: center;
            gap: 0.45rem;
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1;
            background: rgba(23, 162, 184, 0.12);
            color: #0c5460;
            border: 1px solid rgba(23, 162, 184, 0.2);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .crud-alert-indicator.visible {
            display: inline-flex;
            animation: fadeIn 0.2s ease;
        }
        .crud-alert-indicator[data-state="success"] {
            background: rgba(40, 167, 69, 0.15);
            border-color: rgba(40, 167, 69, 0.3);
            color: #1e7e34;
        }
        .crud-alert-indicator[data-state="error"] {
            background: rgba(220, 53, 69, 0.15);
            border-color: rgba(220, 53, 69, 0.3);
            color: #b21f2d;
        }
        .crud-alert-indicator[data-state="info"] {
            background: rgba(23, 162, 184, 0.15);
            border-color: rgba(23, 162, 184, 0.3);
            color: #0c5460;
        }
        .crud-alert-icon {
            display: inline-flex;
            width: 18px;
            height: 18px;
        }
        .crud-alert-icon svg {
            width: 18px;
            height: 18px;
        }
        .crud-alert-text {
            white-space: nowrap;
        }
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e9f3f5;
            color: var(--primary-color);
            font-weight: 700;
            border: 1px solid #e9ecef;
            cursor: pointer;
        }
        .menu {
            position: relative;
        }
        .menu .menu-panel {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            min-width: 200px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            display: none;
            overflow: hidden;
            z-index: 1000;
        }
        .menu.open .menu-panel { display: block; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .menu-panel a, .menu-panel form button {
            width: 100%;
            text-align: left;
            padding: .75rem .9rem;
            background: transparent;
            border: 0;
            outline: 0;
            color: var(--dark-text);
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }
        .menu-panel a:hover, .menu-panel form button:hover {
            background: #f8fafc;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 1rem;
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }

        .user-info span {
            font-size: 0.9rem;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1a6b7a;
            transform: translateY(-2px);
        }

        .btn-accent {
            background-color: var(--accent-color);
            color: var(--dark-text);
        }

        .btn-accent:hover {
            background-color: #e6c200;
            transform: translateY(-2px);
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin: 1rem 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-text);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .main-content {
            padding: 2rem 0;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .content-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                width: 260px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
                box-shadow: 2px 0 20px rgba(0,0,0,0.15);
                overflow: hidden !important;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-header {
                justify-content: space-between;
                padding: 1rem 1rem 0 1rem;
                margin-bottom: 2rem;
            }

            .hamburger {
                display: inline-block;
            }

            .mobile-menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .content-wrapper {
                width: 100%;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 240px;
            }

            .logo {
                font-size: 0.85rem;
            }

            .logo .brand-mark {
                height: 50px;
            }

            .sidebar .nav-links a {
                font-size: 13px;
                padding: 0.4rem 0.6rem;
            }

            .content-wrapper {
                padding-top: 0;
            }

            .container {
                padding: 0 15px;
            }

            .card {
                padding: 1.5rem;
            }

            .top-navbar__title {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 220px;
            }

            .logo span {
                font-size: 0.75rem;
            }

            .sidebar .nav-links a {
                font-size: 12px;
                padding: 0.35rem 0.5rem;
            }
        }

        @media (min-width: 1024px) {
            .top-navbar .top-navbar__inner {
                padding: .75rem 6rem;
            }
        }
        
        /* Explicitly ensure navbar controls are neutral (no gold accents) */
        .top-navbar .icon-button {
            border-color: #e9ecef !important;
            background-color: #ffffff !important;
            color: var(--dark-text) !important;
        }
        .top-navbar .icon-button svg {
            stroke: currentColor !important;
        }
        .top-navbar .avatar {
            border-color: #e9ecef !important;
            background-color: #e9f3f5 !important;
            color: var(--primary-color) !important;
        }
        .top-navbar .icon-button:focus,
        .top-navbar .avatar:focus {
            outline: none;
            box-shadow: none !important;
            border-color: #dde2e7 !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/dwell-logo.png') }}" alt="DWELL Logo" class="brand-mark">
                    <span>DWELL DERMATOLOGY</span>
                </div>
                <button class="hamburger" id="hamburger" aria-label="Toggle menu">☰</button>
            </div>
            <div class="sidebar-content">
                <div class="nav-links">
                    @yield('navbar-links')
                </div>
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="top-navbar">
                <div class="top-navbar__inner container">
                    <div class="top-navbar__left">
                        <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle menu">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </button>
                        @php
                            $name = auth()->user()->name ?? 'User';
                        @endphp
                        <div class="top-navbar__title">Welcome, {{ $name }}!</div>
                    </div>
                    <div class="top-navbar__right">
                        @php
                            $role = auth()->user()->role ?? null;
                            $notifUrl = $role === 'admin' ? route('admin.notifications.index') : ($role === 'doctor' ? route('doctor.notifications.index') : '#');
                            $profileUrl = $role === 'admin' ? route('admin.profile') : ($role === 'doctor' ? route('doctor.profile') : '#');
                            $name = auth()->user()->name ?? '';
                            $initials = collect(explode(' ', trim($name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->join('');
                            if ($initials === '') { $initials = 'U'; }
                        @endphp
                        <div class="crud-alert-indicator" id="crud-alert-indicator" role="status" aria-live="polite" aria-atomic="true" data-state="info">
                            <span class="crud-alert-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12.5" stroke-width="1.5" stroke-linecap="round"></line>
                                    <circle cx="12" cy="16" r="0.9" fill="currentColor"></circle>
                                </svg>
                            </span>
                            <span class="crud-alert-text">All caught up</span>
                        </div>
                        <div class="menu" id="profile-menu">
                            <button class="avatar" id="profile-toggle" aria-haspopup="true" aria-expanded="false" title="Profile">
                                {{ $initials }}
                            </button>
                            <div class="menu-panel" role="menu" aria-labelledby="profile-toggle">
                                <a href="{{ $profileUrl }}" role="menuitem">Update Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" role="menuitem">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <main class="main-content">
                <div class="container">
    @yield('content')
    @stack('scripts')
                </div>
            </main>
        </div>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        // Sidebar mobile toggle
        const sidebar = document.getElementById('sidebar');
        const hamburger = document.getElementById('hamburger');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        
        function toggleSidebar() {
            sidebar.classList.toggle('open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('active');
            }
            // Prevent body scroll when sidebar is open
            if (sidebar.classList.contains('open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
        
        function closeSidebar() {
            sidebar.classList.remove('open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
            document.body.style.overflow = '';
        }
        
        function setSidebarForViewport() {
            if (window.innerWidth <= 1024) {
                closeSidebar();
            } else {
                sidebar.classList.add('open');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            setSidebarForViewport();
            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }
        });
        
        window.addEventListener('resize', setSidebarForViewport);
        
        // Toggle sidebar from hamburger button (inside sidebar)
        if (hamburger) {
            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });
        }
        
        // Toggle sidebar from mobile menu button (in top navbar)
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });
        }
        
        // Close sidebar when clicking overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                closeSidebar();
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024) {
                if (sidebar.classList.contains('open') && 
                    !sidebar.contains(e.target) && 
                    !mobileMenuToggle.contains(e.target) &&
                    !hamburger.contains(e.target)) {
                    closeSidebar();
                }
            }
        });
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });

        // Profile dropdown toggle
        const profileMenu = document.getElementById('profile-menu');
        const profileToggle = document.getElementById('profile-toggle');
        document.addEventListener('click', function(e) {
            if (!profileMenu) { return; }
            if (profileToggle && profileToggle.contains(e.target)) {
                profileMenu.classList.toggle('open');
                const expanded = profileToggle.getAttribute('aria-expanded') === 'true';
                profileToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            } else if (!profileMenu.contains(e.target)) {
                profileMenu.classList.remove('open');
                if (profileToggle) profileToggle.setAttribute('aria-expanded', 'false');
            }
        });

        const crudAlertIndicator = document.getElementById('crud-alert-indicator');
        const crudAlertText = crudAlertIndicator ? crudAlertIndicator.querySelector('.crud-alert-text') : null;
        const crudAlertIcon = crudAlertIndicator ? crudAlertIndicator.querySelector('.crud-alert-icon') : null;
        let crudAlertTimer = null;

        function getCrudIcon(type) {
            switch (type) {
                case 'success':
                    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                        <path d="M8.5 12.5l2.5 2.5 4.5-5.5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>`;
                case 'error':
                    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                        <path d="M9 9l6 6m0-6l-6 6" stroke-width="1.8" stroke-linecap="round"></path>
                    </svg>`;
                default:
                    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                        <line x1="12" y1="8" x2="12" y2="12.5" stroke-width="1.5" stroke-linecap="round"></line>
                        <circle cx="12" cy="16" r="0.9" fill="currentColor"></circle>
                    </svg>`;
            }
        }

        function showCrudAlert(message, type = 'info', duration = 4000) {
            if (!crudAlertIndicator) {
                alert(message);
                return;
            }

            if (crudAlertIcon) {
                crudAlertIcon.innerHTML = getCrudIcon(type);
            }
            if (crudAlertText) {
                crudAlertText.textContent = message;
            }
            crudAlertIndicator.dataset.state = type;
            crudAlertIndicator.classList.add('visible');

            if (crudAlertTimer) {
                clearTimeout(crudAlertTimer);
            }
            crudAlertTimer = setTimeout(() => {
                crudAlertIndicator.classList.remove('visible');
            }, duration);
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showCrudAlert(@json(session('success')), 'success');
            @endif

            @if(session('error'))
                showCrudAlert(@json(session('error')), 'error');
            @endif
        });

        window.showNotification = showCrudAlert;

    </script>
</body>
</html>
