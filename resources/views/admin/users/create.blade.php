@extends('layouts.admin')

@section('title', 'เพิ่มผู้ใช้ใหม่')

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
    <h1>เพิ่มผู้ใช้ใหม่</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">กรอกข้อมูลผู้ใช้ใหม่</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="username">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">ชื่อผู้ใช้ต้องไม่ซ้ำกับผู้ใช้ที่มีอยู่</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">รหัสผ่าน <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirmation">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="firstname">ชื่อ <span class="text-danger">*</span></label>
                        <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror" value="{{ old('firstname') }}" required>
                        @error('firstname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="lastname">นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror" value="{{ old('lastname') }}" required>
                        @error('lastname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="telephone">เบอร์โทรศัพท์</label>
                        <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="user_type_id">ประเภทผู้ใช้ <span class="text-danger">*</span></label>
                                <select name="user_type_id" id="user_type_id" class="form-control @error('user_type_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกประเภทผู้ใช้ --</option>
                                    @foreach($userTypes as $type)
                                        <option value="{{ $type->user_type_id }}" {{ old('user_type_id') == $type->user_type_id ? 'selected' : '' }}>
                                            {{ $type->user_typename }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="user_status_id">สถานะ <span class="text-danger">*</span></label>
                                <select name="user_status_id" id="user_status_id" class="form-control @error('user_status_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกสถานะ --</option>
                                    @foreach($userStatuses as $status)
                                        <option value="{{ $status->user_status_id }}" {{ old('user_status_id') == $status->user_status_id ? 'selected' : '' }}>
                                            {{ $status->user_status_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_status_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> ยกเลิก
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>
@stop
