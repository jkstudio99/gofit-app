@extends('layouts.auth')

@section('content')
<div class="auth-page">
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-logo">
                <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Logo" style="height: 2.5rem;">
            </div>

            <h1 class="auth-title">{{ __('ยืนยันอีเมลของคุณ') }}</h1>
            <p class="auth-subtitle">เราได้ส่งลิงก์ยืนยันไปยังอีเมลของคุณแล้ว</p>

            @if (session('resent'))
                <div class="alert alert-success mb-4" role="alert" style="background-color: var(--color-primary-lighter); color: var(--color-primary-darker); border: none; border-radius: var(--radius-md);">
                    <i class="fas fa-check-circle me-2"></i>{{ __('ลิงก์ยืนยันใหม่ได้ถูกส่งไปยังอีเมลของคุณแล้ว') }}
                </div>
            @endif

            <div class="text-center mb-4 p-4" style="background-color: var(--color-background-alt); border-radius: var(--radius-md);">
                <i class="fas fa-envelope fa-3x mb-3" style="color: var(--color-primary);"></i>
                <p>{{ __('ก่อนดำเนินการต่อ กรุณาตรวจสอบอีเมลของคุณเพื่อคลิกลิงก์ยืนยัน') }}</p>
            </div>

            <div class="mb-4 text-center">
                <p>{{ __('หากคุณไม่ได้รับอีเมล') }}</p>

                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-gofit-outline">
                        <i class="fas fa-paper-plane me-2"></i>{{ __('ส่งลิงก์ยืนยันใหม่อีกครั้ง') }}
                    </button>
                </form>
            </div>

            <div class="text-center">
                <a href="{{ route('home') }}" class="text-decoration-none" style="color: var(--color-primary);">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('กลับสู่หน้าหลัก') }}
                </a>
            </div>
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
