@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">รางวัล</h2>
            <p class="text-muted">แลกคะแนนจากการวิ่งเพื่อรับของรางวัลมากมาย!</p>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-inline-block p-3 bg-light rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <strong class="fs-4">{{ auth()->user()->points ?? 0 }}</strong>
                        <div class="small text-muted">คะแนนของคุณ</div>
                    </div>
                    <i class="fas fa-coins fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- รางวัลที่แลกได้ -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h3>รางวัลที่คุณสามารถแลกได้</h3>
            <hr>
        </div>
    </div>

    <div class="row mb-5">
        @php
            $userPoints = auth()->user()->points ?? 0;
            $hasAvailableRewards = false;
        @endphp

        @foreach($rewards as $reward)
            @if($userPoints >= $reward->point_cost && $reward->stock > 0)
                @php
                    $hasAvailableRewards = true;
                @endphp
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="badge bg-success position-absolute end-0 m-2">แลกได้</div>
                        @if($reward->image)
                            <div class="text-center pt-3">
                                <img src="{{ asset('storage/' . $reward->image) }}" class="card-img-top"
                                    style="height: 180px; width: auto; max-width: 80%; object-fit: contain;" alt="{{ $reward->name }}">
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $reward->name }}</h5>
                            <p class="card-text">{{ $reward->description }}</p>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning fw-bold">
                                    <i class="fas fa-coins me-1"></i> {{ $reward->point_cost }} คะแนน
                                </span>
                                <span class="text-muted small">เหลือ {{ $reward->stock }} ชิ้น</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('rewards.redeem', $reward) }}" class="btn btn-primary w-100"
                                onclick="return confirm('คุณต้องการแลกรางวัล {{ $reward->name }} ด้วย {{ $reward->point_cost }} คะแนนใช่หรือไม่?')">
                                แลกรางวัล
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- รางวัลที่ต้องสะสมคะแนนเพิ่ม -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h3>รางวัลที่ต้องสะสมคะแนนเพิ่ม</h3>
            <hr>
        </div>
    </div>

    <div class="row">
        @php
            $hasUnavailableRewards = false;
        @endphp

        @foreach($rewards as $reward)
            @if($userPoints < $reward->point_cost && $reward->stock > 0)
                @php
                    $hasUnavailableRewards = true;
                    $pointsNeeded = $reward->point_cost - $userPoints;
                    $progressPercent = ($userPoints / $reward->point_cost) * 100;
                @endphp
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm opacity-75">
                        <div class="badge bg-secondary position-absolute end-0 m-2">คะแนนไม่พอ</div>
                        @if($reward->image)
                            <div class="text-center pt-3">
                                <img src="{{ asset('storage/' . $reward->image) }}" class="card-img-top filter-grayscale"
                                    style="height: 180px; width: auto; max-width: 80%; object-fit: contain; filter: grayscale(30%);" alt="{{ $reward->name }}">
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $reward->name }}</h5>
                            <p class="card-text">{{ $reward->description }}</p>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-warning fw-bold">
                                    <i class="fas fa-coins me-1"></i> {{ $reward->point_cost }} คะแนน
                                </span>
                                <span class="text-muted small">เหลือ {{ $reward->stock }} ชิ้น</span>
                            </div>

                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $progressPercent }}%;"
                                    aria-valuenow="{{ $progressPercent }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                            <div class="mt-1 small text-end">ขาดอีก {{ $pointsNeeded }} คะแนน</div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-outline-secondary w-100" disabled>
                                คะแนนไม่เพียงพอ
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- รางวัลที่หมด -->
    @php
        $outOfStockRewards = $rewards->where('stock', 0);
    @endphp

    @if($outOfStockRewards->count() > 0)
        <div class="row mt-5 mb-4">
            <div class="col-md-12">
                <h3>รางวัลที่หมด</h3>
                <hr>
            </div>
        </div>

        <div class="row">
            @foreach($outOfStockRewards as $reward)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm opacity-50">
                        <div class="badge bg-danger position-absolute end-0 m-2">หมด</div>
                        @if($reward->image)
                            <div class="text-center pt-3">
                                <img src="{{ asset('storage/' . $reward->image) }}" class="card-img-top filter-grayscale"
                                    style="height: 180px; width: auto; max-width: 80%; object-fit: contain; filter: grayscale(100%);" alt="{{ $reward->name }}">
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $reward->name }}</h5>
                            <p class="card-text">{{ $reward->description }}</p>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning fw-bold">
                                    <i class="fas fa-coins me-1"></i> {{ $reward->point_cost }} คะแนน
                                </span>
                                <span class="text-danger small">สินค้าหมด</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-outline-danger w-100" disabled>
                                สินค้าหมด
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
/* เพิ่ม CSS เพื่อแก้ไข dropdown ในหน้ารางวัล */
.dropdown-menu.show {
    display: block !important;
    z-index: 9999 !important;
}
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // เพิ่ม event listener สำหรับ dropdown ในหน้ารางวัล
        var dropdownBtns = document.querySelectorAll('.dropdown-toggle');
        dropdownBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    dropdownMenu.classList.toggle('show');
                }
            });
        });

        // ปิด dropdown เมื่อคลิกที่อื่น
        document.addEventListener('click', function(e) {
            var dropdownMenus = document.querySelectorAll('.dropdown-menu.show');
            dropdownMenus.forEach(function(menu) {
                if (!menu.previousElementSibling.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        });
    });
</script>
@endsection
