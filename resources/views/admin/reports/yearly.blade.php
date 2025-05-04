@extends('layouts.admin')

@section('title', 'รายงานประจำปี')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 fw-bold">รายงานประจำปี</h1>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> กลับสู่หน้ารายงาน
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold">รายงานประจำปี {{ $currentYear + 543 }}</h5>
            <div>
                <form action="{{ route('admin.reports.yearly') }}" method="GET" class="d-flex">
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
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width: 80px; height: 80px;">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                            <h3 class="fw-bold">{{ number_format($usersCount) }}</h3>
                            <p class="text-muted mb-0">ผู้ใช้งานใหม่ในปีนี้</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width: 80px; height: 80px;">
                                <i class="fas fa-running fa-2x text-success"></i>
                            </div>
                            <h3 class="fw-bold">{{ number_format($activitiesCount) }}</h3>
                            <p class="text-muted mb-0">กิจกรรมการวิ่งทั้งหมด</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ระยะทางรวมรายเดือน -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">ระยะทางรวมรายเดือน (กม.)</h5>
                </div>
                <div class="card-body">
                    <div id="distance-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- แคลอรี่รวมรายเดือน -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">แคลอรี่รวมรายเดือน</h5>
                </div>
                <div class="card-body">
                    <div id="calories-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- สรุปข้อมูลรายเดือน -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold">สรุปข้อมูลรายเดือน ปี {{ $currentYear + 543 }}</h5>
                    <div>
                        <div class="btn-group" role="group">
                            <button id="export-excel" class="btn btn-sm btn-success">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </button>
                            <button id="export-pdf" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </button>
                            <button id="export-csv" class="btn btn-sm btn-primary">
                                <i class="fas fa-file-csv me-1"></i> CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="yearly-table">
                        <table id="monthly-data" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>เดือน</th>
                                    <th class="text-center">จำนวนกิจกรรม</th>
                                    <th class="text-center">ระยะทางรวม (กม.)</th>
                                    <th class="text-center">แคลอรี่รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $monthNames = [
                                        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                                        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                                    ];

                                    // สร้างข้อมูลรายเดือน (สมมติข้อมูล)
                                    $monthlyData = [];
                                    for ($m = 1; $m <= 12; $m++) {
                                        $distance = 0;
                                        $calories = 0;

                                        // หาข้อมูลระยะทางจาก distanceByMonth
                                        foreach ($distanceByMonth as $item) {
                                            if ($item->month == $m) {
                                                $distance = $item->total_distance;
                                                break;
                                            }
                                        }

                                        // หาข้อมูลแคลอรี่จาก caloriesByMonth
                                        foreach ($caloriesByMonth as $item) {
                                            if ($item->month == $m) {
                                                $calories = $item->total_calories;
                                                break;
                                            }
                                        }

                                        $monthlyData[] = [
                                            'month' => $m,
                                            'month_name' => $monthNames[$m - 1],
                                            'activities' => rand(10, 100), // สมมติค่า
                                            'distance' => $distance,
                                            'calories' => $calories
                                        ];
                                    }
                                @endphp

                                @foreach($monthlyData as $data)
                                    <tr>
                                        <td>{{ $data['month_name'] }}</td>
                                        <td class="text-center">{{ number_format($data['activities']) }}</td>
                                        <td class="text-center">{{ number_format($data['distance'], 2) }}</td>
                                        <td class="text-center">{{ number_format($data['calories']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td>รวมทั้งสิ้น</td>
                                    <td class="text-center">{{ number_format($activitiesCount) }}</td>
                                    <td class="text-center">{{ number_format(collect($monthlyData)->sum('distance'), 2) }}</td>
                                    <td class="text-center">{{ number_format(collect($monthlyData)->sum('calories')) }}</td>
                                </tr>
                            </tfoot>
                        </table>
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
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ข้อมูลสำหรับกราฟ
        @php
            $distanceData = array_fill(0, 12, 0);
            $caloriesData = array_fill(0, 12, 0);

            foreach ($distanceByMonth as $item) {
                $distanceData[$item->month - 1] = $item->total_distance;
            }

            foreach ($caloriesByMonth as $item) {
                $caloriesData[$item->month - 1] = $item->total_calories;
            }

            $monthLabels = [
                'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
            ];
        @endphp

        const monthLabels = @json($monthLabels);
        const distanceData = @json($distanceData);
        const caloriesData = @json($caloriesData);

        // กราฟระยะทางรายเดือน
        const distanceOptions = {
            series: [{
                name: 'ระยะทาง (กม.)',
                data: distanceData
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    columnWidth: '60%',
                    distributed: false
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#36A2EB'],
            xaxis: {
                categories: monthLabels,
                title: {
                    text: 'เดือน'
                }
            },
            yaxis: {
                title: {
                    text: 'ระยะทาง (กม.)'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val.toFixed(2) + " กม.";
                    }
                }
            }
        };

        const distanceChart = new ApexCharts(document.getElementById('distance-chart'), distanceOptions);
        distanceChart.render();

        // กราฟแคลอรี่รายเดือน
        const caloriesOptions = {
            series: [{
                name: 'แคลอรี่',
                data: caloriesData
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    columnWidth: '60%',
                    distributed: false
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#FF6384'],
            xaxis: {
                categories: monthLabels,
                title: {
                    text: 'เดือน'
                }
            },
            yaxis: {
                title: {
                    text: 'แคลอรี่'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return Math.round(val) + " แคลอรี่";
                    }
                }
            }
        };

        const caloriesChart = new ApexCharts(document.getElementById('calories-chart'), caloriesOptions);
        caloriesChart.render();

        // ตาราง DataTable
        $('#monthly-data').DataTable({
            paging: false,
            searching: false,
            info: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
            }
        });

        // ปุ่ม Export
        $('#export-excel').click(function() {
            exportTableToExcel('yearly-table', 'รายงานประจำปี_{{ $currentYear + 543 }}');
        });

        $('#export-pdf').click(function() {
            exportTableToPDF('yearly-table', 'รายงานประจำปี_{{ $currentYear + 543 }}');
        });

        $('#export-csv').click(function() {
            exportTableToCSV('yearly-table', 'รายงานประจำปี_{{ $currentYear + 543 }}');
        });

        // ฟังก์ชัน Export
        function exportTableToExcel(tableID, filename = '') {
            const downloadLink = document.createElement("a");
            const dataType = 'application/vnd.ms-excel';
            const table = document.getElementById(tableID);
            const tableHTML = table.outerHTML.replace(/ /g, '%20');

            filename = filename ? filename + '.xls' : 'excel_data.xls';

            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            downloadLink.download = filename;

            downloadLink.click();
        }

        function exportTableToPDF(tableID, filename = '') {
            const table = document.getElementById(tableID);
            const rows = table.querySelectorAll('table tr');

            const header = [];
            const headerCells = rows[0].querySelectorAll('th');
            headerCells.forEach(cell => {
                header.push(cell.innerText);
            });

            const body = [];
            for (let i = 1; i < rows.length; i++) {
                const rowData = [];
                const cells = rows[i].querySelectorAll('td');
                cells.forEach(cell => {
                    rowData.push(cell.innerText);
                });
                body.push(rowData);
            }

            const docDefinition = {
                content: [
                    { text: 'รายงานประจำปี {{ $currentYear + 543 }}', style: 'header' },
                    { text: 'GoFit Application', style: 'subheader' },
                    {
                        style: 'tableExample',
                        table: {
                            headerRows: 1,
                            body: [
                                header,
                                ...body
                            ]
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
                    fontSize: 12
                },
                customize: function(doc) {
                    doc.styles.tableHeader = {
                        fontSize: 14,
                        bold: true,
                        alignment: 'center'
                    };

                    // กำหนดขอบกระดาษ
                    doc.pageMargins = [20, 20, 20, 20];
                }
            };

            pdfMake.createPdf(docDefinition).download(filename + '.pdf');
        }

        function exportTableToCSV(tableID, filename = '') {
            const table = document.getElementById(tableID);
            const rows = table.querySelectorAll('table tr');
            const csv = [];

            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');

                for (let j = 0; j < cols.length; j++) {
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/,/g, ';');
                    row.push('"' + data + '"');
                }

                csv.push(row.join(','));
            }

            downloadCSV(csv.join('\n'), filename);
        }

        function downloadCSV(csv, filename) {
            const csvFile = new Blob(["\ufeff" + csv], {type: 'text/csv;charset=utf-8;'});
            const downloadLink = document.createElement('a');

            downloadLink.download = filename + '.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";

            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    });
</script>
@endsection
