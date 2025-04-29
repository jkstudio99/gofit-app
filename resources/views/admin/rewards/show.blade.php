@extends('layouts.admin')

@section('title', 'รายละเอียดรางวัล - ' . $reward->name)

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .reward-image {
        max-height: 200px;
        object-fit: contain;
    }

    .reward-info-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .reward-info-item:last-child {
        border-bottom: none;
    }

    .reward-stats-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .reward-stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .stats-icon {
        font-size: 2rem;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">รายละเอียดรางวัล</h1>
                <div>
                    <a href="{{ route('admin.rewards') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                    </a>
                    <a href="{{ route('admin.rewards.edit', $reward->reward_id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> แก้ไข
                    </a>
                    <button type="button" class="btn btn-danger" id="deleteRewardBtn">
                        <i class="fas fa-trash me-1"></i> ลบรางวัล
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- รายละเอียดรางวัล -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-4 text-center">
                            @if($reward->image_path)
                                <img src="{{ asset('storage/' . $reward->image_path) }}" alt="{{ $reward->name }}" class="reward-image">
                            @else
                                <div class="bg-light d-flex justify-content-center align-items-center" style="height: 200px;">
                                    <i class="fas fa-gift fa-5x text-secondary"></i>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <h2 class="mb-3">{{ $reward->name }}</h2>

                            <div class="mb-3">
                                @if($reward->is_enabled)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i> สถานะ: เปิดใช้งาน
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="fas fa-ban me-1"></i> สถานะ: ปิดใช้งาน
                                    </span>
                                @endif

                                <span class="badge bg-warning text-dark px-3 py-2 ms-2">
                                    <i class="fas fa-coins me-1"></i> {{ number_format($reward->points_required) }} คะแนน
                                </span>
                            </div>

                            <p class="mb-3">{{ $reward->description }}</p>

                            <div class="reward-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-cubes me-2"></i>จำนวนคงเหลือ:</span>
                                <span class="fw-bold">
                                    @if($reward->quantity > 10)
                                        <span class="text-success">{{ $reward->quantity }} ชิ้น</span>
                                    @elseif($reward->quantity > 0)
                                        <span class="text-warning">{{ $reward->quantity }} ชิ้น (เหลือน้อย)</span>
                                    @else
                                        <span class="text-danger">หมด</span>
                                    @endif
                                </span>
                            </div>

                            <div class="reward-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-exchange-alt me-2"></i>จำนวนการแลก:</span>
                                <span class="fw-bold">{{ $redeems->count() }} ครั้ง</span>
                            </div>

                            <div class="reward-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-calendar-plus me-2"></i>วันที่สร้าง:</span>
                                <span>{{ \Carbon\Carbon::parse($reward->created_at)->format('d/m/Y H:i') }}</span>
                            </div>

                            <div class="reward-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-calendar-check me-2"></i>แก้ไขล่าสุด:</span>
                                <span>{{ \Carbon\Carbon::parse($reward->updated_at)->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- สถิติและข้อมูลเพิ่มเติม -->
        <div class="col-lg-4">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-1 g-4">
                <!-- สถิติการแลกรางวัล -->
                <div class="col">
                    <div class="card shadow-sm reward-stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-primary-subtle text-primary mx-auto">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <h5 class="card-title">การแลกรางวัล</h5>
                            <h3 class="mb-0">{{ $redeems->count() }}</h3>
                            <div class="text-muted small mt-2">ครั้ง</div>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลคะแนนที่ใช้ -->
                <div class="col">
                    <div class="card shadow-sm reward-stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-warning-subtle text-warning mx-auto">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h5 class="card-title">คะแนนที่ใช้แลก</h5>
                            <div class="mt-2">
                                <h3 class="mb-0">{{ number_format($reward->points_required) }}</h3>
                                <div class="text-muted small mt-2">คะแนน</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลสถานะสินค้า -->
                <div class="col">
                    <div class="card shadow-sm reward-stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-success-subtle text-success mx-auto">
                                <i class="fas fa-cubes"></i>
                            </div>
                            <h5 class="card-title">สถานะสินค้า</h5>
                            <div class="mt-2">
                                <h3 class="mb-0">{{ $reward->quantity }}</h3>
                                <div class="text-muted small mt-2">ชิ้น</div>

                                @if($reward->quantity <= 0)
                                    <div class="alert alert-danger py-2 mt-3">
                                        <i class="fas fa-exclamation-triangle me-1"></i> สินค้าหมด
                                    </div>
                                @elseif($reward->quantity <= 10)
                                    <div class="alert alert-warning py-2 mt-3">
                                        <i class="fas fa-exclamation-circle me-1"></i> สินค้าเหลือน้อย
                                    </div>
                                @else
                                    <div class="alert alert-success py-2 mt-3">
                                        <i class="fas fa-check-circle me-1"></i> มีสินค้าพร้อม
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ประวัติการแลกรางวัลล่าสุด -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">
                    <i class="fas fa-history me-2 text-primary"></i>ประวัติการแลกรางวัลล่าสุด
                </h5>
                <a href="{{ route('admin.redeems') }}?reward_id={{ $reward->reward_id }}" class="btn btn-sm btn-outline-primary">
                    ดูทั้งหมด <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($redeems->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-history fa-4x"></i>
                    </div>
                    <h5>ยังไม่มีประวัติการแลกรางวัลนี้</h5>
                    <p class="text-muted">ยังไม่มีผู้ใช้คนใดแลกรางวัลนี้</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px" class="text-center">#</th>
                                <th>ข้อมูลผู้ใช้</th>
                                <th>วันที่แลก</th>
                                <th>สถานะ</th>
                                <th style="width: 100px" class="text-center">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($redeems->take(5) as $key => $redeem)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @if($redeem->user && $redeem->user->profile_image)
                                                <img src="{{ asset('profile_images/' . $redeem->user->profile_image) }}" class="rounded-circle" width="40" height="40" alt="Profile" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-semibold">{{ $redeem->user ? $redeem->user->username : 'N/A' }}</h6>
                                            <div class="text-muted small">{{ $redeem->user ? $redeem->user->email : 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($redeem->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    @if($redeem->status == 'pending')
                                        <span class="badge bg-warning text-dark">รอดำเนินการ</span>
                                    @elseif($redeem->status == 'completed')
                                        <span class="badge bg-success">เสร็จสิ้น</span>
                                    @elseif($redeem->status == 'cancelled')
                                        <span class="badge bg-danger">ยกเลิก</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $redeem->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($redeem->user)
                                    <a href="{{ route('admin.users.show', $redeem->user->user_id) }}" class="btn btn-sm btn-info" title="ดูข้อมูลผู้ใช้">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    // กำหนดค่าเริ่มต้นสำหรับ SweetAlert2 ทั้งหมด
    window.addEventListener('load', function() {
        Swal.mixin({
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            }
        });

        // กำหนดสี CSS สำหรับปุ่ม SweetAlert
        const style = document.createElement('style');
        style.innerHTML = `
            .swal2-confirm.swal-confirm-btn {
                background-color: #2DC679 !important;
                border-color: #2DC679 !important;
                box-shadow: none !important;
                margin-right: 10px;
            }
            .swal2-confirm:focus {
                box-shadow: 0 0 0 3px rgba(45, 198, 121, 0.3) !important;
            }
            .swal2-actions {
                justify-content: center !important;
                gap: 10px;
            }
        `;
        document.head.appendChild(style);
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Delete button
        const deleteBtn = document.getElementById('deleteRewardBtn');

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const redeemsCount = {{ $redeems->count() }};

                let warningText = 'คุณแน่ใจหรือไม่ที่จะลบรางวัล?';
                if (redeemsCount > 0) {
                    warningText += ` มีประวัติการแลกรางวัลนี้ ${redeemsCount} ครั้ง ซึ่งจะไม่สามารถลบได้`;

                    Swal.fire({
                        title: `ไม่สามารถลบรางวัล "{{ $reward->name }}" ได้`,
                        html: `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                คำเตือน: ไม่สามารถลบรางวัลที่มีประวัติการแลกแล้วได้
                            </div>
                            <p class="mt-3">มีประวัติการแลกรางวัลนี้ ${redeemsCount} ครั้ง</p>
                        `,
                        icon: 'error',
                        confirmButtonColor: '#6c757d',
                        confirmButtonText: 'เข้าใจแล้ว',
                        buttonsStyling: true,
                        customClass: {
                            confirmButton: 'swal-confirm-btn',
                        }
                    });

                    return;
                }

                Swal.fire({
                    title: `ลบรางวัล "{{ $reward->name }}"?`,
                    html: `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            คำเตือน: การลบรางวัลไม่สามารถกู้คืนได้
                        </div>
                        <p class="mt-3">${warningText}</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2DC679', // GoFit primary color
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> ลบรางวัล',
                    cancelButtonText: 'ยกเลิก',
                    buttonsStyling: true,
                    reverseButtons: false,
                    customClass: {
                        confirmButton: 'swal-confirm-btn',
                        actions: 'justify-content-center gap-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // สร้าง form สำหรับ submit การลบ
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('admin.rewards.destroy', $reward->reward_id) }}';

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endsection
