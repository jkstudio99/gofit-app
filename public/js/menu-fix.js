/**
 * Menu Fix สำหรับแก้ไขปัญหาเมนูและปุ่มในหน้า Admin
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Menu Fix loaded');

    // แก้ไขปัญหา Bootstrap Dropdown ที่ไม่ทำงาน
    fixDropdowns();

    // แก้ไขปัญหาปุ่มต่างๆ
    fixButtons();

    // แก้ไขปัญหา z-index
    fixZIndex();
});

/**
 * แก้ไขปัญหา Dropdown
 */
function fixDropdowns() {
    // ตรวจสอบว่าเป็น Bootstrap 5 หรือไม่
    var isBootstrap5 = typeof bootstrap !== 'undefined';

    // ปิด Dropdown เมื่อคลิกนอกพื้นที่
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-menu') && !e.target.classList.contains('dropdown-toggle')) {
            var openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(function(dropdown) {
                dropdown.classList.remove('show');
            });

            var openToggles = document.querySelectorAll('.dropdown-toggle.show');
            openToggles.forEach(function(toggle) {
                toggle.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.parentNode.classList.remove('show');
            });
        }
    });

    // จัดการ Dropdown Toggle โดยตรง
    var dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(function(toggle) {
        // ลบ event listener เดิมเพื่อป้องกันการซ้ำซ้อน
        var newToggle = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(newToggle, toggle);
        toggle = newToggle;

        // เพิ่ม event listener ใหม่
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // ตรวจสอบสถานะปัจจุบัน
            var isOpen = this.classList.contains('show');

            // ปิด dropdown อื่นๆ ที่เปิดอยู่
            document.querySelectorAll('.dropdown-toggle.show').forEach(function(otherToggle) {
                if (otherToggle !== toggle) {
                    otherToggle.classList.remove('show');
                    otherToggle.setAttribute('aria-expanded', 'false');

                    if (otherToggle.parentNode) {
                        otherToggle.parentNode.classList.remove('show');
                    }

                    var dropdownMenu = otherToggle.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                        dropdownMenu.classList.remove('show');
                    }
                }
            });

            // สลับสถานะ dropdown ปัจจุบัน
            this.classList.toggle('show');
            this.setAttribute('aria-expanded', !isOpen);

            // เพิ่มหรือลบคลาส show จาก dropdown parent
            if (this.parentNode && this.parentNode.classList.contains('dropdown')) {
                this.parentNode.classList.toggle('show');
            }

            // หา dropdown-menu และสลับคลาส show
            var dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                dropdownMenu.classList.toggle('show');

                // ตรวจสอบว่าเมนูแสดงถูกต้องหรือไม่
                if (dropdownMenu.classList.contains('show')) {
                    // ตรวจสอบตำแหน่งและปรับให้อยู่ในวิวพอร์ต
                    adjustDropdownPosition(dropdownMenu);
                }
            }
        });
    });

    // ทำให้แน่ใจว่า dropdown-item สามารถคลิกได้
    document.querySelectorAll('.dropdown-item').forEach(function(item) {
        item.style.pointerEvents = 'auto';
        item.style.cursor = 'pointer';
        // ลบ event listener เดิมเพื่อป้องกันการซ้ำซ้อน
        var newItem = item.cloneNode(true);
        item.parentNode.replaceChild(newItem, item);
    });
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
        dropdownMenu.style.bottom = '100%';
    }
}

/**
 * ปรับตำแหน่งของ dropdown menu ให้แสดงผลได้อย่างถูกต้อง
 */
function adjustDropdownPosition(dropdownMenu) {
    // ตรวจสอบขอบเขตของ viewport
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // ตำแหน่งของ dropdown
    var rect = dropdownMenu.getBoundingClientRect();

    // ตรวจสอบว่าเมนูออกนอกขอบด้านขวาหรือไม่
    if (rect.right > viewportWidth) {
        dropdownMenu.style.left = 'auto';
        dropdownMenu.style.right = '0';
    }

    // ตรวจสอบว่าเมนูออกนอกขอบด้านล่างหรือไม่
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.top = 'auto';
function fixButtons() {
    var clickableElements = document.querySelectorAll('a, button, .btn, .nav-link, [role="button"]');
    clickableElements.forEach(function(element) {
        // ตั้งค่า pointer-events เป็น auto
        element.style.pointerEvents = 'auto';

        // เพิ่ม event listener สำหรับการคลิก
        element.addEventListener('click', function(e) {
            // เช็คว่ามี href และไม่ใช่ # เปล่าๆ หรือ javascript:void(0)
            if (element.tagName === 'A' && element.getAttribute('href') &&
                element.getAttribute('href') !== '#' &&
                element.getAttribute('href') !== 'javascript:void(0)') {

                // ถ้าไม่ใช่ dropdown-toggle ให้ทำงานปกติ
                if (!element.classList.contains('dropdown-toggle')) {
                    // ถ้าไม่ได้กด Ctrl หรือ Command (ให้เปิดในแท็บใหม่) ให้นำทางไปหน้าใหม่
                    if (!e.ctrlKey && !e.metaKey && element.target !== '_blank') {
                        window.location.href = element.getAttribute('href');
                    }
                }
            }
        });
    });
}

/**
 * แก้ไขปัญหา z-index
 */
function fixZIndex() {
    // กำหนด z-index สำหรับส่วนต่างๆ ของ Navbar
    var navbar = document.querySelector('.navbar');
    if (navbar) {
        navbar.style.zIndex = '1030';
        navbar.style.position = 'relative';
    }

    // ตั้งค่า z-index สำหรับ navbar-nav
    var navs = document.querySelectorAll('.navbar-nav');
    navs.forEach(function(nav) {
        nav.style.zIndex = '1035';
        nav.style.position = 'relative';
    });

    // ตั้งค่า z-index สำหรับ nav-item
    var items = document.querySelectorAll('.nav-item');
    items.forEach(function(item) {
        item.style.zIndex = '1040';
        item.style.position = 'relative';
    });

    // ตั้งค่า z-index สำหรับ dropdown-toggle
    var toggles = document.querySelectorAll('.dropdown-toggle');
    toggles.forEach(function(toggle) {
        toggle.style.zIndex = '1045';
        toggle.style.position = 'relative';
    });

    // ตั้งค่า z-index สำหรับ dropdown-menu
    var menus = document.querySelectorAll('.dropdown-menu');
    menus.forEach(function(menu) {
        menu.style.zIndex = '1050';
    });
}
