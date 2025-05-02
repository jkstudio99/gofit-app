@extends('layouts.app')

@section('title', 'รางวัล')

@section('content')
<div class="container py-4">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">รางวัล</h2>
            <p class="text-muted">แลกคะแนนจากการวิ่งเพื่อรับของรางวัลมากมาย!</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('rewards.history') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-history me-1"></i> ประวัติการแลกรางวัล
            </a>
            <div class="d-inline-block p-3 bg-light rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <strong class="fs-4">{{ auth()->user()->getAvailablePoints() }}</strong>
                        <div class="small text-muted">คะแนนของคุณ</div>
                    </div>
                    <i class="fas fa-coins fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
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
        <div class="col-md-3 col-sm-6 mb-3">
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
        <div class="col-md-3 col-sm-6 mb-3">
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
        <div class="col-md-3 col-sm-6 mb-3">
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
                            <img src="{{ asset('images/empty-rewards.svg') }}" alt="ไม่มีรางวัล" class="img-fluid mb-3" style="max-width: 200px;">
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

                                                            <div class="reward-stock badge bg-secondary">เหลือ {{ $reward->quantity }} ชิ้น</div>

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

                                                                <div class="reward-stock badge bg-secondary">เหลือ {{ $reward->quantity }} ชิ้น</div>

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

                                                                <div class="reward-stock badge bg-secondary">เหลือ {{ $reward->quantity }} ชิ้น</div>

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
                <div class="card-header bg-primary text-white">
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
    /* Search box */
    .search-box {
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        padding-left: 20px;
    }

    .search-box:focus {
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
        border-color: #2DC679;
    }

    /* Reward Cards */
    .reward-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        border: none;
    }

    .reward-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .reward-card.locked:hover {
        box-shadow: 0 10px 20px rgba(108,117,125,0.2) !important;
    }

    .reward-img-container {
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        padding: 15px;
    }

    .reward-img {
        max-height: 130px;
        max-width: 130px;
        object-fit: contain;
        transition: all 0.3s ease;
    }

    .reward-card:hover .reward-img {
        transform: scale(1.1);
    }

    .filter-grayscale {
        filter: grayscale(70%);
    }

    .reward-status {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 0.7rem;
        z-index: 2;
    }

    .reward-stock {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 0.7rem;
        z-index: 2;
    }

    /* Filter Badges */
    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover {
        background-color: #2DC679 !important;
        color: white;
    }

    /* Stats Cards */
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

    /* Progress animation */
    @keyframes progress-animation {
        0% { width: 0%; }
    }

    .progress-bar {
        animation: progress-animation 1.5s ease-in-out;
        border-radius: 4px;
        background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }

    /* Progress animation */
    @keyframes progress-bar-stripes {
        0% { background-position: 1rem 0 }
        100% { background-position: 0 0 }
    }

    /* Category Section Styling */
    .reward-category-section {
        border-radius: 10px;
        overflow: hidden;
    }

    .reward-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .reward-type-header {
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

    .progress {
        background-color: rgba(0,0,0,0.05);
        height: 8px !important;
        border-radius: 4px;
        overflow: hidden;
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
