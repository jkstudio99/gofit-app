@extends('layouts.app')

@section('title', 'โปรไฟล์ของฉัน')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Profile Image -->
                        <div class="col-12 col-md-4 text-center mb-4 mb-md-0">
                            <div class="mb-3 position-relative mx-auto profile-image-container">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset('profile_images/' . Auth::user()->profile_image) }}"
                                        alt="{{ Auth::user()->username }}"
                                        class="rounded-circle border shadow-sm profile-image">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border shadow-sm profile-placeholder">
                                        <i class="fas fa-user fa-3x text-secondary"></i>
                                    </div>
                                @endif
                            </div>

                            @if($user->user_type_id == 2)
                            <span class="badge bg-primary rounded-pill px-3 py-2 mb-2">
                                <i class="fas fa-user-shield me-1"></i> ผู้ดูแลระบบ
                            </span>
                            @endif
                        </div>

                        <!-- Profile Info -->
                        <div class="col-12 col-md-8">
                            <h4 class="fw-bold mb-1">{{ $user->firstname }} {{ $user->lastname }}</h4>
                            <p class="text-muted mb-3">
                                <i class="fas fa-at me-1"></i> {{ $user->username }}
                            </p>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <div>
                                            <div class="small text-muted">อีเมล</div>
                                            <div>{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <div>
                                            <div class="small text-muted">เบอร์โทรศัพท์</div>
                                            <div>{{ $user->telephone ?? 'ไม่ระบุ' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        <div>
                                            <div class="small text-muted">วันที่สมัคร</div>
                                            <div>{{ $user->created_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        <div>
                                            <div class="small text-muted">เข้าสู่ระบบล่าสุด</div>
                                            <div>{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'ไม่ระบุ' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                    <i class="fas fa-user-edit me-1"></i> แก้ไขข้อมูล
                                </a>
                                @if($user->user_type_id == 2)
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-tachometer-alt me-1"></i> แดชบอร์ด
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .profile-image-container {
        width: 120px;
        height: 120px;
    }

    .profile-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }

    .profile-placeholder {
        width: 120px;
        height: 120px;
    }

    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .badge.bg-primary {
        background-color: #2196F3 !important;
    }

    .border {
        border-color: rgba(0,0,0,0.08) !important;
    }

    /* Button styling - ensure icons and hover text are white */
    .btn-primary, .btn-primary:hover, .btn-primary:focus {
        color: #fff !important;
    }

    .btn-primary i, .btn-primary:hover i {
        color: #fff !important;
    }

    .btn-outline-primary:hover {
        color: #fff !important;
    }

    .btn-outline-primary:hover i {
        color: #fff !important;
    }
</style>
@endsection
