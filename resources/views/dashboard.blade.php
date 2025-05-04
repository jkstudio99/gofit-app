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

    /* Mobile optimizations */
    @media (max-width: 767.98px) {
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
</style>
@endsection

@section('content')
<div class="container"></div>
    <!-- Welcome Header -->
    <div class="welcome-header">
        <h2>สวัสดี, {{ Auth::user()->firstname }}</h2>
        <p class="welcome-text">ยินดีต้อนรับกลับมาที่ GoFit! เริ่มต้นการวิ่งวันนี้เพื่อสุขภาพที่ดีขึ้น</p>
        <a href="{{ route('run.index') }}" class="btn btn-light btn-lg px-5 py-3 shadow-sm" style="font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease; border-radius: 50px;">
            <i class="fas fa-running me-2"></i> เริ่มวิ่งเลย
        </a>
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
                <div class="stat-value">16.7</div>
                <div class="stat-label">กิโลเมตรสะสม</div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-fire" style="color: #ff5e57;"></i>
                </div>
                <div class="stat-value">1,040</div>
                <div class="stat-label">แคลอรี่ที่เผาผลาญ</div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-running" style="color: #2ecc71;"></i>
                </div>
                <div class="stat-value">7</div>
                <div class="stat-label">กิจกรรมทั้งหมด</div>
            </div>
        </div>
    </div>

    <!-- Weekly Progress -->
    <div class="weekly-progress-section">
        <div class="section-header">
            <h5><i class="fas fa-chart-line"></i> ความคืบหน้ารายสัปดาห์</h5>
        </div>

        <div class="goal-info">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="goal-label">เป้าหมายรายสัปดาห์</div>
                <div class="goal-target">
                    <span class="current-value">7.8</span>
                    <span class="target-value">/ 20 กม.</span>
                </div>
            </div>

            <div class="progress-bar-container">
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: 39%"></div>
                </div>
                <div class="progress-percentage">39% ของเป้าหมาย</div>
            </div>
        </div>

        <div class="weekly-details">
            <div class="weekly-detail-item">
                <div class="weekly-detail-icon distance">
                    <i class="fas fa-road"></i>
                </div>
                <div class="weekly-detail-label">ระยะทางสัปดาห์นี้:</div>
                <div class="weekly-detail-value">7.8 กม.</div>
            </div>

            <div class="weekly-detail-item">
                <div class="weekly-detail-icon calories">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="weekly-detail-label">แคลอรี่สัปดาห์นี้:</div>
                <div class="weekly-detail-value">490 kcal</div>
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
            <div class="activity-item">
                <div class="activity-date">03 พ.ค. 2025</div>
                <div class="activity-details">
                    <div class="activity-stat">
                        <div class="activity-icon distance"><i class="fas fa-road"></i></div>
                        <div class="activity-value">2.5 กม.</div>
                    </div>
                    <div class="activity-stat">
                        <div class="activity-icon time"><i class="fas fa-clock"></i></div>
                        <div class="activity-value">30:25</div>
                    </div>
                    <div class="activity-stat">
                        <div class="activity-icon calories"><i class="fas fa-fire"></i></div>
                        <div class="activity-value">180 kcal</div>
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
