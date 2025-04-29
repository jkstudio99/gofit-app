@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลส่วนตัว')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- ส่วนหัวโปรไฟล์ -->
            <div class="card gofit-card mb-4">
                <div class="card-body text-center py-4">
                    <div class="profile-image-container mb-3">
                        @if($user->profile_image)
                            <img src="{{ asset('profile_images/' . $user->profile_image) }}" alt="รูปโปรไฟล์" class="profile-image rounded-circle">
                        @else
                            <div class="profile-placeholder rounded-circle">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $user->firstname }} {{ $user->lastname }}</h4>

                    <form action="{{ route('profile.update-image') }}" method="POST" enctype="multipart/form-data" id="profileImageForm">
                        @csrf
                        <div class="d-flex justify-content-center">
                            <div class="upload-btn-wrapper">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-camera me-1"></i> เปลี่ยนรูปโปรไฟล์
                                </button>
                                <input type="file" name="profile_image" id="profileImageInput" accept="image/*">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- คอลัมน์ซ้าย: ข้อมูลส่วนตัว -->
                <div class="col-md-6">
                    <div class="card gofit-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                ข้อมูลส่วนตัว
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                    <input id="username" type="text" class="form-control" value="{{ $user->username }}" disabled readonly>
                                    <small class="text-muted">ชื่อผู้ใช้ไม่สามารถเปลี่ยนแปลงได้</small>
                                </div>

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

                    <!-- ข้อมูลเพิ่มเติม -->
                    <div class="card gofit-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-heartbeat me-2"></i>
                                ข้อมูลสุขภาพ
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update-health') }}">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="weight" class="form-label">น้ำหนัก (กก.)</label>
                                        <input type="number" class="form-control" id="weight" name="weight" step="0.1" value="{{ $user->weight ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="height" class="form-label">ส่วนสูง (ซม.)</label>
                                        <input type="number" class="form-control" id="height" name="height" step="0.1" value="{{ $user->height ?? '' }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="birthdate" class="form-label">วันเกิด</label>
                                        <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ $user->birthdate ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">เพศ</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="">-- เลือกเพศ --</option>
                                            <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>ชาย</option>
                                            <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>หญิง</option>
                                            <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>อื่นๆ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> บันทึกข้อมูลสุขภาพ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- คอลัมน์ขวา: เปลี่ยนรหัสผ่าน -->
                <div class="col-md-6">
                    <div class="card gofit-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-lock me-2"></i>
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

                    <!-- ส่วนลบบัญชี (แสดงเฉพาะสำหรับผู้ใช้ทั่วไป) -->
                    @if($user->user_type_id == 1)
                    <div class="card gofit-card mt-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                การจัดการบัญชี
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3">ลบบัญชีของคุณ</h6>
                            <p class="text-muted mb-3">
                                เมื่อคุณลบบัญชีของคุณ ข้อมูลทั้งหมดของคุณจะถูกลบออกจากระบบอย่างถาวร ซึ่งรวมถึงประวัติการวิ่ง เป้าหมาย และข้อมูลส่วนตัวทั้งหมด
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
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }

    .profile-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 3px solid var(--primary);
    }

    .profile-placeholder {
        width: 150px;
        height: 150px;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 64px;
        color: #adb5bd;
        border: 3px solid var(--primary);
    }

    .upload-btn-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .upload-btn-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // อัปโหลดรูปโปรไฟล์อัตโนมัติเมื่อเลือกไฟล์
        document.getElementById('profileImageInput').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                document.getElementById('profileImageForm').submit();
            }
        });
    });
</script>
@endsection
