@extends('layouts.admin')

@section('title', 'ประวัติการได้รับเหรียญตรา')

@section('styles')
<style>
ุ   .badge-image {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        max-width: 60px;
    }

    .badge-image:hover {
        transform: scale(1.2);
    }

    /* Badge Cards */
    .badge-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        border: none;
    }

    .badge-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    /* Stats Cards */
    .badge-stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .badge-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .badge-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 10px;
        font-size: 20px;
    }

    /* Card and Table Design */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
    }

    .table thead th {
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        border-top: none;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(45, 198, 121, 0.05);
    }

    /* Filter section */
    .filter-section .form-control, .filter-section .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }

    .filter-section .form-control:focus, .filter-section .form-select:focus {
        border-color: #2DC679;
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
    }

    /* Button styling */
    .btn-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .badge-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0,0,0,0.05);
        margin-right: 0.5rem;
    }

    .history-card {
        border-radius: 10px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }

    .history-card:hover {
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    .badge-pill {
        border-radius: 20px;
        font-size: 0.8rem;
        padding: 3px 15px;
    }

    .user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
    }

    .dot-separator::after {
        content: "•";
        margin: 0 8px;
        color: #adb5bd;
    }

    /* Design system styles */
    .btn-primary, .bg-primary {
        background-color: #2DC679 !important;
        border-color: #2DC679 !important;
    }

    .btn-primary:hover {
        background-color: #24A664 !important;
        border-color: #24A664 !important;
    }

    .badge.bg-info {
        background-color: #3B82F6 !important;
        color: white !important;
    }

    .btn-info, .badge-info, .btn-info.badge-action-btn {
        background-color: #3B82F6 !important;
        border-color: #3B82F6 !important;
        color: white !important;
    }

    .btn-danger i, .btn-danger.badge-action-btn i {
        color: white !important;
    }

    /* User links - remove underline */
    .user-link {
        color: #495057;
        text-decoration: none !important;
    }

    .user-link:hover {
        color: #2DC679;
    }

    /* Filter collapse */
    #filterCollapse {
        transition: all 0.3s ease;
    }

    /* Medal count display style */
    .medal-count {
        background-color: #f8f9fa;
        border-radius: 50px;
        padding: 0.5rem 1rem;
        font-weight: 500;
    }

    /* Mobile responsiveness */
    @media (max-width: 767.98px) {
        .table-responsive {
            border: 0;
        }

        .badge-stat-card .card-body {
            padding: 0.75rem;
        }

        .badge-stat-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .medal-count {
            font-size: 0.875rem;
            padding: 0.35rem 0.75rem;
        }

        .card-header {
            padding: 0.75rem 1rem;
        }

        .badge-image {
            max-width: 50px;
        }

        .table thead th,
        .table tbody td {
            padding: 0.5rem 0.25rem;
            font-size: 0.875rem;
        }

        h2.mb-0 {
            font-size: 1.5rem;
        }

        h4.mb-0 {
            font-size: 1.25rem;
        }

        h6.text-muted {
            font-size: 0.8rem;
        }

        .btn-action {
            width: 30px;
            height: 30px;
        }
    }

    /* Search functionality styles */
    .search-wrapper {
        position: relative;
    }

    .search-wrapper .form-control {
        padding-left: 40px;
        border-radius: 8px;
    }

    .search-wrapper .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
    }

    /* Loading spinner */
    .spinner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
        border-radius: 10px;
    }

    .search-results-count {
        background-color: #f8f9fa;
        border-radius: 50px;
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 500;
        margin-left: 10px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">ประวัติการได้รับเหรียญตรา</h2>
        <div>
            <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-2">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-primary bg-opacity-25 me-3">
                        <i class="fas fa-medal text-white"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">จำนวนเหรียญทั้งหมด</h6>
                        <h4 class="mb-0">{{ number_format($totalBadges) }}</h4>
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
                        <h6 class="text-muted mb-1">ผู้ได้รับเหรียญ</h6>
                        <h4 class="mb-0">{{ number_format($uniqueUsers) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-warning bg-opacity-10 me-3">
                        <i class="fas fa-calendar-alt text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">ได้รับเดือนนี้</h6>
                        <h4 class="mb-0">{{ number_format($monthlyBadges) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-danger bg-opacity-10 me-3">
                        <i class="fas fa-coins text-danger"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">คะแนนที่ได้รับรวม</h6>
                        <h4 class="mb-0">{{ number_format($totalPoints) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm filter-section">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">
                        <button class="btn btn-link p-0 text-decoration-none text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse">
                            <i class="fas fa-filter me-2 text-primary"></i> ตัวกรอง <i class="fas fa-chevron-down ms-2 small"></i>
                        </button>
                    </h5>
                    <div class="medal-count mt-2 mt-md-0">
                        <i class="fas fa-medal text-primary me-1"></i> ทั้งหมด: {{ $badgeHistory->total() ?? 0 }} รายการ
                    </div>
                </div>
                <div class="collapse show" id="filterCollapse">
                    <div class="card-body pt-3">
                        <form action="{{ route('admin.badges.history') }}" method="GET" class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <label for="user_id" class="form-label">ผู้ใช้</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->user_id }}" @if(request('user_id') == $user->user_id) selected @endif>
                                            {{ $user->username }} ({{ $user->firstname }} {{ $user->lastname }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <label for="badge_type" class="form-label">ประเภทเหรียญ</label>
                                <select name="badge_type" id="badge_type" class="form-select">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach($badgeTypes as $type)
                                        <option value="{{ $type->type }}" @if(request('badge_type') == $type->type) selected @endif>
                                            @if($type->type == 'distance')
                                                ระยะทาง
                                            @elseif($type->type == 'calories')
                                                แคลอรี่
                                            @elseif($type->type == 'streak')
                                                วิ่งต่อเนื่อง
                                            @elseif($type->type == 'speed')
                                                ความเร็ว
                                            @elseif($type->type == 'event')
                                                กิจกรรม
                                            @else
                                                {{ $type->type }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <label for="date_start" class="form-label">วันที่เริ่มต้น</label>
                                <input type="date" name="date_start" id="date_start" class="form-control" value="{{ request('date_start') }}">
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <label for="date_end" class="form-label">วันที่สิ้นสุด</label>
                                <input type="date" name="date_end" id="date_end" class="form-control" value="{{ request('date_end') }}">
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <label for="search_input" class="form-label">ค้นหา</label>
                                <div class="search-wrapper">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" id="search_input" class="form-control" placeholder="ค้นหารายการ..." value="{{ request('search') }}">
                                </div>
                            </div>

                            <div class="col-md-2 col-12 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-search me-1"></i> กรอง
                                    </button>
                                    @if(request()->anyFilled(['user_id', 'badge_type', 'date_start', 'date_end']))
                                    <a href="{{ route('admin.badges.history') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-medal me-2 text-primary"></i>
                ประวัติการได้รับเหรียญตรา
            </h5>
            <div id="search-results-count" class="search-results-count d-none">
                <i class="fas fa-search me-1"></i> <span id="count-number">0</span> รายการ
            </div>
        </div>
        <div class="card-body p-0 position-relative">
            <div id="results-loading" class="spinner-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center d-none d-md-table-cell">ลำดับ</th>
                            <th width="15%">ผู้ใช้</th>
                            <th width="10%" class="text-center">เหรียญตรา</th>
                            <th width="25%" class="d-none d-md-table-cell">รายละเอียด</th>
                            <th width="15%" class="d-none d-lg-table-cell">ประเภท</th>
                            <th width="10%" class="text-center">คะแนน</th>
                            <th width="10%">วันที่ได้รับ</th>
                            <th width="10%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="history-list-container">
                        @if($badgeHistory->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted mb-3">
                                        <i class="fas fa-medal fa-4x"></i>
                                    </div>
                                    <h5>ไม่พบข้อมูลประวัติการได้รับเหรียญตรา</h5>
                                    <p class="text-muted">ยังไม่มีผู้ใช้ได้รับเหรียญตราหรือไม่พบข้อมูลตามเงื่อนไขที่กรอง</p>
                                </td>
                            </tr>
                        @else
                            @foreach($badgeHistory as $index => $item)
                                <tr>
                                    <td class="text-center d-none d-md-table-cell">{{ $badgeHistory->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2 d-none d-sm-block">
                                                @if(!empty($item->profile_image) && file_exists(public_path('profile_images/' . $item->profile_image)))
                                                    <img src="{{ asset('profile_images/' . $item->profile_image) }}" class="rounded-circle" width="40" height="40" alt="Profile" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="fw-bold user-link">{{ $item->username }}</span>
                                                <div class="small text-muted d-none d-sm-block">{{ $item->firstname ?? '' }} {{ $item->lastname ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.badges.show', $item->badge_id) }}" class="d-inline-block">
                                        <img src="{{ asset('storage/' . $item->badge_image) }}" alt="{{ $item->badge_name }}" class="badge-image" width="60">
                                        </a>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="fw-bold">{{ $item->badge_name }}</div>
                                        <div class="small text-muted">{{ Str::limit($item->badge_desc, 60) }}</div>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @php
                                            $typeIcons = [
                                                'distance' => 'fa-route',
                                                'calories' => 'fa-fire',
                                                'streak' => 'fa-calendar-check',
                                                'speed' => 'fa-tachometer-alt',
                                                'event' => 'fa-trophy'
                                            ];
                                            $typeColors = [
                                                'distance' => 'success',
                                                'calories' => 'danger',
                                                'streak' => 'success',
                                                'speed' => 'info',
                                                'event' => 'warning'
                                            ];

                                            $color = isset($typeColors[$item->type]) ? $typeColors[$item->type] : 'secondary';
                                            $icon = isset($typeIcons[$item->type]) ? $typeIcons[$item->type] : 'fa-medal';
                                        @endphp

                                        <span class="badge bg-{{ $color }} d-inline-block px-2 py-1 rounded-pill">
                                            <i class="fas {{ $icon }} me-1"></i>
                                        @if($item->type == 'distance')
                                                ระยะทาง {{ $item->criteria }} กม.
                                        @elseif($item->type == 'calories')
                                                แคลอรี่ {{ $item->criteria }} kcal
                                        @elseif($item->type == 'streak')
                                                วิ่งต่อเนื่อง {{ $item->criteria }} วัน
                                        @elseif($item->type == 'speed')
                                                ความเร็ว {{ $item->criteria }} กม./ชม.
                                        @elseif($item->type == 'event')
                                                กิจกรรม {{ $item->criteria }} ครั้ง
                                        @else
                                                {{ $item->type }} {{ $item->criteria }}
                                        @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $key = $item->user_id . '_' . $item->badge_id;
                                            $points = isset($pointsHistory[$key]) ? $pointsHistory[$key][0]->points : '--';
                                        @endphp
                                        @if(is_numeric($points))
                                            <span class="badge bg-success fs-6 px-3 py-2">+{{ $points }}</span>
                                        @else
                                            <span class="badge bg-secondary fs-6 px-3 py-2">{{ $points }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $earnedDate = \Carbon\Carbon::parse($item->earned_at);
                                            $thaiYear = $earnedDate->year + 543;
                                            $formattedDate = $earnedDate->locale('th')->translatedFormat('j M').' '.substr($thaiYear, 2);
                                        @endphp
                                        {{ $formattedDate }}
                                        <div class="small text-muted d-none d-md-block">{{ $earnedDate->format('H:i') }} น.</div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.badges.show', $item->badge_id) }}" class="btn btn-sm btn-info btn-action mb-1" title="ดูรายละเอียดเหรียญตรา">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.show', $item->user_id) }}" class="btn btn-sm btn-primary btn-action mb-1" title="ดูข้อมูลผู้ใช้">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4 mb-4" id="pagination-container">
                {{ $badgeHistory->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // เพิ่ม Select2 สำหรับตัวกรองผู้ใช้
        $('#user_id').select2({
            placeholder: "-- เลือกผู้ใช้ --",
            allowClear: true,
            width: '100%' // เพิ่มความเต็มหน้าจอขนาดเล็ก
        });

        // เพิ่ม Select2 สำหรับตัวกรองประเภทเหรียญ
        $('#badge_type').select2({
            placeholder: "-- เลือกประเภท --",
            allowClear: true,
            width: '100%' // เพิ่มความเต็มหน้าจอขนาดเล็ก
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // ปรับขนาดเมื่อมีการเปลี่ยนขนาดหน้าจอ
        $(window).resize(function() {
            handleResponsiveElements();
        });

        // เรียกใช้ฟังก์ชันครั้งแรก
        handleResponsiveElements();

        // ฟังก์ชันปรับแต่งองค์ประกอบตาม responsive
        function handleResponsiveElements() {
            // ปรับความกว้างของ Select2 ให้เต็มพื้นที่
            setTimeout(function() {
                $('#user_id').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                });

                $('#badge_type').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                });
            }, 100);

            // ตรวจสอบขนาดหน้าจอและซ่อน/แสดงองค์ประกอบให้เหมาะสม
            if (window.innerWidth < 576) {
                // ปรับสำหรับหน้าจอโทรศัพท์
                $('.badge-stat-card').addClass('mb-2');
                $('.btn-action').addClass('btn-sm').css('width', '28px').css('height', '28px');
            } else {
                // ปรับสำหรับหน้าจอใหญ่
                $('.badge-stat-card').removeClass('mb-2');
                $('.btn-action').removeClass('btn-sm').css('width', '').css('height', '');
            }
        }

        // กำหนดตัวแปรสำหรับการค้นหาแบบ realtime
        let searchTimeout;
        const searchDelay = 500; // ระยะเวลารอก่อนส่งคำขอค้นหา (ms)

        // ฟังก์ชันสำหรับการค้นหาแบบ realtime
        $('#search_input').on('input', function() {
            clearTimeout(searchTimeout);
            const searchValue = $(this).val().trim();

            if (searchValue.length >= 2 || searchValue.length === 0) {
                $('#results-loading').removeClass('d-none');

                searchTimeout = setTimeout(function() {
                    performSearch(searchValue);
                }, searchDelay);
            }
        });

        // ฟังก์ชันสำหรับการส่งคำขอค้นหา AJAX
        function performSearch(searchValue) {
            $.ajax({
                url: "{{ route('admin.badges.history.api.search') }}",
                type: "GET",
                data: {
                    search: searchValue,
                    user_id: $('#user_id').val(),
                    badge_type: $('#badge_type').val()
                },
                success: function(response) {
                    if (response.success) {
                        // อัปเดตตาราง
                        $('#history-list-container').html(response.html);

                        // อัปเดตการแบ่งหน้า
                        $('#pagination-container').html(response.pagination);

                        // แสดงจำนวนผลลัพธ์
                        $('#count-number').text(response.count);
                        $('#search-results-count').removeClass('d-none');

                        // ถ้าไม่มีการค้นหา ซ่อนจำนวนผลลัพธ์
                        if (searchValue.length === 0 && $('#user_id').val() === '' && $('#badge_type').val() === '') {
                            $('#search-results-count').addClass('d-none');
                        }
                    }

                    // ซ่อน loading
                    $('#results-loading').addClass('d-none');
                },
                error: function() {
                    // ซ่อน loading กรณีเกิดข้อผิดพลาด
                    $('#results-loading').addClass('d-none');

                    // แสดงข้อความแจ้งเตือน
                    alert('เกิดข้อผิดพลาดในการค้นหา กรุณาลองใหม่อีกครั้ง');
                }
            });
        }
    });
</script>
@endsection
