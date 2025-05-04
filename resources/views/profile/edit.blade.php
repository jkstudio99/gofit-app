@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลส่วนตัว')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- ส่วนหัวโปรไฟล์ -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body text-center py-4">
                    <div class="profile-image-container mb-3">
                        @if($user->profile_image)
                            <img src="{{ asset('profile_images/' . $user->profile_image) }}" alt="รูปโปรไฟล์" class="profile-image rounded-circle border shadow-sm">
                        @else
                            <div class="profile-placeholder rounded-circle border shadow-sm">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <button type="button" class="profile-upload-icon" id="changeProfileBtn">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->firstname }} {{ $user->lastname }}</h5>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-at me-1"></i> {{ $user->username }}
                        @if($user->user_type_id == 2)
                        <span class="badge bg-primary ms-2">ผู้ดูแลระบบ</span>
                        @endif
                    </p>

                    <form action="{{ route('profile.update-image') }}" method="POST" enctype="multipart/form-data" id="profileImageForm" style="display: none;">
                        @csrf
                        <input type="file" name="profile_image" id="profileImageInput" accept="image/*">
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- ข้อมูลส่วนตัว -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-user-edit me-2 text-primary"></i>
                                ข้อมูลส่วนตัว
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="firstname" class="form-label">ชื่อ</label>
                                    <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname', $user->firstname) }}" required autocomplete="firstname">
                                    @error('firstname')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="lastname" class="form-label">นามสกุล</label>
                                    <input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname', $user->lastname) }}" required autocomplete="lastname">
                                    @error('lastname')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">อีเมล</label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="telephone" class="form-label">เบอร์โทรศัพท์</label>
                                    <input id="telephone" type="text" class="form-control @error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone', $user->telephone) }}" autocomplete="telephone">
                                    @error('telephone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- เปลี่ยนรหัสผ่าน -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 mb-4" id="password">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-lock me-2 text-primary"></i>
                                เปลี่ยนรหัสผ่าน
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update-password') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">รหัสผ่านปัจจุบัน</label>
                                    <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">รหัสผ่านใหม่</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="text-muted">รหัสผ่านควรมีความยาวอย่างน้อย 8 ตัวอักษร</small>
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key me-1"></i> เปลี่ยนรหัสผ่าน
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- เฉพาะผู้ใช้ทั่วไป -->
                    @if($user->user_type_id == 1)
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-danger text-white border-0">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                การจัดการบัญชี
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3">ลบบัญชีของคุณ</h6>
                            <p class="text-muted mb-3">
                                เมื่อคุณลบบัญชีของคุณ ข้อมูลทั้งหมดของคุณจะถูกลบออกจากระบบอย่างถาวร
                            </p>
                            <div class="d-grid">
                                <a href="{{ route('profile.delete.confirm') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-trash-alt me-1"></i> ลบบัญชีของฉัน
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .profile-image-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }

    .profile-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }

    .profile-placeholder {
        width: 120px;
        height: 120px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #adb5bd;
    }

    .card {
        border-radius: 0.75rem;
    }

    .badge.bg-primary {
        background-color: #2196F3 !important;
    }

    .border {
        border-color: rgba(0,0,0,0.08) !important;
    }

    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.08);
        padding: 1rem 1.25rem;
    }
</style>
@endsection

@section('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // เปลี่ยนการแสดงปุ่มอัปโหลดรูป
        const changeProfileBtn = document.getElementById('changeProfileBtn');
        const profileImageInput = document.getElementById('profileImageInput');
        const profileImageForm = document.getElementById('profileImageForm');

        // เมื่อคลิกที่ปุ่มจะเปิดหน้าต่างให้เลือกไฟล์
        changeProfileBtn.addEventListener('click', function() {
            profileImageInput.click();
        });

        // เมื่อมีการเลือกไฟล์รูปภาพใหม่
        profileImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];

                // ตรวจสอบว่าเป็นไฟล์รูปภาพหรือไม่
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        title: 'ข้อผิดพลาด!',
                        text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                    return;
                }

                // ตรวจสอบขนาดไฟล์ (ไม่เกิน 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: 'ข้อผิดพลาด!',
                        text: 'ขนาดไฟล์ต้องไม่เกิน 2MB',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                    return;
                }

                // แสดง SweetAlert ยืนยันการอัปโหลด
                Swal.fire({
                    title: 'อัปโหลดรูปโปรไฟล์',
                    text: 'คุณต้องการอัปโหลดรูปโปรไฟล์นี้หรือไม่?',
                    imageUrl: URL.createObjectURL(file),
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: 'รูปโปรไฟล์ใหม่',
                    showCancelButton: true,
                    confirmButtonText: 'อัปโหลด',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // แสดง loading
                        Swal.fire({
                            title: 'กำลังอัปโหลด...',
                            text: 'โปรดรอสักครู่',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // ส่งฟอร์ม
                        profileImageForm.submit();
                    }
                });
            }
        });

        // แสดง SweetAlert เมื่อมีข้อผิดพลาดหรือสำเร็จ
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
                title: 'ข้อผิดพลาด!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        @endif
    });
</script>
@endsection
