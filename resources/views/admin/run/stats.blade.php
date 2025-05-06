@extends('layouts.admin')

@section('title', 'สถิติการวิ่ง')

@section('content')
    <h1 class="mb-4">สถิติการวิ่ง</h1>

    <!-- ข้อมูลสถิติโดยรวม -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ $totalRuns ?? 0 }}</h3>
                            <p class="mb-0">จำนวนการวิ่งทั้งหมด</p>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-running fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ number_format($totalDistance ?? 0, 2) }}</h3>
                            <p class="mb-0">ระยะทางรวม (กม.)</p>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-road fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ number_format($totalCalories ?? 0) }}</h3>
                            <p class="mb-0">แคลอรี่รวม</p>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-fire fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ $formattedTotalDuration ?? '0 ชม. 0 นาที' }}</h3>
                            <p class="mb-0">เวลาวิ่งรวม</p>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="fas fa-clock fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- กราฟสถิติการวิ่ง -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line me-2"></i>
                        สถิติการวิ่งในรอบสัปดาห์
                    </h5>
                </div>
                <div class="card-body">
                    <div id="weeklyRunChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ผู้ใช้ที่วิ่งมากที่สุด -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-trophy me-2"></i>
                        ผู้ใช้ที่วิ่งมากที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>ชื่อผู้ใช้</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>จำนวนครั้ง</th>
                                    <th>ระยะทางรวม</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topRunners ?? [] as $index => $runner)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $runner->username }}</td>
                                    <td>{{ $runner->firstname }} {{ $runner->lastname }}</td>
                                    <td><span class="badge bg-primary">{{ $runner->run_count }}</span></td>
                                    <td>{{ number_format($runner->total_distance, 2) }} กม.</td>
                                    <td>
                                        <a href="{{ route('admin.user-run-stats', $runner->user_id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- การวิ่งล่าสุด -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-history me-2"></i>
                        การวิ่งล่าสุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ผู้ใช้</th>
                                    <th>ระยะทาง</th>
                                    <th>ระยะเวลา</th>
                                    <th>แคลอรี่</th>
                                    <th>วันที่</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestRuns ?? [] as $run)
                                <tr>
                                    <td>{{ $run->user->username ?? 'ไม่ระบุชื่อ' }}</td>
                                    <td>{{ number_format($run->distance, 2) }} กม.</td>
                                    <td>
                                        @php
                                            $hours = floor($run->duration / 3600);
                                            $minutes = floor(($run->duration % 3600) / 60);
                                            $seconds = $run->duration % 60;
                                            echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                        @endphp
                                    </td>
                                    <td>{{ number_format($run->calories_burned) }} kcal</td>
                                    <td>
                                        @php
                                            $thaiMonths = [
                                                1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
                                                5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
                                                9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
                                            ];
                                            $day = $run->created_at->format('j');
                                            $month = $thaiMonths[$run->created_at->format('n')];
                                            $year = $run->created_at->format('Y') + 543 - 2500; // แปลงเป็น พ.ศ. 2 หลัก
                                            $time = $run->created_at->format('H:i');
                                            echo "{$day} {$month} {$year} {$time}";
                                        @endphp
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">ส่งออกข้อมูล</h5>
            <div class="btn-group">
                <a href="{{ route('admin.run.calendar') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-calendar-alt me-1"></i> ปฏิทินการวิ่ง
                </a>
                <a href="{{ route('admin.run.heatmap') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-fire-alt me-1"></i> แผนที่ความร้อน
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.run.export') }}" method="GET" id="exportForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">รูปแบบไฟล์</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-file-export"></i></span>
                            <select class="form-select" name="format" required>
                                <option value="excel" selected>Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">ผู้ใช้</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                            <select class="form-select" name="user_id">
                                <option value="">ผู้ใช้ทั้งหมด</option>
                                @foreach($users as $user)
                                    @if($user->username != 'admin')
                                        <option value="{{ $user->user_id }}">{{ $user->username }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">จากวันที่</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                            <input type="text" class="form-control datepicker-th" name="date_from" id="date_from" autocomplete="off" placeholder="เลือกวันที่">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">ถึงวันที่</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                            <input type="text" class="form-control datepicker-th" name="date_to" id="date_to" autocomplete="off" placeholder="เลือกวันที่">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-grid d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-redo me-1"></i> รีเซ็ต
                            </button>
                            <button type="submit" class="btn btn-primary" id="exportBtn">
                                <i class="fas fa-file-export me-1"></i> ส่งออกข้อมูล
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="{{ asset('js/pdfmake-fonts.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // แปลงวันที่จากรูปแบบ YYYY-MM-DD เป็น dd/mm/yyyy พ.ศ.
        function getThaiDateFormat(standardDate) {
            if (!standardDate) return '';

            let date;
            if (typeof standardDate === 'string') {
                date = new Date(standardDate);
            } else {
                date = standardDate;
            }

            if (isNaN(date.getTime())) return '';

            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const thaiYear = date.getFullYear() + 543;
            return `${day}/${month}/${thaiYear}`;
        }

        // แปลงวันที่จากรูปแบบ dd/mm/yyyy พ.ศ. เป็น YYYY-MM-DD
        function getStandardDateFormat(thaiDate) {
            if (!thaiDate) return '';

            const parts = thaiDate.split('/');
            if (parts.length !== 3) return '';

            const day = parts[0];
            const month = parts[1];
            const thaiYear = parseInt(parts[2]);
            const standardYear = thaiYear - 543;

            return `${standardYear}-${month}-${day}`;
        }

        // ตั้งค่าวันที่ในช่องข้อมูลเป็นรูปแบบไทย
        function setupInitialDate(inputElement, defaultDate) {
            let dateValue = inputElement.value;

            // ถ้ามีค่าวันที่อยู่แล้ว แปลงเป็นรูปแบบไทย
            if (dateValue) {
                inputElement.dataset.standardDate = dateValue;
                inputElement.value = getThaiDateFormat(dateValue);
            }
            // ถ้าไม่มีค่าแต่มี default ให้ใช้ default
            else if (defaultDate) {
                inputElement.dataset.standardDate = defaultDate;
                inputElement.value = getThaiDateFormat(defaultDate);
            }
        }

        // ดึงข้อมูลวันที่จากช่องข้อมูล
        const dateFrom = document.getElementById('date_from');
        const dateTo = document.getElementById('date_to');

        // เพิ่มข้อมูลวันที่ตั้งต้น (ถ้าต้องการ)
        const today = new Date();
        const nintyDaysAgo = new Date();
        nintyDaysAgo.setDate(today.getDate() - 90);

        // ตั้งค่า flatpickr
        const datepickerOptions = {
            dateFormat: 'd/m/Y',
            locale: 'th',
            allowInput: true,
            altInput: false,
            altFormat: 'd/m/Y',
            disableMobile: true,
            yearOffset: 543, // Add 543 years for Buddhist Era
            onOpen: function(selectedDates, dateStr, instance) {
                // เมื่อเปิด datepicker ให้แปลงวันที่เป็นรูปแบบสากลเพื่อการคำนวณที่ถูกต้อง
                const input = instance.input;
                if (input.value) {
                    const standardDate = getStandardDateFormat(input.value);
                    if (standardDate) {
                        instance.setDate(new Date(standardDate), false);
                    }
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                // เมื่อเลือกวันที่ ให้เก็บวันที่รูปแบบสากลเอาไว้
                const input = instance.input;
                if (selectedDates[0]) {
                    const standardDate = selectedDates[0].toISOString().split('T')[0];
                    input.dataset.standardDate = standardDate;
                }
            },
            onClose: function(selectedDates, dateStr, instance) {
                // เมื่อปิด datepicker ให้แปลงวันที่เป็นรูปแบบไทย
                const input = instance.input;
                if (selectedDates[0]) {
                    input.value = getThaiDateFormat(selectedDates[0]);
                }
            }
        };

        // ตั้งค่าเริ่มต้นสำหรับวันที่
        setupInitialDate(dateFrom, nintyDaysAgo.toISOString().split('T')[0]);
        setupInitialDate(dateTo, today.toISOString().split('T')[0]);

        // เริ่มใช้งาน datepicker
        const fromPicker = flatpickr(dateFrom, { ...datepickerOptions });
        const toPicker = flatpickr(dateTo, { ...datepickerOptions });

        // กรณีรีเซ็ตแบบฟอร์ม ให้กลับมาเป็นรูปแบบไทย
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            setTimeout(function() {
                setupInitialDate(dateFrom, nintyDaysAgo.toISOString().split('T')[0]);
                setupInitialDate(dateTo, today.toISOString().split('T')[0]);
            }, 10);
        });

        // จัดการการส่งแบบฟอร์ม
        document.getElementById('exportForm').addEventListener('submit', function(e) {
            // เปลี่ยนจากรูปแบบไทยเป็นรูปแบบสากลก่อนส่งข้อมูล
            dateFrom.value = dateFrom.dataset.standardDate || '';
            dateTo.value = dateTo.dataset.standardDate || '';
        });

        // Weekly Run Chart
        const chartData = @json($lastWeekStats ?? []);
        const labels = chartData.map(item => item.date);
        const counts = chartData.map(item => item.count);

        var options = {
            series: [{
                name: 'จำนวนกิจกรรมการวิ่ง',
                data: counts
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#3b82f6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: labels,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " ครั้ง"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#weeklyRunChart"), options);
        chart.render();
    });
</script>
@endsection
