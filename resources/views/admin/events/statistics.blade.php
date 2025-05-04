@extends('layouts.admin')

@section('title', 'สถิติกิจกรรม')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
    /* Card Design */
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
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">สถิติและการวิเคราะห์กิจกรรม</h2>
        <div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-list me-1"></i> รายการกิจกรรม
            </a>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> สร้างกิจกรรมใหม่
            </a>
        </div>
    </div>

    <!-- ข้อมูลสรุปสำคัญ -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-1 fw-bold mb-0 text-primary">{{ $totalEvents }}</h2>
                            <p class="text-muted mb-0">กิจกรรมทั้งหมด</p>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-1 fw-bold mb-0 text-success">{{ $totalParticipants }}</h2>
                            <p class="text-muted mb-0">ผู้เข้าร่วมทั้งหมด</p>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-1 fw-bold mb-0 text-info">{{ $totalAttended }}</h2>
                            <p class="text-muted mb-0">เช็คอินแล้ว</p>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-check-circle fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-1 fw-bold mb-0 text-warning">{{ $completionRate }}%</h2>
                            <p class="text-muted mb-0">อัตราการเข้าร่วม</p>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-0 icon-container">
                            <i class="fas fa-percentage fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- กิจกรรมตามสถานะ -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>กิจกรรมตามสถานะ
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="eventStatusChart" height="200"></canvas>

                    <div class="mt-4">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>สถานะ</th>
                                        <th class="text-end">จำนวน</th>
                                        <th class="text-end">สัดส่วน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $statuses = [
                                            'published' => 'เผยแพร่แล้ว',
                                            'draft' => 'ฉบับร่าง',
                                            'cancelled' => 'ยกเลิกแล้ว'
                                        ];
                                    @endphp

                                    @foreach($eventsByStatus as $stat)
                                        <tr>
                                            <td>
                                                @if(isset($statuses[$stat->status]))
                                                    {{ $statuses[$stat->status] }}
                                                @else
                                                    {{ $stat->status }}
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $stat->count }}</td>
                                            <td class="text-end">{{ number_format(($stat->count / $totalEvents) * 100, 1) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- สถิติรายเดือน -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">
                        <i class="fas fa-chart-bar text-primary me-2"></i>สถิติรายเดือน
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyStatsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- กิจกรรมยอดนิยม -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">
                        <i class="fas fa-star text-warning me-2"></i>กิจกรรมยอดนิยม
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>กิจกรรม</th>
                                    <th>สถานที่</th>
                                    <th class="text-end">ผู้เข้าร่วม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostPopularEvents as $event)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.events.show', $event) }}" class="text-decoration-none">
                                                {{ $event->title }}
                                            </a>
                                        </td>
                                        <td>{{ $event->location }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-success rounded-pill">{{ $event->registered_count }}</span>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($mostPopularEvents->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center py-3">ไม่มีข้อมูลกิจกรรม</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- กิจกรรมที่กำลังจะมาถึง -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100 rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">
                        <i class="fas fa-calendar-alt text-info me-2"></i>กิจกรรมที่กำลังจะมาถึง
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>กิจกรรม</th>
                                    <th>วันที่</th>
                                    <th class="text-end">จำนวนที่รับ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingEvents as $event)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.events.show', $event) }}" class="text-decoration-none">
                                                {{ $event->title }}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">{{ $event->max_participants > 0 ? $event->max_participants : 'ไม่จำกัด' }}</td>
                                    </tr>
                                @endforeach

                                @if($upcomingEvents->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center py-3">ไม่มีกิจกรรมที่กำลังจะมาถึง</td>
                                    </tr>
                                @endif
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
        // Event Status Chart
        var statusCtx = document.getElementById('eventStatusChart').getContext('2d');
        var statusData = @json($eventsByStatus);

        var labels = [];
        var counts = [];
        var bgColors = ['#4e73df', '#f6c23e', '#e74a3b', '#36b9cc', '#1cc88a'];

        statusData.forEach(function(item, index) {
            var label = '';
            switch(item.status) {
                case 'published':
                    label = 'เผยแพร่แล้ว';
                    break;
                case 'draft':
                    label = 'ฉบับร่าง';
                    break;
                case 'cancelled':
                    label = 'ยกเลิกแล้ว';
                    break;
                default:
                    label = item.status;
            }

            labels.push(label);
            counts.push(item.count);
        });

        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: bgColors,
                    hoverBackgroundColor: bgColors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
                cutout: '0%',
            },
        });

        // Monthly Stats Chart
        var monthlyCtx = document.getElementById('monthlyStatsChart').getContext('2d');
        var monthlyData = @json($monthlyStats);

        var months = [];
        var eventCounts = [];
        var participantCounts = [];

        monthlyData.forEach(function(item) {
            months.push(item.month);
            eventCounts.push(item.event_count);
            participantCounts.push(item.participant_count);
        });

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'กิจกรรม',
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        data: eventCounts,
                    },
                    {
                        label: 'ผู้เข้าร่วม',
                        backgroundColor: 'rgba(28, 200, 138, 0.8)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        data: participantCounts,
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
