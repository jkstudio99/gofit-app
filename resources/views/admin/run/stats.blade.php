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
                                    <td>{{ $run->created_at->format('d/m/Y H:i') }}</td>
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
                        <select class="form-select" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">ผู้ใช้</label>
                        <select class="form-select" name="user_id">
                            <option value="">ทั้งหมด</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">จากวันที่</label>
                        <input type="date" class="form-control" name="date_from">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">ถึงวันที่</label>
                        <input type="date" class="form-control" name="date_to">
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-export me-2"></i> ส่งออกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // สร้างกราฟสถิติการวิ่งในรอบสัปดาห์

        // ข้อมูลจาก controller
        var dates = {!! json_encode(array_column($lastWeekStats ?? [], 'date')) !!};
        var counts = {!! json_encode(array_column($lastWeekStats ?? [], 'count')) !!};

        var options = {
            series: [{
                name: 'จำนวนการวิ่ง',
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
            colors: ['#36A2EB'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            markers: {
                size: 4,
                colors: ['#36A2EB'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 7,
                }
            },
            xaxis: {
                categories: dates,
                title: {
                    text: 'วันที่'
                }
            },
            yaxis: {
                title: {
                    text: 'จำนวนการวิ่ง'
                },
                min: 0,
                forceNiceScale: true,
                labels: {
                    formatter: function(value) {
                        return Math.round(value);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return Math.round(value) + ' ครั้ง';
                    }
                }
            }
        };

        var chart = new ApexCharts(document.getElementById('weeklyRunChart'), options);
        chart.render();
    });
</script>
@endsection
