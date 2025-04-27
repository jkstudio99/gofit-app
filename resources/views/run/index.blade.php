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
                    <div class="control-buttons mt-3">
                        <button id="startRunBtn" class="btn btn-primary btn-lg w-100 mb-2" onclick="startRun()">เริ่มวิ่ง</button>
                        <div class="row g-2 running-controls" style="display: none;">
                            <div class="col">
                                <button id="pauseRunBtn" class="btn btn-secondary btn-lg w-100" onclick="pauseRun()">หยุดชั่วคราว</button>
                            </div>
                            <div class="col">
                                <button id="stopRunBtn" class="btn btn-danger btn-lg w-100" onclick="stopRun()">จบการวิ่ง</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ประวัติการวิ่งล่าสุด -->
            <div class="card gofit-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ประวัติการวิ่งล่าสุด</h5>
                    <div>
                        <a href="{{ route('run.test') }}" class="btn btn-sm btn-warning rounded-pill me-2">
                            <i class="fas fa-flask me-1"></i> ทดสอบการวิ่ง
                        </a>
                        <a href="{{ route('run.history') }}" class="btn btn-sm btn-outline-primary rounded-pill">ดูทั้งหมด</a>
                    </div>
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

                <div id="achievementContainer" class="row mt-4 g-3">
                    <!-- พื้นที่สำหรับแสดงเหรียญรางวัล -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" id="saveActivityBtn">บันทึกการวิ่ง</button>
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
<!-- Bootstrap JS Bundle (Popper included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    let simulationInterval;
    let distance = 0;

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

    // เพิ่มฟังก์ชันจำลองการวิ่ง
    function simulateRunning() {
        // ใช้ตำแหน่งปัจจุบันของ marker ถ้ามี หรือใช้ตำแหน่งกรุงเทพฯเป็นจุดเริ่มต้น
        let currentLat = currentPositionMarker ? currentPositionMarker.getPosition().lat() : 13.7563;
        let currentLng = currentPositionMarker ? currentPositionMarker.getPosition().lng() : 100.5018;

        console.log("เริ่มจำลองการวิ่ง จากตำแหน่ง:", currentLat, currentLng);

        // จำลองการวิ่งเป็นเส้นทางวงกลม
        let angle = 0;
        let radius = 0.001; // ประมาณ 100 เมตร

        // จำลองการเคลื่อนที่ทุก 3 วินาที
        simulationInterval = setInterval(() => {
            // คำนวณตำแหน่งใหม่โดยใช้ฟังก์ชัน sin/cos เพื่อสร้างเส้นทางวงกลม
            angle += (Math.PI / 18); // เพิ่มมุม 10 องศาต่อครั้ง

            // สร้างตำแหน่งใหม่ในรูปแบบวงกลม + มีการเคลื่อนที่ไปข้างหน้าเล็กน้อย
            let newLat = currentLat + radius * Math.sin(angle);
            let newLng = currentLng + radius * Math.cos(angle);

            // เพิ่มการเคลื่อนที่ไปข้างหน้าเล็กน้อย
            currentLat += 0.00005; // เคลื่อนที่ไปทางเหนือเล็กน้อย

            // จำลองการเรียก updatePosition
            updatePosition({
                coords: {
                    latitude: newLat,
                    longitude: newLng,
                    accuracy: 10
                }
            });

            console.log("จำลองตำแหน่งใหม่:", newLat, newLng);

        }, 3000); // ทุก 3 วินาที
    }

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

    // แสดงข้อความแจ้งเตือน
    function showErrorAlert(message) {
        Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            text: message
        });
    }

    function showWarningAlert(message) {
        alert('คำเตือน: ' + message);
    }

    function showSuccessAlert(message) {
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    }

    // เริ่มการวิ่ง
    function startRun() {
        if (isPaused) {
            // กรณีที่กลับมาวิ่งต่อหลังจากพัก
            isPaused = false;
            totalPausedTime += (new Date().getTime() - pauseStartTime);
            document.getElementById('startRunBtn').style.display = 'none';
            document.querySelector('.running-controls').style.display = 'flex';
            document.getElementById('pauseRunBtn').disabled = false;

            // เริ่มการจำลองอีกครั้ง
            simulateRunning();

            console.log("กลับมาวิ่งต่อแล้ว");
        } else {
            // กรณีที่เริ่มวิ่งครั้งแรก
            isRunning = true;
            startTime = new Date().getTime();
            routeCoordinates = [];

            // แสดงปุ่มควบคุมการวิ่ง
            document.getElementById('startRunBtn').style.display = 'none';
            document.querySelector('.running-controls').style.display = 'flex';

            // ใช้ฟังก์ชันจำลองแทนการใช้ GPS จริง
            simulateRunning();

            // เริ่ม timer
            timerInterval = setInterval(function() {
                if (!isPaused) {
                    calculateStats();
                }
            }, 1000);

            document.getElementById('pauseRunBtn').disabled = false;
            document.getElementById('stopRunBtn').disabled = false;

            console.log("เริ่มวิ่งแล้ว (จำลอง)");
        }
    }

    // พักการวิ่ง
    function pauseRun() {
        if (isRunning) {
            isPaused = true;
            pauseStartTime = new Date().getTime();

            // เปลี่ยนไปแสดงปุ่ม Start และซ่อนปุ่มพัก
            document.getElementById('startRunBtn').style.display = 'block';
            document.getElementById('startRunBtn').textContent = 'วิ่งต่อ';
            document.querySelector('.running-controls').style.display = 'none';

            // พักการจำลองเมื่อกดพัก
            clearInterval(simulationInterval);
            console.log("พักการวิ่งแล้ว");
        }
    }

    // หยุดการวิ่ง
    function stopRun() {
        if (isRunning || isPaused) {
            // หยุดการติดตาม
            clearInterval(simulationInterval);
            clearInterval(timerInterval);

            isRunning = false;
            isPaused = false;

            // รีเซ็ตปุ่ม
            document.getElementById('startRunBtn').style.display = 'block';
            document.getElementById('startRunBtn').textContent = 'เริ่มวิ่ง';
            document.querySelector('.running-controls').style.display = 'none';
            document.getElementById('pauseRunBtn').disabled = false;
            document.getElementById('stopRunBtn').disabled = false;

            // แสดงหน้าสรุปกิจกรรม
            showActivitySummary();

            console.log("หยุดการวิ่งแล้ว");
        }
    }

    function showActivitySummary() {
        // สร้างแผนที่สรุป
        if (!summaryMap) {
            summaryMap = new google.maps.Map(document.getElementById("summaryMap"), {
                zoom: 14,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: false,
                fullscreenControl: false,
                streetViewControl: false
            });

            // สร้างเส้นทางบนแผนที่สรุป
            new google.maps.Polyline({
                path: routeCoordinates,
                geodesic: true,
                strokeColor: "#4CAF50", // สีเขียวสำหรับการวิ่งจริง
                strokeOpacity: 1.0,
                strokeWeight: 4,
                map: summaryMap
            });

            // ปรับขอบเขตให้เห็นเส้นทางทั้งหมด
            if (routeCoordinates.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                for (const coord of routeCoordinates) {
                    bounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
                }
                summaryMap.fitBounds(bounds);
            }
        }

        // อัปเดตข้อมูลสรุป
        document.getElementById('summaryDistance').innerText = document.getElementById('distance').innerText + " กม.";
        document.getElementById('summaryTime').innerText = document.getElementById('time').innerText;
        document.getElementById('summaryCalories').innerText = document.getElementById('calories').innerText + " kcal";

        // เตรียมพื้นที่สำหรับเหรียญรางวัล
        document.getElementById('achievementContainer').innerHTML = '';

        // แสดงหน้าต่างสรุป
        const summaryModal = new bootstrap.Modal(document.getElementById('activitySummaryModal'));
        summaryModal.show();
    }

    // ฟังก์ชันสำหรับบันทึกกิจกรรม (สามารถเรียกใช้ได้จากหลายที่)
    document.getElementById('saveActivityBtn').addEventListener('click', function() {
        const distance = parseFloat(document.getElementById('distance').innerText);
        const time = document.getElementById('time').innerText;
        const calories = parseInt(document.getElementById('calories').innerText);
        const speed = parseFloat(document.getElementById('speed').innerText);

        saveActivity(distance, time, calories, speed);
    });

    // ฟังก์ชันบันทึกกิจกรรม
    function saveActivity(distance, time, calories, speed) {
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
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server response was not OK: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // แสดงข้อความสำเร็จ
                showSuccessAlert("บันทึกกิจกรรมเรียบร้อยแล้ว!");

                // รีเฟรชหน้าเพื่อแสดงข้อมูลล่าสุด
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showErrorAlert("ไม่สามารถบันทึกกิจกรรมได้: " + (data.message || 'ไม่ทราบสาเหตุ'));
            }
        })
        .catch(error => {
            console.error("Error saving activity:", error);
            showErrorAlert("เกิดข้อผิดพลาดในการบันทึกกิจกรรม: " + error);
        });
    }

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

                    // ซ่อนปุ่มบันทึกเพราะเป็นข้อมูลที่บันทึกแล้ว
                    document.getElementById('saveActivityBtn').style.display = 'none';

                    // สร้างแผนที่ใหม่สำหรับประวัติ
                    summaryMap = new google.maps.Map(document.getElementById("summaryMap"), {
                        zoom: 14,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        mapTypeControl: false,
                        fullscreenControl: false,
                        streetViewControl: false
                    });

                    // สร้างเส้นทางหากมีข้อมูล
                    if (routeCoords.length > 0) {
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

                    // แสดง modal
                    const summaryModal = new bootstrap.Modal(document.getElementById('activitySummaryModal'));
                    summaryModal.show();

                } catch (e) {
                    console.error("เกิดข้อผิดพลาดในการแสดงข้อมูลเส้นทาง:", e);
                    showErrorAlert("ไม่สามารถแสดงข้อมูลเส้นทางได้");
                }
            });
        });

        // เพิ่มตัวจัดการเมื่อ Modal ถูกปิด
        document.getElementById('activitySummaryModal').addEventListener('hidden.bs.modal', function () {
            // แสดงปุ่มบันทึกกลับมาเมื่อ modal ถูกปิด (เผื่อเปิดใหม่จากการวิ่งครั้งใหม่)
            document.getElementById('saveActivityBtn').style.display = 'block';
        });
    });
</script>
@endsection
