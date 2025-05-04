// กำหนดค่า Virtual File System (VFS) สำหรับ pdfMake
this.pdfMake = this.pdfMake || {};
this.pdfMake.vfs = this.pdfMake.vfs || {};

// กำหนดเส้นทางฟอนต์แบบสัมพัทธ์
const fontBasePath = '/fonts/';

// กำหนดฟอนต์
pdfMake.fonts = {
    THSarabunNew: {
        normal: fontBasePath + 'THSarabunNew.ttf',
        bold: fontBasePath + 'THSarabunNew Bold.ttf',
        italics: fontBasePath + 'THSarabunNew Italic.ttf',
        bolditalics: fontBasePath + 'THSarabunNew BoldItalic.ttf'
    },
    // ยังคงฟอนต์ Roboto เป็นฟอนต์สำรอง
    Roboto: {
        normal: 'Roboto-Regular.ttf',
        bold: 'Roboto-Medium.ttf',
        italics: 'Roboto-Italic.ttf',
        bolditalics: 'Roboto-MediumItalic.ttf'
    }
};

// กำหนด default font เป็น THSarabunNew
pdfMake.defaultStyle = pdfMake.defaultStyle || {};
pdfMake.defaultStyle.font = 'THSarabunNew';

console.log('Thai fonts setup applied successfully!');
