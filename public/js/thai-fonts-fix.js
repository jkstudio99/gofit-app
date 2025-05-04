/**
 * thai-fonts-fix.js
 * แก้ไขปัญหาฟอนต์ภาษาไทยใน pdfMake โดยใช้ Noto Sans Thai
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Thai fonts fix loaded');

    if (typeof pdfMake === 'undefined') {
        console.warn('pdfMake is not loaded yet. Thai font fix cannot be applied.');
        return;
    }

    // พยายามโหลดฟอนต์ Noto Sans Thai สำหรับ PDF
    try {
        var notoThaiUrl = 'https://cdn.jsdelivr.net/npm/@fontsource/noto-sans-thai/files/noto-sans-thai-all-400-normal.woff';

        fetch(notoThaiUrl)
            .then(response => response.blob())
            .then(blob => {
                var reader = new FileReader();
                reader.onload = function() {
                    var base64Font = reader.result.split(',')[1];

                    // กำหนดฟอนต์ Noto Sans Thai
                    pdfMake.fonts = pdfMake.fonts || {};
                    pdfMake.fonts['NotoSansThai'] = {
                        normal: { data: atob(base64Font) },
                        bold: { data: atob(base64Font) },
                        italics: { data: atob(base64Font) },
                        bolditalics: { data: atob(base64Font) }
                    };

                    console.log('Noto Sans Thai font loaded for PDFs');
                };
                reader.readAsDataURL(blob);
            })
            .catch(err => {
                console.error('Failed to load Noto Sans Thai font:', err);
            });
    } catch (e) {
        console.error('Error setting up Thai font:', e);
    }

    // ฟังก์ชันช่วยจัดการฟอนต์ PDF
    window.fixThaiPdf = function(doc) {
        // ใช้ฟอนต์ Noto Sans Thai
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

    // ทำให้มั่นใจว่าอาจมีการใช้งานใน DataTables export
    if ($.fn.dataTable && $.fn.dataTable.Buttons) {
        // ตรวจสอบว่า ext และ buttons มีอยู่หรือไม่
        if (!$.fn.dataTable.ext) {
            $.fn.dataTable.ext = {};
        }
        if (!$.fn.dataTable.ext.buttons) {
            $.fn.dataTable.ext.buttons = {};
        }
    }
});
