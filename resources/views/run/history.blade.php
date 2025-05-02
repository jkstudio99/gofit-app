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
                                      ? $activity->start_time->formatThaiDate()
                                      : \Carbon\Carbon::parse($activity->start_time)->formatThaiDate() }}</td>
                                <td>{{ number_format($activity->distance, 2) }} กม.</td>
                                <td>
                                    @if($activity->duration)
                                        @php
                                            $hours = floor($activity->duration / 3600);
                                            $minutes = floor(($activity->duration % 3600) / 60);
                                            $seconds = $activity->duration % 60;
                                            echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                        @endphp
                                    @else
                                        00:00:00
                                    @endif
                                </td>
                                <td>{{ number_format($activity->average_speed, 1) }} กม./ชม.</td>
                                <td>{{ number_format($activity->calories_burned, 0) }} kcal</td>
                                <td>
                                    <a href="{{ route('run.show', ['id' => $activity->run_id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </a>
                                    @if($activity->is_completed)
                                    <a href="#" class="btn btn-sm btn-outline-success share-run" data-id="{{ $activity->run_id }}">
                                        <i class="fas fa-share-alt"></i>
                                    </a>
                                    @endif
                                    <button class="btn btn-sm btn-outline-danger delete-run" data-id="{{ $activity->run_id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
<!-- Bootstrap JS Bundle (Popper included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
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

        // เพิ่ม event listener สำหรับปุ่มลบ
        const deleteButtons = document.querySelectorAll('.delete-run');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const runId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'ยืนยันการลบ',
                    text: "คุณต้องการลบประวัติการวิ่งนี้ใช่หรือไม่?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ยืนยันการลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ส่งคำขอไปยัง endpoint สำหรับลบ
                        fetch('{{ route("run.destroy") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ run_id: runId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'ลบแล้ว!',
                                    'ประวัติการวิ่งถูกลบเรียบร้อยแล้ว',
                                    'success'
                                ).then(() => {
                                    // รีโหลดหน้าเพื่ออัปเดตรายการ
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'เกิดข้อผิดพลาด!',
                                    data.message || 'ไม่สามารถลบประวัติการวิ่งได้',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                'ไม่สามารถลบประวัติการวิ่งได้ โปรดลองอีกครั้ง',
                                'error'
                            );
                        });
                    }
                });
            });
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
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(summaryMap);

        // สร้างเส้นทางหากมีข้อมูล
        if (routeCoords && routeCoords.length > 0) {
            // จัดการกับรูปแบบข้อมูลที่อาจแตกต่างกัน
            let latLngs;
            if (typeof routeCoords[0] === 'object' && routeCoords[0].lat !== undefined) {
                // แปลงรูปแบบข้อมูลสำหรับ Leaflet (Leaflet ใช้ [lat, lng])
                latLngs = routeCoords.map(coord => [coord.lat, coord.lng]);
            } else if (Array.isArray(routeCoords[0]) && routeCoords[0].length === 2) {
                latLngs = routeCoords;
            } else {
                // ถ้ารูปแบบไม่ตรง
                latLngs = [[13.7563, 100.5018]]; // ใช้ตำแหน่งเริ่มต้น
            }

            // สร้างเส้นทาง
            const polyline = L.polyline(latLngs, {
                color: '#4CAF50',
                weight: 4,
                opacity: 1
            }).addTo(summaryMap);

            // เพิ่มมาร์คเกอร์จุดเริ่มต้นและจุดสิ้นสุด
            if (latLngs.length > 0) {
                L.marker(latLngs[0], {
                    icon: L.divIcon({
                        className: 'location-pin',
                        html: '<i class="fas fa-play-circle text-success" style="font-size: 24px;"></i>',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    })
                }).addTo(summaryMap)
                    .bindPopup('<strong>จุดเริ่มต้น</strong>').openPopup();

                L.marker(latLngs[latLngs.length - 1], {
                    icon: L.divIcon({
                        className: 'location-pin',
                        html: '<i class="fas fa-flag-checkered text-danger" style="font-size: 24px;"></i>',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    })
                }).addTo(summaryMap)
                    .bindPopup('<strong>จุดสิ้นสุด</strong>');
            }

            // ปรับขอบเขตแผนที่ให้เห็นเส้นทางทั้งหมด
            summaryMap.fitBounds(polyline.getBounds(), {
                padding: [50, 50],
                maxZoom: 16
            });
        }

        // Resize map เมื่อ modal แสดงเสร็จสมบูรณ์
        setTimeout(() => {
            summaryMap.invalidateSize();
        }, 100);
    }
</script>
@endsection
