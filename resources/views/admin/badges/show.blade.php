@extends('layouts.admin')

@section('title', 'รายละเอียดเหรียญตรา - ' . $badge->badge_name)

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .badge-image {
        max-height: 200px;
        object-fit: contain;
        transition: all 0.3s ease;
    }

    .badge-image:hover {
        transform: scale(1.05);
    }

    .badge-info-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .badge-info-item:last-child {
        border-bottom: none;
    }

    /* Stats Cards */
    .badge-stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .badge-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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

    .stats-icon {
        font-size: 2rem;
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .badge-stats-card:hover .stats-icon {
        transform: scale(1.1);
    }

    /* Badge Type Styling */
    .badge-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* User List */
    .user-avatar {
        transition: all 0.3s ease;
    }

    tr:hover .user-avatar {
        transform: scale(1.1);
    }

    /* Badges */
    .badge-pill {
        padding: 0.6rem 1rem;
        border-radius: 50rem;
        font-weight: 500;
    }

    /* Card Design */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
    }

    .table thead th {
        font-weight: 600;
        color: #495057;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(45, 198, 121, 0.05);
    }

    .badge-img {
        max-height: 150px;
        object-fit: contain;
    }

    .detail-card {
        border-radius: 10px;
        overflow: hidden;
    }

    .badge-banner {
        min-height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .badge-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
    }

    .progress-container {
        width: 100%;
    }

    /* Make delete button icons white */
    .btn-danger i, .btn-danger.badge-action-btn i {
        color: white !important;
    }

    /* Use primary color from design system */
    .btn-primary, .bg-primary {
        background-color: #2DC679 !important;
        border-color: #2DC679 !important;
    }

    .btn-primary:hover {
        background-color: #24A664 !important;
        border-color: #24A664 !important;
    }

    /* Badge info styling */
    .badge.bg-info {
        background-color: #3B82F6 !important;
        color: white !important;
    }

    .btn-info.badge-action-btn, .btn-info {
        background-color: #3B82F6 !important;
        border-color: #3B82F6 !important;
        color: white !important;
    }

    .btn-info.badge-action-btn:hover, .btn-info:hover {
        background-color: #2563EB !important;
        border-color: #2563EB !important;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">รายละเอียดเหรียญตรา</h2>
        <div>
            <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
            </a>
            <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i> แก้ไข
            </a>
            <button type="button" class="btn btn-danger text-white" id="deleteBadgeBtn">
                <i class="fas fa-trash me-1"></i> ลบเหรียญตรา
            </button>
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
                                <div class="bg-light d-flex justify-content-center align-items-center" style="height: 200px; border-radius: 10px;">
                                    <i class="fas fa-medal fa-5x text-secondary"></i>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <h2 class="mb-3">{{ $badge->badge_name }}</h2>

                            <div class="mb-3">
                                @php
                                    $typeIcons = [
                                        'distance' => 'fa-route',
                                        'calories' => 'fa-fire',
                                        'streak' => 'fa-calendar-check',
                                        'speed' => 'fa-tachometer-alt',
                                        'event' => 'fa-trophy'
                                    ];
                                    $typeColors = [
                                        'distance' => 'success',
                                        'calories' => 'danger',
                                        'streak' => 'success',
                                        'speed' => 'info',
                                        'event' => 'warning'
                                    ];
                                    $typeNames = [
                                        'distance' => 'ระยะทาง',
                                        'calories' => 'แคลอรี่',
                                        'streak' => 'ต่อเนื่อง',
                                        'speed' => 'ความเร็ว',
                                        'event' => 'กิจกรรม'
                                    ];

                                    $color = isset($typeColors[$badge->type]) ? $typeColors[$badge->type] : 'secondary';
                                    $icon = isset($typeIcons[$badge->type]) ? $typeIcons[$badge->type] : 'fa-medal';
                                    $typeName = isset($typeNames[$badge->type]) ? $typeNames[$badge->type] : $badge->type;
                                @endphp

                                <span class="badge bg-{{ $color }} badge-pill">
                                    <i class="fas {{ $icon }} me-1"></i> ประเภท: {{ $typeName }}
                                </span>
                            </div>

                            <p class="mb-3">{{ $badge->badge_desc }}</p>

                            <div class="badge-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-trophy me-2"></i>เงื่อนไขการได้รับ:</span>
                                <span class="fw-bold">
                                    @if($badge->type == 'distance')
                                        วิ่งระยะทางสะสม {{ $badge->criteria }} กม.
                                    @elseif($badge->type == 'calories')
                                        เผาผลาญแคลอรี่สะสม {{ $badge->criteria }} แคลอรี่
                                    @elseif($badge->type == 'streak')
                                        วิ่งติดต่อกัน {{ $badge->criteria }} วัน
                                    @elseif($badge->type == 'speed')
                                        วิ่งด้วยความเร็วเฉลี่ย {{ $badge->criteria }} กม./ชม.
                                    @elseif($badge->type == 'event')
                                        เข้าร่วมกิจกรรม {{ $badge->criteria }} ครั้ง
                                    @else
                                        {{ $badge->criteria }}
                                    @endif
                                </span>
                            </div>

                            <div class="badge-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-users me-2"></i>จำนวนผู้ได้รับ:</span>
                                <a href="{{ route('admin.badges.users', $badge) }}" class="fw-bold text-primary">
                                    {{ $badge->users()->count() }} คน <i class="fas fa-external-link-alt ms-1 small"></i>
                                </a>
                            </div>

                            <div class="badge-info-item d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-coins me-2"></i>คะแนนที่ได้รับ:</span>
                                <span class="badge bg-warning text-dark px-3 py-1">
                                    <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                                </span>
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
                            <div class="stats-icon bg-primary text-white bg-opacity-10 mx-auto">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">ผู้ได้รับเหรียญตรา</h5>
                            <h3 class="mb-0">{{ $badge->users()->count() }}</h3>
                            <div class="text-muted small mt-2">คน</div>

                            <a href="{{ route('admin.badges.users', $badge) }}" class="btn btn-outline-primary mt-3 w-100">
                                <i class="fas fa-list me-2"></i>ดูรายชื่อทั้งหมด
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลเงื่อนไขเหรียญ -->
                <div class="col">
                    <div class="card shadow-sm badge-stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-{{ $color }} bg-opacity-10 text-{{ $color }} mx-auto">
                                <i class="fas {{ $icon }}"></i>
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
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">
                    <i class="fas fa-users me-2 text-{{ $color }}"></i>ผู้ได้รับเหรียญตราล่าสุด
                </h5>
                <a href="{{ route('admin.badges.users', $badge) }}" class="btn btn-sm btn-outline-{{ $color }}">
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
                                                <img src="{{ asset('profile_images/' . $user->profile_image) }}" class="rounded-circle user-avatar" width="40" height="40" alt="Profile" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white user-avatar" style="width: 40px; height: 40px;">
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
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // กำหนดค่าเริ่มต้นสำหรับ SweetAlert2 ทั้งหมด
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

        // Add animation and hover effects
        const badgeImage = document.querySelector('.badge-image');
        const statsCards = document.querySelectorAll('.badge-stats-card');

        if (badgeImage) {
            badgeImage.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.3s ease';
            });
        }

        statsCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.stats-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.1)';
                }
            });

            card.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.stats-icon');
                if (icon) {
                    icon.style.transform = 'scale(1)';
                }
            });
        });

        // Display SweetAlert for session message if exists
        @if(session('success'))
            Swal.fire({
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'ผิดพลาด!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        @endif
    });
</script>

<!-- Include SweetAlert message partial -->
@include('partials.sweetalert-messages')
@endsection
