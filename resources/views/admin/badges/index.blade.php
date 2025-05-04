@extends('layouts.admin')

@section('title', 'จัดการเหรียญตรา')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
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

    .badge-card.unlocked:hover {
        box-shadow: 0 10px 20px rgba(40,167,69,0.2);
    }

    .badge-img-container {
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        padding: 15px;
    }

    .badge-img {
        max-height: 90px;
        max-width: 90px;
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
        top: 10px;
        right: 10px;
        font-size: 0.7rem;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .badge-stats {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 0.7rem;
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
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .badge-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
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
        border-radius: 10px;
        overflow: hidden;
    }

    .badge-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
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
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
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
            <form action="{{ route('admin.badges.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box"
                               placeholder="ค้นหาตามชื่อหรือคำอธิบาย..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
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
                                <select name="type" class="form-select">
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
                                <select name="sort" class="form-select">
                                    <option value="created_at" {{ $sortField == 'created_at' ? 'selected' : '' }}>วันที่สร้าง</option>
                                    <option value="badge_name" {{ $sortField == 'badge_name' ? 'selected' : '' }}>ชื่อเหรียญตรา</option>
                                    <option value="type" {{ $sortField == 'type' ? 'selected' : '' }}>ประเภท</option>
                                    <option value="criteria" {{ $sortField == 'criteria' ? 'selected' : '' }}>เกณฑ์</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">ลำดับ</label>
                                <select name="direction" class="form-select">
                                    <option value="asc" {{ $sortDirection == 'asc' ? 'selected' : '' }}>น้อยไปมาก</option>
                                    <option value="desc" {{ $sortDirection == 'desc' ? 'selected' : '' }}>มากไปน้อย</option>
                                </select>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i> รีเซ็ตตัวกรอง
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> กรอง
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
                            @if(request()->hasAny(['search', 'type', 'sort']))
                            <span class="badge bg-success ms-2">
                                <i class="fas fa-filter me-1"></i> กำลังแสดงผลลัพธ์: {{ $badges->total() }} รายการ
                            </span>
                            @endif
                        </h5>
                        <span class="badge bg-info text-white rounded-pill px-3 py-2">
                            <i class="fas fa-medal me-1"></i> เหรียญตราทั้งหมด: {{ $badges->total() }}
                        </span>
                    </div>
                </div>

                <div class="card-body pt-4">
                    @if($badges->isEmpty())
                        <div class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="fas fa-medal fa-4x"></i>
                            </div>
                            <h5>ไม่พบข้อมูลเหรียญตรา</h5>
                            <p class="text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</p>
                        </div>
                    @else
                        @php
                            // จัดกลุ่มตามประเภท
                            $badgesByType = $badges->groupBy('type');

                            // กำหนดลำดับการแสดงผล และชื่อแสดงผลภาษาไทย
                            $typeOrder = ['distance', 'calories', 'streak', 'speed', 'event'];
                            $typeNames = [
                                'distance' => 'ระยะทาง',
                                'calories' => 'แคลอรี่',
                                'streak' => 'ต่อเนื่อง',
                                'speed' => 'ความเร็ว',
                                'event' => 'กิจกรรม'
                            ];
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
                        @endphp

                        <!-- แสดงเหรียญตามประเภท -->
                        <ul class="nav nav-tabs mb-4" id="badgeTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-badges" type="button" role="tab" aria-controls="all-badges" aria-selected="true">
                                    <i class="fas fa-medal me-1"></i> ทั้งหมด
                                </button>
                            </li>
                            @foreach($typeOrder as $type)
                                @if($badgesByType->has($type))
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="{{ $type }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $type }}-badges" type="button" role="tab" aria-controls="{{ $type }}-badges" aria-selected="false">
                                            <i class="fas {{ $typeIcons[$type] }} me-1"></i> {{ $typeNames[$type] }}
                                            <span class="badge bg-{{ $typeColors[$type] }} ms-1 rounded-pill">{{ $badgesByType[$type]->count() }}</span>
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <div class="tab-content" id="badgeTypeContent">
                            <!-- ทั้งหมด -->
                            <div class="tab-pane fade show active" id="all-badges" role="tabpanel" aria-labelledby="all-tab">
                                @foreach($typeOrder as $type)
                                    @if($badgesByType->has($type))
                                        <div class="badge-category-section mb-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="badge-type-icon bg-{{ $typeColors[$type] }} bg-opacity-10 text-{{ $typeColors[$type] }} me-2">
                                                    <i class="fas {{ $typeIcons[$type] }}"></i>
                                                </div>
                                                <h5 class="mb-0">เหรียญ{{ $typeNames[$type] }}</h5>
                                                <div class="ms-auto">
                                                    <span class="badge bg-{{ $typeColors[$type] }}">
                                                        {{ $badgesByType[$type]->count() }} รายการ
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
                                                @foreach($badgesByType[$type] as $badge)
                                                    <div class="col">
                                                        <div class="card h-100 badge-card"
                                                             data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             title="{{ $badge->badge_desc }}">

                                                            <!-- Badge Type Indicator -->
                                                            <span class="badge badge-type bg-{{ $typeColors[$type] }}">
                                                                <i class="fas {{ $typeIcons[$type] }}"></i>
                                                            </span>

                                                            <!-- Users Badge -->
                                                            <div class="badge-stats">
                                                                <a href="{{ route('admin.badges.users', $badge) }}" class="badge bg-light text-dark">
                                                                    <i class="fas fa-users me-1"></i> {{ $badge->users_count ?? 0 }} คน
                                                                </a>
                                                            </div>

                                                            <div class="badge-img-container">
                                                                @if($badge->badge_image)
                                                                    <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                                                         class="badge-img"
                                                                         alt="{{ $badge->badge_name }}">
                                                                @else
                                                                    <div class="text-center text-muted">
                                                                        <i class="fas fa-medal fa-3x"></i>
                                                                        <p class="small mt-2">ไม่มีรูปภาพ</p>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="card-body">
                                                                <h6 class="card-title">{{ $badge->badge_name }}</h6>
                                                                <p class="card-text badge-requirement small text-muted">
                                                                    @if($badge->type == 'distance')
                                                                        วิ่งระยะทางสะสม {{ $badge->criteria }} กม.
                                                                    @elseif($badge->type == 'calories')
                                                                        เผาผลาญแคลอรี่สะสม {{ $badge->criteria }} แคลอรี่
                                                                    @elseif($badge->type == 'streak')
                                                                        วิ่งติดต่อกัน {{ $badge->criteria }} วัน
                                                                    @elseif($badge->type == 'speed')
                                                                        วิ่งด้วยความเร็วเฉลี่ย {{ $badge->criteria }} กม./ชม.
                                                                    @elseif($badge->type == 'event')
                                                                        เข้าร่วมกิจกรรม {{ $badge->criteria }} ครั้ง
                                                                    @else
                                                                        {{ $badge->criteria }}
                                                                    @endif
                                                                </p>

                                                                <!-- แสดงคะแนนที่จะได้รับ -->
                                                                <div class="badge-points small fw-bold">
                                                                    <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                                        <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="card-footer bg-white py-2">
                                                                <div class="d-flex justify-content-center">
                                                                    <a href="{{ route('admin.badges.show', $badge) }}" class="btn btn-sm btn-info badge-action-btn me-2 text-white" title="ดูรายละเอียด">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-sm btn-warning badge-action-btn me-2" title="แก้ไข">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-danger badge-action-btn delete-badge"
                                                                            title="ลบ" data-badge-id="{{ $badge->badge_id }}"
                                                                            data-badge-name="{{ $badge->badge_name }}"
                                                                            data-users-count="{{ $badge->users_count ?? 0 }}">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- แท็บแยกตามประเภท -->
                            @foreach($typeOrder as $type)
                                @if($badgesByType->has($type))
                                    <div class="tab-pane fade" id="{{ $type }}-badges" role="tabpanel" aria-labelledby="{{ $type }}-tab">
                                        <div class="badge-type-header mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="badge-type-icon bg-{{ $typeColors[$type] }} bg-opacity-10 text-{{ $typeColors[$type] }} me-3">
                                                    <i class="fas {{ $typeIcons[$type] }}"></i>
                                                </div>
                                                <div>
                                                    <h4 class="mb-1">เหรียญ{{ $typeNames[$type] }}</h4>
                                                    <p class="text-muted mb-0">
                                                        @if($type == 'distance')
                                                            เหรียญที่ผู้ใช้จะได้รับเมื่อวิ่งได้ระยะทางตามเป้าหมาย
                                                        @elseif($type == 'calories')
                                                            เหรียญที่ผู้ใช้จะได้รับเมื่อเผาผลาญแคลอรี่ตามเป้าหมาย
                                                        @elseif($type == 'streak')
                                                            เหรียญที่ผู้ใช้จะได้รับเมื่อวิ่งติดต่อกันตามจำนวนวัน
                                                        @elseif($type == 'speed')
                                                            เหรียญที่ผู้ใช้จะได้รับเมื่อวิ่งด้วยความเร็วเฉลี่ยตามเป้าหมาย
                                                        @elseif($type == 'event')
                                                            เหรียญที่ผู้ใช้จะได้รับเมื่อเข้าร่วมกิจกรรมตามจำนวนครั้ง
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="ms-auto">
                                                    <span class="badge bg-{{ $typeColors[$type] }} rounded-pill px-3 py-2">
                                                        <i class="fas fa-medal me-1"></i>
                                                        {{ $badgesByType[$type]->count() }} รายการ
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                                            @foreach($badgesByType[$type] as $badge)
                                                <div class="col">
                                                    <div class="card h-100 badge-card"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"
                                                         title="{{ $badge->badge_desc }}">

                                                        <!-- Badge Type Indicator -->
                                                        <span class="badge badge-type bg-{{ $typeColors[$type] }}">
                                                            <i class="fas {{ $typeIcons[$type] }}"></i>
                                                        </span>

                                                        <!-- Users Badge -->
                                                        <div class="badge-stats">
                                                            <a href="{{ route('admin.badges.users', $badge) }}" class="badge bg-light text-dark">
                                                                <i class="fas fa-users me-1"></i> {{ $badge->users_count ?? 0 }} คน
                                                            </a>
                                                        </div>

                                                        <div class="badge-img-container">
                                                            @if($badge->badge_image)
                                                                <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                                                     class="badge-img"
                                                                     alt="{{ $badge->badge_name }}">
                                                            @else
                                                                <div class="text-center text-muted">
                                                                    <i class="fas fa-medal fa-3x"></i>
                                                                    <p class="small mt-2">ไม่มีรูปภาพ</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="card-body">
                                                            <h6 class="card-title">{{ $badge->badge_name }}</h6>
                                                            <p class="card-text badge-requirement small text-muted">
                                                                @if($badge->type == 'distance')
                                                                    วิ่งระยะทางสะสม {{ $badge->criteria }} กม.
                                                                @elseif($badge->type == 'calories')
                                                                    เผาผลาญแคลอรี่สะสม {{ $badge->criteria }} แคลอรี่
                                                                @elseif($badge->type == 'streak')
                                                                    วิ่งติดต่อกัน {{ $badge->criteria }} วัน
                                                                @elseif($badge->type == 'speed')
                                                                    วิ่งด้วยความเร็วเฉลี่ย {{ $badge->criteria }} กม./ชม.
                                                                @elseif($badge->type == 'event')
                                                                    เข้าร่วมกิจกรรม {{ $badge->criteria }} ครั้ง
                                                                @else
                                                                    {{ $badge->criteria }}
                                                                @endif
                                                            </p>

                                                            <!-- แสดงคะแนนที่จะได้รับ -->
                                                            <div class="badge-points small fw-bold">
                                                                <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                                    <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="card-footer bg-white py-2">
                                                            <div class="d-flex justify-content-center">
                                                                <a href="{{ route('admin.badges.show', $badge) }}" class="btn btn-sm btn-info badge-action-btn me-2 text-white" title="ดูรายละเอียด">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-sm btn-warning badge-action-btn me-2" title="แก้ไข">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-danger badge-action-btn delete-badge"
                                                                        title="ลบ" data-badge-id="{{ $badge->badge_id }}"
                                                                        data-badge-name="{{ $badge->badge_name }}"
                                                                        data-users-count="{{ $badge->users_count ?? 0 }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            แสดง {{ $badges->firstItem() ?? 0 }} ถึง {{ $badges->lastItem() ?? 0 }} จาก {{ $badges->total() }} รายการ
                        </div>
                        <div class="pagination-container">
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
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Handle filter badge clicks
        document.querySelectorAll('.filter-badge').forEach(badge => {
            badge.addEventListener('click', function() {
                document.querySelectorAll('.filter-badge').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Handle tab clicks - preserve selected tab on page reload using localStorage
        const tabButtons = document.querySelectorAll('#badgeTypeTabs .nav-link');
        const tabItems = document.querySelectorAll('.tab-pane');

        // Check if there's a saved tab
        const savedTab = localStorage.getItem('selectedAdminBadgeTab');
        if (savedTab && document.getElementById(savedTab)) {
            // Deactivate all tabs
            tabButtons.forEach(button => button.classList.remove('active'));
            tabItems.forEach(item => {
                item.classList.remove('show', 'active');
            });

            // Activate the saved tab
            document.getElementById(savedTab).classList.add('active');
            const targetId = document.getElementById(savedTab).getAttribute('data-bs-target').substring(1);
            document.getElementById(targetId).classList.add('show', 'active');
        }

        // Handle tab clicks
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                localStorage.setItem('selectedAdminBadgeTab', this.id);
            });
        });

        // กำหนดค่าเริ่มต้นสำหรับ SweetAlert2 ทั้งหมด
        Swal.mixin({
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            }
        });

        // กำหนดสี CSS สำหรับปุ่ม SweetAlert
        const style = document.createElement('style');
        style.innerHTML = `
            .swal2-confirm.swal-confirm-btn {
                background-color: #2DC679 !important;
                border-color: #2DC679 !important;
                box-shadow: none !important;
                margin-right: 10px;
            }
            .swal2-confirm:focus {
                box-shadow: 0 0 0 3px rgba(45, 198, 121, 0.3) !important;
            }
            .swal2-actions {
                justify-content: center !important;
                gap: 10px;
            }
        `;
        document.head.appendChild(style);

        // Delete confirmation with SweetAlert2
        const deleteButtons = document.querySelectorAll('.delete-badge');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const badgeId = this.getAttribute('data-badge-id');
                const badgeName = this.getAttribute('data-badge-name');
                const usersCount = parseInt(this.getAttribute('data-users-count'));

                let warningText = 'คุณแน่ใจหรือไม่ที่จะลบเหรียญตรา?';
                if (usersCount > 0) {
                    warningText += ` มีผู้ใช้ ${usersCount} คนที่ได้รับเหรียญตรานี้ ซึ่งจะถูกลบออกด้วย`;
                }

                Swal.fire({
                    title: `ลบเหรียญตรา "${badgeName}"?`,
                    html: `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            คำเตือน: การลบเหรียญตราไม่สามารถกู้คืนได้
                        </div>
                        <p class="mt-3">${warningText}</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2DC679', // GoFit primary color
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> ลบเหรียญตรา',
                    cancelButtonText: 'ยกเลิก',
                    buttonsStyling: true,
                    reverseButtons: false,
                    customClass: {
                        confirmButton: 'swal-confirm-btn',
                        actions: 'justify-content-center gap-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // สร้าง form สำหรับ submit การลบ
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/badges/${badgeId}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Display SweetAlert for session message if exists
        @if(session('success'))
            Swal.fire({
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'ผิดพลาด!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        @endif
    });
</script>

<!-- Include SweetAlert message partial -->
@include('partials.sweetalert-messages')
@endsection
