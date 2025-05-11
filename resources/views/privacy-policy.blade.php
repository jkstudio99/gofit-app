<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="นโยบายความเป็นส่วนตัว GoFit - เว็บแอปพลิเคชันออกกำลังกายอันดับ 1 ด้วยระบบเกมมิฟิเคชัน">
        <meta name="keywords" content="นโยบายความเป็นส่วนตัว, ออกกำลังกาย, เกมมิฟิเคชัน, สุขภาพ, วิ่ง, รางวัล, เหรียญตรา, เว็บแอปพลิเคชัน">
        <meta name="author" content="GoFit Team">
        <meta name="robots" content="index, follow">
        <meta name="theme-color" content="#2DC679">
        <link rel="canonical" href="{{ url('/privacy-policy') }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/privacy-policy') }}">
        <meta property="og:title" content="นโยบายความเป็นส่วนตัว - GoFit">
        <meta property="og:description" content="นโยบายความเป็นส่วนตัวของ GoFit เว็บแอปพลิเคชันออกกำลังกายด้วยระบบเกมมิฟิเคชัน">
        <meta property="og:image" content="{{ asset('images/gofit-share.jpg') }}">

        <title>นโยบายความเป็นส่วนตัว - GoFit</title>

        <!-- Preconnect to required origins -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">

        <!-- Preload critical assets -->
        <link rel="preload" href="{{ asset('images/gofit-logo-text-black.svg') }}" as="image" type="image/svg+xml">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Styles -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Noto Sans Thai', sans-serif;
                color: var(--color-text-primary);
                background-color: var(--color-background);
                overflow-x: hidden;
            }

            /* Contact links without underline */
            .col-lg-4.col-md-4 a {
                text-decoration: none;
                color: inherit;
            }

            .col-lg-4.col-md-4 a:hover {
                color: var(--color-primary);
            }

            .navbar-gofit {
                background-color: white;
                margin: 0.5rem 0;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
            }

            .navbar-gofit.scrolled {
                padding: 5px 0;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }

            .nav-link {
                color: var(--color-text-primary);
                font-weight: var(--font-weight-medium);
                padding: 0.75rem 1rem;
                transition: all 0.2s;
                position: relative;
            }

            .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                width: 0;
                height: 2px;
                background: var(--color-primary);
                transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
                transform: translateX(-50%);
            }

            .nav-link:hover::after {
                width: 80%;
            }

            .nav-link:hover {
                color: var(--color-primary);
            }

            .navbar-brand img {
                height: 2.5rem;
                transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
            }

            .navbar-brand:hover img {
                transform: scale(1.05);
            }

            footer {
                background-color: var(--color-background-alt);
                padding: 4rem 0 2rem;
            }

            .footer-title {
                font-weight: var(--font-weight-bold);
                margin-bottom: 1.5rem;
                color: var(--color-text-primary);
            }

            .footer-link {
                color: var(--color-text-secondary);
                display: block;
                padding: 0.5rem 0;
                transition: all 0.2s;
                text-decoration: none;
                position: relative;
                padding-left: 0;
            }

            .footer-link::before {
                content: '';
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 0;
                height: 2px;
                background-color: var(--color-primary);
                transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
                opacity: 0;
            }

            .footer-link:hover {
                color: var(--color-primary);
                padding-left: 10px;
            }

            .footer-link:hover::before {
                width: 5px;
                opacity: 1;
            }

            .social-icons {
                display: flex;
                gap: 1rem;
            }

            .social-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 50%;
                background-color: var(--color-primary-lighter);
                color: var(--color-primary-darker);
                transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
                will-change: transform, background-color;
            }

            .social-icon:hover {
                background-color: var(--color-primary);
                color: white;
                transform: translateY(-3px);
            }

            .copyright {
                padding-top: 2rem;
                border-top: 1px solid rgba(0,0,0,0.1);
                margin-top: 3rem;
            }

            /* Privacy Policy specific styles */
            .privacy-content h2 {
                color: var(--color-text-primary);
                margin-top: 2rem;
                margin-bottom: 1rem;
            }

            .privacy-content section {
                margin-bottom: 2rem;
            }

            .privacy-content ul {
                margin-bottom: 1rem;
            }

            .privacy-header {
                background-color: var(--color-primary-lighter);
                padding: 2rem 0;
                margin-bottom: 2rem;
            }

            .privacy-container {
                max-width: 800px;
                margin: 0 auto;
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                padding: 2rem;
            }

            .privacy-title {
                font-weight: 700;
                margin-bottom: 0.5rem;
                color: var(--color-text-primary);
            }

            .privacy-date {
                color: var(--color-text-secondary);
                margin-bottom: 2rem;
            }

            .back-link {
                display: inline-block;
                margin-top: 1.5rem;
            }

            /* Cookie Modal Styles */
            .cookie-modal {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1001;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .cookie-modal-content {
                background-color: white;
                border-radius: 0.5rem;
                max-width: 600px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
                position: relative;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                margin: 0 auto;
            }

            .cookie-modal-header {
                padding: 1.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: relative;
            }

            .cookie-modal-body {
                padding: 1.5rem;
            }

            .cookie-modal-footer {
                padding: 1.5rem;
                border-top: 1px solid rgba(0, 0, 0, 0.1);
                text-align: right;
            }

            .close-button {
                background-color: #f0f0f0;
                border: 1px solid #ddd;
                font-size: 1.25rem;
                cursor: pointer;
                color: #333;
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.2s ease;
                z-index: 10;
            }

            .close-button:hover {
                background-color: #e0e0e0;
                color: #000;
            }

            .cookie-option {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1.5rem;
                padding-bottom: 1.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            }

            .cookie-option:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .cookie-option h5 {
                margin-bottom: 0.5rem;
            }

            .cookie-option p {
                color: #6c757d;
                margin-bottom: 0;
            }

            /* Switch Toggle */
            .switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
                flex-shrink: 0;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .4s;
            }

            input:checked + .slider {
                background-color: #2DC679;
            }

            input:disabled + .slider {
                opacity: 0.5;
                cursor: not-allowed;
            }

            input:checked + .slider:before {
                transform: translateX(26px);
            }

            .slider.round {
                border-radius: 24px;
            }

            .slider.round:before {
                border-radius: 50%;
            }

            .colored-toast.swal2-icon-success {
                background-color: #17A15F !important;
                color: white !important;
            }

            .colored-toast .swal2-title {
                color: white !important;
                font-size: 1rem !important;
            }

            .colored-toast .swal2-close {
                color: white !important;
            }

            .colored-toast .swal2-html-container {
                color: white !important;
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-gofit sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Logo" width="120" height="40">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/#features') }}">ฟีเจอร์</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/#how-it-works') }}">วิธีการใช้งาน</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link">เข้าสู่ระบบ</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-gofit ms-2">ลงทะเบียน</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Privacy Policy Content -->
        <div class="privacy-header">
            <div class="container">
                <h1 class="text-center privacy-title display-5">นโยบายความเป็นส่วนตัว</h1>
            </div>
        </div>

        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="privacy-container">
                        <p class="privacy-date">อัปเดตล่าสุด: {{ date('d/m/Y') }}</p>

                        <div class="privacy-content">
                            <section>
                                <h2 class="h5">1. บทนำ</h2>
                                <p>นโยบายความเป็นส่วนตัวนี้อธิบายวิธีที่ GoFit ("บริษัท", "เรา", "ของเรา") รวบรวม ใช้ และเปิดเผยข้อมูลของคุณเมื่อคุณใช้แอปพลิเคชันและบริการของเรา</p>
                            </section>

                            <section>
                                <h2 class="h5">2. ข้อมูลที่เราเก็บรวบรวม</h2>
                                <p>เราอาจเก็บรวบรวมข้อมูลประเภทต่าง ๆ ต่อไปนี้:</p>
                                <ul>
                                    <li><strong>ข้อมูลส่วนบุคคล</strong>: ชื่อ, อีเมล, หมายเลขโทรศัพท์, ที่อยู่, วันเกิด</li>
                                    <li><strong>ข้อมูลบัญชี</strong>: ชื่อผู้ใช้, รหัสผ่าน (ในรูปแบบที่เข้ารหัส)</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="h5">3. วิธีที่เราใช้ข้อมูลของคุณ</h2>
                                <p>เราใช้ข้อมูลที่เก็บรวบรวมเพื่อ:</p>
                                <ul>
                                    <li>จัดหาและบำรุงรักษาบริการของเรา</li>
                                    <li>ปรับแต่งประสบการณ์การใช้งานของคุณ</li>
                                    <li>พัฒนาและปรับปรุงฟีเจอร์และบริการใหม่</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="h5">4. การแบ่งปันข้อมูลของคุณ</h2>
                                <p>เราจะไม่ขาย, ให้เช่า, หรือแลกเปลี่ยนข้อมูลส่วนบุคคลของคุณกับบุคคลที่สามโดยไม่ได้รับความยินยอมจากคุณ</p>
                            </section>

                            <section>
                                <h2 class="h5">5. ความปลอดภัยของข้อมูล</h2>
                                <p>เราใช้มาตรการความปลอดภัยทางเทคนิคและองค์กรที่เหมาะสมเพื่อปกป้องข้อมูลส่วนบุคคลของคุณ</p>
                            </section>

                            <section>
                                <h2 class="h5">6. สิทธิความเป็นส่วนตัวของคุณ</h2>
                                <p>คุณมีสิทธิ์ดังต่อไปนี้เกี่ยวกับข้อมูลส่วนบุคคลของคุณ:</p>
                                <ul>
                                    <li>สิทธิ์ในการเข้าถึงและรับสำเนาข้อมูลส่วนบุคคลของคุณ</li>
                                    <li>สิทธิ์ในการแก้ไขหรืออัปเดตข้อมูลส่วนบุคคลของคุณที่ไม่ถูกต้อง</li>
                                    <li>สิทธิ์ในการลบข้อมูลส่วนบุคคลของคุณ</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="h5">7. การเปลี่ยนแปลงนโยบายความเป็นส่วนตัวนี้</h2>
                                <p>เราอาจปรับปรุงนโยบายความเป็นส่วนตัวนี้เป็นครั้งคราว เราจะแจ้งให้คุณทราบเกี่ยวกับการเปลี่ยนแปลงที่สำคัญใด ๆ</p>
                            </section>

                            <section>
                                <h2 class="h5">8. ติดต่อเรา</h2>
                                <p>หากคุณมีคำถามใด ๆ เกี่ยวกับนโยบายความเป็นส่วนตัวนี้ โปรดติดต่อเราที่: <a href="mailto:contact@gofitrunnow.com">contact@gofitrunnow.com</a></p>
                            </section>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ url('/') }}" class="btn btn-gofit back-link">กลับสู่หน้าหลัก</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
                <div class="text-center copyright">
                    <p>&copy; {{ date('Y') }} GoFit Web Application. All rights reserved.</p>
                    <div class="mt-2">
                        <a href="{{ url('/privacy-policy') }}" class="text-muted mx-2">นโยบายความเป็นส่วนตัว</a>
                        <span class="text-muted">|</span>
                        <a href="#" id="cookie-settings-footer" class="text-muted mx-2">การตั้งค่าคุกกี้</a>
                    </div>
                </div>
            </div>
        </footer>

        <script>
            // Add scrolled class to navbar on scroll
            document.addEventListener('DOMContentLoaded', function() {
                const navbar = document.querySelector('.navbar-gofit');

                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                });

                // Get link to cookie settings in footer
                const cookieSettingsFooter = document.getElementById('cookie-settings-footer');
                if (cookieSettingsFooter) {
                    cookieSettingsFooter.addEventListener('click', function(e) {
                        e.preventDefault();
                        showCookieSettings();
                    });
                }
            });

            function showCookieSettings() {
                // Get saved preferences
                let functional = false;
                let analytics = false;
                let marketing = false;

                try {
                    const savedPreferences = localStorage.getItem('cookie_preferences');
                    if (savedPreferences) {
                        const preferences = JSON.parse(savedPreferences);
                        functional = preferences.functional || false;
                        analytics = preferences.analytics || false;
                        marketing = preferences.marketing || false;
                    }
                } catch (e) {
                    console.error('Error loading cookie preferences:', e);
                }

                // Show SweetAlert for cookie settings
                Swal.fire({
                    title: 'ตั้งค่าความเป็นส่วนตัวของคุกกี้',
                    html: `
                        <div class="text-start">
                            <div class="mb-4">
                                <p class="text-muted">คุณสามารถปรับแต่งการตั้งค่าคุกกี้ได้ตามความต้องการ</p>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">คุกกี้ที่จำเป็น</h5>
                                        <p class="text-muted small mb-0">คุกกี้เหล่านี้จำเป็นสำหรับการทำงานของเว็บไซต์ และไม่สามารถปิดได้</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="swal-necessary-cookies" checked disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">คุกกี้ฟังก์ชันการใช้งาน</h5>
                                        <p class="text-muted small mb-0">ช่วยให้เราสามารถจดจำการตั้งค่าที่คุณเลือก เช่น ภาษา ธีม และการกำหนดค่าอื่นๆ</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="swal-functional-cookies" ${functional ? 'checked' : ''}>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">คุกกี้วิเคราะห์</h5>
                                        <p class="text-muted small mb-0">ช่วยให้เราเข้าใจวิธีที่ผู้ใช้โต้ตอบกับเว็บไซต์ของเรา และปรับปรุงประสบการณ์การใช้งาน</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="swal-analytics-cookies" ${analytics ? 'checked' : ''}>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">คุกกี้การตลาด</h5>
                                        <p class="text-muted small mb-0">ช่วยให้เราสามารถแสดงโฆษณาที่เกี่ยวข้องกับความสนใจของคุณ</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="swal-marketing-cookies" ${marketing ? 'checked' : ''}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'บันทึกการตั้งค่า',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#2DC679',
                    width: '32rem',
                    customClass: {
                        title: 'text-dark'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const preferences = {
                            necessary: true, // Always true
                            functional: document.getElementById('swal-functional-cookies').checked,
                            analytics: document.getElementById('swal-analytics-cookies').checked,
                            marketing: document.getElementById('swal-marketing-cookies').checked
                        };

                        localStorage.setItem('cookie_consent', 'custom');
                        localStorage.setItem('cookie_preferences', JSON.stringify(preferences));

                        // Show success notification
                        Swal.fire({
                            position: 'bottom-end',
                            icon: 'success',
                            title: 'บันทึกการตั้งค่าคุกกี้แล้ว',
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'colored-toast'
                            },
                            iconColor: '#2DC679'
                        });

                        // Apply cookie settings
                        applyCookieSettings(preferences);
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Refresh the page when user clicks Cancel
                        location.reload();
                    }
                });
            }

            function applyCookieSettings(preferences) {
                // Apply necessary cookies - always enabled

                // Apply functional cookies if enabled
                if (preferences.functional) {
                    // Example: enable theme preferences, language settings
                    console.log('Functional cookies enabled');
                }

                // Apply analytics cookies if enabled
                if (preferences.analytics) {
                    // Initialize analytics with respectful tracking
                    console.log('Analytics cookies enabled');
                    if (typeof gtag === 'function') {
                        // Google Analytics consent
                        gtag('consent', 'update', {
                            'analytics_storage': 'granted'
                        });
                    }
                } else {
                    // Deny analytics tracking
                    console.log('Analytics cookies disabled');
                    if (typeof gtag === 'function') {
                        gtag('consent', 'update', {
                            'analytics_storage': 'denied'
                        });
                    }
                }

                // Apply marketing cookies if enabled
                if (preferences.marketing) {
                    // Example: initialize ad personalization
                    console.log('Marketing cookies enabled');
                    if (typeof gtag === 'function') {
                        // Google Ads consent
                        gtag('consent', 'update', {
                            'ad_storage': 'granted',
                            'ad_user_data': 'granted',
                            'ad_personalization': 'granted'
                        });
                    }
                } else {
                    // Deny marketing tracking
                    console.log('Marketing cookies disabled');
                    if (typeof gtag === 'function') {
                        gtag('consent', 'update', {
                            'ad_storage': 'denied',
                            'ad_user_data': 'denied',
                            'ad_personalization': 'denied'
                        });
                    }
                }
            }
        </script>
    </body>
</html>
