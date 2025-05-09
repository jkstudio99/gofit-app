@extends('layouts.app')

@section('title', 'กิจกรรมของฉัน')

@section('styles')
<style>
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

    .event-img-container {
        position: relative;
        height: 160px;
    }

    .event-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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

    .event-meta {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .event-meta i {
        width: 18px;
        margin-right: 8px;
        color: #2DC679;
    }

    .empty-state {
        padding: 50px 0;
        text-align: center;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d1d1;
        margin-bottom: 1rem;
    }

    .timeline-item {
        padding-left: 30px;
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-item::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 2px;
        height: 100%;
        background-color: #e0e0e0;
    }

    .timeline-icon {
        position: absolute;
        left: -12px;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .timeline-content {
        padding-left: 15px;
    }

    .registration-status {
        border-radius: 50px;
        padding: 0.25rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
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

    /* Search Box */
    .search-box {
        border-radius: 30px 0 0 30px;
        padding-left: 15px;
    }

    .search-box:focus {
        box-shadow: none;
        border-color: #ced4da;
    }

    .search-box + .btn {
        border-radius: 0 30px 30px 0;
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

    /* Custom styling for cancel button hover state */
    .btn-outline-danger:hover {
        color: #fff !important;
    }

    .btn-outline-danger:hover i {
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="mb-0">กิจกรรมของฉัน</h2>
            <p class="text-muted">รายการกิจกรรมที่คุณลงทะเบียนเข้าร่วม</p>
        </div>
        <div>
            <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list-alt me-1"></i> กิจกรรมทั้งหมด
            </a>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-calendar-check text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">กิจกรรมทั้งหมด</h6>
                        <h4 class="mb-0">{{ $registrations->total() }}</h4>
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
                        <h4 class="mb-0">{{ $activeCount ?? 0 }}</h4>
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
                        <h4 class="mb-0">{{ $upcomingCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-info bg-opacity-10 me-3">
                        <i class="fas fa-medal text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">เสร็จสิ้นแล้ว</h6>
                        <h4 class="mb-0">{{ $completedCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status filters in card with tabs -->
    <div class="card shadow-sm mb-4">
        <div class="card-body pt-4">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="eventStatusTabs" role="tablist">
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
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past-events" type="button" role="tab" aria-controls="past-events" aria-selected="false">
                        <i class="fas fa-history me-1"></i> ที่ผ่านมา
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled-events" type="button" role="tab" aria-controls="cancelled-events" aria-selected="false">
                        <i class="fas fa-times-circle me-1"></i> ยกเลิกแล้ว
                    </button>
                </li>
            </ul>

            <!-- Search Bar -->
            <div class="mb-4">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control search-box" placeholder="ค้นหากิจกรรมของฉัน...">
                    <button class="btn btn-primary" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="eventsTabContent">
                <!-- Tab: ทั้งหมด -->
                <div class="tab-pane fade show active" id="all-events" role="tabpanel" aria-labelledby="all-tab">
                    @if ($registrations->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-calendar-times fa-5x text-muted"></i>
                        </div>
                        <h5>ไม่มีกิจกรรม</h5>
                        <p class="text-muted mb-4">คุณยังไม่มีกิจกรรมที่ลงทะเบียน</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($registrations as $registration)
                            @if ($registration->event->hasEnded())
                                @continue
                            @endif
                            <div class="col">
                                <div class="card event-card h-100 shadow-sm">
                                    <!-- Event Image -->
                                    <div class="event-img-container">
                                        <img src="{{ asset('storage/' . ($registration->event->event_image ?? $registration->event->image_url ?? 'events/default.jpg')) }}"
                                             alt="{{ $registration->event->title }}">

                                        <!-- Event Status -->
                                        <div class="event-status">
                                            @if ($registration->event->hasEnded())
                                                <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                                            @elseif ($registration->event->isActive())
                                                <span class="badge bg-success">กำลังดำเนินการ</span>
                                            @else
                                                <span class="badge bg-info">กำลังจะมาถึง</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <!-- Registration Status -->
                                        <div class="mb-3">
                                            @if ($registration->status == 'registered')
                                                <span class="registration-status bg-success bg-opacity-10 text-success">
                                                    <i class="fas fa-check-circle me-1"></i> ลงทะเบียนแล้ว
                                                </span>
                                            @elseif ($registration->status == 'cancelled')
                                                <span class="registration-status bg-danger bg-opacity-10 text-danger">
                                                    <i class="fas fa-times-circle me-1"></i> ยกเลิกแล้ว
                                                </span>
                                            @elseif ($registration->status == 'attended')
                                                <span class="registration-status bg-primary bg-opacity-10 text-primary">
                                                    <i class="fas fa-clipboard-check me-1"></i> เข้าร่วมแล้ว
                                                </span>
                                            @elseif ($registration->status == 'absent')
                                                <span class="registration-status bg-warning bg-opacity-10 text-warning">
                                                    <i class="fas fa-user-slash me-1"></i> ไม่ได้เข้าร่วม
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Event Title -->
                                        <h5 class="card-title mb-3">{{ $registration->event->title ?? $registration->event->event_name }}</h5>

                                        <!-- Event Meta -->
                                        <div class="event-meta">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->locale('th')->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->format('H:i') }} น.</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span class="text-truncate">{{ $registration->event->location }}</span>
                                        </div>

                                        <!-- Registration Date -->
                                        <div class="event-meta mt-2">
                                            <i class="fas fa-calendar-check"></i>
                                            <small class="text-muted">ลงทะเบียนเมื่อ: {{ \Carbon\Carbon::parse($registration->registered_at)->locale('th')->translatedFormat('d M Y') }}</small>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('events.show', $registration->event->event_id) }}" class="btn btn-outline-primary flex-grow-1">
                                                <i class="fas fa-info-circle me-1"></i> รายละเอียด
                                            </a>

                                            @if (!$registration->event->hasEnded() && $registration->status == 'registered')
                                                <form action="{{ route('events.cancel', $registration->event->event_id) }}" method="POST" class="flex-grow-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger w-100"
                                                            onclick="return confirm('คุณแน่ใจที่จะยกเลิกการลงทะเบียนหรือไม่?')">
                                                        <i class="fas fa-times-circle me-1"></i> ยกเลิก
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Tab: กำลังจะมาถึง -->
                <div class="tab-pane fade" id="upcoming-events" role="tabpanel" aria-labelledby="upcoming-tab">
                    @php
                        $upcomingEvents = $registrations->filter(function($registration) {
                            return !$registration->event->hasStarted() && !$registration->event->hasEnded() && $registration->status == 'registered';
                        });
                    @endphp

                    @if ($upcomingEvents->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-hourglass fa-5x text-muted"></i>
                        </div>
                        <h5>ไม่พบกิจกรรม</h5>
                        <p class="text-muted">ไม่มีกิจกรรมที่กำลังจะมาถึง</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($upcomingEvents as $registration)
                            <div class="col">
                                <div class="card event-card h-100 shadow-sm">
                                    <!-- Event Image -->
                                    <div class="event-img-container">
                                        <img src="{{ asset('storage/' . ($registration->event->event_image ?? $registration->event->image_url ?? 'events/default.jpg')) }}"
                                             alt="{{ $registration->event->title }}">

                                        <!-- Event Status -->
                                        <div class="event-status">
                                            <span class="badge bg-info">กำลังจะมาถึง</span>
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <!-- Registration Status -->
                                        <div class="mb-3">
                                            <span class="registration-status bg-success bg-opacity-10 text-success">
                                                <i class="fas fa-check-circle me-1"></i> ลงทะเบียนแล้ว
                                            </span>
                                        </div>

                                        <!-- Event Title -->
                                        <h5 class="card-title mb-3">{{ $registration->event->title ?? $registration->event->event_name }}</h5>

                                        <!-- Event Meta -->
                                        <div class="event-meta">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->locale('th')->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->format('H:i') }} น.</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span class="text-truncate">{{ $registration->event->location }}</span>
                                        </div>

                                        <!-- Registration Date -->
                                        <div class="event-meta mt-2">
                                            <i class="fas fa-calendar-check"></i>
                                            <small class="text-muted">ลงทะเบียนเมื่อ: {{ \Carbon\Carbon::parse($registration->registered_at)->locale('th')->translatedFormat('d M Y') }}</small>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('events.show', $registration->event->event_id) }}" class="btn btn-outline-primary flex-grow-1">
                                                <i class="fas fa-info-circle me-1"></i> รายละเอียด
                                            </a>

                                            <form action="{{ route('events.cancel', $registration->event->event_id) }}" method="POST" class="flex-grow-1">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger w-100"
                                                        onclick="return confirm('คุณแน่ใจที่จะยกเลิกการลงทะเบียนหรือไม่?')">
                                                    <i class="fas fa-times-circle me-1"></i> ยกเลิก
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Tab: กำลังดำเนินการ -->
                <div class="tab-pane fade" id="active-events" role="tabpanel" aria-labelledby="active-tab">
                    @php
                        $activeEvents = $registrations->filter(function($registration) {
                            return $registration->event->isActive() && $registration->status == 'registered';
                        });
                    @endphp

                    @if ($activeEvents->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-running fa-5x text-muted"></i>
                        </div>
                        <h5>ไม่พบกิจกรรม</h5>
                        <p class="text-muted">ไม่มีกิจกรรมที่กำลังดำเนินการอยู่</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($activeEvents as $registration)
                            <div class="col">
                                <div class="card event-card h-100 shadow-sm">
                                    <!-- Event Image -->
                                    <div class="event-img-container">
                                        <img src="{{ asset('storage/' . ($registration->event->event_image ?? $registration->event->image_url ?? 'events/default.jpg')) }}"
                                             alt="{{ $registration->event->title }}">

                                        <!-- Event Status -->
                                        <div class="event-status">
                                            <span class="badge bg-success">กำลังดำเนินการ</span>
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <!-- Registration Status -->
                                        <div class="mb-3">
                                            <span class="registration-status bg-success bg-opacity-10 text-success">
                                                <i class="fas fa-check-circle me-1"></i> ลงทะเบียนแล้ว
                                            </span>
                                        </div>

                                        <!-- Event Title -->
                                        <h5 class="card-title mb-3">{{ $registration->event->title ?? $registration->event->event_name }}</h5>

                                        <!-- Event Meta -->
                                        <div class="event-meta">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->locale('th')->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->format('H:i') }} น.</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span class="text-truncate">{{ $registration->event->location }}</span>
                                        </div>

                                        <!-- Registration Date -->
                                        <div class="event-meta mt-2">
                                            <i class="fas fa-calendar-check"></i>
                                            <small class="text-muted">ลงทะเบียนเมื่อ: {{ \Carbon\Carbon::parse($registration->registered_at)->locale('th')->translatedFormat('d M Y') }}</small>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('events.show', $registration->event->event_id) }}" class="btn btn-outline-primary flex-grow-1">
                                                <i class="fas fa-info-circle me-1"></i> รายละเอียด
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Tab: ที่ผ่านมา -->
                <div class="tab-pane fade" id="past-events" role="tabpanel" aria-labelledby="past-tab">
                    @php
                        $pastEvents = $registrations->filter(function($registration) {
                            return $registration->event->hasEnded() && $registration->status == 'registered';
                        });
                    @endphp

                    @if ($pastEvents->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-history fa-5x text-muted"></i>
                        </div>
                        <h5>ไม่พบกิจกรรม</h5>
                        <p class="text-muted">ไม่มีกิจกรรมที่ผ่านมา</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($pastEvents as $registration)
                            <div class="col">
                                <div class="card event-card h-100 shadow-sm">
                                    <!-- Event Image -->
                                    <div class="event-img-container">
                                        <img src="{{ asset('storage/' . ($registration->event->event_image ?? $registration->event->image_url ?? 'events/default.jpg')) }}"
                                             alt="{{ $registration->event->title }}">

                                        <!-- Event Status -->
                                        <div class="event-status">
                                            <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <!-- Registration Status -->
                                        <div class="mb-3">
                                            <span class="registration-status bg-success bg-opacity-10 text-success">
                                                <i class="fas fa-check-circle me-1"></i> ลงทะเบียนแล้ว
                                            </span>
                                        </div>

                                        <!-- Event Title -->
                                        <h5 class="card-title mb-3">{{ $registration->event->title ?? $registration->event->event_name }}</h5>

                                        <!-- Event Meta -->
                                        <div class="event-meta">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->locale('th')->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->format('H:i') }} น.</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span class="text-truncate">{{ $registration->event->location }}</span>
                                        </div>

                                        <!-- Registration Date -->
                                        <div class="event-meta mt-2">
                                            <i class="fas fa-calendar-check"></i>
                                            <small class="text-muted">ลงทะเบียนเมื่อ: {{ \Carbon\Carbon::parse($registration->registered_at)->locale('th')->translatedFormat('d M Y') }}</small>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('events.show', $registration->event->event_id) }}" class="btn btn-outline-primary flex-grow-1">
                                                <i class="fas fa-info-circle me-1"></i> รายละเอียด
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Tab: ยกเลิกแล้ว -->
                <div class="tab-pane fade" id="cancelled-events" role="tabpanel" aria-labelledby="cancelled-tab">
                    @php
                        $cancelledEvents = $registrations->filter(function($registration) {
                            return $registration->status == 'cancelled';
                        });
                    @endphp

                    @if ($cancelledEvents->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-times-circle fa-5x text-muted"></i>
                        </div>
                        <h5>ไม่พบกิจกรรม</h5>
                        <p class="text-muted">ไม่มีกิจกรรมที่ยกเลิกแล้ว</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($cancelledEvents as $registration)
                            <div class="col">
                                <div class="card event-card h-100 shadow-sm">
                                    <!-- Event Image -->
                                    <div class="event-img-container">
                                        <img src="{{ asset('storage/' . ($registration->event->event_image ?? $registration->event->image_url ?? 'events/default.jpg')) }}"
                                             alt="{{ $registration->event->title }}">

                                        <!-- Event Status -->
                                        <div class="event-status">
                                            @if ($registration->event->hasEnded())
                                                <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                                            @elseif ($registration->event->isActive())
                                                <span class="badge bg-success">กำลังดำเนินการ</span>
                                            @else
                                                <span class="badge bg-info">กำลังจะมาถึง</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <!-- Registration Status -->
                                        <div class="mb-3">
                                            <span class="registration-status bg-danger bg-opacity-10 text-danger">
                                                <i class="fas fa-times-circle me-1"></i> ยกเลิกแล้ว
                                            </span>
                                        </div>

                                        <!-- Event Title -->
                                        <h5 class="card-title mb-3">{{ $registration->event->title ?? $registration->event->event_name }}</h5>

                                        <!-- Event Meta -->
                                        <div class="event-meta">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->locale('th')->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ \Carbon\Carbon::parse($registration->event->start_datetime)->format('H:i') }} น.</span>
                                        </div>
                                        <div class="event-meta">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span class="text-truncate">{{ $registration->event->location }}</span>
                                        </div>

                                        <!-- Cancellation Date -->
                                        <div class="event-meta mt-2">
                                            <i class="fas fa-times-circle"></i>
                                            <small class="text-muted">ยกเลิกเมื่อ: {{ \Carbon\Carbon::parse($registration->cancelled_at)->locale('th')->translatedFormat('d M Y') }}</small>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('events.show', $registration->event->event_id) }}" class="btn btn-outline-primary flex-grow-1">
                                                <i class="fas fa-info-circle me-1"></i> รายละเอียด
                                            </a>

                                            @if (!$registration->event->hasStarted() && !$registration->event->isFull())
                                                <form action="{{ route('events.register', $registration->event->event_id) }}" method="POST" class="flex-grow-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success w-100">
                                                        <i class="fas fa-redo me-1"></i> ลงทะเบียนใหม่
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $registrations->appends(request()->except(['page']))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ฟังก์ชั่นค้นหากิจกรรม
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');

        searchButton.addEventListener('click', function() {
            performSearch();
        });

        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        });

        function performSearch() {
            const searchTerm = searchInput.value.trim().toLowerCase();
            let foundEvents = false;

            // ซ่อนทุกการ์ดกิจกรรมก่อน
            document.querySelectorAll('.tab-pane.active .col').forEach(container => {
                const card = container.querySelector('.event-card');
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const location = card.querySelector('.event-meta:nth-child(3) span').textContent.toLowerCase();

                // แสดงเฉพาะการ์ดที่ตรงกับคำค้นหา
                if (searchTerm === '' ||
                    title.includes(searchTerm) ||
                    location.includes(searchTerm)) {
                    container.style.display = '';
                    foundEvents = true;
                } else {
                    container.style.display = 'none';
                }
            });

            // แสดงข้อความเมื่อไม่พบกิจกรรม
            document.querySelectorAll('.tab-pane.active .text-center.py-5').forEach(emptyState => {
                emptyState.style.display = !foundEvents ? '' : 'none';
            });

            document.querySelectorAll('.tab-pane.active .row').forEach(row => {
                row.style.display = foundEvents ? '' : 'none';
            });
        }

        // เมื่อเปลี่ยนแท็บให้รีเซ็ตการค้นหา
        const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function() {
                searchInput.value = '';
                document.querySelectorAll('.col').forEach(card => {
                    card.style.display = '';
                });

                // แสดง/ซ่อน empty state ให้ถูกต้อง
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    const hasEvents = pane.querySelector('.row .col') !== null &&
                                      pane.querySelector('.row').children.length > 0;
                    if (pane.classList.contains('active')) {
                        if (pane.querySelector('.text-center.py-5')) {
                            pane.querySelector('.text-center.py-5').style.display = hasEvents ? 'none' : '';
                        }
                        if (pane.querySelector('.row')) {
                            pane.querySelector('.row').style.display = hasEvents ? '' : 'none';
                        }
                    }
                });
            });
        });
    });
</script>
@endsection
