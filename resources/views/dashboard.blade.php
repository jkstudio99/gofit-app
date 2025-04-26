@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="section-title mb-4">แดชบอร์ดการวิ่งของคุณ</h2>

            <!-- ปุ่มวิ่งที่เด่นชัด -->
            <div class="run-button-container mb-5">
                <div class="run-button-pulse"></div>
                <a href="{{ route('run.index') }}" class="run-button">
                    <div class="run-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <div class="run-text">เริ่มวิ่ง</div>
                </a>
            </div>

            <!-- Stats Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon text-primary">
                            <i class="fas fa-road"></i>
                        </div>
                        <div class="stat-value">{{ number_format($totalDistance, 1) }}</div>
                        <div class="stat-label">กิโลเมตร</div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon text-danger">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="stat-value">{{ number_format($totalCalories, 0) }}</div>
                        <div class="stat-label">แคลอรี่</div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon text-success">
                            <i class="fas fa-running"></i>
                        </div>
                        <div class="stat-value">{{ $totalActivities }}</div>
                        <div class="stat-label">กิจกรรมทั้งหมด</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Weekly Progress -->
                <div class="col-md-8 mb-4">
                    <div class="card gofit-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">ความคืบหน้ารายสัปดาห์</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span>เป้าหมายรายสัปดาห์ ({{ $weeklyGoal }} กม.)</span>
                                        <span>{{ number_format($weeklyDistance, 1) }} กม.</span>
                                    </div>
                                    <div class="gofit-progress">
                                        <div class="progress-bar" style="width: {{ $weeklyGoalProgress }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="weekly-stats">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>ระยะทางสัปดาห์นี้:</span>
                                            <span class="fw-bold">{{ number_format($weeklyDistance, 1) }} กม.</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>แคลอรี่สัปดาห์นี้:</span>
                                            <span class="fw-bold">{{ number_format($weeklyCalories, 0) }} kcal</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="weeklyChart" style="height: 150px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="col-md-4 mb-4">
                    <div class="card gofit-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">เหรียญตราล่าสุด</h5>
                            <a href="{{ route('badges.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">ดูทั้งหมด</a>
                        </div>
                        <div class="card-body">
                            @if($badges->count() > 0)
                                <div class="row">
                                    @foreach($badges->take(4) as $badge)
                                        <div class="col-6 text-center mb-3">
                                            <div class="badge-card">
                                                <div class="badge-icon">
                                                    <i class="fa {{ $badge->badge_icon ?? 'fa-medal' }}"></i>
                                                </div>
                                                <div class="badge-name">{{ $badge->badge_name }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center my-4">
                                    <div class="mb-3">
                                        <i class="fas fa-medal fa-3x text-muted"></i>
                                    </div>
                                    <p class="text-muted">คุณยังไม่ได้รับเหรียญตราใดๆ เริ่มวิ่งเพื่อรับเหรียญตราแรกของคุณ!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card gofit-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">กิจกรรมล่าสุด</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill">ดูทั้งหมด</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="gofit-table">
                                    <thead>
                                        <tr>
                                            <th>วันที่</th>
                                            <th>ระยะทาง</th>
                                            <th>ระยะเวลา</th>
                                            <th>ความเร็วเฉลี่ย</th>
                                            <th>แคลอรี่</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentActivities as $activity)
                                            <tr>
                                                <td>{{ $activity->start_time->format('d M Y, H:i') }}</td>
                                                <td>{{ number_format($activity->distance, 2) }} กม.</td>
                                                <td>
                                                    @if($activity->end_time)
                                                        {{ gmdate('H:i:s', $activity->end_time->diffInSeconds($activity->start_time)) }}
                                                    @else
                                                        <span class="badge bg-warning">กำลังดำเนินการ</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($activity->average_speed, 1) }} กม./ชม.</td>
                                                <td>{{ number_format($activity->calories_burned, 0) }} kcal</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">ยังไม่มีกิจกรรมที่บันทึก</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ตัวอย่างกราฟ
        var options = {
            series: [{
                name: 'ระยะทาง (กม.)',
                data: [2.5, 3.2, 0, 4.1, 1.8, 5.2, {{ $weeklyDistance }}]
            }],
            chart: {
                type: 'bar',
                height: 150,
                toolbar: {
                    show: false
                }
            },
            colors: ['#2DC679'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: ['จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส', 'อา'],
                labels: {
                    style: {
                        fontSize: '10px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return value.toFixed(1);
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#weeklyChart"), options);
        chart.render();
    });
</script>
@endsection
