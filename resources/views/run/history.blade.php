@extends('layouts.app')

@section('title', 'ประวัติการวิ่ง')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card gofit-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ประวัติการวิ่งทั้งหมด</h5>
                    <div>
                        <a href="{{ route('run.index') }}" class="btn btn-sm btn-primary rounded-pill">
                            <i class="fas fa-running me-1"></i> กลับไปหน้าวิ่ง
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="row p-3 mb-3">
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <div class="fs-2 text-primary fw-bold">{{ $activities->count() }}</div>
                                <div class="text-muted">กิจกรรมทั้งหมด</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <div class="fs-2 text-primary fw-bold">{{ number_format($totalDistance, 2) }}</div>
                                <div class="text-muted">กิโลเมตรสะสม</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <div class="fs-2 text-primary fw-bold">{{ number_format($totalCalories, 0) }}</div>
                                <div class="text-muted">แคลอรี่สะสม</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <div class="fs-2 text-primary fw-bold">{{ $totalTime }}</div>
                                <div class="text-muted">เวลาสะสม (ชม.)</div>
                            </div>
                        </div>
                    </div>

                    <table class="gofit-table">
                        <thead>
                            <tr>
                                <th>วันที่</th>
                                <th>ระยะทาง</th>
                                <th>เวลา</th>
                                <th>ความเร็วเฉลี่ย</th>
                                <th>แคลอรี่</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                            <tr>
                                <td>{{ $activity->start_time instanceof \Carbon\Carbon
                                      ? $activity->start_time->format('d M Y, H:i')
                                      : \Carbon\Carbon::parse($activity->start_time)->format('d M Y, H:i') }}</td>
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
                                <td>{{ number_format($activity->calories_burned, 0) }} kcal</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary show-run-map" data-id="{{ $activity->id }}" data-route="{{ $activity->route_gps_data }}" data-distance="{{ $activity->distance }}" data-time="{{ $activity->end_time ? gmdate('H:i:s', strtotime($activity->end_time) - strtotime($activity->start_time)) : '00:00:00' }}" data-calories="{{ $activity->calories_burned }}">
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

                    <div class="p-3">
                        {{ $activities->links() }}
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
</style>
@endsection

@section('scripts')
<!-- Bootstrap JS Bundle (Popper included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry" async defer></script>
<script>
    let summaryMap;

    // เพิ่มฟังก์ชันสำหรับแสดงแผนที่จากประวัติการวิ่ง
    document.addEventListener('DOMContentLoaded', function() {
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

                    // สร้างแผนที่
                    if (!window.google || !google.maps) {
                        console.log("Google Maps ยังโหลดไม่เสร็จ กำลังรอ...");
                        // รอให้ Google Maps API โหลดเสร็จ
                        const waitForMaps = setInterval(() => {
                            if (window.google && google.maps) {
                                clearInterval(waitForMaps);
                                initSummaryMap(routeCoords);
                            }
                        }, 500);
                    } else {
                        initSummaryMap(routeCoords);
                    }

                    // แสดง modal
                    const summaryModal = new bootstrap.Modal(document.getElementById('activitySummaryModal'));
                    summaryModal.show();

                } catch (e) {
                    console.error("เกิดข้อผิดพลาดในการแสดงข้อมูลเส้นทาง:", e);
                    alert("ไม่สามารถแสดงข้อมูลเส้นทางได้");
                }
            });
        });
    });

    function initSummaryMap(routeCoords) {
        // สร้างแผนที่ใหม่สำหรับประวัติ
        summaryMap = new google.maps.Map(document.getElementById("summaryMap"), {
            zoom: 14,
            center: { lat: 13.7563, lng: 100.5018 }, // กรุงเทพฯ (ค่าเริ่มต้น)
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            fullscreenControl: false,
            streetViewControl: false
        });

        // สร้างเส้นทางหากมีข้อมูล
        if (routeCoords && routeCoords.length > 0) {
            // สร้างเส้นทาง
            new google.maps.Polyline({
                path: routeCoords,
                geodesic: true,
                strokeColor: "#4CAF50",
                strokeOpacity: 1.0,
                strokeWeight: 4,
                map: summaryMap
            });

            // ปรับขอบเขตแผนที่ให้เห็นเส้นทางทั้งหมด
            const bounds = new google.maps.LatLngBounds();
            for (const coord of routeCoords) {
                bounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
            }
            summaryMap.fitBounds(bounds);
        }
    }
</script>
@endsection
