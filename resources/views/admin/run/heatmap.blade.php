@extends('layouts.admin')

@section('title', 'แผนที่ความร้อนการวิ่ง')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #heatmap {
        height: 600px;
        width: 100%;
        border-radius: 8px;
    }
    .popular-area-card {
        transition: all 0.3s ease;
    }
    .popular-area-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .custom-range-inputs {
        display: flex;
        gap: 10px;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #86b7fe;
    }
</style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">แผนที่ความร้อนกิจกรรมการวิ่ง</h1>
        <div class="btn-group">
            <a href="{{ route('admin.run.stats') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-1"></i> สถิติการวิ่ง
            </a>
            <a href="{{ route('admin.run.calendar') }}" class="btn btn-outline-primary">
                <i class="fas fa-calendar-alt me-1"></i> ปฏิทินการวิ่ง
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">ตัวกรองข้อมูล</h5>
                    <form id="heatmapFilterForm">
                        <div class="mb-3">
                            <label class="form-label">ช่วงเวลา</label>
                            <select class="form-select" id="timeRange">
                                <option value="7">7 วันล่าสุด</option>
                                <option value="30" selected>30 วันล่าสุด</option>
                                <option value="90">3 เดือนล่าสุด</option>
                                <option value="365">1 ปีล่าสุด</option>
                                <option value="custom">กำหนดเอง</option>
                            </select>
                        </div>

                        <div id="customDateRange" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">จากวันที่</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ถึงวันที่</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ผู้ใช้</label>
                            <select class="form-select" id="userFilter">
                                <option value="all" selected>ทั้งหมด</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">ใช้ตัวกรอง</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div id="heatmap"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">พื้นที่ยอดนิยม</h5>
        </div>
        <div class="card-body">
            <div class="row" id="popularAreas">
                <div class="col-12 text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">กำลังโหลด...</span>
                    </div>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">คำแนะนำในการใช้งาน</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-fire-alt fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5>การแสดงความร้อน</h5>
                            <p class="text-muted">สีแดงแสดงพื้นที่ที่มีการวิ่งมาก, สีน้ำเงินแสดงพื้นที่ที่มีการวิ่งน้อย</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-filter fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5>การกรองข้อมูล</h5>
                            <p class="text-muted">คุณสามารถกรองข้อมูลตามช่วงเวลาและผู้ใช้ที่ต้องการได้</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5>พื้นที่ยอดนิยม</h5>
                            <p class="text-muted">แสดงพื้นที่ที่มีการวิ่งมากที่สุด สามารถคลิกเพื่อดูบนแผนที่ได้</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>
    let map, heatLayer;
    let heatData = [];
    const defaultCenter = [13.736717, 100.523186]; // กรุงเทพฯ

    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        setupEventListeners();
        loadHeatmapData();
    });

    function initMap() {
        map = L.map('heatmap').setView(defaultCenter, 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    }

    function setupEventListeners() {
        document.getElementById('timeRange').addEventListener('change', function() {
            const customDateDiv = document.getElementById('customDateRange');
            customDateDiv.style.display = this.value === 'custom' ? 'block' : 'none';
        });

        document.getElementById('heatmapFilterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loadHeatmapData();
        });
    }

    function loadHeatmapData() {
        // แสดง loading
        document.getElementById('popularAreas').innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <p class="mt-2">กำลังโหลดข้อมูล...</p>
            </div>
        `;

        const timeRange = document.getElementById('timeRange').value;
        const userId = document.getElementById('userFilter').value;

        let url = '{{ route("admin.run.heatmap.data") }}?';

        if (timeRange === 'custom') {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            if (startDate && endDate) {
                url += `start_date=${startDate}&end_date=${endDate}`;
            }
        } else {
            url += `days=${timeRange}`;
        }

        if (userId !== 'all') {
            url += `&user_id=${userId}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                updateHeatmap(data.heatmap_data);
                updatePopularAreas(data.popular_areas);
            })
            .catch(error => {
                console.error('Error loading heatmap data:', error);
                document.getElementById('popularAreas').innerHTML = `
                    <div class="col-12 text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            เกิดข้อผิดพลาดในการโหลดข้อมูล: ${error.message || 'โปรดลองอีกครั้ง'}
                        </div>
                    </div>
                `;
            });
    }

    function updateHeatmap(data) {
        // ถ้ามี heatLayer อยู่แล้ว ให้ลบออกก่อน
        if (heatLayer) {
            map.removeLayer(heatLayer);
        }

        // แปลงข้อมูลให้เป็นรูปแบบที่ leaflet-heat ต้องการ [lat, lng, intensity]
        heatData = data.map(point => [point.lat, point.lng, point.count]);

        // สร้าง heatLayer ใหม่
        heatLayer = L.heatLayer(heatData, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: {
                0.4: 'blue',
                0.6: 'lime',
                0.8: 'yellow',
                1.0: 'red'
            }
        }).addTo(map);

        // ปรับ zoom ให้เห็นข้อมูลทั้งหมด
        if (heatData.length > 0) {
            const points = heatData.map(point => [point[0], point[1]]);
            try {
                map.fitBounds(L.latLngBounds(points));
            } catch (error) {
                console.error('Error fitting bounds:', error);
                map.setView(defaultCenter, 12);
            }
        } else {
            map.setView(defaultCenter, 12);
        }
    }

    function updatePopularAreas(areas) {
        const container = document.getElementById('popularAreas');
        container.innerHTML = '';

        if (areas.length === 0) {
            container.innerHTML = '<div class="col-12 text-center py-4">ไม่พบข้อมูลพื้นที่ยอดนิยม</div>';
            return;
        }

        areas.forEach(area => {
            const div = document.createElement('div');
            div.className = 'col-md-4 mb-3';
            div.innerHTML = `
                <div class="card border-0 shadow-sm h-100 popular-area-card">
                    <div class="card-body">
                        <h5 class="card-title">${area.name}</h5>
                        <p class="mb-1">จำนวนการวิ่ง: <span class="fw-bold">${area.count}</span></p>
                        <p class="mb-1">ระยะทางเฉลี่ย: <span class="fw-bold">${area.avg_distance.toFixed(2)} กม.</span></p>
                        <button class="btn btn-sm btn-primary mt-2" onclick="focusOnArea(${area.lat}, ${area.lng})">
                            <i class="fas fa-search-location me-1"></i> ดูบนแผนที่
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }

    function focusOnArea(lat, lng) {
        map.setView([lat, lng], 14);

        // เพิ่มมาร์กเกอร์ชั่วคราวที่จะหายไปหลังจาก 3 วินาที
        const marker = L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'custom-marker',
                html: '<i class="fas fa-map-pin text-danger" style="font-size: 24px;"></i>',
                iconSize: [24, 24],
                iconAnchor: [12, 24]
            })
        }).addTo(map);

        // สร้างเอฟเฟกต์กระพริบ
        let opacity = 1;
        let direction = -0.1;

        const blinkInterval = setInterval(() => {
            opacity += direction;
            if (opacity <= 0.3 || opacity >= 1) {
                direction *= -1;
            }

            const customIcon = document.querySelector('.custom-marker i');
            if (customIcon) {
                customIcon.style.opacity = opacity;
            }
        }, 100);

        // ลบมาร์กเกอร์หลังจาก 3 วินาที
        setTimeout(() => {
            map.removeLayer(marker);
            clearInterval(blinkInterval);
        }, 3000);
    }
</script>
@endsection
