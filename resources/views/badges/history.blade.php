@extends('layouts.app')

@section('title', 'ประวัติการรับเหรียญตรา')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">ประวัติการรับเหรียญตรา</h2>
        <a href="{{ route('badges.index') }}" class="btn btn-outline-primary mobile-history-btn">
            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-md-inline">กลับไปหน้าเหรียญตรา</span><span class="d-inline d-md-none">กลับ</span>
        </a>
    </div>
    <p class="text-muted mb-3">ประวัติเหรียญตราที่คุณได้รับทั้งหมด เรียงตามลำดับล่าสุด</p>

    <!-- Stats overview -->
    <div class="row mb-4">
        <div class="col-6 col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-medal text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">เหรียญที่ปลดล็อคแล้ว</h6>
                        <h4 class="mb-0">{{ $badgeHistory->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-success bg-opacity-10 me-3">
                        <i class="fas fa-award text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">คะแนนรวมที่ได้รับ</h6>
                        <h4 class="mb-0">{{ $pointsHistory->sum('points') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-info bg-opacity-10 me-3">
                        <i class="fas fa-calendar-check text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">วันที่ได้รับเหรียญล่าสุด</h6>
                        <h4 class="mb-0">
                            @if($badgeHistory->isNotEmpty())
                                @php
                                    $lastBadgeDate = \Carbon\Carbon::parse($badgeHistory->first()->earned_at);
                                    $thaiYear = $lastBadgeDate->year + 543;
                                    $formattedDate = $lastBadgeDate->locale('th')->translatedFormat('j M').' '.substr($thaiYear, 2);
                                @endphp
                                {{ $formattedDate }}
                            @else
                                -
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">ประวัติเหรียญตรา</h5>
        </div>
        <div class="card-body">
            @if($badgeHistory->isEmpty())
                <div class="text-center py-5">
                    <img src="{{ asset('images/empty-badges.svg') }}" alt="ไม่มีเหรียญตรา" class="img-fluid mb-3" style="max-width: 200px;">
                    <h5>ยังไม่มีประวัติการรับเหรียญตรา</h5>
                    <p class="text-muted">คุณยังไม่ได้รับเหรียญตราใดๆ เริ่มวิ่งเพื่อรับเหรียญตราแรกของคุณ!</p>
                    <a href="{{ route('run.index') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-running me-1"></i> เริ่มวิ่งเลย
                    </a>
                </div>
            @else
                <div class="badge-timeline">
                    @foreach($badgeHistory as $badge)
                        <div class="badge-timeline-item">
                            <div class="badge-timeline-point
                                @if($badge->type == 'distance') bg-primary
                                @elseif($badge->type == 'calories') bg-danger
                                @elseif($badge->type == 'streak') bg-success
                                @elseif($badge->type == 'speed') bg-info
                                @elseif($badge->type == 'event') bg-warning
                                @else bg-secondary @endif">
                                <i class="fas
                                    @if($badge->type == 'distance') fa-route
                                    @elseif($badge->type == 'calories') fa-fire
                                    @elseif($badge->type == 'streak') fa-calendar-check
                                    @elseif($badge->type == 'speed') fa-tachometer-alt
                                    @elseif($badge->type == 'event') fa-trophy
                                    @else fa-medal @endif"></i>
                            </div>
                            <div class="badge-timeline-card">
                                <div class="badge-timeline-date">
                                    @php
                                        $earnedDate = \Carbon\Carbon::parse($badge->earned_at);
                                        $thaiYear = $earnedDate->year + 543;
                                        $formattedDate = $earnedDate->locale('th')->translatedFormat('j M').' '.substr($thaiYear, 2).' '.$earnedDate->format('H:i').' น.';
                                    @endphp
                                    {{ $formattedDate }}
                                </div>
                                <div class="badge-timeline-content">
                                    <div class="row">
                                        <div class="col-4 col-md-2 text-center mb-3 mb-md-0">
                                            <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                                alt="{{ $badge->badge_name }}"
                                                class="badge-timeline-image">
                                        </div>
                                        <div class="col-8 col-md-7 mb-3 mb-md-0">
                                            <h5 class="badge-timeline-title">{{ $badge->badge_name }}</h5>
                                            <p class="badge-timeline-desc">{{ $badge->badge_desc }}</p>
                                            <div class="badge-type-pill
                                                @if($badge->type == 'distance') badge-type-distance
                                                @elseif($badge->type == 'calories') badge-type-calories
                                                @elseif($badge->type == 'streak') badge-type-streak
                                                @elseif($badge->type == 'speed') badge-type-speed
                                                @elseif($badge->type == 'event') badge-type-event
                                                @endif">
                                                @if($badge->type == 'distance')
                                                    <i class="fas fa-route me-1"></i> ระยะทาง {{ $badge->criteria }} กม.
                                                @elseif($badge->type == 'calories')
                                                    <i class="fas fa-fire me-1"></i> {{ $badge->criteria }} แคลอรี่
                                                @elseif($badge->type == 'streak')
                                                    <i class="fas fa-calendar-check me-1"></i> {{ $badge->criteria }} วันติดต่อกัน
                                                @elseif($badge->type == 'speed')
                                                    <i class="fas fa-tachometer-alt me-1"></i> {{ $badge->criteria }} กม./ชม.
                                                @elseif($badge->type == 'event')
                                                    <i class="fas fa-trophy me-1"></i> {{ $badge->criteria }} กิจกรรม
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 text-center mt-2 mt-md-0">
                                            <div class="badge-timeline-points">
                                                @if(isset($pointsHistory[$badge->badge_id]))
                                                    <div class="badge-point-value">+{{ $pointsHistory[$badge->badge_id]->points }}</div>
                                                    <div class="badge-point-label">คะแนน</div>
                                                @else
                                                    <div class="badge-point-value">--</div>
                                                    <div class="badge-point-label">คะแนน</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Timeline styles */
    .badge-timeline {
        position: relative;
        padding-left: 40px;
    }

    .badge-timeline:before {
        content: '';
        position: absolute;
        left: 19px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }

    .badge-timeline-item {
        position: relative;
        margin-bottom: 25px;
    }

    .badge-timeline-item:last-child {
        margin-bottom: 0;
    }

    .badge-timeline-point {
        position: absolute;
        left: -40px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        z-index: 2;
    }

    .badge-timeline-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .badge-timeline-date {
        background-color: #f8f9fa;
        padding: 8px 15px;
        font-size: 14px;
        color: #6c757d;
        border-bottom: 1px solid #e9ecef;
    }

    .badge-timeline-content {
        padding: 15px;
    }

    .badge-timeline-title {
        font-size: 18px;
        margin-bottom: 5px;
        font-weight: 600;
        color: #212529;
    }

    .badge-timeline-desc {
        color: #6c757d;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .badge-timeline-image {
        max-width: 80px;
        max-height: 80px;
        object-fit: contain;
        margin: 0 auto;
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

    /* Badge type pills */
    .badge-type-pill {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-right: 8px;
    }

    .badge-type-distance {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .badge-type-calories {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .badge-type-streak {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .badge-type-speed {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }

    .badge-type-event {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    /* Points earned */
    .badge-timeline-points {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
        display: inline-block;
    }

    .badge-point-value {
        font-size: 24px;
        font-weight: 700;
        color: #198754;
    }

    .badge-point-label {
        font-size: 14px;
        color: #6c757d;
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

        .mobile-history-btn {
            width: auto;
            margin-left: auto;
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .row.mb-4 {
            margin-left: -10px;
            margin-right: -10px;
        }

        .badge-timeline {
            padding-left: 30px;
        }

        .badge-timeline:before {
            left: 14px;
        }

        .badge-timeline-point {
            left: -30px;
            width: 30px;
            height: 30px;
            font-size: 14px;
        }

        .badge-timeline-item {
            margin-bottom: 20px;
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

        /* ปรับปุ่มกลับในโหมดมือถือ */
        .mobile-history-btn {
            font-size: 0.9rem;
            padding: 0.4rem 1rem;
            border-radius: 30px;
        }

        /* ปรับการแสดงผลการ์ดสถิติบนมือถือ */
        .row.mb-4 {
            margin-left: -8px;
            margin-right: -8px;
        }

        .col-6.col-md-4.mb-3, .col-12.col-md-4.mb-3 {
            padding-left: 8px;
            padding-right: 8px;
            margin-bottom: 16px;
        }

        .badge-stat-card {
            border-radius: 12px;
        }

        .badge-stat-icon {
            width: 45px;
            height: 45px;
            font-size: 20px;
            margin-right: 10px !important;
        }

        .badge-stat-card .card-body {
            padding: 15px;
        }

        .badge-stat-card h6 {
            font-size: 0.85rem;
            margin-bottom: 5px !important;
        }

        .badge-stat-card h4 {
            font-size: 1.4rem;
            font-weight: 600;
        }

        /* ปรับการแสดงผล Timeline บนมือถือ */
        .badge-timeline {
            padding-left: 25px;
        }

        .badge-timeline:before {
            left: 12px;
        }

        .badge-timeline-point {
            left: -25px;
            width: 25px;
            height: 25px;
            font-size: 12px;
        }

        .badge-timeline-date {
            padding: 6px 12px;
            font-size: 12px;
        }

        .badge-timeline-content {
            padding: 10px;
        }

        .badge-timeline-title {
            font-size: 16px;
        }

        .badge-timeline-desc {
            font-size: 13px;
        }

        .badge-timeline-image {
            max-width: 60px;
            max-height: 60px;
        }

        .badge-point-value {
            font-size: 20px;
        }

        .badge-point-label {
            font-size: 12px;
        }
    }
</style>
@endsection
