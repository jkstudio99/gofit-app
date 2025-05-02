@extends('layouts.app')

@section('title', 'รายละเอียดการวิ่ง')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card gofit-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">รายละเอียดการวิ่ง</h5>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary rounded-pill me-2">
                            <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
                        </a>
                        <a href="{{ route('run.history') }}" class="btn btn-sm btn-primary rounded-pill">
                            <i class="fas fa-history me-1"></i> ประวัติการวิ่ง
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div id="runMap" style="height: 400px; width: 100%; border-radius: var(--radius-lg);"></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="run-stat p-3 rounded bg-light">
                                <div class="fs-5 text-muted mb-2">ระยะทาง</div>
                                <div class="fs-2 fw-bold text-primary">{{ number_format($run->distance, 2) }}</div>
                                <div class="small">กิโลเมตร</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="run-stat p-3 rounded bg-light">
                                <div class="fs-5 text-muted mb-2">เวลา</div>
                                <div class="fs-2 fw-bold text-primary">
                                    @php
                                        $hours = floor($run->duration / 3600);
                                        $minutes = floor(($run->duration % 3600) / 60);
                                        $seconds = $run->duration % 60;
                                        echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                    @endphp
                                </div>
                                <div class="small">ชั่วโมง:นาที:วินาที</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="run-stat p-3 rounded bg-light">
                                <div class="fs-5 text-muted mb-2">ความเร็วเฉลี่ย</div>
                                <div class="fs-2 fw-bold text-primary">{{ number_format($run->average_speed, 1) }}</div>
                                <div class="small">กม./ชม.</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="run-stat p-3 rounded bg-light">
                                <div class="fs-5 text-muted mb-2">แคลอรี่</div>
                                <div class="fs-2 fw-bold text-primary">{{ number_format($run->calories_burned) }}</div>
                                <div class="small">kcal</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> ข้อมูลการวิ่ง</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>วันที่</strong></td>
                                            <td>{{ $run->created_at instanceof \Carbon\Carbon
                                                  ? $run->created_at->formatThaiDate(false)
                                                  : \Carbon\Carbon::parse($run->created_at)->formatThaiDate(false) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>เวลาเริ่มต้น</strong></td>
                                            <td>{{ $run->start_time instanceof \Carbon\Carbon
                                                  ? $run->start_time->format('H:i:s') . ' น.'
                                                  : \Carbon\Carbon::parse($run->start_time)->format('H:i:s') . ' น.' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>เวลาสิ้นสุด</strong></td>
                                            <td>{{ $run->end_time instanceof \Carbon\Carbon
                                                  ? $run->end_time->format('H:i:s') . ' น.'
                                                  : \Carbon\Carbon::parse($run->end_time)->format('H:i:s') . ' น.' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>เพซ</strong></td>
                                            <td>{{ $run->formatted_pace ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>บันทึก</strong></td>
                                            <td>{{ $run->notes ?? 'ไม่มีบันทึก' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i> กราฟความเร็ว</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="speedChart" style="width: 100%; height: 200px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-share-alt me-2"></i> แชร์การวิ่ง</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">แชร์รายละเอียดการวิ่งและเส้นทางกับเพื่อนของคุณ</p>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shareRunModal">
                                            <i class="fas fa-share-alt me-1"></i> แชร์การวิ่ง
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal แชร์การวิ่ง -->
<div class="modal fade" id="shareRunModal" tabindex="-1" aria-labelledby="shareRunModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareRunModalLabel">แชร์การวิ่ง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="shareRunForm">
                    <input type="hidden" id="runIdToShare" name="run_id" value="{{ $run->run_id }}">

                    <div class="mb-3">
                        <label for="shareWithUser" class="form-label">แชร์กับเพื่อน <span class="text-danger">*</span></label>
                        <select class="form-select" id="shareWithUser" name="user_id" required>
                            <option value="">เลือกเพื่อน</option>
                            <!-- ตัวอย่างเพื่อน - ในการใช้งานจริงจะดึงจากฐานข้อมูล -->
                            <option value="1">user1</option>
                            <option value="2">user2</option>
                            <option value="3">user3</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="shareMessage" class="form-label">ข้อความ</label>
                        <textarea class="form-control" id="shareMessage" name="message" rows="3" placeholder="เพิ่มข้อความที่ต้องการส่งถึงเพื่อน..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="confirmShareBtn">แชร์</button>
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

    #runMap {
        position: relative;
        z-index: 1;
    }

    .location-pin {
        transition: all 0.3s ease;
    }

    .location-pin:hover {
        transform: scale(1.2);
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // แสดงแผนที่เส้นทางการวิ่ง
        const routeData = @json($run->route_data ?? []);
        const runMap = L.map('runMap').setView([13.736717, 100.523186], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(runMap);

        if (routeData && routeData.length > 0) {
            let points = [];

            try {
                // Handle different formats of route data
                if (typeof routeData[0] === 'object' && routeData[0].lat !== undefined) {
                    points = routeData.map(point => [point.lat, point.lng]);
                } else if (Array.isArray(routeData[0]) && routeData[0].length === 2) {
                    points = routeData;
                }

                if (points.length > 0) {
                    const polyline = L.polyline(points, {
                        color: 'blue',
                        weight: 5,
                        opacity: 0.7
                    }).addTo(runMap);

                    runMap.fitBounds(polyline.getBounds());

                    // เพิ่มมาร์กเกอร์จุดเริ่มต้นและจุดสิ้นสุด
                    L.marker(points[0], {
                        icon: L.divIcon({
                            className: 'location-pin',
                            html: '<i class="fas fa-play-circle text-success" style="font-size: 24px;"></i>',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        })
                    }).addTo(runMap);

                    L.marker(points[points.length - 1], {
                        icon: L.divIcon({
                            className: 'location-pin',
                            html: '<i class="fas fa-flag-checkered text-danger" style="font-size: 24px;"></i>',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        })
                    }).addTo(runMap);
                }
            } catch (error) {
                console.error('Error processing route data:', error);
            }
        }

        // สร้างกราฟความเร็ว
        let speedData = [];
        let labels = [];

        if (routeData && routeData.length > 0) {
            // คำนวณความเร็วทุก 500 เมตร
            const segmentDistance = 0.5; // 500 เมตร
            let currentDistance = 0;
            let lastTimestamp = routeData[0].timestamp;
            let segmentStartPoint = routeData[0];
            let segmentStartDistance = 0;

            for (let i = 1; i < routeData.length; i++) {
                const point = routeData[i];
                const prevPoint = routeData[i-1];

                const segment = calculateDistance(
                    prevPoint.lat, prevPoint.lng,
                    point.lat, point.lng
                );

                currentDistance += segment;

                if (currentDistance - segmentStartDistance >= segmentDistance) {
                    // คำนวณความเร็วสำหรับช่วงนี้ (กม./ชม.)
                    const timeElapsed = (point.timestamp - segmentStartPoint.timestamp) / 3600; // แปลงเป็นชั่วโมง
                    const distanceCovered = currentDistance - segmentStartDistance;
                    const speed = timeElapsed > 0 ? distanceCovered / timeElapsed : 0;

                    speedData.push(speed);
                    labels.push(currentDistance.toFixed(1) + ' กม.');

                    segmentStartPoint = point;
                    segmentStartDistance = currentDistance;
                }
            }
        }

        // สร้างกราฟ
        const ctx = document.getElementById('speedChart').getContext('2d');
        const speedChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'ความเร็ว (กม./ชม.)',
                    data: speedData,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'ความเร็ว (กม./ชม.)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'ระยะทาง'
                        }
                    }
                }
            }
        });

        // ฟังก์ชันคำนวณระยะทางระหว่างสองจุด (กิโลเมตร) โดยใช้ Haversine formula
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // รัศมีของโลกในกิโลเมตร
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;

            const a =
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const distance = R * c; // ระยะทางในกิโลเมตร

            return distance;
        }

        // แชร์การวิ่ง
        document.getElementById('confirmShareBtn').addEventListener('click', function() {
            const form = document.getElementById('shareRunForm');
            const formData = new FormData(form);
            const shareData = {};

            formData.forEach((value, key) => {
                shareData[key] = value;
            });

            fetch('{{ url("/run/share") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(shareData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('แชร์การวิ่งสำเร็จ');

                    // ปิดโมดัล
                    const modal = bootstrap.Modal.getInstance(document.getElementById('shareRunModal'));
                    modal.hide();
                }
            })
            .catch(error => {
                console.error('Error sharing run:', error);
                alert('เกิดข้อผิดพลาดในการแชร์การวิ่ง');
            });
        });
    });
</script>
@endsection
