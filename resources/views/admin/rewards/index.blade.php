@extends('layouts.admin')

@section('title', 'จัดการรางวัล')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .reward-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .reward-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .reward-img-container {
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .reward-img {
        max-height: 140px;
        max-width: 140px;
        object-fit: contain;
    }

    .reward-status {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 0.7rem;
    }

    .reward-stock {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 0.7rem;
    }

    .search-box {
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        padding-left: 20px;
    }

    .search-box:focus {
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
        border-color: #2DC679;
    }

    .reward-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
    }

    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover, .filter-badge.active {
        background-color: #2DC679 !important;
        color: white;
    }

    /* Advanced filter panel */
    .filter-panel {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">จัดการรางวัล</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>เพิ่มรางวัลใหม่
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.rewards') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box"
                               placeholder="ค้นหาตามชื่อหรือคำอธิบาย..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 text-md-end">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                        <i class="fas fa-filter me-1"></i> ตัวกรองขั้นสูง
                    </button>
                </div>

                <div class="col-12 collapse {{ request()->hasAny(['status', 'min_points', 'max_points', 'sort']) ? 'show' : '' }}" id="advancedFilters">
                    <div class="filter-panel mt-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">สถานะรางวัล</label>
                                <select name="status" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    <option value="enabled" {{ request('status') == 'enabled' ? 'selected' : '' }}>เปิดใช้งาน</option>
                                    <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>ปิดใช้งาน</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">คะแนนขั้นต่ำ</label>
                                <input type="number" name="min_points" class="form-control" placeholder="0" value="{{ request('min_points') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">คะแนนสูงสุด</label>
                                <input type="number" name="max_points" class="form-control" placeholder="10000" value="{{ request('max_points') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">สถานะสินค้า</label>
                                <select name="stock" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>มีสินค้า</option>
                                    <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>สินค้าเหลือน้อย (≤ 10)</option>
                                    <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>สินค้าหมด</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">เรียงตาม</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sort" id="sort-points-asc" value="points-asc" {{ request('sort') == 'points-asc' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sort-points-asc">คะแนนน้อยไปมาก</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sort" id="sort-points-desc" value="points-desc" {{ request('sort') == 'points-desc' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sort-points-desc">คะแนนมากไปน้อย</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sort" id="sort-newest" value="newest" {{ request('sort', 'newest') == 'newest' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sort-newest">ใหม่ล่าสุด</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sort" id="sort-oldest" value="oldest" {{ request('sort') == 'oldest' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sort-oldest">เก่าสุด</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 text-end align-self-end">
                                <a href="{{ route('admin.rewards') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i> รีเซ็ตตัวกรอง
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> กรอง
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Filter Tags -->
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.rewards', request()->except('status')) }}"
               class="badge bg-{{ request('status') ? 'light text-dark' : 'primary' }} py-2 px-3 filter-badge">
                <i class="fas fa-gift me-1"></i> ทั้งหมด
            </a>

            <a href="{{ route('admin.rewards', array_merge(request()->except('status'), ['status' => 'enabled'])) }}"
               class="badge bg-{{ request('status') == 'enabled' ? 'primary' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-check-circle me-1"></i> เปิดใช้งาน
            </a>

            <a href="{{ route('admin.rewards', array_merge(request()->except('status'), ['status' => 'disabled'])) }}"
               class="badge bg-{{ request('status') == 'disabled' ? 'primary' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-ban me-1"></i> ปิดใช้งาน
            </a>
        </div>
    </div>

    <!-- Results -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">
                    <i class="fas fa-gift me-2 text-primary"></i>รายการรางวัล
                    @if(request()->hasAny(['search', 'status', 'min_points', 'max_points', 'stock', 'sort']))
                    <span class="badge bg-success ms-2">
                        <i class="fas fa-filter me-1"></i> กำลังแสดงผลลัพธ์: {{ $rewards->count() }} รายการ
                    </span>
                    @endif
                </h5>
                <span class="badge bg-info rounded-pill">
                    <i class="fas fa-gift me-1"></i> รางวัลทั้งหมด: {{ $rewards->count() }}
                </span>
            </div>
        </div>

        <div class="card-body">
            @if($rewards->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-gift fa-4x"></i>
                    </div>
                    <h5>ไม่พบข้อมูลรางวัล</h5>
                    <p class="text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</p>
                    <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> เพิ่มรางวัลใหม่
                    </a>
                </div>
                            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($rewards as $reward)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm reward-card">
                            <div class="reward-status">
                                @if($reward->is_enabled)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i> เปิดใช้งาน
                                    </span>
                                @else
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="fas fa-ban me-1"></i> ปิดใช้งาน
                                    </span>
                                @endif
                            </div>

                            <div class="reward-stock">
                                @if($reward->quantity > 10)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-cubes me-1"></i> คงเหลือ {{ $reward->quantity }}
                            </span>
                            @elseif($reward->quantity > 0)
                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i> เหลือน้อย {{ $reward->quantity }}
                                    </span>
                            @else
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i> หมด
                                    </span>
                            @endif
                            </div>

                            <div class="reward-img-container">
                                @if($reward->image_path)
                                    <img src="{{ asset('storage/' . $reward->image_path) }}" class="reward-img" alt="{{ $reward->name }}">
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-gift fa-3x"></i>
                                        <p class="small mt-2">ไม่มีรูปภาพ</p>
                                    </div>
                            @endif
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">{{ $reward->name }}</h5>
                                <p class="card-text small text-muted">{{ Str::limit($reward->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-coins me-1"></i> {{ number_format($reward->points_required) }} คะแนน
                                    </span>
                                </div>
                            </div>

                            <div class="card-footer bg-white py-2">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('admin.rewards.show', $reward) }}" class="btn btn-sm btn-info text-white reward-action-btn me-2" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                </a>

                                    <a href="{{ route('admin.rewards.edit', $reward) }}" class="btn btn-sm btn-warning reward-action-btn me-2" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button" class="btn btn-sm btn-danger reward-action-btn delete-reward"
                                            data-reward-id="{{ $reward->reward_id }}"
                                            data-reward-name="{{ $reward->name }}"
                                            title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                            </div>

                <!-- Pagination links -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $rewards->links() }}
                            </div>
                    @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup direct SweetAlert delete confirmation without using Bootstrap modal
        const deleteButtons = document.querySelectorAll('.delete-reward');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const rewardId = this.getAttribute('data-reward-id');
                const rewardName = this.getAttribute('data-reward-name');

                Swal.fire({
                    title: 'ยืนยันการลบรางวัล?',
                    html: `คุณต้องการลบรางวัล <strong>${rewardName}</strong> ใช่หรือไม่?<br><span class="text-danger">การดำเนินการนี้ไม่สามารถเรียกคืนได้ และจะลบรางวัลนี้ออกจากระบบอย่างถาวร</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ลบรางวัล!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit the form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/rewards/${rewardId}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(method);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
            });

        // Display alerts for success/error messages
        @if(session('success'))
                Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#2DC679'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc3545'
            });
        @endif
        });
    </script>
@endsection
