<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GoFit') }} Admin - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- เพิ่ม styles ถ้ามี -->
    <style>
        /* Navbar user dropdown styles */
        .navbar .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .navbar .dropdown-header {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .navbar .dropdown-item {
            padding: 0.6rem 1.5rem;
            transition: all 0.2s ease;
        }

        .navbar .dropdown-item:hover {
            background-color: #F0FBF5;
            color: #2DC679;
        }

        .navbar .dropdown-item.active {
            background-color: #F0FBF5;
            color: #2DC679;
            font-weight: 500;
        }

        .navbar .dropdown-divider {
            margin: 0.25rem 0;
        }

        /* Admin profile image styles */
        .admin-profile-img {
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        #userDropdown:hover .admin-profile-img {
            transform: scale(1.05);
        }

        /* Admin navigation styles */
        .nav-link {
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: #2DC679 !important;
        }

        .nav-link.active {
            color: #2DC679 !important;
            font-weight: 500;
        }

        .nav-pills .nav-link.active {
            background-color: #2DC679 !important;
            color: white !important;
        }

        /* Dropdown active state */
        .nav-item.dropdown .nav-link.active,
        .nav-item.dropdown .nav-link.dropdown-toggle.active {
            color: #2DC679 !important;
        }

        /* Main navigation dropdown */
        .navbar-nav .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
        }

        /* Button styles */
        .btn-primary {
            background-color: #2DC679 !important;
            border-color: #2DC679 !important;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #24A664 !important;
            border-color: #24A664 !important;
        }

        .btn-outline-primary {
            color: #2DC679 !important;
            border-color: #2DC679 !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background-color: #2DC679 !important;
            color: white !important;
        }

        /* Badge styles */
        .badge.bg-primary {
            background-color: #2DC679 !important;
        }

        .badge.bg-admin {
            background-color: #212529 !important;
            color: white !important;
        }

        /* Outline filled badges for user statuses */
        .badge.badge-outline-success {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
        }

        .badge.badge-outline-danger {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
        }

        /* Custom header style */
        .gofit-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dropdown-header.text-primary {
            color: #2DC679 !important;
        }

        .text-primary {
            color: #2DC679 !important;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="app" class="admin-panel">
        <!-- Header -->
        <header class="gofit-header">
            <nav class="navbar navbar-expand-md navbar-light">
                <div class="container">
                    <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Admin" style="height: 2.2rem;"> <span class="ms-2 text-primary">Admin</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarContent">
                        <!-- Left Side Menu -->
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i> แดชบอร์ด
                                </a>
                            </li>

                            <!-- Badges and Rewards Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.badges.*') || request()->routeIs('admin.rewards') || request()->routeIs('admin.redeems') ? 'active' : '' }}" href="#" id="rewardsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-medal me-1"></i> รางวัลและเหรียญตรา
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="rewardsDropdown">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.badges.*') ? 'active' : '' }}" href="{{ route('admin.badges.index') }}">
                                    <i class="fas fa-medal me-1"></i> เหรียญตรา
                                </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.badges.statistics') ? 'active' : '' }}" href="{{ route('admin.badges.statistics') }}">
                                            <i class="fas fa-chart-pie me-1"></i> สถิติเหรียญตรา
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.rewards') ? 'active' : '' }}" href="{{ route('admin.rewards') }}">
                                            <i class="fas fa-gift me-1"></i> รางวัล
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.rewards.statistics') ? 'active' : '' }}" href="{{ route('admin.rewards.statistics') }}">
                                            <i class="fas fa-chart-bar me-1"></i> สถิติรางวัล
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.redeems') ? 'active' : '' }}" href="{{ route('admin.redeems') }}">
                                            <i class="fas fa-exchange-alt me-1"></i> ประวัติการแลกรางวัล
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Events Management Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.events.*') ? 'active' : '' }}" href="#" id="eventsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-calendar-alt me-1"></i> จัดการกิจกรรม
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="eventsDropdown">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.events.index') ? 'active' : '' }}" href="{{ route('admin.events.index') }}">
                                            <i class="fas fa-list me-1"></i> รายการกิจกรรม
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.events.create') ? 'active' : '' }}" href="{{ route('admin.events.create') }}">
                                            <i class="fas fa-plus me-1"></i> สร้างกิจกรรมใหม่
                                </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Health Articles Management Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.health-articles.*') ? 'active' : '' }}" href="#" id="articlesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-newspaper me-1"></i> บทความสุขภาพ
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="articlesDropdown">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.health-articles.index') ? 'active' : '' }}" href="{{ route('admin.health-articles.index') }}">
                                            <i class="fas fa-list me-1"></i> รายการบทความ
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.health-articles.create') ? 'active' : '' }}" href="{{ route('admin.health-articles.create') }}">
                                            <i class="fas fa-plus me-1"></i> เพิ่มบทความใหม่
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.health-articles.statistics') ? 'active' : '' }}" href="{{ route('admin.health-articles.statistics') }}">
                                            <i class="fas fa-chart-bar me-1"></i> สถิติบทความ
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <!-- Right Side Menu -->
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a id="userDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @if(Auth::user()->profile_image)
                                        <img src="{{ asset('profile_images/' . Auth::user()->profile_image) }}" class="rounded-circle me-2 admin-profile-img" width="32" height="32" alt="Profile" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white me-2 admin-profile-img" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <span>{{ Auth::user()->username }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">
                                    <div class="dropdown-header bg-light py-2">
                                        <i class="fas fa-user-shield me-1 text-primary"></i> <span class="fw-bold text-primary">บัญชีผู้ดูแลระบบ</span>
                                    </div>

                                    <a class="dropdown-item" href="{{ route('admin.users.show', Auth::id()) }}">
                                        <i class="fas fa-user-cog me-1"></i> โปรไฟล์ของฉัน
                                    </a>

                                    <!-- User Management Menu Items -->
                                    <a class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                        <i class="fas fa-users me-1"></i> รายชื่อผู้ใช้งาน
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <main class="py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>

        <footer class="gofit-footer">
            <div class="container">
                <div class="footer-bottom">
                    <p class="mb-0">&copy; {{ date('Y') }} GoFit Admin Panel. สงวนลิขสิทธิ์ทั้งหมด</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <!-- แสดง SweetAlert จาก session flash messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#28a745'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#dc3545'
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'คำเตือน',
                    text: "{{ session('warning') }}",
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#ffc107'
                });
            @endif
        });
    </script>

    <!-- เพิ่ม scripts ถ้ามี -->
    @yield('scripts')
</body>
</html>
