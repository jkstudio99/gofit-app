<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GoFit') }} - @yield('title', 'สุขภาพดีด้วยการวิ่ง')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert-custom.css') }}">

    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mobile-first.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app-tour.css') }}" rel="stylesheet">
    <link href="{{ asset('css/rewards-mobile.css') }}" rel="stylesheet">
    <link href="{{ asset('css/events-mobile.css') }}" rel="stylesheet">
    <link href="{{ asset('css/profile-mobile.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* Mobile-first styles */
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 0.95rem;
            overflow-x: hidden;
            background-color: #f8f8f8;
            padding: 0;
            margin: 0;
        }

        /* Profile image upload styling */
        .profile-upload-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
            margin: 0 auto 20px auto;
            border: 3px solid #f0f0f0;
            background-color: #f8f8f8;
        }

        .profile-upload-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-upload-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background-color: #2ecc71;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50% !important;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            transition: all 0.2s ease;
            z-index: 10;
            padding: 0;
            overflow: hidden;
        }

        .profile-upload-icon i {
            font-size: 1.2rem;
            line-height: 1;
            display: block;
            text-align: center;
            width: 100%;
        }

        .profile-upload-icon:hover {
            transform: scale(1.1);
            background-color: #27ae60;
        }

        /* Brand color variables */
        :root {
            --primary-color: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #a5e6c0;
            --accent-color: #3498db;
            --text-color: #333;
            --text-light: #6c757d;
            --background-light: #f8f9fa;
            --white: #ffffff;
        }

        /* Compact navbar for mobile - reduced height to match red line */
        .navbar {
            position: relative;
            z-index: 1030 !important;
            padding: 0.2rem 1rem;
            background: var(--white) !important;
            border-bottom: none !important;
            box-shadow: none !important;
            height: 50px;
            display: flex;
            align-items: center;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            height: 50px;
            padding: 0;
            margin-right: 0.5rem;
        }

        .navbar-brand img {
            height: 32px !important;
        }

        /* Hamburger button styling - smaller for mobile */
        .navbar-toggler {
            padding: 0.25rem;
            border: none;
            outline: none !important;
            box-shadow: none !important;
        }

        .navbar-toggler-icon {
            width: 20px;
            height: 20px;
        }

        /* Mobile optimized dropdown menu */
        .dropdown-menu {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
            display: none;
            z-index: 9999 !important;
        }

        .dropdown-menu.show {
            display: block !important;
        }

        .dropdown-item {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }

        /* Compact content area */
        main {
            padding: 0.5rem 0;
        }

        main .container {
            padding-left: 0;
            padding-right: 0;
        }

        /* Compact cards and content */
        .card {
            margin-bottom: 0.5rem;
            border-radius: 0.75rem;
        }

        .card-header {
            padding: 0.5rem 1rem;
        }

        .card-body {
            padding: 20px;
        }

        /* Bottom navigation bar for mobile */
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: var(--white);
            border-top: 1px solid #eee;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-around;
            padding: 0.5rem 0;
            z-index: 1020;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.65rem;
            color: var(--text-light);
            text-decoration: none;
            padding: 0.2rem;
        }

        .mobile-nav-item.active {
            color: var(--primary-color);
        }

        .mobile-nav-icon {
            font-size: 1.1rem;
            margin-bottom: 0.1rem;
        }

        /* Adjusted for 3-column stats on mobile */
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin: 1rem 15px 1rem 15px;
            background-color: transparent;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
            gap: 15px;
            width: calc(100% - 30px); /* Ensure full width minus margins */
        }

        .stat-col {
            flex: 0 0 32%;
            text-align: center;
            padding: 15px 10px !important;
            border: none;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .stat-icon {
            margin-bottom: 8px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            font-size: 28px;
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 2px;
            color: #333;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #777;
            line-height: 1.2;
        }

        /* Adjust footer for mobile bottom nav */
        .gofit-footer {
            background-color: #fff;
            padding: 5px 0;
            margin-top: 0.5rem;
            margin-bottom: 3.5rem; /* Space for bottom nav */
            border-top: none; /* ลบเส้นขอบด้านบนของ footer */
        }

        .footer-bottom {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .footer-text {
            display: inline-block;
            border-bottom: none; /* ลบเส้นใต้ชื่อใน footer */
            padding-bottom: 0;
        }

        /* Desktop navbar on md screens and up */
        @media (min-width: 768px) {
            /* Increased navbar padding for desktop */
            .navbar {
                padding: 0.6rem 1.5rem !important;
                min-height: 70px !important;
                height: 70px !important;
            }

            .navbar-brand {
                height: 70px !important;
            }

            .navbar-brand img {
                height: 36px !important;
            }

            /* Better padding for nav links - improved clickability */
            .desktop-nav .nav-link {
                padding: 0.8rem 1.2rem !important;
                margin: 0 0.2rem;
                border-radius: 8px;
                transition: all 0.2s ease;
            }

            .desktop-nav .nav-link:hover {
                background-color: rgba(46, 204, 113, 0.1);
            }

            .desktop-nav .nav-link.active {
                background-color: rgba(46, 204, 113, 0.15);
                color: #2ecc71;
                font-weight: 500;
            }

            .navbar-expand-md .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }

            .navbar-expand-md .navbar-nav {
                flex-direction: row;
            }

            /* Ensures desktop navbar links are displayed properly */
            .desktop-nav {
                display: flex !important;
            }

            /* Adjust dropdown positioning */
            .navbar-expand-md .navbar-nav .dropdown-menu {
                position: absolute;
            }
        }

        /* Mid-size screens (tablets and small desktops) optimization */
        @media (min-width: 768px) and (max-width: 1160px) {
            /* Uniform styling for ALL nav items */
            .desktop-nav .nav-link,
            .desktop-nav .nav-item > a,
            .desktop-nav .dropdown > a {
                padding: 0.5rem 0.6rem !important;
                margin: 0 0.1rem !important;
                font-size: 0.85rem !important;
                text-align: center !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: 40px !important;
            }

            /* Override any specific styling for nav items */
            .desktop-nav .nav-link.dropdown-toggle,
            .nav-link.dropdown-toggle {
                padding: 0.5rem 0.6rem !important;
            }

            /* Ensure navbar doesn't overflow */
            .navbar > .container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                justify-content: flex-start !important;
                gap: 5px !important;
            }

            /* Make logo visible and properly sized */
            .navbar-brand {
                margin-right: 0 !important;
                display: flex !important;
                align-items: center !important;
                padding-right: 0.5rem !important;
                min-width: 95px !important;
            }

            .navbar-brand img {
                height: 26px !important;
                display: block !important;
                visibility: visible !important;
            }

            /* Adjust user dropdown - always consistent */
            #userDropdown {
                padding: 0.5rem 0.6rem !important;
                margin-left: auto !important;
            }

            /* Ensure no text wrapping and proper alignment */
            .navbar-nav .nav-link {
                white-space: nowrap !important;
                overflow: visible !important;
            }

            /* Better spacing for navbar elements */
            .navbar-expand-md .navbar-nav {
                gap: 0 !important;
                display: flex !important;
                align-items: center !important;
            }

            /* Force consistent styling on all nav items */
            .desktop-nav {
                margin-left: 0 !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
            }

            /* Icons inside nav items */
            .desktop-nav .nav-link i {
                margin-right: 4px !important;
                font-size: 0.9rem !important;
            }

            /* Ensuring ALL nav-items are identical */
            .desktop-nav .nav-item {
                display: flex !important;
                align-items: center !important;
                height: 40px !important;
            }
        }

        /* Hide top navbar links on mobile, show in sidebar */
        @media (max-width: 767.98px) {
            /* Header optimizations for mobile - much smaller height */
            .navbar {
                padding: 0 0.8rem;
                min-height: 50px;
                height: 50px;
                display: flex;
                align-items: center;
            }

            /* Increase logo size for mobile */
            .navbar-brand {
                height: 50px;
                display: flex;
                align-items: center;
                padding: 8px 0;
            }

            .navbar-brand img {
                height: 36px !important; /* Increased from 24px */
                max-width: none !important;
            }

            /* Adjust container for better logo positioning */
            .navbar > .container {
                padding-left: 0;
            }

            /* Smaller hamburger button */
            .navbar-toggler {
                padding: 0.15rem !important;
            }

            .navbar-toggler-icon {
                width: 18px;
                height: 18px;
            }

            /* ลด margin ส่วนล่าง */
            main {
                padding-bottom: 0.5rem !important;
                padding-top: 0.25rem !important;
                margin-bottom: 0 !important;
            }

            .gofit-footer {
                margin-top: 0 !important;
                margin-bottom: 3.5rem !important;
                padding: 5px 0 !important;
            }

            /* เพิ่ม spacing ระหว่างรายการในหน้าแดชบอร์ด */
            .recent-activities-section, .weekly-progress-section {
                margin-bottom: 0.5rem !important;
                padding: 15px !important;
            }

            /* ลดช่องว่างของ welcome header */
            .welcome-header {
                padding: 1.25rem !important;
                margin-bottom: 0.75rem !important;
            }

            /* ลดช่องว่างของ stat cards */
            .stat-col {
                padding: 10px 8px !important;
            }

            /* ปรับ spacing ของ activity items */
            .activity-item {
                padding: 18px !important;
                margin-bottom: 8px !important;
            }

            .desktop-nav {
                display: none;
            }

            .navbar-collapse {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 80%;
                max-width: 300px;
                padding: 1rem;
                background: white;
                z-index: 1040;
                transition: transform 0.3s ease;
                transform: translateX(-100%);
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                overflow-y: auto;
            }

            .navbar-collapse.show {
                transform: translateX(0);
                display: block !important;
                visibility: visible !important;
            }

            /* Mobile optimized dropdown menu */
            .dropdown-menu {
                position: static !important;
                width: 100%;
                margin-top: 0.5rem;
                margin-bottom: 0.5rem;
                box-shadow: none;
                border: none;
                background-color: #f8f9fa;
                padding: 0.5rem;
            }

            .dropdown-item {
                padding: 0.75rem 1rem;
                border-radius: 0.5rem;
            }

            .dropdown-item:hover, .dropdown-item:focus {
                background-color: rgba(46, 204, 113, 0.1);
            }

            /* Mobile sidebar header with user info */
            .user-info {
                display: flex;
                align-items: center;
                margin-top: 10px;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #eee;
            }

            .container {
                padding-left: 0;
                padding-right: 0;
            }

            .dashboard-container {
                padding-left: 0;
                padding-right: 0;
            }

            /* Adjust vertical alignment for navbar content */
            .navbar-brand, .navbar-toggler {
                display: flex;
                align-items: center;
            }

            /* Adjust space after header */
            main {
                padding-top: 0.2rem !important;
            }

            /* Make all section spacing consistent on mobile */
            .welcome-header,
            .stats-row,
            .weekly-progress-section,
            .recent-activities-section,
            .run-history-section {
                margin: 1rem 15px 1rem 15px !important;
            }
        }

        /* Show top navbar on desktop, hide mobile elements */
        @media (min-width: 768px) {
            .mobile-nav {
                display: none;
            }

            .desktop-nav {
                display: flex;
            }

            .gofit-footer {
                margin-bottom: 0;
            }

            /* Ensure desktop navbar is always visible */
            .navbar-collapse {
                display: flex !important;
            }

            /* Style for user dropdown on desktop */
            #userDropdown {
                font-weight: 500;
            }

            #userDropdown img, #userDropdown i {
                margin-right: 5px;
            }
        }

        /* Fix z-index for map container */
        #map {
            z-index: 1 !important;
        }

        /* Button styling */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Welcome header with full width */
        .welcome-header {
            background: linear-gradient(135deg, #08c7a5, #2ecc71);
            color: white;
            border-radius: 20px;
            margin: 1rem 15px 1rem 15px;
            padding: 2rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
        }

        .welcome-header .welcome-text {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }

        .welcome-header .btn {
            border: none;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Improved button styling */
        .welcome-header .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        @media (min-width: 768px) {
            .welcome-header .btn {
                padding: 0.8rem 2rem !important;
                font-size: 1.2rem !important;
                margin-top: 0.5rem;
            }
        }

        /* Weekly Progress Section */
        .weekly-progress-section {
            background-color: #fff;
            border-radius: 20px;
            padding: 20px;
            margin: 0.5rem 15px 0.5rem 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: none;
        }

        .section-header {
            margin-bottom: 15px;
        }

        .section-header h5 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .section-header h5 i {
            color: #2ecc71;
            margin-right: 8px;
        }

        .goal-info {
            margin-bottom: 15px;
        }

        .goal-label {
            font-size: 0.85rem;
            color: #555;
        }

        .goal-target {
            display: flex;
            align-items: center;
        }

        .current-value {
            font-weight: 700;
            font-size: 0.9rem;
            color: #2ecc71;
        }

        .target-value {
            font-size: 0.8rem;
            color: #777;
            margin-left: 4px;
        }

        .goal-unit {
            font-size: 0.8rem;
            color: #777;
            margin-left: 4px;
        }

        .progress-bar-container {
            margin-bottom: 5px;
        }

        .progress-bar-custom {
            height: 10px;
            background-color: #f1f1f1;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .progress-fill {
            height: 100%;
            background-color: #2ecc71;
            border-radius: 10px;
        }

        .progress-percentage {
            font-size: 0.7rem;
            color: #777;
            text-align: right;
        }

        .weekly-details {
            margin-top: 15px;
        }

        .weekly-detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px 12px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .weekly-detail-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .weekly-detail-icon.distance {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .weekly-detail-icon.calories {
            background-color: rgba(255, 94, 87, 0.1);
            color: #ff5e57;
        }

        .weekly-detail-label {
            font-size: 0.8rem;
            color: #666;
            flex: 1;
        }

        .weekly-detail-value {
            font-weight: 600;
            font-size: 0.85rem;
            color: #333;
        }

        /* Recent Activities Section */
        .recent-activities-section {
            background-color: #fff;
            border-radius: 20px;
            padding: 20px;
            margin: 0.5rem 15px 0.5rem 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: none;
        }

        /* Run History Section */
        .run-history-section {
            background-color: #fff;
            border-radius: 20px;
            padding: 20px;
            margin: 0.5rem 15px 0.5rem 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: none;
        }

        .view-all-link {
            color: #2ecc71;
            font-size: 0.8rem;
            text-decoration: none;
        }

        .view-all-link i {
            font-size: 0.7rem;
            margin-left: 2px;
        }

        .activities-list {
            margin-top: 15px;
        }

        .activity-item {
            padding: 18px !important;
            border-radius: 12px;
            background-color: #f9f9f9;
            margin-bottom: 10px;
            position: relative;
        }

        .activity-date {
            font-size: 0.75rem;
            color: #777;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .activity-details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .activity-stat {
            display: flex;
            align-items: center;
            flex: 1 1 30%;
            min-width: 90px;
        }

        .activity-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-size: 0.8rem;
        }

        .activity-icon.distance {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .activity-icon.time {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        .activity-icon.calories {
            background-color: rgba(255, 94, 87, 0.1);
            color: #ff5e57;
        }

        .activity-value {
            font-size: 0.85rem;
            font-weight: 600;
            color: #444;
        }

        .activity-actions {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .empty-activities {
            text-align: center;
            padding: 30px 0;
            color: #777;
        }

        .empty-activities p {
            margin-bottom: 15px;
        }

        /* Desktop stats cards */
        .stat-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 15px 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            text-align: center;
            height: 100%;
            border: none;
        }

        .stat-card .stat-icon {
            margin-bottom: 8px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card .stat-icon i {
            font-size: 28px;
        }

        .stat-card .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 2px;
            color: #333;
        }

        .stat-card .stat-label {
            font-size: 0.75rem;
            color: #777;
            line-height: 1.2;
        }

        /* Mobile sidebar styling */
        .navbar-nav .nav-item.d-md-none .nav-link {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 5px;
            color: #444;
            transition: all 0.2s ease;
        }

        .navbar-nav .nav-item.d-md-none .nav-link:hover {
            background-color: rgba(46, 204, 113, 0.1);
        }

        .navbar-nav .nav-item.d-md-none .nav-link.active {
            background-color: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            font-weight: 500;
        }

        .navbar-nav .nav-item.d-md-none .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 8px;
        }

        .navbar-nav .nav-item.d-md-none .nav-link.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        /* ปุ่มปิดเมนู X ที่มุมบนขวา */
        .btn-close {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 50%;
            padding: 6px;
            opacity: 0.7;
            transition: all 0.2s ease;
        }

        .btn-close:hover {
            opacity: 1;
            background-color: rgba(0, 0, 0, 0.1);
        }

        /* Compact header for mobile */
        .gofit-header {
            margin-bottom: 0;
        }

        /* Remove horizontal divider lines */
        .card, .section, .welcome-header, .weekly-progress-section,
        .recent-activities-section, .run-history-section {
            border: none !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        /* Remove any divider borders throughout the app */
        hr, .border-top, .border-bottom {
            display: none !important;
        }

        /* Set consistent section backgrounds */
        .section-bg {
            background-color: #fff;
            border-radius: 20px;
        }

        /* Card with no border */
        .card {
            border: none !important;
        }

        /* Activity items with consistent spacing */
        .activity-item {
            margin-bottom: 0.5rem !important;
        }

        /* Last item in any list should not have bottom margin */
        .activity-item:last-child,
        .weekly-detail-item:last-child {
            margin-bottom: 0 !important;
        }

        /* Remove any unwanted borders */
        .user-info {
            border-bottom: none !important;
        }

        /* Remove all horizontal separators in mobile view */
        @media (max-width: 767.98px) {
            .border-bottom, .border-top {
                border: none !important;
            }

            /* Consistent card spacing */
            .card, .section, .welcome-header, .weekly-progress-section,
            .recent-activities-section, .run-history-section {
                margin-bottom: 1rem !important;
                margin-top: 1rem !important;
            }

            /* Remove double margins between sections */
            .stats-row + .weekly-progress-section,
            .weekly-progress-section + .recent-activities-section,
            .recent-activities-section + .run-history-section {
                margin-top: 1rem !important;
            }

            /* Increase navbar bottom spacing */
            .navbar {
                margin-bottom: 1rem !important;
            }

            /* Increase padding in all sections */
            .weekly-progress-section,
            .recent-activities-section,
            .run-history-section {
                padding: 22px !important;
            }

            /* Increase padding in stats */
            .stat-col {
                padding: 18px 12px !important;
            }
        }

        /* Remove shadow from navbar */
        .navbar {
            box-shadow: none !important;
        }

        /* Add consistent container padding */
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Set consistent vertical spacing between all sections */
        .section, .card, .welcome-header, .stats-row,
        .weekly-progress-section, .recent-activities-section, .run-history-section {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        /* Stats row items with consistent spacing */
        .stat-col {
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        /* Adjust padding in card sections */
        .card-body, .section-content {
            padding: 20px;
        }

        /* Remove margin bottom from last elements */
        *:last-child {
            margin-bottom: 0;
        }

        /* Mobile-specific adjustments */
        @media (max-width: 767.98px) {
            /* Remove all border styling from mobile elements */
            .navbar, .card, .section, .welcome-header,
            .weekly-progress-section, .recent-activities-section,
            .run-history-section, .stat-col {
                border: none !important;
                box-shadow: 0 2px 5px rgba(0,0,0,0.03) !important;
            }

            /* Ensure no horizontal lines between sections */
            .mobile-nav {
                border-top: none !important;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.05) !important;
            }
        }

        /* Increase padding in component sections */
        .weekly-progress-section,
        .recent-activities-section,
        .run-history-section {
            padding: 20px;
        }

        /* Increase padding in activity items */
        .activity-item {
            padding: 18px !important;
        }

        /* Increase spacing between stats */
        .stats-row {
            gap: 15px;
        }

        /* Remove duplicate stats-row definition */
        /* Increase spacing between stats */
        .stat-col {
            padding: 15px 10px !important;
        }

        /* Stats row with consistent spacing */
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin: 1rem 15px 1rem 15px;
            background-color: transparent;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
            gap: 15px;
            width: calc(100% - 30px); /* Ensure full width minus margins */
        }

        /* Ensure all content sections have consistent margins */
        .welcome-header,
        .stats-row,
        .weekly-progress-section,
        .recent-activities-section,
        .run-history-section,
        .section,
        .card-container {
            margin-left: 15px !important;
            margin-right: 15px !important;
        }

        @media (max-width: 767.98px) {
            /* Ensure consistent horizontal alignment */
            .container, .container-fluid {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* Ensure all content has same horizontal spacing */
            .welcome-header,
            .stats-row,
            .weekly-progress-section,
            .recent-activities-section,
            .run-history-section,
            .section,
            .card-container {
                margin-left: 15px !important;
                margin-right: 15px !important;
                width: calc(100% - 30px) !important;
            }
        }

        /* Custom fixes for stats row margins */
        .stats-row {
            margin-left: 15px !important;
            margin-right: 15px !important;
            width: calc(100% - 30px) !important;
        }

        /* Custom fixes for header height with better UX/UI */
        .gofit-header {
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            min-height: auto !important;
            box-shadow: none !important;
            border: none !important;
            background: none !important;
        }

        /* Force navbar height but allow for bigger logo */
        .navbar {
            height: 80px !important;
            min-height: 80px !important;
            max-height: 80px !important;
            overflow: visible !important;
        }

        /* User profile image styling */
        .user-profile-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            border: 1px solid #f0f0f0;
            background-color: #f8f8f8;
        }

        .profile-dropdown-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #f0f0f0;
            background-color: #f8f8f8;
        }

        /* Mobile sidebar user image */
        .sidebar-profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Improve hamburger button styling */
        .navbar-toggler {
            padding: 8px !important;
            margin-top: 0 !important;
            margin-right: 5px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .navbar-toggler-icon {
            width: 24px !important;
            height: 24px !important;
        }

        /* Ensure hamburger is only visible on mobile */
        @media (min-width: 768px) {
            .navbar-toggler {
                display: none !important;
            }

            /* Always show navbar on desktop */
            .navbar-collapse {
                display: flex !important;
                flex-basis: auto !important;
                transform: none !important;
                position: static !important;
                width: auto !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            /* Increase clickable area for navbar dropdown buttons */
            .navbar .dropdown-toggle {
                padding: 0.8rem 1.2rem !important;
            }

            /* Improved dropdown styling */
            .navbar .dropdown-menu {
                border-radius: 12px !important;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
                padding: 0.8rem 0 !important;
                margin-top: 0.5rem !important;
                min-width: 220px !important;
            }

            .navbar .dropdown-item {
                padding: 0.7rem 1.5rem !important;
                transition: all 0.2s ease !important;
            }

            .navbar .dropdown-item:hover {
                background-color: rgba(46, 204, 113, 0.1) !important;
                transform: translateX(5px) !important;
            }
        }

        /* Main navbar flex container */
        .navbar > .container {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        /* Make sure content starts immediately after navbar */
        main {
            padding-top: 0.5rem !important;
            margin-top: 0 !important;
        }

        /* Add some room after navbar */
        .gofit-header + * {
            margin-top: 0.5rem !important;
        }
    </style>

    <!-- Custom fixes for margins and alignment -->
    <style>
        /* Adjust stats-row with fixed width and margins */
        .stats-row {
            margin-left: 15px !important;
            margin-right: 15px !important;
            width: calc(100% - 30px) !important;
            gap: 15px;
            background-color: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        /* Consistent card styling */
        .welcome-header,
        .weekly-progress-section,
        .recent-activities-section,
        .run-history-section {
            margin-left: 15px !important;
            margin-right: 15px !important;
            width: calc(100% - 30px) !important;
        }

        /* Better stat column spacing */
        .stat-col {
            flex: 1 1 auto;
            max-width: 33.333%;
        }
    </style>

    <!-- User profile image styling -->
    <style>
        .user-profile-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            border: 1px solid #f0f0f0;
            background-color: #f8f8f8;
        }

        .profile-dropdown-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #f0f0f0;
            background-color: #f8f8f8;
        }

        .sidebar-profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #f0f0f0;
            background-color: #f8f8f8;
        }
    </style>

    @yield('styles')
</head>
<body>
    <div id="app">
        <!-- Header -->
        <header class="gofit-header">
            <nav class="navbar navbar-expand-md navbar-light bg-white">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/dashboard') }}">
                        <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="Logo" height="40">
                    </a>

                    <!-- Mobile only hamburger menu - hidden on desktop -->
                    <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto desktop-nav">
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <i class="fas fa-home"></i> แดชบอร์ด
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('run.*') ? 'active' : '' }}" href="{{ route('run.index') }}">
                                        <i class="fas fa-running"></i> เริ่มวิ่ง
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('badges.*') ? 'active' : '' }}" href="#" id="badgesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-medal"></i> เหรียญตรา
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="badgesDropdown">
                                        <li><a class="dropdown-item" href="{{ route('badges.index') }}"><i class="fas fa-medal me-2"></i> เหรียญตราทั้งหมด</a></li>
                                        <li><a class="dropdown-item" href="{{ route('badges.history') }}"><i class="fas fa-history me-2"></i> ประวัติการได้รับเหรียญตรา</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('rewards.*') ? 'active' : '' }}" href="#" id="rewardsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-gift"></i> รางวัล
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="rewardsDropdown">
                                        <li><a class="dropdown-item" href="{{ route('rewards.index') }}"><i class="fas fa-gift me-2"></i> รางวัลทั้งหมด</a></li>
                                        <li><a class="dropdown-item" href="{{ route('rewards.history') }}"><i class="fas fa-history me-2"></i> ประวัติการแลกรางวัล</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                                        <i class="fas fa-calendar-alt"></i> กิจกรรม
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}" href="{{ route('goals.index') }}">
                                        <i class="fas fa-bullseye"></i> เป้าหมาย
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('health-articles.*') ? 'active' : '' }}" href="{{ route('health-articles.index') }}">
                                        <i class="fas fa-heartbeat"></i> บทความสุขภาพ
                                    </a>
                                </li>
                            @endauth
                        </ul>

                        <!-- Sidebar Menu for Mobile -->
                        <ul class="navbar-nav">
                            @auth
                                <li class="nav-item d-md-none">
                                    <div class="user-info mb-3 pb-3 position-relative border-bottom">
                                        <div class="d-flex align-items-center">
                                            @if(Auth::user()->profile_image)
                                                <img src="{{ asset('profile_images/' . Auth::user()->profile_image) }}" alt="Profile" class="sidebar-profile-image me-2">
                                            @else
                                                <i class="fas fa-user-circle fa-2x me-2 text-muted"></i>
                                            @endif
                                            <span class="fw-bold">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</span>
                                        </div>
                                        <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2" aria-label="Close" onclick="document.querySelector('.navbar-collapse').classList.remove('show'); document.querySelector('.navbar-collapse').style.transform = 'translateX(-100%)';"></button>
                                    </div>
                                </li>
                                <li class="nav-item d-md-none">
                                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <i class="fas fa-home me-2"></i> แดชบอร์ด
                                    </a>
                                </li>
                                <li class="nav-item d-md-none">
                                    <a class="nav-link {{ request()->routeIs('run.*') ? 'active' : '' }}" href="{{ route('run.index') }}">
                                        <i class="fas fa-running me-2"></i> เริ่มวิ่ง
                                    </a>
                                </li>
                                <li class="nav-item d-md-none">
                                    <a class="nav-link {{ request()->routeIs('badges.*') ? 'active' : '' }}" href="{{ route('badges.index') }}">
                                        <i class="fas fa-medal me-2"></i> เหรียญตรา
                                    </a>
                                </li>
                                <li class="nav-item d-md-none">
                                    <a class="nav-link {{ request()->routeIs('rewards.*') ? 'active' : '' }}" href="{{ route('rewards.index') }}">
                                        <i class="fas fa-gift me-2"></i> รางวัล
                                    </a>
                                </li>
                                <li class="nav-item d-md-none">
                                    <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                                        <i class="fas fa-calendar-alt me-2"></i> กิจกรรม
                                    </a>
                                </li>
                                <li class="nav-item d-md-none">
                                    <a class="nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}" href="{{ route('goals.index') }}">
                                        <i class="fas fa-bullseye me-2"></i> เป้าหมาย
                                    </a>
                                </li>
                                <li class="nav-item d-md-none">
                                    <a class="nav-link {{ request()->routeIs('health-articles.*') ? 'active' : '' }}" href="{{ route('health-articles.index') }}">
                                        <i class="fas fa-heartbeat me-2"></i> บทความสุขภาพ
                                    </a>
                                </li>

                            @endauth
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i> เข้าสู่ระบบ
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1"></i> สมัครสมาชิก
                                    </a>
                                </li>
                            @else
                                <li class="nav-item dropdown">
                                    <a id="userDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 0.6rem 1rem; border-radius: 10px; transition: all 0.2s ease;">
                                        @if(Auth::user()->profile_image)
                                            <img src="{{ asset('profile_images/' . Auth::user()->profile_image) }}" alt="Profile" class="user-profile-image me-2">
                                        @else
                                            <i class="fas fa-user-circle me-2" style="font-size: 1.4rem;"></i>
                                        @endif
                                        <span style="font-weight: 500;">{{ Auth::user()->firstname }}</span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown" style="border-radius: 12px; border: none; padding: 0.5rem 0; min-width: 220px;">
                                        <div class="d-flex align-items-center px-3 py-2 border-bottom mb-2">
                                            @if(Auth::user()->profile_image)
                                                <img src="{{ asset('profile_images/' . Auth::user()->profile_image) }}" alt="Profile" class="profile-dropdown-image me-2">
                                            @else
                                                <i class="fas fa-user-circle fa-2x me-2 text-muted"></i>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</div>
                                                <div class="small text-muted">{{ Auth::user()->email }}</div>
                                            </div>
                                        </div>

                                        <a class="dropdown-item" href="{{ route('profile.edit') }}" style="padding: 0.7rem 1.5rem;">
                                            <i class="fas fa-user-edit me-2"></i> ข้อมูลส่วนตัว
                                        </a>

                                        <a class="dropdown-item" href="{{ route('tour.settings') }}" style="padding: 0.7rem 1.5rem;">
                                            <i class="fas fa-info-circle me-2"></i> ตั้งค่าการแนะนำ
                                        </a>

                                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="padding: 0.7rem 1.5rem;">
                                            <i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <div class="container">
                <!-- ลบ Bootstrap alerts ออกเพื่อป้องกันการแสดงซ้ำซ้อนกับ SweetAlert -->
            </div>

            @yield('content')
        </main>

        <!-- Mobile Bottom Navigation -->
        @auth
        <nav class="mobile-nav d-md-none">
            <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home mobile-nav-icon"></i>
                <span>หน้าแรก</span>
            </a>
            <a href="{{ route('run.index') }}" class="mobile-nav-item {{ request()->routeIs('run.*') ? 'active' : '' }}">
                <i class="fas fa-running mobile-nav-icon"></i>
                <span>วิ่ง</span>
            </a>
            <a href="{{ route('badges.index') }}" class="mobile-nav-item {{ request()->routeIs('badges.*') ? 'active' : '' }}">
                <i class="fas fa-medal mobile-nav-icon"></i>
                <span>เหรียญ</span>
            </a>
            <a href="{{ route('rewards.index') }}" class="mobile-nav-item {{ request()->routeIs('rewards.*') ? 'active' : '' }}">
                <i class="fas fa-gift mobile-nav-icon"></i>
                <span>รางวัล</span>
            </a>
            <a href="{{ route('health-articles.index') }}" class="mobile-nav-item {{ request()->routeIs('health-articles.*') ? 'active' : '' }}">
                <i class="fas fa-heartbeat mobile-nav-icon"></i>
                <span>บทความ</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="mobile-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fas fa-user mobile-nav-icon"></i>
                <span>โปรไฟล์</span>
            </a>
        </nav>
        @endauth

        <footer class="gofit-footer">
            <div class="container">
                <div class="footer-bottom text-center py-3">
                    <p class="mb-0 footer-text">&copy; 2025 DPU | 66130773 WARONGKON FUKTHONGYOO</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- jQuery (ต้องโหลดก่อน Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle JS (รวม Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert-custom.css') }}"

    <!-- สคริปต์สำหรับ SweetAlert สำหรับ Session Flash Messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ถ้ามีข้อความ Success จาก session flash
            @if(session('success'))
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#2DC679'
                });
            @endif

            // ถ้ามีข้อความ Error จาก session flash
            @if(session('error'))
                Swal.fire({
                    title: 'ข้อผิดพลาด!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#2DC679'
                });
            @endif

            // ถ้าปลดล็อคเหรียญตราสำเร็จ แสดงผลตามนี้
            @if(session('badge_unlocked'))
                Swal.fire({
                    title: 'ยินดีด้วย!',
                    html: `
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . session('badge_unlocked.image')) }}"
                                 alt="{{ session('badge_unlocked.badge_name') }}"
                                 style="max-height: 120px; max-width: 120px; margin-bottom: 15px;">
                            <h5 class="mb-2">{{ session('badge_unlocked.badge_name') }}</h5>
                            <div class="text-success mb-3">คุณได้ปลดล็อคเหรียญตรารายการนี้แล้ว!</div>
                            <div class="text-primary">
                                <i class="fas fa-coins text-warning me-1"></i> <strong>{{ session('badge_unlocked.points') }} คะแนน</strong>
                            </div>
                        </div>
                    `,
                    icon: false,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#2DC679'
                });
            @endif

            // แสดง toast แจ้งเตือน
            @if(session('toast'))
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                Toast.fire({
                    icon: '{{ session('toast.type') }}',
                    title: '{{ session('toast.message') }}'
                });
            @endif
        });
    </script>

    <!-- เพิ่ม scripts ถ้ามี -->
    @yield('scripts')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Removing problematic scripts that cause conflicts -->
    <!-- <script src="{{ asset('js/dropdown-fix.js') }}"></script> -->
    <!-- <script src="{{ asset('mobile-menu-fix.js') }}"></script> -->

    <!-- New Unified Navigation Fix -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('New Navigation Fix Loaded');

            // Prevent multiple initializations
            if (window.goFitNavFixApplied) return;
            window.goFitNavFixApplied = true;

            // Clean up any leftover elements
            document.querySelectorAll('.modal-backdrop, #mobile-menu-overlay').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Get references to navigation elements
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const allDropdownToggles = document.querySelectorAll('.dropdown-toggle');
            const allDropdownMenus = document.querySelectorAll('.dropdown-menu');
            const closeButton = document.querySelector('.navbar-collapse .btn-close');

            // 1. HAMBURGER MENU TOGGLE
            if (navbarToggler && navbarCollapse) {
                // Remove bootstrap data attributes
                navbarToggler.removeAttribute('data-bs-toggle');
                navbarToggler.removeAttribute('data-bs-target');

                // Clean slate - remove any existing listeners by cloning
                const newToggler = navbarToggler.cloneNode(true);
                navbarToggler.parentNode.replaceChild(newToggler, navbarToggler);

                // Add new toggle behavior
                newToggler.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                        navbarCollapse.style.transform = 'translateX(-100%)';
                    } else {
                        navbarCollapse.classList.add('show');
                        navbarCollapse.style.transform = 'translateX(0)';
                    }
                });
            }

            // 2. CLOSE BUTTON IN MOBILE MENU
            if (closeButton && navbarCollapse) {
                // Fresh listener
                const newCloseBtn = closeButton.cloneNode(true);
                closeButton.parentNode.replaceChild(newCloseBtn, closeButton);

                newCloseBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    navbarCollapse.classList.remove('show');
                    navbarCollapse.style.transform = 'translateX(-100%)';
                });
            }

            // 3. DROPDOWN MENUS
            allDropdownToggles.forEach(function(toggle) {
                // Remove bootstrap data attributes
                toggle.removeAttribute('data-bs-toggle');
                toggle.removeAttribute('data-bs-auto-close');

                // Clean slate
                const newToggle = toggle.cloneNode(true);
                toggle.parentNode.replaceChild(newToggle, toggle);

                // New toggle behavior
                newToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const dropdownMenu = this.nextElementSibling;
                    if (!dropdownMenu || !dropdownMenu.classList.contains('dropdown-menu')) return;

                    // Close all other dropdowns
                    allDropdownMenus.forEach(function(menu) {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                        }
                    });

                    // Toggle this dropdown
                    dropdownMenu.classList.toggle('show');
                });
            });

            // 4. DROPDOWN MENU ITEMS
            document.querySelectorAll('.dropdown-item').forEach(function(item) {
                // Replace with new element to remove old listeners
                const newItem = item.cloneNode(true);
                item.parentNode.replaceChild(newItem, item);

                newItem.addEventListener('click', function(e) {
                    e.stopPropagation();

                    // If it's a link with href, navigate to it
                    if (this.getAttribute('href') && this.getAttribute('href') !== '#') {
                        console.log('Navigating to:', this.getAttribute('href'));
                        window.location.href = this.getAttribute('href');
                    }
                });
            });

            // 5. GLOBAL DOCUMENT CLICK
            document.addEventListener('click', function(e) {
                // Close dropdowns when clicking outside
                if (!e.target.closest('.dropdown')) {
                    allDropdownMenus.forEach(menu => menu.classList.remove('show'));
                }

                // Close mobile menu when clicking outside
                if (navbarCollapse && navbarCollapse.classList.contains('show') &&
                    !navbarCollapse.contains(e.target) &&
                    !e.target.classList.contains('navbar-toggler') &&
                    !e.target.closest('.navbar-toggler')) {
                    navbarCollapse.classList.remove('show');
                    navbarCollapse.style.transform = 'translateX(-100%)';
                }
            });

            // 6. ESCAPE KEY HANDLER
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    // Close mobile menu
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                        navbarCollapse.style.transform = 'translateX(-100%)';
                    }

                    // Close all dropdowns
                    allDropdownMenus.forEach(menu => menu.classList.remove('show'));
                }
            });

            // 7. LINKS IN MOBILE MENU
            document.querySelectorAll('.navbar-collapse .nav-link:not(.dropdown-toggle)').forEach(function(link) {
                // Replace to remove old listeners
                const newLink = link.cloneNode(true);
                link.parentNode.replaceChild(newLink, link);

                newLink.addEventListener('click', function() {
                    if (navbarCollapse) {
                        setTimeout(function() {
                            navbarCollapse.classList.remove('show');
                            navbarCollapse.style.transform = 'translateX(-100%)';
                        }, 100);
                    }
                });
            });

            console.log('Navigation fix applied successfully');
        });
    </script>

    <!-- Comprehensive navbar fix -->
    <script>
        // Remove this script entirely to avoid conflicts
    </script>

    <!-- Tour Button -->
    @auth
    <!-- Tour script remains but button removed -->
    @endauth

    <!-- Tour Script -->
    <script src="{{ asset('js/app-tour.js') }}"></script>
    <script src="{{ asset('js/tour-settings-fix.js') }}"></script>
</body>
</html>
