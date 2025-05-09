@extends('layouts.app')

@section('title', 'เหรียญตราของฉัน')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">เหรียญตราของฉัน</h2>
        <a href="{{ route('badges.history') }}" class="btn btn-outline-primary mobile-history-btn">
            <i class="fas fa-history me-1"></i> <span class="d-none d-md-inline">ประวัติการรับเหรียญตรา</span><span class="d-inline d-md-none">ประวัติ</span>
        </a>
    </div>
    <p class="text-muted mb-3">รวบรวมเหรียญตราความสำเร็จจากการวิ่งของคุณ</p>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-medal text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">เหรียญทั้งหมด</h6>
                        <h4 class="mb-0">{{ $totalBadges ?? $badges->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-success bg-opacity-10 me-3">
                        <i class="fas fa-unlock-alt text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">ปลดล็อคแล้ว</h6>
                        <h4 class="mb-0">{{ $unlockedCount ?? $badges->where('isUnlocked', true)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-warning bg-opacity-10 me-3">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">รอปลดล็อค</h6>
                        <h4 class="mb-0">{{ $lockedCount ?? $badges->where('isUnlocked', false)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 badge-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="badge-stat-icon bg-info bg-opacity-10 me-3">
                        <i class="fas fa-percentage text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">ความสำเร็จ</h6>
                        <h4 class="mb-0">{{ $progressPercentage ?? number_format(($badges->where('isUnlocked', true)->count() / $badges->count()) * 100, 0) }}%</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Badges Grid -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body pt-4">
                    @if($badges->isEmpty())
                        <div class="text-center py-5">
                            <img src="{{ asset('images/empty-badges.svg') }}" alt="ไม่มีเหรียญตรา" class="img-fluid mb-3" style="max-width: 200px;">
                            <h5>ยังไม่มีเหรียญตรา</h5>
                            <p class="text-muted">เริ่มวิ่งเพื่อรับเหรียญตราแรกของคุณ!</p>
                        </div>
                    @else
                        <!-- จัดกลุ่มเหรียญตามประเภท -->
                        @php
                            // ใช้ collect()->unique() เพื่อป้องกันข้อมูลซ้ำซ้อน
                            $allBadges = collect($badges)->unique('badge_id');

                            // จัดกลุ่มตามประเภท
                            $badgesByType = $allBadges->groupBy('type');

                            // กำหนดลำดับการแสดงผล และชื่อแสดงผลภาษาไทย
                            $typeOrder = ['calories', 'distance', 'streak', 'speed', 'event'];
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
                                'distance' => 'primary',
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
                                            <span class="badge bg-{{ $typeColors[$type] }} bg-opacity-75 ms-1 rounded-pill">{{ $badgesByType[$type]->count() }}</span>
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
                                                    <span class="badge bg-{{ $typeColors[$type] }} bg-opacity-75">
                                                        {{ $badgesByType[$type]->where('isUnlocked', true)->count() }}/{{ $badgesByType[$type]->count() }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
                                                @foreach($badgesByType[$type]->sortByDesc('isUnlocked') as $badge)
                            <div class="col">
                                                        <div class="card h-100 badge-card {{ $badge->isUnlocked() ? 'unlocked' : 'locked' }}"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                             title="{{ $badge->badge_desc }}">

                                            <!-- Badge Type Indicator -->
                                                            <span class="badge badge-type bg-{{ $typeColors[$type] }}">
                                                                <i class="fas {{ $typeIcons[$type] }}"></i>
                                            </span>

                                            <div class="badge-img-container">
                                    <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                                                     class="badge-img {{ $badge->isUnlocked() ? '' : 'opacity-50 grayscale' }}"
                                        alt="{{ $badge->badge_name }}">
                                            </div>

                                            <div class="card-body">
                                                <h6 class="card-title">{{ $badge->badge_name }}</h6>
                                                <p class="card-text badge-requirement small text-muted">
                                                    {{ $badge->getRequirementText() }}
                                                </p>

                                                <!-- แสดงคะแนนที่จะได้รับ -->
                                                                <div class="badge-points small fw-bold">
                                                                    <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                                        <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                                                                    </span>
                                                </div>

                                                                @if(!$badge->isUnlocked())
                                                <div class="progress mt-2" style="height: 6px;">
                                                    @php
                                                                            // Reset progress percentage for clean calculation
                                                            $progressPercentage = 0;

                                                                            // Try to use the dedicated progress calculation method first
                                                                            if (method_exists($badge, 'calculateProgressPercentage')) {
                                                                                $progressPercentage = $badge->calculateProgressPercentage();
                                                                            }
                                                                            // Fallback: Try to get progress from calculatedProgress property
                                                                            else if (isset($badge->calculatedProgress) && is_numeric($badge->calculatedProgress)) {
                                                                                $progressPercentage = round($badge->calculatedProgress);
                                                                            }
                                                                            // Last resort: Try to extract from progress text if available
                                                                            else if (method_exists($badge, 'progress')) {
                                                                                $progressText = $badge->progress();
                                                            if (strpos($progressText, '%') !== false) {
                                                                                    preg_match('/(\d+)%/', $progressText, $matches);
                                                                                    if (isset($matches[1]) && is_numeric($matches[1])) {
                                                                                        $progressPercentage = $matches[1];
                                                                                    }
                                                            }
                                                        }

                                                                            // Ensure valid percentage range
                                                                            $progressPercentage = max(0, min(100, intval($progressPercentage)));
                                                    @endphp
                                                                        <div class="progress-bar bg-{{ $typeColors[$type] }}"
                                                        role="progressbar"
                                                        style="width: {{ $progressPercentage }}%"
                                                        aria-valuenow="{{ $progressPercentage }}"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2">
                                                                        <span class="small text-{{ $typeColors[$type] }} fw-bold">{{ $progressPercentage }}%</span>
                                                </div>

                                                @if($badge->isEligibleToUnlock())
                                                    <form id="unlock-form-{{ $type }}-{{ $badge->badge_id }}" action="{{ route('badges.unlock', $badge->badge_id) }}" method="POST" class="mt-2">
                                                        @csrf
                                                        <button type="button"
                                                                                    onclick="confirmUnlock({{ $badge->badge_id }}, '{{ $badge->badge_name }}', '{{ asset('storage/' . $badge->badge_image) }}', {{ $badge->points ?? 100 }})"
                                                                class="btn btn-success btn-sm w-100">
                                                            <i class="fas fa-unlock-alt me-1"></i> ปลดล็อค
                                                        </button>
                                                    </form>
                                                @endif

                                                <div class="badge-overlay">
                                                    <i class="fas fa-lock"></i>
                                                </div>
                                                                @else
                                                                    <div class="unlocked-info mt-2 text-center">
                                                                        <span class="badge bg-success w-100 py-2">
                                                                            <i class="fas fa-check-circle me-1"></i> ปลดล็อคแล้ว
                                                                        </span>
                                                                    </div>
                                                                @endif
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
                                                            วิ่งให้ได้ระยะทางตามเป้าหมายเพื่อรับเหรียญ
                                                        @elseif($type == 'calories')
                                                            เผาผลาญแคลอรี่ตามเป้าหมายเพื่อรับเหรียญ
                                                        @elseif($type == 'streak')
                                                            วิ่งติดต่อกันตามจำนวนวันเพื่อรับเหรียญ
                                                        @elseif($type == 'speed')
                                                            วิ่งด้วยความเร็วเฉลี่ยตามเป้าหมายเพื่อรับเหรียญ
                                                        @elseif($type == 'event')
                                                            เข้าร่วมกิจกรรมตามจำนวนครั้งเพื่อรับเหรียญ
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="ms-auto">
                                                    <span class="badge bg-{{ $typeColors[$type] }} rounded-pill px-3 py-2">
                                                        <i class="fas fa-medal me-1"></i>
                                                        {{ $badgesByType[$type]->where('isUnlocked', true)->count() }}/{{ $badgesByType[$type]->count() }}
                                                    </span>
                                                </div>
                                    </div>
                                </div>

                                        <!-- แยกแสดงเหรียญที่ปลดล็อคแล้วกับยังไม่ปลดล็อค -->
                                        @php
                                            $unlockedBadges = $badgesByType[$type]->filter(function($badge) {
                                                return $badge->isUnlocked();
                                            });

                                            $lockedBadges = $badgesByType[$type]->filter(function($badge) {
                                                return !$badge->isUnlocked();
                                            });
                                        @endphp

                                        @if($unlockedBadges->isNotEmpty())
                                            <div class="mb-4">
                                                <div class="card-header-custom bg-success bg-opacity-10 text-success rounded p-2 mb-3">
                                                    <i class="fas fa-unlock-alt me-2"></i> เหรียญที่ปลดล็อคแล้ว
                                                </div>
                                                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                                                    @foreach($unlockedBadges as $badge)
                                    <div class="col">
                                        <div class="card h-100 badge-card unlocked"
                                             data-bs-toggle="tooltip"
                                             data-bs-placement="top"
                                             title="{{ $badge->badge_desc }}">

                                            <!-- Badge Type Indicator -->
                                                                <span class="badge badge-type bg-{{ $typeColors[$type] }}">
                                                                    <i class="fas {{ $typeIcons[$type] }}"></i>
                                            </span>

                                            <div class="badge-img-container">
                                                <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                                     class="badge-img"
                                                     alt="{{ $badge->badge_name }}">
                                            </div>

                                            <div class="card-body">
                                                <h6 class="card-title">{{ $badge->badge_name }}</h6>
                                                <p class="card-text badge-requirement small text-muted">
                                                    {{ $badge->getRequirementText() }}
                                                </p>

                                                <!-- แสดงคะแนนที่ได้รับ -->
                                                                    <div class="badge-points small fw-bold">
                                                                        <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                                            <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                                                                        </span>
                                                </div>

                                                <div class="unlocked-info mt-2 text-center">
                                                    <span class="badge bg-success w-100 py-2">
                                                        <i class="fas fa-check-circle me-1"></i> ปลดล็อคแล้ว
                                                    </span>
                                                </div>
                                            </div>
                                </div>
                            </div>
                        @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($lockedBadges->isNotEmpty())
                                            <div>
                                                <div class="card-header-custom bg-secondary bg-opacity-10 text-secondary rounded p-2 mb-3">
                                                    <i class="fas fa-lock me-2"></i> เหรียญที่ยังไม่ปลดล็อค
                                                </div>
                                                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                                                    @foreach($lockedBadges as $badge)
                                                        <div class="col">
                                                            <div class="card h-100 badge-card locked"
                                                                 data-bs-toggle="tooltip"
                                                                 data-bs-placement="top"
                                                                 title="{{ $badge->badge_desc }}">

                                                                <!-- Badge Type Indicator -->
                                                                <span class="badge badge-type bg-{{ $typeColors[$type] }}">
                                                                    <i class="fas {{ $typeIcons[$type] }}"></i>
                                                                </span>

                                                                <div class="badge-img-container">
                                                                    <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                                                         class="badge-img opacity-50 grayscale"
                                                                         alt="{{ $badge->badge_name }}">
                                                                </div>

                                                                <div class="card-body">
                                                                    <h6 class="card-title">{{ $badge->badge_name }}</h6>
                                                                    <p class="card-text badge-requirement small text-muted">
                                                                        {{ $badge->getRequirementText() }}
                                                                    </p>

                                                                    <!-- แสดงคะแนนที่จะได้รับ -->
                                                                    <div class="badge-points small fw-bold">
                                                                        <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                                            <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                                                                        </span>
                                                                    </div>

                                                                    <div class="progress mt-2" style="height: 6px;">
                                                                        @php
                                                                            // Reset progress percentage for clean calculation
                                                                            $progressPercentage = 0;

                                                                            // Try to use the dedicated progress calculation method first
                                                                            if (method_exists($badge, 'calculateProgressPercentage')) {
                                                                                $progressPercentage = $badge->calculateProgressPercentage();
                                                                            }
                                                                            // Fallback: Try to get progress from calculatedProgress property
                                                                            else if (isset($badge->calculatedProgress) && is_numeric($badge->calculatedProgress)) {
                                                                                $progressPercentage = round($badge->calculatedProgress);
                                                                            }
                                                                            // Last resort: Try to extract from progress text if available
                                                                            else if (method_exists($badge, 'progress')) {
                                                                                $progressText = $badge->progress();
                                                                                if (strpos($progressText, '%') !== false) {
                                                                                    preg_match('/(\d+)%/', $progressText, $matches);
                                                                                    if (isset($matches[1]) && is_numeric($matches[1])) {
                                                                                        $progressPercentage = $matches[1];
                                                                                    }
                                                                                }
                                                                            }

                                                                            // Ensure valid percentage range
                                                                            $progressPercentage = max(0, min(100, intval($progressPercentage)));
                                                                        @endphp
                                                                        <div class="progress-bar bg-{{ $typeColors[$type] }}"
                                                                             role="progressbar"
                                                                             style="width: {{ $progressPercentage }}%"
                                                                             aria-valuenow="{{ $progressPercentage }}"
                                                                             aria-valuemin="0"
                                                                             aria-valuemax="100"></div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between mt-2">
                                                                        <span class="small text-{{ $typeColors[$type] }} fw-bold">{{ $progressPercentage }}%</span>
                                                                    </div>

                                                                    @if($badge->isEligibleToUnlock())
                                                                        <form id="unlock-form-{{ $type }}-{{ $badge->badge_id }}" action="{{ route('badges.unlock', $badge->badge_id) }}" method="POST" class="mt-2">
                                                                            @csrf
                                                                            <button type="button"
                                                                                    onclick="confirmUnlock({{ $badge->badge_id }}, '{{ $badge->badge_name }}', '{{ asset('storage/' . $badge->badge_image) }}', {{ $badge->points ?? 100 }})"
                                                                                    class="btn btn-success btn-sm w-100">
                                                                                <i class="fas fa-unlock-alt me-1"></i> ปลดล็อค
                                                                            </button>
                                                                        </form>
                                                                    @endif

                                                                    <div class="badge-overlay">
                                                                        <i class="fas fa-lock"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                    </div>
                        @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Explanation Card -->
    <div class="row">
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
                                    <td>คุณจะได้รับเหรียญตราเมื่อวิ่งได้ระยะทางสะสมตามที่กำหนด เช่น 5 กม., 10 กม., 20 กม., 50 กม., 100 กม.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-fire me-2 text-danger"></i> เหรียญจากแคลอรี่</td>
                                    <td>เผาผลาญแคลอรี่ตามที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อเผาผลาญแคลอรี่สะสมตามที่กำหนด เช่น 100, 500, 1,000, 2,500, 5,000 แคลอรี่</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-check me-2 text-success"></i> เหรียญจากการวิ่งติดต่อกัน</td>
                                    <td>วิ่งติดต่อกันตามจำนวนวันที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อวิ่งติดต่อกันตามจำนวนวันที่กำหนด เช่น 3 วัน, 7 วัน, 14 วัน, 30 วัน</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-tachometer-alt me-2 text-info"></i> เหรียญจากความเร็ว</td>
                                    <td>วิ่งด้วยความเร็วเฉลี่ยตามที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อวิ่งด้วยความเร็วเฉลี่ยตามที่กำหนด เช่น 5 กม./ชม., 8 กม./ชม., 10 กม./ชม.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-trophy me-2 text-warning"></i> เหรียญจากการเข้าร่วมกิจกรรม</td>
                                    <td>เข้าร่วมกิจกรรมตามจำนวนครั้งที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อเข้าร่วมกิจกรรมตามที่กำหนด เช่น เข้าร่วมกิจกรรม 1 ครั้ง, 3 ครั้ง, 5 ครั้ง</td>
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

@section('styles')
    <link href="{{ asset('css/badges-mobile.css') }}" rel="stylesheet">
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

        /* Badge History Button */
        .btn-outline-primary:hover {
            color: white !important;
        }

        /* Filter Buttons */
        .badge.filter-badge:hover,
        .btn-outline-secondary:hover,
        .badge.bg-light:hover,
        .badge.bg-light.text-dark:hover {
            color: white !important;
            text-decoration: none !important;
        }

        /* Remove underlines everywhere */
        a,
        button,
        .btn,
        .badge,
        a:hover,
        button:hover,
        .btn:hover,
        .badge:hover {
            text-decoration: none !important;
        }

        /* Filter badge specific styling */
        .filter-badge {
            text-decoration: none !important;
            border: none !important;
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

            .btn-outline-primary {
                width: auto;
                margin-left: auto;
                padding: 8px 16px;
                font-size: 0.9rem;
            }

            .row.mb-4 {
                margin-left: -10px;
                margin-right: -10px;
            }

            .col-md-3.col-sm-6 {
                padding-left: 10px;
                padding-right: 10px;
            }

            .nav-tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
                scrollbar-width: none; /* Firefox */
                -ms-overflow-style: none; /* IE and Edge */
                padding-bottom: 5px;
            }

            .nav-tabs::-webkit-scrollbar {
                display: none; /* Chrome, Safari and Opera */
            }

            .nav-tabs .nav-link {
                white-space: nowrap;
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .badge-type-header {
                padding: 12px;
            }

            .badge-type-header h4 {
                font-size: 1.3rem;
            }

            .card-header-custom {
                padding: 8px 12px;
            }

            .row-cols-2 {
                margin-left: -8px;
                margin-right: -8px;
            }

            .row-cols-2 > .col {
                padding-left: 8px;
                padding-right: 8px;
            }

            .badge-img-container {
                height: 100px;
                padding: 10px;
            }

            .badge-img {
                max-height: 80px;
                max-width: 80px;
            }

            .card-body {
                padding: 1rem;
            }

            .card-title {
                font-size: 0.95rem;
            }

            .card-text.badge-requirement {
                font-size: 0.8rem;
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

            /* ปรับปุ่มประวัติในโหมดมือถือ */
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

            .col-6.col-md-3.mb-3 {
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

            .badge-img-container {
                height: 90px;
            }

            .badge-img {
                max-height: 70px;
                max-width: 70px;
            }

            .card-body {
                padding: 0.75rem;
            }

            .card-title {
                font-size: 0.9rem;
            }

            .table-responsive {
                border-radius: 10px;
                overflow: hidden;
            }

            .table th, .table td {
                padding: 0.75rem;
            }
        }
    </style>
@endsection

@section('scripts')
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
        const savedTab = localStorage.getItem('selectedBadgeTab');
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
                localStorage.setItem('selectedBadgeTab', this.id);
            });
        });
    });

    // Function to confirm badge unlock with SweetAlert
    function confirmUnlock(badgeId, badgeName, imageUrl, pointsToEarn) {
        Swal.fire({
            title: 'ปลดล็อคเหรียญตรา?',
            html: `
                <div class="text-center mb-4">
                    <img src="${imageUrl}" alt="${badgeName}" style="max-height: 100px; max-width: 100px; margin-bottom: 15px;">
                    <h5 class="mb-2">${badgeName}</h5>
                    <div class="text-primary">
                        <i class="fas fa-coins text-warning me-1"></i> <strong>${pointsToEarn} คะแนน</strong>
                    </div>
                </div>
                <p>คุณต้องการปลดล็อคเหรียญตรานี้ใช่หรือไม่?</p>
            `,
            icon: false,
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ปลดล็อค',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดง loading
                Swal.fire({
                    title: 'กำลังดำเนินการ...',
                    html: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Find the correct form to submit - check both formats of form IDs
                const form = document.getElementById(`unlock-form-${badgeId}`) ||
                             document.querySelector(`