@extends('layouts.app')

@section('title', 'เริ่มวิ่ง')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card gofit-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">บันทึกกิจกรรมการวิ่ง</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <!-- แผนที่สำหรับแสดงการวิ่ง -->
                        <div class="col-12">
                            <div id="map" style="height: 400px; width: 100%; border-radius: var(--radius-lg);" class="mb-4"></div>
                        </div>
                    </div>

                    <!-- ข้อมูลการวิ่ง -->
                    <div class="row text-center mb-4">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="run-stat p-3 rounded bg-light">
                                <div class="fs-5 text-muted mb-2">ระยะทาง</div>
                                <div class="fs-2 fw-bold text-primary" id="distance">0.00</div>
                                <div class="small">กิโลเมตร</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="run-stat p-3 rounded bg-light">
                                <div class="fs-5 text-muted mb-2">เวลา</div>
                                <div class="fs-2 fw-bold text-primary" id="time">00:00:00</div>
                                <div class="small">ชั่วโมง:นาที:วินาที</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="run-stat p-3 rounded bg-light">
                                <div class="fs-5 text-muted mb-2">ความเร็ว</div>
                                <div class="fs-2 fw-bold text-primary" id="speed">0.0</div>
                                <div class="small">กม./ชม.</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="run-stat p-3 rounded bg-light">
                                <div class="fs-5 text-muted mb-2">แคลอรี่</div>
                                <div class="fs-2 fw-bold text-primary" id="calories">0</div>
                                <div class="small">kcal</div>
                            </div>
                        </div>
                    </div>

                    <!-- ปุ่มควบคุม -->
                    <div class="run-controls text-center">
                        <button type="button" id="startRunBtn" class="btn btn-primary btn-lg px-5 py-3 me-2">
                            <i class="fas fa-play me-2"></i> เริ่มวิ่ง
                        </button>
                        <button type="button" id="pauseRunBtn" class="btn btn-warning btn-lg px-5 py-3 me-2" disabled>
                            <i class="fas fa-pause me-2"></i> พัก
                        </button>
                        <button type="button" id="stopRunBtn" class="btn btn-danger btn-lg px-5 py-3" disabled>
                            <i class="fas fa-stop me-2"></i> หยุดวิ่ง
                        </button>
                    </div>
                </div>
            </div>

            <!-- ประวัติการวิ่งล่าสุด -->
            <div class="card gofit-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ประวัติการวิ่งล่าสุด</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary rounded-pill">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
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
                            @forelse($recentActivities ?? [] as $activity)
                            <tr>
                                <td>{{ $activity->start_time->format('d M Y, H:i') }}</td>
                                <td>{{ number_format($activity->distance, 2) }} กม.</td>
                                <td>
                                    @if($activity->end_time)
                                        {{ gmdate('H:i:s', $activity->end_time->diffInSeconds($activity->start_time)) }}
                                    @else
                                        <span class="badge bg-warning">กำลังดำเนินการ</span>
                                    @endif
                                </td>
                                <td>{{ number_format($activity->average_speed, 1) }} กม./ชม.</td>
                                <td>{{ number_format($activity->calories_burned, 0) }} kcal</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
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

<!-- Modal สรุปกิจกรรม -->
<div class="modal fade" id="activitySummaryModal" tabindex="-1" aria-labelledby="activitySummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activitySummaryModalLabel">สรุปกิจกรรมการวิ่ง</h5>
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

                <div class="achievement-section mt-3">
                    <h6>ความสำเร็จ</h6>
                    <div class="achievement-badges d-flex flex-wrap gap-3 mt-2">
                        <!-- Example badge that might be earned -->
                        <div class="badge-earned p-2 rounded bg-light text-center">
                            <div><i class="fas fa-medal text-warning fa-2x"></i></div>
                            <div class="mt-1 badge bg-warning">การวิ่งครั้งแรก</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" id="saveActivityBtn">บันทึกกิจกรรม</button>
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

    .run-controls {
        margin-top: 1.5rem;
    }

    #startRunBtn, #pauseRunBtn, #stopRunBtn {
        border-radius: var(--radius-full);
        box-shadow: var(--shadow-sm);
        font-weight: var(--font-weight-medium);
        transition: all var(--transition-normal);
    }

    #startRunBtn:hover, #pauseRunBtn:hover, #stopRunBtn:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
