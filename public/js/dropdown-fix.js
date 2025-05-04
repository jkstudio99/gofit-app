/**
* dropdown-fix.js
 * แก้ปัญหา dropdown ไม่ทำงานใน Bootstrap
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dropdown Fix loaded');

    // ตรวจสอบว่ามีการโหลดซ้ำหรือไม่
    if (window.dropdownFixLoaded) {
        console.log('Dropdown Fix already loaded - preventing duplicate initialization');
        return;
    }

    // ตั้งค่าตัวแปรเพื่อป้องกันการโหลดซ้ำ
    window.dropdownFixLoaded = true;

    // เรียกใช้ bootstrap tooltips
    try {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    } catch(e) {
        console.log('Tooltip initialization error:', e);
    }

    // ลบ event listener เดิมออกก่อน
    document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
        var newToggle = toggle.cloneNode(true);
        if (toggle.parentNode) {
            toggle.parentNode.replaceChild(newToggle, toggle);
        }
    });

    // แก้ไขทุก dropdown menu
    var dropdownMenus = document.querySelectorAll('.dropdown-menu');
    dropdownMenus.forEach(function(menu) {
        // กำหนด z-index และพื้นหลัง
        menu.style.zIndex = '9999';
        menu.style.backgroundColor = 'white';
        menu.style.position = 'absolute';
        menu.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
        menu.style.border = 'none';
        menu.style.borderRadius = '0.5rem';
    });

    // แก้ไข dropdown toggle
    var toggles = document.querySelectorAll('.dropdown-toggle');
    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // เก็บ reference ไปยัง dropdown menu ที่เกี่ยวข้อง
            var dropdownMenu = this.nextElementSibling;
            if (!dropdownMenu || !dropdownMenu.classList.contains('dropdown-menu')) {
                return; // ไม่พบ dropdown menu
            }

            // ปิด dropdown ทั้งหมดก่อน โดยไม่มีเงื่อนไข
            document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                menu.classList.remove('show');

                var otherToggle = menu.previousElementSibling;
                if (otherToggle && otherToggle.classList.contains('dropdown-toggle')) {
                    otherToggle.classList.remove('show');
                    otherToggle.setAttribute('aria-expanded', 'false');

                    if (otherToggle.parentNode) {
                        otherToggle.parentNode.classList.remove('show');
                    }
                }
            });

            // สลับสถานะของ dropdown ปัจจุบัน
            var isCurrentlyShown = dropdownMenu.classList.contains('show');

            if (isCurrentlyShown) {
                // ถ้าเปิดอยู่แล้ว ให้ปิด
                dropdownMenu.classList.remove('show');
                this.classList.remove('show');

                if (this.parentNode) {
                    this.parentNode.classList.remove('show');
                }

                this.setAttribute('aria-expanded', 'false');
            } else {
                // ถ้ายังไม่เปิด ให้เปิด
                dropdownMenu.classList.add('show');
                this.classList.add('show');

                if (this.parentNode) {
                    this.parentNode.classList.add('show');
                }

                this.setAttribute('aria-expanded', 'true');

                // แก้ไขตำแหน่งของ dropdown menu ให้ถูกต้อง
                dropdownMenu.style.top = '100%';
                dropdownMenu.style.left = '0';

                // ตรวจสอบว่า dropdown ออกนอกหน้าจอหรือไม่
                var menuRect = dropdownMenu.getBoundingClientRect();
                var viewportWidth = window.innerWidth || document.documentElement.clientWidth;

                if (menuRect.right > viewportWidth) {
                    // ถ้าออกนอกจอด้านขวา ให้แสดงทางด้านซ้าย
                    dropdownMenu.style.left = 'auto';
                    dropdownMenu.style.right = '0';
                }
            }
        });
    });

    // ปิด dropdown เมื่อคลิกนอกพื้นที่
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-menu') && !e.target.classList.contains('dropdown-toggle')) {
            document.querySelectorAll('.dropdown-menu.show, .dropdown-toggle.show, .dropdown.show').forEach(function(el) {
                el.classList.remove('show');

                if (el.classList.contains('dropdown-toggle')) {
                    el.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });

    // ทำให้ dropdown items ทำงานได้
    var dropdownItems = document.querySelectorAll('.dropdown-item');
    dropdownItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            if (!item.hasAttribute('href') || item.getAttribute('href') === '#') {
                e.preventDefault();
            }
            // ปิด dropdown หลังจาก click
            var dropdownMenu = item.closest('.dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.classList.remove('show');

                var toggle = dropdownMenu.previousElementSibling;
                if (toggle && toggle.classList.contains('dropdown-toggle')) {
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');

                    if (toggle.parentNode) {
                        toggle.parentNode.classList.remove('show');
                    }
                }
            }
        });
    });
});
