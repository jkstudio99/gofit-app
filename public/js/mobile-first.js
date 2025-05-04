/**
 * GoFit - Mobile First Experience Enhancement
 * เวอร์ชัน 1.0.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // ตรวจสอบว่าเป็นอุปกรณ์มือถือหรือไม่
    const isMobile = window.innerWidth < 768;

    // ปรับแต่งเมนูมือถือ
    setupMobileMenu();

    // จัดการ Card transitions
    setupCardTransitions();

    // ปรับความสูงของ containers
    adjustContainerHeights();

    // ติดตามการเลื่อนและทำเอฟเฟกต์ปุ่มเมนูล่าง
    handleScrollEffects();

    // ตั้งค่า Lazy Loading สำหรับรูปภาพ
    setupLazyLoading();

    // เพิ่มเอฟเฟกต์ Ripple ให้กับปุ่ม
    setupRippleEffect();

    // แก้ไขปัญหา Double-tap บน iOS
    fixDoubleTapIssue();

    // ปรับปรุงรูปการแสดงผลพิกัดบนแผนที่
    improveMapExperience();
});

/**
 * จัดการเมนูมือถือให้ทำงานอย่างราบรื่น
 */
function setupMobileMenu() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const mobileMenu = document.querySelector('.navbar-collapse');

    if (navbarToggler && mobileMenu) {
        // ป้องกันการคลิกหลายครั้ง
        let isProcessing = false;

        // ปิดเมนูเมื่อคลิกนอกพื้นที่
        document.addEventListener('click', function(e) {
            if (mobileMenu.classList.contains('show') &&
                !mobileMenu.contains(e.target) &&
                e.target !== navbarToggler &&
                !navbarToggler.contains(e.target)) {
                closeMenu();
            }
        });

        // เพิ่ม transition ที่ราบรื่น
        function closeMenu() {
            if (isProcessing) return;
            isProcessing = true;

            if (mobileMenu.classList.contains('show')) {
                const collapse = bootstrap.Collapse.getInstance(mobileMenu);
                if (collapse) {
                    collapse.hide();
                } else {
                    mobileMenu.classList.remove('show');
                }

                // ปรับ transform เพื่อให้เคลื่อนที่ออกนอกหน้าจอ
                mobileMenu.style.transform = 'translateX(-100%)';
            }

            setTimeout(() => {
                isProcessing = false;
            }, 300);
        }

        // แก้ไขปัญหาเมื่อใช้งานบน iOS
        if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
            mobileMenu.style.cssText += '-webkit-overflow-scrolling: touch !important;';
        }
    }
}

/**
 * จัดการเอฟเฟกต์ transition ของการ์ด
 */
function setupCardTransitions() {
    // ค้นหาการ์ดทั้งหมด
    const cards = document.querySelectorAll('.card, .badge-item, .event-card, .reward-card, .article-card');

    cards.forEach(card => {
        // เพิ่ม transition
        card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';

        // เพิ่ม hover effect
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 5px rgba(0,0,0,0.05)';
        });
    });
}

/**
 * ปรับความสูงของ containers เพื่อการมองเห็นที่ดี
 */
function adjustContainerHeights() {
    // ปรับความสูงของแผนที่
    const mapContainers = document.querySelectorAll('.run-map-container, #map, #summaryMap');

    if (window.innerWidth < 576) { // Extra small devices
        mapContainers.forEach(container => {
            if (container) container.style.height = '250px';
        });
    } else if (window.innerWidth < 768) { // Small devices
        mapContainers.forEach(container => {
            if (container) container.style.height = '300px';
        });
    }

    // ปรับแต่งความสูงของรูปภาพในการ์ด
    const cardImages = document.querySelectorAll('.card-img-top, .event-image, .reward-image, .article-image');
    if (window.innerWidth < 576) {
        cardImages.forEach(img => {
            if (img) img.style.height = '160px';
        });
    }
}

/**
 * จัดการเอฟเฟกต์เมื่อมีการเลื่อนหน้าจอ
 */
