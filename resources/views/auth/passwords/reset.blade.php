@extends('layouts.auth')

@section('content')
<div class="auth-page">
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-logo">
                <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Logo" style="height: 2.5rem;">
            </div>

            <h1 class="auth-title">{{ __('รีเซ็ตรหัสผ่าน') }}</h1>
            <p class="auth-subtitle">กรุณากำหนดรหัสผ่านใหม่สำหรับบัญชีของคุณ</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label for="email" class="form-label fw-medium">{{ __('อีเมล') }}</label>
                    <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-medium">{{ __('รหัสผ่านใหม่') }}</label>
                    <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="รหัสผ่านอย่างน้อย 8 ตัวอักษร">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label fw-medium">{{ __('ยืนยันรหัสผ่านใหม่') }}</label>
                    <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password" placeholder="กรอกรหัสผ่านใหม่อีกครั้ง">
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-gofit w-100 py-3 fw-medium">
                        {{ __('บันทึกรหัสผ่านใหม่') }}
                    </button>
                </div>

                <div class="text-center">
                    <p class="mb-0"><a href="{{ route('login') }}" style="color: var(--color-primary);" class="text-decoration-none fw-medium">{{ __('กลับไปหน้าเข้าสู่ระบบ') }}</a></p>
                </div>
            </form>
        </div>
    </div>

    <div class="auth-image-side">
        <div class="auth-image-shape shape-1"></div>
        <div class="auth-image-shape shape-2"></div>
        <div class="auth-image-content">
            <img src="{{ asset('images/login-cover-right.png') }}" alt="GoFit Dashboard" class="img-fluid">
        </div>
    </div>
</div>
@endsection
