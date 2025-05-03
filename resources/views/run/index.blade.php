@extends('layouts.app')

@section('title', 'เริ่มวิ่ง')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- การ์ดสำหรับการวิ่ง -->
            <div class="card gofit-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">บันทึกกิจกรรมการวิ่ง <span id="simulationBadge" class="badge bg-info ms-2">โหมดจำลอง</span></h5>
                    <div>
                        <button id="toggleModeBtn" class="btn btn-sm btn-outline-primary rounded-pill me-2" data-mode="simulation">
                            <i class="fas fa-exchange-alt me-1"></i> <span id="toggleModeText">ใช้ GPS จริง</span>
                        </button>
                        <a href="{{ route('run.history') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-history me-1"></i> ประวัติการวิ่ง
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="simulationAlert" class="alert alert-info mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fs-4"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading mb-1">กำลังใช้โหมดจำลองการวิ่ง</h6>
                                <p class="mb-0 small">ระบบจะจำลองเส้นทางและพิกัดการวิ่งโดยอัตโนมัติ เหมาะสำหรับการทดสอบและสาธิต ปรับความเร็วได้จากตัวเลือกด้านล่างซ้ายของแผนที่</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <!-- แผนที่สำหรับแสดงการวิ่ง -->
                        <div class="col-12">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div id="map" style="height: 400px; width: 100%; border-radius: var(--radius-lg); position: relative; z-index: 1; overflow: hidden;" class="mb-4 position-relative">
                                        <!-- ปุ่มรีเซ็ตตำแหน่ง -->
                                        <button id="centerLocationBtn" class="btn btn-light position-absolute bottom-0 end-0 m-3 shadow-sm" style="z-index: 1001;" title="ไปยังตำแหน่งปัจจุบัน">
                                            <i class="fas fa-location-arrow text-primary"></i>
                                        </button>
                                        <!-- ปุ่มเลือกความเร็ว (แสดงเฉพาะในโหมดจำลอง) -->
                                        <div id="speedSelectorContainer" class="position-absolute bottom-0 start-0 m-3 bg-white p-2 rounded shadow-sm" style="z-index: 1001;">
                                            <div class="d-flex align-items-center">
                                                <span class="me-2"><i class="fas fa-tachometer-alt text-success"></i></span>
                                                <select id="speedSelector" class="form-select form-select-sm" style="width: 180px;">
                                                    <option value="5">เดิน (5 กม./ชม.)</option>
                                                    <option value="8">วิ่งช้า (8 กม./ชม.)</option>
                                                    <option value="10" selected>วิ่งปกติ (10 กม./ชม.)</option>
                                                    <option value="15">วิ่งเร็ว (15 กม./ชม.)</option>
                                                    <option value="20">วิ่งเร็วมาก (20 กม./ชม.)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <div class="run-stat p-3 rounded bg-light position-relative overflow-hidden">
                                <div class="fs-5 text-muted mb-2">แคลอรี่</div>
                                <div class="fs-2 fw-bold text-primary" id="calories">0</div>
                                <div class="small">kcal</div>

                                <!-- แสดงการลดลงของแคลอรี่ (แสดงเมื่อมีการวิ่ง) -->
                                <div id="calorieAnimation" class="position-absolute start-0 end-0 bottom-0" style="height: 0%; background: linear-gradient(to top, rgba(255,193,7,0.2), rgba(255,193,7,0)); transition: height 0.3s ease-out;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ปุ่มควบคุม -->
                    <div class="control-buttons mt-3">
                        <button id="startRunBtn" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="fas fa-play-circle me-2"></i>เริ่มวิ่ง
                        </button>
                        <div class="row g-2 running-controls" style="display: none;">
                            <div class="col">
                                <button id="pauseRunBtn" class="btn btn-secondary btn-lg w-100">
                                    <i class="fas fa-pause-circle me-2"></i>หยุดชั่วคราว
                                </button>
                            </div>
                            <div class="col">
                                <button id="stopRunBtn" class="btn btn-danger btn-lg w-100">
                                    <i class="fas fa-stop-circle me-2"></i>จบการวิ่ง
                                </button>
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
                            @forelse($recentRuns ?? [] as $run)
                            <tr>
                                <td>{{ $run->start_time instanceof \Carbon\Carbon
                                      ? $run->start_time->formatThaiDate()
                                      : \Carbon\Carbon::parse($run->start_time)->formatThaiDate() }}</td>
                                <td>{{ number_format($run->distance, 2) }} กม.</td>
                                <td>
                                    @if($run->duration)
                                        @php
                                            $hours = floor($run->duration / 3600);
                                            $minutes = floor(($run->duration % 3600) / 60);
                                            $seconds = $run->duration % 60;
                                            echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                        @endphp
                                    @else
                                        <span class="badge bg-warning">กำลังดำเนินการ</span>
                                    @endif
                                </td>
                                <td>{{ number_format($run->average_speed, 1) }} กม./ชม.</td>
                                <td>{{ number_format($run->calories_burned, 0) }} kcal</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary show-run-map"
                                       data-id="{{ $run->run_id }}"
                                       data-route="{{ json_encode($run->route_data ?? []) }}"
                                       data-distance="{{ $run->distance }}"
                                       data-time="{{ $run->duration ? sprintf('%02d:%02d:%02d', floor($run->duration / 3600), floor(($run->duration % 3600) / 60), $run->duration % 60) : '00:00:00' }}"
                                       data-calories="{{ $run->calories_burned }}">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </a>
                                    @if($run->is_completed)
                                    <a href="{{ route('run.show', ['id' => $run->run_id]) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
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

                <!-- พื้นที่สำหรับแสดงข้อมูลเพิ่มเติมในอนาคต (ถ้าจำเป็น) -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" id="saveActivityBtn">บันทึกการวิ่ง</button>
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
                    <input type="hidden" id="runIdToShare" name="run_id">

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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<!-- Pre-load Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<style>
    /* Ensure map container has explicit height */
    #map {
        height: 400px !important;
        width: 100% !important;
        z-index: 1;
        max-width: 100%;
        overflow: hidden;
        border-radius: var(--radius-lg);
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    /* Fix for Leaflet map container to stay within bounds */
    .leaflet-container {
        max-width: 100%;
        max-height: 100%;
        border-radius: var(--radius-lg);
    }

    #summaryMap {
        height: 300px !important;
        width: 100%;
        z-index: 1;
        max-width: 100%;
        overflow: hidden;
        border-radius: var(--radius-md);
    }

    /* Rest of existing styles */
    .run-stat {
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-normal);
        position: relative;
        overflow: hidden;
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
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    #startRunBtn:hover, #pauseRunBtn:hover, #stopRunBtn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .location-pin {
        transition: all 0.3s ease;
    }

    .location-pin:hover {
        transform: scale(1.2);
    }

    /* สไตล์สำหรับปุ่มควบคุม */
    .control-buttons {
        margin-top: 1rem;
    }

    /* สไตล์สำหรับสถิติการวิ่ง */
    .run-stat {
        text-align: center;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .run-stat:hover {
        transform: translateY(-5px);
    }

    /* เอฟเฟกต์การเต้นของไอคอนแคลอรี่ */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .calorie-pulse {
        animation: pulse 0.5s ease-in-out;
    }

    /* ทำให้ dropdown ในแผนที่สวยงามขึ้น */
    #speedSelector {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        font-size: 0.875rem;
        padding: 0.4rem 0.8rem;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.8rem center;
        background-size: 16px 12px;
        padding-right: 2.5rem;
        min-width: 180px; /* เพิ่มความกว้างขั้นต่ำ */
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }

    #speedSelector:hover {
        border-color: #adb5bd;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    #speedSelector:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        outline: none;
    }

    #speedSelectorContainer {
        padding: 0.5rem 0.8rem;
        border-radius: 22px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    /* ฟอร์มูล่าการคำนวณแคลอรี่ */
    .formula-calculation {
        background-color: #f8f9fa;
        border-left: 4px solid #3085d6;
    }
