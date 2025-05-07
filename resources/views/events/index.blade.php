@extends('layouts.app')

@section('title', 'กิจกรรมทั้งหมด')

@section('styles')
<style>
    .event-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
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
    }

    .event-status {
        position: absolute;
        top: 10px;
        right: 10px;
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

    .view-toggle .btn {
        border-radius: 4px;
        padding: 6px 12px;
    }

    .filter-badge {
        cursor: pointer;
        transition: all 0.2s;
        border: none !important;
    }

    .filter-badge:hover, .filter-badge.active {
        background-color: #2DC679 !important;
        color: white !important;
    }

    .hover-translate {
        transition: transform 0.3s ease;
    }

    .hover-translate:hover {
        transform: translateY(-5px);
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

    /* Stats Cards Styles */
    .event-stat-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .event-stat-card:hover {
        transform: translateY(-5px);
    }

    .event-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
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

    .card-body {
        padding-top: 0;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="mb-0">กิจกรรมทั้งหมด</h2>
            <p class="text-muted">ค้นหาและเข้าร่วมกิจกรรมออกกำลังกายต่างๆ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('events.my') }}" class="btn btn-outline-primary">
                <i class="fas fa-bookmark me-1"></i> กิจกรรมของฉัน
            </a>
            @can('create', App\Models\Event::class)
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> สร้างกิจกรรม
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
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
        <div class="col-md-3 col-sm-6 mb-3">
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
        <div class="col-md-3 col-sm-6 mb-3">
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
        <div class="col-md-3 col-sm-6 mb-3">
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

    <!-- Events Grid with Tabs -->
    <div class="card shadow-sm mb-4">
        <div class="card-body pt-4">
            <!-- Tab Navigation - แบบใหม่ใช้ Bootstrap client-side tabs -->
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
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past-events" type="button" role="tab" aria-controls="past-events" aria-selected="false">
                        <i class="fas fa-history me-1"></i> ที่ผ่านมา
                    </button>
                </li>
            </ul>

            <!-- Search Bar -->
            <div class="mb-4">
                <div class="d-flex">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="ค้นหากิจกรรม..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="button" id="searchButton">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="eventsTabContent">
                <!-- Tab: ทั้งหมด -->
                <div class="tab-pane fade show active" id="all-events" role="tabpanel" aria-labelledby="all-tab">
                    @if (count($events->filter(function($event) { return !$event->hasEnded(); })) === 0)
                    <div class="empty-state bg-light rounded">
                        <i class="fas fa-calendar-times"></i>
                        <h4>ไม่พบกิจกรรม</h4>
                        <p class="text-muted">ไม่มีกิจกรรมที่ตรงกับเงื่อนไข</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($events as $event)
                            @if ($event->hasEnded())
                                @continue
                            @endif
                            @include('events.partials.event-card', ['event' => $event])
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Tab: กำลังจะมาถึง -->
                <div class="tab-pane fade" id="upcoming-events" role="tabpanel" aria-labelledby="upcoming-tab">
                    @php
                        $upcomingEvents = $events->filter(function($event) {
                            return !$event->hasStarted() && !$event->hasEnded();
                        });
                    @endphp

                    @if ($upcomingEvents->isEmpty())
                    <div class="empty-state bg-light rounded">
                        <i class="fas fa-calendar-times"></i>
                        <h4>ไม่พบกิจกรรม</h4>
                        <p class="text-muted">ไม่มีกิจกรรมที่กำลังจะมาถึง</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($upcomingEvents as $event)
                            @include('events.partials.event-card', ['event' => $event])
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Tab: กำลังดำเนินการ -->
                <div class="tab-pane fade" id="active-events" role="tabpanel" aria-labelledby="active-tab">
                    @php
                        $activeEvents = $events->filter(function($event) {
                            return $event->isActive();
                        });
                    @endphp

                    @if ($activeEvents->isEmpty())
                    <div class="empty-state bg-light rounded">
                        <i class="fas fa-calendar-times"></i>
                        <h4>ไม่พบกิจกรรม</h4>
                        <p class="text-muted">ไม่มีกิจกรรมที่กำลังดำเนินการอยู่</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($activeEvents as $event)
                            @include('events.partials.event-card', ['event' => $event])
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Tab: ที่ผ่านมา -->
                <div class="tab-pane fade" id="past-events" role="tabpanel" aria-labelledby="past-tab">
                    @php
                        $pastEvents = $events->filter(function($event) {
                            return $event->hasEnded();
                        });
                    @endphp

                    @if ($pastEvents->isEmpty())
                    <div class="empty-state bg-light rounded">
                        <i class="fas fa-calendar-times"></i>
                        <h4>ไม่พบกิจกรรม</h4>
                        <p class="text-muted">ไม่มีกิจกรรมที่ผ่านมา</p>
                    </div>
                    @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($pastEvents as $event)
                            @include('events.partials.event-card', ['event' => $event])
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $events->appends(request()->query())->links() }}
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
            document.querySelectorAll('.event-card').forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const description = card.querySelector('.card-text').textContent.toLowerCase();
                const location = card.querySelector('.event-stat:nth-child(3) span').textContent.toLowerCase();

                // แสดงเฉพาะการ์ดที่ตรงกับคำค้นหา
                if (searchTerm === '' ||
                    title.includes(searchTerm) ||
                    description.includes(searchTerm) ||
                    location.includes(searchTerm)) {
                    card.closest('.col-lg-4').style.display = '';
                    foundEvents = true;
                } else {
                    card.closest('.col-lg-4').style.display = 'none';
                }
            });

            // แสดงข้อความเมื่อไม่พบกิจกรรม
            document.querySelectorAll('.tab-pane.active .empty-state').forEach(emptyState => {
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
                document.querySelectorAll('.event-card').forEach(card => {
                    card.closest('.col-lg-4').style.display = '';
                });

                // แสดง/ซ่อน empty state ให้ถูกต้อง
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    const hasEvents = pane.querySelector('.row .col-lg-4') !== null;
                    if (pane.classList.contains('active')) {
                        if (pane.querySelector('.empty-state')) {
                            pane.querySelector('.empty-state').style.display = hasEvents ? 'none' : '';
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

