@extends('layouts.app')

@section('title', 'กิจกรรมของฉัน')

@section('styles')
<style>
    .event-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
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
    }

    .event-status {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .status-tab {
        cursor: pointer;
        padding: 10px 20px;
        border-radius: 30px;
        margin-right: 10px;
        transition: all 0.2s;
    }

    .status-tab:hover, .status-tab.active {
        background-color: #2DC679;
        color: white;
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
        padding: 80px 0;
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
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
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

    <!-- Status filter -->
    <div class="mb-4">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link status-tab {{ !request('status') || request('status') == 'all' ? 'active bg-primary text-white' : 'bg-light' }}"
                   href="{{ route('events.my', ['status' => 'all']) }}">
                    <i class="fas fa-list me-1"></i> ทั้งหมด
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link status-tab {{ request('status') == 'upcoming' ? 'active bg-primary text-white' : 'bg-light' }}"
                   href="{{ route('events.my', ['status' => 'upcoming']) }}">
                    <i class="fas fa-hourglass-start me-1"></i> กำลังจะมาถึง
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link status-tab {{ request('status') == 'active' ? 'active bg-primary text-white' : 'bg-light' }}"
                   href="{{ route('events.my', ['status' => 'active']) }}">
                    <i class="fas fa-play-circle me-1"></i> กำลังดำเนินการ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link status-tab {{ request('status') == 'past' ? 'active bg-primary text-white' : 'bg-light' }}"
                   href="{{ route('events.my', ['status' => 'past']) }}">
                    <i class="fas fa-history me-1"></i> ที่ผ่านมา
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link status-tab {{ request('status') == 'cancelled' ? 'active bg-primary text-white' : 'bg-light' }}"
                   href="{{ route('events.my', ['status' => 'cancelled']) }}">
                    <i class="fas fa-times-circle me-1"></i> ยกเลิกแล้ว
                </a>
            </li>
        </ul>
    </div>

    <!-- Empty state -->
    @if ($registrations->isEmpty())
    <div class="empty-state bg-light rounded">
        <i class="fas fa-calendar-times"></i>
        <h4>ไม่มีกิจกรรม</h4>
        <p class="text-muted">
            คุณยังไม่มีกิจกรรมในสถานะที่เลือก
        </p>
        <div class="mt-3">
            <a href="{{ route('events.index') }}" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> ค้นหากิจกรรม
            </a>
        </div>
    </div>
    @else

    <!-- Event Cards -->
    <div class="row">
        @foreach ($registrations as $registration)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card event-card h-100 shadow-sm">
                <!-- Event Image -->
                <div class="event-img-container">
                    <img src="{{ asset('storage/' . ($registration->event->event_image ?? $registration->event->image_url ?? 'events/default.jpg')) }}"
                         alt="{{ $registration->event->title }}">

                    <!-- Event Badges -->
                    <div class="event-badges">
                        <span class="badge category-badge bg-primary">
                            {{ ucfirst($registration->event->category ?? 'กิจกรรม') }}
                        </span>
                    </div>

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
                    <h5 class="card-title mb-3">{{ $registration->event->title }}</h5>

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

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $registrations->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
