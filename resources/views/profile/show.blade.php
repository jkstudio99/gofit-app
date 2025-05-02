@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ asset('storage/' . ($user->profile_image ?? 'profile/default.jpg')) }}"
                             alt="{{ $user->name }}"
                             class="rounded-circle me-3"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <h3 class="mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-0">
                                <i class="fas fa-user me-1"></i> {{ '@' . $user->username }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle text-primary me-2"></i> ข้อมูลทั่วไป
                            </h5>
                            <div class="mb-3">
                                <p class="text-muted mb-1">อีเมล</p>
                                <p>{{ $user->email }}</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-1">วันที่สมัคร</p>
                                <p>{{ $user->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-running text-primary me-2"></i> สถิติการออกกำลังกาย
                            </h5>
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="fw-bold text-primary">{{ $user->activities_count ?? 0 }}</h4>
                                    <p class="small text-muted">กิจกรรม</p>
                                </div>
                                <div class="col-4">
                                    <h4 class="fw-bold text-primary">{{ $user->total_distance ?? 0 }} กม.</h4>
                                    <p class="small text-muted">ระยะทาง</p>
                                </div>
                                <div class="col-4">
                                    <h4 class="fw-bold text-primary">{{ $user->fitness_score ?? 0 }}</h4>
                                    <p class="small text-muted">คะแนน</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-medal text-primary me-2"></i> เหรียญตรา
                        </h5>
                        <div class="row">
                            @forelse ($user->badges ?? [] as $badge)
                                <div class="col-md-3 col-6 mb-3 text-center">
                                    <img src="{{ asset('storage/' . $badge->image) }}"
                                         alt="{{ $badge->name }}"
                                         class="img-fluid mb-2"
                                         style="max-height: 80px;">
                                    <p class="small mb-0">{{ $badge->name }}</p>
                                </div>
                            @empty
                                <div class="col-12 text-center py-3">
                                    <p class="text-muted mb-0">ยังไม่มีเหรียญตรา</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-history text-primary me-2"></i> กิจกรรมล่าสุด
                        </h5>
                        @if (isset($user->recent_activities) && $user->recent_activities->count() > 0)
                            <div class="list-group">
                                @foreach($user->recent_activities ?? [] as $activity)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ ucfirst($activity->activity_type) }}</h6>
                                                <p class="text-muted small mb-0">
                                                    {{ $activity->started_at->format('d M Y H:i') }}
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold">{{ $activity->distance ?? 0 }} กม.</div>
                                                <div class="small text-muted">{{ $activity->duration ?? 0 }} นาที</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">ยังไม่มีกิจกรรมล่าสุด</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
