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
                                    <button class="btn btn-sm btn-outline-primary view-run-details"
                                        data-id="{{ $activity->run_id }}"
                                        data-distance="{{ number_format($activity->distance, 2) }}"
                                        data-time="@if($activity->duration){{ sprintf('%02d:%02d:%02d', floor($activity->duration / 3600), floor(($activity->duration % 3600) / 60), $activity->duration % 60) }}@else 00:00:00 @endif"
                                        data-calories="{{ number_format($activity->calories_burned, 0) }}"
                                        data-route="{{ json_encode($activity->route_data) }}">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </button>
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
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="text-muted small mb-2">
                                แสดงผล {{ $activities->firstItem() ?? 0 }} ถึง {{ $activities->lastItem() ?? 0 }} จากทั้งหมด {{ $activities->total() }} รายการ
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($activities->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $activities->previousPageUrl() }}" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @for ($i = 1; $i <= $activities->lastPage(); $i++)
                                        <li class="page-item {{ $i == $activities->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $activities->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($activities->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $activities->nextPageUrl() }}" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
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
        overflow: hidden;
    }

    .btn-outline-danger:hover i {
        color: white;
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }

    .page-item.active .page-link {
        background-color: #4CAF50;
        border-color: #4CAF50;
    }

    .page-link {
        color: #4CAF50;
    }

    .page-link:hover {
        color: #2E7D32;
        background-color: #e9f2ef;
    }

    .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
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
    let currentRouteData = null;

    // ตำแหน่งเริ่มต้น (กรุงเทพฯ)
    const defaultPosition = {
        lat: 13.736717,
        lng: 100.523186
    };

    // เพิ่มฟังก์ชันสำหรับแสดงข้อมูลการวิ่ง
    document.addEventListener('DOMContentLoaded', function() {
        const summaryModal = document.getElementById('activitySummaryModal');

        // เพิ่ม event listener เมื่อ modal แสดง
        summaryModal.addEventListener('shown.bs.modal', function () {
            console.log('Modal shown, rendering map');
            setTimeout(() => {
                if (summaryMap) {
                    summaryMap.remove();
                    summaryMap = null;
                }
                initSummaryMap(currentRouteData);
            }, 300);
        });

        // ดักจับการคลิกปุ่มแสดงรายละเอียด
        document.querySelectorAll('.view-run-details').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // ดึงข้อมูลจาก data attributes
                const routeData = this.getAttribute('data-route');
                const distance = this.getAttribute('data-distance');
                const time = this.getAttribute('data-time');
                const calories = this.getAttribute('data-calories');

                // กำหนดค่าตัวแปร global เพื่อใช้ในเหตุการณ์ shown.bs.modal
                currentRouteData = routeData;

                // แสดงข้อมูลสรุป
                document.getElementById('summaryDistance').innerText = distance + " กม.";
                document.getElementById('summaryTime').innerText = time;
                document.getElementById('summaryCalories').innerText = calories + " kcal";

                // แสดง modal
                const modal = new bootstrap.Modal(document.getElementById('activitySummaryModal'));
                modal.show();
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

        // แก้ไขการทำงานของ pagination ให้มี event listener
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.href;
            });
        });
    });

    function initSummaryMap(routeData) {
        console.log('Initializing summary map');

        // ลบแผนที่เก่าถ้ามี
        if (summaryMap) {
            summaryMap.remove();
            summaryMap = null;
        }

        try {
            // สร้างแผนที่ใหม่
            summaryMap = L.map('summaryMap').setView([defaultPosition.lat, defaultPosition.lng], 13);

            // เพิ่ม tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(summaryMap);

            // แปลง route data ให้อยู่ในรูปแบบที่ถูกต้อง
            let parsedRouteData;
            try {
                if (typeof routeData === 'string') {
                    parsedRouteData = JSON.parse(routeData || '[]');
                } else {
                    parsedRouteData = routeData || [];
                }
            } catch (e) {
                console.error('Error parsing route data JSON:', e);
                parsedRouteData = [];
            }

            // เตรียมข้อมูลเส้นทาง
            let points = [];

            // แปลงข้อมูลเป็นรูปแบบ [lat, lng]
            if (parsedRouteData.length > 0) {
                console.log('Route data format:', parsedRouteData[0]);
                if (typeof parsedRouteData[0] === 'object' && parsedRouteData[0].lat !== undefined) {
                    // กรณีข้อมูลอยู่ในรูปแบบ {lat, lng}
                    points = parsedRouteData.map(point => [point.lat, point.lng]);
                } else if (Array.isArray(parsedRouteData[0]) && parsedRouteData[0].length === 2) {
                    // กรณีข้อมูลอยู่ในรูปแบบ [lat, lng] อยู่แล้ว
                    points = parsedRouteData;
                }
            }

            if (points && points.length > 0) {
                // สร้างเส้นทาง
                const polyline = L.polyline(points, {
                    color: 'blue',
                    weight: 5,
                    opacity: 0.7
                }).addTo(summaryMap);

                // เพิ่มมาร์กเกอร์จุดเริ่มต้นและจุดสิ้นสุด
                const startPoint = points[0];
                const endPoint = points[points.length - 1];

                L.marker(startPoint, {
                    icon: L.divIcon({
                        className: 'location-pin',
                        html: '<i class="fas fa-play-circle text-success" style="font-size: 24px;"></i>',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    })
                }).addTo(summaryMap);

                L.marker(endPoint, {
                    icon: L.divIcon({
                        className: 'location-pin',
                        html: '<i class="fas fa-flag-checkered text-danger" style="font-size: 24px;"></i>',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    })
                }).addTo(summaryMap);

                // ปรับขอบเขตแผนที่ให้เห็นเส้นทางทั้งหมด
                summaryMap.fitBounds(polyline.getBounds(), {
                    padding: [50, 50],
                    maxZoom: 16
                });
            } else {
                // ถ้าไม่มีข้อมูลเส้นทาง แสดงตำแหน่งเริ่มต้น
                L.marker([defaultPosition.lat, defaultPosition.lng], {
                    icon: L.divIcon({
                        className: 'location-pin',
                        html: '<i class="fas fa-running text-primary" style="font-size: 24px;"></i>',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    })
                }).addTo(summaryMap)
                    .bindPopup('<strong>ไม่มีข้อมูลเส้นทาง</strong>').openPopup();
            }
        } catch (error) {
            console.error('Error creating summary map:', error);
        }

        // Resize map เมื่อ modal แสดงเสร็จสมบูรณ์
        setTimeout(() => {
            if (summaryMap) {
                summaryMap.invalidateSize();
            }
        }, 200);
    }
</script>
@endsection
