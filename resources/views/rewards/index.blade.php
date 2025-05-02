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

                            // Get points spent on rewards
                            $spentPoints = App\Models\Redeem::where('user_id', auth()->user()->user_id)
                                ->where('status', '!=', 'cancelled')
                                ->join('tb_reward', 'tb_redeem.reward_id', '=', 'tb_reward.reward_id')
                                ->sum('points_required');

                            // Available points
                            $availablePoints = $earnedPoints - $spentPoints;
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
        @if($rewards->isEmpty())
            <div class="col-12 text-center py-5">
                <img src="{{ asset('images/empty-rewards.svg') }}" alt="ไม่มีรางวัล" class="img-fluid mb-3" style="max-width: 200px;">
                <h4>ไม่พบรางวัล</h4>
                <p class="text-muted">ขออภัย ไม่มีรางวัลในขณะนี้ กรุณาลองใหม่ในภายหลัง</p>
            </div>
        @else
            @foreach($rewards as $reward)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 reward-card shadow-sm">
                        @php
                            // Calculate user points from point history
                            $earnedPoints = DB::table('tb_point_history')
                                ->where('user_id', auth()->user()->user_id)
                                ->sum('points');

                            // Get points spent on rewards
                            $spentPoints = App\Models\Redeem::where('user_id', auth()->user()->user_id)
                                ->where('status', '!=', 'cancelled')
                                ->join('tb_reward', 'tb_redeem.reward_id', '=', 'tb_reward.reward_id')
                                ->sum('points_required');

                            // Available points
                            $availablePoints = $earnedPoints - $spentPoints;
                        @endphp

                        @if($reward->quantity <= 0)
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
                                     alt="{{ $reward->name }}">
                            @else
                                @if(strpos(strtolower($reward->name), 'bottle') !== false || strpos(strtolower($reward->name), 'ขวดน้ำ') !== false)
                                    <img src="{{ asset('images/rewards/bottle.png') }}"
                                         class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                         alt="{{ $reward->name }}">
                                @elseif(strpos(strtolower($reward->name), 'cap') !== false || strpos(strtolower($reward->name), 'หมวก') !== false)
                                    <img src="{{ asset('images/rewards/cap.png') }}"
                                         class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                         alt="{{ $reward->name }}">
                                @elseif(strpos(strtolower($reward->name), 'shirt') !== false || strpos(strtolower($reward->name), 'เสื้อ') !== false)
                                    <img src="{{ asset('images/rewards/tshirt.png') }}"
                                         class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                         alt="{{ $reward->name }}">
                                @else
                                    <img src="{{ asset('images/rewards/gift.png') }}"
                                         class="reward-img {{ $reward->quantity <= 0 ? 'filter-grayscale' : '' }}"
                                         alt="{{ $reward->name }}">
                                @endif
                            @endif
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{ $reward->name }}</h5>
                            <p class="card-text">{{ $reward->description }}</p>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-warning fw-bold">
                                    <i class="fas fa-coins me-1"></i> {{ $reward->points_required }} คะแนน
                                </span>
                            </div>

                            @if($availablePoints < $reward->points_required && $reward->quantity > 0)
                                @php
                                    $pointsNeeded = $reward->points_required - $availablePoints;
                                    $progressPercent = min(100, ($availablePoints / $reward->points_required) * 100);
                                @endphp
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $progressPercent }}%;"
                                        aria-valuenow="{{ $progressPercent }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="mt-1 small text-end">ขาดอีก {{ $pointsNeeded }} คะแนน</div>
                            @endif
                        </div>

                        <div class="card-footer bg-transparent">
                            @if($availablePoints >= $reward->points_required && $reward->quantity > 0)
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
        @endif
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
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize filter badges
        document.querySelectorAll('.filter-badge').forEach(badge => {
            badge.addEventListener('click', function() {
                document.querySelectorAll('.filter-badge').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });

    // Function to confirm reward redemption with SweetAlert
    function confirmRedeem(rewardId, rewardName, points) {
        Swal.fire({
            title: 'แลกรางวัล?',
            html: `คุณต้องการแลกรางวัล <strong>${rewardName}</strong> ด้วย <strong>${points} คะแนน</strong> ใช่หรือไม่?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'แลกรางวัล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                document.getElementById(`redeem-form-${rewardId}`).submit();
            }
        });
    }
</script>
@endsection
