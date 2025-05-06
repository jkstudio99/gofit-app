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
            this.checkIfShouldShowTour();
            this.isInitialized = true;
        });
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
                this.markTourAsCompleted(this.currentTourKey);
            }
        });

        // เมื่อกดปิด (cancel) Tour
        this.tour.on('cancel', () => {
            if (this.currentTourKey) {
                this.markTourAsSkipped(this.currentTourKey);
            }
        });
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

        // ตรวจสอบสถานะของทัวร์จาก server
        fetch(`/tour/status?tour_key=${tourKey}`)
            .then(response => response.json())
            .then(data => {
                // ถ้าควรแสดงทัวร์
                if (data.shouldShow) {
                    // ตรวจสอบว่ามี URL parameter tour=skip หรือไม่
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('tour') === 'skip') {
                        // ข้ามการแสดงทัวร์และบันทึกว่าทัวร์ถูกข้าม
                        this.markTourAsSkipped(tourKey);
                        return;
                    }

                    // ล้าง steps เก่าก่อน
                    this.tour.steps.forEach(step => this.tour.removeStep(step.id));

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
            this.markTourAsCompleted(this.currentTourKey);
        }

        // รอให้ API เสร็จก่อนแล้วค่อยเปลี่ยนหน้า
        setTimeout(() => {
            window.location.href = nextPage;
        }, 500);
    }

    /**
     * บันทึกว่าทัวร์เสร็จสิ้นแล้ว
     */
    markTourAsCompleted(tourKey) {
        fetch('/tour/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                tour_key: tourKey,
                status: 'completed',
                show_again: false
            })
        }).catch(error => {
            console.error('Error marking tour as completed:', error);
        });
    }

    /**
     * บันทึกว่าทัวร์ถูกข้าม
     */
    markTourAsSkipped(tourKey) {
        fetch('/tour/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                tour_key: tourKey,
                status: 'skipped',
                show_again: false
            })
        }).catch(error => {
            console.error('Error marking tour as skipped:', error);
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
                        action: () => this.tour.cancel()
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
                            this.completeAndGoToNextPage('/rewards?tour=show');
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
