/**
 * กำหนดฟอนต์ THSarabunNew สำหรับ pdfMake
 * ให้เพิ่ม script นี้หลังจากโหลด pdfmake.min.js และ vfs_fonts.js
 */
if (typeof pdfMake !== 'undefined') {
    pdfMake.fonts = {
        THSarabunNew: {
            normal: '/fonts/THSarabunNew.ttf',
            bold: '/fonts/THSarabunNew Bold.ttf',
            italics: '/fonts/THSarabunNew Italic.ttf',
            bolditalics: '/fonts/THSarabunNew BoldItalic.ttf'
        },
        Roboto: {
            normal: 'Roboto-Regular.ttf',
            bold: 'Roboto-Medium.ttf',
            italics: 'Roboto-Italic.ttf',
            bolditalics: 'Roboto-MediumItalic.ttf'
        }
    };
} else {
    console.warn('pdfMake ไม่ได้ถูกโหลด ไม่สามารถกำหนดฟอนต์ได้');
}
