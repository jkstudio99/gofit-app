@extends('layouts.admin')

@section('title', 'แก้ไขข้อมูลผู้ใช้')

@section('styles')
<style>
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

@section('content_header')
    <h1>แก้ไขข้อมูลผู้ใช้</h1>
@stop

@section('content')
<div class="container py-3">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">แก้ไขข้อมูลผู้ใช้</h1>
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายละเอียดผู้ใช้
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title m-0 fw-bold">
                <i class="fas fa-user-edit me-2 text-primary"></i>
                แก้ไขข้อมูลของ {{ $user->username }}
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly disabled>
                            <small class="text-muted">ชื่อผู้ใช้ไม่สามารถเปลี่ยนแปลงได้</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="firstname" class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror" value="{{ old('firstname', $user->firstname) }}" required>
                            @error('firstname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="lastname" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror" value="{{ old('lastname', $user->lastname) }}" required>
                            @error('lastname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="telephone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', $user->telephone) }}">
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="user_type_id" class="form-label">ประเภทผู้ใช้ <span class="text-danger">*</span></label>
                            <select name="user_type_id" id="user_type_id" class="form-select @error('user_type_id') is-invalid @enderror" required>
                                @foreach($userTypes as $type)
                                    <option value="{{ $type->user_type_id }}" {{ (old('user_type_id', $user->user_type_id) == $type->user_type_id) ? 'selected' : '' }}>
                                        {{ $type->user_typename }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="user_status_id" class="form-label">สถานะบัญชี <span class="text-danger">*</span></label>
                            <select name="user_status_id" id="user_status_id" class="form-select @error('user_status_id') is-invalid @enderror" required>
                                @foreach($userStatuses as $status)
                                    <option value="{{ $status->user_status_id }}" {{ (old('user_status_id', $user->user_status_id) == $status->user_status_id) ? 'selected' : '' }}>
                                        {{ $status->user_status_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($user->user_status_id == 2)
                                <small class="text-danger">บัญชีนี้ถูกระงับการใช้งาน ผู้ใช้จะไม่สามารถเข้าสู่ระบบได้</small>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">วันที่สมัคร</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->created_at->thaiFormat('j M y H:i') }} น." readonly disabled>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> หากต้องการเปลี่ยนรหัสผ่าน โปรดใช้ตัวเลือก "รีเซ็ตรหัสผ่าน" จากหน้าแสดงข้อมูลผู้ใช้
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-times me-1"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // คำเตือนเมื่อลบบัญชี
        $('#deleteUserBtn').click(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'ยืนยันการลบบัญชี',
                text: "คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีผู้ใช้นี้? การกระทำนี้ไม่สามารถเปลี่ยนแปลงได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบบัญชี',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#deleteUserForm').submit();
                }
            });
        });
    });
</script>
@stop
