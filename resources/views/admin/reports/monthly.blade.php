@extends('layouts.admin')

@section('title', 'รายงานประจำเดือน')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 fw-bold">รายงานประจำเดือน</h1>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> กลับสู่หน้ารายงาน
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold">รายงานประจำเดือน {{ Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale('th')->translatedFormat('F Y') }}</h5>
            <div>
                <form action="{{ route('admin.reports.monthly') }}" method="GET" class="d-flex">
                    <select name="month" class="form-select form-select-sm me-2">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::createFromDate($currentYear, $m, 1)->locale('th')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="form-select form-select-sm me-2">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                {{ $y + 543 }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search me-1"></i> ค้นหา
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width: 80px; height: 80px;">
                                <i class="fas fa-running fa-2x text-primary"></i>
                            </div>
                            <h3 class="fw-bold">{{ number_format($runActivities) }}</h3>
                            <p class="text-muted mb-0">กิจกรรมการวิ่ง</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width: 80px; height: 80px;">
                                <i class="fas fa-route fa-2x text-success"></i>
                            </div>
                            <h3 class="fw-bold">{{ number_format($totalDistance, 2) }}</h3>
                            <p class="text-muted mb-0">ระยะทางทั้งหมด (กม.)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-danger bg-opacity-10" style="width: 80px; height: 80px;">
                                <i class="fas fa-fire-alt fa-2x text-danger"></i>
                            </div>
                            <h3 class="fw-bold">{{ number_format($totalCalories) }}</h3>
                            <p class="text-muted mb-0">แคลอรี่ที่เผาผลาญ</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-warning bg-opacity-10" style="width: 80px; height: 80px;">
                                <i class="fas fa-medal fa-2x text-warning"></i>
                            </div>
                            <h3 class="fw-bold">{{ number_format($badgesAwarded) }}</h3>
                            <p class="text-muted mb-0">เหรียญตราที่มอบให้</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-info bg-opacity-10" style="width: 80px; height: 80px;">
                                <i class="fas fa-gift fa-2x text-info"></i>
                            </div>
                            <h3 class="fw-bold">{{ number_format($rewards) }}</h3>
                            <p class="text-muted mb-0">การแลกรางวัล</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- กราฟแสดงกิจกรรมการวิ่งรายวัน -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">กิจกรรมการวิ่งรายวัน</h5>
                </div>
                <div class="card-body">
                    <div id="daily-activities-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- สรุปข้อมูลทั่วไป -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">สรุปข้อมูลทั่วไป</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="report-table">
                        <table class="table table-bordered">
                            <tr>
                                <th class="bg-light">เดือน</th>
                                <td>{{ Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale('th')->translatedFormat('F Y') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">จำนวนกิจกรรมการวิ่ง</th>
                                <td>{{ number_format($runActivities) }} ครั้ง</td>
                            </tr>
                            <tr>
                                <th class="bg-light">ระยะทางทั้งหมด</th>
                                <td>{{ number_format($totalDistance, 2) }} กิโลเมตร</td>
                            </tr>
                            <tr>
                                <th class="bg-light">แคลอรี่ที่เผาผลาญ</th>
                                <td>{{ number_format($totalCalories) }} แคลอรี่</td>
                            </tr>
                            <tr>
                                <th class="bg-light">เหรียญตราที่มอบให้</th>
                                <td>{{ number_format($badgesAwarded) }} เหรียญ</td>
                            </tr>
                            <tr>
                                <th class="bg-light">การแลกรางวัล</th>
                                <td>{{ number_format($rewards) }} ครั้ง</td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-center gap-2">
                            <button id="export-excel" class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </button>
                            <button id="export-pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </button>
                            <button id="export-csv" class="btn btn-primary">
                                <i class="fas fa-file-csv me-1"></i> CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/table-to-json@1.0.0/lib/jquery.tabletojson.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // สร้างข้อมูลสำหรับกราฟแสดงกิจกรรมรายวัน
        const daysInMonth = new Date({{ $currentYear }}, {{ $currentMonth }}, 0).getDate();
        const dailyData = Array(daysInMonth).fill(0); // สร้างอาร์เรย์ตามจำนวนวันในเดือน

        // สมมติข้อมูลตัวอย่าง (ในการใช้งานจริงควรส่งข้อมูลจาก Controller)
        @php
            // สุ่มข้อมูลตัวอย่างสำหรับกราฟ
            $dailyActivities = [];
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dailyActivities[] = rand(0, 15);
            }
        @endphp

        const dailyActivitiesData = @json($dailyActivities);

        // สร้างรายการวันที่
        const days = [];
        for (let i = 1; i <= daysInMonth; i++) {
            days.push(i);
        }

        // สร้างกราฟแสดงกิจกรรมรายวัน
        const options = {
            series: [{
                name: 'จำนวนกิจกรรม',
                data: dailyActivitiesData
            }],
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#36A2EB'],
            xaxis: {
                categories: days,
                title: {
                    text: 'วันที่'
                }
            },
            yaxis: {
                title: {
                    text: 'จำนวนกิจกรรม'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " กิจกรรม";
                    }
                }
            }
        };

        const chart = new ApexCharts(document.getElementById('daily-activities-chart'), options);
        chart.render();

        // Export เป็น Excel
        $('#export-excel').click(function() {
            exportTableToExcel('report-table', 'รายงานประจำเดือน_{{ Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale("th")->translatedFormat("F_Y") }}');
        });

        // Export เป็น PDF
        $('#export-pdf').click(function() {
            exportTableToPDF('report-table', 'รายงานประจำเดือน_{{ Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale("th")->translatedFormat("F_Y") }}');
        });

        // Export เป็น CSV
        $('#export-csv').click(function() {
            exportTableToCSV('report-table', 'รายงานประจำเดือน_{{ Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale("th")->translatedFormat("F_Y") }}');
        });

        // ฟังก์ชันสำหรับ Export Excel
        function exportTableToExcel(tableID, filename = '') {
            const downloadLink = document.createElement("a");
            const dataType = 'application/vnd.ms-excel';
            const table = document.getElementById(tableID);
            const tableHTML = table.outerHTML.replace(/ /g, '%20');

            // สร้างชื่อไฟล์
            filename = filename ? filename + '.xls' : 'excel_data.xls';

            // สร้าง download link
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            downloadLink.download = filename;

            // คลิกเพื่อดาวน์โหลด
            downloadLink.click();
        }

        // ฟังก์ชันสำหรับ Export PDF
        function exportTableToPDF(tableID, filename = '') {
            const table = document.getElementById(tableID);
            const data = [];

            // สร้างข้อมูลตาราง
            const rows = table.getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                const row = [];
                const cells = rows[i].getElementsByTagName('td');
                const headers = rows[i].getElementsByTagName('th');

                // อ่านข้อมูลจาก th
                for (let j = 0; j < headers.length; j++) {
                    row.push(headers[j].innerText);
                }

                // อ่านข้อมูลจาก td
                for (let j = 0; j < cells.length; j++) {
                    row.push(cells[j].innerText);
                }

                data.push(row);
            }

            // สร้าง Document
            const docDefinition = {
                content: [
                    { text: 'รายงานประจำเดือน {{ Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale("th")->translatedFormat("F Y") }}', style: 'header' },
                    { text: 'GoFit Application', style: 'subheader' },
                    {
                        style: 'tableExample',
                        table: {
                            body: data
                        }
                    }
                ],
                styles: {
                    header: {
                        fontSize: 18,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    },
                    subheader: {
                        fontSize: 14,
                        bold: true,
                        margin: [0, 10, 0, 5]
                    },
                    tableExample: {
                        margin: [0, 5, 0, 15]
                    }
                },
                defaultStyle: {
                    font: 'THSarabunNew'
                }
            };

            // ดาวน์โหลด PDF
            pdfMake.createPdf(docDefinition).download(filename + '.pdf');
        }

        // ฟังก์ชันสำหรับ Export CSV
        function exportTableToCSV(tableID, filename = '') {
            const table = document.getElementById(tableID);
            const rows = table.querySelectorAll('tr');
            const csv = [];

            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');

                for (let j = 0; j < cols.length; j++) {
                    // แทนเครื่องหมาย comma ด้วย semicolon เพื่อป้องกันปัญหาใน CSV
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/,/g, ';');

                    // ล้อมรอบข้อมูลด้วยเครื่องหมาย quotes
                    row.push('"' + data + '"');
                }

                csv.push(row.join(','));
            }

            // Download CSV file
            downloadCSV(csv.join('\n'), filename);
        }

        function downloadCSV(csv, filename) {
            const csvFile = new Blob(["\ufeff" + csv], {type: 'text/csv;charset=utf-8;'});
            const downloadLink = document.createElement('a');

            // ชื่อไฟล์
            downloadLink.download = filename + '.csv';

            // แปลงข้อมูลเป็น dataURI
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";

            // เพิ่มลิงก์เข้าไปในเอกสารและคลิก
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    });
</script>
@endsection
