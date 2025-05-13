@extends('layouts.app')

@section('title', 'รางวัล')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">รางวัล</h2>
        <a href="{{ route('rewards.history') }}" class="btn btn-outline-primary mobile-history-btn">
            <i class="fas fa-history me-1"></i> <span class="d-none d-md-inline">ประวัติการแลกรางวัล</span><span class="d-inline d-md-none">ประวัติ</span>
        </a>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">แลกคะแนนจากการวิ่งเพื่อรับของรางวัลมากมาย!</p>
        <div class="p-2 bg-light rounded-3 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <strong class="fs-5">{{ auth()->user()->getAvailablePoints() }}</strong>
                    <div class="small text-muted">คะแนนของคุณ</div>
                </div>
                <i class="fas fa-coins fs-4 text-warning"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 reward-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="reward-stat-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-coins text-primary"></i>
                    </div>
                    <div>
                        @php
                            // Calculate user points from point history
                            $earnedPoints = DB::table('tb_point_history')
                                ->where('user_id', auth()->user()->user_id)
                                ->sum('points');

                            // Get points spent on rewards - Use points_spent field instead of points_required
                            $spentPoints = App\Models\Redeem::where('user_id', auth()->user()->user_id)
                                ->where('status', '!=', 'cancelled')
                                ->sum('points_spent');

                            // Available points
                            $availablePoints = max(0, $earnedPoints - $spentPoints);

                            // Let's try to print the values to debug
                            //echo "Earned: $earnedPoints, Spent: $spentPoints, Available: $availablePoints";
                        @endphp
                        <h6 class="text-muted mb-1">คะแนนที่มี</h6>
                        <h4 class="mb-0">{{ $availablePoints }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 reward-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="reward-stat-icon bg-success bg-opacity-10 me-3">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">รางวัลที่แลกได้</h6>
                        <h4 class="mb-0">{{ $rewards->where('points_required', '<=', $availablePoints)->where('quantity', '>', 0)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 reward-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="reward-stat-icon bg-warning bg-opacity-10 me-3">
                        <i class="fas fa-gift text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">รางวัลทั้งหมด</h6>
                        <h4 class="mb-0">{{ $rewards->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 reward-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="reward-stat-icon bg-danger bg-opacity-10 me-3">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">รางวัลที่หมด</h6>
                        <h4 class="mb-0">{{ $rewards->where('quantity', 0)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Sort Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('rewards.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box"
                               placeholder="ค้นหารางวัล..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="points-asc" {{ request('sort') == 'points-asc' ? 'selected' : '' }}>เรียงตามคะแนน (น้อยไปมาก)</option>
                        <option value="points-desc" {{ request('sort') == 'points-desc' ? 'selected' : '' }}>เรียงตามคะแนน (มากไปน้อย)</option>
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Filter Tags -->
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('rewards.index', request()->except('filter')) }}"
               class="badge bg-{{ request('filter') ? 'light text-dark' : 'primary' }} py-2 px-3 filter-badge">
                <i class="fas fa-gift me-1"></i> ทั้งหมด
            </a>

            <a href="{{ route('rewards.index', array_merge(request()->except('filter'), ['filter' => 'available'])) }}"
               class="badge bg-{{ request('filter') == 'available' ? 'success' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-check-circle me-1"></i> แลกได้
            </a>

            <a href="{{ route('rewards.index', array_merge(request()->except('filter'), ['filter' => 'unavailable'])) }}"
               class="badge bg-{{ request('filter') == 'unavailable' ? 'warning' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-clock me-1"></i> คะแนนไม่พอ
            </a>

            <a href="{{ route('rewards.index', array_merge(request()->except('filter'), ['filter' => 'sold-out'])) }}"
               class="badge bg-{{ request('filter') == 'sold-out' ? 'danger' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-times-circle me-1"></i> สินค้าหมด
            </a>
        </div>
    </div>

    <!-- Rewards Grid -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body pt-4">
                    @if($rewards->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-gift fa-5x text-secondary mb-3"></i>
                            <h4>ไม่พบรางวัล</h4>
                            <p class="text-muted">ขออภัย ไม่มีรางวัลในขณะนี้ กรุณาลองใหม่ในภายหลัง</p>
                        </div>
                    @else
                        <!-- จัดกลุ่มรางวัลตามช่วงคะแนน -->
                        @php
                            // คำนวณคะแนนที่ผู้ใช้มี
                            $earnedPoints = DB::table('tb_point_history')
                                ->where('user_id', auth()->user()->user_id)
                                ->sum('points');
                            $spentPoints = App\Models\Redeem::where('user_id', auth()->user()->user_id)
                                ->where('status', '!=', 'cancelled')
                                ->sum('points_spent');
                            $availablePoints = max(0, $earnedPoints - $spentPoints);

                            // จัดกลุ่มรางวัลตามช่วงคะแนน
                            $allRewards = collect($rewards);
                            $rewardsByPoints = [
                                'low' => $allRewards->where('points_required', '<=', 100),
                                'medium' => $allRewards->whereBetween('points_required', [101, 300]),
                                'high' => $allRewards->where('points_required', '>', 300)
                            ];

                            // กำหนดลำดับการแสดงผล ชื่อแสดงผลภาษาไทย ไอคอน และสี
                            $pointsOrder = ['low', 'medium', 'high'];
                            $pointsNames = [
                                'low' => 'คะแนนน้อย (≤ 100)',
                                'medium' => 'คะแนนปานกลาง (101-300)',
                                'high' => 'คะแนนสูง (> 300)'
                            ];
                            $pointsIcons = [
                                'low' => 'fa-coins',
                                'medium' => 'fa-award',
                                'high' => 'fa-trophy'
                            ];
                            $pointsColors = [
                                'low' => 'success',
                                'medium' => 'warning',
                                'high' => 'danger'
                            ];
                        @endphp

                        <!-- แสดงแท็บสำหรับช่วงคะแนนต่างๆ -->
                        <ul class="nav nav-tabs mb-4" id="rewardPointsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-rewards" type="button" role="tab" aria-controls="all-rewards" aria-selected="true">
                                    <i class="fas fa-gift me-1"></i> ทั้งหมด
                                </button>
                            </li>
                            @foreach($pointsOrder as $range)
                                @if($rewardsByPoints[$range]->isNotEmpty())
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="{{ $range }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $range }}-rewards" type="button" role="tab" aria-controls="{{ $range }}-rewards" aria-selected="false">
                                            <i class="fas {{ $pointsIcons[$range] }} me-1"></i> {{ $pointsNames[$range] }}
                                            <span class="badge bg-{{ $pointsColors[$range] }} bg-opacity-75 ms-1 rounded-pill">{{ $rewardsByPoints[$range]->count() }}</span>
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <div class="tab-content" id="rewardPointsContent">
                            <!-- แสดงรางวัลทั้งหมด -->
                            <div class="tab-pane fade show active" id="all-rewards" role="tabpanel" aria-labelledby="all-tab">
                                @foreach($pointsOrder as $range)
                                    @if($rewardsByPoints[$range]->isNotEmpty())
                                        <div class="reward-category-section mb-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="reward-type-icon bg-{{ $pointsColors[$range] }} bg-opacity-10 text-{{ $pointsColors[$range] }} me-2">
                                                    <i class="fas {{ $pointsIcons[$range] }}"></i>
                                                </div>
                                                <h5 class="mb-0">รางวัล{{ $pointsNames[$range] }}</h5>
                                            </div>

                                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
                                                @foreach($rewardsByPoints[$range] as $reward)
                                                    <!-- Reward Card -->
                                                    <div class="col">
                                                        <div class="card h-100 reward-card shadow-sm" id="reward-card-{{ $reward->reward_id }}">
                                                            @php
                                                                // Check if user has already redeemed this reward
                                                                $alreadyRedeemed = App\Models\Redeem::where('user_id', auth()->user()->user_id)
                                                                    ->where('reward_id', $reward->reward_id)
                                                                    ->where('status', '!=', 'cancelled')
                                                                    ->exists();
                                                            @endphp

                                                            @if($alreadyRedeemed)
                                                                <div class="reward-status badge bg-info">เคยแลกแล้ว</div>
                                                            @elseif($reward->quantity <= 0)
                                                                <div class="reward-status badge bg-danger">สินค้าหมด</div>
                                                            @elseif($availablePoints >= $reward->points_required)
                                                                <div class="reward-status badge bg-success">แลกได้</div>
                                                            @else
                                                                <div class="reward-status badge bg-warning">คะแนนไม่พอ</div>
                                                            @endif

                                                            <div class="reward-stock">เหลือ {{ $reward->quantity }} ชิ้น</div>

                                                            <div class="reward-img-container">
                                                                @if($reward->image_path)
                                                                    <img src="{{ asset('storage/' . $reward->image_path) }}"
                                                                        class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                                                        alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                @else
                                                                    @if(strpos(strtolower($reward->name), 'bottle') !== false || strpos(strtolower($reward->name), 'ขวดน้ำ') !== false)
                                                                        <img src="{{ asset('images/rewards/bottle.png') }}"
                                                                            class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                                                            alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                    @elseif(strpos(strtolower($reward->name), 'cap') !== false || strpos(strtolower($reward->name), 'หมวก') !== false)
                                                                        <img src="{{ asset('images/rewards/cap.png') }}"
                                                                            class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                                                            alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                    @elseif(strpos(strtolower($reward->name), 'shirt') !== false || strpos(strtolower($reward->name), 'เสื้อ') !== false)
                                                                        <img src="{{ asset('images/rewards/tshirt.png') }}"
                                                                            class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                                                            alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                    @else
                                                                        <img src="{{ asset('images/rewards/gift.png') }}"
                                                                            class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                                                            alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                    @endif
                                                                @endif
                                                            </div>

                                                            <div class="card-body">
                                                                <h5 class="card-title">{{ $reward->name }}</h5>
                                                                <p class="card-text small text-muted">{{ $reward->description }}</p>

                                                                <div class="reward-points small fw-bold">
                                                                    <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                                        <i class="fas fa-coins me-1"></i> {{ $reward->points_required }} คะแนน
                                                                    </span>
                                                                </div>

                                                                @if($availablePoints < $reward->points_required && $reward->quantity > 0)
                                                                    @php
                                                                        $pointsNeeded = $reward->points_required - $availablePoints;
                                                                        $progressPercent = min(100, ($availablePoints / $reward->points_required) * 100);
                                                                    @endphp
                                                                    <div class="progress mt-2" style="height: 6px;">
                                                                        <div class="progress-bar bg-{{ $pointsColors[$range] }}"
                                                                            role="progressbar"
                                                                            style="width: {{ $progressPercent }}%;"
                                                                            aria-valuenow="{{ $progressPercent }}"
                                                                            aria-valuemin="0"
                                                                            aria-valuemax="100"></div>
                                                                    </div>
                                                                    <div class="mt-1 small text-end">ขาดอีก {{ $pointsNeeded }} คะแนน</div>
                                                                @endif
                                                            </div>

                                                            <div class="card-footer bg-transparent">
                                                                @if($alreadyRedeemed)
                                                                    <button class="btn btn-info w-100" disabled>
                                                                        <i class="fas fa-check-double me-1"></i> เคยแลกแล้ว
                                                                    </button>
                                                                @elseif($availablePoints >= $reward->points_required && $reward->quantity > 0)
                                                                    <form action="{{ route('rewards.redeem', $reward->reward_id) }}" method="POST" id="redeem-form-{{ $reward->reward_id }}">
                                                                        @csrf
                                                                        <button type="button"
                                                                                onclick="confirmRedeem({{ $reward->reward_id }}, '{{ $reward->name }}', {{ $reward->points_required }})"
                                                                                class="btn btn-success w-100">
                                                                            <i class="fas fa-gift me-1"></i> แลกรางวัล
                                                                        </button>
                                                                    </form>
                                                                @elseif($reward->quantity <= 0)
                                                                    <button class="btn btn-secondary w-100" disabled>
                                                                        <i class="fas fa-times-circle me-1"></i> สินค้าหมด
                                                                    </button>
                                                                @else
                                                                    <button class="btn btn-warning w-100" disabled>
                                                                        <i class="fas fa-coins me-1"></i> คะแนนไม่พอ
                                                                    </button>
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

                            <!-- แท็บแยกตามช่วงคะแนน -->
                            @foreach($pointsOrder as $range)
                                @if($rewardsByPoints[$range]->isNotEmpty())
                                    <div class="tab-pane fade" id="{{ $range }}-rewards" role="tabpanel" aria-labelledby="{{ $range }}-tab">
                                        <div class="reward-type-header mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="reward-type-icon bg-{{ $pointsColors[$range] }} bg-opacity-10 text-{{ $pointsColors[$range] }} me-3">
                                                    <i class="fas {{ $pointsIcons[$range] }}"></i>
                                                </div>
                                                <div>
                                                    <h4 class="mb-1">รางวัล{{ $pointsNames[$range] }}</h4>
                                                    <p class="text-muted mb-0">
                                                        @if($range == 'low')
                                                            รางวัลที่สามารถแลกได้ด้วยคะแนนน้อย เหมาะสำหรับผู้เริ่มต้น
                                                        @elseif($range == 'medium')
                                                            รางวัลคุณภาพดี ด้วยคะแนนปานกลาง
                                                        @elseif($range == 'high')
                                                            รางวัลพรีเมียม สำหรับผู้ที่สะสมคะแนนมากกว่า 300 คะแนน
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- แยกแสดงรางวัลที่แลกได้กับยังแลกไม่ได้ -->
                                        @php
                                            $availableRewards = $rewardsByPoints[$range]->filter(function($reward) use ($availablePoints) {
                                                return $availablePoints >= $reward->points_required && $reward->quantity > 0;
                                            });

                                            $unavailableRewards = $rewardsByPoints[$range]->filter(function($reward) use ($availablePoints) {
                                                return $availablePoints < $reward->points_required || $reward->quantity <= 0;
                                            });
                                        @endphp

                                        @if($availableRewards->isNotEmpty())
                                            <div class="mb-4">
                                                <div class="card-header-custom bg-success bg-opacity-10 text-success rounded p-2 mb-3">
                                                    <i class="fas fa-check-circle me-2"></i> รางวัลที่แลกได้ทันที
                                                </div>
                                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                                    @foreach($availableRewards as $reward)
                                                        <!-- Reward Card for Available Rewards -->
                                                        <div class="col">
                                                            <div class="card h-100 reward-card shadow-sm" id="reward-card-{{ $reward->reward_id }}">
                                                                @php
                                                                    $alreadyRedeemed = App\Models\Redeem::where('user_id', auth()->user()->user_id)
                                                                        ->where('reward_id', $reward->reward_id)
                                                                        ->where('status', '!=', 'cancelled')
                                                                        ->exists();
                                                                @endphp

                                                                @if($alreadyRedeemed)
                                                                    <div class="reward-status badge bg-info">เคยแลกแล้ว</div>
                                                                @else
                                                                    <div class="reward-status badge bg-success">แลกได้</div>
                                                                @endif

                                                                <div class="reward-stock">เหลือ {{ $reward->quantity }} ชิ้น</div>

                                                                <div class="reward-img-container">
                                                                    @if($reward->image_path)
                                                                        <img src="{{ asset('storage/' . $reward->image_path) }}"
                                                                            class="reward-img"
                                                                            alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                    @else
                                                                        <!-- Default images based on reward name -->
                                                                        @if(strpos(strtolower($reward->name), 'bottle') !== false || strpos(strtolower($reward->name), 'ขวดน้ำ') !== false)
                                                                            <img src="{{ asset('images/rewards/bottle.png') }}"
                                                                                class="reward-img"
                                                                                alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                        @elseif(strpos(strtolower($reward->name), 'cap') !== false || strpos(strtolower($reward->name), 'หมวก') !== false)
                                                                            <img src="{{ asset('images/rewards/cap.png') }}"
                                                                                class="reward-img"
                                                                                alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                        @elseif(strpos(strtolower($reward->name), 'shirt') !== false || strpos(strtolower($reward->name), 'เสื้อ') !== false)
                                                                            <img src="{{ asset('images/rewards/tshirt.png') }}"
                                                                                class="reward-img"
                                                                                alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                        @else
                                                                            <img src="{{ asset('images/rewards/gift.png') }}"
                                                                                class="reward-img"
                                                                                alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                        @endif
                                                                    @endif
                                                                </div>

                                                                <div class="card-body">
                                                                    <h5 class="card-title">{{ $reward->name }}</h5>
                                                                    <p class="card-text small text-muted">{{ $reward->description }}</p>

                                                                    <div class="reward-points small fw-bold">
                                                                        <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                                            <i class="fas fa-coins me-1"></i> {{ $reward->points_required }} คะแนน
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="card-footer bg-transparent">
                                                                    @if($alreadyRedeemed)
                                                                        <button class="btn btn-info w-100" disabled>
                                                                            <i class="fas fa-check-double me-1"></i> เคยแลกแล้ว
                                                                        </button>
                                                                    @else
                                                                        <form action="{{ route('rewards.redeem', $reward->reward_id) }}" method="POST" id="redeem-form-{{ $reward->reward_id }}">
                                                                            @csrf
                                                                            <button type="button"
                                                                                    onclick="confirmRedeem({{ $reward->reward_id }}, '{{ $reward->name }}', {{ $reward->points_required }})"
                                                                                    class="btn btn-success w-100">
                                                                                <i class="fas fa-gift me-1"></i> แลกรางวัล
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($unavailableRewards->isNotEmpty())
                                            <div>
                                                <div class="card-header-custom bg-secondary bg-opacity-10 text-secondary rounded p-2 mb-3">
                                                    <i class="fas fa-lock me-2"></i> รางวัลที่ยังแลกไม่ได้
                                                </div>
                                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                                    @foreach($unavailableRewards as $reward)
                                                        <!-- Reward Card for Unavailable Rewards -->
                                                        <div class="col">
                                                            <div class="card h-100 reward-card shadow-sm locked" id="reward-card-{{ $reward->reward_id }}">
                                                                @php
                                                                    $alreadyRedeemed = App\Models\Redeem::where('user_id', auth()->user()->user_id)
                                                                        ->where('reward_id', $reward->reward_id)
                                                                        ->where('status', '!=', 'cancelled')
                                                                        ->exists();
                                                                @endphp

                                                                @if($alreadyRedeemed)
                                                                    <div class="reward-status badge bg-info">เคยแลกแล้ว</div>
                                                                @elseif($reward->quantity <= 0)
                                                                    <div class="reward-status badge bg-danger">สินค้าหมด</div>
                                                                @else
                                                                    <div class="reward-status badge bg-warning">คะแนนไม่พอ</div>
                                                                @endif

                                                                <div class="reward-stock">เหลือ {{ $reward->quantity }} ชิ้น</div>

                                                                <div class="reward-img-container">
                                                                    @if($reward->image_path)
                                                                        <img src="{{ asset('storage/' . $reward->image_path) }}"
                                                                            class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : 'opacity-50' }}"
                                                                            alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                    @else
                                                                        <!-- Default images based on reward name -->
                                                                        @if(strpos(strtolower($reward->name), 'bottle') !== false || strpos(strtolower($reward->name), 'ขวดน้ำ') !== false)
                                                                            <img src="{{ asset('images/rewards/bottle.png') }}"
                                                                                class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : 'opacity-50' }}"
                                                                                alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                        @elseif(strpos(strtolower($reward->name), 'cap') !== false || strpos(strtolower($reward->name), 'หมวก') !== false)
                                                                            <img src="{{ asset('images/rewards/cap.png') }}"
                                                                                class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : 'opacity-50' }}"
                                                                                alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                        @elseif(strpos(strtolower($reward->name), 'shirt') !== false || strpos(strtolower($reward->name), 'เสื้อ') !== false)
                                                                            <img src="{{ asset('images/rewards/tshirt.png') }}"
                                                                                class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : 'opacity-50' }}"
                                                                                alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                        @else
                                                                            <img src="{{ asset('images/rewards/gift.png') }}"
                                                                                class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : 'opacity-50' }}"
                                                                                alt="{{ $reward->name }}" id="reward-{{ $reward->reward_id }}">
                                                                        @endif
                                                                    @endif
                                                                </div>

                                                                <div class="card-body">
                                                                    <h5 class="card-title">{{ $reward->name }}</h5>
                                                                    <p class="card-text small text-muted">{{ $reward->description }}</p>

                                                                    <div class="reward-points small fw-bold">
                                                                        <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                                            <i class="fas fa-coins me-1"></i> {{ $reward->points_required }} คะแนน
                                                                        </span>
                                                                    </div>

                                                                    @if($reward->quantity > 0)
                                                                        @php
                                                                            $pointsNeeded = $reward->points_required - $availablePoints;
                                                                            $progressPercent = min(100, ($availablePoints / $reward->points_required) * 100);
                                                                        @endphp
                                                                        <div class="progress mt-2" style="height: 6px;">
                                                                            <div class="progress-bar bg-{{ $pointsColors[$range] }}"
                                                                                role="progressbar"
                                                                                style="width: {{ $progressPercent }}%;"
                                                                                aria-valuenow="{{ $progressPercent }}"
                                                                                aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                        <div class="mt-1 small text-end">ขาดอีก {{ $pointsNeeded }} คะแนน</div>
                                                                    @endif
                                                                </div>

                                                                <div class="card-footer bg-transparent">
                                                                    @if($alreadyRedeemed)
                                                                        <button class="btn btn-info w-100" disabled>
                                                                            <i class="fas fa-check-double me-1"></i> เคยแลกแล้ว
                                                                        </button>
                                                                    @elseif($reward->quantity <= 0)
                                                                        <button class="btn btn-secondary w-100" disabled>
                                                                            <i class="fas fa-times-circle me-1"></i> สินค้าหมด
                                                                        </button>
                                                                    @else
                                                                        <button class="btn btn-warning w-100" disabled>
                                                                            <i class="fas fa-coins me-1"></i> คะแนนไม่พอ
                                                                        </button>
                                                                    @endif
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
                <div class="card-header bg-primary">
                    <h5 class="mb-0">รายละเอียดการแลกรางวัล</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ช่วงคะแนน</th>
                                    <th>รายละเอียด</th>
                                    <th>ประเภทของรางวัล</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-coins me-2 text-success"></i> คะแนนน้อย (≤ 100)</td>
                                    <td>สามารถแลกได้ง่าย เหมาะสำหรับผู้เริ่มต้น</td>
                                    <td>ของรางวัลขนาดเล็ก, สติกเกอร์, เครื่องดื่ม</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-award me-2 text-warning"></i> คะแนนปานกลาง (101-300)</td>
                                    <td>รางวัลคุณภาพดี สำหรับผู้ที่สะสมคะแนนพอสมควร</td>
                                    <td>ขวดน้ำ, หมวก, อุปกรณ์เสริมการวิ่ง</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-trophy me-2 text-danger"></i> คะแนนสูง (> 300)</td>
                                    <td>รางวัลพรีเมียม สำหรับผู้ที่สะสมคะแนนเยอะ</td>
                                    <td>เสื้อผ้า, รองเท้า, อุปกรณ์อิเล็กทรอนิกส์</td>
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
<style>
    /* Reward Cards */
    .reward-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        border: none;
    }

    .reward-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .reward-img-container {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        padding: 15px;
    }

    .reward-img {
        max-height: 120px;
        max-width: 100%;
        object-fit: contain;
    }

    /* Status badges positioning and styling */
    .reward-status {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 2;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
    }

    .reward-stock {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 2;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        background-color: rgba(33, 37, 41, 0.8);
        color: white;
    }

    .reward-stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .reward-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }

    .reward-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 10px;
        font-size: 20px;
    }

    /* Filter Badge */
    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover {
        filter: brightness(0.9);
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

    /* Fix for grayscale on out of stock items */
    .filter-grayscale {
        filter: grayscale(100%);
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

        .card-body {
            padding: 1rem;
        }

        .reward-img-container {
            height: 120px;
        }

        .reward-img {
            max-height: 100px;
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

        .reward-stat-card {
            border-radius: 12px;
        }

        .reward-stat-icon {
            width: 45px;
            height: 45px;
            font-size: 20px;
            margin-right: 10px !important;
        }

        .reward-stat-card .card-body {
            padding: 15px;
        }

        .reward-stat-card h6 {
            font-size: 0.85rem;
            margin-bottom: 5px !important;
        }

        .reward-stat-card h4 {
            font-size: 1.4rem;
            font-weight: 600;
        }

        /* ปรับค้นหาและตัวกรองบนมือถือ */
        .card.shadow-sm.mb-4 .card-body {
            padding: 10px;
        }

        .form-control, .form-select {
            font-size: 0.9rem;
        }

        .filter-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.7rem !important;
        }

        /* ปรับรายการรางวัล */
        .reward-img-container {
            height: 100px;
        }

        .reward-img {
            max-height: 80px;
        }

        .reward-card .card-body {
            padding: 0.7rem;
        }

        .reward-card h5 {
            font-size: 1rem;
        }

        .reward-card p {
            font-size: 0.8rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/tour-settings-fix.js') }}"></script>
<script src="{{ asset('js/badge-onboarding-fix.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialize filter badges
        document.querySelectorAll('.filter-badge').forEach(badge => {
            badge.addEventListener('click', function() {
                document.querySelectorAll('.filter-badge').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Handle tab clicks - preserve selected tab on page reload using localStorage
        const tabButtons = document.querySelectorAll('#rewardPointsTabs .nav-link');
        const tabItems = document.querySelectorAll('.tab-pane');

        // Check if there's a saved tab
        const savedTab = localStorage.getItem('selectedRewardTab');
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
                localStorage.setItem('selectedRewardTab', this.id);
            });
        });

        // Function to confirm reward redemption with SweetAlert
        window.confirmRedeem = function(rewardId, rewardName, points) {
            // Get the image URL from the card
            const imgElement = document.querySelector(`#reward-card-${rewardId} .reward-img`);
            const imageUrl = imgElement ? imgElement.src : '';

            Swal.fire({
                title: 'แลกรางวัล?',
                html: `
                    <div class="text-center mb-4">
                        <img src="${imageUrl}" alt="${rewardName}" style="max-height: 100px; max-width: 100px; margin-bottom: 15px;">
                        <h5 class="mb-2">${rewardName}</h5>
                        <div class="text-primary">
                            <i class="fas fa-coins text-warning me-1"></i> <strong>${points} คะแนน</strong>
                        </div>
                    </div>
                    <p>คุณต้องการแลกรางวัลนี้ใช่หรือไม่?</p>
                `,
                icon: false,
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'แลกรางวัล',
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

                    // Submit the form
                    document.getElementById(`redeem-form-${rewardId}`).submit();
                }
            });
        };

        // Handle success message with SweetAlert
        @if(session('success') && session('reward_redeemed'))
            Swal.fire({
                title: 'ยินดีด้วย!',
                html: `
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . session('reward_redeemed.image')) }}"
                             alt="{{ session('reward_redeemed.reward_name') }}"
                             style="max-height: 120px; max-width: 120px; margin-bottom: 15px;">
                        <h5 class="mb-2">แลกรางวัล "{{ session('reward_redeemed.reward_name') }}" สำเร็จ</h5>
                        <div class="text-success mt-3">
                            <i class="fas fa-coins text-warning me-1"></i> <strong>ใช้ {{ session('reward_redeemed.points') }} คะแนน</strong>
                        </div>
                    </div>
                `,
                icon: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'ยอดเยี่ยม!'
            });
        @elseif(session('success'))
            Swal.fire({
                title: 'ยินดีด้วย!',
                html: `
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <h5 class="mb-2">การแลกรางวัลสำเร็จ</h5>
                        <p>{{ session('success') }}</p>
                        <div class="text-success mt-3">
                            <i class="fas fa-gift text-warning me-1"></i> <strong>รอรับรางวัลได้เลย!</strong>
                        </div>
                    </div>
                `,
                icon: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'ยอดเยี่ยม!'
            });
        @endif

        // Handle error message with SweetAlert
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: "{{ session('error') }}",
                confirmButtonColor: '#28a745'
            });
        @endif
    });
</script>
@endsection
