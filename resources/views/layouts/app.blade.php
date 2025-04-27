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

    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
    <style>
        /* เพิ่ม CSS เพื่อแก้ไข dropdown */
        .dropdown-menu.show {
            display: block !important;
            z-index: 9999 !important;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="app">
        <!-- Header -->
        <header class="gofit-header">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/dashboard') }}">
                        <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="Logo" height="30">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <i class="fas fa-home"></i> หน้าแรก
                                    </a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('run.*') ? 'active' : '' }}" href="{{ route('run.index') }}">
                                        <i class="fas fa-running"></i> วิ่ง
                                    </a>
                                </li> --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('badges.*') ? 'active' : '' }}" href="{{ route('badges.index') }}">
                                        <i class="fas fa-medal"></i> เหรียญตรา
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('rewards.*') ? 'active' : '' }}" href="{{ route('rewards.index') }}">
                                        <i class="fas fa-gift"></i> รางวัล
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                                        <i class="fas fa-calendar-alt"></i> กิจกรรม
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}" href="{{ route('activities.index') }}">
                                        <i class="fas fa-heartbeat"></i> การออกกำลังกาย
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}" href="{{ route('goals.index') }}">
                                        <i class="fas fa-bullseye"></i> เป้าหมาย
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
                                    <a id="userDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        @if(Auth::user()->profile_image)
                                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="Profile" class="rounded-circle me-1" style="width: 25px; height: 25px; object-fit: cover;">
                                        @else
                                            <i class="fas fa-user-circle me-1"></i>
                                        @endif
                                        {{ Auth::user()->firstname }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-edit me-1"></i> ข้อมูลส่วนตัว
                                        </a>

                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
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

        <main class="py-4">
            <div class="container">
                <!-- ลบ Bootstrap alerts ออกเพื่อป้องกันการแสดงซ้ำซ้อนกับ SweetAlert -->
            </div>

            @yield('content')
        </main>

        <footer class="gofit-footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <h5 class="footer-title">เกี่ยวกับ GoFit</h5>
                        <p>แอปพลิเคชันที่ช่วยให้คุณมีสุขภาพดีด้วยการวิ่ง ติดตามกิจกรรม รับเหรียญตรา และแลกรางวัลมากมาย</p>
                    </div>

                    <div class="col-md-3 mb-4 mb-md-0">
                        <h5 class="footer-title">ลิงก์ด่วน</h5>
                        <ul class="footer-links">
                            <li><a href="{{ route('home') }}">หน้าแรก</a></li>
                            <li><a href="{{ route('run.index') }}">เริ่มวิ่ง</a></li>
                            <li><a href="{{ route('badges.index') }}">เหรียญตรา</a></li>
                            <li><a href="{{ route('rewards.index') }}">รางวัล</a></li>
                        </ul>
                    </div>

                    <div class="col-md-3 mb-4 mb-md-0">
                        <h5 class="footer-title">ช่วยเหลือ</h5>
                        <ul class="footer-links">
                            <li><a href="#">คำถามที่พบบ่อย</a></li>
                            <li><a href="#">นโยบายความเป็นส่วนตัว</a></li>
                            <li><a href="#">เงื่อนไขการใช้งาน</a></li>
                            <li><a href="#">ติดต่อเรา</a></li>
                        </ul>
                    </div>

                    <div class="col-md-2">
                        <h5 class="footer-title">ติดตามเรา</h5>
                        <div class="d-flex gap-3 fs-4">
                            <a href="#" class="text-secondary"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-secondary"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-secondary"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p class="mb-0">&copy; {{ date('Y') }} GoFit. สงวนลิขสิทธิ์ทั้งหมด</p>
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

    <!-- สคริปต์สำหรับ SweetAlert สำหรับ Session Flash Messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // แสดง SweetAlert สำหรับ Session Messages
            const successMessage = "{{ session('success') }}";
            const errorMessage = "{{ session('error') }}";
            const warningMessage = "{{ session('warning') }}";

            if (successMessage) {
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: successMessage,
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#2DC679'
                });
            }

            if (errorMessage) {
                Swal.fire({
                    title: 'ผิดพลาด!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#FF4646'
                });
            }

            if (warningMessage) {
                Swal.fire({
                    title: 'คำเตือน!',
                    text: warningMessage,
                    icon: 'warning',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#FFB800'
                });
            }
        });
    </script>

    <!-- เพิ่ม scripts ถ้ามี -->
    @yield('scripts')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/dropdown-fix.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // เรียกใช้ bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // แก้ปัญหา dropdown ไม่ทำงาน
            var dropdownToggleList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'))
            dropdownToggleList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });
        });
    </script>
</body>
</html>
