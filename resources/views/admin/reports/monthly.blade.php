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
                <form action="{{ route('admin.reports.monthly') }}" method="GET" class="d-flex align-items-center">
                    <select name="month" class="form-select form-select-md me-2" style="min-width: 120px;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::createFromDate($currentYear, $m, 1)->locale('th')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="form-select form-select-md me-2" style="min-width: 100px;">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                {{ $y + 543 }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit" class="d-flex align-items-center btn btn-md btn-primary">
                        <i class="fas fa-search me-2"></i> ค้นหา
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

                    <div class="d-flex justify-content-center mt-4">
                        <button id="export-excel" class="btn btn-primary mx-2">
                            <i class="fas fa-file-excel me-2"></i> ส่งออก Excel
                            </button>
                        <button id="export-csv" class="btn btn-primary mx-2">
                            <i class="fas fa-file-csv me-2"></i> ส่งออก CSV
                            </button>
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
<script src="https://cdn.jsdelivr.net/npm/table-to-json@1.0.0/lib/jquery.tabletojson.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // สร้างข้อมูลสำหรับกราฟแสดงกิจกรรมรายวัน
        const daysInMonth = new Date({{ $currentYear }}, {{ $currentMonth }}, 0).getDate();

        @php
            // ดึงข้อมูลจริงจากฐานข้อมูลแทนการสุ่ม
            $dailyActivities = [];
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

            // สร้าง array เริ่มต้นด้วย 0 สำหรับทุกวันในเดือน
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dailyActivities[$i] = 0;
            }

            // สมมติว่ามีตัวแปร $activities ที่มาจาก Controller ซึ่งเก็บข้อมูลกิจกรรมตามวันที่
            if (isset($dailyRunData) && is_array($dailyRunData)) {
                foreach ($dailyRunData as $day => $count) {
                    $dailyActivities[$day] = $count;
                }
            }
        @endphp

        const dailyActivitiesData = @json(array_values($dailyActivities));

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
                    },
                    columnWidth: '70%'
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
                },
                min: 0,
                forceNiceScale: true,
                labels: {
                    formatter: function(val) {
                        return Math.floor(val);
                    }
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
