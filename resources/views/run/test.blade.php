@extends('layouts.app')

@section('title', 'ทดสอบการวิ่ง')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card gofit-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ทดสอบการวิ่ง (ไม่นับสถิติ)</h5>
                    <span class="badge bg-warning">โหมดทดสอบ</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        การวิ่งทดสอบจะไม่นำไปคิดคะแนนสะสมเพื่อรับเหรียญรางวัล
                    </div>

                    <form id="temp-csrf-form" action="/run/start" method="post" style="display:none;">
                        @csrf
                        <input type="hidden" name="is_test" value="1">
                        <button type="submit">Submit</button>
                    </form>

                    <button id="testConnectionBtn" class="btn btn-sm btn-info mb-3">ทดสอบการเชื่อมต่อ</button>
                    <button id="testCSRFBtn" class="btn btn-sm btn-success mb-3 ms-2">ทดสอบ CSRF</button>

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
                            <i class="fas fa-play me-2"></i> เริ่มทดสอบวิ่ง
                        </button>
                        <button type="button" id="pauseRunBtn" class="btn btn-warning btn-lg px-5 py-3 me-2" disabled>
                            <i class="fas fa-pause me-2"></i> พัก
                        </button>
                        <button type="button" id="stopRunBtn" class="btn btn-danger btn-lg px-5 py-3" disabled>
                            <i class="fas fa-stop me-2"></i> หยุดทดสอบ
                        </button>
                    </div>
                </div>
            </div>

            <!-- ประวัติการทดสอบล่าสุด -->
            <div class="card gofit-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ประวัติการทดสอบวิ่งล่าสุด</h5>
                    <a href="{{ route('run.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">กลับสู่การวิ่งจริง</a>
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
                            @forelse($testActivities ?? [] as $activity)
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
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">ยังไม่มีการทดสอบที่บันทึก</td>
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
                <h5 class="modal-title" id="activitySummaryModalLabel">สรุปการทดสอบวิ่ง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    นี่เป็นการทดสอบและจะไม่นับสถิติสะสมเพื่อรับเหรียญรางวัล
                </div>

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
                <button type="button" class="btn btn-warning" id="saveActivityBtn">บันทึกการทดสอบวิ่ง</button>
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
<!-- โหลด jQuery ก่อน -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- จากนั้นโหลด Google Maps API -->
<script>
    // ฟังก์ชันเริ่มต้นแผนที่ที่จะถูกเรียกโดย Maps API
    function initMap() {
        console.log("กำลังเริ่มต้นแผนที่...");

        try {
            // สร้างแผนที่เริ่มต้น
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 13.7563, lng: 100.5018 }, // กรุงเทพฯ
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: false,
                fullscreenControl: false,
                streetViewControl: false
            });

            console.log("สร้างแผนที่สำเร็จ");

            // สร้างเส้นทาง
            routePath = new google.maps.Polyline({
                path: routeCoordinates,
                geodesic: true,
                strokeColor: "#FFC107", // สีเหลืองสำหรับโหมดทดสอบ
                strokeOpacity: 1.0,
                strokeWeight: 4
            });

            routePath.setMap(map);
            console.log("สร้างเส้นทางสำเร็จ");

            // สร้างตำแหน่งจำลอง
            const pos = { lat: 13.7563, lng: 100.5018 };

            // สร้างมาร์กเกอร์
            currentPositionMarker = new google.maps.Marker({
                position: pos,
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#FFC107",
                    fillOpacity: 1,
                    strokeColor: "#fff",
                    strokeWeight: 2,
                },
            });

            map.setCenter(pos);
            console.log("สร้างตำแหน่งจำลองสำเร็จ:", pos);

            // หาตำแหน่งปัจจุบันถ้าสามารถใช้งานได้
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const realPos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        // อัปเดตตำแหน่งจริง
                        currentPositionMarker.setPosition(realPos);
                        map.setCenter(realPos);
                        console.log("หาตำแหน่งปัจจุบันสำเร็จ:", realPos);
                    },
                    (error) => {
                        console.warn("ไม่สามารถอ่านตำแหน่งปัจจุบัน:", error);
                        console.log("ใช้ตำแหน่งจำลองแทน");
                    }
                );
            } else {
                console.log("Geolocation ไม่รองรับในเบราว์เซอร์นี้ ใช้ตำแหน่งจำลองแทน");
            }
        } catch (error) {
            console.error("เกิดข้อผิดพลาดในการสร้างแผนที่:", error);

            // แสดงข้อความแทนแผนที่
            const mapDiv = document.getElementById("map");
            if (mapDiv) {
                mapDiv.innerHTML = '<div class="alert alert-danger text-center p-5 m-0"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>ไม่สามารถโหลดแผนที่ได้<br>โปรดรีเฟรชหน้านี้</div>';
            }
        }
    }
