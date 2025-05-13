@extends('layouts.app')

@section('title', 'กิจกรรมทั้งหมด')

@section('styles')
<style>
    /* Event Cards Styling */
    .event-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        border: none !important;
        position: relative;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .event-card .card-img-top {
        height: 180px;
        object-fit: cover;
    }

    .event-img-container {
        position: relative;
    }

    .event-badges {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        z-index: 2;
    }

    .event-status {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 2;
    }

    .category-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .event-stat {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .event-stat i {
        width: 18px;
        margin-right: 8px;
        text-align: center;
    }

    .participants-progress {
        height: 6px;
        border-radius: 3px;
        margin-top: 5px;
    }

    /* View toggle and filter badges */
    .view-toggle .btn {
        border-radius: 4px;
        padding: 6px 12px;
    }

    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
        border: none !important;
    }

    .filter-badge:hover {
        background-color: #2DC679 !important;
        color: white !important;
    }

    .hover-translate {
        transition: transform 0.3s ease;
    }

    .hover-translate:hover {
        transform: translateY(-5px);
    }

    /* Empty State */
    .empty-state {
        padding: 50px 0;
        text-align: center;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d1d1;
        margin-bottom: 1rem;
    }

    /* Stats Cards Styles */
    .event-stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .event-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }

    .event-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 10px;
        font-size: 20px;
    }

    /* Tab Navigation Styles */
    .nav-tabs .nav-link {
        border: none;
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 500;
        color: #6c757d;
        background-color: #f8f9fa;
        margin-right: 0.5rem;
        transition: all 0.2s;
    }

    .nav-tabs .nav-link:hover {
        background-color: #e9ecef;
    }

    .nav-tabs .nav-link.active {
        color: #fff;
        background-color: #2DC679;
    }

    /* Remove the border/line between tabs and content */
    .nav-tabs {
        border-bottom: none;
    }

    /* Mobile & Tablet Responsive Adjustments */
    @media (max-width: 991.98px) {
        .container {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .d-flex.justify-content-between.align-items-center {
            flex-wrap: wrap;
            gap: 10px;
        }

        h2.mb-0 {
            font-size: 1.6rem;
            margin-bottom: 0.5rem !important;
        }

        .btn-action-group {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
        }

        .event-card .card-img-top {
            height: 150px;
        }

        .mobile-full-width {
            width: 100%;
        }
    }

    /* Specific mobile adjustments */
    @media (max-width: 575.98px) {
        .container {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        h2.mb-0 {
            font-size: 1.5rem;
        }

        /* ปรับการแสดงผลการ์ดสถิติบนมือถือ */
        .row.mb-4 {
            margin-left: -8px;
            margin-right: -8px;
        }

        .col-6.col-md-3.mb-3 {
            padding-left: 8px;
            padding-right: 8px;
            margin-bottom: 16px;
        }

        .event-stat-card {
            border-radius: 12px;
        }

        .event-stat-icon {
            width: 45px !important;
            height: 45px !important;
            font-size: 20px;
            margin-right: 10px !important;
        }

        .event-stat-card .card-body {
            padding: 15px !important;
        }

        .event-stat-card h6 {
            font-size: 0.85rem !important;
            margin-bottom: 5px !important;
        }

        .event-stat-card h4 {
            font-size: 1.4rem !important;
            font-weight: 600 !important;
        }

        .filter-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.7rem !important;
        }

        /* Make action buttons smaller on mobile */
        .btn-sm-mobile {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .event-card .card-body {
            padding: 0.7rem !important;
        }

        .card-header, .card-footer {
            padding: 0.75rem 1rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">กิจกรรมทั้งหมด</h2>
            <p class="text-muted mb-0">ค้นหาและเข้าร่วมกิจกรรมออกกำลังกายต่างๆ</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('events.my') }}" class="btn btn-outline-primary">
                <i class="fas fa-bookmark me-1"></i> <span class="d-none d-md-inline">กิจกรรมของฉัน</span><span class="d-inline d-md-none">ของฉัน</span>
            </a>
            @can('create', App\Models\Event::class)
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> <span class="d-none d-md-inline">สร้างกิจกรรม</span><span class="d-inline d-md-none">สร้าง</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-calendar-alt text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">กิจกรรมทั้งหมด</h6>
                        <h4 class="mb-0">{{ $events->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-success bg-opacity-10 me-3">
                        <i class="fas fa-play-circle text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">กำลังดำเนินการ</h6>
                        <h4 class="mb-0">{{ $activeEventsCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-warning bg-opacity-10 me-3">
                        <i class="fas fa-hourglass-start text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">กำลังจะมาถึง</h6>
                        <h4 class="mb-0">{{ $upcomingEventsCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-info bg-opacity-10 me-3">
                        <i class="fas fa-users text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">ผู้เข้าร่วมทั้งหมด</h6>
                        <h4 class="mb-0">{{ $totalParticipants ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('events.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box"
                               placeholder="ค้นหากิจกรรม..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เก่าสุด</option>
                        <option value="upcoming" {{ request('sort') == 'upcoming' ? 'selected' : '' }}>กำลังจะมาถึง</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>ยอดนิยม</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Grid with Tabs -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body pt-4">
                    <!-- Tab Navigation - ใช้ Bootstrap client-side tabs -->
                    <ul class="nav nav-tabs mb-4" id="eventsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-events" type="button" role="tab" aria-controls="all-events" aria-selected="true">
                                <i class="fas fa-list me-1"></i> ทั้งหมด
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming-events" type="button" role="tab" aria-controls="upcoming-events" aria-selected="false">
                                <i class="fas fa-hourglass-start me-1"></i> กำลังจะมาถึง
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-events" type="button" role="tab" aria-controls="active-events" aria-selected="false">
                                <i class="fas fa-play-circle me-1"></i> กำลังดำเนินการ
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-events" type="button" role="tab" aria-controls="completed-events" aria-selected="false">
                                <i class="fas fa-flag-checkered me-1"></i> เสร็จสิ้นแล้ว
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="eventsTabsContent">
                        <!-- All Events Tab (ไม่แสดงกิจกรรมที่สิ้นสุดแล้ว) -->
                        <div class="tab-pane fade show active" id="all-events" role="tabpanel" aria-labelledby="all-tab">
                            @php
                                $activeAndUpcomingEvents = $events->filter(function($event) {
                                    return $event->status !== 'completed';
                                });
                            @endphp

                            @if ($activeAndUpcomingEvents->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-calendar-times fa-5x text-muted"></i>
                                    </div>
                                    <h5>ไม่พบกิจกรรม</h5>
                                    <p class="text-muted mb-4">ขออภัย ไม่พบกิจกรรมที่กำลังจะมาถึงหรือกำลังดำเนินการอยู่</p>
                                </div>
                            @else
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                    @foreach ($activeAndUpcomingEvents as $event)
                                    <div class="col">
                                        @include('events.partials.event-card')
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $events->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>

                        <!-- Upcoming Events Tab -->
                        <div class="tab-pane fade" id="upcoming-events" role="tabpanel" aria-labelledby="upcoming-tab">
                            @php
                                $upcomingEvents = $events->filter(function($event) {
                                    return $event->status === 'upcoming';
                                });
                            @endphp

                            @if ($upcomingEvents->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-hourglass fa-5x text-muted"></i>
                                    </div>
                                    <h5>ไม่พบกิจกรรมที่กำลังจะมาถึง</h5>
                                    <p class="text-muted">ยังไม่มีกิจกรรมที่กำลังจะเริ่มในขณะนี้</p>
                                </div>
                            @else
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                    @foreach ($upcomingEvents as $event)
                                    <div class="col">
                                        @include('events.partials.event-card')
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Active Events Tab -->
                        <div class="tab-pane fade" id="active-events" role="tabpanel" aria-labelledby="active-tab">
                            @php
                                $activeEvents = $events->filter(function($event) {
                                    return $event->status === 'active';
                                });
                            @endphp

                            @if ($activeEvents->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-running fa-5x text-muted"></i>
                                    </div>
                                    <h5>ไม่พบกิจกรรมที่กำลังดำเนินการ</h5>
                                    <p class="text-muted">ยังไม่มีกิจกรรมที่กำลังดำเนินอยู่ในขณะนี้</p>
                                </div>
                            @else
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                    @foreach ($activeEvents as $event)
                                    <div class="col">
                                        @include('events.partials.event-card')
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Completed Events Tab -->
                        <div class="tab-pane fade" id="completed-events" role="tabpanel" aria-labelledby="completed-tab">
                            @php
                                $completedEvents = $events->filter(function($event) {
                                    return $event->status === 'completed';
                                });
                            @endphp

                            @if ($completedEvents->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-flag-checkered fa-5x text-muted"></i>
                                    </div>
                                    <h5>ไม่พบกิจกรรมที่เสร็จสิ้นแล้ว</h5>
                                    <p class="text-muted">ยังไม่มีกิจกรรมที่เสร็จสิ้นในขณะนี้</p>
                                </div>
                            @else
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                    @foreach ($completedEvents as $event)
                                    <div class="col">
                                        @include('events.partials.event-card')
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How Events Work Section -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-primary">
            <h5 class="mb-0">วิธีการเข้าร่วมกิจกรรม</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-search fa-3x text-primary"></i>
                        </div>
                        <h5>1. ค้นหากิจกรรม</h5>
                        <p class="text-muted">เลือกกิจกรรมที่คุณสนใจจากรายการข้างต้น</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-clipboard-check fa-3x text-primary"></i>
                        </div>
                        <h5>2. ลงทะเบียนเข้าร่วม</h5>
                        <p class="text-muted">คลิกที่ "ลงทะเบียน" เพื่อยืนยันการเข้าร่วมกิจกรรม</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-medal fa-3x text-primary"></i>
                        </div>
                        <h5>3. รับรางวัล</h5>
                        <p class="text-muted">เข้าร่วมและทำกิจกรรมให้สำเร็จเพื่อรับคะแนนและเหรียญตรา</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // เก็บแท็บที่เลือกใน localStorage
        const tabLinks = document.querySelectorAll('.nav-link');

        tabLinks.forEach(tabLink => {
            tabLink.addEventListener('click', function() {
                localStorage.setItem('selectedEventsTab', this.id);
            });
        });

        // โหลดแท็บที่เลือกไว้ล่าสุด
        const selectedTab = localStorage.getItem('selectedEventsTab');
        if (selectedTab) {
            const tab = document.getElementById(selectedTab);
            if (tab) {
                const tabTrigger = new bootstrap.Tab(tab);
                tabTrigger.show();
            }
        }
    });
</script>
@endsection

