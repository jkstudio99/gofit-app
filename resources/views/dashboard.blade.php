@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="section-title mb-4">แดชบอร์ดการวิ่งของคุณ</h2>

            <!-- ปุ่มวิ่งที่เด่นชัด -->
            <div class="run-button-container mb-5">
                <div class="run-button-pulse"></div>
                <a href="{{ route('run.index') }}" class="run-button">
                    <div class="run-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <div class="run-text">เริ่มวิ่ง</div>
                </a>
            </div>

            <!-- Stats Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon text-primary">
                            <i class="fas fa-road"></i>
                        </div>
                        <div class="stat-value">{{ number_format($totalDistance, 1) }}</div>
                        <div class="stat-label">กิโลเมตร</div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon text-danger">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="stat-value">{{ number_format($totalCalories, 0) }}</div>
                        <div class="stat-label">แคลอรี่</div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon text-success">
                            <i class="fas fa-running"></i>
                        </div>
                        <div class="stat-value">{{ $totalActivities }}</div>
                        <div class="stat-label">กิจกรรมทั้งหมด</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Weekly Progress -->
                <div class="col-md-8 mb-4">
                    <div class="card gofit-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">ความคืบหน้ารายสัปดาห์</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span>เป้าหมายรายสัปดาห์ ({{ $weeklyGoal }} กม.)</span>
                                        <span>{{ number_format($weeklyDistance, 1) }} กม.</span>
                                    </div>
                                    <div class="gofit-progress">
                                        <div class="progress-bar" style="width: {{ $weeklyGoalProgress }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="weekly-stats">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>ระยะทางสัปดาห์นี้:</span>
                                            <span class="fw-bold">{{ number_format($weeklyDistance, 1) }} กม.</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>แคลอรี่สัปดาห์นี้:</span>
                                            <span class="fw-bold">{{ number_format($weeklyCalories, 0) }} kcal</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="weeklyChart" style="height: 150px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="col-md-4 mb-4">
                    <div class="card gofit-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">เหรียญตราล่าสุด</h5>
                            <a href="{{ route('badges.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">ดูทั้งหมด</a>
                        </div>
                        <div class="card-body">
                            @if($badges->count() > 0)
                                <div class="row">
                                    @foreach($badges->take(4) as $badge)
                                        <div class="col-6 text-center mb-3">
                                            <div class="badge-card">
                                                <div class="badge-icon">
                                                    <i class="fa {{ $badge->badge_icon ?? 'fa-medal' }}"></i>
                                                </div>
                                                <div class="badge-name">{{ $badge->badge_name }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center my-4">
                                    <div class="mb-3">
                                        <i class="fas fa-medal fa-3x text-muted"></i>
                                    </div>
                                    <p class="text-muted">คุณยังไม่ได้รับเหรียญตราใดๆ เริ่มวิ่งเพื่อรับเหรียญตราแรกของคุณ!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rewards Section -->
            <div class="row my-4">
                <div class="col-md-12">
                    <div class="card gofit-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">รางวัลของฉัน</h5>
                            <div>
                                <a href="{{ route('rewards.history') }}" class="btn btn-sm btn-outline-secondary rounded-pill me-2">
                                    <i class="fas fa-history me-1"></i> ประวัติการแลก
                                </a>
                                <a href="{{ route('rewards.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-gift me-1"></i> ดูทั้งหมด
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">คะแนนสะสมของคุณ</h6>
                                    <div class="d-flex align-items-center mt-2">
                                        <i class="fas fa-coins text-warning me-2 fa-2x"></i>
                                        <span class="fs-4 fw-bold">{{ Auth::user()->points ?? 0 }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('rewards.index') }}" class="btn btn-primary">
                                    <i class="fas fa-exchange-alt me-1"></i> แลกรางวัล
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card gofit-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">ประวัติการวิ่งทั้งหมด</h5>
                            <a href="{{ route('run.history') }}" class="btn btn-sm btn-outline-primary rounded-pill">ดูทั้งหมด</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="gofit-table">
                                    <thead>
                                        <tr>
                                            <th>วันที่</th>
                                            <th>ระยะทาง</th>
                                            <th>ระยะเวลา</th>
                                            <th>ความเร็วเฉลี่ย</th>
                                            <th>แคลอรี่</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentActivities as $activity)
                                            <tr>
                                                <td>{{ $activity->start_time instanceof \Carbon\Carbon
                                                    ? \Carbon\Carbon::parse($activity->start_time)->locale('th')->translatedFormat('d M Y, H:i')
                                                    : \Carbon\Carbon::parse($activity->start_time)->locale('th')->translatedFormat('d M Y, H:i') }}</td>
                                                <td>{{ number_format($activity->distance, 2) }} กม.</td>
                                                <td>
                                                    @if($activity->end_time)
                                                        {{ $activity->end_time instanceof \Carbon\Carbon && $activity->start_time instanceof \Carbon\Carbon
                                                            ? gmdate('H:i:s', $activity->end_time->diffInSeconds($activity->start_time))
                                                            : gmdate('H:i:s', \Carbon\Carbon::parse($activity->end_time)->diffInSeconds(\Carbon\Carbon::parse($activity->start_time)))
                                                        }}
                                                    @else
                                                        <span class="badge bg-warning">กำลังดำเนินการ</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($activity->average_speed, 1) }} กม./ชม.</td>
                                                <td>{{ number_format($activity->calories_burned) }} kcal</td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-primary show-run-map" data-id="{{ $activity->activity_id }}" data-route="{{ is_string($activity->route_gps_data) ? $activity->route_gps_data : json_encode($activity->route_gps_data) }}" data-distance="{{ $activity->distance }}" data-time="{{ $activity->end_time ? gmdate('H:i:s', strtotime($activity->end_time) - strtotime($activity->start_time)) : '00:00:00' }}" data-calories="{{ $activity->calories_burned }}">
                                                        <i class="fas fa-map-marked-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">ยังไม่มีกิจกรรมที่บันทึก</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal สรุปกิจกรรม -->
