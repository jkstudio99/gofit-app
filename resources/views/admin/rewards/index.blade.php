@extends('layouts.admin')

@section('title', 'จัดการรางวัล')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .reward-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .reward-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .reward-img-container {
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .reward-img {
        max-height: 140px;
        max-width: 140px;
        object-fit: contain;
    }

    .reward-status {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 0.7rem;
    }

    .reward-stock {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 0.7rem;
    }

    .search-box {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding-left: 20px;
    }

    .search-box:focus {
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
        border-color: #2DC679;
    }

    .reward-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
    }

    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover, .filter-badge.active {
        background-color: #2DC679 !important;
        color: white;
    }

    /* Advanced filter panel */
    .filter-panel {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    /* Loading spinner */
    .loading-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 10px;
    }

    .btn-info, .btn-info:hover, .btn-info:active, .btn-info:focus {
        color: white !important;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">จัดการรางวัล</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>เพิ่มรางวัลใหม่
            </a>
        </div>
    </div>

    <!-- คำแนะนำการใช้งาน -->
    <div class="alert alert-info mb-4">
        <h5><i class="fas fa-info-circle me-2"></i>คำแนะนำการจัดการรางวัล</h5>
        <ul class="mb-0">
            <li>รางวัลที่มีประวัติการแลกแล้วจะไม่สามารถลบได้ แต่สามารถปิดการใช้งานได้</li>
            <li>การปิดการใช้งานจะช่วยให้รางวัลไม่แสดงในหน้าแลกรางวัลของผู้ใช้ แต่ยังคงดูประวัติการแลกได้</li>
            <li>ใช้ปุ่ม <i class="fas fa-toggle-on"></i> / <i class="fas fa-toggle-off"></i> เพื่อเปิดหรือปิดการใช้งานรางวัล</li>
            <li>แถบ <span class="badge bg-light text-dark py-1"><i class="fas fa-check-circle"></i> รางวัลที่เปิดใช้งาน</span> และแถบ <span class="badge bg-light text-dark py-1"><i class="fas fa-ban"></i> รางวัลที่ปิดใช้งาน</span> ช่วยให้คุณแยกดูรางวัลตามสถานะการใช้งาน</li>
        </ul>
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

                <div class="col-12 collapse {{ request()->hasAny(['status', 'min_points', 'max_points', 'sort']) ? 'show' : '' }}" id="advancedFilters">
                    <div class="filter-panel mt-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">สถานะรางวัล</label>
                                <select id="status-filter" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    <option value="enabled" {{ request('status') == 'enabled' ? 'selected' : '' }}>เปิดใช้งาน</option>
                                    <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>ปิดใช้งาน</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">คะแนนขั้นต่ำ</label>
                                <input type="number" id="min-points-filter" class="form-control" placeholder="0" value="{{ request('min_points') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">คะแนนสูงสุด</label>
                                <input type="number" id="max-points-filter" class="form-control" placeholder="10000" value="{{ request('max_points') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">สถานะสินค้า</label>
                                <select id="stock-filter" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>มีสินค้า</option>
                                    <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>สินค้าเหลือน้อย (≤ 10)</option>
                                    <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>สินค้าหมด</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">เรียงตาม</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input sort-radio" type="radio" name="sort" id="sort-points-asc" value="points-asc" {{ request('sort') == 'points-asc' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sort-points-asc">คะแนนน้อยไปมาก</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input sort-radio" type="radio" name="sort" id="sort-points-desc" value="points-desc" {{ request('sort') == 'points-desc' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sort-points-desc">คะแนนมากไปน้อย</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input sort-radio" type="radio" name="sort" id="sort-newest" value="newest" {{ request('sort', 'newest') == 'newest' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sort-newest">ใหม่ล่าสุด</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input sort-radio" type="radio" name="sort" id="sort-oldest" value="oldest" {{ request('sort') == 'oldest' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sort-oldest">เก่าสุด</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 text-end align-self-end">
                                <button id="reset-filter" type="button" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i> รีเซ็ตตัวกรอง
                                </button>
                                <button id="apply-filter" type="button" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> กรอง
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Tags -->
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-{{ request('status') ? 'light text-dark' : 'primary' }} py-2 px-3 filter-badge status-filter" data-status="">
                <i class="fas fa-check-circle me-1"></i> รางวัลที่เปิดใช้งาน
            </span>

            <span class="badge bg-{{ request('status') == 'disabled' ? 'primary' : 'light text-dark' }} py-2 px-3 filter-badge status-filter" data-status="disabled">
                <i class="fas fa-ban me-1"></i> รางวัลที่ปิดใช้งาน
            </span>
        </div>
    </div>

    <!-- Results -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">
                    <i class="fas fa-gift me-2 text-primary"></i>รายการรางวัล
                    <span id="filter-badge" class="badge bg-success ms-2 {{ !request()->hasAny(['search', 'status', 'min_points', 'max_points', 'stock', 'sort']) ? 'd-none' : '' }}">
                        <i class="fas fa-filter me-1"></i> กำลังแสดงผลลัพธ์: <span id="total-count">{{ $rewards->total() }}</span> รายการ
                    </span>
                </h5>
                <span class="badge bg-info rounded-pill">
                    <i class="fas fa-gift me-1"></i> รางวัลทั้งหมด: <span id="all-count">{{ $rewards->count() }}</span>
                </span>
            </div>
        </div>

        <div class="card-body position-relative">
            <!-- Loading spinner -->
            <div id="loading-spinner" class="loading-container d-none">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">กำลังโหลด...</span>
                    </div>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            </div>

            <!-- Reward list container -->
            <div id="reward-list-container">
                @include('admin.rewards.partials.reward_list')
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    แสดง {{ $rewards->firstItem() ?? 0 }} ถึง {{ $rewards->lastItem() ?? 0 }} จาก <span id="pagination-total">{{ $rewards->total() }}</span> รายการ
                </div>
                <div class="pagination-container" id="pagination-container">
                    {{ $rewards->appends(request()->query())->links('pagination::bootstrap-4') }}
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
        // Setup direct SweetAlert delete confirmation without using Bootstrap modal
        setupDeleteButtons();

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // กำหนดค่าเริ่มต้นสำหรับ SweetAlert2 ทั้งหมด
        Swal.mixin({
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            }
        });

        // Timer สำหรับ debounce
        let typingTimer;
        const doneTypingInterval = 500; // เวลารอ 500 ms หลังจากพิมพ์เสร็จ

        // AJAX search และตัวกรอง
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        const minPointsFilter = document.getElementById('min-points-filter');
        const maxPointsFilter = document.getElementById('max-points-filter');
        const stockFilter = document.getElementById('stock-filter');
        const sortRadios = document.querySelectorAll('.sort-radio');
        const searchButton = document.getElementById('search-button');
        const applyFilterButton = document.getElementById('apply-filter');
        const resetFilterButton = document.getElementById('reset-filter');
        const statusFilterBadges = document.querySelectorAll('.status-filter');

        // Event สำหรับ debounce การพิมพ์
        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            if (searchInput.value) {
                typingTimer = setTimeout(fetchRewards, doneTypingInterval);
            }
        });

        // ให้ทุก element ที่เป็นตัวกรองทำงานแบบ live search
        statusFilter.addEventListener('change', fetchRewards);
        minPointsFilter.addEventListener('input', debounceFilter);
        maxPointsFilter.addEventListener('input', debounceFilter);
        stockFilter.addEventListener('change', fetchRewards);
        sortRadios.forEach(radio => {
            radio.addEventListener('change', fetchRewards);
        });

        // ปุ่มค้นหา
        searchButton.addEventListener('click', fetchRewards);

        // ปุ่ม apply filter
        applyFilterButton.addEventListener('click', fetchRewards);

        // ปุ่ม reset filter
        resetFilterButton.addEventListener('click', resetFilters);

        // Status filter badges
        statusFilterBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                const status = this.getAttribute('data-status');
                if (statusFilter) {
                    statusFilter.value = status;
                }
                // อัพเดต UI ของ badges
                statusFilterBadges.forEach(b => {
                    b.classList.remove('bg-primary');
                    b.classList.add('bg-light', 'text-dark');
                });
                this.classList.remove('bg-light', 'text-dark');
                this.classList.add('bg-primary');
                fetchRewards();
            });
        });

        // Pagination จะถูกจัดการใน fetchRewards และจะเป็นแบบ Ajax ด้วย
        document.addEventListener('click', function(e) {
            // ตรวจสอบว่า element ที่ click เป็นลิงก์หน้าหรือไม่
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const href = e.target.closest('a').getAttribute('href');
                if (href) {
                    fetchRewardsFromUrl(href);
                }
            }
        });

        // ฟังก์ชันสำหรับ debounce การพิมพ์ในช่องคะแนน
        function debounceFilter() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(fetchRewards, doneTypingInterval);
        }

        // ฟังก์ชันดึงรางวัลจากพารามิเตอร์ปัจจุบัน
        function fetchRewards() {
            const searchValue = searchInput.value.trim();
            const statusValue = statusFilter ? statusFilter.value : '';
            const minPointsValue = minPointsFilter ? minPointsFilter.value : '';
            const maxPointsValue = maxPointsFilter ? maxPointsFilter.value : '';
            const stockValue = stockFilter ? stockFilter.value : '';

            // หา sort value จาก radio ที่เลือก
            let sortValue = '';
            sortRadios.forEach(radio => {
                if (radio.checked) {
                    sortValue = radio.value;
                }
            });

            // แสดง loading spinner
            document.getElementById('loading-spinner').classList.remove('d-none');

            // สร้าง URL พร้อม query parameters
            const url = new URL('{{ route("admin.rewards.api.search") }}');
            if (searchValue) url.searchParams.append('search', searchValue);
            if (statusValue) url.searchParams.append('status', statusValue);
            if (minPointsValue) url.searchParams.append('min_points', minPointsValue);
            if (maxPointsValue) url.searchParams.append('max_points', maxPointsValue);
            if (stockValue) url.searchParams.append('stock', stockValue);
            if (sortValue) url.searchParams.append('sort', sortValue);

            // ส่ง AJAX request
            fetch(url.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // อัพเดต reward list
                        document.getElementById('reward-list-container').innerHTML = data.html;

                        // อัพเดต pagination
                        document.getElementById('pagination-container').innerHTML = data.pagination;

                        // อัพเดต count
                        document.getElementById('total-count').textContent = data.count;
                        document.getElementById('pagination-total').textContent = data.count;

                        // แสดง/ซ่อน filter badge
                        const filterBadge = document.getElementById('filter-badge');
                        if (searchValue || statusValue || minPointsValue || maxPointsValue || stockValue || sortValue) {
                            filterBadge.classList.remove('d-none');
                        } else {
                            filterBadge.classList.add('d-none');
                        }

                        // ติดตั้ง event listeners สำหรับปุ่มลบ
                        setupDeleteButtons();

                        // Initialize tooltips again
                        var tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipList.forEach(tooltip => {
                            new bootstrap.Tooltip(tooltip);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching rewards:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถดึงข้อมูลรางวัลได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonColor: '#dc3545'
                    });
                })
                .finally(() => {
                    // ซ่อน loading spinner
                    document.getElementById('loading-spinner').classList.add('d-none');
                });
        }

        // ฟังก์ชันดึงรางวัลจาก URL ที่กำหนด (สำหรับ pagination)
        function fetchRewardsFromUrl(url) {
            // แสดง loading spinner
            document.getElementById('loading-spinner').classList.remove('d-none');

            // ส่ง AJAX request
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // อัพเดต reward list
                        document.getElementById('reward-list-container').innerHTML = data.html;

                        // อัพเดต pagination
                        document.getElementById('pagination-container').innerHTML = data.pagination;

                        // อัพเดต count
                        document.getElementById('total-count').textContent = data.count;
                        document.getElementById('pagination-total').textContent = data.count;

                        // ติดตั้ง event listeners สำหรับปุ่มลบ
                        setupDeleteButtons();

                        // Initialize tooltips again
                        var tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipList.forEach(tooltip => {
                            new bootstrap.Tooltip(tooltip);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching rewards:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถดึงข้อมูลรางวัลได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonColor: '#dc3545'
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
            if (statusFilter) statusFilter.value = '';
            if (minPointsFilter) minPointsFilter.value = '';
            if (maxPointsFilter) maxPointsFilter.value = '';
            if (stockFilter) stockFilter.value = '';

            // Reset radio buttons
            const defaultSortRadio = document.getElementById('sort-newest');
            if (defaultSortRadio) defaultSortRadio.checked = true;

            // Reset status filter badges
            statusFilterBadges.forEach((b, index) => {
                if (index === 0) {
                    b.classList.remove('bg-light', 'text-dark');
                    b.classList.add('bg-primary');
                } else {
                    b.classList.remove('bg-primary');
                    b.classList.add('bg-light', 'text-dark');
                }
            });

            // Fetch rewards with reset filters
            fetchRewards();
        }

        // ฟังก์ชันตั้งค่า event listeners สำหรับปุ่มลบ
        function setupDeleteButtons() {
            const deleteButtons = document.querySelectorAll('.delete-reward');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const rewardId = this.getAttribute('data-reward-id');
                    const rewardName = this.getAttribute('data-reward-name');

                    Swal.fire({
                        title: 'ยืนยันการลบรางวัล?',
                        html: `คุณต้องการลบรางวัล <strong>${rewardName}</strong> ใช่หรือไม่?<br><span class="text-danger">การดำเนินการนี้ไม่สามารถเรียกคืนได้ และจะลบรางวัลนี้ออกจากระบบอย่างถาวร</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'ใช่, ลบรางวัล!',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create and submit the form
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/admin/rewards/${rewardId}`;

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

        // Display alerts for success/error messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#2DC679'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc3545'
            });
        @endif
    });
</script>
@endsection