</style>
@endsection

@section('scripts')
<!-- Bootstrap JS Bundle (Popper included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

<script>
    // Fix for navbar and map interaction
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure map has proper z-index
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.style.zIndex = 1;
        }

        // Ensure navbar is clickable
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            navbar.style.zIndex = 1030;
        }

        // Initialize Bootstrap dropdowns properly
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl)
        });
    });

    // Global variables
    let map;
    let currentPositionMarker;
    let routePolyline;
    let routePoints = [];
    let isRunning = false;
    let isPaused = false;
    let runId;
    let timer;
    let elapsedSeconds = 0;
    let currentDistance = 0;
    let currentSpeed = 0;
    let totalCalories = 0;
    let simulationInterval;
    let watchPositionId = null;

    // ตัวแปรใหม่สำหรับโหมดจำลอง
    let useSimulation = true; // เริ่มต้นใช้โหมดจำลอง
    let selectedSpeed = 10; // ความเร็วเริ่มต้น (km/h)
    let userWeight = 65; // น้ำหนักเริ่มต้น (kg)
    let calorieAnimationTimeout = null;

    // ตำแหน่งเริ่มต้น (กรุงเทพฯ)
    const defaultPosition = {
        lat: {{ $defaultLat ?? 13.736717 }},
        lng: {{ $defaultLng ?? 100.523186 }}
    };

    document.addEventListener('DOMContentLoaded', function() {
        try {
            console.log('DOM loaded, initializing map...');

        // ตั้งค่าแผนที่หลัก
        initMap();

            console.log('Map initialized');

        // ตั้งค่า Event Listeners
        setupEventListeners();

            // ขอตำแหน่งปัจจุบันและอัปเดตแผนที่
            getCurrentLocation();

            // ตรวจสอบว่ามีกิจกรรมที่ยังไม่เสร็จสิ้นหรือไม่
            checkForActiveActivity();

            // เมื่อผู้ใช้รีเฟรชหน้าหรือปิดแท็บ จะไม่บันทึกข้อมูลโดยอัตโนมัติ
            window.addEventListener('beforeunload', function(e) {
                if (isRunning) {
                    // ยกเลิกการวิ่งโดยไม่บันทึก
                    cancelRunning();

                    // แสดงข้อความเตือนถ้าอยู่ระหว่างการวิ่ง
                    e.preventDefault();
                    e.returnValue = 'คุณกำลังอยู่ระหว่างการวิ่ง ต้องการออกจากหน้านี้จริงหรือไม่?';
                    return e.returnValue;
                }
            });
        } catch (error) {
            console.error('Error initializing map:', error);
            alert('เกิดข้อผิดพลาดในการโหลดแผนที่: ' + error.message);
        }
    });

    // ฟังก์ชันตั้งค่าแผนที่
    function initMap() {
        try {
            console.log('Initializing map at position:', defaultPosition);
            console.debug('Map container element:', document.getElementById('map'));

            // Toggle map element visibility for debugging
            toggleMapDebug();

            // Create map with proper options
            map = L.map('map', {
                zoomControl: true,
                maxBounds: [
                    [-90, -180],  // Southwest corner
                    [90, 180]     // Northeast corner
                ],
                maxBoundsViscosity: 1.0,  // Prevent map from escaping bounds
                scrollWheelZoom: true
            }).setView([defaultPosition.lat, defaultPosition.lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // สร้างเส้นทาง
            routePolyline = L.polyline([], {
                color: 'blue',
                weight: 5,
                opacity: 0.7
            }).addTo(map);

            // Handle window resize to make map responsive
            window.addEventListener('resize', function() {
                map.invalidateSize();
            });

            // Force map container to fit within bounds
            setTimeout(() => {
                map.invalidateSize();
            }, 100);

            console.log('Map initialization complete');
        } catch (error) {
            console.error('Error in initMap function:', error);
            throw error;
        }
    }

    // ฟังก์ชันสลับแสดง/ซ่อนแผนที่สำหรับการดีบัก
    function toggleMapDebug() {
        const mapEl = document.getElementById('map');
        console.log('Map container dimensions:', mapEl.offsetWidth, mapEl.offsetHeight);

        // Force redraw of element
        mapEl.style.display = 'none';
        setTimeout(() => {
            mapEl.style.display = 'block';
        }, 100);
    }

    // ฟังก์ชันสลับโหมดการวิ่งระหว่างจำลองและจริง
    function toggleRunMode() {
        // ดึงโหมดปัจจุบัน (simulation หรือ real)
        const currentMode = document.getElementById('toggleModeBtn').getAttribute('data-mode');

        // สลับโหมด
        if (currentMode === 'simulation') {
            // เปลี่ยนเป็นโหมดจริง
            useSimulation = false;
            document.getElementById('toggleModeBtn').setAttribute('data-mode', 'real');
            document.getElementById('toggleModeText').textContent = 'ใช้โหมดจำลอง';
            document.getElementById('simulationBadge').textContent = 'โหมด GPS จริง';
            document.getElementById('simulationBadge').className = 'badge bg-success ms-2';
            document.getElementById('simulationAlert').style.display = 'none';
            document.getElementById('speedSelectorContainer').style.display = 'none';

            // ขอตำแหน่งปัจจุบัน
            getCurrentLocation();
        } else {
            // เปลี่ยนเป็นโหมดจำลอง
            useSimulation = true;
            document.getElementById('toggleModeBtn').setAttribute('data-mode', 'simulation');
            document.getElementById('toggleModeText').textContent = 'ใช้ GPS จริง';
            document.getElementById('simulationBadge').textContent = 'โหมดจำลอง';
            document.getElementById('simulationBadge').className = 'badge bg-info ms-2';
            document.getElementById('simulationAlert').style.display = 'block';
            document.getElementById('speedSelectorContainer').style.display = 'block';
        }

        console.log('Toggled mode to:', useSimulation ? 'simulation' : 'real GPS');
    }

    // ฟังก์ชันตั้งค่า Event Listeners
    function setupEventListeners() {
        // ปุ่มควบคุมการวิ่ง
        document.getElementById('startRunBtn').addEventListener('click', startRun);
        document.getElementById('pauseRunBtn').addEventListener('click', pauseRun);
        document.getElementById('stopRunBtn').addEventListener('click', stopRun);
        document.getElementById('saveActivityBtn').addEventListener('click', saveActivity);

        // ปุ่มรีเซ็ตตำแหน่ง
        document.getElementById('centerLocationBtn').addEventListener('click', function() {
            // แสดงไอคอน loading
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            this.disabled = true;

            // เรียกใช้ฟังก์ชันขอตำแหน่งปัจจุบันพร้อมซูมเข้า
            getCurrentLocation(true);

            // คืนค่าปุ่มหลังจาก 1 วินาที
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-location-arrow text-primary"></i>';
                this.disabled = false;
            }, 1000);
        });

        // ปุ่มสลับโหมด
        document.getElementById('toggleModeBtn').addEventListener('click', toggleRunMode);

        // ตัวเลือกความเร็ว
        document.getElementById('speedSelector').addEventListener('change', function() {
            selectedSpeed = parseFloat(this.value);
            if (isRunning && !isPaused) {
                // อัปเดตความเร็วทันที
                currentSpeed = selectedSpeed;
                document.getElementById('speed').textContent = selectedSpeed.toFixed(1);
            }
        });

        // แสดงสถานที่บนแผนที่
        document.querySelectorAll('.show-on-map').forEach(button => {
            button.addEventListener('click', function() {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name');

                map.setView([lat, lng], 16);

                // หาและเปิด popup
                map.eachLayer(function(layer) {
                    if (layer instanceof L.Marker) {
                        const latlng = layer.getLatLng();
                        if (latlng.lat === lat && latlng.lng === lng) {
                            layer.openPopup();
                        }
                    }
                });
            });
        });

        // แชร์การวิ่ง
        document.querySelectorAll('.share-run').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const runId = this.getAttribute('data-id');
                document.getElementById('runIdToShare').value = runId;

                const modal = new bootstrap.Modal(document.getElementById('shareRunModal'));
                modal.show();
            });
        });

        // ยืนยันการแชร์
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
            .then(response => {
                if (!response.ok) {
                    console.error('Network response error:', response.status);
                    return response.text().then(text => {
                        console.error('Response body:', text);
                        throw new Error('Network response was not ok: ' + response.status);
                    });
                }
                return response.json();
            })
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

        // แสดงเส้นทางการวิ่ง
        document.querySelectorAll('.show-run-map').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const runId = this.getAttribute('data-id');
                const routeData = JSON.parse(this.getAttribute('data-route') || '[]');
                const distance = this.getAttribute('data-distance');
                const time = this.getAttribute('data-time');
                const calories = this.getAttribute('data-calories');

                // Check if elements exist before trying to set their content
                const distanceElement = document.getElementById('summaryDistance');
                const timeElement = document.getElementById('summaryTime');
                const caloriesElement = document.getElementById('summaryCalories');
                const achievementContainer = document.getElementById('achievementContainer');
                const saveActivityBtn = document.getElementById('saveActivityBtn');
                const calorieCalcDetails = document.getElementById('calorieCalculationDetails');

                if (distanceElement) distanceElement.textContent = distance + ' กม.';
                if (timeElement) timeElement.textContent = time;
                if (caloriesElement) caloriesElement.textContent = calories + ' kcal';

                // Safely check if elements exist before manipulating them
                if (achievementContainer) achievementContainer.innerHTML = '';
                if (saveActivityBtn) saveActivityBtn.style.display = 'none';
                if (calorieCalcDetails) calorieCalcDetails.style.display = 'none';

                const modal = new bootstrap.Modal(document.getElementById('activitySummaryModal'));
                modal.show();

                setTimeout(() => {
                    // ทำลายแผนที่เดิมถ้ามี
                    if (window.summaryMapInstance) {
                        window.summaryMapInstance.remove();
                    }

                    const summaryMapElement = document.getElementById('summaryMap');
                    if (!summaryMapElement) {
                        console.error('Summary map element not found');
                        return;
                    }

                    const summaryMap = L.map('summaryMap').setView([defaultPosition.lat, defaultPosition.lng], 13);
                    window.summaryMapInstance = summaryMap;

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19
                    }).addTo(summaryMap);

                    if (routeData && routeData.length > 0) {
                        // จัดการกับรูปแบบข้อมูลที่อาจแตกต่างกัน
                        let points;

                        if (typeof routeData[0] === 'object' && routeData[0].lat !== undefined) {
                            points = routeData.map(point => [point.lat, point.lng]);
                        } else if (Array.isArray(routeData[0]) && routeData[0].length === 2) {
                            points = routeData;
                        } else {
                            // ถ้ารูปแบบไม่ตรง ใช้ตำแหน่งเริ่มต้น
                            points = [[defaultPosition.lat, defaultPosition.lng]];
                        }

                        const polyline = L.polyline(points, {
                            color: 'blue',
                            weight: 5,
                            opacity: 0.7
                        }).addTo(summaryMap);

                        if (points.length > 1) {
                            summaryMap.fitBounds(polyline.getBounds(), {
                                padding: [50, 50],
                                maxZoom: 16
                            });

                            // เพิ่มมาร์กเกอร์จุดเริ่มต้นและจุดสิ้นสุด
                            L.marker(points[0], {
                                icon: L.divIcon({
                                    className: 'location-pin',
                                    html: '<i class="fas fa-play-circle text-success" style="font-size: 24px;"></i>',
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                })
                            }).addTo(summaryMap);

                            L.marker(points[points.length - 1], {
                                icon: L.divIcon({
                                    className: 'location-pin',
                                    html: '<i class="fas fa-flag-checkered text-danger" style="font-size: 24px;"></i>',
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                })
                            }).addTo(summaryMap);
                        } else {
                            // ถ้ามีจุดเดียว แสดงจุดนั้น
                            L.marker(points[0], {
                                icon: L.divIcon({
                                    className: 'location-pin',
                                    html: '<i class="fas fa-running text-primary" style="font-size: 24px;"></i>',
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                })
                            }).addTo(summaryMap);
                        }
                    } else {
                        // ถ้าไม่มีข้อมูลเส้นทาง แสดงตำแหน่งเริ่มต้น
                        L.marker([defaultPosition.lat, defaultPosition.lng], {
                            icon: L.divIcon({
                                className: 'location-pin',
                                html: '<i class="fas fa-running text-primary" style="font-size: 24px;"></i>',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            })
                        }).addTo(summaryMap);
                    }

                    // ทำให้แผนที่ refresh เพื่อแสดงผลอย่างถูกต้อง
                    setTimeout(() => {
                        if (summaryMap) {
                            summaryMap.invalidateSize();
                        }
                    }, 100);
                }, 300);
            });
        });
    }

    // ฟังก์ชันขอตำแหน่งปัจจุบัน
    function getCurrentLocation(withZoom = false) {
        // แสดง loading ในขณะรอตำแหน่ง
        const loadingElement = document.createElement('div');
        loadingElement.className = 'position-absolute top-50 start-50 translate-middle bg-white p-3 rounded shadow-sm';
        loadingElement.id = 'locationLoading';
        loadingElement.innerHTML = '<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div><span>กำลังค้นหาตำแหน่งปัจจุบัน...</span></div>';
        document.getElementById('map').appendChild(loadingElement);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // ลบ loading
                    const loadingElement = document.getElementById('locationLoading');
                    if (loadingElement) {
                        loadingElement.remove();
                    }

                    // อัปเดตแผนที่ - ถ้ามีการเรียกจากปุ่มตำแหน่ง ให้ซูมเข้า
                    updateMap(lat, lng, withZoom ? 17 : null);
                },
                function(error) {
                    console.error("เกิดข้อผิดพลาดในการเข้าถึงตำแหน่ง:", error);

                    // ลบ loading
                    const loadingElement = document.getElementById('locationLoading');
                    if (loadingElement) {
                        loadingElement.remove();
                    }

                    // อัปเดตโดยใช้ตำแหน่งเริ่มต้น
                    updateMap(defaultPosition.lat, defaultPosition.lng, withZoom ? 17 : null);

                    // แสดงข้อความเตือน
                    showAlert('warning', 'ไม่สามารถเข้าถึงตำแหน่ง', 'ระบบจะใช้การจำลองตำแหน่งแทน');

                    // ใช้โหมดจำลองแทน
                    useSimulation = true;
                    document.getElementById('toggleModeBtn').setAttribute('data-mode', 'simulation');
                    document.getElementById('toggleModeText').textContent = 'ใช้ GPS จริง';
                    document.getElementById('simulationBadge').textContent = 'โหมดจำลอง';
                    document.getElementById('simulationBadge').className = 'badge bg-info ms-2';
                    document.getElementById('simulationAlert').style.display = 'block';
                    document.getElementById('speedSelectorContainer').style.display = 'block';
                    simulateRunning();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            // เบราว์เซอร์ไม่รองรับ Geolocation
            updateMap(defaultPosition.lat, defaultPosition.lng, withZoom ? 17 : null);

            // ลบ loading
            const loadingElement = document.getElementById('locationLoading');
            if (loadingElement) {
                loadingElement.remove();
            }

            showAlert('warning', 'ไม่สามารถระบุตำแหน่ง', 'เบราว์เซอร์ของคุณไม่รองรับการระบุตำแหน่ง');

            // ใช้โหมดจำลองแทน
            useSimulation = true;
            document.getElementById('toggleModeBtn').setAttribute('data-mode', 'simulation');
            document.getElementById('toggleModeText').textContent = 'ใช้ GPS จริง';
            document.getElementById('simulationBadge').textContent = 'โหมดจำลอง';
            document.getElementById('simulationBadge').className = 'badge bg-info ms-2';
            document.getElementById('simulationAlert').style.display = 'block';
            document.getElementById('speedSelectorContainer').style.display = 'block';
        }
    }

    // ฟังก์ชันอัปเดตแผนที่
    function updateMap(lat, lng, zoomLevel = null) {
        if (!map || !lat || !lng) return;

        // อัปเดตมาร์กเกอร์ตำแหน่งปัจจุบัน
        if (currentPositionMarker) {
            currentPositionMarker.setLatLng([lat, lng]);
        } else {
            currentPositionMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'location-pin',
                    html: '<i class="fas fa-running text-primary" style="font-size: 24px;"></i>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                })
            }).addTo(map);
        }

        // ถ้ากำลังวิ่งอยู่ และมีเส้นทาง ให้ใช้ fitBounds จากเส้นทางแทน
        if (isRunning && routePoints.length > 1 && routePolyline) {
            // map.fitBounds จะถูกเรียกใน addPointToRoute แล้ว
        } else {
            // ถ้ามีการกำหนดระดับการซูม ให้ใช้ setView แทน panTo
            if (zoomLevel !== null) {
                map.setView([lat, lng], zoomLevel, {
                    animate: true,
                    duration: 1
                });
            } else {
                // ถ้าไม่มีเส้นทาง หรือยังไม่ได้เริ่มวิ่ง ให้เลื่อนแผนที่ไปที่ตำแหน่งปัจจุบันโดยไม่ซูม
                map.panTo([lat, lng]);
            }
        }
    }

    // ฟังก์ชันเพิ่มจุดไปยังเส้นทาง
    function addPointToRoute(lat, lng) {
        routePoints.push([lat, lng]);

        // อัปเดตเส้นทางบนแผนที่
        if (routePolyline) {
            routePolyline.setLatLngs(routePoints);
        } else {
            routePolyline = L.polyline(routePoints, {
                color: 'blue',
                weight: 5,
                opacity: 0.7
            }).addTo(map);
        }

        // ปรับขอบเขตแผนที่ให้มองเห็นเส้นทางทั้งหมด
        if (routePoints.length > 1) {
            map.fitBounds(routePolyline.getBounds(), {
                padding: [50, 50],
                maxZoom: 16,
                animate: true
            });
        }
    }

    // ฟังก์ชันยกเลิกการวิ่งโดยไม่บันทึก
    function cancelRunning() {
        if (!isRunning) return;

        isRunning = false;
        isPaused = false;

        // หยุดการจำลองการวิ่ง
        clearInterval(simulationInterval);

        // หยุดการติดตาม GPS
        stopLocationTracking();

        // หยุดนับเวลา
        clearInterval(timer);

        // ส่งคำขอไปยัง server เพื่อยกเลิกการบันทึกข้อมูลการวิ่ง
        if (runId) {
            fetch('{{ url("/run/destroy") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ run_id: runId }),
                // ใช้ keepalive เพื่อให้แน่ใจว่าคำขอจะยังถูกส่งแม้หน้าจะถูกรีโหลด
                keepalive: true
            }).catch(error => {
                console.error('Error canceling run:', error);
            });
        }

        console.log('ยกเลิกการวิ่งโดยไม่บันทึก');
    }

    // ฟังก์ชันสำหรับเริ่มการวิ่ง
    function startRun() {
        if (isRunning && !isPaused) return;

        if (isPaused) {
            // กลับมาวิ่งต่อหลังจากหยุดชั่วคราว
            isPaused = false;
            document.getElementById('pauseRunBtn').innerHTML = '<i class="fas fa-pause-circle me-2"></i>หยุดชั่วคราว';
            document.getElementById('pauseRunBtn').classList.remove('btn-success');
            document.getElementById('pauseRunBtn').classList.add('btn-secondary');
            startTimer();

            // เริ่มการติดตามตำแหน่งอีกครั้ง
            if (useSimulation) {
                // เริ่มการจำลองการวิ่งอีกครั้ง
                simulateRunning();
            } else {
                startLocationTracking();
            }

            // อัปเดตสถานะบน server
            fetch('{{ url("/run/toggle-pause") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    activity_id: runId,
                    is_paused: false
                })
            }).catch(error => {
                console.error('Error resuming run:', error);
            });

            console.log('กลับมาวิ่งต่อแล้ว');
            return;
        }

        // เริ่มการวิ่งครั้งใหม่
        routePoints = [];
        currentDistance = 0;
        elapsedSeconds = 0;
        currentSpeed = useSimulation ? selectedSpeed : 0;
        totalCalories = 0;

        updateRunStats();

        // เปลี่ยนสถานะ UI
        document.getElementById('startRunBtn').style.display = 'none';
        document.querySelector('.running-controls').style.display = 'flex';

        // กำหนดค่าเริ่มต้น
        isRunning = true;
        startTimer();

        // เริ่มบันทึกการวิ่งใหม่ในระบบ
        fetch('{{ url("/run/start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                is_test: useSimulation
            })
        })
        .then(response => {
            // อ่านข้อมูล response ไม่ว่าจะสำเร็จหรือไม่
                return response.text().then(text => {
                try {
                    // พยายามแปลงเป็น JSON
                    const data = JSON.parse(text);
                    // เพิ่มสถานะ HTTP
                    data.httpStatus = response.status;
                    return data;
                } catch (e) {
                    // กรณีที่ response ไม่ใช่ JSON ที่ถูกต้อง
                    return {
                        status: 'error',
                        message: 'ไม่สามารถอ่านข้อมูลจากเซิร์ฟเวอร์ได้: ' + text,
                        httpStatus: response.status
                    };
                }
            });
        })
        .then(data => {
            console.log('Server response:', data);

            if (data.status === 'success') {
                runId = data.activity_id;
                console.log('บันทึกการเริ่มวิ่งแล้ว, activity_id:', runId);

                // เริ่มการติดตามตำแหน่ง
                if (useSimulation) {
                    // เริ่มการจำลองการวิ่ง
                    simulateRunning();

                    // แสดงแจ้งเตือน
                    Swal.fire({
                        icon: 'info',
                        title: 'โหมดจำลองการวิ่ง',
                        text: 'ระบบกำลังจำลองการวิ่งด้วยความเร็ว ' + selectedSpeed + ' กม./ชม.',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                } else {
                    startLocationTracking();
                }
            } else {
                // รีเซ็ตสถานะในกรณีที่เกิดข้อผิดพลาด
                isRunning = false;
                document.getElementById('startRunBtn').style.display = 'block';
                document.querySelector('.running-controls').style.display = 'none';
                clearInterval(timer);

                // ถ้าเป็นข้อผิดพลาดเกี่ยวกับกิจกรรมที่เริ่มไปแล้ว ให้แสดงตัวเลือก
                if (data.message && data.message.includes('กิจกรรมการวิ่งเริ่มต้นไปแล้ว')) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'พบกิจกรรมที่กำลังดำเนินการอยู่',
                        text: 'คุณมีกิจกรรมการวิ่งที่ยังไม่เสร็จสิ้น ต้องการดำเนินการอย่างไร?',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'จบกิจกรรมปัจจุบัน',
                        denyButtonText: `ดำเนินการต่อ`,
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // จบกิจกรรมที่ค้างอยู่
                            finishPendingActivity(data.activity_id);
                        } else if (result.isDenied && data.activity_id) {
                            // กลับไปยังกิจกรรมที่กำลังวิ่งอยู่
                            resumePendingActivity(data.activity_id);
                        }
                    });
                } else {
                    // ข้อผิดพลาดอื่นๆ
                    showAlert('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถเริ่มการวิ่งได้');
                }
            }
        })
        .catch(error => {
            console.error('Error starting run:', error);

            // รีเซ็ตสถานะ
            isRunning = false;
            document.getElementById('startRunBtn').style.display = 'block';
            document.querySelector('.running-controls').style.display = 'none';
            clearInterval(timer);

            showAlert('error', 'เกิดข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเริ่มการวิ่ง โปรดลองอีกครั้ง: ' + error.message);
        });
    }

    // ฟังก์ชันสำหรับจบกิจกรรมที่ค้างอยู่
    function finishPendingActivity(activityId) {
        if (!activityId) {
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่พบรหัสกิจกรรมที่ต้องการจบ');
            return;
        }

        console.log('Finishing pending activity:', activityId);

        Swal.fire({
            title: 'กำลังจบกิจกรรม',
            text: 'กำลังจบกิจกรรมที่ค้างอยู่...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ใช้เส้นทางว่างสำหรับการปิดกิจกรรมแบบฉุกเฉิน
        const emptyRouteData = JSON.stringify([]);

        try {
            fetch('{{ url("/run/finish") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    activity_id: activityId, // ส่ง activity_id อย่างชัดเจน
                    route_data: emptyRouteData,
                    distance: 0,
                    duration: 0,
                    calories: 0,
                    average_speed: 0,
                    is_test: false
                })
            })
            .then(response => {
                // ตรวจสอบประเภทของ content-type ที่ได้รับกลับมา
                const contentType = response.headers.get('content-type');

                // อ่านข้อมูล response และตรวจสอบว่าเป็น JSON หรือไม่
                return response.text().then(text => {
                    console.log('Response status:', response.status);
                    console.log('Response content-type:', contentType);
                    console.log('Response text sample:', text.substring(0, 200));

                    // ถ้า response สำเร็จและเป็น JSON ให้แปลงเป็น object
                    if (response.ok && contentType && contentType.includes('application/json')) {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            return {
                                status: 'error',
                                message: 'ไม่สามารถอ่านข้อมูลจากเซิร์ฟเวอร์ได้: ' + text.substring(0, 100)
                            };
                        }
                    } else {
                        // สำหรับ response ที่ไม่ใช่ JSON หรือมีรหัสสถานะผิดพลาด
                        return {
                            status: 'error',
                            message: 'เซิร์ฟเวอร์ตอบกลับในรูปแบบที่ไม่ถูกต้อง',
                            details: text.substring(0, 200)
                        };
                    }
                });
            })
            .then(data => {
                Swal.close();
                console.log('Finish pending activity response:', data);

                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'จบกิจกรรมที่ค้างอยู่แล้ว คุณสามารถเริ่มกิจกรรมใหม่ได้',
                        showConfirmButton: true
                    }).then(() => {
                        // รีเฟรชหน้าเพื่อเริ่มต้นใหม่
                        window.location.reload();
                    });
                } else {
                    // แสดงรายละเอียดข้อผิดพลาดมากขึ้นสำหรับการดีบัก
                    let errorMsg = data.message || 'ไม่สามารถจบกิจกรรมได้';
                    if (data.details) {
                        console.error('Error details:', data.details);
                    }

                    showAlert('error', 'เกิดข้อผิดพลาด', errorMsg);

                    // ให้โอกาสผู้ใช้ลองใหม่อีกครั้ง หรือรีเฟรชหน้า
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error finishing activity:', error);
                showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถจบกิจกรรมได้: ' + error.message);

                // ให้โอกาสผู้ใช้รีเฟรชหน้าเพื่อลองใหม่
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            });
        } catch (error) {
            Swal.close();
            console.error('Exception during finish activity request:', error);
            showAlert('error', 'เกิดข้อผิดพลาด', 'เกิดข้อผิดพลาดในการส่งคำขอ: ' + error.message);

            // ให้โอกาสผู้ใช้รีเฟรชหน้าเพื่อลองใหม่
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
    }

    // ฟังก์ชันสำหรับกลับไปดำเนินการกิจกรรมที่ค้างอยู่
    function resumePendingActivity(activityId) {
        runId = activityId;
        isRunning = true;
        startLocationTracking();
    }

    // ฟังก์ชันสำหรับหยุดชั่วคราว
    function pauseRun() {
        if (!isRunning) return;

        if (isPaused) {
            // กลับมาวิ่งต่อ
            isPaused = false;
            document.getElementById('pauseRunBtn').innerHTML = '<i class="fas fa-pause-circle me-2"></i>หยุดชั่วคราว';
            document.getElementById('pauseRunBtn').classList.remove('btn-success');
            document.getElementById('pauseRunBtn').classList.add('btn-secondary');
            startTimer();

            // เริ่มการติดตามตำแหน่งหรือการจำลองอีกครั้ง
            if (useSimulation) {
                simulateRunning();
            } else {
                startLocationTracking();
            }

            // อัปเดตสถานะบน server
            fetch('{{ url("/run/toggle-pause") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    run_id: runId,
                    is_paused: false
                })
            }).catch(error => {
                console.error('Error resuming run:', error);
            });

            console.log('กลับมาวิ่งต่อแล้ว');
        } else {
            // หยุดชั่วคราว
            isPaused = true;
            document.getElementById('pauseRunBtn').innerHTML = '<i class="fas fa-play-circle me-2"></i>วิ่งต่อ';
            document.getElementById('pauseRunBtn').classList.remove('btn-secondary');
            document.getElementById('pauseRunBtn').classList.add('btn-success');
            clearInterval(timer);

            // หยุดการติดตามตำแหน่งหรือการจำลอง
            if (useSimulation) {
                clearInterval(simulationInterval);
            } else {
                stopLocationTracking();
            }

            // อัปเดตสถานะบน server
            fetch('{{ url("/run/toggle-pause") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    run_id: runId,
                    is_paused: true
                })
            }).catch(error => {
                console.error('Error pausing run:', error);
            });

            console.log('หยุดชั่วคราวแล้ว');
        }
    }

    // ฟังก์ชันสำหรับจบการวิ่ง
    function stopRun() {
        if (!isRunning) return;

        // หยุดนับเวลา
        clearInterval(timer);

        // หยุดการติดตามตำแหน่งหรือการจำลอง
        if (useSimulation) {
            // หยุดการจำลองการวิ่ง
            clearInterval(simulationInterval);
        } else {
            stopLocationTracking();
        }

        // เตรียมข้อมูลเส้นทาง
        let routeDataFormatted = [];
        try {
            // เตรียมข้อมูลเส้นทางในรูปแบบที่ถูกต้อง
            routeDataFormatted = routePoints.map(point => ({
            lat: point[0],
            lng: point[1],
            timestamp: Date.now()
            }));

            // ถ้าไม่มีข้อมูลเส้นทาง ให้ใช้อาร์เรย์ว่าง
            if (routeDataFormatted.length === 0) {
                routeDataFormatted = [];
            }
        } catch (error) {
            console.error('Error formatting route data:', error);
            routeDataFormatted = [];
        }

        // แปลงเป็น JSON string
        const routeDataJSON = JSON.stringify(routeDataFormatted);

        console.log('Prepared route data:', {
            routePoints: routePoints.length,
            routeDataJSON: routeDataJSON.substring(0, 100) + (routeDataJSON.length > 100 ? '...' : '')
        });

        // แสดง loading
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล',
            text: 'โปรดรอสักครู่...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        console.log('Sending finish request for activity_id:', runId, {
            activity_id: runId,
            distance: currentDistance,
            duration: elapsedSeconds,
            calories: totalCalories,
            average_speed: currentSpeed,
            is_test: useSimulation
        });

        // ส่งข้อมูลไปบันทึก
        try {
        fetch('{{ url("/run/finish") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                    activity_id: runId,
                route_data: routeDataJSON,
                distance: currentDistance,
                duration: elapsedSeconds,
                calories: totalCalories,
                average_speed: currentSpeed,
                    is_test: useSimulation
            })
        })
        .then(response => {
                // ตรวจสอบประเภทของ content-type ที่ได้รับกลับมา
                const contentType = response.headers.get('content-type');

                // อ่านข้อมูล response และตรวจสอบว่าเป็น JSON หรือไม่
                return response.text().then(text => {
                    console.log('Response status:', response.status);
                    console.log('Response content-type:', contentType);
                    console.log('Response text sample:', text.substring(0, 200) + (text.length > 200 ? '...' : ''));

                    // ถ้า response สำเร็จและเป็น JSON ให้แปลงเป็น object
                    if (response.ok && contentType && contentType.includes('application/json')) {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Error parsing JSON response:', e);
                            return {
                                status: 'error',
                                message: 'ไม่สามารถอ่านข้อมูลจากเซิร์ฟเวอร์ได้: ' + text.substring(0, 100),
                                httpStatus: response.status
                            };
            }
                    } else {
                        // สำหรับ response ที่ไม่ใช่ JSON หรือมีรหัสสถานะผิดพลาด
                        return {
                            status: 'error',
                            message: 'เซิร์ฟเวอร์ตอบกลับในรูปแบบที่ไม่ถูกต้อง',
                            details: text.substring(0, 200),
                            httpStatus: response.status
                        };
                    }
                });
        })
        .then(data => {
            // ปิด loading
            Swal.close();

                console.log('Server response for finish:', data);

            if (data.status === 'success') {
                // แสดงสรุปการวิ่ง
                showRunSummary(data.activity, data.achievements || []);

                // รีเซ็ตตัวแปรที่เกี่ยวข้อง
                isRunning = false;
                isPaused = false;
                runId = null;

                // รีเซ็ต UI
                document.getElementById('startRunBtn').style.display = 'block';
                document.querySelector('.running-controls').style.display = 'none';
            } else {
                    // แสดงข้อผิดพลาด
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: data.message || 'ไม่สามารถบันทึกการวิ่งได้',
                    confirmButtonColor: '#3085d6'
                });

                    // รีเซ็ตสถานะของปุ่ม
                    document.getElementById('startRunBtn').style.display = 'block';
                    document.querySelector('.running-controls').style.display = 'none';
                    isRunning = false;
                    isPaused = false;
            }
        })
        .catch(error => {
            // ปิด loading
            Swal.close();

            console.error('Error stopping run:', error);

                // แสดงข้อผิดพลาด
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการจบการวิ่ง โปรดลองอีกครั้ง: ' + error.message,
                confirmButtonColor: '#3085d6'
            });

                // รีเซ็ตสถานะของปุ่ม
                document.getElementById('startRunBtn').style.display = 'block';
                document.querySelector('.running-controls').style.display = 'none';
                isRunning = false;
                isPaused = false;
            });
        } catch (error) {
            // ปิด loading
            Swal.close();
            console.error('Exception during stopRun request:', error);

            // แสดงข้อผิดพลาด
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการส่งคำขอ: ' + error.message,
                confirmButtonColor: '#3085d6'
            });

            // รีเซ็ตสถานะของปุ่ม
            document.getElementById('startRunBtn').style.display = 'block';
            document.querySelector('.running-controls').style.display = 'none';
            isRunning = false;
            isPaused = false;
        }
    }

    // ฟังก์ชันสำหรับแสดงสรุปการวิ่ง
    function showRunSummary(run, achievements) {
        document.getElementById('summaryDistance').textContent = (parseFloat(run.distance) || 0).toFixed(2) + ' กม.';

        const hours = Math.floor(run.duration / 3600);
        const minutes = Math.floor((run.duration % 3600) / 60);
        const seconds = run.duration % 60;
        document.getElementById('summaryTime').textContent =
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');

        document.getElementById('summaryCalories').textContent = run.calories_burned + ' kcal';

        // ตรวจสอบว่าปุ่มบันทึกมองเห็นได้
        document.getElementById('saveActivityBtn').style.display = 'block';

        // แสดงเส้นทาง
        setTimeout(() => {
            try {
            // ทำลายแผนที่เดิมถ้ามี (เพื่อป้องกันปัญหา ID ซ้ำ)
            if (window.summaryMapInstance) {
                window.summaryMapInstance.remove();
            }

            const summaryMapElement = document.getElementById('summaryMap');
            if (!summaryMapElement) {
                console.error('Summary map element not found');
                return;
            }

                // สร้างแผนที่ใหม่
            const summaryMap = L.map('summaryMap').setView([defaultPosition.lat, defaultPosition.lng], 13);
            window.summaryMapInstance = summaryMap;

                // เพิ่ม tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
                    }).addTo(summaryMap);

            // เตรียมข้อมูลเส้นทาง
            let points = [];

            // ใช้ข้อมูลจาก routePoints ที่บันทึกระหว่างการวิ่ง
            if (routePoints && routePoints.length > 0) {
                points = routePoints;
            }
            // หรือใช้ข้อมูลจาก run.route_data ถ้ามี
            else if (run.route_data && Array.isArray(run.route_data) && run.route_data.length > 0) {
                points = run.route_data.map(point => [point.lat, point.lng]);
            }

            if (points && points.length > 0) {
                        const polyline = L.polyline(points, {
                            color: 'blue',
                            weight: 5,
                            opacity: 0.7
                        }).addTo(summaryMap);

                // ให้แผนที่แสดงทั้งเส้นทาง
                summaryMap.fitBounds(polyline.getBounds(), {
                    padding: [50, 50],
                    maxZoom: 16
                });

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
            } else {
                // ถ้าไม่มีข้อมูลเส้นทาง แสดงตำแหน่งเริ่มต้น
                L.marker([defaultPosition.lat, defaultPosition.lng], {
                    icon: L.divIcon({
                        className: 'location-pin',
                        html: '<i class="fas fa-running text-primary" style="font-size: 24px;"></i>',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            })
                        }).addTo(summaryMap);
                    }

            // ทำให้แผนที่ refresh เพื่อแสดงผลอย่างถูกต้อง
            setTimeout(() => {
                if (summaryMap) {
                    summaryMap.invalidateSize();
                }
                }, 200);
            } catch (error) {
                console.error('Error creating summary map:', error);
            }
        }, 300);

        // แสดงโมดัล
        const modal = new bootstrap.Modal(document.getElementById('activitySummaryModal'));
        modal.show();
    }

    // ฟังก์ชันบันทึกกิจกรรมการวิ่ง
    function saveActivity() {
        // แสดง loading state
        Swal.fire({
            title: 'กำลังบันทึก',
            text: 'กำลังบันทึกข้อมูลการวิ่ง...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // เตรียมข้อมูลเส้นทาง
        let routeDataFormatted = [];
        try {
            // เตรียมข้อมูลเส้นทางในรูปแบบที่ถูกต้อง
            routeDataFormatted = routePoints.map(point => ({
                lat: point[0],
                lng: point[1],
                timestamp: Date.now()
            }));
        } catch (error) {
            console.error('Error formatting route data:', error);
            routeDataFormatted = [];
        }

        // แปลงเป็น JSON string
        const routeDataJSON = JSON.stringify(routeDataFormatted);

        // ส่งข้อมูลไปบันทึกที่ API endpoint
        fetch('{{ route("run.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                distance: currentDistance,
                duration: elapsedSeconds,
                calories_burned: totalCalories,
                average_speed: currentSpeed,
                route_data: routeDataJSON,
                is_test: useSimulation
            })
        })
        .then(response => {
            // อ่านข้อมูล response ไม่ว่าจะสำเร็จหรือไม่
            return response.text().then(text => {
                console.log('Response status:', response.status);
                console.log('Response text:', text.substring(0, 200) + (text.length > 200 ? '...' : ''));

                try {
                    // พยายามแปลงเป็น JSON
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    // กรณีที่ response ไม่ใช่ JSON ที่ถูกต้อง
                    return {
                        status: 'error',
                        message: 'ไม่สามารถอ่านข้อมูลจากเซิร์ฟเวอร์ได้: ' + text.substring(0, 100)
                    };
                }
            });
        })
        .then(data => {
            // ปิด loading
            Swal.close();

            if (data.status === 'success' || data.success === true) {
            // เมื่อบันทึกเสร็จแล้ว แสดงข้อความสำเร็จ
        Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                text: 'บันทึกข้อมูลการวิ่งเรียบร้อยแล้ว',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // ปิด modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('activitySummaryModal'));
                    if (modal) {
                modal.hide();
                    }

                // รีเฟรชหน้าเพื่ออัปเดตประวัติการวิ่ง
                window.location.reload();
            });
            } else {
                // แสดงข้อผิดพลาด
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: data.message || 'ไม่สามารถบันทึกข้อมูลการวิ่งได้',
                    confirmButtonColor: '#3085d6'
                });
            }
        })
        .catch(error => {
            // ปิด loading
            Swal.close();

            console.error('Error saving run:', error);

            // แสดงข้อผิดพลาด
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' + error.message,
                confirmButtonColor: '#3085d6'
            });
        });
    }

    // ฟังก์ชันสำหรับแสดงสรุปการวิ่ง
    function checkForActiveActivity() {
        fetch('{{ url("/run/check-active") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.has_active && data.activity_id) {
                // แสดงการแจ้งเตือนว่ามีกิจกรรมที่ค้างอยู่
                Swal.fire({
                    icon: 'warning',
                    title: 'พบกิจกรรมที่ยังไม่เสร็จสิ้น',
                    text: 'คุณมีกิจกรรมการวิ่งที่ยังไม่เสร็จสิ้น ต้องการดำเนินการอย่างไร?',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'จบกิจกรรม',
                    denyButtonText: 'ดำเนินการต่อ',
                    cancelButtonText: 'ไม่ทำอะไร'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // จบกิจกรรมที่ค้างอยู่
                        finishPendingActivity(data.activity_id);
                    } else if (result.isDenied) {
                        // ดำเนินการต่อกับกิจกรรมที่ค้างอยู่
                        resumePendingActivity(data.activity_id);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error checking for active activities:', error);
        });
    }
</script>
@endsection

