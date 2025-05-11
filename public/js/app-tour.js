/**
 * GoFit Onboarding Tour System
 * เป็นระบบแนะนำการใช้งานแอพพลิเคชั่น GoFit แบบข้ามหน้า
 */

class GoFitTour {
    constructor() {
        this.tour = null;
        this.currentTourKey = null;
        this.tourSteps = {
            'dashboard': this.getDashboardTourSteps(),
            'run': this.getRunTourSteps(),
            'badges': this.getBadgesTourSteps(),
            'rewards': this.getRewardsTourSteps()
        };
        this.isInitialized = false;
    }

    /**
     * เริ่มต้นการทำงานของทัวร์
     */
    init() {
        if (this.isInitialized) return;

        // โหลด Shepherd CSS และ JS เพื่มเติม (ถ้ายังไม่มี)
        this.loadDependencies();

        // รอให้ Shepherd พร้อมใช้งาน
        this.waitForShepherd(() => {
            this.initializeTour();
            this.setupButtonListeners();
            this.setupTourNavigation();
            this.checkIfShouldShowTour();
            this.isInitialized = true;
        });
    }

    /**
     * ตรวจสอบและสร้าง CSRF token ถ้าจำเป็น
     */
    ensureCSRFToken() {
        // ตรวจสอบว่ามี CSRF token meta tag หรือไม่
        let csrfToken = document.querySelector('meta[name="csrf-token"]');

        // ถ้าไม่มี ให้สร้างขึ้นมาใหม่
        if (!csrfToken) {
            console.log('CSRF token meta tag not found, creating one');
            csrfToken = document.createElement('meta');
            csrfToken.setAttribute('name', 'csrf-token');
            csrfToken.setAttribute('content', '');
            document.head.appendChild(csrfToken);
        }

        // ตรวจสอบว่า content ว่างหรือไม่
        if (!csrfToken.getAttribute('content')) {
            console.log('CSRF token is empty');

            // ลองค้นหา CSRF token จากฟอร์มอื่นๆ ในหน้า
            const csrfInput = document.querySelector('input[name="_token"]');
            if (csrfInput && csrfInput.value) {
                console.log('Found CSRF token in form input, using that value');
                csrfToken.setAttribute('content', csrfInput.value);
            } else {
                // ถ้าไม่พบในฟอร์ม ให้ลองค้นหาจาก cookie
                const cookies = document.cookie.split(';');
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i].trim();
                    if (cookie.startsWith('XSRF-TOKEN=')) {
                        const token = decodeURIComponent(cookie.substring('XSRF-TOKEN='.length));
                        console.log('Found CSRF token in cookie, using that value');
                        csrfToken.setAttribute('content', token);
                        break;
                    }
                }
            }
        }

        return csrfToken;
    }

    /**
     * โหลด CSS และ JS ที่จำเป็น
     */
    loadDependencies() {
        // ตรวจสอบว่ามี Shepherd ทำงานแล้วหรือไม่
        if (typeof Shepherd !== 'undefined') return;

        // โหลด CSS
        const cssLink = document.createElement('link');
        cssLink.rel = 'stylesheet';
        cssLink.href = 'https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css';
        document.head.appendChild(cssLink);

        // โหลด JS
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js';
        document.head.appendChild(script);
    }

    /**
     * รอให้ Shepherd โหลดเสร็จ
     */
    waitForShepherd(callback) {
        const checkShepherd = () => {
            if (typeof Shepherd !== 'undefined') {
                callback();
            } else {
                setTimeout(checkShepherd, 100);
            }
        };
        checkShepherd();
    }

    /**
     * สร้าง Tour object
     */
    initializeTour() {
        // ตรวจสอบและสร้าง CSRF token ในกรณีที่ไม่มี
        this.ensureCSRFToken();

        this.tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                classes: 'shadow-lg rounded',
                scrollTo: true,
                cancelIcon: {
                    enabled: true
                },
                modalOverlayOpeningRadius: 10,
                highlightClass: 'shepherd-highlight',
                floatingUIOptions: {
                    middleware: [
                        {
                            name: 'offset',
                            options: {
                                offset: [0, 16],
                            },
                        },
                    ],
                },
                popperOptions: {
                    modifiers: [{ name: 'offset', options: { offset: [0, 16] } }]
                },
                classes: 'gofit-tour-step',
                when: {
                    show: () => {
                        // Add custom styling for buttons
                        setTimeout(() => {
                            // Primary buttons (Next, Continue)
                            document.querySelectorAll('.shepherd-button:not(.shepherd-button-secondary):not(.shepherd-button-back)').forEach(btn => {
                                btn.classList.add('gofit-primary-btn');
                                btn.style.backgroundColor = '#2ecc71';
                                btn.style.borderColor = '#2ecc71';
                                btn.style.color = 'white';
                                btn.style.borderRadius = '50px';
                                btn.style.padding = '0.5rem 1.5rem';
                                btn.style.transition = 'all 0.2s ease';
                            });

                            // Secondary buttons (Skip)
                            document.querySelectorAll('.shepherd-button-secondary').forEach(btn => {
                                btn.classList.add('gofit-secondary-btn');
                                btn.style.backgroundColor = 'transparent';
                                btn.style.borderColor = '#3498db';
                                btn.style.color = '#3498db';
                                btn.style.borderRadius = '50px';
                                btn.style.padding = '0.5rem 1.5rem';
                                btn.style.transition = 'all 0.2s ease';
                            });

                            // Back buttons
                            document.querySelectorAll('.shepherd-button-back').forEach(btn => {
                                btn.classList.add('gofit-back-btn');
                                btn.style.backgroundColor = 'transparent';
                                btn.style.borderColor = '#95a5a6';
                                btn.style.color = '#95a5a6';
                                btn.style.borderRadius = '50px';
                                btn.style.padding = '0.5rem 1.5rem';
                                btn.style.transition = 'all 0.2s ease';
                            });
                        }, 100);
                    }
                }
            },
            exitOnEsc: true,
            keyboardNavigation: true,
            styleVariables: {
                shepherdButtonBorderRadius: '50px',
                shepherdElementBorderRadius: '16px',
                shepherdHeaderBackground: '#2ecc71', // สีหลักของ GoFit
                shepherdThemeTextColor: '#333333',
                shepherdThemePrimary: '#2ecc71',
                shepherdThemeSecondary: '#3498db'
            }
        });

        // เมื่อ Tour จบแล้ว
        this.tour.on('complete', () => {
            if (this.currentTourKey) {
                this.markTourAsCompleted(this.currentTourKey)
                    .then(() => {
                        console.log(`Tour ${this.currentTourKey} completed successfully`);
                    })
                    .catch(error => {
                        console.error(`Error completing tour ${this.currentTourKey}:`, error);
                    });
            }
        });

        // เมื่อกดปิด (cancel) Tour
        this.tour.on('cancel', () => {
            if (this.currentTourKey) {
                this.markTourAsSkipped(this.currentTourKey)
                    .then(() => {
                        console.log(`Tour ${this.currentTourKey} cancelled and marked as skipped`);
                        // รีโหลดหน้าเพื่อให้แน่ใจว่า UI อัปเดต
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error(`Error skipping tour ${this.currentTourKey}:`, error);
                        // รีโหลดหน้าเพื่อให้แน่ใจว่า UI อัปเดต แม้จะมี error
                        window.location.reload();
                    });
            }
        });

        // เพิ่ม event listener สำหรับปุ่ม X บนหน้าต่าง tour
        document.addEventListener('click', (e) => {
            if (e.target.closest('.shepherd-cancel-icon')) {
                if (this.currentTourKey) {
                    // ทำ preventDefault เพื่อไม่ให้ tour ทำงานต่อตามปกติ
                    e.preventDefault();
                    e.stopPropagation();

                    // บันทึกข้อมูลใน localStorage โดยตรง
                    localStorage.setItem(`tour_${this.currentTourKey}_skipped`, 'true');
                    console.log(`Marking tour ${this.currentTourKey} as skipped via X button`);

                    // ใช้ XMLHttpRequest แทน fetch
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/tour/update', true);

                    // ดึง CSRF token จาก meta
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken && csrfToken.getAttribute('content')) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                    }

                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('Accept', 'application/json');

                    xhr.onload = () => {
                        if (xhr.status >= 200 && xhr.status < 400) {
                            console.log(`Tour ${this.currentTourKey} marked as skipped successfully via X button`);
                        } else {
                            console.warn(`Error marking tour ${this.currentTourKey} as skipped via X button. Status: ${xhr.status}`);
                        }

                        // หยุด tour ก่อนรีโหลดหน้า
                        if (this.tour && this.tour.isActive()) {
                            this.tour.cancel();
                        }

                        // รีโหลดหน้าเพื่อแสดงผลการข้ามทัวร์
                            setTimeout(() => {
                            const timestamp = new Date().getTime();
                            window.location.href = window.location.pathname + `?_=${timestamp}`;
                            }, 100);
                    };

                    xhr.onerror = () => {
                        console.warn('Network error when marking tour as skipped via X button');
                        // หยุด tour ก่อนรีโหลดหน้า
                        if (this.tour && this.tour.isActive()) {
                            this.tour.cancel();
                        }
                            setTimeout(() => {
                            const timestamp = new Date().getTime();
                            window.location.href = window.location.pathname + `?_=${timestamp}`;
                            }, 100);
                    };

                    // ส่งข้อมูลไปยังเซิร์ฟเวอร์
                    try {
                        xhr.send(JSON.stringify({
                            tour_key: this.currentTourKey,
                            status: 'skipped',
                            show_again: false
                        }));
                    } catch (e) {
                        console.warn('Error sending request:', e);
                        // หยุด tour ก่อนรีโหลดหน้า
                        if (this.tour && this.tour.isActive()) {
                            this.tour.cancel();
                        }
                        setTimeout(() => {
                            const timestamp = new Date().getTime();
                            window.location.href = window.location.pathname + `?_=${timestamp}`;
                        }, 100);
                    }

                    // ต้องคืนค่า false เพื่อไม่ให้ event propagate ไปที่อื่น
                    return false;
                }
            }
        }, true); // ใช้ event capturing phase
    }

    /**
     * เพิ่ม event listener สำหรับการนำทางระหว่างทัวร์
     */
    setupTourNavigation() {
        // เพิ่ม event listener สำหรับการคลิกที่เหรียญตรา
        document.addEventListener('click', (e) => {
            // ตรวจสอบว่ากำลังแสดงทัวร์อยู่หรือไม่
            if (!this.tour || !this.tour.isActive() || !this.currentTourKey) {
                return;
            }

            // ตรวจสอบว่า currentTourKey เป็น badges และคลิกที่เหรียญตรา
            if (this.currentTourKey === 'badges' &&
                (e.target.closest('.badge-item') || e.target.closest('.badge-card') || e.target.closest('.badge-icon'))) {

                e.preventDefault();
                e.stopPropagation();

                // บันทึกว่าทัวร์เสร็จสิ้น
                localStorage.setItem(`tour_${this.currentTourKey}_completed`, 'true');
                console.log(`Marking tour ${this.currentTourKey} as completed from badge click`);

                // สร้าง CSRF token ในกรณีที่ไม่มี
                this.ensureCSRFToken();

                // สร้าง XHR request
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/tour/update', true);

                // ดึง CSRF token จาก meta
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken && csrfToken.getAttribute('content')) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                }

                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.onload = () => {
                    // เคลียร์ทัวร์
                    if (this.tour) {
                        this.tour.complete();
                    }

                    // นำทางไปยังหน้ารางวัล
                    setTimeout(() => {
                        const timestamp = new Date().getTime();
                        window.location.href = `/rewards?tour=show&_=${timestamp}`;
                    }, 300);
                };

                xhr.onerror = () => {
                    // เคลียร์ทัวร์และนำทางไปยังหน้ารางวัลแม้มี error
                    if (this.tour) {
                        this.tour.complete();
                    }
                    setTimeout(() => {
                        const timestamp = new Date().getTime();
                        window.location.href = `/rewards?tour=show&_=${timestamp}`;
                    }, 300);
                };

                // ส่งข้อมูลไปยังเซิร์ฟเวอร์
                try {
                    xhr.send(JSON.stringify({
                        tour_key: this.currentTourKey,
                        status: 'completed',
                        show_again: false
                    }));
                } catch (e) {
                    // เคลียร์ทัวร์และนำทางไปยังหน้ารางวัลแม้มี error
                    if (this.tour) {
                        this.tour.complete();
                    }
                    setTimeout(() => {
                        const timestamp = new Date().getTime();
                        window.location.href = `/rewards?tour=show&_=${timestamp}`;
                    }, 300);
                }

                return false;
            }
        }, true);
    }

    /**
     * ตรวจสอบว่าควรแสดงทัวร์หรือไม่ตามหน้าปัจจุบัน
     */
    checkIfShouldShowTour() {
        // ตรวจสอบว่าอยู่หน้าไหน
        const currentPath = window.location.pathname;
        let tourKey = 'dashboard'; // default

        if (currentPath.includes('/run')) {
            tourKey = 'run';
        } else if (currentPath.includes('/badges')) {
            tourKey = 'badges';
        } else if (currentPath.includes('/rewards')) {
            tourKey = 'rewards';
        } else if (currentPath === '/' || currentPath.includes('/dashboard')) {
            tourKey = 'dashboard';
        } else {
            // ถ้าไม่ใช่หน้าหลักที่จะแสดงทัวร์ ก็ไม่ต้องทำอะไร
            return;
        }

        // เก็บ tourKey ปัจจุบัน
        this.currentTourKey = tourKey;

        // ตรวจสอบ localStorage ก่อนว่าเคยข้ามหรือทำเสร็จแล้วหรือไม่
        if (localStorage.getItem(`tour_${tourKey}_skipped`) === 'true' ||
            localStorage.getItem(`tour_${tourKey}_completed`) === 'true') {
            console.log(`Tour ${tourKey} was previously skipped or completed, not showing.`);
            return;
        }

        // ตรวจสอบและสร้าง CSRF token ในกรณีที่ไม่มี
        this.ensureCSRFToken();

        // ตรวจสอบสถานะของทัวร์จาก server
        fetch(`/tour/status?tour_key=${tourKey}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                // ถ้าควรแสดงทัวร์
                if (data.shouldShow) {
                    // ตรวจสอบว่ามี URL parameter tour=skip หรือไม่
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('tour') === 'skip') {
                        // ข้ามการแสดงทัวร์และบันทึกว่าทัวร์ถูกข้าม
                        localStorage.setItem(`tour_${tourKey}_skipped`, 'true');

                        // ใช้ XMLHttpRequest แทน fetch
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '/tour/update', true);

                        // ดึง CSRF token จาก meta
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken && csrfToken.getAttribute('content')) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                        }

                        xhr.setRequestHeader('Content-Type', 'application/json');
                        xhr.setRequestHeader('Accept', 'application/json');

                        xhr.onload = () => {
                            if (xhr.status >= 200 && xhr.status < 400) {
                                console.log(`Tour ${tourKey} marked as skipped via URL parameter`);
                            } else {
                                console.warn(`Error marking tour ${tourKey} as skipped via URL parameter`);
                            }

                                // ลบ parameter และรีโหลดหน้า
                                const url = new URL(window.location);
                                url.searchParams.delete('tour');
                                window.history.pushState({}, '', url);
                        };

                        xhr.onerror = () => {
                            console.warn('Network error when marking tour as skipped via URL parameter');
                            // ลบ parameter และรีโหลดหน้า
                            const url = new URL(window.location);
                            url.searchParams.delete('tour');
                            window.history.pushState({}, '', url);
                        };

                        // ส่งข้อมูลไปยังเซิร์ฟเวอร์
                        try {
                            xhr.send(JSON.stringify({
                                tour_key: tourKey,
                                status: 'skipped',
                                show_again: false
                            }));
                        } catch (e) {
                            console.warn('Error sending request:', e);
                            // ลบ parameter และรีโหลดหน้า
                            const url = new URL(window.location);
                            url.searchParams.delete('tour');
                            window.history.pushState({}, '', url);
                        }

                        return;
                    }

                    // ล้าง steps เก่าก่อน
                    if (this.tour && this.tour.steps) {
                        this.tour.steps.forEach(step => this.tour.removeStep(step.id));
                    }

                    // เพิ่ม steps ตาม tourKey
                    if (this.tourSteps[tourKey]) {
                        this.tourSteps[tourKey].forEach(step => {
                            this.tour.addStep(step);
                        });
                    }

                    // เริ่มแสดงทัวร์
                    setTimeout(() => {
                        this.startTour();
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error checking tour status:', error);
            });
    }

    /**
     * เริ่มแสดงทัวร์
     */
    startTour() {
        if (this.tour.isActive()) {
            return;
        }

        // แสดง tour
        this.tour.start();
    }

    /**
     * จบทัวร์และไปยังหน้าถัดไป
     */
    completeAndGoToNextPage(nextPage) {
        if (this.currentTourKey) {
            // บันทึกว่าทัวร์เสร็จสิ้นและตั้งค่า localStorage
            this.markTourAsCompleted(this.currentTourKey)
                .then(() => {
                    // เคลียร์ tour ปัจจุบันก่อนเปลี่ยนหน้า
                    if (this.tour) {
                        this.tour.complete();
                    }

                    // รอให้ API เสร็จก่อนแล้วค่อยเปลี่ยนหน้า
                    setTimeout(() => {
                        // เพิ่มพารามิเตอร์เวลาเพื่อป้องกันการแคช
                        const timestamp = new Date().getTime();
                        const separator = nextPage.includes('?') ? '&' : '?';
                        window.location.href = `${nextPage}${separator}_=${timestamp}`;
                    }, 500);
                })
                .catch(error => {
                    console.error('Error completing tour:', error);
                    // นำทางไปยังหน้าถัดไปถึงแม้จะเกิด error
                    const timestamp = new Date().getTime();
                    const separator = nextPage.includes('?') ? '&' : '?';
                    setTimeout(() => {
                        window.location.href = `${nextPage}${separator}_=${timestamp}`;
                    }, 500);
                });
        } else {
            // หากไม่มี tourKey ให้นำทางไปหน้าถัดไปเลย
            const timestamp = new Date().getTime();
            const separator = nextPage.includes('?') ? '&' : '?';
            setTimeout(() => {
                window.location.href = `${nextPage}${separator}_=${timestamp}`;
            }, 500);
        }
    }

    /**
     * บันทึกว่าทัวร์เสร็จสิ้นแล้ว
     */
    markTourAsCompleted(tourKey) {
        console.log('Marking tour as completed:', tourKey);

        // เก็บ flag ใน localStorage ทันที - ทำทันทีเพื่อให้แน่ใจว่าจะทำงานได้แม้มี error
        localStorage.setItem(`tour_${tourKey}_completed`, 'true');

        // สร้าง Promise ที่ resolve เสมอเพื่อให้การทำงานดำเนินต่อไปได้
        return new Promise((resolve) => {
            try {
                // ตรวจสอบว่ามี CSRF token หรือไม่
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken || !csrfToken.getAttribute('content')) {
                    console.log('No valid CSRF token found, skipping server update');
                    // ไม่มี CSRF token ที่ถูกต้อง ให้ resolve ทันที
                    resolve({success: true, local: true});
                    return;
                }

                // ส่งข้อมูลไปยัง server เฉพาะเมื่อมี CSRF token ที่ถูกต้อง
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/tour/update', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                xhr.setRequestHeader('Accept', 'application/json');

                // กำหนดเวลาหมดเวลา
                xhr.timeout = 3000; // 3 วินาที

                xhr.onload = function() {
                    if (xhr.status === 419) {
                        // กรณี CSRF token หมดอายุ (Laravel specific)
                        console.log('CSRF token expired or invalid, but tour state saved locally');
                        resolve({success: true, local: true});
                    } else if (xhr.status >= 200 && xhr.status < 300) {
                        // สำเร็จ
                        try {
                            const data = JSON.parse(xhr.responseText);
                            console.log('Tour marked as completed successfully:', data);
                            resolve(data);
                        } catch (e) {
                            console.warn('Response not JSON but tour state saved locally');
                            resolve({success: true, local: true});
                        }
                    } else {
                        // ไม่สำเร็จ แต่ไม่เป็นไรเพราะเราบันทึกใน localStorage แล้ว
                        console.warn(`Server returned status ${xhr.status}, but tour state saved locally`);
                        resolve({success: true, local: true});
                    }
                };

                // กรณีหมดเวลา
                xhr.ontimeout = function() {
                    console.warn('Request timed out, but tour state saved locally');
                    resolve({success: true, local: true});
                };

                // กรณีมี error อื่นๆ
                xhr.onerror = function() {
                    console.warn('Network error, but tour state saved locally');
                    resolve({success: true, local: true});
                };

                // ส่ง request
                try {
                    xhr.send(JSON.stringify({
                tour_key: tourKey,
                status: 'completed',
                show_again: false
                    }));
                } catch (e) {
                    console.warn('Error sending request, but tour state saved locally:', e);
                    resolve({success: true, local: true});
                }
            } catch (e) {
                // กรณีมี error ในการทำงานของฟังก์ชัน
                console.warn('Error in markTourAsCompleted function, but state saved locally:', e);
                resolve({success: true, local: true});
            }
        });
    }

    /**
     * บันทึกว่าทัวร์ถูกข้าม
     */
    markTourAsSkipped(tourKey) {
        // แสดง indicator ว่ากำลังบันทึก
        console.log('Marking tour as skipped:', tourKey);

        // เก็บ flag ใน localStorage ทันที - ทำทันทีเพื่อให้แน่ใจว่าจะทำงานได้แม้มี error
        localStorage.setItem(`tour_${tourKey}_skipped`, 'true');

        // สร้าง Promise ที่ resolve เสมอเพื่อให้การทำงานดำเนินต่อไปได้
        return new Promise((resolve) => {
            try {
                // ตรวจสอบว่ามี CSRF token หรือไม่
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken || !csrfToken.getAttribute('content')) {
                    console.log('No valid CSRF token found, skipping server update');
                    // ไม่มี CSRF token ที่ถูกต้อง ให้ resolve ทันที
                    resolve({success: true, local: true});
                    return;
                }

                // ส่งข้อมูลไปยัง server เฉพาะเมื่อมี CSRF token ที่ถูกต้อง
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/tour/update', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                xhr.setRequestHeader('Accept', 'application/json');

                // กำหนดเวลาหมดเวลา
                xhr.timeout = 3000; // 3 วินาที

                xhr.onload = function() {
                    if (xhr.status === 419) {
                        // กรณี CSRF token หมดอายุ (Laravel specific)
                        console.log('CSRF token expired or invalid, but tour state saved locally');
                        resolve({success: true, local: true});
                    } else if (xhr.status >= 200 && xhr.status < 300) {
                        // สำเร็จ
                        try {
                            const data = JSON.parse(xhr.responseText);
                            console.log('Tour marked as skipped successfully:', data);
                            resolve(data);
                        } catch (e) {
                            console.warn('Response not JSON but tour state saved locally');
                            resolve({success: true, local: true});
                        }
                    } else {
                        // ไม่สำเร็จ แต่ไม่เป็นไรเพราะเราบันทึกใน localStorage แล้ว
                        console.warn(`Server returned status ${xhr.status}, but tour state saved locally`);
                        resolve({success: true, local: true});
                    }
                };

                // กรณีหมดเวลา
                xhr.ontimeout = function() {
                    console.warn('Request timed out, but tour state saved locally');
                    resolve({success: true, local: true});
                };

                // กรณีมี error อื่นๆ
                xhr.onerror = function() {
                    console.warn('Network error, but tour state saved locally');
                    resolve({success: true, local: true});
                };

                // ส่ง request
                try {
                    xhr.send(JSON.stringify({
                tour_key: tourKey,
                status: 'skipped',
                show_again: false
                    }));
                } catch (e) {
                    console.warn('Error sending request, but tour state saved locally:', e);
                    resolve({success: true, local: true});
                }
            } catch (e) {
                // กรณีมี error ในการทำงานของฟังก์ชัน
                console.warn('Error in markTourAsSkipped function, but state saved locally:', e);
                resolve({success: true, local: true});
            }
        });
    }

    /**
     * เพิ่มปุ่มเริ่มทัวร์
     */
    setupButtonListeners() {
        document.addEventListener('click', (e) => {
            // ถ้าคลิกที่ปุ่มเริ่มทัวร์
            if (e.target.matches('#start-tour-btn') || e.target.closest('#start-tour-btn')) {
                e.preventDefault();
                this.startTour();
            }
        });
    }

    /**
     * ขั้นตอนทัวร์สำหรับหน้า Dashboard
     */
    getDashboardTourSteps() {
        return [
            {
                id: 'welcome',
                title: 'ยินดีต้อนรับสู่ GoFit',
                text: 'ขอต้อนรับเข้าสู่แอพพลิเคชั่น GoFit สำหรับการออกกำลังกายด้วยการวิ่งและเก็บสะสมเหรียญรางวัล เราจะพาคุณเรียนรู้การใช้งานพื้นฐาน',
                buttons: [
                    {
                        text: 'ข้าม',
                        action: () => this.tour.cancel()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ],
                when: {
                    show: () => {
                        // เพิ่ม class highlight ให้กับ element ที่ต้องการ highlight
                        document.querySelector('.welcome-header')?.classList.add('highlight-element');
                    },
                    hide: () => {
                        // ลบ class highlight เมื่อซ่อน step
                        document.querySelector('.welcome-header')?.classList.remove('highlight-element');
                    }
                }
            },
            {
                id: 'dashboard-stats',
                title: 'สถิติการวิ่ง',
                text: 'ส่วนนี้แสดงสถิติโดยรวมของคุณ ทั้งระยะทางสะสม แคลอรี่ที่เผาผลาญ และจำนวนกิจกรรมที่ทำไปแล้ว',
                attachTo: {
                    element: () => {
                        // เลือก element ตามขนาดหน้าจอ
                        return window.innerWidth < 768 ? '.mobile-stats' : '.desktop-stats';
                    },
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ],
                popperOptions: {
                    placement: 'bottom',
                    modifiers: [
                        {
                            name: 'offset',
                            options: {
                                offset: [0, 15]
                            }
                        },
                        {
                            name: 'arrow',
                            options: {
                                element: '[data-popper-arrow]',
                                padding: 10
                            }
                        },
                        {
                            name: 'computeStyles',
                            options: {
                                gpuAcceleration: false
                            }
                        },
                        {
                            name: 'preventOverflow',
                            options: {
                                boundary: document.body,
                                padding: 10
                            }
                        }
                    ]
                }
            },
            {
                id: 'weekly-progress',
                title: 'ความคืบหน้ารายสัปดาห์',
                text: 'ที่นี่คุณจะเห็นความคืบหน้าของการวิ่งในสัปดาห์นี้เทียบกับเป้าหมายที่ตั้งไว้',
                attachTo: {
                    element: '.weekly-progress-section',
                    on: 'top'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'recent-activities',
                title: 'ประวัติการวิ่งล่าสุด',
                text: 'ส่วนนี้แสดงประวัติการวิ่งล่าสุดของคุณ คุณสามารถดูประวัติทั้งหมดได้โดยคลิกที่ "ดูทั้งหมด"',
                attachTo: {
                    element: '.recent-activities-section',
                    on: 'top'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'start-run-button',
                title: 'เริ่มวิ่งกันเลย!',
                text: 'คุณสามารถเริ่มบันทึกการวิ่งได้โดยคลิกที่ปุ่ม "เริ่มวิ่งเลย" ต้องการไปที่หน้าวิ่งเลยไหม?',
                attachTo: {
                    element: '.welcome-header .btn-lg',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'ไม่ ฉันจะสำรวจเอง',
                        action: () => this.tour.complete()
                    },
                    {
                        text: 'ไปที่หน้าวิ่ง',
                        action: () => {
                            this.completeAndGoToNextPage('/run?tour=show');
                        }
                    }
                ]
            }
        ];
    }

    /**
     * ขั้นตอนทัวร์สำหรับหน้าวิ่ง
     */
    getRunTourSteps() {
        return [
            {
                id: 'run-page-welcome',
                title: 'หน้าบันทึกการวิ่ง',
                text: 'นี่คือหน้าสำหรับบันทึกกิจกรรมการวิ่งของคุณ คุณสามารถเริ่มวิ่งแบบเรียลไทม์หรือบันทึกย้อนหลังได้',
                buttons: [
                    {
                        text: 'ข้าม',
                        action: () => this.tour.cancel()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'run-map',
                title: 'แผนที่การวิ่ง',
                text: 'แผนที่นี้จะแสดงตำแหน่งและเส้นทางการวิ่งของคุณ ระบบจะติดตามการเคลื่อนไหวโดยใช้ GPS ของอุปกรณ์',
                attachTo: {
                    element: '#map',
                    on: 'top'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'run-stats',
                title: 'สถิติการวิ่ง',
                text: 'ส่วนนี้จะแสดงข้อมูลสถิติการวิ่งแบบเรียลไทม์ ทั้งระยะทาง เวลา ความเร็ว และแคลอรี่ที่เผาผลาญ',
                attachTo: {
                    element: '.run-stat',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'run-start-button',
                title: 'เริ่มวิ่ง',
                text: 'คลิกที่นี่เพื่อเริ่มบันทึกการวิ่งแบบเรียลไทม์ คุณสามารถหยุดชั่วคราวหรือหยุดการวิ่งได้ตลอดเวลา',
                attachTo: {
                    element: '#startRunBtn',
                    on: 'top'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'run-mode-button',
                title: 'โหมดการวิ่ง',
                text: 'คุณสามารถเลือกระหว่างโหมดจำลองและโหมด GPS จริงได้ตามความต้องการ',
                attachTo: {
                    element: '#toggleModeBtn',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'run-history-button',
                title: 'ประวัติการวิ่ง',
                text: 'คุณสามารถดูประวัติการวิ่งทั้งหมดของคุณได้ที่นี่',
                attachTo: {
                    element: '.btn-outline-primary.rounded-pill',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ไปที่หน้าเหรียญตรา',
                        action: () => {
                            this.completeAndGoToNextPage('/badges?tour=show');
                        }
                    }
                ]
            }
        ];
    }

    /**
     * ขั้นตอนทัวร์สำหรับหน้าเหรียญตรา
     */
    getBadgesTourSteps() {
        return [
            {
                id: 'badges-welcome',
                title: 'หน้าเหรียญตรา',
                text: 'ยินดีต้อนรับสู่หน้าเหรียญตรา ที่นี่คุณจะเห็นเหรียญตราที่คุณได้รับจากความสำเร็จในการวิ่ง',
                buttons: [
                    {
                        text: 'ข้าม',
                        action: () => {
                            // ทำการ mark tour เป็น skipped ในฐานข้อมูลและบันทึกใน localStorage
                            localStorage.setItem(`tour_${this.currentTourKey}_skipped`, 'true');
                            console.log(`Marking tour ${this.currentTourKey} as skipped directly`);

                            // สร้างข้อมูล CSRF token ในกรณีที่ไม่มี
                            this.ensureCSRFToken();

                            // สร้าง XHR request แบบเดียวกับหน้ารางวัล
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', '/tour/update', true);

                            // ดึง CSRF token จาก meta หลังจากสร้างใหม่ถ้าจำเป็น
                            const csrfToken = document.querySelector('meta[name="csrf-token"]');
                            if (csrfToken && csrfToken.getAttribute('content')) {
                                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                            }

                            xhr.setRequestHeader('Content-Type', 'application/json');
                            xhr.setRequestHeader('Accept', 'application/json');

                            xhr.onload = () => {
                                if (xhr.status >= 200 && xhr.status < 400) {
                                    console.log(`Tour ${this.currentTourKey} marked as skipped successfully on server`);
                                } else {
                                    console.warn(`Error marking tour ${this.currentTourKey} as skipped on server. Status: ${xhr.status}`);
                                }

                                // ยกเลิกทัวร์และรีโหลดหน้าไม่ว่าคำขอจะสำเร็จหรือไม่
                                if (this.tour && this.tour.isActive()) {
                                    this.tour.cancel();
                                }

                                // เพิ่ม timestamp เพื่อป้องกันการแคช
                                    const timestamp = new Date().getTime();
                                    window.location.href = window.location.pathname + `?_=${timestamp}`;
                            };

                            xhr.onerror = () => {
                                console.warn('Network error when marking tour as skipped');
                                // รีโหลดหน้าแม้ว่าจะมี error
                                if (this.tour && this.tour.isActive()) {
                                    this.tour.cancel();
                                }
                                    const timestamp = new Date().getTime();
                                    window.location.href = window.location.pathname + `?_=${timestamp}`;
                            };

                            // ส่งข้อมูลไปยังเซิร์ฟเวอร์
                            try {
                                xhr.send(JSON.stringify({
                                    tour_key: this.currentTourKey,
                                    status: 'skipped',
                                    show_again: false
                                }));
                            } catch (e) {
                                console.warn('Error sending request:', e);
                                // ยกเลิกทัวร์และรีโหลดหน้า
                                if (this.tour && this.tour.isActive()) {
                                    this.tour.cancel();
                                }
                                const timestamp = new Date().getTime();
                                window.location.href = window.location.pathname + `?_=${timestamp}`;
                            }
                        }
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'badges-stats',
                title: 'สถิติเหรียญตรา',
                text: 'ส่วนนี้แสดงสถิติเหรียญตราของคุณ ทั้งจำนวนทั้งหมด จำนวนที่ปลดล็อคแล้ว และความคืบหน้า',
                attachTo: {
                    element: '.badge-stat-card',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'badges-categories',
                title: 'ประเภทเหรียญ',
                text: 'เหรียญตราถูกแบ่งเป็นหลายประเภท เช่น ระยะทาง แคลอรี่ ความเร็ว ตามความสำเร็จที่คุณทำได้',
                attachTo: {
                    element: '#badgeTypeTabs',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'badges-collection',
                title: 'คอลเลกชันเหรียญ',
                text: 'คุณสามารถดูเหรียญตราทั้งหมดที่นี่ เหรียญที่เป็นสีทึบคือเหรียญที่คุณปลดล็อคแล้ว ส่วนเหรียญที่เป็นสีจางคือเหรียญที่คุณยังไม่ได้ปลดล็อค',
                attachTo: {
                    element: '.badge-card',
                    on: 'top'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ไปที่หน้ารางวัล',
                        action: () => {
                            // บันทึกว่าทัวร์เสร็จสิ้นและนำทางไปยังหน้ารางวัล
                            localStorage.setItem(`tour_${this.currentTourKey}_completed`, 'true');
                            console.log(`Marking tour ${this.currentTourKey} as completed directly`);

                            // สร้างข้อมูล CSRF token ในกรณีที่ไม่มี
                            this.ensureCSRFToken();

                            // สร้าง XHR request
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', '/tour/update', true);

                            // ดึง CSRF token จาก meta หลังจากสร้างใหม่ถ้าจำเป็น
                            const csrfToken = document.querySelector('meta[name="csrf-token"]');
                            if (csrfToken && csrfToken.getAttribute('content')) {
                                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                            }

                            xhr.setRequestHeader('Content-Type', 'application/json');
                            xhr.setRequestHeader('Accept', 'application/json');

                            xhr.onload = () => {
                                if (xhr.status >= 200 && xhr.status < 400) {
                                    console.log(`Tour ${this.currentTourKey} marked as completed successfully on server`);
                                } else {
                                    console.warn(`Error marking tour ${this.currentTourKey} as completed on server. Status: ${xhr.status}`);
                                }

                                    // เคลียร์ tour ปัจจุบันก่อนเปลี่ยนหน้า
                                    if (this.tour) {
                                        this.tour.complete();
                                    }

                                    // รอให้ API เสร็จก่อนแล้วค่อยเปลี่ยนหน้า
                                    setTimeout(() => {
                                        // เพิ่มพารามิเตอร์เวลาเพื่อป้องกันการแคช
                                        const timestamp = new Date().getTime();
                                        window.location.href = `/rewards?tour=show&_=${timestamp}`;
                                    }, 500);
                            };

                            xhr.onerror = () => {
                                console.warn('Network error when marking tour as completed');
                                // นำทางไปยังหน้ารางวัลแม้ว่าจะมี error
                                if (this.tour) {
                                    this.tour.complete();
                                }
                                    setTimeout(() => {
                                        const timestamp = new Date().getTime();
                                        window.location.href = `/rewards?tour=show&_=${timestamp}`;
                                    }, 500);
                            };

                            // ส่งข้อมูลไปยังเซิร์ฟเวอร์
                            try {
                                xhr.send(JSON.stringify({
                                    tour_key: this.currentTourKey,
                                    status: 'completed',
                                    show_again: false
                                }));
                            } catch (e) {
                                console.warn('Error sending request:', e);
                                // นำทางไปยังหน้ารางวัล
                                if (this.tour) {
                                    this.tour.complete();
                                }
                                setTimeout(() => {
                                    const timestamp = new Date().getTime();
                                    window.location.href = `/rewards?tour=show&_=${timestamp}`;
                                }, 500);
                            }
                        }
                    }
                ]
            }
        ];
    }

    /**
     * ขั้นตอนทัวร์สำหรับหน้ารางวัล
     */
    getRewardsTourSteps() {
        return [
            {
                id: 'rewards-welcome',
                title: 'หน้ารางวัล',
                text: 'ยินดีต้อนรับสู่หน้ารางวัล ที่นี่คุณจะเห็นของรางวัลที่สามารถแลกได้ด้วยคะแนนจากการวิ่ง',
                buttons: [
                    {
                        text: 'ข้าม',
                        action: () => this.tour.cancel()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'rewards-points',
                title: 'คะแนนของคุณ',
                text: 'ส่วนนี้แสดงคะแนนที่คุณมีอยู่ ซึ่งได้มาจากการวิ่งและการทำกิจกรรมต่างๆ',
                attachTo: {
                    element: '.d-inline-block.p-3.bg-light.rounded-3',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'rewards-stats',
                title: 'สถิติรางวัล',
                text: 'แสดงสถิติของรางวัลทั้งหมด รวมถึงรางวัลที่คุณสามารถแลกได้ด้วยคะแนนที่มีอยู่',
                attachTo: {
                    element: '.reward-stat-card',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'rewards-filters',
                title: 'กรองรางวัล',
                text: 'คุณสามารถกรองรางวัลตามประเภทต่างๆ เช่น รางวัลที่แลกได้ รางวัลที่คะแนนไม่พอ หรือรางวัลที่หมด',
                attachTo: {
                    element: '.filter-badge',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'rewards-search',
                title: 'ค้นหารางวัล',
                text: 'คุณสามารถค้นหารางวัลที่ต้องการได้ด้วยการพิมพ์ชื่อรางวัลลงในช่องนี้',
                attachTo: {
                    element: '.search-box',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'ถัดไป',
                        action: () => this.tour.next()
                    }
                ]
            },
            {
                id: 'rewards-items',
                title: 'รายการรางวัล',
                text: 'รายการรางวัลที่คุณสามารถแลกได้ โดยรางวัลแต่ละชิ้นจะแสดงจำนวนคะแนนที่ต้องใช้ในการแลก',
                attachTo: {
                    element: '.card.shadow-sm .card-body',
                    on: 'top'
                },
                buttons: [
                    {
                        text: 'กลับ',
                        action: () => this.tour.back()
                    },
                    {
                        text: 'เสร็จสิ้น',
                        action: () => this.tour.complete()
                    }
                ]
            }
        ];
    }
}

// สร้าง instance และเริ่มต้นเมื่อ DOM โหลดเสร็จ
document.addEventListener('DOMContentLoaded', () => {
    window.goFitTour = new GoFitTour();
    window.goFitTour.init();
});
