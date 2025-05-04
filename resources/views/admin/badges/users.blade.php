@extends('layouts.admin')

@section('title', 'ผู้ใช้ที่ได้รับเหรียญตรา - ' . $badge->badge_name)

@section('styles')
<style>
    .badge-img {
        max-height: 100px;
        object-fit: contain;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }

    .badge-banner {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
    }

    /* Design system styles */
    .btn-primary, .bg-primary {
        background-color: #2DC679 !important;
        border-color: #2DC679 !important;
    }

    .btn-primary:hover {
        background-color: #24A664 !important;
        border-color: #24A664 !important;
    }

    .badge.bg-info {
        background-color: #3B82F6 !important;
        color: white !important;
    }

    .btn-info.badge-action-btn {
        background-color: #3B82F6 !important;
        border-color: #3B82F6 !important;
    }

    .btn-info.badge-action-btn:hover {
        background-color: #2563EB !important;
        border-color: #2563EB !important;
    }

    .btn-danger i, .btn-danger.badge-action-btn i {
        color: white !important;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">ผู้ใช้ที่ได้รับเหรียญตรา</h1>
        <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>กลับไปยังรายการเหรียญตรา
        </a>
    </div>

    <!-- Badge Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-2 col-md-3 text-center mb-3 mb-md-0">
                    @if($badge->badge_image)
                        <img src="{{ asset('storage/' . $badge->badge_image) }}" alt="{{ $badge->badge_name }}" class="badge-img">
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-medal fa-4x"></i>
                        </div>
                    @endif
                </div>
                <div class="col-lg-10 col-md-9">
                    <h2 class="h4 mb-3">{{ $badge->badge_name }}</h2>

                    <div class="mb-3">
                        @if($badge->type == 'distance')
                            <span class="badge bg-info text-white">
                                <i class="fas fa-route me-1"></i> ระยะทาง
                            </span>
                        @elseif($badge->type == 'calories')
                            <span class="badge bg-danger">
                                <i class="fas fa-fire-alt me-1"></i> แคลอรี่
                            </span>
                        @elseif($badge->type == 'streak')
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-calendar-check me-1"></i> ต่อเนื่อง
                            </span>
                        @elseif($badge->type == 'speed')
                            <span class="badge bg-success">
                                <i class="fas fa-tachometer-alt me-1"></i> ความเร็ว
                            </span>
                        @elseif($badge->type == 'event')
                            <span class="badge bg-primary">
                                <i class="fas fa-calendar-day me-1"></i> กิจกรรม
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-medal me-1"></i> {{ $badge->type }}
                            </span>
                        @endif
                    </div>

                    <p>{{ $badge->badge_description }}</p>

                    <div class="mt-3">
                        <strong>เงื่อนไขการได้รับ:</strong>
                        <span class="badge bg-light text-dark px-3 py-2 mt-1">{{ $badge->getRequirementText() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">
                    <i class="fas fa-users me-2 text-primary"></i>ผู้ใช้ที่ได้รับเหรียญตรานี้
                </h5>
                <span class="badge bg-info text-white rounded-pill">
                    <i class="fas fa-user-check me-1"></i> ทั้งหมด: {{ $users->total() }} คน
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-users fa-4x"></i>
                    </div>
                    <h5>ยังไม่มีผู้ใช้ได้รับเหรียญตรานี้</h5>
                    <p class="text-muted">ยังไม่มีผู้ใช้คนใดที่บรรลุเงื่อนไขการได้รับเหรียญตรานี้</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px" class="text-center">#</th>
                                <th>ข้อมูลผู้ใช้</th>
                                <th>วันที่ได้รับ</th>
                                <th style="width: 100px" class="text-center">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $key => $user)
                            <tr>
                                <td class="text-center">{{ $users->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @if($user->profile_image)
                                                <img src="{{ asset('profile_images/' . $user->profile_image) }}" class="rounded-circle user-avatar" alt="Profile">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white user-avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-semibold">{{ $user->username }}</h6>
                                            <div class="text-muted small">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($user->pivot->earned_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info badge-action-btn text-white" title="ดูข้อมูลผู้ใช้">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
