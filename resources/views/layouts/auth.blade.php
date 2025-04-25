<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GoFit - แอปพลิเคชันส่งเสริมการออกกำลังกายโดยใช้หลักการเกมมิฟิเคชัน">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GoFit') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // แก้ไขปัญหาไอคอนในช่องกรอกข้อมูล
            const fixInputIcons = function() {
                document.querySelectorAll('.input-icon-group').forEach(function(group) {
                    const input = group.querySelector('.form-control');
                    const icon = group.querySelector('.input-icon');
                    const toggleBtn = group.querySelector('.toggle-password');

                    if (input && icon) {
                        // คำนวณตำแหน่งไอคอนใหม่หากมีการ validation
                        const inputHeight = input.offsetHeight;
                        if (inputHeight > 0) {
                            icon.style.top = (inputHeight / 2) + 'px';
                        }
                    }

                    if (input && toggleBtn) {
                        // คำนวณตำแหน่งปุ่มเปิด/ปิดรหัสผ่านใหม่หากมีการ validation
                        const inputHeight = input.offsetHeight;
                        if (inputHeight > 0) {
                            toggleBtn.style.top = (inputHeight / 2) + 'px';
                        }
                    }
                });
            };

            // เรียกใช้ฟังก์ชันเมื่อโหลดหน้า
            fixInputIcons();

            // เรียกใช้ฟังก์ชันเมื่อมีการปรับขนาดหน้าจอ
            window.addEventListener('resize', fixInputIcons);

            // Toggle password visibility
            const togglePasswordButtons = document.querySelectorAll('.toggle-password');
            togglePasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const passwordInput = document.querySelector(this.getAttribute('toggle'));
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.innerHTML = '<i class="bx bx-hide"></i>';
                    } else {
                        passwordInput.type = 'password';
                        this.innerHTML = '<i class="bx bx-show"></i>';
                    }
                });
            });

            // กำหนดตำแหน่งของไอคอนอีกครั้งหลังจาก validation
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    setTimeout(fixInputIcons, 10);
                });
            });
        });
    </script>
</body>
</html>