function handleScrollEffects() {
    const bottomNav = document.querySelector('.mobile-nav');
    let lastScrollTop = 0;

    if (bottomNav) {
        window.addEventListener('scroll', function() {
            let st = window.pageYOffset || document.documentElement.scrollTop;

            // ซ่อนเมนูเมื่อเลื่อนลง แสดงเมื่อเลื่อนขึ้น
            if (st > lastScrollTop && st > 150) {
                // เลื่อนลง
                bottomNav.style.transform = 'translateY(100%)';
            } else {
                // เลื่อนขึ้น
                bottomNav.style.transform = 'translateY(0)';
            }

            lastScrollTop = st <= 0 ? 0 : st;
        }, false);
    }
}

/**
 * ตั้งค่า Lazy Loading สำหรับรูปภาพเพื่อประสิทธิภาพ
 */
function setupLazyLoading() {
    // ตรวจสอบว่า browser รองรับ IntersectionObserver หรือไม่
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');

                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                    }

                    observer.unobserve(img);
                }
            });
        });

        // ค้นหารูปภาพทั้งหมดที่มี data-src
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    }
}

/**
 * เพิ่มเอฟเฟกต์ Ripple ให้กับปุ่ม
 */
function setupRippleEffect() {
    const buttons = document.querySelectorAll('.btn, .mobile-nav-item');

    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const rect = button.getBoundingClientRect();
            const ripple = document.createElement('span');

            ripple.className = 'ripple-effect';
            ripple.style.position = 'absolute';
            ripple.style.width = '10px';
            ripple.style.height = '10px';
            ripple.style.background = 'rgba(255, 255, 255, 0.4)';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'scale(0)';
            ripple.style.pointerEvents = 'none';
            ripple.style.left = `${e.clientX - rect.left}px`;
            ripple.style.top = `${e.clientY - rect.top}px`;
            ripple.style.animation = 'ripple 0.6s linear';

            button.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // เพิ่ม keyframe animation ถ้ายังไม่มี
    if (!document.querySelector('style#ripple-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-style';
        style.innerHTML = `
            @keyframes ripple {
                to {
                    transform: scale(30);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

/**
 * แก้ไขปัญหา Double-tap บน iOS
 */
function fixDoubleTapIssue() {
    const clickables = document.querySelectorAll('a, button, .btn, [role="button"]');

    clickables.forEach(el => {
        el.addEventListener('touchend', function(e) {
            e.preventDefault();
            // เรียกใช้คลิกทันที
            const clickEvent = new MouseEvent('click', {
                view: window,
                bubbles: true,
                cancelable: true
            });
            e.target.dispatchEvent(clickEvent);
        });
    });
}

/**
 * ปรับปรุงประสบการณ์แผนที่บนมือถือ
 */
function improveMapExperience() {
    // ถ้ามีการใช้งาน Leaflet Map
    if (window.L && document.getElementById('map')) {
        // ตั้งค่าเพิ่มเติมสำหรับแผนที่บนมือถือ
        try {
            const map = window.runMap || window.map;
            if (map) {
                // ปรับ zoom control ให้อยู่มุมขวา
                map.zoomControl.setPosition('topright');

                // เพิ่ม Fullscreen control ถ้ามี plugin
                if (L.control.fullscreen) {
                    L.control.fullscreen({
                        position: 'topright',
                        title: 'เต็มหน้าจอ',
                        titleCancel: 'ออกจากเต็มหน้าจอ',
                        forceSeparateButton: true,
                    }).addTo(map);
                }

                // ปรับตัวเลือกสำหรับ touch interface
                map.options.tap = true;
                map.options.bounceAtZoomLimits = false;

                // ปรับ padding เมื่อ pan
                const mapEl = document.getElementById('map');
                if (mapEl) {
                    const height = mapEl.offsetHeight;
                    map.setMaxBounds(map.getBounds().pad(0.5));
                    map.on('drag', function() {
                        map.panInsideBounds(map.getBounds(), { animate: false });
                    });
                }
            }
        } catch (error) {
            console.log('Map enhancement failed', error);
        }
    }
}
