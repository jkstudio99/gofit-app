@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
    /* Modern Dashboard Styling */
    .dashboard-container {
        padding: 1rem 0;
    }

    .welcome-header {
        background: linear-gradient(135deg, #3498db, #2ecc71);
        color: white;
        border-radius: 16px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .welcome-header::after {
        content: '';
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .welcome-header h2 {
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 2.2rem;
    }

    .welcome-text {
        max-width: 70%;
        margin-bottom: 1.5rem;
    }

    /* Desktop stats cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 16px rgba(0,0,0,0.05);
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 22px rgba(0,0,0,0.08);
    }

    /* Goals Section Styling */
    .goals-section {
        margin-bottom: 2rem;
    }

    .section-header {
        margin-bottom: 1.2rem;
    }

    .section-header h5 {
        font-weight: 600;
        margin-bottom: 0;
    }

    .section-header h5 i {
        color: #2ecc71;
        margin-right: 8px;
    }

    .view-all-link {
        color: #2ecc71;
        font-weight: 500;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .view-all-link:hover {
        text-decoration: underline;
        color: #27ae60;
    }

    .goals-section .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .goals-section .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }

    .goals-section .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.7em;
        font-weight: 500;
    }

    .goals-section .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .goals-section .progress-bar {
        border-radius: 10px;
    }

    /* Card styles */
    .gofit-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 8px 16px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .gofit-card:hover {
        box-shadow: 0 12px 22px rgba(0,0,0,0.08);
    }

    .gofit-card .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }

    .gofit-card .card-body {
        padding: 1.5rem;
    }

    .gofit-progress {
        height: 0.8rem;
        background-color: #f1f1f1;
        border-radius: 1rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .gofit-progress .progress-bar {
        background: linear-gradient(90deg, #3498db, #2ecc71);
        height: 100%;
        border-radius: 1rem;
    }

    .weekly-stats {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
    }

    .badge-card {
        text-align: center;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .badge-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.05);
    }

    .badge-icon {
        font-size: 2rem;
        color: #ffc107;
        margin-bottom: 0.5rem;
    }

    .badge-name {
        font-weight: 600;
        font-size: 0.85rem;
    }

    .gofit-table {
        width: 100%;
    }

    .gofit-table th {
        background-color: #f8f9fa;
        padding: 1rem;
        font-weight: 600;
    }

    .gofit-table td {
        padding: 1rem;
        border-bottom: 1px solid #f1f1f1;
    }

    .gofit-table tr:last-child td {
        border-bottom: none;
    }

    /* Mobile and tablet optimizations */
    @media (max-width: 991.98px) {
        .welcome-header {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .welcome-header h2 {
            font-size: 1.8rem;
        }

        .welcome-text {
            max-width: 100%;
        }

        .run-btn {
            width: 100%;
            display: block;
            margin-left: auto;
            margin-right: auto;
            border-radius: 9999px !important; /* แคปซูลแบบสมบูรณ์ */
            font-weight: 600;
        }

        .desktop-stats {
            display: none;
        }
    }

    /* Hide mobile stats on desktop */
    @media (min-width: 768px) {
        .mobile-stats {
            display: none;
        }
    }

    /* Hide the question mark icon in the fixed position (onboarding help) */
    .btn-help,
    .floating-help-btn,
    .help-button,
    .question-mark-button,
    .onboarding-help,
    .fixed-help-button {
        display: none !important;
    }

    /* Hide specific question mark button with fixed position in bottom-right */
    .btn-floating,
    .btn-floating.btn-large,
    .fixed-action-btn,
    .fixed-help {
        display: none !important;
    }

    /* Hide by position */
    .position-fixed.bottom-0.end-0,
    [style*="position: fixed"][style*="bottom"][style*="right"],
    [class*="help"][class*="icon"],
    [class*="question"][class*="icon"] {
        display: none !important;
    }

    .progress-bar-container {
        margin-top: 10px;
    }

    .progress-bar-custom {
        height: 8px;
        background-color: #f0f0f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3498db, #2ecc71);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .progress-percentage {
        font-size: 0.85rem;
        text-align: right;
        margin-top: 5px;
        color: #666;
    }

    .weekly-details {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
        width: 100%;
    }

    .weekly-detail-item {
        display: flex;
        align-items: center;
        flex: 1;
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 12px;
        margin: 0 5px;
    }

    .weekly-detail-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        flex-shrink: 0;
    }

    .weekly-detail-icon.distance {
        background-color: rgba(46, 204, 113, 0.1);
        color: #2ecc71;
    }

    .weekly-detail-icon.calories {
        background-color: rgba(255, 94, 87, 0.1);
        color: #ff5e57;
    }

    .weekly-detail-label {
        font-size: 0.85rem;
        margin-right: 5px;
        color: #666;
        white-space: nowrap;
    }

    .weekly-detail-value {
        font-weight: 600;
        white-space: nowrap;
    }

    /* Mobile adjustments for weekly details */
    @media (max-width: 767.98px) {
        .weekly-details {
            flex-direction: column;
            gap: 10px;
        }

        .weekly-detail-item {
            margin: 0;
            padding: 10px;
            flex-wrap: nowrap;
            justify-content: space-between;
        }

        .weekly-detail-label {
            font-size: 0.8rem;
            margin-right: 5px;
        }

        .weekly-detail-value {
            font-size: 0.9rem;
            white-space: nowrap;
            text-align: right;
        }
    }

    /* Desktop specific rules */
    @media (min-width: 992px) {
        .run-btn {
            width: auto;
            display: inline-block;
            border-radius: 9999px !important; /* ขอบแคปซูลสำหรับ desktop */
        }
    }
</style>
@endsection

@section('content')
<div class="container"></div>
    <!-- Welcome Header -->
    <div class="welcome-header">
        <h2>สวัสดี, {{ Auth::user()->firstname }}</h2>
        <p class="welcome-text">ยินดีต้อนรับกลับมาที่ GoFit! เริ่มต้นการวิ่งวันนี้เพื่อสุขภาพที่ดีขึ้น</p>
        <div class="text-center text-lg-start">
            <a href="{{ route('run.index') }}" class="btn btn-light btn-lg px-5 py-3 shadow-sm run-btn" style="font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease; border-radius: 50px;">
                <i class="fas fa-running me-2"></i> เริ่มวิ่งเลย
            </a>
        </div>
    </div>

    <!-- Mobile Stats (3-column) -->
    <div class="mobile-stats d-md-none">
        @include('components.stats-summary')
    </div>

    <!-- Desktop Stats Summary Cards -->
    <div class="desktop-stats d-none d-md-flex mx-3" style="gap: 15px; margin-bottom: 20px;">
        <div style="flex: 1;">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-road" style="color: #2ecc71;"></i>
                </div>
                <div class="stat-value">{{ number_format($totalDistance ?? 0, 1) }}</div>
                <div class="stat-label">กิโลเมตรสะสม</div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-fire" style="color: #ff5e57;"></i>
                </div>
                <div class="stat-value">{{ number_format($totalCalories ?? 0) }}</div>
                <div class="stat-label">แคลอรี่ที่เผาผลาญ</div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check" style="color: #2ecc71;"></i>
                </div>
                <div class="stat-value">{{ $userRegisteredEvents ?? 0 }}</div>
                <div class="stat-label">กิจกรรมที่เข้าร่วม</div>
            </div>
        </div>
    </div>

    <!-- Weekly Progress -->
    <div class="weekly-progress-section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-chart-line"></i> ความคืบหน้ารายสัปดาห์</h5>
            <a href="{{ route('goals.index') }}" class="view-all-link">จัดการเป้าหมาย <i class="fas fa-chevron-right"></i></a>
        </div>

        @if(isset($activeGoals) && count($activeGoals) > 0)
            <!-- แสดงเป้าหมายล่าสุด -->
            @php
                $latestGoal = $activeGoals->first();
                $progressPercentage = 0;
                if ($latestGoal->target_value > 0) {
                    $progressPercentage = min(100, round(($latestGoal->current_value / $latestGoal->target_value) * 100));
                }
            @endphp

        <div class="goal-info">
            <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="goal-label">
                        <span class="fw-bold">{{ $latestGoal->formattedType }}</span>
                        <span class="badge bg-primary ms-2">{{ $latestGoal->period == 'daily' ? 'รายวัน' :
                            ($latestGoal->period == 'weekly' ? 'รายสัปดาห์' :
                            ($latestGoal->period == 'monthly' ? 'รายเดือน' : 'กำหนดเอง')) }}</span>
                    </div>
                <div class="goal-target">
                        <span class="current-value">{{ number_format($latestGoal->current_value, 1) }}</span>
                        <span class="target-value">/ {{ $latestGoal->target_value }}
                            @if($latestGoal->type == 'distance')
                                กม.
                            @elseif($latestGoal->type == 'duration')
                                นาที
                            @elseif($latestGoal->type == 'calories')
                                แคลอรี่
                            @elseif($latestGoal->type == 'frequency')
                                ครั้ง
                            @endif
                        </span>
                </div>
            </div>

            <div class="progress-bar-container">
                <div class="progress-bar-custom">
                        <div class="progress-fill" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <div class="progress-percentage">{{ $progressPercentage }}% ของเป้าหมาย</div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="small text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        @if($latestGoal->end_date)
                            สิ้นสุด: {{ $latestGoal->end_date->format('d M Y') }}
                        @else
                            ไม่มีกำหนด
                        @endif
                    </div>
                    <a href="{{ route('goals.show', $latestGoal) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> รายละเอียด
                    </a>
                </div>
            </div>
        @else
        <div class="alert alert-success mt-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <div class="fw-bold mb-1">ยังไม่มีเป้าหมายรายสัปดาห์</div>
                    <p class="mb-2">ตั้งเป้าหมายการวิ่งเพื่อติดตามความคืบหน้าของคุณ</p>
                    <a href="{{ route('goals.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> ตั้งเป้าหมายใหม่
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div class="weekly-details">
            <div class="weekly-detail-item">
                <div class="weekly-detail-icon calories">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="weekly-detail-label">แคลอรี่สัปดาห์นี้:</div>
                <div class="weekly-detail-value">{{ number_format($weeklyCalories) }} kcal</div>
            </div>

            <div class="weekly-detail-item">
                <div class="weekly-detail-icon distance">
                    <i class="fas fa-road"></i>
                </div>
                <div class="weekly-detail-label">ระยะทางสัปดาห์นี้:</div>
                <div class="weekly-detail-value">{{ number_format($weeklyDistance, 1) }} กม.</div>
            </div>

            <div class="weekly-detail-item">
                <div class="weekly-detail-icon" style="background-color: rgba(52, 152, 219, 0.1); color: #3498db;">
                    <i class="fas fa-running"></i>
                </div>
                <div class="weekly-detail-label">กิจกรรมสัปดาห์นี้:</div>
                <div class="weekly-detail-value">{{ $weeklyRunCount ?? 0 }} ครั้ง</div>
            </div>
        </div>
    </div>

    <!-- Recent Activities (Mobile Friendly) -->
    <div class="recent-activities-section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-history"></i> ประวัติการวิ่งล่าสุด</h5>
            <a href="{{ route('run.history') }}" class="view-all-link">ดูทั้งหมด <i class="fas fa-chevron-right"></i></a>
        </div>

        <div class="activities-list">
            @forelse($recentActivities as $activity)
            <div class="activity-item">
                <div class="activity-date">
                    @if($activity->start_time instanceof \Carbon\Carbon)
                        {{ $activity->start_time->format('d M. Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($activity->start_time)->format('d M. Y') }}
                    @endif
                </div>
                <div class="activity-details">
                    <div class="activity-stat">
                        <div class="activity-icon distance"><i class="fas fa-road"></i></div>
                        <div class="activity-value">{{ number_format($activity->distance, 1) }} กม.</div>
                    </div>
                    <div class="activity-stat">
                        <div class="activity-icon time"><i class="fas fa-clock"></i></div>
                        <div class="activity-value">
                            @php
                                $hours = floor($activity->duration / 3600);
                                $minutes = floor(($activity->duration % 3600) / 60);
                                $seconds = $activity->duration % 60;
                                echo sprintf('%02d:%02d', $minutes, $seconds);
                                if ($hours > 0) echo sprintf('%02d:', $hours);
                            @endphp
                        </div>
                    </div>
                    <div class="activity-stat">
                        <div class="activity-icon calories"><i class="fas fa-fire"></i></div>
                        <div class="activity-value">{{ number_format($activity->calories_burned) }} kcal</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="activity-item text-center py-3">
                <p class="text-muted">ยังไม่มีกิจกรรมการวิ่ง</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Weekly Chart
        if (document.getElementById('weeklyChart')) {
            var options = {
                series: [{
                    name: 'ระยะทาง (กม.)',
                    data: @json($weeklyDistanceChart)
                }],
                chart: {
                    type: 'area',
                    height: 150,
                    sparkline: {
                        enabled: true
                    },
                    toolbar: {
                        show: false
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#3498db'],
                tooltip: {
                    fixed: {
                        enabled: false
                    },
                    x: {
                        show: true,
                        formatter: function(value) {
                            const days = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
                            return days[value];
                        }
                    },
                    y: {
                        formatter: function(value) {
                            return value.toFixed(1) + ' กม.';
                        }
                    },
                    marker: {
                        show: false
                    }
                }
            };

            var chart = new ApexCharts(document.getElementById('weeklyChart'), options);
            chart.render();
        }
    });
</script>
@endsection

