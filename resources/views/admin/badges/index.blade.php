@extends('layouts.admin')

@section('title', 'จัดการเหรียญตรา')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    /* Badge Cards */
    .badge-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        border: none;
    }

    .badge-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .badge-card.unlocked:hover {
        box-shadow: 0 10px 20px rgba(40,167,69,0.2);
    }

    .badge-img-container {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .badge-img {
        max-height: 120px;
        width: auto;
        max-width: 80%;
        object-fit: contain;
        transition: all 0.3s ease;
    }

    .badge-card:hover .badge-img {
        transform: scale(1.1);
    }

    .grayscale {
        filter: grayscale(100%);
    }

    .badge-type {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 2;
        border-radius: 8px;
        padding: 5px 8px;
        font-size: 0.75rem;
    }

    .badge-stats {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
    }

    .badge-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: rgba(255,255,255,0.5);
        pointer-events: none;
    }

    /* Filter Badges */
    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover, .filter-badge.active {
        background-color: #2DC679 !important;
        color: white;
    }

    /* Stats Cards */
    .badge-stat-card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .badge-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
    }

    .badge-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Progress bars */
    .progress {
        background-color: rgba(0,0,0,0.05);
        height: 8px !important;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 4px;
        animation: progress-bar-stripes 1s linear infinite, progress-animation 1.5s ease-out;
        background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }

    /* Progress animation */
    @keyframes progress-animation {
        0% { width: 0%; }
    }

    @keyframes progress-bar-stripes {
        0% { background-position: 1rem 0 }
        100% { background-position: 0 0 }
    }

    /* Badge Category Styling */
    .badge-category-section {
        border-radius: 12px;
        padding: 15px;
        background-color: #f8f9fa;
        margin-bottom: 25px;
    }

    .badge-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .badge-type-header {
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
    }

    /* Tab Styling */
    .nav-tabs {
        border-bottom: 1px solid #e9ecef;
    }

    .nav-tabs .nav-item {
        margin-bottom: -1px;
    }

    .nav-tabs .nav-link {
        border: none;
        border-radius: 0;
        color: #495057;
        font-weight: 500;
        padding: 12px 20px;
        transition: all 0.2s ease;
        position: relative;
    }

    .nav-tabs .nav-link:hover {
        background-color: rgba(45, 198, 121, 0.05);
        border-color: transparent;
    }

    .nav-tabs .nav-link.active {
        color: #2DC679;
        background-color: white;
        border-color: transparent;
    }

    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #2DC679;
    }

    .card-header-custom {
        font-weight: 500;
        padding: 10px 15px;
    }

    /* Search and Filter */
    .search-box {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding-left: 20px;
    }

    .search-box:focus {
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
        border-color: #2DC679;
    }

    .badge-action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    /* Make delete button icons white */
    .btn-danger.badge-action-btn i,
    .btn-danger i {
        color: white !important;
    }

    /* Use primary color from design system */
    .btn-primary, .bg-primary {
        background-color: #2DC679 !important;
        border-color: #2DC679 !important;
    }

    .btn-primary:hover {
        background-color: #24A664 !important;
        border-color: #24A664 !important;
    }

    /* Badge info styling */
    .badge.bg-info {
        background-color: #3B82F6 !important;
        color: white !important;
    }

    .sort-icon {
        font-size: 0.8rem;
        margin-left: 5px;
    }

    /* Advanced filter panel */
    .filter-panel {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    /* CSS สำหรับแสดง loading */
    .loading-container {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        padding: 20px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="mb-0">จัดการเหรียญตรา</h2>
        <div class="d-flex gap-2">

            <a href="{{ route('admin.badges.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>เพิ่มเหรียญตราใหม่
            </a>
        </div>
    </div>
    <p class="text-muted">จัดการเหรียญตราความสำเร็จสำหรับผู้ใช้งานในระบบ</p>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-medal text-white"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">เหรียญทั้งหมด</h6>
                        <h4 class="mb-0">{{ $badges->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-success bg-opacity-10 me-3">
                        <i class="fas fa-users text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">ผู้ใช้ที่ได้รับ</h6>
                        <h4 class="mb-0">{{ $totalUsers ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-warning bg-opacity-10 me-3">
                        <i class="fas fa-fire text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">รับล่าสุด</h6>
                        <h4 class="mb-0">{{ $recentBadges ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-info bg-opacity-10 me-3">
                        <i class="fas fa-percentage text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">อัตราปลดล็อค</h6>
                        <h4 class="mb-0">{{ $unlockRate ?? '0' }}%</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="search-input" class="form-control search-box"
                               placeholder="ค้นหาตามชื่อหรือคำอธิบาย..." value="{{ request('search') }}">
                        <button class="btn btn-primary" id="search-button" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 text-md-end">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                        <i class="fas fa-filter me-1"></i> ตัวกรองขั้นสูง
                    </button>
                </div>

                <div class="col-12 collapse {{ request()->hasAny(['type', 'sort']) ? 'show' : '' }}" id="advancedFilters">
                    <div class="filter-panel mt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">ประเภทเหรียญตรา</label>
                                <select id="type-filter" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach($badgeTypes as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            @if($type == 'distance')
                                                ระยะทาง
                                            @elseif($type == 'calories')
                                                แคลอรี่
                                            @elseif($type == 'streak')
                                                ต่อเนื่อง
                                            @elseif($type == 'speed')
                                                ความเร็ว
                                            @elseif($type == 'event')
                                                กิจกรรม
                                            @else
                                                {{ $type }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">เรียงตาม</label>
                                <select id="sort-filter" class="form-select">
                                    <option value="created_at" {{ $sortField == 'created_at' ? 'selected' : '' }}>วันที่สร้าง</option>
                                    <option value="badge_name" {{ $sortField == 'badge_name' ? 'selected' : '' }}>ชื่อเหรียญตรา</option>
                                    <option value="type" {{ $sortField == 'type' ? 'selected' : '' }}>ประเภท</option>
                                    <option value="criteria" {{ $sortField == 'criteria' ? 'selected' : '' }}>เกณฑ์</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">ลำดับ</label>
                                <select id="direction-filter" class="form-select">
                                    <option value="asc" {{ $sortDirection == 'asc' ? 'selected' : '' }}>น้อยไปมาก</option>
                                    <option value="desc" {{ $sortDirection == 'desc' ? 'selected' : '' }}>มากไปน้อย</option>
                                </select>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <button type="button" id="reset-filter" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i> รีเซ็ตตัวกรอง
                                </button>
                                <button type="button" id="apply-filter" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> กรอง
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Badges Grid -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0">
                            <i class="fas fa-medal me-2 text-primary"></i>รายการเหรียญตรา
                            <span id="filter-badge" class="badge bg-success ms-2 {{ !request()->hasAny(['search', 'type', 'sort']) ? 'd-none' : '' }}">
                                <i class="fas fa-filter me-1"></i> กำลังแสดงผลลัพธ์: <span id="total-count">{{ $badges->total() }}</span> รายการ
                            </span>
                        </h5>
                        <span class="badge bg-info text-white rounded-pill px-3 py-2">
                            <i class="fas fa-medal me-1"></i> เหรียญตราทั้งหมด: <span id="all-count">{{ $badges->total() }}</span>
                        </span>
                    </div>
                </div>

                <div class="card-body pt-4 position-relative">
                    <!-- Loading spinner -->
                    <div id="loading-spinner" class="loading-container d-none">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">กำลังโหลด...</span>
                            </div>
                            <p class="mt-2">กำลังโหลดข้อมูล...</p>
                        </div>
                    </div>

                    <!-- Badges list container -->
                    <div id="badges-list-container">
                        @include('admin.badges.partials.badge_list')
                    </div>
                </div>

                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            แสดง {{ $badges->firstItem() ?? 0 }} ถึง {{ $badges->lastItem() ?? 0 }} จาก <span id="pagination-total">{{ $badges->total() }}</span> รายการ
                        </div>
                        <div class="pagination-container" id="pagination-container">
                            {{ $badges->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Explanation Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">เงื่อนไขการรับเหรียญตรา</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ประเภท</th>
                                    <th>เงื่อนไข</th>
                                    <th>คำอธิบาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-route me-2 text-primary"></i> เหรียญจากระยะทาง</td>
                                    <td>วิ่งให้ได้ตามระยะทางที่กำหนด</td>
                                    <td>ผู้ใช้จะได้รับเหรียญตราเมื่อวิ่งได้ระยะทางสะสมตามที่กำหนด เช่น 5 กม., 10 กม., 20 กม., 50 กม., 100 กม.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-fire me-2 text-danger"></i> เหรียญจากแคลอรี่</td>
                                    <td>เผาผลาญแคลอรี่ตามที่กำหนด</td>
                                    <td>ผู้ใช้จะได้รับเหรียญตราเมื่อเผาผลาญแคลอรี่สะสมตามที่กำหนด เช่น 100, 500, 1,000, 2,500, 5,000 แคลอรี่</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-check me-2 text-success"></i> เหรียญจากการวิ่งติดต่อกัน</td>
                                    <td>วิ่งติดต่อกันตามจำนวนวันที่กำหนด</td>
                                    <td>ผู้ใช้จะได้รับเหรียญตราเมื่อวิ่งติดต่อกันตามจำนวนวันที่กำหนด เช่น 3 วัน, 7 วัน, 14 วัน, 30 วัน</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-tachometer-alt me-2 text-info"></i> เหรียญจากความเร็ว</td>
                                    <td>วิ่งด้วยความเร็วเฉลี่ยตามที่กำหนด</td>
                                    <td>ผู้ใช้จะได้รับเหรียญตราเมื่อวิ่งด้วยความเร็วเฉลี่ยตามที่กำหนด เช่น 5 กม./ชม., 8 กม./ชม., 10 กม./ชม.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-trophy me-2 text-warning"></i> เหรียญจากการเข้าร่วมกิจกรรม</td>
                                    <td>เข้าร่วมกิจกรรมตามจำนวนครั้งที่กำหนด</td>
                                    <td>ผู้ใช้จะได้รับเหรียญตราเมื่อเข้าร่วมกิจกรรมตามที่กำหนด เช่น เข้าร่วมกิจกรรม 1 ครั้ง, 3 ครั้ง, 5 ครั้ง</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup delete badge functionality with SweetAlert2
        setupDeleteButtons();

        // Timer สำหรับ debounce
        let typingTimer;
        const doneTypingInterval = 500; // เวลารอ 500 ms หลังจากพิมพ์เสร็จ

        // AJAX search และตัวกรอง
        const searchInput = document.getElementById('search-input');
        const typeFilter = document.getElementById('type-filter');
        const sortFilter = document.getElementById('sort-filter');
        const directionFilter = document.getElementById('direction-filter');
        const searchButton = document.getElementById('search-button');
        const applyFilterButton = document.getElementById('apply-filter');
        const resetFilterButton = document.getElementById('reset-filter');

        // Event สำหรับ debounce การพิมพ์
        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            if (searchInput.value) {
                typingTimer = setTimeout(fetchBadges, doneTypingInterval);
            }
        });

        // ให้ทุก element ที่เป็นตัวกรองทำงานเมื่อมีการเปลี่ยนแปลง
        typeFilter.addEventListener('change', fetchBadges);
        sortFilter.addEventListener('change', fetchBadges);
        directionFilter.addEventListener('change', fetchBadges);

        // ปุ่มค้นหา
        searchButton.addEventListener('click', fetchBadges);

        // ปุ่ม apply filter
        applyFilterButton.addEventListener('click', fetchBadges);

        // ปุ่ม reset filter
        resetFilterButton.addEventListener('click', resetFilters);

        // Pagination จะถูกจัดการใน fetchBadges และจะเป็นแบบ Ajax ด้วย
        document.addEventListener('click', function(e) {
            // ตรวจสอบว่า element ที่ click เป็นลิงก์หน้าหรือไม่
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const href = e.target.closest('a').getAttribute('href');
                if (href) {
                    fetchBadgesFromUrl(href);
                }
            }
        });

        // ฟังก์ชันดึงรางวัลจากพารามิเตอร์ปัจจุบัน
        function fetchBadges() {
            const searchValue = searchInput.value.trim();
            const typeValue = typeFilter ? typeFilter.value : '';
            const sortValue = sortFilter ? sortFilter.value : 'created_at';
            const directionValue = directionFilter ? directionFilter.value : 'desc';

            // แสดง loading spinner
            document.getElementById('loading-spinner').classList.remove('d-none');

            // สร้าง URL พร้อม query parameters
            const url = new URL('{{ route("admin.badges.api.search") }}');
            if (searchValue) url.searchParams.append('search', searchValue);
            if (typeValue) url.searchParams.append('type', typeValue);
            if (sortValue) url.searchParams.append('sort', sortValue);
            if (directionValue) url.searchParams.append('direction', directionValue);

            // ส่ง AJAX request
            fetch(url.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // อัพเดต badges list
                        document.getElementById('badges-list-container').innerHTML = data.html;

                        // อัพเดต pagination
                        document.getElementById('pagination-container').innerHTML = data.pagination;

                        // อัพเดต count ถ้ามี
                        const totalCountElement = document.getElementById('total-count');
                        if (totalCountElement) {
                            totalCountElement.textContent = data.count || 0;
                        }
                        const paginationTotalElement = document.getElementById('pagination-total');
                        if (paginationTotalElement) {
                            paginationTotalElement.textContent = data.count || 0;
                        }
                        const allCountElement = document.getElementById('all-count');
                        if (allCountElement) {
                            allCountElement.textContent = data.count || 0;
                        }

                        // แสดง/ซ่อน filter badge
                        const filterBadge = document.getElementById('filter-badge');
                        if (searchValue || typeValue || sortValue !== 'created_at' || directionValue !== 'desc') {
                            filterBadge.classList.remove('d-none');
                        } else {
                            filterBadge.classList.add('d-none');
                        }

                        // ติดตั้ง event listeners สำหรับปุ่มลบ
                        setupDeleteButtons();

                        // Initialize tooltips
                        reinitializeTooltips();
                    }
                })
                .catch(error => {
                    console.error('Error fetching badges:', error);
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                })
                .finally(() => {
                    // ซ่อน loading spinner
                    document.getElementById('loading-spinner').classList.add('d-none');
                });
        }

        // ฟังก์ชันดึงรางวัลจาก URL ที่กำหนด (สำหรับ pagination)
        function fetchBadgesFromUrl(url) {
            // แสดง loading spinner
            document.getElementById('loading-spinner').classList.remove('d-none');

            // ส่ง AJAX request
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // อัพเดต badges list
                        document.getElementById('badges-list-container').innerHTML = data.html;

                        // อัพเดต pagination
                        document.getElementById('pagination-container').innerHTML = data.pagination;

                        // ติดตั้ง event listeners สำหรับปุ่มลบ
                        setupDeleteButtons();

                        // Initialize tooltips
                        reinitializeTooltips();
                    }
                })
                .catch(error => {
                    console.error('Error fetching badges:', error);
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                })
                .finally(() => {
                    // ซ่อน loading spinner
                    document.getElementById('loading-spinner').classList.add('d-none');
                });
        }

        // ฟังก์ชัน reset filters
        function resetFilters() {
            searchInput.value = '';
            if (typeFilter) typeFilter.value = '';
            if (sortFilter) sortFilter.value = 'created_at';
            if (directionFilter) directionFilter.value = 'desc';

            // Fetch badges with reset filters
            fetchBadges();
        }

        // Initialize tooltips
        function reinitializeTooltips() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // ฟังก์ชันตั้งค่า event listeners สำหรับปุ่มลบ
        function setupDeleteButtons() {
            document.querySelectorAll('.delete-badge').forEach(button => {
                button.addEventListener('click', function() {
                    const badgeId = this.getAttribute('data-badge-id');
                    const badgeName = this.getAttribute('data-badge-name');
                    const usersCount = parseInt(this.getAttribute('data-users-count') || '0');

                    let confirmMessage = `คุณต้องการลบเหรียญตรา "${badgeName}" ใช่หรือไม่?`;
                    if (usersCount > 0) {
                        confirmMessage += `<br><br><span class="text-danger">เหรียญตรานี้มีผู้ใช้ได้รับแล้ว ${usersCount} คน การลบจะทำให้ผู้ใช้เหล่านั้นเสียเหรียญตรานี้ไป</span>`;
                    }

                    Swal.fire({
                        title: 'ยืนยันการลบ',
                        html: confirmMessage,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'ลบ',
                        cancelButtonText: 'ยกเลิก',
                        showLoaderOnConfirm: false // ปิดการแสดง loading
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create form for deletion
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `{{ url('admin/badges') }}/${badgeId}`;
                            form.style.display = 'none';

                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';

                            const method = document.createElement('input');
                            method.type = 'hidden';
                            method.name = '_method';
                            method.value = 'DELETE';

                            form.appendChild(csrfToken);
                            form.appendChild(method);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        }

        // Initialize tooltips on page load
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Display alerts for success/error messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: "{{ session('error') }}",
                confirmButtonText: 'ตกลง'
            });
        @endif
    });
</script>

<!-- Include SweetAlert message partial -->
@include('partials.sweetalert-messages')
@endsection
