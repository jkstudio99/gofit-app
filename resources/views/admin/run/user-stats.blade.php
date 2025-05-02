@extends('layouts.admin')

@section('title', 'สถิติการวิ่งของผู้ใช้')

@section('content')
    <h1 class="mb-4">สถิติการวิ่งของผู้ใช้: {{ $user->username }}</h1>

    <!-- ข้อมูลสถิติผู้ใช้ -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card gofit-card bg-primary bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ $userStats['total_runs'] ?? 0 }}</h3>
                            <p class="mb-0">จำนวนการวิ่ง</p>
                        </div>
                        <div>
                            <i class="fas fa-running fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card gofit-card bg-success bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ number_format($userStats['total_distance'] ?? 0, 2) }}</h3>
                            <p class="mb-0">ระยะทางรวม (กม.)</p>
                        </div>
                        <div>
                            <i class="fas fa-road fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card gofit-card bg-warning bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ number_format($userStats['total_calories'] ?? 0) }}</h3>
                            <p class="mb-0">แคลอรี่รวม</p>
                        </div>
                        <div>
                            <i class="fas fa-fire fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card gofit-card bg-danger bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold fs-2">{{ $userStats['total_duration'] ?? '0 ชม. 0 นาที' }}</h3>
                            <p class="mb-0">เวลาวิ่งรวม</p>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- กราฟสถิติการวิ่ง -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card gofit-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        สถิติการวิ่งรายเดือน
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyRunChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ประวัติการวิ่ง -->
    <div class="card gofit-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                ประวัติการวิ่งทั้งหมด
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>ระยะทาง</th>
                            <th>ระยะเวลา</th>
                            <th>ความเร็วเฉลี่ย</th>
                            <th>แคลอรี่</th>
                            <th>เส้นทาง</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userRuns as $run)
                        <tr>
                            <td>{{ $run->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($run->distance, 2) }} กม.</td>
                            <td>
                                @php
                                    $hours = floor($run->duration / 3600);
                                    $minutes = floor(($run->duration % 3600) / 60);
                                    $seconds = $run->duration % 60;
                                    echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                @endphp
                            </td>
                            <td>{{ number_format($run->average_speed, 1) }} กม./ชม.</td>
                            <td>{{ number_format($run->calories_burned) }} kcal</td>
                            <td>
                                <button class="btn btn-sm btn-primary view-route"
                                    data-run-id="{{ $run->run_id }}"
                                    data-route="{{ json_encode($run->route_data) }}">
                                    <i class="fas fa-map-marked-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $userRuns->links() }}
            </div>
        </div>
    </div>

    <!-- Modal แสดงเส้นทาง -->
    <div class="modal fade" id="routeModal" tabindex="-1" aria-labelledby="routeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="routeModalLabel">เส้นทางการวิ่ง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="routeMap" style="height: 400px; width: 100%; border-radius: var(--radius-md);"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="" />
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // สร้างกราฟสถิติการวิ่งรายเดือน
        var ctx = document.getElementById('monthlyRunChart').getContext('2d');

        // สร้างข้อมูลสำหรับกราฟ
        var monthlyStats = @json($monthlyStats ?? []);
        var labels = [];
        var runCounts = [];
        var distances = [];

        monthlyStats.forEach(function(stat) {
            // สร้างป้ายชื่อเดือน/ปี
            var date = new Date(stat.year, stat.month - 1);
            var monthName = new Intl.DateTimeFormat('th-TH', { month: 'short' }).format(date);
            labels.push(monthName + ' ' + stat.year);

            runCounts.push(stat.count);
            distances.push(parseFloat(stat.distance).toFixed(2));
        });

        // กลับลำดับข้อมูลเพื่อให้แสดงเรียงตามเวลา
        labels.reverse();
        runCounts.reverse();
        distances.reverse();

        var monthlyRunChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'จำนวนครั้ง',
                        data: runCounts,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'ระยะทาง (กม.)',
                        data: distances,
                        type: 'line',
                        fill: false,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'จำนวนครั้ง'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'ระยะทาง (กม.)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // จัดการแสดงเส้นทางการวิ่ง
        var routeModal = document.getElementById('routeModal');
        var routeMap = null;

        document.querySelectorAll('.view-route').forEach(function(button) {
            button.addEventListener('click', function() {
                var runId = this.getAttribute('data-run-id');
                var routeData = JSON.parse(this.getAttribute('data-route') || '[]');

                // เปิด Modal
                var modal = new bootstrap.Modal(routeModal);
                modal.show();

                // สร้างแผนที่เมื่อ Modal เปิด
                routeModal.addEventListener('shown.bs.modal', function() {
                    if (routeMap !== null) {
                        routeMap.remove();
                    }

                    routeMap = L.map('routeMap').setView([13.736717, 100.523186], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(routeMap);

                    if (routeData && routeData.length > 0) {
                        var points = routeData.map(point => [point.lat, point.lng]);

                        var polyline = L.polyline(points, {
                            color: 'blue',
                            weight: 5,
                            opacity: 0.7
                        }).addTo(routeMap);

                        routeMap.fitBounds(polyline.getBounds());

                        // เพิ่มมาร์กเกอร์จุดเริ่มต้นและจุดสิ้นสุด
                        L.marker(points[0], {
                            icon: L.divIcon({
                                className: 'location-pin',
                                html: '<i class="fas fa-play-circle text-success" style="font-size: 24px;"></i>',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            })
                        }).addTo(routeMap);

                        L.marker(points[points.length - 1], {
                            icon: L.divIcon({
                                className: 'location-pin',
                                html: '<i class="fas fa-flag-checkered text-danger" style="font-size: 24px;"></i>',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            })
                        }).addTo(routeMap);
                    }
                }, { once: true });
            });
        });
    });
</script>
@endsection
