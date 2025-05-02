@extends('layouts.admin')

@section('title', 'จัดการการแลกรางวัล')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .search-box {
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        padding-left: 20px;
    }

    .search-box:focus {
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
        border-color: #2DC679;
    }

    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover, .filter-badge.active {
        background-color: #2DC679 !important;
        color: white;
    }

    /* Redeem status indicator */
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .status-pending {
        background-color: #ffc107;
    }

    .status-completed {
        background-color: #28a745;
    }

    .status-cancelled {
        background-color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">จัดการการแลกรางวัล</h1>
        <a href="{{ route('admin.rewards') }}" class="btn btn-outline-primary">
            <i class="fas fa-gift me-1"></i> จัดการรางวัล
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.redeems') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box"
                               placeholder="ค้นหาตามชื่อผู้ใช้หรือรางวัล..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">สถานะทั้งหมด</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>เรียง: ล่าสุด</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เรียง: เก่าสุด</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Filter Tags -->
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.redeems', request()->except('status')) }}"
               class="badge bg-{{ request('status') ? 'light text-dark' : 'primary' }} py-2 px-3 filter-badge">
                <i class="fas fa-list me-1"></i> ทั้งหมด
            </a>

            <a href="{{ route('admin.redeems', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
               class="badge bg-{{ request('status') == 'pending' ? 'warning' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-clock me-1"></i> รอดำเนินการ
            </a>

            <a href="{{ route('admin.redeems', array_merge(request()->except('status'), ['status' => 'completed'])) }}"
               class="badge bg-{{ request('status') == 'completed' ? 'success' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-check-circle me-1"></i> เสร็จสิ้น
            </a>

            <a href="{{ route('admin.redeems', array_merge(request()->except('status'), ['status' => 'cancelled'])) }}"
               class="badge bg-{{ request('status') == 'cancelled' ? 'danger' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-times-circle me-1"></i> ยกเลิก
            </a>
        </div>
    </div>

    <!-- Redeems Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">รายการแลกรางวัลทั้งหมด</h5>
            <span class="badge bg-primary">{{ $redeems->total() }} รายการ</span>
        </div>
        <div class="card-body">
            @if($redeems->isEmpty())
                <div class="text-center py-5">
                    <img src="{{ asset('images/empty-redeem.svg') }}" alt="ไม่มีรายการ" class="img-fluid mb-3" style="max-width: 200px;">
                    <h5>ไม่พบรายการแลกรางวัล</h5>
                    <p class="text-muted">ยังไม่มีรายการแลกรางวัลในระบบ</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ผู้ใช้</th>
                                <th>รางวัล</th>
                                <th>คะแนนที่ใช้</th>
                                <th>สถานะ</th>
                                <th>วันที่แลก</th>
                                <th>หมายเหตุ</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($redeems as $key => $redeem)
                            <tr>
                                <td>{{ $redeems->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($redeem->user->profile_image)
                                            <img src="{{ asset('storage/' . $redeem->user->profile_image) }}"
                                                 class="rounded-circle me-2" width="40" height="40"
                                                 alt="{{ $redeem->user->username }}">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 40px; height: 40px; color: white;">
                                                {{ substr($redeem->user->username, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $redeem->user->username }}</div>
                                            <div class="small text-muted">{{ $redeem->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="reward-img-small me-2">
                                            @if($redeem->reward->image_path)
                                                <img src="{{ asset('storage/' . $redeem->reward->image_path) }}"
                                                     style="width: 40px; height: 40px; object-fit: contain;"
                                                     alt="{{ $redeem->reward->name }}">
                                            @else
                                                <i class="fas fa-gift fa-2x text-primary"></i>
                                            @endif
                                        </div>
                                        <div>{{ $redeem->reward->name }}</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-warning text-dark">{{ $redeem->points_used ?? $redeem->reward->points_required }} คะแนน</span></td>
                                <td>
                                    @if($redeem->status == 'pending')
                                        <span class="badge bg-warning">
                                            <span class="status-indicator status-pending"></span>รอดำเนินการ
                                        </span>
                                    @elseif($redeem->status == 'completed')
                                        <span class="badge bg-success">
                                            <span class="status-indicator status-completed"></span>เสร็จสิ้น
                                        </span>
                                    @elseif($redeem->status == 'cancelled')
                                        <span class="badge bg-danger">
                                            <span class="status-indicator status-cancelled"></span>ยกเลิก
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $redeem->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $redeem->note ?? '-' }}</td>
                                <td>
                                    <div class="d-flex">
                                        @if($redeem->status == 'pending')
                                            <button type="button"
                                                    class="btn btn-success btn-sm me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateStatusModal"
                                                    data-redeem-id="{{ $redeem->redeem_id }}"
                                                    data-status="completed">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateStatusModal"
                                                    data-redeem-id="{{ $redeem->redeem_id }}"
                                                    data-status="cancelled">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="card-footer clearfix">
            {{ $redeems->links() }}
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateStatusForm" action="" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">อัปเดตสถานะการแลกรางวัล</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="note" class="form-label">หมายเหตุ (ถ้ามี)</label>
                        <textarea name="note" id="note" class="form-control" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="status" id="status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle filter badge clicks
        document.querySelectorAll('.filter-badge').forEach(badge => {
            badge.addEventListener('click', function(e) {
                document.querySelectorAll('.filter-badge').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Update Status Modal
        const updateStatusModal = document.getElementById('updateStatusModal');
        if (updateStatusModal) {
            updateStatusModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const redeemId = button.getAttribute('data-redeem-id');
                const status = button.getAttribute('data-status');

                const form = document.getElementById('updateStatusForm');
                form.action = `/admin/redeems/${redeemId}/status`;

                const statusInput = document.getElementById('status');
                statusInput.value = status;

                const modalTitle = updateStatusModal.querySelector('.modal-title');
                if (status === 'completed') {
                    modalTitle.textContent = 'ยืนยันการจัดส่งรางวัล';
                } else if (status === 'cancelled') {
                    modalTitle.textContent = 'ยกเลิกการแลกรางวัล';
                }
            });
        }

        // Show flash message if exists
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#28a745'
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
