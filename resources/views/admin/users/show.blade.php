@extends('layouts.admin')

@section('title', 'ข้อมูลผู้ใช้ - ' . $user->username)

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<style>
    .profile-image {
        max-height: 120px;
        max-width: 120px;
        object-fit: cover;
        border-radius: 50%;
    }
    .badge {
        font-weight: 500;
    }
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        margin-right: 0.5rem;
    }
    .nav-pills .nav-link.active {
        color: #fff;
        background-color: #007bff;
    }
    .tab-pane {
        padding: 1rem 0;
    }

    /* Button styling for action buttons - matching badges style */
    .btn-info.badge-action-btn {
        background-color: #3B82F6 !important;
        border-color: #3B82F6 !important;
        color: white !important;
    }

    .btn-info.badge-action-btn:hover {
        background-color: #2563EB !important;
        border-color: #2563EB !important;
    }

    .btn-warning.badge-action-btn {
        background-color: #F59E0B !important;
        border-color: #F59E0B !important;
    }

    .btn-warning.badge-action-btn:hover {
        background-color: #D97706 !important;
        border-color: #D97706 !important;
    }

    .btn-secondary.badge-action-btn {
        background-color: #6B7280 !important;
        border-color: #6B7280 !important;
    }

    .btn-secondary.badge-action-btn:hover {
        background-color: #4B5563 !important;
        border-color: #4B5563 !important;
    }

    .btn-danger.badge-action-btn {
        background-color: #EF4444 !important;
        border-color: #EF4444 !important;
    }

    .btn-danger.badge-action-btn:hover {
        background-color: #DC2626 !important;
        border-color: #DC2626 !important;
    }

    .btn-danger i, .btn-danger.badge-action-btn i,
    .btn-info.badge-action-btn i,
    .btn-secondary.badge-action-btn i {
        color: white !important;
    }

    /* Make buttons circular and ensure proper icon alignment */
    .badge-action-btn {
        border-radius: 50% !important;
        width: 36px !important;
        height: 36px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 3px !important;
    }

    .badge-action-btn i {
        font-size: 0.875rem !important;
    }

    /* For action buttons that include text */
    .badge-action-btn.with-text {
        border-radius: 0.5rem !important;
        width: auto !important;
        height: auto !important;
        padding: 0.375rem 0.75rem !important;
    }

    /* SweetAlert2 Custom Styles */
    .swal2-styled.swal2-confirm {
        background-color: #2DC679 !important;
        border-radius: 0.5rem !important;
        font-weight: 500 !important;
        padding: 0.75rem 1.5rem !important;
        box-shadow: 0 5px 10px rgba(45, 198, 121, 0.25) !important;
    }

    .swal2-styled.swal2-confirm:hover {
        background-color: #24A664 !important;
    }

    .swal2-styled.swal2-cancel {
        background-color: #FFFFFF !important;
        color: #4A4A4A !important;
        border: 1px solid #E9E9E9 !important;
        border-radius: 0.5rem !important;
        font-weight: 500 !important;
        padding: 0.75rem 1.5rem !important;
    }

    .swal2-styled.swal2-cancel:hover {
        background-color: #F8F8F8 !important;
    }

    .swal2-popup {
        border-radius: 0.75rem !important;
        padding: 1.5rem !important;
        font-family: 'Noto Sans Thai', -apple-system, sans-serif !important;
    }

    .swal2-title {
        color: #121212 !important;
        font-weight: 700 !important;
    }

    .swal2-html-container {
        color: #4A4A4A !important;
    }

    .swal2-icon.swal2-question {
        border-color: #2DC679 !important;
        color: #2DC679 !important;
    }

    .swal2-icon.swal2-warning {
        border-color: #FFB800 !important;
        color: #FFB800 !important;
    }

    .swal2-icon.swal2-error {
        border-color: #FF4646 !important;
        color: #FF4646 !important;
    }

    .swal2-icon.swal2-success {
        border-color: #2DC679 !important;
        color: #2DC679 !important;
    }

    .swal2-icon.swal2-success [class^=swal2-success-line] {
        background-color: #2DC679 !important;
    }

    .swal2-icon.swal2-success .swal2-success-ring {
        border-color: rgba(45, 198, 121, 0.3) !important;
    }

    /* Card styling */
    .card {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }

    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.08);
        padding: 0.75rem 1.25rem;
    }

    /* User info items */
    .user-info-item {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .user-info-item:last-child {
        border-bottom: none;
    }

    .user-info-item i {
        width: 24px;
        color: #2DC679;
        text-align: center;
        margin-right: 12px;
    }

    .user-info-item .label {
        color: #6c757d;
        font-size: 0.9rem;
        min-width: 100px;
    }

    .user-info-item .value {
        font-weight: 500;
        margin-left: auto;
    }
</style>
<script>
function openFileDialog() {
    document.getElementById('profileImageInput').click();
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    // ตรวจสอบประเภทไฟล์
    const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    if (!validImageTypes.includes(file.type)) {
        Swal.fire({
            title: 'ไม่สามารถอัพโหลดได้',
            text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPEG, PNG, GIF, WEBP)',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
        event.target.value = ''; // ล้างค่า input
        return;
    }

    // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({
            title: 'ไม่สามารถอัพโหลดได้',
            text: 'ขนาดไฟล์ต้องไม่เกิน 5MB',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
        event.target.value = ''; // ล้างค่า input
        return;
    }

    // แสดงตัวอย่างรูปภาพและยืนยันการอัพโหลด
    const reader = new FileReader();
    reader.onload = function(e) {
        const imgSrc = e.target.result;

        Swal.fire({
            title: 'ยืนยันการเปลี่ยนรูปโปรไฟล์',
            html: `
                <div class="text-center mb-3">
                    <img src="${imgSrc}" style="max-width: 200px; max-height: 200px; border-radius: 50%;" class="img-fluid mb-2">
                </div>
                <p>คุณต้องการใช้รูปนี้เป็นรูปโปรไฟล์ใหม่หรือไม่?</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดง loading
                Swal.fire({
                    title: 'กำลังอัพโหลด...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // ส่งฟอร์ม
                setTimeout(() => {
                    document.getElementById('profileImageForm').submit();
                }, 500);
            } else {
                // ยกเลิกการอัพโหลด ล้างค่า input
                document.getElementById('profileImageInput').value = '';
            }
        });
    };
    reader.readAsDataURL(file);
}

// เตรียม event handlers สำหรับเมื่อหน้าเว็บโหลดเสร็จ
document.addEventListener('DOMContentLoaded', function() {
    // แสดงข้อความแจ้งเตือน success หรือ error หลังจากการอัพโหลด
    @if(session('success'))
    Swal.fire({
        title: 'สำเร็จ!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'ตกลง'
    });
    @endif

    @if(session('error'))
    Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: 'ตกลง'
    });
    @endif

    // เพิ่ม event listener สำหรับ input file
    document.getElementById('profileImageInput').addEventListener('change', handleFileSelect);
});
</script>
@endsection

@section('content')
<div class="container py-3">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">รายละเอียดผู้ใช้</h1>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning badge-action-btn" title="แก้ไขข้อมูล">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('admin.users.reset-password', $user) }}" class="btn btn-secondary badge-action-btn" title="รีเซ็ตรหัสผ่าน">
                        <i class="fas fa-key"></i>
                    </a>
                    @if(Auth::id() != $user->user_id)
                    <button type="button" class="btn btn-danger badge-action-btn" id="deleteUserBtn" title="ลบผู้ใช้นี้">
                        <i class="fas fa-trash"></i>
                    </button>
                    <form id="deleteUserForm" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ข้อมูลผู้ใช้ -->
        <div class="col-lg-4">
            <!-- User Profile Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-4 position-relative">
                    <div class="mb-4 position-relative mx-auto" style="width: 120px; height: 120px;">
                        @if($user->profile_image)
                            <img src="{{ asset('profile_images/' . $user->profile_image) }}" alt="{{ $user->username }}" class="rounded-circle shadow-sm border" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm border" style="width: 120px; height: 120px;">
                                <i class="fas fa-user fa-3x text-secondary"></i>
                            </div>
                        @endif

                        <!-- ปุ่มเปลี่ยนรูปโปรไฟล์ - ใช้แท็ก button ธรรมดา -->
                        <button type="button" onclick="openFileDialog()" class="btn btn-sm btn-primary rounded-circle position-absolute" style="bottom: 0; right: 0; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 5;">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>

                    <h4 class="fw-bold mb-1">{{ $user->firstname }} {{ $user->lastname }}</h4>
                    <p class="text-muted mb-3">
                        <i class="fas fa-at me-1"></i> {{ $user->username }}
                    </p>

                    <div class="mb-3">
                        @if($user->user_type_id == 1)
                            <span class="badge bg-primary px-3 py-2"><i class="fas fa-user me-1"></i> ผู้ใช้ทั่วไป</span>
                        @else
                            <span class="badge bg-admin px-3 py-2"><i class="fas fa-user-shield me-1"></i> ผู้ดูแลระบบ</span>
                        @endif

                        @if($user->user_status_id == 1)
                            <span class="badge badge-outline-success px-3 py-2 ms-1"><i class="fas fa-check-circle me-1"></i> ใช้งาน</span>
                        @else
                            <span class="badge badge-outline-danger px-3 py-2 ms-1"><i class="fas fa-ban me-1"></i> ระงับการใช้งาน</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-0">{{ $runningActivities->count() }}</h5>
                            <small class="text-muted">กิจกรรม</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">{{ $events->count() }}</h5>
                            <small class="text-muted">อีเวนต์</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">{{ $badges->count() }}</h5>
                            <small class="text-muted">เหรียญตรา</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>ข้อมูลติดต่อ</h5>
                </div>
                <div class="card-body">
                    <div class="user-info-item">
                        <i class="fas fa-envelope"></i>
                        <span class="label">อีเมล</span>
                        <span class="value">{{ $user->email }}</span>
                    </div>
                    <div class="user-info-item">
                        <i class="fas fa-phone"></i>
                        <span class="label">เบอร์โทรศัพท์</span>
                        <span class="value">{{ $user->telephone ?? 'ไม่ระบุ' }}</span>
                    </div>
                </div>
            </div>

            <!-- System Information Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0 fw-bold"><i class="fas fa-cogs me-2 text-primary"></i>ข้อมูลระบบ</h5>
                </div>
                <div class="card-body">
                    <div class="user-info-item">
                        <i class="fas fa-id-card"></i>
                        <span class="label">รหัสผู้ใช้</span>
                        <span class="value">{{ $user->user_id }}</span>
                    </div>
                    <div class="user-info-item">
                        <i class="fas fa-calendar-plus"></i>
                        <span class="label">วันที่สมัคร</span>
                        <span class="value">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="user-info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="label">อัปเดตล่าสุด</span>
                        <span class="value">{{ $user->updated_at->format('d/m/Y') }}</span>
                    </div>
                    @if($user->last_login_at)
                    <div class="user-info-item">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="label">เข้าสู่ระบบล่าสุด</span>
                        <span class="value">{{ \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    <div class="user-info-item">
                        <i class="fas fa-coins"></i>
                        <span class="label">คะแนนทั้งหมด</span>
                        <span class="value">{{ $user->points ?? 0 }} คะแนน</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- แท็บข้อมูลอื่นๆ -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <ul class="nav nav-pills" id="userTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="badges-tab" data-bs-toggle="tab" data-bs-target="#badges" type="button" role="tab" aria-controls="badges" aria-selected="true">
                                <i class="fas fa-award me-1"></i> เหรียญตรา ({{ $badges->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="events" aria-selected="false">
                                <i class="fas fa-calendar-day me-1"></i> อีเวนต์ ({{ $events->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab" aria-controls="activities" aria-selected="false">
                                <i class="fas fa-running me-1"></i> กิจกรรม ({{ $runningActivities->count() }})
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="userTabsContent">
                        <!-- เหรียญตรา -->
                        <div class="tab-pane fade show active" id="badges" role="tabpanel" aria-labelledby="badges-tab">
                            @if($badges->count() > 0)
                                <div class="row mt-3">
                                    @foreach($badges as $badge)
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <img src="{{ asset('badges/' . $badge->badge_image) }}" alt="{{ $badge->badge_name }}" class="img-fluid mb-2" style="height: 80px;">
                                                    <h5 class="card-title mb-1">{{ $badge->badge_name }}</h5>
                                                    <p class="text-muted small">ได้รับเมื่อ {{ $badge->pivot->created_at->format('d/m/Y') }}</p>
                                                    <p class="small">{{ $badge->badge_description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-light my-3">
                                    <i class="fas fa-info-circle me-2"></i> ยังไม่มีเหรียญตราในขณะนี้
                                </div>
                            @endif
                        </div>

                        <!-- อีเวนต์ -->
                        <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
                            @if($events->count() > 0)
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>อีเวนต์</th>
                                                <th>วันที่</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($events as $event)
                                                <tr>
                                                    <td>{{ $event->event_name }}</td>
                                                    <td>{{ $event->event_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        @switch($event->pivot->status)
                                                            @case('registered')
                                                                <span class="badge bg-success">ลงทะเบียนแล้ว</span>
                                                                @break
                                                            @case('attended')
                                                                <span class="badge bg-primary">เข้าร่วมแล้ว</span>
                                                                @break
                                                            @case('cancelled')
                                                                <span class="badge bg-danger">ยกเลิกแล้ว</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">{{ $event->pivot->status }}</span>
                                                        @endswitch
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-light my-3">
                                    <i class="fas fa-info-circle me-2"></i> ยังไม่มีการลงทะเบียนอีเวนต์ในขณะนี้
                                </div>
                            @endif
                        </div>

                        <!-- กิจกรรม -->
                        <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                            @if($runningActivities->count() > 0)
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>วันที่</th>
                                                <th>กิจกรรม</th>
                                                <th>ระยะทาง</th>
                                                <th>เวลา</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($runningActivities as $activity)
                                                <tr>
                                                    <td>{{ $activity->created_at->format('d/m/Y') }}</td>
                                                    <td>{{ $activity->activity_name }}</td>
                                                    <td>{{ $activity->distance }} กม.</td>
                                                    <td>{{ $activity->duration_formatted }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-light my-3">
                                    <i class="fas fa-info-circle me-2"></i> ยังไม่มีกิจกรรมในขณะนี้
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ฟอร์มสำหรับอัพโหลดรูปโปรไฟล์ (ซ่อนไว้) -->
<form id="profileImageForm" action="{{ route('admin.users.update-profile-image', $user->user_id) }}" method="POST" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="file" name="profile_image" id="profileImageInput" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
</form>
@endsection
