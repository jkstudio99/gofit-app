@extends('layouts.auth')

@section('content')
<div class="auth-page">
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-logo">
                <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Logo" style="height: 2.5rem;">
            </div>

            <h1 class="auth-title">{{ __('ลืมรหัสผ่าน') }}</h1>
            <p class="auth-subtitle">กรอกชื่อผู้ใช้ของคุณเพื่อรับลิงก์สำหรับรีเซ็ตรหัสผ่าน</p>

            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert" style="background-color: var(--color-primary-lighter); color: var(--color-primary-darker); border: none; border-radius: var(--radius-md);">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label for="username" class="form-label fw-medium">{{ __('ชื่อผู้ใช้') }}</label>
                    <input id="username" type="text" class="form-control form-control-lg @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus placeholder="กรอกชื่อผู้ใช้ที่ใช้ลงทะเบียน">

                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-gofit w-100 py-3 fw-medium">
                        {{ __('ส่งลิงก์รีเซ็ตรหัสผ่าน') }}
                    </button>
                </div>

                <div class="text-center">
                    <p class="mb-0">{{ __('จำรหัสผ่านได้แล้ว?') }} <a href="{{ route('login') }}" style="color: var(--color-primary);" class="text-decoration-none fw-medium">{{ __('เข้าสู่ระบบเลย') }}</a></p>
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
