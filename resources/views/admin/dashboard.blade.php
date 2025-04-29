@extends('layouts.admin')

@section('title', 'แดชบอร์ด')

@section('content')
    <h1 class="mb-4">แดชบอร์ดผู้ดูแลระบบ</h1>

    <!-- Small boxes (Stat box) -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card gofit-card bg-primary bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ $totalUsers ?? 0 }}</h3>
                            <p class="mb-0">ผู้ใช้งานทั้งหมด</p>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.users.index') }}" class="text-primary text-decoration-none">รายละเอียดเพิ่มเติม <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card gofit-card bg-success bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ $totalActivities ?? 0 }}</h3>
                            <p class="mb-0">กิจกรรมทั้งหมด</p>
                        </div>
                        <div>
                            <i class="fas fa-running fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.user-activities') }}" class="text-success text-decoration-none">รายละเอียดเพิ่มเติม <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card gofit-card bg-warning bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ $totalBadges ?? 0 }}</h3>
                            <p class="mb-0">เหรียญตราทั้งหมด</p>
                        </div>
                        <div>
                            <i class="fas fa-medal fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.badges.index') }}" class="text-warning text-decoration-none">รายละเอียดเพิ่มเติม <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card gofit-card bg-danger bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ $totalRewards ?? 0 }}</h3>
                            <p class="mb-0">รางวัลทั้งหมด</p>
                        </div>
                        <div>
                            <i class="fas fa-gift fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.rewards') }}" class="text-danger text-decoration-none">รายละเอียดเพิ่มเติม <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- เป้าหมายของผู้ใช้ -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card gofit-card bg-info bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-4">สถิติเป้าหมายของผู้ใช้</h3>
                            <p class="mb-0">ดูสถิติการตั้งเป้าหมายของผู้ใช้งานทั้งหมดในระบบ</p>
                        </div>
                        <div>
                            <i class="fas fa-bullseye fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.goals.statistics') }}" class="text-info text-decoration-none">
                        ไปยังหน้าสถิติเป้าหมาย <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- สถิติเหรียญตรา -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card gofit-card bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-4">สถิติเหรียญตรา</h3>
                            <p class="mb-0">วิเคราะห์การกระจายตัวของเหรียญตราและการได้รับเหรียญตราของผู้ใช้</p>
                        </div>
                        <div>
                            <i class="fas fa-medal fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.badges.statistics') }}" class="text-warning text-decoration-none">
                        ไปยังหน้าสถิติเหรียญตรา <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-lg-7 mb-4">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card gofit-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        สถิติกิจกรรมประจำสัปดาห์
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activityChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <!-- ผู้ใช้ที่มีกิจกรรมมากที่สุด -->
            <div class="card gofit-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        ผู้ใช้ที่มีกิจกรรมมากที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>ชื่อผู้ใช้</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>จำนวนกิจกรรม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers ?? [] as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $user->activity_count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- กิจกรรมล่าสุด -->
            <div class="card gofit-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-running me-2"></i>
                        กิจกรรมล่าสุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>ผู้ใช้</th>
                                    <th>ประเภทกิจกรรม</th>
                                    <th>ระยะทาง</th>
                                    <th>ระยะเวลา</th>
                                    <th>วันที่</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestActivities ?? [] as $key => $activity)
                                <tr>
                                    <td>{{ $activity->user->username ?? 'ไม่ระบุชื่อ' }}</td>
                                    <td>{{ $activity->activity_type }}</td>
                                    <td>{{ number_format($activity->distance, 2) }} กม.</td>
                                    <td>
                                        @if($activity->end_time)
                                            @php
                                            if(is_string($activity->start_time) || is_string($activity->end_time)) {
                                                $start = is_string($activity->start_time) ? strtotime($activity->start_time) : $activity->start_time->timestamp;
                                                $end = is_string($activity->end_time) ? strtotime($activity->end_time) : $activity->end_time->timestamp;
                                                echo gmdate('H:i:s', $end - $start);
                                            } else {
                                                echo gmdate('H:i:s', $activity->end_time->diffInSeconds($activity->start_time));
                                            }
                                            @endphp
                                        @else
                                            <span class="badge bg-warning">กำลังดำเนินการ</span>
                                        @endif
                                    </td>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.Left col -->

        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <div class="col-lg-5">
            <!-- สรุปข้อมูลเดือนนี้ -->
            <div class="card gofit-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        สรุปข้อมูลเดือนนี้
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex p-3 mb-3 bg-info bg-opacity-10 rounded align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-plus fa-2x text-info"></i>
                        </div>
                        <div>
                            <div class="text-muted">ผู้ใช้ใหม่</div>
                            <div class="fs-5 fw-bold">{{ $newUsers ?? 0 }} คน</div>
                        </div>
                    </div>

                    <div class="d-flex p-3 mb-3 bg-success bg-opacity-10 rounded align-items-center">
                        <div class="me-3">
                            <i class="fas fa-walking fa-2x text-success"></i>
                        </div>
                        <div>
                            <div class="text-muted">กิจกรรมทั้งหมด</div>
                            <div class="fs-5 fw-bold">{{ $monthlyActivities ?? 0 }} กิจกรรม</div>
                        </div>
                    </div>

                    <div class="d-flex p-3 bg-warning bg-opacity-10 rounded align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exchange-alt fa-2x text-warning"></i>
                        </div>
                        <div>
                            <div class="text-muted">การแลกรางวัล</div>
                            <div class="fs-5 fw-bold">{{ $monthlyRedeems ?? 0 }} ครั้ง</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- การแลกรางวัลล่าสุด -->
            <div class="card gofit-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-gift me-2"></i>
                        การแลกรางวัลล่าสุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>ผู้ใช้</th>
                                    <th>รางวัล</th>
                                    <th>เหรียญที่ใช้</th>
                                    <th>วันที่</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestRedeems ?? [] as $redeem)
                                <tr>
                                    <td>{{ $redeem->user->username }}</td>
                                    <td>{{ $redeem->reward->reward_name }}</td>
                                    <td>{{ $redeem->points_used }}</td>
                                    <td>{{ $redeem->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.redeems') }}" class="text-decoration-none">ดูทั้งหมด</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // สร้างกราฟสถิติกิจกรรม
        var ctx = document.getElementById('activityChart').getContext('2d');
        var activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'],
                datasets: [{
                    label: 'จำนวนกิจกรรม',
                    data: [12, 19, 8, 15, 12, 28, 20],
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
