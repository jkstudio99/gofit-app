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

    <!-- Navbar Fix CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-dropdown.css') }}">

    <!-- SweetAlert Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert-custom.css') }}">

    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- เพิ่ม styles ถ้ามี -->
    <style>
        /* Global Admin Styling */
        body {
            background-color: #f9f9f9;
            overflow-x: hidden;
        }

        /* Card styles */
        .card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border: 0 !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.15) !important;
        }

        /* Hover effect for elements with special hover class */
        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            transition: box-shadow 0.3s ease-in-out;
        }

        /* Chart styles */
        .apexcharts-tooltip {
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            border: none;
        }

        .apexcharts-tooltip-title {
            background: #f8f9fa !important;
            border-bottom: 1px solid #eee !important;
        }

        .apexcharts-xaxistooltip {
            border: none;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }

        /* Navbar user dropdown styles */
        .navbar .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* CSS แก้ไขปัญหา dropdown */
        .dropdown-menu {
            position: absolute !important;
            display: none;
            z-index: 9999 !important;
            margin: 0;
            padding: 0.5rem 0;
            border: 0;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            pointer-events: auto !important;
        }

        /* Fix navbar position */
        .navbar {
            position: relative;
            z-index: 1030 !important;
        }

        /* Ensure dropdowns appear above other elements */
        .dropdown {
            position: relative;
        }

        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }

        .dropdown-toggle:not(.show)::after {
            transform: rotate(0deg);
            transition: transform 0.2s;
        }

        .dropdown-toggle.show::after {
            transform: rotate(180deg);
            transition: transform 0.2s;
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
            cursor: pointer;
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

        /* Ensure clickable elements have proper cursor and z-index */
        .nav-link, .dropdown-item, button, a, .btn {
            cursor: pointer !important;
            position: relative;
            z-index: 1500 !important;
            pointer-events: auto !important;
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
            z-index: 1600 !important;
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

        /* Updated footer styling */
        .gofit-footer {
            background-color: #fff;
            padding: 10px 0;
            margin-top: 30px;
        }

        .footer-bottom {
            font-size: 14px;
            color: #6c757d;
        }

        /* แก้ไขปัญหากดปุ่มไม่ได้ */
        * {
            pointer-events: auto !important;
        }

        body {
            pointer-events: auto !important;
        }

        .navbar-nav .nav-item {
            position: relative !important;
            z-index: 2000 !important;
        }

        .navbar-nav .nav-link {
            position: relative !important;
            z-index: 2001 !important;
        }

        .dropdown-menu {
            z-index: 2002 !important;
        }

        /* แก้ไขปัญหา navbar toggle */
        .navbar-toggler {
            z-index: 2003 !important;
            position: relative !important;
        }

        /* Fix for collapse issue */
        .collapsing {
            position: relative;
            height: 0;
            overflow: hidden;
            transition: height 0.35s ease;
        }

        .collapse.show {
            display: block !important;
        }

        /* Fix for dropdown issues in navbar */
        .navbar .nav-item .dropdown-menu {
            display: none;
        }

        .navbar .nav-item.show .dropdown-menu {
            display: block;
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
                            <!-- Reports Dropdown (New) -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.badges.statistics') || request()->routeIs('admin.rewards.statistics') || request()->routeIs('admin.run.stats') || request()->routeIs('admin.health-articles.statistics') ? 'active' : '' }}" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-line me-1"></i> รายงานและสถิติทั้งหมด
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                            <i class="fas fa-chart-pie me-1"></i> ภาพรวมทั้งหมด
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.badges.statistics') ? 'active' : '' }}" href="{{ route('admin.badges.statistics') }}">
                                            <i class="fas fa-medal me-1"></i> สถิติเหรียญตรา
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.rewards.statistics') ? 'active' : '' }}" href="{{ route('admin.rewards.statistics') }}">
                                            <i class="fas fa-gift me-1"></i> สถิติรางวัล
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.run.stats') ? 'active' : '' }}" href="{{ route('admin.run.stats') }}">
                                            <i class="fas fa-running me-1"></i> สถิติการวิ่ง
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.health-articles.statistics') ? 'active' : '' }}" href="{{ route('admin.health-articles.statistics') }}">
                                            <i class="fas fa-newspaper me-1"></i> สถิติบทความ
                                        </a>
                                    </li>

                                </ul>
                            </li>

                            <!-- Badges and Rewards Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.badges.*') || request()->routeIs('admin.rewards') || request()->routeIs('admin.redeems') ? 'active' : '' }}" href="#" id="rewardsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-medal me-1"></i> รางวัลและเหรียญตรา
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="rewardsDropdown">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.badges.index') ? 'active' : '' }}" href="{{ route('admin.badges.index') }}">
                                    <i class="fas fa-medal me-1"></i> เหรียญตรา
                                </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.badges.history') ? 'active' : '' }}" href="{{ route('admin.badges.history') }}">
                                            <i class="fas fa-history me-1"></i> ประวัติการได้รับเหรียญตรา
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.rewards') ? 'active' : '' }}" href="{{ route('admin.rewards') }}">
                                            <i class="fas fa-gift me-1"></i> รางวัล
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
                <div class="footer-bottom text-center py-3">
                    <p class="mb-0 footer-text">&copy; 2025 DPU | 66130773 WARONGKON FUKTHONGYOO</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- สคริปต์สำหรับ SweetAlert -->
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

            // ถ้ามีการปลดล็อคเหรียญตรา
            @if(session('badge_unlocked'))
                Swal.fire({
                    title: 'ยินดีด้วย!',
                    html: '<div class="text-center">' +
                          '<img src="{{ asset('storage/' . session('badge_unlocked.image')) }}" class="img-fluid mb-3" style="max-height: 150px;"><br>' +
                          'ปลดล็อคเหรียญตรา <strong>{{ session('badge_unlocked.badge_name') }}</strong><br>' +
                          'สำเร็จและเพิ่ม <strong>{{ session('badge_unlocked.points') }} คะแนน</strong>' +
                          '</div>',
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#2DC679'
                });
            @endif

            // แก้ไขปัญหาการ collapse
            var navbarToggler = document.querySelector('.navbar-toggler');
            var navbarCollapse = document.querySelector('.navbar-collapse');

            if (navbarToggler && navbarCollapse) {
                navbarToggler.addEventListener('click', function() {
                    if (navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                    } else {
                        navbarCollapse.classList.add('show');
                    }
                });
            }

            // แก้ไขปัญหาการ dropdown
            var dropdownToggles = document.querySelectorAll('.dropdown-toggle');

            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var parent = this.parentElement;
                    var dropdownMenu = parent.querySelector('.dropdown-menu');

                    // Close all other dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                            menu.parentElement.classList.remove('show');
                        }
                    });

                    // Toggle current dropdown
                    parent.classList.toggle('show');
                    dropdownMenu.classList.toggle('show');
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                        menu.classList.remove('show');
                        menu.parentElement.classList.remove('show');
                    });
                }
            });
        });
    </script>

    <!-- DataTables and Export Plugins -->
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>

    <!-- Thai Font Fixes & DataTables Fixes -->
    <script src="{{ asset('js/thai-fonts-fix.js') }}"></script>
    <script src="{{ asset('js/datatables-buttons-fix.js') }}"></script>
    <script src="{{ asset('js/fixes-bundle.js') }}"></script>

    @yield('scripts')
</body>
</html>