</style>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry&callback=initMap" async defer></script>
<script>
    let map;
    let summaryMap;
    let currentPositionMarker;
    let routePath;
    let watchId;
    let routeCoordinates = [];
    let startTime;
    let timerInterval;
    let isPaused = false;
    let pauseStartTime;
    let totalPausedTime = 0;
    let isRunning = false;

    window.initMap = function() {
        // สร้างแผนที่เริ่มต้น
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 13.7563, lng: 100.5018 }, // กรุงเทพฯ
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            fullscreenControl: false,
            streetViewControl: false
        });

        // สร้างเส้นทาง
        routePath = new google.maps.Polyline({
            path: routeCoordinates,
            geodesic: true,
            strokeColor: "#2DC679",
            strokeOpacity: 1.0,
            strokeWeight: 4
        });

        routePath.setMap(map);

        // หาตำแหน่งปัจจุบัน
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    map.setCenter(pos);

                    // สร้าง marker สำหรับตำแหน่งปัจจุบัน
                    currentPositionMarker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 8,
                            fillColor: "#2DC679",
                            fillOpacity: 1,
                            strokeColor: "#FFFFFF",
                            strokeWeight: 2
                        }
                    });
                },
                () => {
                    // กรณีที่ไม่อนุญาติให้ใช้ตำแหน่ง
                    showErrorAlert("เกิดข้อผิดพลาดในการเข้าถึงตำแหน่งของคุณ โปรดอนุญาตการเข้าถึงตำแหน่งและรีเฟรชหน้า");
                }
            );
        }
    };

    // อัปเดตตำแหน่งและเส้นทาง
    function updatePosition(position) {
        if (!isRunning) return;

        const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        // อัปเดต marker ตำแหน่งปัจจุบัน
        if (currentPositionMarker) {
            currentPositionMarker.setPosition(pos);
        }

        // เพิ่มตำแหน่งในเส้นทาง
        routeCoordinates.push(pos);
        routePath.setPath(routeCoordinates);

        // อัปเดตแผนที่ให้ตามตำแหน่งปัจจุบัน
        map.setCenter(pos);

        // คำนวณระยะทางและความเร็ว
        calculateStats();
    }

    // คำนวณสถิติการวิ่ง
    function calculateStats() {
        // คำนวณระยะทาง (กิโลเมตร)
        const distance = calculateDistance(routeCoordinates);
        document.getElementById('distance').textContent = distance.toFixed(2);

        // คำนวณเวลาที่ใช้
        const now = new Date().getTime();
        const elapsedTime = now - startTime - totalPausedTime;
        document.getElementById('time').textContent = formatTime(elapsedTime);

        // คำนวณความเร็ว (กม./ชม.)
        const elapsedHours = elapsedTime / (1000 * 60 * 60);
        const speed = elapsedHours > 0 ? distance / elapsedHours : 0;
        document.getElementById('speed').textContent = speed.toFixed(1);

        // คำนวณแคลอรี่ (ค่าประมาณโดยใช้ MET = 7 สำหรับการวิ่ง)
        // สมมติว่าผู้ใช้มีน้ำหนัก 70 กก. (ควรจะเปลี่ยนให้ใช้น้ำหนักจริงของผู้ใช้)
        const weight = 70; // กิโลกรัม
        const calories = calculateCalories(weight, elapsedHours, 7);
        document.getElementById('calories').textContent = Math.round(calories);
    }

    // คำนวณระยะทางจากเส้นทาง
    function calculateDistance(coordinates) {
        if (coordinates.length < 2) return 0;

        let totalDistance = 0;
        for (let i = 0; i < coordinates.length - 1; i++) {
            const point1 = coordinates[i];
            const point2 = coordinates[i + 1];

            // ใช้ Haversine formula จาก Google Maps Geometry library
            totalDistance += google.maps.geometry.spherical.computeDistanceBetween(
                new google.maps.LatLng(point1.lat, point1.lng),
                new google.maps.LatLng(point2.lat, point2.lng)
            );
        }

        // แปลงจากเมตรเป็นกิโลเมตร
        return totalDistance / 1000;
    }

    // คำนวณแคลอรี่เผาผลาญ
    function calculateCalories(weight, hours, met) {
        // Calories = MET * Weight (kg) * Time (hours)
        return met * weight * hours;
    }

    // จัดรูปแบบเวลาเป็น HH:MM:SS
    function formatTime(milliseconds) {
        const totalSeconds = Math.floor(milliseconds / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        return [
            hours.toString().padStart(2, '0'),
            minutes.toString().padStart(2, '0'),
            seconds.toString().padStart(2, '0')
        ].join(':');
    }

    // เริ่มการวิ่ง
    document.getElementById('startRunBtn').addEventListener('click', function() {
        // ขอสิทธิ์การเข้าถึงตำแหน่ง
        if (navigator.geolocation) {
            if (isPaused) {
                // กรณีที่กลับมาวิ่งต่อหลังจากพัก
                isPaused = false;
                totalPausedTime += (new Date().getTime() - pauseStartTime);
                this.disabled = true;
                document.getElementById('pauseRunBtn').disabled = false;
            } else {
                // กรณีที่เริ่มวิ่งครั้งแรก
                isRunning = true;
                startTime = new Date().getTime();
                routeCoordinates = [];

                // เริ่มติดตามตำแหน่ง
                watchId = navigator.geolocation.watchPosition(
                    updatePosition,
                    (error) => {
                        showErrorAlert("เกิดข้อผิดพลาดในการติดตามตำแหน่ง: " + error.message);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );

                // เริ่ม timer
                timerInterval = setInterval(function() {
                    if (!isPaused) {
                        calculateStats();
                    }
                }, 1000);

                this.disabled = true;
                document.getElementById('pauseRunBtn').disabled = false;
                document.getElementById('stopRunBtn').disabled = false;
            }
        } else {
            showErrorAlert("เบราว์เซอร์ของคุณไม่รองรับการค้นหาตำแหน่ง");
        }
    });

    // พักการวิ่ง
    document.getElementById('pauseRunBtn').addEventListener('click', function() {
        if (isRunning) {
            isPaused = true;
            pauseStartTime = new Date().getTime();
            this.disabled = true;
            document.getElementById('startRunBtn').disabled = false;
        }
    });

    // หยุดการวิ่ง
    document.getElementById('stopRunBtn').addEventListener('click', function() {
        if (isRunning || isPaused) {
            // หยุดการติดตามตำแหน่ง
            navigator.geolocation.clearWatch(watchId);
            clearInterval(timerInterval);

            // รีเซ็ตสถานะ
            isRunning = false;
            isPaused = false;

            // รีเซ็ตปุ่ม
            document.getElementById('startRunBtn').disabled = false;
            document.getElementById('pauseRunBtn').disabled = true;
            document.getElementById('stopRunBtn').disabled = true;

            // แสดงสรุปกิจกรรม
            const distance = parseFloat(document.getElementById('distance').textContent);
            const time = document.getElementById('time').textContent;
            const calories = document.getElementById('calories').textContent;

            // ตรวจสอบว่ามีการวิ่งจริงๆ หรือไม่
            if (distance > 0.01) {
                document.getElementById('summaryDistance').textContent = distance + " กม.";
                document.getElementById('summaryTime').textContent = time;
                document.getElementById('summaryCalories').textContent = calories + " kcal";

                // สร้างแผนที่สรุป
                setTimeout(() => {
                    showSummaryMap();

                    // แสดง modal สรุปกิจกรรม
                    const summaryModal = new bootstrap.Modal(document.getElementById('activitySummaryModal'));
                    summaryModal.show();
                }, 500);
            } else {
                showWarningAlert("ระยะทางวิ่งน้อยเกินไป ไม่สามารถบันทึกกิจกรรมได้");
            }
        }
    });

    // แสดงแผนที่สรุป
    function showSummaryMap() {
        if (routeCoordinates.length === 0) return;

        // สร้างแผนที่สรุป
        summaryMap = new google.maps.Map(document.getElementById("summaryMap"), {
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            fullscreenControl: false,
            streetViewControl: false
        });

        // สร้างเส้นทางบนแผนที่สรุป
        const summaryPath = new google.maps.Polyline({
            path: routeCoordinates,
            geodesic: true,
            strokeColor: "#2DC679",
            strokeOpacity: 1.0,
            strokeWeight: 4
        });

        summaryPath.setMap(summaryMap);

        // สร้าง marker จุดเริ่มต้นและจุดสิ้นสุด
        if (routeCoordinates.length > 1) {
            // จุดเริ่มต้น (สีเขียว)
            new google.maps.Marker({
                position: routeCoordinates[0],
                map: summaryMap,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    fillColor: "#2DC679",
                    fillOpacity: 1,
                    strokeColor: "#FFFFFF",
                    strokeWeight: 2
                }
            });

            // จุดสิ้นสุด (สีแดง)
            new google.maps.Marker({
                position: routeCoordinates[routeCoordinates.length - 1],
                map: summaryMap,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    fillColor: "#FF4646",
                    fillOpacity: 1,
                    strokeColor: "#FFFFFF",
                    strokeWeight: 2
                }
            });
        }

        // ปรับ zoom และ center ให้เห็นเส้นทางทั้งหมด
        const bounds = new google.maps.LatLngBounds();
        routeCoordinates.forEach(coord => {
            bounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
        });
        summaryMap.fitBounds(bounds);
    }

    // บันทึกกิจกรรม
    document.getElementById('saveActivityBtn').addEventListener('click', function() {
        const distance = parseFloat(document.getElementById('distance').textContent);
        const time = document.getElementById('time').textContent;
        const calories = parseInt(document.getElementById('calories').textContent);
        const speed = parseFloat(document.getElementById('speed').textContent);

        // สร้าง FormData เพื่อส่งข้อมูล
        const formData = new FormData();
        formData.append('distance', distance);
        formData.append('calories_burned', calories);
        formData.append('average_speed', speed);
        formData.append('route_gps_data', JSON.stringify(routeCoordinates));
        formData.append('activity_type', 'running');
        formData.append('_token', '{{ csrf_token() }}');

        // ส่งข้อมูลไปบันทึก
        fetch('{{ route("run.store") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // ซ่อน modal
                bootstrap.Modal.getInstance(document.getElementById('activitySummaryModal')).hide();

                // แสดงข้อความสำเร็จ
                showSuccessAlert("บันทึกกิจกรรมเรียบร้อยแล้ว!");

                // รีเฟรชหน้าเพื่อแสดงข้อมูลล่าสุด
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showErrorAlert("ไม่สามารถบันทึกกิจกรรมได้: " + data.message);
            }
        })
        .catch(error => {
            showErrorAlert("เกิดข้อผิดพลาดในการบันทึกกิจกรรม: " + error);
        });
    });
</script>
@endsection
