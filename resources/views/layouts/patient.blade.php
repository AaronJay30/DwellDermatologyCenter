<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dwell Dermatology Center') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @stack('styles')

    <style>
        :root {
            --primary-color: #197a8c;
            --accent-color: #ffd700;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --light-text: #6c757d;
            --teal-light: #e5f7fa;
            --teal-medium: #b8e7ef;
            --white: #ffffff;
            --gray-light: #f1f3f4;
            --gray-medium: #e9ecef;
            --gray-dark: #6c757d;
            --black: #000000;
            --green: #28a745;
            --pink: #e91e63;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --input-bg: #ffffff;
            --input-border: #ced4da;
            --modal-bg: #ffffff;
            --dropdown-bg: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.15);
        }

        [data-theme="dark"] {
            --light-bg: #1a1a1a;
            --dark-text: #ffffff;
            --light-text: #b0b0b0;
            --white: #2d2d2d;
            --gray-light: #3a3a3a;
            --gray-medium: #4a4a4a;
            --gray-dark: #888888;
            --black: #ffffff;
            --teal-light: #1a3a3f;
            --teal-medium: #2a5a5f;
            --card-bg: #2d2d2d;
            --border-color: #4a4a4a;
            --input-bg: #3a3a3a;
            --input-border: #4a4a4a;
            --modal-bg: #2d2d2d;
            --dropdown-bg: #2d2d2d;
            --shadow-color: rgba(0, 0, 0, 0.5);
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
            transition: all 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Top Bar */
        .top-bar {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 0;
            font-size: 12px;
        }

        /* Global price display styles */
        .price-display {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .price-display .price-original {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .price-display .price-promo {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.4rem;
        }

        .price-display .price-regular {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.3rem;
        }

        .price-display .price-discount {
            background: #27ae60;
            color: #fff;
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .price-display.price-compact .price-promo,
        .price-display.price-compact .price-regular {
            font-size: 1.1rem;
        }

        .price-display.price-compact .price-original {
            font-size: 0.85rem;
        }

        .top-bar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .contact-info {
            display: flex;
            gap: 20px;
            color: #b0b0b0;
        }

        .promo-message {
            font-weight: 500;
            text-align: left;
        }

        .top-bar-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            opacity: 0.8;
        }

        .theme-icon {
            font-size: 16px;
        }

        /* Main Navigation */
        .main-nav {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-img {
            width: 40px;
            height: 40px;
        }

        .logo-text {
            font-family: serif;
            font-size: 24px;
            font-weight: bold;
            color: var(--black);
        }

        .nav-menu {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-menu a {
            color: var(--black);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 0;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-menu a:hover {
            color: var(--primary-color);
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }

        .nav-menu a:hover::after,
        .nav-menu a.active::after {
            width: 100%;
        }

        .nav-menu a.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .nav-icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-icon {
            color: var(--black);
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-icon:hover {
            color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            font-weight: bold;
        }

        .notification-badge.show {
            display: flex;
        }
        
        .nav-menu a[href*="notifications"] {
            position: relative;
        }

        /* Settings Modal Styles */
        .modal-content button:hover {
            background-color: var(--light-bg) !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .modal-content button[type="submit"]:hover {
            background-color: #f8f9fa !important;
            border-color: #c82333 !important;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--teal-light), var(--teal-medium));
            padding: 60px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .hero-text h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--black);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-text p {
            color: var(--gray-dark);
            margin-bottom: 30px;
            font-size: 14px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1a6b7a;
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--black);
            border: 2px solid var(--gray-medium);
        }

        .btn-outline:hover {
            background-color: var(--gray-light);
            border-color: var(--gray-dark);
        }

        /* Shop by Category */
        .shop-categories {
            padding: 60px 0;
            background-color: var(--white);
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            color: var(--black);
            margin-bottom: 50px;
            font-family: serif;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .category-item {
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .category-item:hover {
            transform: translateY(-5px);
        }

        .category-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--white);
            border: 2px solid var(--gray-medium);
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .category-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
        }

        .category-name {
            font-size: 14px;
            color: var(--black);
            font-weight: 500;
        }

        /* Services Section */
        .services-section {
            padding: 60px 0;
            background-color: var(--light-bg);
        }

        .services-title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--black);
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .category-tabs {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .category-tabs {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                justify-content: flex-start;
                padding-bottom: 10px;
                gap: 10px;
            }

            .category-tabs::-webkit-scrollbar {
                height: 4px;
            }

            .category-tabs::-webkit-scrollbar-track {
                background: var(--gray-light);
                border-radius: 10px;
            }

            .category-tabs::-webkit-scrollbar-thumb {
                background: var(--primary-color);
                border-radius: 10px;
            }

            .tab-btn {
                flex-shrink: 0;
                white-space: nowrap;
                padding: 8px 16px;
                font-size: 0.85rem;
            }
        }

        .tab-btn {
            padding: 10px 20px;
            border: 2px solid var(--gray-medium);
            background-color: var(--white);
            color: var(--black);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .tab-btn.active,
        .tab-btn:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .service-card {
            background-color: var(--teal-light);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-tag {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .service-tag.discount {
            background-color: var(--green);
        }

        .service-tag.sold {
            background-color: var(--pink);
        }

        .service-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .service-name {
            font-size: 16px;
            font-weight: bold;
            color: var(--black);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-price {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .price-current {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .price-original {
            font-size: 14px;
            color: var(--gray-dark);
            text-decoration: line-through;
        }

        .price-single {
            font-size: 18px;
            font-weight: bold;
            color: var(--black);
        }

        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--black);
            cursor: pointer;
        }

        /* Additional polish */
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23197a8c" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23197a8c" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="%23197a8c" opacity="0.15"/><circle cx="10" cy="60" r="0.5" fill="%23197a8c" opacity="0.15"/><circle cx="90" cy="40" r="0.5" fill="%23197a8c" opacity="0.15"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        .service-card {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .service-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .category-item:hover .category-circle {
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(25, 122, 140, 0.2);
        }

        .btn {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .btn:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Common Elements - Dark Mode Support */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            color: var(--dark-text);
        }

        input, textarea, select {
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--dark-text);
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        /* Bottom Navigation Bar */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #197a8c;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 8px 0;
        }

        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 100%;
            padding: 0 10px;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #ffd700;
            flex: 1;
            padding: 8px 4px;
            transition: all 0.3s ease;
            position: relative;
        }

        .bottom-nav-item .notification-badge {
            position: absolute;
            top: 2px;
            right: 8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            min-width: 16px;
            height: 16px;
            font-size: 9px;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            font-weight: bold;
            border: 2px solid #197a8c;
        }

        .bottom-nav-item .notification-badge.show {
            display: flex;
        }

        .bottom-nav-item i {
            font-size: 20px;
            margin-bottom: 4px;
            color: #ffd700;
        }

        .bottom-nav-item span {
            font-size: 11px;
            font-weight: 500;
            color: #ffd700;
        }

        .bottom-nav-item.active {
            opacity: 1;
        }

        .bottom-nav-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background-color: #ffd700;
            border-radius: 0 0 3px 3px;
        }

        .bottom-nav-item:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        /* Top Right Icons Container */
        .top-right-icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .top-right-icon {
            color: var(--black);
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease;
            position: relative;
            text-decoration: none;
        }

        .top-right-icon:hover {
            color: var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .nav-menu {
                display: none;
            }

            .bottom-nav {
                display: block;
            }

            .nav-content {
                justify-content: space-between;
            }

            .top-right-icons {
                display: flex;
            }

            /* Add padding to main content to prevent overlap with bottom nav */
            main {
                padding-bottom: 70px;
            }

            /* Ensure top bar is visible but compact */
            .top-bar {
                font-size: 11px;
                padding: 6px 0;
            }

            .contact-info {
                font-size: 10px;
            }
        }

        @media (max-width: 900px) {
            .nav-content {
                padding: 12px 15px;
            }

            .logo-text {
                font-size: 18px;
            }
        }

        @media (max-width: 768px) {
            .top-bar-content {
                flex-direction: column;
                gap: 10px;
            }

            .contact-info {
                display: none;
            }

            .top-bar-main {
                width: 100%;
            }

            .nav-content {
                flex-direction: row;
                gap: 10px;
                padding: 10px 15px;
            }

            .logo-section {
                flex: 1;
            }

            .logo-text {
                font-size: 20px;
            }

            .logo-img {
                width: 35px;
                height: 35px;
            }

            .nav-menu {
                display: none;
            }

            .nav-icons {
                justify-content: flex-end;
                gap: 12px;
            }

            .top-right-icons {
                display: flex;
                gap: 12px;
            }

            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 2rem;
            }

            .hero-buttons {
                justify-content: center;
            }

            .categories-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
            }

            .category-circle {
                width: 70px;
                height: 70px;
            }

            .category-image {
                width: 50px;
                height: 50px;
            }

            .category-item i {
                font-size: 28px !important;
            }

            .category-name {
                font-size: 12px;
            }

            .services-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .service-card {
                padding: 12px;
            }

            .service-image {
                height: 120px;
            }

            .service-name {
                font-size: 0.95rem;
                margin-bottom: 6px;
            }

            .service-description {
                font-size: 0.75rem;
                line-height: 1.4;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
                margin-bottom: 10px;
            }

            .service-price {
                margin-bottom: 10px;
            }

            .service-actions {
                flex-direction: row;
                gap: 8px;
            }

            .service-actions .btn {
                padding: 6px 12px;
                font-size: 0.8rem;
                flex: 1;
            }

            .category-tabs {
                flex-direction: column;
                align-items: center;
            }

            /* Bottom nav adjustments for mobile */
            .bottom-nav-item i {
                font-size: 18px;
            }

            .bottom-nav-item span {
                font-size: 10px;
            }

            /* Search dropdown adjustments for mobile */
            .search-dropdown {
                right: -50px !important;
                min-width: 280px !important;
                max-width: calc(100vw - 40px) !important;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 15px;
            }

            .hero-section {
                padding: 40px 0;
            }

            .shop-categories,
            .services-section {
                padding: 40px 0;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .hero-text h1 {
                font-size: 1.8rem;
            }

            /* Hide contact info on very small screens */
            .contact-info {
                display: none;
            }

            .top-bar {
                padding: 4px 0;
                font-size: 10px;
            }

            .promo-message {
                font-size: 11px;
            }

            /* Compact bottom nav for very small screens */
            .bottom-nav {
                padding: 6px 0;
            }

            .bottom-nav-item {
                padding: 6px 2px;
            }

            .bottom-nav-item i {
                font-size: 16px;
            }

            .bottom-nav-item span {
                font-size: 9px;
            }
        }

        /* Responsive styles for edit profile form */
        @media (max-width: 480px) {
            #edit-profile-form .form-row {
                flex-direction: column !important;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="top-bar-main">
                <div class="promo-message">
                    Hi, {{ auth()->user()->name }}!
                </div>
                <div class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-sun theme-icon" id="theme-icon"></i>
                    <span id="theme-text">Light</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="main-nav">
        <div class="nav-content">
            <div class="logo-section">
                <img src="{{ asset('images/dwell-logo.png') }}" alt="DWELL Logo" class="logo-img">
                <div class="logo-text">DWELL</div>
            </div>
            
            <div class="nav-menu">
                <a href="{{ route('dashboard') }}">Home</a>
                <a href="{{ route('consultations.medical') }}">Consultation</a>
                <a href="{{ route('consultations.index') }}">Appointments</a>
                <a href="{{ route('patient.history') }}">History</a>
                <a href="{{ route('notifications.index') }}" style="position: relative;">
                    Notifications
                    <span class="notification-badge" id="notification-badge" style="display: none;">0</span>
                </a>
            </div>
            
            <div class="nav-icons">
                <div class="search-container" style="position: relative;">
                    <i class="fas fa-search nav-icon" id="search-toggle" style="cursor: pointer;"></i>
                    <div class="search-dropdown" id="search-dropdown" style="display: none; position: absolute; top: 100%; right: 0; background: var(--dropdown-bg); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px var(--shadow-color); z-index: 1000; min-width: 300px; max-width: 400px;">
                        <div style="padding: 15px; border-bottom: 1px solid var(--border-color);">
                            <input type="text" id="search-input" placeholder="Search services and categories..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--input-border); border-radius: 4px; font-size: 14px; background: var(--input-bg); color: var(--dark-text);" autocomplete="off">
                        </div>
                        <div id="search-results" style="max-height: 400px; overflow-y: auto;">
                            <div style="padding: 15px; text-align: center; color: var(--light-text);">
                                Start typing to search...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="top-right-icons">
                    <a href="{{ route('cart.index') }}" class="top-right-icon" style="position: relative; text-decoration: none;">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="notification-badge" id="cart-badge">0</span>
                    </a>
                </div>
                <i class="fas fa-cog nav-icon" id="settings-toggle" style="cursor: pointer;"></i>
            </div>
        </div>
    </nav>

    <!-- Bottom Navigation Bar (Mobile/Tablet) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-items">
            <a href="{{ route('dashboard') }}" class="bottom-nav-item" data-route="dashboard">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('consultations.medical') }}" class="bottom-nav-item" data-route="consultations.medical">
                <i class="fas fa-stethoscope"></i>
                <span>Consultation</span>
            </a>
            <a href="{{ route('consultations.index') }}" class="bottom-nav-item" data-route="consultations.index">
                <i class="fas fa-calendar-check"></i>
                <span>Appointments</span>
            </a>
            <a href="{{ route('patient.history') }}" class="bottom-nav-item" data-route="patient.history">
                <i class="fas fa-history"></i>
                <span>History</span>
            </a>
            <a href="{{ route('notifications.index') }}" class="bottom-nav-item" data-route="notifications.index">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                <span class="notification-badge" id="notification-badge-bottom">0</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; margin: 1rem 0; border-radius: 5px; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 1rem; margin: 1rem 0; border-radius: 5px; border: 1px solid #f5c6cb;">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Settings Modal -->
    <div id="settings-modal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1001; align-items: center; justify-content: center; padding: 20px;">
        <div class="modal-content" style="background: var(--modal-bg); border-radius: 10px; padding: 1.5rem; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px var(--shadow-color); color: var(--dark-text); margin-top: 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                <h2 style="margin: 0; color: var(--primary-color); font-size: 1.5rem;">Profile Settings</h2>
                <button onclick="closeSettingsModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--light-text);">&times;</button>
            </div>
            
            <!-- Profile Overview -->
            <div id="profile-overview" style="margin-bottom: 2rem;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; background: var(--primary-color); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; overflow: hidden; position: relative;">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}?t={{ time() }}" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            <i class="fas fa-user" style="font-size: 2rem; color: white;"></i>
                        @endif
                    </div>
                    <h3 style="margin: 0; color: var(--black); font-size: 1.2rem;">{{ auth()->user()->name }}</h3>
                    <p style="margin: 0.5rem 0 0; color: var(--gray-dark); font-size: 0.9rem;">{{ auth()->user()->email }}</p>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <button onclick="openEditProfile()" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--card-bg); cursor: pointer; transition: all 0.3s ease; text-align: left; width: 100%;">
                        <i class="fas fa-edit" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                        <div>
                            <div style="font-weight: 600; color: var(--black);">Edit Profile</div>
                            <div style="font-size: 0.9rem; color: var(--gray-dark);">Update your personal information</div>
                        </div>
                    </button>
                    
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #dc3545; border-radius: 8px; background: var(--card-bg); cursor: pointer; transition: all 0.3s ease; text-align: left; width: 100%;">
                            <i class="fas fa-sign-out-alt" style="color: #dc3545; font-size: 1.2rem;"></i>
                            <div>
                                <div style="font-weight: 600; color: #dc3545;">Logout</div>
                                <div style="font-size: 0.9rem; color: var(--gray-dark);">Sign out of your account</div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div id="edit-profile-form" style="display: none;">
                <div style="margin-bottom: 0.75rem;">
                    <h3 style="margin: 0; color: var(--primary-color); font-size: 1.1rem; margin-bottom: 0.4rem;">Edit Profile</h3>
                </div>
                
                <form id="profile-update-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Profile Photo Circle -->
                    <div style="text-align: center; margin-bottom: 0.75rem;">
                        <label style="display: block; margin-bottom: 0.4rem; font-weight: 600; color: var(--black); font-size: 0.85rem;">Profile Photo</label>
                        <div style="position: relative; display: inline-block; cursor: pointer;" onclick="document.getElementById('edit_photo').click()">
                            <div id="profile-photo-circle" style="width: 90px; height: 90px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary-color); display: inline-flex; align-items: center; justify-content: center; background: var(--primary-color);">
                                @if(auth()->user()->profile_photo)
                                    <img id="profile-photo-img" src="{{ asset('storage/' . auth()->user()->profile_photo) }}?t={{ time() }}" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <i class="fas fa-user" style="font-size: 2.2rem; color: white;" id="profile-photo-icon"></i>
                                @endif
                            </div>
                            <p style="margin-top: 0.4rem; color: var(--gray-dark); font-size: 0.8rem;">Click photo to change</p>
                        </div>
                        <input type="file" id="edit_photo" name="profile_photo" accept="image/*" style="display: none;">
                    </div>
                    
                    <div style="margin-bottom: 0.6rem;">
                        <label for="edit_name" style="display: block; margin-bottom: 0.3rem; font-weight: 600; color: var(--black); font-size: 0.85rem;">Full Name</label>
                        <input type="text" id="edit_name" name="name" value="{{ auth()->user()->name }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--input-border); border-radius: 5px; font-size: 0.9rem; background: var(--input-bg); color: var(--dark-text);" required>
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 0.6rem; margin-bottom: 0.6rem;">
                        <div style="flex: 1;">
                            <label for="edit_email" style="display: block; margin-bottom: 0.3rem; font-weight: 600; color: var(--black); font-size: 0.85rem;">Email</label>
                            <input type="email" id="edit_email" name="email" value="{{ auth()->user()->email }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--input-border); border-radius: 5px; font-size: 0.9rem; background: var(--input-bg); color: var(--dark-text);" required>
                        </div>
                        <div style="flex: 1;">
                            <label for="edit_phone" style="display: block; margin-bottom: 0.3rem; font-weight: 600; color: var(--black); font-size: 0.85rem;">Phone</label>
                            <input type="text" id="edit_phone" name="phone" value="{{ auth()->user()->phone }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--input-border); border-radius: 5px; font-size: 0.9rem; background: var(--input-bg); color: var(--dark-text);">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 0.75rem;">
                        <label for="edit_address" style="display: block; margin-bottom: 0.3rem; font-weight: 600; color: var(--black); font-size: 0.85rem;">Address</label>
                        <textarea id="edit_address" name="address" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid var(--input-border); border-radius: 5px; font-size: 0.9rem; resize: vertical; background: var(--input-bg); color: var(--dark-text);">{{ auth()->user()->address }}</textarea>
                    </div>
                    
                    <div style="display: flex; gap: 0.6rem; justify-content: flex-end; margin-top: 0.75rem;">
                        <button type="button" onclick="closeEditProfile()" style="padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: 5px; background: var(--card-bg); color: var(--black); cursor: pointer; font-weight: 600; font-size: 0.85rem;">Cancel</button>
                        <button type="submit" style="padding: 0.5rem 1rem; border: none; border-radius: 5px; background: var(--primary-color); color: white; cursor: pointer; font-weight: 600; font-size: 0.85rem;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')

    <script>
        // Theme Toggle Functionality
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');
            
            if (body.getAttribute('data-theme') === 'dark') {
                body.removeAttribute('data-theme');
                themeIcon.className = 'fas fa-sun theme-icon';
                themeText.textContent = 'Light';
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-moon theme-icon';
                themeText.textContent = 'Dark';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                document.getElementById('theme-icon').className = 'fas fa-moon theme-icon';
                document.getElementById('theme-text').textContent = 'Dark';
            }
        });

        // Update cart badge
        function updateCartBadge(countOverride) {
            const applyCount = (count) => {
                const badge = document.getElementById('cart-badge');
                if (!badge) return;
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.add('show');
                } else {
                    badge.classList.remove('show');
                }
            };

            // When count is already known, just update the badge
            if (typeof countOverride === 'number') {
                applyCount(countOverride);
                return;
            }

            // Otherwise fetch the latest count
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => applyCount(data.count || 0))
                .catch(error => console.error('Error fetching cart count:', error));
        }

        // Initialize cart badge and notification badge
        document.addEventListener('DOMContentLoaded', function() {
            updateCartBadge();
            updateNotificationBadge();
            initializeSearch();
            setActiveNavLink();
            initializeSettings();
            
            // Poll for new notifications every 5 seconds
            setInterval(updateNotificationBadge, 5000);
        });
        
        // Update notification badge
        function updateNotificationBadge() {
            fetch('{{ route("notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badges = [
                        document.getElementById('notification-badge'),
                        document.getElementById('notification-badge-bottom')
                    ];
                    const count = data.count || 0;
                    
                    badges.forEach(badge => {
                        if (badge) {
                            if (count > 0) {
                                badge.textContent = count > 99 ? '99+' : count;
                                badge.style.display = 'flex';
                                badge.classList.add('show');
                            } else {
                                badge.style.display = 'none';
                                badge.classList.remove('show');
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching notification count:', error));
        }

        // Set active navigation link
        function setActiveNavLink() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-menu a');
            const bottomNavItems = document.querySelectorAll('.bottom-nav-item');
            
            // Set active state for top navigation
            navLinks.forEach(link => {
                link.classList.remove('active');
                const linkPath = new URL(link.href).pathname;
                
                // Check if current path exactly matches the link path
                if (currentPath === linkPath || 
                    (linkPath === '/dashboard' && currentPath === '/')) {
                    link.classList.add('active');
                }
            });

            // Set active state for bottom navigation
            bottomNavItems.forEach(item => {
                item.classList.remove('active');
                if (item.href && item.href !== '#') {
                    try {
                        const itemPath = new URL(item.href).pathname;
                        const routeName = item.getAttribute('data-route');
                        
                        // Normalize paths (remove trailing slashes)
                        const normalizedCurrent = currentPath.replace(/\/$/, '') || '/';
                        const normalizedItem = itemPath.replace(/\/$/, '') || '/';
                        
                        // Check dashboard route
                        if (routeName === 'dashboard' && (normalizedCurrent === '/dashboard' || normalizedCurrent === '/')) {
                            item.classList.add('active');
                        }
                        // Check consultations.medical route
                        else if (routeName === 'consultations.medical' && normalizedCurrent.includes('/consultations/medical')) {
                            item.classList.add('active');
                        }
                        // Check consultations.index route
                        else if (routeName === 'consultations.index' && normalizedCurrent === '/consultations') {
                            item.classList.add('active');
                        }
                        // Check patient.history route
                        else if (routeName === 'patient.history' && normalizedCurrent === '/patient/history') {
                            item.classList.add('active');
                        }
                        // Check notifications.index route
                        else if (routeName === 'notifications.index' && normalizedCurrent === '/notifications') {
                            item.classList.add('active');
                        }
                        // Fallback to path matching
                        else if (normalizedCurrent === normalizedItem) {
                            item.classList.add('active');
                        }
                    } catch (e) {
                        // If href is not a valid URL (like #), skip
                    }
                }
            });
        }

        // Search functionality
        function initializeSearch() {
            const searchToggle = document.getElementById('search-toggle');
            const searchDropdown = document.getElementById('search-dropdown');
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            let searchTimeout;

            // Toggle search dropdown
            searchToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                searchDropdown.style.display = searchDropdown.style.display === 'none' ? 'block' : 'none';
                if (searchDropdown.style.display === 'block') {
                    searchInput.focus();
                }
            });

            // Close search dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchToggle.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.style.display = 'none';
                }
            });

            // Live search functionality
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    const lightText = getComputedStyle(document.documentElement).getPropertyValue('--light-text').trim();
                    searchResults.innerHTML = `<div style="padding: 15px; text-align: center; color: ${lightText};">Start typing to search...</div>`;
                    return;
                }

                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            function performSearch(query) {
                const lightText = getComputedStyle(document.documentElement).getPropertyValue('--light-text').trim();
                searchResults.innerHTML = `<div style="padding: 15px; text-align: center; color: ${lightText};"><i class="fas fa-spinner fa-spin"></i> Searching...</div>`;

                fetch(`{{ route('search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data, query);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<div style="padding: 15px; text-align: center; color: #dc3545;">Search failed. Please try again.</div>';
                    });
            }

            function displaySearchResults(data, query) {
                const root = document.documentElement;
                const lightText = getComputedStyle(root).getPropertyValue('--light-text').trim();
                const darkText = getComputedStyle(root).getPropertyValue('--dark-text').trim();
                const grayLight = getComputedStyle(root).getPropertyValue('--gray-light').trim();
                const borderColor = getComputedStyle(root).getPropertyValue('--border-color').trim();
                const cardBg = getComputedStyle(root).getPropertyValue('--card-bg').trim();
                const primaryColor = getComputedStyle(root).getPropertyValue('--primary-color').trim();
                
                let html = '';

                if (data.services.length === 0 && data.categories.length === 0) {
                    html = `<div style="padding: 15px; text-align: center; color: ${lightText};">No results found for "${query}"</div>`;
                } else {
                    // Display categories first
                    if (data.categories.length > 0) {
                        html += `<div style="padding: 10px 15px; background: ${grayLight}; border-bottom: 1px solid ${borderColor}; font-weight: 600; color: ${darkText}; font-size: 12px; text-transform: uppercase;">Categories</div>`;
                        data.categories.forEach(category => {
                            html += `
                                <a href="${category.url}" style="display: block; padding: 12px 15px; text-decoration: none; color: ${darkText}; border-bottom: 1px solid ${borderColor}; transition: background-color 0.2s;">
                                    <div style="font-weight: 500;">${category.name}</div>
                                    ${category.description ? `<div style="font-size: 12px; color: ${lightText}; margin-top: 2px;">${category.description}</div>` : ''}
                                </a>
                            `;
                        });
                    }

                    // Display services
                    if (data.services.length > 0) {
                        if (data.categories.length > 0) {
                            html += `<div style="padding: 10px 15px; background: ${grayLight}; border-bottom: 1px solid ${borderColor}; font-weight: 600; color: ${darkText}; font-size: 12px; text-transform: uppercase;">Services</div>`;
                        }
                        data.services.forEach(service => {
                            const price = service.discount_percentage > 0 
                                ? `<span style="color: #28a745; font-weight: 600;">$${service.price}</span> <span style="text-decoration: line-through; color: ${lightText}; font-size: 12px;">$${service.price}</span>`
                                : `<span style="color: ${primaryColor}; font-weight: 600;">$${service.price}</span>`;
                            
                            html += `
                                <a href="${service.url}" style="display: block; padding: 12px 15px; text-decoration: none; color: ${darkText}; border-bottom: 1px solid ${borderColor}; transition: background-color 0.2s;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        ${service.image ? `<img src="${service.image}" alt="${service.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">` : `<div style="width: 40px; height: 40px; background: ${grayLight}; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-spa" style="color: ${lightText};"></i></div>`}
                                        <div style="flex: 1;">
                                            <div style="font-weight: 500; margin-bottom: 2px;">${service.name}</div>
                                            <div style="font-size: 12px; color: ${lightText}; margin-bottom: 2px;">${service.category}</div>
                                            <div style="font-size: 12px; color: ${lightText};">${service.description.substring(0, 50)}${service.description.length > 50 ? '...' : ''}</div>
                                        </div>
                                        <div style="text-align: right; font-size: 14px;">
                                            ${price}
                                        </div>
                                    </div>
                                </a>
                            `;
                        });
                    }
                }

                searchResults.innerHTML = html;

                // Add hover effects
                searchResults.querySelectorAll('a').forEach(link => {
                    link.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = grayLight;
                    });
                    link.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = 'transparent';
                    });
                });
            }
        }

        // Settings Modal Functions
        function initializeSettings() {
            const settingsToggle = document.getElementById('settings-toggle');
            const settingsModal = document.getElementById('settings-modal');

            settingsToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                settingsModal.style.display = 'flex';
            });

            // Close modal when clicking outside
            settingsModal.addEventListener('click', function(e) {
                if (e.target === settingsModal) {
                    closeSettingsModal();
                }
            });

            // Close modal with escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && settingsModal.style.display === 'flex') {
                    closeSettingsModal();
                }
            });
        }

        function closeSettingsModal() {
            document.getElementById('settings-modal').style.display = 'none';
        }

        function openEditProfile() {
            document.getElementById('profile-overview').style.display = 'none';
            document.getElementById('edit-profile-form').style.display = 'block';
        }

        function closeEditProfile() {
            document.getElementById('edit-profile-form').style.display = 'none';
            document.getElementById('profile-overview').style.display = 'block';
        }

        // Photo preview functionality - update circle photo
        const editPhotoInput = document.getElementById('edit_photo');
        if (editPhotoInput) {
            editPhotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const photoCircle = document.getElementById('profile-photo-circle');
                        if (!photoCircle) return;
                        
                        const photoImg = document.getElementById('profile-photo-img');
                        
                        if (photoImg) {
                            // Update existing image
                            photoImg.src = e.target.result;
                            photoImg.style.display = 'block';
                        } else {
                            // Create new img element and replace icon
                            const img = document.createElement('img');
                            img.id = 'profile-photo-img';
                            img.src = e.target.result;
                            img.alt = 'Profile Photo';
                            img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                            photoCircle.innerHTML = '';
                            photoCircle.appendChild(img);
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Handle profile update form submission
        document.getElementById('profile-update-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            // Show loading state
            submitButton.textContent = 'Saving...';
            submitButton.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification(data.message, 'success');
                    
                    // Update the profile overview with new data
                    document.querySelector('#profile-overview h3').textContent = formData.get('name');
                    document.querySelector('#profile-overview p').textContent = formData.get('email');
                    
                    // Close edit form and return to overview
                    closeEditProfile();
                    
                    // Reload page after a short delay to show updated data from server
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating your profile', 'error');
            })
            .finally(() => {
                // Reset button state
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            });
        });

        // Show notification function
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 5px;
                color: white;
                font-weight: 500;
                z-index: 10000;
                max-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;
            
            if (type === 'success') {
                notification.style.backgroundColor = '#28a745';
            } else {
                notification.style.backgroundColor = '#dc3545';
            }
            
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>