<div class="modal fade" id="activitySummaryModal" tabindex="-1" aria-labelledby="activitySummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activitySummaryModalLabel">สรุปการวิ่ง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="run-stat p-3 rounded bg-light">
                            <div class="fs-6 text-muted mb-1">ระยะทางทั้งหมด</div>
                            <div class="fs-3 fw-bold text-primary" id="summaryDistance">0.00 กม.</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="run-stat p-3 rounded bg-light">
                            <div class="fs-6 text-muted mb-1">เวลาทั้งหมด</div>
                            <div class="fs-3 fw-bold text-primary" id="summaryTime">00:00:00</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="run-stat p-3 rounded bg-light">
                            <div class="fs-6 text-muted mb-1">แคลอรี่ที่เผาผลาญ</div>
                            <div class="fs-3 fw-bold text-primary" id="summaryCalories">0 kcal</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>เส้นทางการวิ่ง</h6>
                    <div id="summaryMap" style="height: 300px; width: 100%; border-radius: var(--radius-md);" class="mb-4"></div>
                </div>
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
<style>
    .run-stat {
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-normal);
    }

    .run-stat:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    #summaryMap {
        height: 300px;
        width: 100%;
        border-radius: var(--radius-md);
        z-index: 1;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ตัวอย่างกราฟ
        var options = {
            series: [{
                name: 'ระยะทาง (กม.)',
                data: [2.5, 3.2, 0, 4.1, 1.8, 5.2, {{ $weeklyDistance }}]
            }],
            chart: {
                type: 'bar',
                height: 150,
                toolbar: {
                    show: false
                }
            },
            colors: ['#2DC679'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: ['จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส', 'อา'],
                labels: {
                    style: {
                        fontSize: '10px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return (parseFloat(value) || 0).toFixed(1);
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#weeklyChart"), options);
        chart.render();

        // เพิ่มฟังก์ชันสำหรับแสดงแผนที่จากประวัติการวิ่ง
        let summaryMap;

        // ดักจับการคลิกปุ่มแสดงแผนที่
        document.querySelectorAll('.show-run-map').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // ดึงข้อมูลจาก data attributes
                const routeData = this.getAttribute('data-route');
                const distance = this.getAttribute('data-distance');
                const time = this.getAttribute('data-time');
                const calories = this.getAttribute('data-calories');

                try {
                    // แปลงข้อมูลเส้นทางจาก JSON string เป็น array
                    const routeCoords = JSON.parse(routeData || '[]');

                    // แสดงข้อมูลสรุป
                    document.getElementById('summaryDistance').innerText = distance + " กม.";
                    document.getElementById('summaryTime').innerText = time;
                    document.getElementById('summaryCalories').innerText = calories + " kcal";

                    // สร้างแผนที่ด้วย Leaflet
                    initSummaryMap(routeCoords);

                    // แสดง modal
                    const summaryModal = new bootstrap.Modal(document.getElementById('activitySummaryModal'));
                    summaryModal.show();

                } catch (e) {
                    console.error("เกิดข้อผิดพลาดในการแสดงข้อมูลเส้นทาง:", e);
                    alert("ไม่สามารถแสดงข้อมูลเส้นทางได้");
                }
            });
        });

        function initSummaryMap(routeCoords) {
            // ลบแผนที่เก่าถ้ามี
            if (summaryMap) {
                summaryMap.remove();
            }

            // สร้างแผนที่ใหม่ด้วย Leaflet
            summaryMap = L.map('summaryMap').setView([13.7563, 100.5018], 13); // กรุงเทพฯ (ค่าเริ่มต้น)

            // เพิ่ม OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(summaryMap);

            // สร้างเส้นทางหากมีข้อมูล
            if (routeCoords && routeCoords.length > 0) {
                // แปลงรูปแบบข้อมูลสำหรับ Leaflet (Leaflet ใช้ [lat, lng] ในขณะที่ Google Maps ใช้ {lat, lng})
                const latLngs = routeCoords.map(coord => [coord.lat, coord.lng]);

                // สร้างเส้นทาง
                const polyline = L.polyline(latLngs, {
                    color: '#4CAF50',
                    weight: 4,
                    opacity: 1
                }).addTo(summaryMap);

                // เพิ่มมาร์คเกอร์จุดเริ่มต้นและจุดสิ้นสุด
                if (latLngs.length > 0) {
                    L.marker(latLngs[0]).addTo(summaryMap)
                        .bindPopup('<strong>จุดเริ่มต้น</strong>').openPopup();

                    L.marker(latLngs[latLngs.length - 1]).addTo(summaryMap)
                        .bindPopup('<strong>จุดสิ้นสุด</strong>');
                }

                // ปรับขอบเขตแผนที่ให้เห็นเส้นทางทั้งหมด
                summaryMap.fitBounds(polyline.getBounds());
            }

            // Resize map เมื่อ modal แสดงเสร็จสมบูรณ์
            document.getElementById('activitySummaryModal').addEventListener('shown.bs.modal', function() {
                summaryMap.invalidateSize();
            });
        }
    });
</script>
@endsection
