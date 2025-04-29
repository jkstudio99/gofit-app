@extends('layouts.admin')

@section('title', 'แก้ไขข้อมูลผู้ใช้')

@section('content_header')
    <h1>แก้ไขข้อมูลผู้ใช้</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">แก้ไขข้อมูลผู้ใช้: {{ $user->username }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="username">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" value="{{ $user->username }}" readonly disabled>
                        <small class="text-muted">ชื่อผู้ใช้ไม่สามารถเปลี่ยนแปลงได้</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="firstname">ชื่อ <span class="text-danger">*</span></label>
                        <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror" value="{{ old('firstname', $user->firstname) }}" required>
                        @error('firstname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="lastname">นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror" value="{{ old('lastname', $user->lastname) }}" required>
                        @error('lastname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="telephone">เบอร์โทรศัพท์</label>
                        <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', $user->telephone) }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="user_type_id">ประเภทผู้ใช้ <span class="text-danger">*</span></label>
                        <select name="user_type_id" id="user_type_id" class="form-control @error('user_type_id') is-invalid @enderror" required>
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
                        <label for="user_status_id">สถานะบัญชี <span class="text-danger">*</span></label>
                        <select name="user_status_id" id="user_status_id" class="form-control @error('user_status_id') is-invalid @enderror" required>
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
                        <label>วันที่สมัคร</label>
                        <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y H:i:s') }}" readonly disabled>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> หากต้องการเปลี่ยนรหัสผ่าน โปรดใช้ตัวเลือก "รีเซ็ตรหัสผ่าน" จากหน้าแสดงข้อมูลผู้ใช้
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
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
