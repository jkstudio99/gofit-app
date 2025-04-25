@extends('layouts.admin')

@section('title', 'แดชบอร์ด')

@section('breadcrumb')
<li class="breadcrumb-item active">แดชบอร์ด</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalUsers }}</h3>
                <p>ผู้ใช้งานทั้งหมด</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.users.index') }}" class="small-box-footer">รายละเอียดเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalActivities }}</h3>
                <p>กิจกรรมทั้งหมด</p>
            </div>
            <div class="icon">
                <i class="fas fa-running"></i>
            </div>
            <a href="{{ route('admin.user-activities') }}" class="small-box-footer">รายละเอียดเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalBadges }}</h3>
                <p>เหรียญตราทั้งหมด</p>
            </div>
            <div class="icon">
                <i class="fas fa-medal"></i>
            </div>
            <a href="{{ route('admin.badges.index') }}" class="small-box-footer">รายละเอียดเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalRewards }}</h3>
                <p>รางวัลทั้งหมด</p>
            </div>
            <div class="icon">
                <i class="fas fa-gift"></i>
            </div>
            <a href="{{ route('admin.rewards.index') }}" class="small-box-footer">รายละเอียดเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7 connectedSortable">
        <!-- Custom tabs (Charts with tabs)-->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    สถิติกิจกรรมประจำสัปดาห์
                </h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="chart">
                    <canvas id="activityChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- ผู้ใช้ที่มีกิจกรรมมากที่สุด -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-1"></i>
                    ผู้ใช้ที่มีกิจกรรมมากที่สุด
                </h3>
            </div><!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>ชื่อผู้ใช้</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>จำนวนกิจกรรม</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topUsers as $index => $user)
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
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- กิจกรรมล่าสุด -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-running mr-1"></i>
                    กิจกรรมล่าสุด
                </h3>
            </div><!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-sm">
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
                        @foreach($latestActivities as $activity)
                        <tr>
                            <td>{{ $activity->user->username }}</td>
                            <td>{{ $activity->activity_type }}</td>
                            <td>{{ number_format($activity->distance, 2) }} กม.</td>
                            <td>{{ gmdate('H:i:s', $activity->duration) }}</td>
                            <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    <!-- /.Left col -->

    <!-- right col (We are only adding the ID to make the widgets sortable)-->
    <section class="col-lg-5 connectedSortable">
        <!-- สรุปข้อมูลเดือนนี้ -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    สรุปข้อมูลเดือนนี้
                </h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="info-box mb-3 bg-info">
                    <span class="info-box-icon"><i class="fas fa-user-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">ผู้ใช้ใหม่</span>
                        <span class="info-box-number">{{ $newUsers }} คน</span>
                    </div>
                </div>

                <div class="info-box mb-3 bg-success">
                    <span class="info-box-icon"><i class="fas fa-walking"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">กิจกรรมทั้งหมด</span>
                        <span class="info-box-number">{{ $monthlyActivities }} กิจกรรม</span>
                    </div>
                </div>

                <div class="info-box mb-3 bg-warning">
                    <span class="info-box-icon"><i class="fas fa-exchange-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">การแลกรางวัล</span>
                        <span class="info-box-number">{{ $monthlyRedeems }} ครั้ง</span>
                    </div>
                </div>
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- การแลกรางวัลล่าสุด -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-gift mr-1"></i>
                    การแลกรางวัลล่าสุด
                </h3>
            </div><!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ผู้ใช้</th>
                            <th>รางวัล</th>
                            <th>เหรียญที่ใช้</th>
                            <th>วันที่</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestRedeems as $redeem)
                        <tr>
                            <td>{{ $redeem->user->username }}</td>
                            <td>{{ $redeem->reward->reward_name }}</td>
                            <td>{{ $redeem->coin_used }}</td>
                            <td>{{ $redeem->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="{{ route('admin.redeems') }}" class="uppercase">ดูทั้งหมด</a>
            </div><!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </section>
    <!-- right col -->
</div>
<!-- /.row (main row) -->
@endsection

@section('scripts')
<!-- ChartJS -->
<script>
    $(function () {
        // สถิติกิจกรรมประจำสัปดาห์
        var ctx = document.getElementById('activityChart').getContext('2d');
        var activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'จำนวนกิจกรรม',
                    data: {!! json_encode($dailyActivities) !!},
                    backgroundColor: 'rgba(60,141,188,0.2)',
                    borderColor: 'rgba(60,141,188,1)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'วัน'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'จำนวนกิจกรรม'
                        }
                    }]
                }
            }
        });
    });
</script>
@endsection
