@extends('layouts.admin')

@section('title', 'รีเซ็ตรหัสผ่านผู้ใช้')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .btn-primary {
        background-color: #2DC679;
        border-color: #2DC679;
    }
    .btn-primary:hover {
        background-color: #24A664;
        border-color: #24A664;
    }
    .form-control:focus {
        border-color: #2DC679;
        box-shadow: 0 0 0 0.25rem rgba(45, 198, 121, 0.25);
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
</style>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">รีเซ็ตรหัสผ่านผู้ใช้</h1>
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> กลับไปยังข้อมูลผู้ใช้
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0">
                        <i class="fas fa-user-edit me-2 text-primary"></i>
                        รีเซ็ตรหัสผ่านสำหรับ: <strong>{{ $user->username }}</strong> ({{ $user->firstname }} {{ $user->lastname }})
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>คำเตือน:</strong> การรีเซ็ตรหัสผ่านจะทำให้รหัสผ่านเดิมของผู้ใช้ถูกแทนที่ด้วยรหัสผ่านใหม่ที่คุณกำหนด
                    </div>

                    <form action="{{ route('admin.users.update-password', $user) }}" method="POST" id="resetPasswordForm">
                        @csrf

                        <div class="mb-4">
                            <label for="password" class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร</small>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-1"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-key me-1"></i> บันทึกรหัสผ่านใหม่
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // Toggle confirm password visibility
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPassword = document.getElementById('password_confirmation');

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // Form submit with validation
        const form = document.getElementById('resetPasswordForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (password.value !== confirmPassword.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'รหัสผ่านไม่ตรงกัน',
                    text: 'กรุณาตรวจสอบว่ารหัสผ่านและการยืนยันรหัสผ่านตรงกัน',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            if (password.value.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'รหัสผ่านสั้นเกินไป',
                    text: 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            Swal.fire({
                title: 'ยืนยันการรีเซ็ตรหัสผ่าน',
                text: `คุณต้องการรีเซ็ตรหัสผ่านของ ${document.querySelector('.card-title strong').textContent} ใช่หรือไม่?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2DC679',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, รีเซ็ตรหัสผ่าน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@stop
