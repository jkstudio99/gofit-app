@extends('layouts.admin')

@section('title', 'สถิติรางวัล')

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

    .reward-icon {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }

    .reward-small {
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

    /* Badge styling */
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

    /* Badge styles with gradients */
    .bg-primary-soft {
        background-color: rgba(78, 115, 223, 0.1);
        color: #4e73df;
    }

    .bg-success-soft {
        background-color: rgba(28, 200, 138, 0.1);
        color: #1cc88a;
    }

    .bg-danger-soft {
        background-color: rgba(231, 74, 59, 0.1);
        color: #e74a3b;
    }

    .bg-warning-soft {
        background-color: rgba(246, 194, 62, 0.1);
        color: #f6c23e;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">สถิติและการวิเคราะห์รางวัล</h2>
        <div>
            <a href="{{ route('admin.rewards') }}" class="btn btn-primary">
                <i class="fas fa-gift me-1"></i> จัดการรางวัล
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
                            <h2 class="fs-1 fw-bold mb-0 text-primary">{{ number_format($totalRewards) }}</h2>
                            <p class="text-muted mb-0">รางวัลทั้งหมด</p>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-gift fa-2x text-primary"></i>
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
                            <h2 class="fs-1 fw-bold mb-0 text-success">{{ number_format($availableRewards) }}</h2>
                            <p class="text-muted mb-0">รางวัลที่มีของเหลืออยู่</p>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
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
                            <h2 class="fs-1 fw-bold mb-0 text-danger">{{ number_format($outOfStockRewards) }}</h2>
                            <p class="text-muted mb-0">รางวัลที่หมดแล้ว</p>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
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
                            <h2 class="fs-1 fw-bold mb-0 text-warning">{{ number_format($totalRedeems) }}</h2>
                            <p class="text-muted mb-0">จำนวนครั้งที่มีการแลกรางวัล</p>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-exchange-alt fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Monthly Redeem Trends -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-chart-line text-primary me-2"></i>แนวโน้มการแลกรางวัลรายเดือน
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="monthlyRedeemChart"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>เดือน</th>
                                    <th class="text-center">จำนวนการแลกรางวัล</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyRedeems as $redeem)
                                <tr>
                                    <td>{{ $redeem['month'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">{{ $redeem['count'] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Redeem Status Distribution -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>สถานะการแลกรางวัล
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="redeemStatusChart"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="row">
                        @foreach($redeemStatuses as $status)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    @if($status->status == 'pending')
                                        <i class="fas fa-clock text-warning"></i>
                                    @elseif($status->status == 'completed')
                                        <i class="fas fa-check-circle text-success"></i>
                                    @elseif($status->status == 'cancelled')
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @else
                                        <i class="fas fa-circle text-secondary"></i>
                                    @endif
                                </div>
                                <div>
                                    <strong>
                                        @if($status->status == 'pending')
                                            รอดำเนินการ
                                        @elseif($status->status == 'completed')
                                            เสร็จสิ้น
                                        @elseif($status->status == 'cancelled')
                                            ยกเลิก
                                        @else
                                            {{ $status->status }}
                                        @endif
                                    </strong>:
                                    <span class="text-muted">{{ $status->count }} รายการ</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Most Redeemed Rewards -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-trophy text-warning me-2"></i>รางวัลที่ได้รับความนิยมสูงสุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>รางวัล</th>
                                    <th class="text-center">คะแนนที่ใช้</th>
                                    <th class="text-center">จำนวนการแลก</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($topRedeemed->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-gift fa-3x mb-3"></i>
                                                <p>ยังไม่มีการแลกรางวัล</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($topRedeemed as $index => $reward)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    @if($reward->image_path)
                                                        <img src="{{ asset('storage/' . $reward->image_path) }}" class="reward-icon" alt="{{ $reward->name }}">
                                                    @else
                                                        <i class="fas fa-gift text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $reward->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-white rounded-pill">
                                                <i class="fas fa-coins me-1"></i> {{ number_format($reward->points_required) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success rounded-pill">{{ $reward->redeems_count }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Users -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-users text-primary me-2"></i>ผู้ใช้ที่แลกรางวัลบ่อยที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>ผู้ใช้</th>
                                    <th class="text-center">จำนวนครั้งที่แลก</th>
                                    <th class="text-end">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($topUsers->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <p>ยังไม่มีข้อมูลผู้ใช้</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($topUsers as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white user-avatar">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->username }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill py-2 px-3">
                                                <i class="fas fa-exchange-alt me-1"></i> {{ $user->redeem_count }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.users.show', $user->user_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i> ดูข้อมูลผู้ใช้
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Redeem History -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0 fw-bold">
                    <i class="fas fa-history text-primary me-2"></i>ประวัติการแลกรางวัลล่าสุด
                </h5>
                <a href="{{ route('admin.redeems') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                    <i class="fas fa-list me-1"></i> ดูประวัติทั้งหมด
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ผู้ใช้</th>
                            <th>รางวัล</th>
                            <th>คะแนนที่ใช้</th>
                            <th>สถานะ</th>
                            <th>วันที่แลก</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($recentRedeems->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-history fa-3x mb-3"></i>
                                        <p>ยังไม่มีประวัติการแลกรางวัล</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($recentRedeems as $redeem)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.show', $redeem->user->user_id) }}" class="text-decoration-none">
                                        {{ $redeem->user->username }}
                                    </a>
                                </td>
                                <td>{{ $redeem->reward->name }}</td>
                                <td>
                                    <span class="badge bg-warning text-white rounded-pill">
                                        <i class="fas fa-coins me-1"></i> {{ number_format($redeem->reward->points_required) }}
                                    </span>
                                </td>
                                <td>
                                    @if($redeem->status == 'pending')
                                        <span class="badge bg-warning text-dark">รอดำเนินการ</span>
                                    @elseif($redeem->status == 'completed')
                                        <span class="badge bg-success">เสร็จสิ้น</span>
                                    @elseif($redeem->status == 'cancelled')
                                        <span class="badge bg-danger">ยกเลิก</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $redeem->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ \Carbon\Carbon::parse($redeem->created_at)->format('d/m/Y H:i') }}">
                                        {{ \Carbon\Carbon::parse($redeem->created_at)->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Redeem Trends Chart
        const monthlyRedeemData = @json($monthlyRedeems);
        const monthlyRedeemCtx = document.getElementById('monthlyRedeemChart').getContext('2d');

        new Chart(monthlyRedeemCtx, {
            type: 'line',
            data: {
                labels: monthlyRedeemData.map(item => item.month),
                datasets: [{
                    label: 'จำนวนการแลกรางวัล',
                    data: monthlyRedeemData.map(item => item.count),
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

        // Redeem Status Distribution Chart
        const redeemStatusData = @json($redeemStatuses);
        const statusColors = {
            'pending': '#ffc107',
            'completed': '#28a745',
            'cancelled': '#dc3545',
        };

        const statusLabels = {
            'pending': 'รอดำเนินการ',
            'completed': 'เสร็จสิ้น',
            'cancelled': 'ยกเลิก',
        };

        const redeemStatusCtx = document.getElementById('redeemStatusChart').getContext('2d');
        new Chart(redeemStatusCtx, {
            type: 'doughnut',
            data: {
                labels: redeemStatusData.map(item => statusLabels[item.status] || item.status),
                datasets: [{
                    data: redeemStatusData.map(item => item.count),
                    backgroundColor: redeemStatusData.map(item => statusColors[item.status] || '#6c757d'),
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
    });
</script>
@endsection
