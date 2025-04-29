@extends('layouts.admin')

@section('title', 'สถิติเป้าหมายของผู้ใช้')

@section('styles')
<style>
    .goal-card {
        transition: all 0.3s ease;
    }
    .goal-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .stat-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2rem;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">สถิติเป้าหมายของผู้ใช้</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> กลับสู่แดชบอร์ด
            </a>
        </div>
    </div>

    <!-- สรุปข้อมูลเป้าหมาย -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card goal-card bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <div class="stat-circle bg-primary bg-opacity-25 mb-3">
                        <span>{{ $totalGoals }}</span>
                    </div>
                    <h4>เป้าหมายทั้งหมด</h4>
                    <p class="text-muted">จำนวนเป้าหมายที่สร้างในระบบ</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card goal-card bg-success bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <div class="stat-circle bg-success bg-opacity-25 mb-3">
                        <span>{{ $completedGoals }}</span>
                    </div>
                    <h4>สำเร็จแล้ว</h4>
                    <p class="text-muted">จำนวนเป้าหมายที่สำเร็จแล้ว</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card goal-card bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <div class="stat-circle bg-warning bg-opacity-25 mb-3">
                        <span>{{ $inProgressGoals }}</span>
                    </div>
                    <h4>กำลังดำเนินการ</h4>
                    <p class="text-muted">เป้าหมายที่กำลังดำเนินการ</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card goal-card bg-info bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <div class="stat-circle bg-info bg-opacity-25 mb-3">
                        <span>{{ $completionRate }}%</span>
                    </div>
                    <h4>อัตราความสำเร็จ</h4>
                    <p class="text-muted">อัตราการทำเป้าหมายสำเร็จ</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ประเภทเป้าหมายที่นิยม -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> ประเภทเป้าหมายที่ได้รับความนิยม</h5>
                </div>
                <div class="card-body">
                    <canvas id="goalTypesChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- แนวโน้มการสร้างเป้าหมาย -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> แนวโน้มการสร้างเป้าหมาย</h5>
                </div>
                <div class="card-body">
                    <canvas id="goalTrendsChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ผู้ใช้ที่มีเป้าหมายมากที่สุด -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i> ผู้ใช้ที่มีเป้าหมายมากที่สุด</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>ผู้ใช้</th>
                                    <th>จำนวนเป้าหมาย</th>
                                    <th>สำเร็จแล้ว</th>
                                    <th>อัตราความสำเร็จ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->profile_image)
                                                <img src="{{ asset('storage/' . $user->profile_image) }}"
                                                     class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                     alt="{{ $user->username }}">
                                            @else
                                                <div class="rounded-circle me-2 bg-secondary d-flex justify-content-center align-items-center text-white"
                                                     style="width: 30px; height: 30px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            {{ $user->username }}
                                        </div>
                                    </td>
                                    <td>{{ $user->goals_count }}</td>
                                    <td>{{ $user->completed_goals_count }}</td>
                                    <td>
                                        @php
                                            $rate = $user->goals_count > 0
                                                ? round(($user->completed_goals_count / $user->goals_count) * 100)
                                                : 0;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">{{ $rate }}%</div>
                                            <div class="progress flex-grow-1" style="height:.5rem">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $rate }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- เป้าหมายล่าสุด -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i> เป้าหมายล่าสุด</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>เป้าหมาย</th>
                                    <th>ผู้ใช้</th>
                                    <th>ประเภท</th>
                                    <th>สถานะ</th>
                                    <th>วันที่สร้าง</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestGoals as $goal)
                                <tr>
                                    <td>{{ $goal->title }}</td>
                                    <td>{{ $goal->user->username }}</td>
                                    <td>
                                        @if($goal->goal_type == 'distance')
                                            <span class="badge bg-primary">ระยะทาง</span>
                                        @elseif($goal->goal_type == 'time')
                                            <span class="badge bg-info">เวลา</span>
                                        @elseif($goal->goal_type == 'frequency')
                                            <span class="badge bg-warning">ความถี่</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $goal->goal_type }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($goal->status == 'completed')
                                            <span class="badge bg-success">สำเร็จแล้ว</span>
                                        @elseif($goal->status == 'in_progress')
                                            <span class="badge bg-warning">กำลังดำเนินการ</span>
                                        @elseif($goal->status == 'failed')
                                            <span class="badge bg-danger">ไม่สำเร็จ</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $goal->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $goal->created_at->format('d/m/Y') }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ประเภทเป้าหมายที่นิยม
        const goalTypesCtx = document.getElementById('goalTypesChart').getContext('2d');
        const goalTypesChart = new Chart(goalTypesCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($goalTypes->pluck('label')) !!},
                datasets: [{
                    data: {!! json_encode($goalTypes->pluck('count')) !!},
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(76, 175, 80, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // แนวโน้มการสร้างเป้าหมาย
        const goalTrendsCtx = document.getElementById('goalTrendsChart').getContext('2d');
        const goalTrendsChart = new Chart(goalTrendsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($goalTrends->pluck('month')) !!},
                datasets: [{
                    label: 'เป้าหมายที่สร้าง',
                    data: {!! json_encode($goalTrends->pluck('created')) !!},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'เป้าหมายที่สำเร็จ',
                    data: {!! json_encode($goalTrends->pluck('completed')) !!},
                    borderColor: 'rgba(76, 175, 80, 1)',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
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
