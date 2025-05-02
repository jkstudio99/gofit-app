@extends('layouts.admin')

@section('title', 'สถิติกิจกรรม')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">สถิติและการวิเคราะห์กิจกรรม</h1>
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
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fs-4 fw-bold">{{ $totalEvents }}</h2>
                        <p class="text-muted mb-0">กิจกรรมทั้งหมด</p>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-calendar-alt text-primary fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fs-4 fw-bold">{{ $totalParticipants }}</h2>
                        <p class="text-muted mb-0">ผู้เข้าร่วมทั้งหมด</p>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-users text-success fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fs-4 fw-bold">{{ $totalAttended }}</h2>
                        <p class="text-muted mb-0">เช็คอินแล้ว</p>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="fas fa-check-circle text-info fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fs-4 fw-bold">{{ $completionRate }}%</h2>
                        <p class="text-muted mb-0">อัตราการเข้าร่วม</p>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-percentage text-warning fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- กิจกรรมตามสถานะ -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 fw-bold">กิจกรรมตามสถานะ</h5>
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
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 fw-bold">สถิติรายเดือน</h5>
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
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 fw-bold">กิจกรรมยอดนิยม</h5>
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
                                        <span class="badge bg-success">{{ $event->registered_count }}</span>
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
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 fw-bold">กิจกรรมที่กำลังจะมาถึง</h5>
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
