@extends('layouts.admin')

@section('title', 'สถิติเหรียญตรา')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .badge-icon {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }

    .badge-small {
        width: 20px;
        height: 20px;
        object-fit: contain;
        margin-right: 5px;
    }

    .user-avatar {
        width: 30px;
        height: 30px;
        object-fit: cover;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(45, 198, 121, 0.05);
    }

    /* Style for the badges */
    .badge-type-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        color: white;
    }

    /* User links - remove underline */
    .user-link {
        color: #495057;
        text-decoration: none !important;
    }

    .user-link:hover {
        color: #2DC679;
    }

    /* Card and Table Design */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,0.15) !important;
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
    }

    .table th {
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        border-top: none;
    }

    .table td {
        vertical-align: middle;
        padding: 0.75rem 1rem;
    }

    /* Badge pill styling */
    .badge-pill-custom {
        border-radius: 50px;
        padding: 0.35rem 0.75rem;
        font-weight: 500;
        font-size: 0.8rem;
    }

    /* Dashboard style icon container */
    .icon-container {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">สถิติและการวิเคราะห์เหรียญตรา</h2>
        <div>
            <a href="{{ route('admin.badges.history') }}" class="btn btn-outline-info me-2">
                <i class="fas fa-history me-1"></i> ประวัติการได้รับ
            </a>
            <a href="{{ route('admin.badges.index') }}" class="btn btn-primary">
                <i class="fas fa-medal me-1"></i> จัดการเหรียญตรา
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-1 fw-bold mb-0 text-primary">{{ number_format($totalBadges) }}</h2>
                            <p class="text-muted mb-0">เหรียญตราทั้งหมด</p>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-medal fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-1 fw-bold mb-0 text-success">{{ number_format($totalAssignments) }}</h2>
                            <p class="text-muted mb-0">จำนวนการได้รับ</p>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-award fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-1 fw-bold mb-0 text-info">{{ number_format($usersWithBadges) }}</h2>
                            <p class="text-muted mb-0">ผู้ใช้ที่มีเหรียญ</p>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-1 fw-bold mb-0 text-warning">{{ $averageBadgesPerUser }}</h2>
                            <p class="text-muted mb-0">เหรียญเฉลี่ยต่อผู้ใช้</p>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-chart-pie fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Badge Distribution by Type -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>การกระจายตัวของเหรียญตราตามประเภท
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="badgeDistributionChart"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="row">
                        @foreach($badgesByType as $type)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                @php
                                    $bgColors = [
                                        'distance' => 'bg-info',
                                        'calories' => 'bg-danger',
                                        'streak' => 'bg-warning',
                                        'speed' => 'bg-success',
                                        'event' => 'bg-primary',
                                    ];
                                    $bgColor = isset($bgColors[$type['type']]) ? $bgColors[$type['type']] : 'bg-secondary';

                                    $icons = [
                                        'distance' => 'fa-route',
                                        'calories' => 'fa-fire-alt',
                                        'streak' => 'fa-calendar-check',
                                        'speed' => 'fa-tachometer-alt',
                                        'event' => 'fa-calendar-day',
                                    ];
                                    $icon = isset($icons[$type['type']]) ? $icons[$type['type']] : 'fa-medal';
                                @endphp
                                <div class="badge-type-icon {{ $bgColor }}">
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                <div>
                                    <strong>{{ $type['label'] }}:</strong>
                                    <span class="text-muted">{{ $type['count'] }} เหรียญตรา</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Badge Trends -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-chart-line text-primary me-2"></i>แนวโน้มการได้รับเหรียญตรารายเดือน
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="badgeTrendsChart"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>เดือน</th>
                                    <th class="text-center">จำนวนการได้รับเหรียญตรา</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($badgeTrends as $trend)
                                <tr>
                                    <td>{{ $trend['month'] }}</td>
                                    <td class="text-center">
                                        <span class="badge-pill-custom bg-primary">{{ $trend['count'] }}</span>
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

    <div class="row g-4 mb-4">
        <!-- Most Earned Badges -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-trophy text-warning me-2"></i>เหรียญตราที่มีผู้ได้รับมากที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>เหรียญตรา</th>
                                    <th>ประเภท</th>
                                    <th class="text-center">จำนวนผู้ได้รับ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostEarnedBadges as $index => $badge)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                @if($badge->badge_image)
                                                    <img src="{{ asset('storage/' . $badge->badge_image) }}" class="badge-icon" alt="{{ $badge->badge_name }}">
                                                @else
                                                    <i class="fas fa-medal text-warning"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $badge->badge_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($badge->type == 'distance')
                                            <span class="badge bg-info text-white">
                                                <i class="fas fa-route me-1"></i> ระยะทาง
                                            </span>
                                        @elseif($badge->type == 'calories')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-fire-alt me-1"></i> แคลอรี่
                                            </span>
                                        @elseif($badge->type == 'streak')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-calendar-check me-1"></i> ต่อเนื่อง
                                            </span>
                                        @elseif($badge->type == 'speed')
                                            <span class="badge bg-success">
                                                <i class="fas fa-tachometer-alt me-1"></i> ความเร็ว
                                            </span>
                                        @elseif($badge->type == 'event')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-calendar-day me-1"></i> กิจกรรม
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-medal me-1"></i> {{ $badge->type }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill">{{ $badge->users_count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Least Earned Badges -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-medal text-danger me-2"></i>เหรียญตราที่มีผู้ได้รับน้อยที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>เหรียญตรา</th>
                                    <th>ประเภท</th>
                                    <th class="text-center">จำนวนผู้ได้รับ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leastEarnedBadges as $index => $badge)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                @if($badge->badge_image)
                                                    <img src="{{ asset('storage/' . $badge->badge_image) }}" class="badge-icon" alt="{{ $badge->badge_name }}">
                                                @else
                                                    <i class="fas fa-medal text-secondary"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $badge->badge_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($badge->type == 'distance')
                                            <span class="badge bg-info text-white">
                                                <i class="fas fa-route me-1"></i> ระยะทาง
                                            </span>
                                        @elseif($badge->type == 'calories')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-fire-alt me-1"></i> แคลอรี่
                                            </span>
                                        @elseif($badge->type == 'streak')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-calendar-check me-1"></i> ต่อเนื่อง
                                            </span>
                                        @elseif($badge->type == 'speed')
                                            <span class="badge bg-success">
                                                <i class="fas fa-tachometer-alt me-1"></i> ความเร็ว
                                            </span>
                                        @elseif($badge->type == 'event')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-calendar-day me-1"></i> กิจกรรม
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-medal me-1"></i> {{ $badge->type }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill">{{ $badge->users_count }}</span>
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

    <div class="row g-4 mb-4">
        <!-- Top Users with Badges -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-users text-primary me-2"></i>ผู้ใช้ที่มีเหรียญตรามากที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>ชื่อผู้ใช้</th>
                                    <th class="text-center">จำนวนเหรียญตรา</th>
                                    <th class="text-end">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                @if(isset($user->profile_image))
                                                    <img src="{{ asset('profile_images/' . $user->profile_image) }}" class="rounded-circle user-avatar" alt="{{ $user->username }}">
                                                @else
                                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white user-avatar">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->username }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill py-2 px-3">
                                            <i class="fas fa-medal me-1"></i> {{ $user->badges_count }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.users.show', $user->user_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-eye me-1"></i> ดูข้อมูลผู้ใช้
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

        <!-- Recent Badge Assignments -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-history text-primary me-2"></i>การได้รับเหรียญตราล่าสุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ผู้ใช้</th>
                                    <th>เหรียญตรา</th>
                                    <th>เวลาที่ได้รับ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAssignments as $assignment)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.users.show', $assignment->user_id) }}" class="text-decoration-none">
                                            {{ $assignment->username }}
                                        </a>
                                    </td>
                                    <td>{{ $assignment->badge_name }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($assignment->created_at)->diffForHumans() }}
                                        </span>
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
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Badge Type Distribution Chart
        const badgeTypeData = @json($badgesByType);
        const badgeTypeColors = [
            'rgba(0, 123, 255, 0.8)',  // Blue
            'rgba(220, 53, 69, 0.8)',   // Red
            'rgba(255, 193, 7, 0.8)',   // Yellow
            'rgba(40, 167, 69, 0.8)',   // Green
            'rgba(23, 162, 184, 0.8)',  // Cyan
            'rgba(108, 117, 125, 0.8)'  // Gray
        ];

        const badgeDistributionCtx = document.getElementById('badgeDistributionChart').getContext('2d');
        new Chart(badgeDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: badgeTypeData.map(item => item.label),
                datasets: [{
                    data: badgeTypeData.map(item => item.count),
                    backgroundColor: badgeTypeColors,
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 15,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.formattedValue || '';
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((context.raw / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Badge Trends Chart
        const badgeTrendsData = @json($badgeTrends);
        const badgeTrendsCtx = document.getElementById('badgeTrendsChart').getContext('2d');

        new Chart(badgeTrendsCtx, {
            type: 'line',
            data: {
                labels: badgeTrendsData.map(item => item.month),
                datasets: [{
                    label: 'จำนวนการได้รับเหรียญตรา',
                    data: badgeTrendsData.map(item => item.count),
                    backgroundColor: 'rgba(45, 198, 121, 0.2)',
                    borderColor: '#2DC679',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#2DC679',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    });
</script>
<style>
    body {
        background-color: #f9f9f9;
    }
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        transition: box-shadow 0.3s ease-in-out;
    }
</style>
@endsection