</script>

<!-- โหลด Google Maps API พร้อมคอลแบ็ก initMap -->
<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry&callback=initMap" async defer></script>

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
    let activityId = null;

    // ตรวจสอบว่ามี jQuery แล้วหรือยัง
    function ensureJQuery(callback) {
        if (window.jQuery) {
            // มี jQuery แล้ว, เรียกฟังก์ชันต่อไป
            console.log("jQuery พร้อมใช้งาน");
            callback();
        } else {
            // ยังไม่มี jQuery, โหลดและลองอีกครั้ง
            console.log("กำลังโหลด jQuery...");
            var script = document.createElement('script');
            script.src = "https://code.jquery.com/jquery-3.6.0.min.js";
            script.onload = function() {
                console.log("โหลด jQuery สำเร็จ");
                callback();
            };
            document.head.appendChild(script);
        }
    }

    // ใช้ฟังก์ชันเมื่อโหลดเพจเสร็จ
    window.onload = function() {
        console.log("หน้าเว็บโหลดเสร็จแล้ว");

        ensureJQuery(function() {
            // ตั้งค่า click handler เมื่อมี jQuery แล้ว
            setupEventHandlers();
        });
    };

    function setupEventHandlers() {
        console.log("กำลังตั้งค่าตัวจัดการเหตุการณ์...");

        // ทดสอบ CSRF Token
        $("#testCSRFBtn").on("click", function() {
            console.log("กดปุ่มทดสอบ CSRF");
            // แสดง CSRF Token ที่ได้
            const csrfTokenMeta = $('meta[name="csrf-token"]').attr("content");
            const csrfTokenInput = $('input[name="_token"]').val();

            alert("CSRF Token from meta: " + (csrfTokenMeta || "ไม่พบ") +
                  "\nCSRF Token from form: " + (csrfTokenInput || "ไม่พบ"));

            if (!csrfTokenMeta && csrfTokenInput) {
                // ถ้าไม่มี meta แต่มี form input ให้สร้าง meta
                $('head').append('<meta name="csrf-token" content="' + csrfTokenInput + '">');
                alert("สร้าง meta csrf-token ใหม่แล้ว");
            }
        });

        // ทดสอบการเชื่อมต่อกับเซิร์ฟเวอร์
        $("#testConnectionBtn").on("click", function() {
            console.log("กดปุ่มทดสอบการเชื่อมต่อ");

            // ใช้ XMLHttpRequest แทน jQuery AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/run/test-start', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        console.log("ทดสอบการเชื่อมต่อสำเร็จ:", data);
                        alert("การเชื่อมต่อกับเซิร์ฟเวอร์สำเร็จ: " + data.message + " (" + data.time + ")");
                    } catch (e) {
                        console.error("ข้อมูลตอบกลับไม่ใช่ JSON:", xhr.responseText);
                        alert("ข้อมูลตอบกลับไม่ใช่ JSON: " + xhr.responseText.substring(0, 100));
                    }
                } else {
                    console.error("ทดสอบการเชื่อมต่อล้มเหลว:", xhr.status);
                    alert("การเชื่อมต่อกับเซิร์ฟเวอร์ล้มเหลว: " + xhr.status);
                }
            };
            xhr.onerror = function() {
                console.error("เกิดข้อผิดพลาดในการเชื่อมต่อ");
                alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");
            };
            xhr.send();
        });

        // ปุ่มเริ่มวิ่ง
        $("#startRunBtn").on("click", function() {
            console.log("กดปุ่มเริ่มทดสอบวิ่ง");

            if (!isRunning) {
                console.log("กำลังเริ่มทดสอบวิ่ง...");

                // ใช้ฟอร์มปกติแทน AJAX
                if (confirm("คุณต้องการทดสอบวิ่งใช่หรือไม่?")) {
                    // ทดสอบโดยการกำหนดค่า
                    activityId = Date.now(); // ใช้เวลาปัจจุบันเป็น ID ชั่วคราว
                    console.log("กำหนด ID กิจกรรมชั่วคราว:", activityId);
                    startRunTracking();
                }

                /* ถ้าต้องการใช้ AJAX ให้ปลดคอมเมนต์ส่วนนี้
                // ดึง token จากฟอร์ม
                const csrfToken = $('input[name="_token"]').val();
                console.log("CSRF Token จากฟอร์ม:", csrfToken ? "มี Token" : "ไม่มี Token");

                // ส่ง API เพื่อสร้างกิจกรรมใหม่
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/run/start', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            var data = JSON.parse(xhr.responseText);
                            console.log("ได้รับการตอบกลับ:", data);
                            if (data.status === "success") {
                                activityId = data.activity_id;
                                startRunTracking();
                            } else {
                                alert(data.message || "เกิดข้อผิดพลาด");
                            }
                        } catch (e) {
                            console.error("ข้อมูลตอบกลับไม่ใช่ JSON:", xhr.responseText);
                            alert("ข้อมูลตอบกลับไม่ใช่ JSON");
                        }
                    } else {
                        console.error("ไม่สามารถเริ่มการวิ่งได้:", xhr.status);
                        alert("ไม่สามารถเริ่มการวิ่งได้: " + xhr.status);
                    }
                };
                xhr.onerror = function() {
                    console.error("เกิดข้อผิดพลาดในการเชื่อมต่อ");
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");
                };
                xhr.send('is_test=1&_token=' + encodeURIComponent(csrfToken));
                */
            } else {
                resumeRun();
            }
        });

        // ปุ่มพักการวิ่ง
        $("#pauseRunBtn").on("click", function() {
            pauseRun();
        });

        // ปุ่มหยุดการวิ่ง
        $("#stopRunBtn").on("click", function() {
            stopRun();
        });

        // ปุ่มบันทึกกิจกรรม
        $("#saveActivityBtn").on("click", function() {
            saveActivity();
        });

        console.log("ตั้งค่าตัวจัดการเหตุการณ์เสร็จสิ้น");
    }

    function startRunTracking() {
        // เริ่มการวิ่ง
        isRunning = true;
        routeCoordinates = [];
        startTime = new Date();

        // อัปเดตปุ่ม
        $("#startRunBtn").prop("disabled", true);
        $("#pauseRunBtn").prop("disabled", false);
        $("#stopRunBtn").prop("disabled", false);

        // เริ่มจับเวลา
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);

        // ระบบติดตามตำแหน่ง
        if (navigator.geolocation) {
            watchId = navigator.geolocation.watchPosition(
                trackPosition,
                (error) => {
                    console.log("Error:", error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        } else {
            // จำลองการเคลื่อนที่สำหรับการทดสอบ
            startSimulation();
        }
    }

    function trackPosition(position) {
        const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
        };

        // อัปเดตแผนที่
        if (currentPositionMarker) {
            currentPositionMarker.setPosition(pos);
        } else {
            currentPositionMarker = new google.maps.Marker({
                position: pos,
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#FFC107",
                    fillOpacity: 1,
                    strokeColor: "#fff",
                    strokeWeight: 2,
                },
            });
        }

        map.setCenter(pos);

        // อัปเดตเส้นทาง
        routeCoordinates.push(pos);
        routePath.setPath(routeCoordinates);

        // อัปเดตระยะทาง
        updateDistance();

        // อัปเดตความเร็ว
        updateSpeed();

        // อัปเดตแคลอรี่
        updateCalories();

        // ส่งข้อมูลไปยังเซิร์ฟเวอร์
        updateRouteOnServer();
    }

    function startSimulation() {
        // สำหรับการทดสอบเท่านั้น: จำลองการเคลื่อนที่
        let center = map.getCenter();
        let basePos = { lat: center.lat(), lng: center.lng() };
        let step = 0;

        simulationInterval = setInterval(() => {
            if (!isPaused) {
                // สร้างจุดรอบ ๆ ตำแหน่งเริ่มต้น
                const pos = {
                    lat: basePos.lat + 0.0003 * Math.sin(step / 10),
                    lng: basePos.lng + 0.0003 * Math.cos(step / 10),
                };

                // เพิ่มตำแหน่งในเส้นทาง
                routeCoordinates.push(pos);
                routePath.setPath(routeCoordinates);

                // อัปเดตมาร์กเกอร์
                if (currentPositionMarker) {
                    currentPositionMarker.setPosition(pos);
                } else {
                    currentPositionMarker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 10,
                            fillColor: "#FFC107",
                            fillOpacity: 1,
                            strokeColor: "#fff",
                            strokeWeight: 2,
                        },
                    });
                }

                // อัปเดตข้อมูล
                updateDistance();
                updateSpeed();
                updateCalories();
                updateRouteOnServer();

                step++;
            }
        }, 1000);
    }

    function pauseRun() {
        isPaused = true;
        pauseStartTime = new Date();
        $("#startRunBtn").prop("disabled", false).text("ดำเนินการต่อ").prepend('<i class="fas fa-play me-2"></i>');
        $("#pauseRunBtn").prop("disabled", true);
    }

    function resumeRun() {
        isPaused = false;
        totalPausedTime += new Date() - pauseStartTime;
        $("#startRunBtn").prop("disabled", true).text("เริ่มทดสอบวิ่ง").prepend('<i class="fas fa-play me-2"></i>');
        $("#pauseRunBtn").prop("disabled", false);
    }

    function stopRun() {
        isRunning = false;
        isPaused = false;

        // หยุดการติดตาม
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
        }

        clearInterval(timerInterval);
        if (simulationInterval) clearInterval(simulationInterval);

        // สรุปกิจกรรม
        showActivitySummary();
    }

    function updateTimer() {
        if (!isPaused) {
            const now = new Date();
            const elapsedTime = now - startTime - totalPausedTime;
            const hours = Math.floor(elapsedTime / 3600000);
            const minutes = Math.floor((elapsedTime % 3600000) / 60000);
            const seconds = Math.floor((elapsedTime % 60000) / 1000);

            $("#time").text(
                hours.toString().padStart(2, "0") + ":" +
                minutes.toString().padStart(2, "0") + ":" +
                seconds.toString().padStart(2, "0")
            );
        }
    }

    function updateDistance() {
        if (routeCoordinates.length < 2) return;

        let distance = 0;
        for (let i = 1; i < routeCoordinates.length; i++) {
            distance += google.maps.geometry.spherical.computeDistanceBetween(
                new google.maps.LatLng(routeCoordinates[i-1].lat, routeCoordinates[i-1].lng),
                new google.maps.LatLng(routeCoordinates[i].lat, routeCoordinates[i].lng)
            );
        }

        // แปลงเป็นกิโลเมตร
        distance = distance / 1000;
        $("#distance").text(distance.toFixed(2));
    }

    function updateSpeed() {
        if (routeCoordinates.length < 2) return;

        const now = new Date();
        const elapsedHours = (now - startTime - totalPausedTime) / 3600000;
        const distance = parseFloat($("#distance").text());

        if (elapsedHours > 0) {
            const speed = distance / elapsedHours;
            $("#speed").text(speed.toFixed(1));
        }
    }

    function updateCalories() {
        // คำนวณแคลอรี่อย่างง่าย (ตัวอย่าง)
        const distance = parseFloat($("#distance").text());
        // สมมติว่าการวิ่ง 1 กิโลเมตรใช้พลังงานประมาณ 70 แคลอรี่
        const calories = Math.round(distance * 70);
        $("#calories").text(calories);
    }

    function updateRouteOnServer() {
        if (!activityId) return;

        $.ajax({
            url: "/run/updateRoute",
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            data: {
                activity_id: activityId,
                route_data: JSON.stringify(routeCoordinates),
                current_distance: parseFloat($("#distance").text())
            },
            success: function(data) {
                console.log("Route updated:", data);
            },
            error: function(xhr, status, error) {
                console.error("Error updating route:", error);
            }
        });
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
                strokeColor: "#FFC107",
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
        $("#summaryDistance").text($("#distance").text() + " กม.");
        $("#summaryTime").text($("#time").text());
        $("#summaryCalories").text($("#calories").text() + " kcal");

        // แสดงหน้าต่างสรุป
        $("#activitySummaryModal").modal("show");
    }

    function saveActivity() {
        if (!activityId) return;

        // คำนวณระยะเวลาทั้งหมด (วินาที)
        const timeStr = $("#time").text();
        const timeParts = timeStr.split(":");
        const durationSeconds = parseInt(timeParts[0]) * 3600 + parseInt(timeParts[1]) * 60 + parseInt(timeParts[2]);

        $.ajax({
            url: "/run/finish",
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            data: {
                activity_id: activityId,
                route_data: JSON.stringify(routeCoordinates),
                distance: parseFloat($("#distance").text()),
                duration: durationSeconds,
                calories: parseInt($("#calories").text()),
                average_speed: parseFloat($("#speed").text()),
                is_test: true // ระบุว่าเป็นการทดสอบ
            },
            success: function(data) {
                console.log("Activity saved:", data);
                $("#activitySummaryModal").modal("hide");

                // รีเซ็ตการทำงาน
                resetRun();

                // รีโหลดหน้าเพื่อแสดงกิจกรรมล่าสุด
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr, status, error) {
                console.error("Error saving activity:", error);
                console.error("Status:", status);
                console.error("Response:", xhr.responseText);
                alert("ไม่สามารถบันทึกกิจกรรมได้ กรุณาลองใหม่อีกครั้ง");
            }
        });
    }

    function resetRun() {
        // รีเซ็ตตัวแปรและการแสดงผล
        isRunning = false;
        isPaused = false;
        routeCoordinates = [];
        routePath.setPath([]);

        $("#time").text("00:00:00");
        $("#distance").text("0.00");
        $("#speed").text("0.0");
        $("#calories").text("0");

        $("#startRunBtn").prop("disabled", false).text("เริ่มทดสอบวิ่ง").prepend('<i class="fas fa-play me-2"></i>');
        $("#pauseRunBtn").prop("disabled", true);
        $("#stopRunBtn").prop("disabled", true);

        activityId = null;
    }
</script>
@endsection
