@extends('layouts.admin')

@section('title', 'รายละเอียดเหรียญตรา - ' . $badge->badge_name)

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .badge-image {
        max-height: 200px;
        object-fit: contain;
    }

    .badge-info-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .badge-info-item:last-child {
        border-bottom: none;
    }

    .badge-stats-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .badge-stats-card:hover {
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
                <h1 class="h3 mb-0">รายละเอียดเหรียญตรา</h1>
                <div>
                    <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                    </a>
                    <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> แก้ไข
                    </a>
                    <button type="button" class="btn btn-danger" id="deleteBadgeBtn">
                        <i class="fas fa-trash me-1"></i> ลบเหรียญตรา
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- รายละเอียดเหรียญตรา -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-4 text-center">
                            @if($badge->badge_image)
                                <img src="{{ asset('storage/' . $badge->badge_image) }}" alt="{{ $badge->badge_name }}" class="badge-image">
                            @else
                                <div class="bg-light d-flex justify-content-center align-items-center" style="height: 200px;">
                                    <i class="fas fa-medal fa-5x text-secondary"></i>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <h2 class="mb-3">{{ $badge->badge_name }}</h2>

                            <div class="mb-3">
                                @if($badge->type == 'distance')
                                    <span class="badge bg-info text-dark px-3 py-2">
                                        <i class="fas fa-route me-1"></i> ประเภท: ระยะทาง
                                    </span>
                                @elseif($badge->type == 'calories')
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="fas fa-fire-alt me-1"></i> ประเภท: แคลอรี่
                                    </span>
                                @elseif($badge->type == 'streak')
                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-calendar-check me-1"></i> ประเภท: ต่อเนื่อง
                                    </span>
                                @elseif($badge->type == 'speed')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-tachometer-alt me-1"></i> ประเภท: ความเร็ว
                                    </span>
                                @elseif($badge->type == 'event')
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="fas fa-calendar-day me-1"></i> ประเภท: กิจกรรม
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="fas fa-medal me-1"></i> ประเภท: {{ $badge->type }}
                                    </span>
                                @endif
                            </div>

                            <p class="mb-3">{{ $badge->badge_description }}</p>

                            <div class="badge-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-trophy me-2"></i>เงื่อนไขการได้รับ:</span>
                                <span class="fw-bold">{{ $badge->getRequirementText() }}</span>
                            </div>

                            <div class="badge-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-users me-2"></i>จำนวนผู้ได้รับ:</span>
                                <a href="{{ route('admin.badges.users', $badge) }}" class="fw-bold text-primary">
                                    {{ $badge->users()->count() }} คน <i class="fas fa-external-link-alt ms-1 small"></i>
                                </a>
                            </div>

                            <div class="badge-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-calendar-plus me-2"></i>วันที่สร้าง:</span>
                                <span>{{ \Carbon\Carbon::parse($badge->created_at)->format('d/m/Y H:i') }}</span>
                            </div>

                            <div class="badge-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-calendar-check me-2"></i>แก้ไขล่าสุด:</span>
                                <span>{{ \Carbon\Carbon::parse($badge->updated_at)->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- สถิติและข้อมูลเพิ่มเติม -->
        <div class="col-lg-4">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-1 g-4">
                <!-- สถิติผู้ได้รับเหรียญตรา -->
                <div class="col">
                    <div class="card shadow-sm badge-stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-primary-subtle text-primary mx-auto">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">ผู้ได้รับเหรียญตรา</h5>
                            <h3 class="mb-0">{{ $badge->users()->count() }}</h3>
                            <div class="text-muted small mt-2">คน</div>

                            <a href="{{ route('admin.badges.users', $badge) }}" class="btn btn-outline-primary mt-3">
                                <i class="fas fa-list me-2"></i>ดูรายชื่อทั้งหมด
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลเงื่อนไขเหรียญ -->
                <div class="col">
                    <div class="card shadow-sm badge-stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-success-subtle text-success mx-auto">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h5 class="card-title">เงื่อนไขการได้รับ</h5>
                            <div class="mt-2">
                                @if($badge->type == 'distance')
                                    <h3 class="mb-0">{{ number_format($badge->criteria, 1) }}</h3>
                                    <div class="text-muted small mt-2">กิโลเมตร</div>
                                @elseif($badge->type == 'calories')
                                    <h3 class="mb-0">{{ number_format($badge->criteria) }}</h3>
                                    <div class="text-muted small mt-2">แคลอรี่</div>
                                @elseif($badge->type == 'streak')
                                    <h3 class="mb-0">{{ number_format($badge->criteria) }}</h3>
                                    <div class="text-muted small mt-2">วันติดต่อกัน</div>
                                @elseif($badge->type == 'speed')
                                    <h3 class="mb-0">{{ number_format($badge->criteria, 1) }}</h3>
                                    <div class="text-muted small mt-2">กม./ชม.</div>
                                @elseif($badge->type == 'event')
                                    <h3 class="mb-0">{{ number_format($badge->criteria) }}</h3>
                                    <div class="text-muted small mt-2">กิจกรรม</div>
                                @else
                                    <h3 class="mb-0">{{ number_format($badge->criteria) }}</h3>
                                    <div class="text-muted small mt-2">หน่วย</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- รายชื่อผู้ได้รับเหรียญล่าสุด -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">
                    <i class="fas fa-users me-2 text-primary"></i>ผู้ได้รับเหรียญตราล่าสุด
                </h5>
                <a href="{{ route('admin.badges.users', $badge) }}" class="btn btn-sm btn-outline-primary">
                    ดูทั้งหมด <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($badge->users()->count() == 0)
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-users fa-4x"></i>
                    </div>
                    <h5>ยังไม่มีผู้ใช้ได้รับเหรียญตรานี้</h5>
                    <p class="text-muted">ยังไม่มีผู้ใช้คนใดที่บรรลุเงื่อนไขการได้รับเหรียญตรานี้</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px" class="text-center">#</th>
                                <th>ข้อมูลผู้ใช้</th>
                                <th>วันที่ได้รับ</th>
                                <th style="width: 100px" class="text-center">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($badge->users()->orderBy('tb_user_badge.earned_at', 'desc')->take(5)->get() as $key => $user)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @if($user->profile_image)
                                                <img src="{{ asset('profile_images/' . $user->profile_image) }}" class="rounded-circle" width="40" height="40" alt="Profile" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-semibold">{{ $user->username }}</h6>
                                            <div class="text-muted small">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($user->pivot->earned_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info" title="ดูข้อมูลผู้ใช้">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i> ข้อมูลเหรียญตรา</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-muted">ชื่อเหรียญตรา</label>
                    <div class="fw-bold">{{ $badge->badge_name }}</div>
                </div>
                <div class="col-md-4">
                    <label class="text-muted">ประเภท</label>
                    <div>
                        @if($badge->type == 'distance')
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-route me-1"></i> ระยะทาง
                            </span>
                        @elseif($badge->type == 'calories')
                            <span class="badge bg-danger">
                                <i class="fas fa-fire-alt me-1"></i> แคลอรี่
                            </span>
                        @elseif($badge->type == 'streak')
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-calendar-check me-1"></i> ต่อเนื่อง
                            </span>
                        @elseif($badge->type == 'speed')
                            <span class="badge bg-success">
                                <i class="fas fa-tachometer-alt me-1"></i> ความเร็ว
                            </span>
                        @elseif($badge->type == 'event')
                            <span class="badge bg-primary">
                                <i class="fas fa-calendar-day me-1"></i> กิจกรรม
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-medal me-1"></i> {{ $badge->type }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-muted">เกณฑ์</label>
                    <div class="fw-bold">
                        @if($badge->type == 'distance')
                            {{ $badge->criteria }} กิโลเมตร
                        @elseif($badge->type == 'calories')
                            {{ $badge->criteria }} แคลอรี่
                        @elseif($badge->type == 'streak')
                            {{ $badge->criteria }} วันติดต่อกัน
                        @elseif($badge->type == 'speed')
                            {{ $badge->criteria }} กม./ชม.
                        @elseif($badge->type == 'event')
                            {{ $badge->criteria }} กิจกรรม
                        @else
                            {{ $badge->criteria }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-muted">คะแนนที่จะได้รับ</label>
                    <div class="fw-bold">
                        <span class="badge bg-warning text-dark p-2">
                            <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-muted">จำนวนผู้ได้รับ</label>
                    <div class="fw-bold">{{ $badge->users()->count() }} คน</div>
                </div>
                <div class="col-md-4">
                    <label class="text-muted">วันที่สร้าง</label>
                    <div>{{ $badge->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
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
        const deleteBtn = document.getElementById('deleteBadgeBtn');

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const usersCount = {{ $badge->users()->count() }};

                let warningText = 'คุณแน่ใจหรือไม่ที่จะลบเหรียญตรา?';
                if (usersCount > 0) {
                    warningText += ` มีผู้ใช้ ${usersCount} คนที่ได้รับเหรียญตรานี้ ซึ่งจะถูกลบออกด้วย`;
                }

                Swal.fire({
                    title: `ลบเหรียญตรา "{{ $badge->badge_name }}"?`,
                    html: `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            คำเตือน: การลบเหรียญตราไม่สามารถกู้คืนได้
                        </div>
                        <p class="mt-3">${warningText}</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2DC679', // GoFit primary color
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> ลบเหรียญตรา',
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
                        form.action = '{{ route('admin.badges.destroy', $badge) }}';

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
