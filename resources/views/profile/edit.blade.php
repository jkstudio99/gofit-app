@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลส่วนตัว')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card gofit-card">
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

                        <div class="row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-end">ชื่อผู้ใช้</label>
                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" value="{{ $user->username }}" disabled readonly>
                                <small class="text-muted">ชื่อผู้ใช้ไม่สามารถเปลี่ยนแปลงได้</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="firstname" class="col-md-4 col-form-label text-md-end">ชื่อ</label>
                            <div class="col-md-6">
                                <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname', $user->firstname) }}" required autocomplete="firstname">
                                @error('firstname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="lastname" class="col-md-4 col-form-label text-md-end">นามสกุล</label>
                            <div class="col-md-6">
                                <input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname', $user->lastname) }}" required autocomplete="lastname">
                                @error('lastname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">อีเมล</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="telephone" class="col-md-4 col-form-label text-md-end">เบอร์โทรศัพท์</label>
                            <div class="col-md-6">
                                <input id="telephone" type="text" class="form-control @error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone', $user->telephone) }}" autocomplete="telephone">
                                @error('telephone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card gofit-card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lock me-2"></i>
                        เปลี่ยนรหัสผ่าน
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">หากคุณต้องการเปลี่ยนรหัสผ่าน สามารถทำได้ที่นี่</p>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-key me-1"></i> เปลี่ยนรหัสผ่าน
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
