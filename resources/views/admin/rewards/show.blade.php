@extends('layouts.admin')

@section('title', 'รายละเอียดรางวัล')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .reward-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .reward-img-container {
        height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .reward-img {
        max-height: 200px;
        max-width: 200px;
        object-fit: contain;
    }

    .stats-card {
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: all 0.2s ease;
        border: none;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .text-primary {
        color: #2DC679 !important;
    }

    .bg-primary-light {
        background-color: rgba(45, 198, 121, 0.1);
    }

    .reward-detail-title {
        position: relative;
        padding-left: 15px;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }

    .reward-detail-title:before {
        content: "";
        position: absolute;
        left: 0;
        top: 10%;
        height: 80%;
        width: 4px;
        background-color: #2DC679;
        border-radius: 2px;
    }

    .detail-section {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .detail-item {
        margin-bottom: 15px;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
    }

    .reward-status-badge {
        font-size: 0.9rem;
        padding: 8px 15px;
        border-radius: 20px;
    }

    .btn-action {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">รายละเอียดรางวัล</h1>
                <div>
            <a href="{{ route('admin.rewards') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>กลับไปยังรายการ
                    </a>
        </div>
    </div>

    <div class="row">
        <!-- รายละเอียดรางวัล -->
        <div class="col-lg-8 mb-4">
            <div class="card reward-card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-gift text-primary me-2"></i>
                            {{ $reward->name }}
                        </h5>

                        @if($reward->is_enabled)
                            <span class="badge bg-success reward-status-badge">
                                <i class="fas fa-check-circle me-1"></i> เปิดใช้งาน
                            </span>
                        @else
                            <span class="badge bg-danger reward-status-badge">
                                <i class="fas fa-ban me-1"></i> ปิดใช้งาน
                            </span>
                        @endif
                    </div>
                </div>

                <div class="reward-img-container">
                            @if($reward->image_path)
                        <img src="{{ asset('storage/' . $reward->image_path) }}" class="reward-img" alt="{{ $reward->name }}">
                            @else
                        <div class="text-center text-muted">
                            <i class="fas fa-gift fa-5x mb-3"></i>
                            <p>ไม่มีรูปภาพ</p>
                                </div>
                            @endif
                        </div>

                <div class="card-body">
                    <h4 class="reward-detail-title">รายละเอียด</h4>

                    <div class="detail-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">คะแนนที่ใช้แลก</div>
                                    <div class="h5 mt-1">
                                        <span class="badge bg-warning text-dark px-3 py-2">
                                    <i class="fas fa-coins me-1"></i> {{ number_format($reward->points_required) }} คะแนน
                                </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">สถานะสินค้า</div>
                                    <div class="h5 mt-1">
                                    @if($reward->quantity > 10)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-cubes me-1"></i> คงเหลือ {{ number_format($reward->quantity) }} ชิ้น
                                            </span>
                                    @elseif($reward->quantity > 0)
                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                <i class="fas fa-exclamation-triangle me-1"></i> เหลือน้อย {{ number_format($reward->quantity) }} ชิ้น
                                            </span>
                                    @else
                                            <span class="badge bg-danger px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i> หมด
                                            </span>
                                    @endif
                                    </div>
                                </div>
                            </div>
                            </div>

                        <div class="detail-item mt-3">
                            <div class="detail-label">คำอธิบาย</div>
                            <div class="mt-2">
                                <p>{{ $reward->description }}</p>
                            </div>
                        </div>
                    </div>

                    <h4 class="reward-detail-title">ข้อมูลเพิ่มเติม</h4>

                    <div class="detail-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">รหัสรางวัล</div>
                                    <div>{{ $reward->reward_id }}</div>
                            </div>

                                <div class="detail-item">
                                    <div class="detail-label">วันที่สร้าง</div>
                                    <div>{{ $reward->created_at->format('d/m/Y H:i:s') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">อัปเดตล่าสุด</div>
                                    <div>{{ $reward->updated_at->format('d/m/Y H:i:s') }}</div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">จำนวนการแลกทั้งหมด</div>
                                    <div>{{ number_format($reward->redeems->count()) }} ครั้ง</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- สถิติและการดำเนินการ -->
        <div class="col-lg-4">
            <!-- สถิติ -->
            <div class="card stats-card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-4">
                        <i class="fas fa-chart-bar text-primary me-2"></i>สถิติการแลก
                    </h5>

                    <div class="d-flex align-items-center mb-4">
                        <div class="stats-icon bg-primary-light text-primary">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                        <div>
                            <div class="text-muted small">จำนวนการแลกทั้งหมด</div>
                            <div class="h4 mb-0">{{ number_format($reward->redeems->count()) }} ครั้ง</div>
                    </div>
                </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-coins"></i>
                            </div>
                        <div>
                            <div class="text-muted small">คะแนนที่ใช้ไปทั้งหมด</div>
                            <div class="h4 mb-0">{{ number_format($reward->redeems->count() * $reward->points_required) }} คะแนน</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-box"></i>
                            </div>
                        <div>
                            <div class="text-muted small">จำนวนคงเหลือ</div>
                            <div class="h4 mb-0">{{ number_format($reward->quantity) }} ชิ้น</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- การดำเนินการ -->
            <div class="card stats-card">
                <div class="card-body p-4">
                    <h5 class="mb-4">
                        <i class="fas fa-cog text-primary me-2"></i>การดำเนินการ
                    </h5>

                    <div class="list-group">
                        <a href="{{ route('admin.rewards.edit', $reward) }}" class="list-group-item list-group-item-action d-flex align-items-center border-0 mb-2 rounded-3">
                            <span class="btn-action bg-warning text-white me-3">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>แก้ไขรางวัล</span>
                        </a>

                        <a href="{{ route('admin.redeems') }}?reward_id={{ $reward->reward_id }}" class="list-group-item list-group-item-action d-flex align-items-center border-0 mb-2 rounded-3">
                            <span class="btn-action bg-info text-white me-3">
                                <i class="fas fa-history"></i>
                            </span>
                            <span>ดูประวัติการแลก</span>
                        </a>

                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center border-0 mb-2 rounded-3" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                            <span class="btn-action bg-success text-white me-3">
                                <i class="fas fa-cubes"></i>
                            </span>
                            <span>ปรับจำนวนสินค้า</span>
                        </button>

                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center border-0 rounded-3 text-danger delete-reward">
                            <span class="btn-action bg-danger text-white me-3">
                                <i class="fas fa-trash"></i>
                            </span>
                            <span>ลบรางวัล</span>
                        </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal ปรับจำนวนสินค้า -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">ปรับจำนวนสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.rewards.update', $reward) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">จำนวนสินค้าปัจจุบัน</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $reward->quantity }}" min="0" required>
                        <small class="text-muted">ระบุจำนวนสินค้าทั้งหมดที่มีอยู่</small>
                    </div>

                    <!-- ค่าฟิลด์อื่นๆ ที่จำเป็นต้องส่งไปด้วย แต่ไม่ได้แก้ไข -->
                    <input type="hidden" name="name" value="{{ $reward->name }}">
                    <input type="hidden" name="description" value="{{ $reward->description }}">
                    <input type="hidden" name="points_required" value="{{ $reward->points_required }}">
                    <input type="hidden" name="is_enabled" value="{{ $reward->is_enabled ? 'on' : '' }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                                                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup delete reward functionality using SweetAlert2
        const deleteButtons = document.querySelectorAll('.delete-reward');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                @if($reward->redeems->count() > 0)
                    // Cannot delete if there are redeems
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถลบรางวัลนี้ได้!',
                        text: 'มีการแลกรางวัลนี้แล้ว {{ $reward->redeems->count() }} ครั้ง',
                        confirmButtonColor: '#dc3545'
                    });
                @else
                    // Can delete - show confirmation
                Swal.fire({
                        title: 'ยืนยันการลบรางวัล?',
                        html: 'คุณต้องการลบรางวัล <strong>{{ $reward->name }}</strong> ใช่หรือไม่?<br><span class="text-danger">การดำเนินการนี้ไม่สามารถเรียกคืนได้ และจะลบรางวัลนี้ออกจากระบบอย่างถาวร</span>',
                    icon: 'warning',
                    showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                        confirmButtonText: 'ใช่, ลบรางวัล!',
                        cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                            // Create and submit the delete form
                        const form = document.createElement('form');
                        form.method = 'POST';
                            form.action = '{{ route('admin.rewards.destroy', $reward) }}';

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
                @endif
            });
        });

        // Setup adjustStock form submit with SweetAlert2
        const adjustStockForm = document.querySelector('#adjustStockModal form');
        if (adjustStockForm) {
            adjustStockForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const quantity = document.getElementById('quantity').value;

                Swal.fire({
                    title: 'ยืนยันการปรับจำนวน?',
                    text: `คุณต้องการปรับจำนวนสินค้าเป็น ${quantity} ชิ้นใช่หรือไม่?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2DC679',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ยืนยัน!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        }

        // Display alerts for success/error messages from session
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
