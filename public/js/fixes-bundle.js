/**
 * GoFit App Fixes Bundle
 * รวมทุกการแก้ไขเข้าด้วยกันเพื่อให้ใช้งานได้ง่าย
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('GoFit App Fixes Bundle loaded');

    // 1. เตรียมการแก้ไขสำหรับ DataTables
    if (typeof $.fn !== 'undefined' && typeof $.fn.dataTable !== 'undefined') {
        // แก้ไขปัญหา ext undefined
        if (!$.fn.dataTable.ext) {
            $.fn.dataTable.ext = {};
        }

        if (!$.fn.dataTable.ext.buttons) {
            $.fn.dataTable.ext.buttons = {};
        }

        // แก้ไขปัญหาปุ่ม Print
        if ($.fn.dataTable.Buttons && !$.fn.dataTable.ext.buttons.print) {
            $.fn.dataTable.ext.buttons.print = {
                className: 'buttons-print',
                text: '<i class="fas fa-print"></i> พิมพ์',
                action: function(e, dt, node, config) {
                    window.print();
                }
            };
        }

        // แก้ไขปัญหาปุ่ม ColVis
        if ($.fn.dataTable.Buttons && !$.fn.dataTable.ext.buttons.colvis) {
            $.fn.dataTable.ext.buttons.colvis = {
                className: 'buttons-colvis',
                text: '<i class="fas fa-columns"></i> คอลัมน์',
                action: function(e, dt, node, config) {
                    $(dt.table().container()).trigger('colvis.dt');
                }
            };
        }

        console.log('DataTables fixes applied');
    }

    // 2. แก้ไขปัญหาฟอนต์ใน pdfMake - ใช้ฟอนต์ Noto Sans Thai แทน
    if (typeof pdfMake !== 'undefined') {
        // สร้าง URL สำหรับฟอนต์ Noto Sans Thai (มีให้ใช้ผ่าน CDN)
        var notoThaiUrl = 'https://cdn.jsdelivr.net/npm/@fontsource/noto-sans-thai/files/noto-sans-thai-all-400-normal.woff';
        var notoBoldThaiUrl = 'https://cdn.jsdelivr.net/npm/@fontsource/noto-sans-thai/files/noto-sans-thai-all-700-normal.woff';

        // โหลดฟอนต์ Noto Sans Thai ผ่าน AJAX
        fetch(notoThaiUrl)
            .then(response => response.blob())
            .then(blob => {
                var reader = new FileReader();
                reader.onload = function() {
                    var notoThaiBase64 = reader.result.split(',')[1];

                    // กำหนดฟอนต์ Noto Sans Thai
                    pdfMake.fonts = pdfMake.fonts || {};
                    pdfMake.fonts['NotoSansThai'] = {
                        normal: { data: atob(notoThaiBase64) },
                        bold: { data: atob(notoThaiBase64) }
                    };

                    // กำหนดเป็นฟอนต์ default
                    pdfMake.defaultStyle = pdfMake.defaultStyle || {};
                    pdfMake.defaultStyle.font = 'NotoSansThai';

                    console.log('Thai font (Noto Sans Thai) loaded for PDF');
                };
                reader.readAsDataURL(blob);
            })
            .catch(err => {
                console.error('Error loading Noto Sans Thai font:', err);
            });

        // ฟังก์ชันช่วยเหลือสำหรับการสร้าง PDF
        window.generateThaiPDF = function(docDefinition) {
            docDefinition.defaultStyle = docDefinition.defaultStyle || {};
            docDefinition.defaultStyle.font = 'NotoSansThai';
            return pdfMake.createPdf(docDefinition);
        };

        // ฟังก์ชัน fix ฟอนต์สำหรับ PDF
        window.fixThaiPdf = function(doc) {
            doc.defaultStyle = doc.defaultStyle || {};
            doc.defaultStyle.font = 'NotoSansThai';
            doc.defaultStyle.fontSize = 12;

            doc.styles = doc.styles || {};
            doc.styles.tableHeader = {
                font: 'NotoSansThai',
                fontSize: 14,
                bold: true,
                alignment: 'center'
            };

            return doc;
        };
    }

    // 3. แก้ไขปัญหา collapse ไม่ทำงาน (ตัวกรองขั้นสูง) และเพิ่มอนิเมชั่นให้สมูทขึ้น
    function fixCollapseButtons() {
        // เพิ่ม CSS เพื่อทำให้การเปลี่ยนความสูงเป็นแบบ transition (ใช้เฉพาะกับตัวกรองขั้นสูง)
        const styleElement = document.createElement('style');
        styleElement.textContent = `
            /* ใช้เฉพาะกับ collapse ที่ใช้ในตัวกรองขั้นสูงเท่านั้น */
            #advancedFilters.collapse {
                overflow: hidden !important;
                transition: height 0.35s ease !important;
                height: 0;
            }
            #advancedFilters.collapse.show {
                height: auto;
            }
        `;
        document.head.appendChild(styleElement);

        // ค้นหาเฉพาะปุ่มที่ควบคุม advancedFilters เท่านั้น
        const advancedFilterButtons = document.querySelectorAll('[data-bs-target="#advancedFilters"]');

        advancedFilterButtons.forEach(button => {
            // ตรวจสอบว่าเป็นปุ่มสำหรับ Advanced Filters หรือไม่
            if (!button.getAttribute('data-bs-target') === '#advancedFilters') return;

            const target = document.querySelector('#advancedFilters');
            if (!target) return;

            // Remove Bootstrap data attributes to prevent conflicts
            button.removeAttribute('data-bs-toggle');
            button.removeAttribute('data-bs-target');

            // ให้แน่ใจว่ามี class collapse
            if (!target.classList.contains('collapse')) {
                target.classList.add('collapse');
            }

            // คำนวณความสูงของเนื้อหาเมื่อเปิดแสดง (ถ้าถูกแสดงอยู่แล้ว)
            if (target.classList.contains('show')) {
                target.style.height = target.scrollHeight + 'px';
            }

            // Add manual toggle functionality with smooth animation
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // เพิ่ม class collapsing เพื่อให้มีการ transition
                if (target.classList.contains('show')) {
                    // กำลังจะปิด
                    target.style.height = target.scrollHeight + 'px';

                    // รอให้ browser ประมวลผลความสูงก่อน
                    setTimeout(() => {
                        target.style.height = '0px';

                        // รอให้ animation เสร็จก่อนแล้วค่อยเอา class ออก
                        target.addEventListener('transitionend', function handler() {
                            target.classList.remove('show');
                            target.removeEventListener('transitionend', handler);
                        }, { once: true });
                    }, 10);
                } else {
                    // กำลังจะเปิด
                    target.classList.add('show');
                    const height = target.scrollHeight;
                    target.style.height = '0px';

                    // รอให้ browser ประมวลผลความสูงก่อน
                    setTimeout(() => {
                        target.style.height = height + 'px';

                        // รอให้ animation เสร็จก่อนแล้วค่อยคืนค่า height เป็น auto
                        target.addEventListener('transitionend', function handler() {
                            target.style.height = 'auto';
                            target.removeEventListener('transitionend', handler);
                        }, { once: true });
                    }, 10);
                }
            });

            console.log('Fixed collapse button for advancedFilters with smooth animation');
        });
    }

    // Apply the collapse fix
    fixCollapseButtons();

    // Try again after a slight delay to ensure all elements are loaded
    setTimeout(fixCollapseButtons, 500);
});
