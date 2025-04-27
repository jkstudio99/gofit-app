/**
* dropdown-fix.js
 * แก้ปัญหา dropdown ไม่ทำงานใน Bootstrap
 */
document.addEventListener('DOMContentLoaded', function() {
    // เรียกใช้ bootstrap tooltips
    try {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    } catch(e) {
        console.log('Tooltip initialization error:', e);
    }

    // แก้ปัญหา dropdown โดยใช้ vanilla JavaScript
    var dropdownBtns = document.querySelectorAll('.dropdown-toggle');
    dropdownBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                // ปิด dropdown อื่นๆ ก่อน
                document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                    }
                });
                // สลับสถานะ dropdown ปัจจุบัน
                dropdownMenu.classList.toggle('show');
            }
        });
    });

    // ปิด dropdown เมื่อคลิกที่อื่น
    document.addEventListener('click', function(e) {
        var dropdownMenus = document.querySelectorAll('.dropdown-menu.show');
        dropdownMenus.forEach(function(menu) {
            if (!menu.previousElementSibling.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
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
            }
        });
    });
});
