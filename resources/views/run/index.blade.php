@extends('layouts.app')

@section('title', 'เริ่มวิ่ง')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- การ์ดสำหรับการวิ่ง -->
            <div class="card gofit-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold fs-5">บันทึกกิจกรรมการวิ่ง <span id="simulationBadge" class="badge bg-info ms-2">โหมดจำลอง</span></h4>
                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <button id="toggleModeBtn" class="btn btn-outline-primary rounded-pill px-3 py-2" data-mode="simulation">
                            <i class="fas fa-exchange-alt me-1"></i> <span id="toggleModeText">ใช้ GPS จริง</span>
                        </button>
                        <a href="{{ route('run.history') }}" class="btn btn-outline-primary rounded-pill px-3 py-2">
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
                                <button id="stopRunBtn" class="btn btn-danger text-white btn-lg w-100">
                                    <i class="fas fa-stop-circle me-2"></i>จบการวิ่ง
                                </button>
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
    <link href="{{ asset('css/run-mobile.css') }}" rel="stylesheet">
<!-- Pre-load Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<style>
        /* เพิ่มสไตล์เฉพาะหน้านี้ (ถ้ามี) */
        #map, #summaryMap {
        z-index: 1;
        overflow: hidden;
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
            console.log('DOM loaded, initializing app...');

        // ตั้งค่าแผนที่หลัก
        initMap();

            console.log('Map initialized');

        // ตั้งค่า Event Listeners
        setupEventListeners();

            // ขอตำแหน่งปัจจุบันและอัปเดตแผนที่
            getCurrentLocation();

            // ตรวจสอบว่ามีกิจกรรมที่ยังไม่เสร็จสิ้นหรือไม่
            console.log('เริ่มตรวจสอบกิจกรรมที่ยังไม่เสร็จสิ้น...');
            checkForActiveActivity();
            console.log('เรียกใช้ฟังก์ชัน checkForActiveActivity แล้ว');

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

        // ถ้ากำลังวิ่งอยู่ ไม่อนุญาตให้เปลี่ยนโหมด
        if (isRunning) {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถเปลี่ยนโหมดได้',
                text: 'ไม่สามารถเปลี่ยนโหมดขณะกำลังวิ่ง โปรดหยุดการวิ่งก่อน',
                confirmButtonColor: '#2ecc71'
            });
            return;
        }

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

        // อัปเดตสถานะปุ่มสลับโหมดเมื่อโหลดหน้า
        updateToggleModeButton();
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
                // ถ้าไม่มีเส้นทาง หรือยังไม่เริ่มวิ่ง ให้เลื่อนแผนที่ไปที่ตำแหน่งปัจจุบันโดยไม่ซูม
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

    // ฟังก์ชันสำหรับอัปเดตข้อมูลการวิ่งบนหน้าจอ
    function updateRunStats() {
        // อัปเดตระยะทาง
        document.getElementById('distance').textContent = currentDistance.toFixed(2);

        // อัปเดตเวลา
        document.getElementById('time').textContent = formatTime(elapsedSeconds);

        // อัปเดตความเร็ว
        document.getElementById('speed').textContent = currentSpeed.toFixed(1);

        // อัปเดตแคลอรี่ - แสดงเป็นจำนวนเต็มเสมอ
        document.getElementById('calories').textContent = Math.round(totalCalories);

        // แสดงการเปลี่ยนแปลงด้วยอนิเมชั่น
        animateCalorieChange();
    }

    // ฟังก์ชันแสดงอนิเมชั่นเมื่อแคลอรี่เปลี่ยนแปลง
    function animateCalorieChange() {
        // เคลียร์ timeout เดิมถ้ามี
        if (calorieAnimationTimeout) {
            clearTimeout(calorieAnimationTimeout);
        }

        // แสดงอนิเมชั่นการเพิ่มของแคลอรี่
        const calorieAnimation = document.getElementById('calorieAnimation');
        if (calorieAnimation) {
            const height = Math.min(80, totalCalories / 5); // สูงสุด 80%
            calorieAnimation.style.height = height + '%';

            // รีเซ็ตอนิเมชั่นหลังจาก 1.5 วินาที
            calorieAnimationTimeout = setTimeout(() => {
                calorieAnimation.style.height = '0%';
            }, 1500);
        }
    }

    // ฟังก์ชันสำหรับเริ่มจับเวลา
    function startTimer() {
        // หยุดเวลาเดิม (ถ้ามี)
        if (timer) clearInterval(timer);

        // บันทึกเวลาเริ่มต้นของ JavaScript ณ ปัจจุบันเพื่อคำนวณเวลาที่ผ่านไปอย่างแม่นยำ
        const timerStartTime = Date.now() - (elapsedSeconds * 1000);

        timer = setInterval(function() {
            // คำนวณเวลาที่ผ่านไปจากเวลาเริ่มต้น
            const currentTime = Date.now();
            elapsedSeconds = Math.floor((currentTime - timerStartTime) / 1000);

            // คำนวณระยะทางและแคลอรี่ถ้าอยู่ในโหมดจำลอง
            if (useSimulation && !isPaused) {
                // คำนวณระยะทางตามความเร็วที่เลือก (km/h)
                const hoursPassed = elapsedSeconds / 3600;
                currentDistance = selectedSpeed * hoursPassed;

                // คำนวณแคลอรี่เผาผลาญ (ประมาณการณ์)
                // ใช้สูตร: แคลอรี่ = น้ำหนัก (kg) * ระยะทาง (km) * ค่าคงที่
                // ค่าคงที่ประมาณ 1.036 สำหรับการวิ่ง
                totalCalories = userWeight * currentDistance * 1.036;
            }

            // อัปเดตค่าบนหน้าจอ
            updateRunStats();
        }, 1000);
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
                    resetRunState();
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

        // ปิดไม่ให้เปลี่ยนโหมดในขณะวิ่ง
        updateToggleModeButton();
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
        // แสดงการ loading
        Swal.fire({
            title: 'กำลังโหลดข้อมูลกิจกรรม',
            text: 'กรุณารอสักครู่...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // เรียกข้อมูลรายละเอียดของกิจกรรมจาก server
        fetch('{{ url("/run/check-active") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();

            console.log('ข้อมูลกิจกรรมค้าง:', data);

            if (data.has_active) {
                // บันทึกข้อมูลจาก server
                runId = data.activity_id;

                // กำหนดเวลาที่ผ่านไปจากข้อมูลบน server
                if (data.elapsed_time && data.elapsed_time > 0) {
                    elapsedSeconds = parseInt(data.elapsed_time);
                    console.log('กำหนดเวลาที่ผ่านไปเป็น ' + elapsedSeconds + ' วินาที');
                }

                // โหลดข้อมูลเส้นทาง
                if (data.route_data) {
                    try {
                        if (typeof data.route_data === 'string') {
                            let routeData = JSON.parse(data.route_data);
                            if (Array.isArray(routeData)) {
                                routePoints = routeData.map(point => [point.lat, point.lng]);
                            }
                        } else if (Array.isArray(data.route_data)) {
                            routePoints = data.route_data.map(point => [point.lat, point.lng]);
                        }
                    } catch (error) {
                        console.error('Error parsing route data:', error);
                    }
                }

                // อัปเดตค่าต่างๆ
                currentDistance = data.distance || 0;
                totalCalories = data.calories_burned || 0;
                currentSpeed = data.average_speed || 0;
                useSimulation = data.is_test || false;

                // อัปเดต UI
                document.getElementById('distance').innerText = currentDistance.toFixed(2);
                document.getElementById('time').innerText = formatTime(elapsedSeconds);
                document.getElementById('speed').innerText = currentSpeed.toFixed(1);
                document.getElementById('calories').innerText = Math.round(totalCalories);

                // แสดงหน้าจอการวิ่ง
                document.getElementById('startRunBtn').style.display = 'none';
                document.querySelector('.running-controls').style.display = 'block';

                // เริ่มต้นการวิ่งอีกครั้ง
                isRunning = true;
                isPaused = false;

                // เริ่มนับเวลาใหม่
                startTimer();

                // เริ่มการจำลองหรือติดตามตำแหน่งอีกครั้ง
                if (useSimulation) {
                    // อัปเดตปุ่มสลับโหมด
                    document.getElementById('toggleModeBtn').classList.remove('btn-primary');
                    document.getElementById('toggleModeBtn').classList.add('btn-warning');
                    document.getElementById('toggleModeBtn').innerHTML = '<i class="fas fa-running me-2"></i>โหมดจำลอง';

                    // เริ่มการจำลอง
                    simulateRunning();
                } else {
                    // อัปเดตปุ่มสลับโหมด
                    document.getElementById('toggleModeBtn').classList.remove('btn-warning');
                    document.getElementById('toggleModeBtn').classList.add('btn-primary');
                    document.getElementById('toggleModeBtn').innerHTML = '<i class="fas fa-map-marker-alt me-2"></i>ใช้ GPS จริง';

                    // เริ่มการติดตามตำแหน่ง
                    startLocationTracking();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'ดำเนินการต่อ',
                    text: 'กำลังกลับไปดำเนินการกิจกรรมที่ค้างอยู่',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                // ไม่พบกิจกรรมค้าง
                Swal.fire({
                    icon: 'info',
                    title: 'ไม่พบกิจกรรมค้าง',
                    text: 'ไม่พบกิจกรรมที่ยังไม่เสร็จสิ้น'
                });
            }
        })
        .catch(error => {
            console.error('Error fetching activity data:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลกิจกรรมได้: ' + error.message
            });
        });
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

            // เริ่มจับเวลาต่อโดยใช้เวลาที่เก็บไว้ล่าสุด
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

            console.log('กลับมาวิ่งต่อแล้ว เวลาปัจจุบัน:', elapsedSeconds, 'วินาที');
        } else {
            // หยุดชั่วคราว
            isPaused = true;
            document.getElementById('pauseRunBtn').innerHTML = '<i class="fas fa-play-circle me-2"></i>วิ่งต่อ';
            document.getElementById('pauseRunBtn').classList.remove('btn-secondary');
            document.getElementById('pauseRunBtn').classList.add('btn-success');

            // บันทึกเวลาล่าสุดก่อนหยุด
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

            console.log('หยุดชั่วคราวแล้ว เวลาที่หยุด:', elapsedSeconds, 'วินาที');
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
                distance: parseFloat(currentDistance.toFixed(2)),
                duration: elapsedSeconds,
                calories: Math.round(totalCalories),
                average_speed: parseFloat(currentSpeed.toFixed(1)),
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

                // อัปเดตสถานะปุ่มสลับโหมด
                updateToggleModeButton();
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

        // แก้ไขการแสดงค่าแคลอรี่ - ปัดเป็นจำนวนเต็ม
        document.getElementById('summaryCalories').textContent = Math.round(parseFloat(run.calories_burned) || 0) + ' kcal';

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
                calories_burned: Math.round(totalCalories),
                average_speed: parseFloat(currentSpeed.toFixed(1)),
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
        console.log('===== กำลังตรวจสอบกิจกรรมที่ยังไม่เสร็จสิ้น... =====');

        fetch('{{ url("/run/check-active") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                console.error('Server response error:', response.status);
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) {
                console.log('ไม่ได้รับข้อมูลจาก server');
                return;
            }

            console.log('ผลการตรวจสอบกิจกรรมที่ยังไม่เสร็จสิ้น:', data);

            if (data.has_active) {
                console.log('พบกิจกรรมที่ยังไม่เสร็จสิ้น, กำลังแสดง SweetAlert...');
                // คำนวณเวลาที่ผ่านไปเป็นรูปแบบที่อ่านง่าย
                const elapsedTimeFormatted = formatTime(data.elapsed_time || 0);
                const distance = parseFloat(data.distance || 0).toFixed(2);

                // แสดง SweetAlert ถามว่าต้องการทำอย่างไรกับกิจกรรมที่ยังไม่เสร็จสิ้น
                Swal.fire({
                    title: 'พบกิจกรรมที่ยังไม่เสร็จสิ้น',
                    html: `<p>พบกิจกรรมการวิ่งที่ยังไม่เสร็จสิ้น:</p>
                          <p>ระยะทาง: <strong>${distance} กม.</strong></p>
                          <p>เวลาที่ใช้: <strong>${elapsedTimeFormatted}</strong></p>
                          <p>คุณต้องการทำอย่างไรต่อไป?</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'ดำเนินการต่อ',
                    denyButtonText: 'จบการวิ่ง',
                    cancelButtonText: 'ไม่ทำอะไร',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ดำเนินการต่อ
                        resumePendingActivity(data.activity_id);
                    } else if (result.isDenied) {
                        // จบการวิ่ง (finish activity with no additional data)
                        finishPendingActivity(data.activity_id);
                    }
                    // ถ้ากด cancel จะไม่ทำอะไร
                });
            }
        })
        .catch(error => {
            console.error('Error checking active activities:', error);
        });
    }

    // ฟังก์ชันสำหรับจบกิจกรรมที่ค้างอยู่
    function finishPendingActivity(activityId) {
        // แสดง loading
        Swal.fire({
            title: 'กำลังจบกิจกรรม',
            text: 'กรุณารอสักครู่...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ส่งคำขอไปยังเซิร์ฟเวอร์
        fetch('{{ url("/run/finish") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                activity_id: activityId,
                route_data: '[]',
                distance: 0,
                duration: 0,
                calories: 0,
                average_speed: 0
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();

            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'จบกิจกรรมเรียบร้อย',
                    text: 'กิจกรรมที่ค้างอยู่ถูกจบเรียบร้อยแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: data.message || 'ไม่สามารถจบกิจกรรมได้'
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error finishing activity:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถจบกิจกรรมได้: ' + error.message
            });
        });
    }

    // ฟังก์ชันสำหรับเริ่มการติดตาม GPS
    function startLocationTracking() {
        // หยุดการติดตามเดิมก่อน (ถ้ามี)
        stopLocationTracking();

        // เริ่มติดตามตำแหน่งใหม่
        if (navigator.geolocation) {
            watchPositionId = navigator.geolocation.watchPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // อัปเดตตำแหน่งบนแผนที่
                    updateMap(lat, lng);

                    // เพิ่มจุดไปยังเส้นทาง
                    if (routePoints.length === 0) {
                        // จุดแรก
                        addPointToRoute(lat, lng);
                    } else {
                        // คำนวณระยะทางจากจุดก่อนหน้า
                        const lastPoint = routePoints[routePoints.length - 1];
                        const distanceFromLast = calculateDistance(
                            lastPoint[0], lastPoint[1], lat, lng
                        );

                        // เพิ่มจุดใหม่เมื่อเคลื่อนที่ได้ระยะทางอย่างน้อย 5 เมตร
                        if (distanceFromLast >= 0.005) {
                            addPointToRoute(lat, lng);

                            // เพิ่มระยะทางอย่างสม่ำเสมอ - ไม่ให้มีการลดลง
                            currentDistance += distanceFromLast;

                            // คำนวณความเร็วปัจจุบัน (km/h)
                            if (position.coords.speed && position.coords.speed > 0) {
                                // ถ้า API ให้ข้อมูลความเร็ว
                                currentSpeed = position.coords.speed * 3.6; // แปลงจาก m/s เป็น km/h
                            } else {
                                // คำนวณความเร็วจากระยะทางและเวลา
                                const timeDiff = elapsedSeconds > 0 ? elapsedSeconds : 1;
                                currentSpeed = (currentDistance / timeDiff) * 3600;
                            }

                            // ประมาณแคลอรี่ที่เผาผลาญ - คำนวณจากระยะทางรวมเท่านั้น ไม่มีการลดลง
                            totalCalories = userWeight * currentDistance * 1.036;

                            // อัปเดตค่าบนหน้าจอ
                            updateRunStats();

                            // อัปเดตข้อมูลบน server (เป็นระยะๆ)
                            updateServerData();
                        }
                    }
                },
                function(error) {
                    console.error("Error tracking position:", error);

                    // ถ้าเกิดข้อผิดพลาด ให้เปลี่ยนเป็นโหมดจำลอง
                    if (!useSimulation) {
        Swal.fire({
                            icon: 'warning',
                            title: 'ไม่สามารถติดตามตำแหน่งได้',
                            text: 'ระบบจะเปลี่ยนเป็นโหมดจำลองแทน',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });

                        // เปลี่ยนเป็นโหมดจำลอง
                        useSimulation = true;
                        document.getElementById('toggleModeBtn').setAttribute('data-mode', 'simulation');
                        document.getElementById('toggleModeText').textContent = 'ใช้ GPS จริง';
                        document.getElementById('simulationBadge').textContent = 'โหมดจำลอง';
                        document.getElementById('simulationBadge').className = 'badge bg-info ms-2';
                        document.getElementById('simulationAlert').style.display = 'block';
                        document.getElementById('speedSelectorContainer').style.display = 'block';

                        // เริ่มการจำลอง
                        simulateRunning();
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            // เบราว์เซอร์ไม่รองรับ geolocation
            showAlert('warning', 'ไม่รองรับการระบุตำแหน่ง', 'เบราว์เซอร์ของคุณไม่รองรับการติดตาม GPS');

            // เปลี่ยนเป็นโหมดจำลอง
            useSimulation = true;
            simulateRunning();
        }
    }

    // ฟังก์ชันสำหรับหยุดการติดตาม GPS
    function stopLocationTracking() {
        if (watchPositionId !== null) {
            navigator.geolocation.clearWatch(watchPositionId);
            watchPositionId = null;
        }
    }

    // ฟังก์ชันจำลองการวิ่ง
    function simulateRunning() {
        // ยกเลิกการจำลองที่กำลังทำงานอยู่ (ถ้ามี)
        if (simulationInterval) {
            clearInterval(simulationInterval);
        }

        // ถ้าไม่ได้กำลังวิ่งหรือกำลังหยุดชั่วคราว ไม่ต้องเริ่มจำลอง
        if (!isRunning || isPaused) return;

        // ตำแหน่งเริ่มต้นคือตำแหน่งปัจจุบันหรือตำแหน่งเริ่มต้น
        let currentLat = defaultPosition.lat;
        let currentLng = defaultPosition.lng;

        // ถ้ามีตำแหน่งปัจจุบันบนแผนที่แล้ว ให้ใช้ตำแหน่งนั้น
        if (currentPositionMarker) {
            const pos = currentPositionMarker.getLatLng();
            currentLat = pos.lat;
            currentLng = pos.lng;
        }

        // ถ้ายังไม่มีจุดในเส้นทาง ให้เพิ่มจุดเริ่มต้น
        if (routePoints.length === 0) {
            addPointToRoute(currentLat, currentLng);
        }

        // สร้างทิศทางแบบสุ่มเริ่มต้น (ในรูปเรเดียน)
        let direction = Math.random() * Math.PI * 2;

        // จำนวนครั้งที่จำลองการวิ่ง (เพื่อใช้ในการคำนวณระยะทางที่ถูกต้อง)
        let simulationCount = 0;

        // เริ่มการจำลองการวิ่ง - อัปเดตตำแหน่งทุก 2 วินาที
        simulationInterval = setInterval(() => {
            simulationCount++;

            // คำนวณความเร็วในหน่วย องศาละติจูด/ลองจิจูดต่อการอัปเดต
            // ความเร็ว km/h แปลงเป็นระยะทางต่อช่วงเวลาอัปเดต
            const speedPerUpdate = selectedSpeed / 3600 * 2; // ความเร็วต่อ 2 วินาที

            // แปลงความเร็วเป็นระยะต่อการอัปเดต (ประมาณการณ์)
            // 1 ละติจูด = ประมาณ 110 กม., 1 ลองจิจูด = ประมาณ 111 กม. * cos(ละติจูด)
            const latDistance = speedPerUpdate / 110;
            const lngDistance = speedPerUpdate / (111 * Math.cos(currentLat * Math.PI / 180));

            // เปลี่ยนทิศทางบ้างเล็กน้อยเพื่อให้เส้นทางดูเป็นธรรมชาติ
            direction += (Math.random() - 0.5) * 0.5;

            // คำนวณตำแหน่งใหม่
            currentLat += latDistance * Math.sin(direction);
            currentLng += lngDistance * Math.cos(direction);

            // อัปเดตตำแหน่งบนแผนที่
            updateMap(currentLat, currentLng);

            // เพิ่มจุดไปยังเส้นทาง
            addPointToRoute(currentLat, currentLng);

            // คำนวณระยะทางโดยตรงจากเวลาและความเร็ว - ไม่พึ่งพาการคำนวณจาก GPS
            // ทำให้ค่าเพิ่มขึ้นอย่างสม่ำเสมอ
            currentDistance = (selectedSpeed * elapsedSeconds) / 3600;

            // ใช้ความเร็วที่เลือกเป็นความเร็วปัจจุบัน
            currentSpeed = selectedSpeed;

            // คำนวณแคลอรี่ที่เผาผลาญตามระยะทางที่สะสมไว้ - ทำให้เพิ่มขึ้นอย่างสม่ำเสมอ
            totalCalories = userWeight * currentDistance * 1.036;

            // อัปเดตค่าบนหน้าจอ
            updateRunStats();

            // อัปเดตข้อมูลบน server (เป็นระยะๆ)
            updateServerData();
        }, 2000);
    }

    // ฟังก์ชันคำนวณระยะทางระหว่างจุด (Haversine formula)
    function calculateDistance(lat1, lng1, lat2, lng2) {
        const R = 6371; // รัศมีของโลกในหน่วย km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a =
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c; // ระยะทางในหน่วย km
        return distance;
    }

    // ฟังก์ชันอัปเดตข้อมูลไปยัง server
    function updateServerData() {
        // อัปเดตข้อมูลทุก 10 วินาที (หรือตามความเหมาะสม)
        if (!runId || !isRunning || isPaused || elapsedSeconds % 10 !== 0) return;

        try {
            const updateData = {
                run_id: runId,
                distance: parseFloat(currentDistance.toFixed(2)),
                duration: elapsedSeconds,
                calories: Math.round(totalCalories),
                average_speed: parseFloat(currentSpeed.toFixed(1))
            };

            console.log('กำลังอัปเดตข้อมูลไปยังเซิร์ฟเวอร์:', updateData);

            fetch('{{ url("/run/update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(updateData)
            })
            .then(response => {
                if (!response.ok) {
                    console.error('Server response error:', response.status);
                    return;
                }
                console.log('อัปเดตข้อมูลสำเร็จ');
            })
            .catch(error => {
                console.error('Error updating run data:', error);
            });
        } catch (error) {
            console.error('Exception during update request:', error);
        }
    }

    // ฟังก์ชันแสดง alert ด้วย SweetAlert2
    function showAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#2ecc71'
        });
    }

    // ฟังก์ชันสำหรับตรวจสอบกิจกรรมที่ยังไม่เสร็จสิ้น

    // ฟังก์ชันรีเซ็ตสถานะการวิ่ง
    function resetRunState() {
        // ... existing code ...

        // อัปเดตสถานะปุ่มสลับโหมด
        updateToggleModeButton();
    }

    // อัปเดตสถานะของปุ่มสลับโหมด (เปิด/ปิดการใช้งาน)
    function updateToggleModeButton() {
        const toggleBtn = document.getElementById('toggleModeBtn');

        // ถ้ากำลังวิ่งอยู่ ปิดปุ่มไม่ให้เปลี่ยนโหมด
        if (isRunning) {
            toggleBtn.classList.add('disabled');
            toggleBtn.setAttribute('disabled', 'disabled');
            toggleBtn.title = 'ไม่สามารถเปลี่ยนโหมดขณะกำลังวิ่งได้';
        } else {
            toggleBtn.classList.remove('disabled');
            toggleBtn.removeAttribute('disabled');
            toggleBtn.title = '';
        }
    }

    // ฟังก์ชันสำหรับแปลงวินาทีเป็นรูปแบบ HH:MM:SS
    function formatTime(totalSeconds) {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return String(hours).padStart(2, '0') + ':' +
               String(minutes).padStart(2, '0') + ':' +
               String(seconds).padStart(2, '0');
    }
</script>
@endsection

