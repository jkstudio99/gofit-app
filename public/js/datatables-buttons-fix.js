/**
 * DataTables Buttons Fix
 * แก้ไขปัญหาปุ่ม Export ไม่ทำงาน
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DataTables buttons fix loading...');

    if (typeof $.fn === 'undefined' || typeof $.fn.dataTable === 'undefined') {
        console.warn('DataTables is not loaded yet. Buttons fix cannot be applied.');
        return;
    }

    // แก้ไขปัญหา ext undefined
    if (!$.fn.dataTable.ext) {
        $.fn.dataTable.ext = {};
    }

    if (!$.fn.dataTable.ext.buttons) {
        $.fn.dataTable.ext.buttons = {};
    }

    // แก้ไขปัญหาปุ่ม PDF
    if ($.fn.dataTable.ext.buttons && !$.fn.dataTable.ext.buttons.pdfHtml5) {
        $.fn.dataTable.ext.buttons.pdfHtml5 = {
            className: 'buttons-pdf buttons-html5',
            text: function(dt) {
                return '<i class="fas fa-file-pdf"></i> PDF';
            },
            action: function(e, dt, button, config) {
                var data = dt.buttons.exportData(config.exportOptions);
                var columns = dt.settings()[0].aoColumns;

                var tableBody = [];
                var tableHeader = [];

                // สร้างหัวตาราง
                $.each(data.header, function(i, value) {
                    tableHeader.push({ text: value, style: 'tableHeader' });
                });

                // สร้างข้อมูลในตาราง
                $.each(data.body, function(i, row) {
                    var dataRow = [];
                    $.each(row, function(j, value) {
                        dataRow.push({ text: value });
                    });
                    tableBody.push(dataRow);
                });

                // สร้าง PDF ที่มีตาราง
                var docDefinition = {
                    pageOrientation: 'portrait',
                    pageSize: 'A4',
                    content: [
                        { text: config.title || 'รายงานข้อมูล', style: 'header', alignment: 'center', margin: [0, 0, 0, 10] },
                        {
                            table: {
                                headerRows: 1,
                                widths: Array(tableHeader.length).fill('*'),
                                body: [tableHeader].concat(tableBody)
                            },
                            layout: {
                                fillColor: function (rowIndex, node, columnIndex) {
                                    return (rowIndex === 0) ? '#f2f2f2' : null;
                                }
                            }
                        }
                    ],
                    styles: {
                        header: {
                            fontSize: 16,
                            bold: true,
                            margin: [0, 0, 0, 10]
                        },
                        tableHeader: {
                            bold: true,
                            fontSize: 12,
                            color: 'black',
                            alignment: 'center'
                        }
                    },
                    defaultStyle: {
                        fontSize: 10,
                        font: 'NotoSansThai'
                    }
                };

                // ตรวจสอบว่ามีการกำหนดฟอนต์ Thai แล้วหรือยัง
                if (typeof pdfMake.fonts['NotoSansThai'] === 'undefined') {
                    // ถ้ายังไม่มีฟอนต์ Thai ให้ดาวน์โหลดฟอนต์ Noto Sans Thai
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

                                // สร้างและดาวน์โหลด PDF หลังจากที่โหลดฟอนต์เสร็จแล้ว
                                pdfMake.createPdf(docDefinition).download(config.title + '.pdf');
                            };
                            reader.readAsDataURL(blob);
                        })
                        .catch(err => {
                            console.error('Failed to load Noto Sans Thai font:', err);
                            // ถ้าโหลดฟอนต์ไม่สำเร็จ ก็ใช้ฟอนต์เริ่มต้นแทน
                            docDefinition.defaultStyle.font = undefined;
                            pdfMake.createPdf(docDefinition).download(config.title + '.pdf');
                        });
                } else {
                    // ถ้ามีฟอนต์ Thai แล้ว ให้สร้าง PDF ได้เลย
                    pdfMake.createPdf(docDefinition).download(config.title + '.pdf');
                }
            }
        };
    }

    console.log('DataTables buttons fix applied');
});
