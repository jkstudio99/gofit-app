@extends('layouts.app')

@section('title', 'เป้าหมายของฉัน')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">เป้าหมายของฉัน</h2>
            <p class="text-muted">ตั้งเป้าหมายการออกกำลังกายและติดตามความคืบหน้า</p>
        </div>
        <a href="{{ route('goals.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> ตั้งเป้าหมายใหม่
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" id="active-tab" data-bs-toggle="tab" href="#active" role="tab">
                <i class="fas fa-chart-line me-1"></i> เป้าหมายที่กำลังดำเนินการ
                @if($activeGoals->count() > 0)
                    <span class="badge bg-primary ms-1">{{ $activeGoals->count() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab">
                <i class="fas fa-check-circle me-1"></i> เป้าหมายที่สำเร็จแล้ว
                @if($completedGoals->count() > 0)
                    <span class="badge bg-success ms-1">{{ $completedGoals->count() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="expired-tab" data-bs-toggle="tab" href="#expired" role="tab">
                <i class="fas fa-calendar-times me-1"></i> เป้าหมายที่หมดเวลา
                @if($expiredGoals->count() > 0)
                    <span class="badge bg-secondary ms-1">{{ $expiredGoals->count() }}</span>
                @endif
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- เป้าหมายที่กำลังดำเนินการ -->
        <div class="tab-pane fade show active" id="active" role="tabpanel">
            @if($activeGoals->isEmpty())
                <div class="text-center py-5 bg-light rounded">
                    <i class="fas fa-bullseye fa-4x text-muted mb-3"></i>
                    <h4>ยังไม่มีเป้าหมายที่กำลังดำเนินการ</h4>
                    <p class="text-muted">เริ่มตั้งเป้าหมายการออกกำลังกายเพื่อติดตามความคืบหน้าของคุณ</p>
                    <a href="{{ route('goals.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> ตั้งเป้าหมายแรกของคุณ
                    </a>
                </div>
            @else
                <div class="row">
                    @foreach($activeGoals as $goal)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm hover-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title mb-0">{{ $goal->formattedType }}</h5>
                                    <span class="badge bg-primary">{{ $goal->period }}</span>
                                </div>

                                @if($goal->activity_type)
                                <div class="mb-3 small">
                                    <i class="fas fa-running me-1 text-primary"></i>
                                    {{ $goal->formattedActivityType }}
                                </div>
                                @endif

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center small mb-1">
                                        <span>ความคืบหน้า</span>
                                        <span class="fw-medium">{{ $goal->current_value }}/{{ $goal->target_value }}</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $goal->progressPercentage }}%;"
                                            aria-valuenow="{{ $goal->progressPercentage }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="text-end small mt-1">{{ $goal->progressPercentage }}%</div>
                                </div>

                                <div class="small mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                        <span>เริ่ม: {{ $goal->start_date->format('d M Y') }}</span>
                                    </div>
                                    @if($goal->end_date)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-hourglass-end me-2 text-primary"></i>
                                        <span>สิ้นสุด: {{ $goal->end_date->format('d M Y') }}</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="d-flex gap-2 mt-auto">
                                    <a href="{{ route('goals.show', $goal) }}" class="btn btn-outline-primary flex-grow-1">
                                        <i class="fas fa-eye me-1"></i> รายละเอียด
                                    </a>
                                    <a href="{{ route('goals.edit', $goal) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- เป้าหมายที่สำเร็จแล้ว -->
        <div class="tab-pane fade" id="completed" role="tabpanel">
            @if($completedGoals->isEmpty())
                <div class="text-center py-5 bg-light rounded">
                    <i class="fas fa-trophy fa-4x text-muted mb-3"></i>
                    <h4>ยังไม่มีเป้าหมายที่สำเร็จ</h4>
                    <p class="text-muted">ความสำเร็จของคุณจะแสดงที่นี่ ตั้งเป้าหมายและบรรลุผลสำเร็จ!</p>
                </div>
            @else
                <div class="row">
                    @foreach($completedGoals as $goal)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm hover-card bg-success-subtle">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title mb-0">{{ $goal->formattedType }}</h5>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> สำเร็จ
                                    </span>
                                </div>

                                @if($goal->activity_type)
                                <div class="mb-3 small">
                                    <i class="fas fa-running me-1 text-success"></i>
                                    {{ $goal->formattedActivityType }}
                                </div>
                                @endif

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center small mb-1">
                                        <span>เป้าหมาย</span>
                                        <span class="fw-medium">{{ $goal->current_value }}/{{ $goal->target_value }}</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: 100%;"
                                            aria-valuenow="100"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <div class="small mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-calendar-alt me-2 text-success"></i>
                                        <span>เริ่ม: {{ $goal->start_date->format('d M Y') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-flag-checkered me-2 text-success"></i>
                                        <span>สำเร็จเมื่อ: {{ $goal->updated_at->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('goals.show', $goal) }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-eye me-1"></i> รายละเอียด
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- เป้าหมายที่หมดเวลา -->
        <div class="tab-pane fade" id="expired" role="tabpanel">
            @if($expiredGoals->isEmpty())
                <div class="text-center py-5 bg-light rounded">
                    <i class="fas fa-hourglass-end fa-4x text-muted mb-3"></i>
                    <h4>ไม่มีเป้าหมายที่หมดเวลา</h4>
                    <p class="text-muted">ดีมาก! คุณไม่มีเป้าหมายที่หมดเวลาโดยไม่สำเร็จ</p>
                </div>
            @else
                <div class="row">
                    @foreach($expiredGoals as $goal)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm hover-card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title mb-0">{{ $goal->formattedType }}</h5>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-calendar-times me-1"></i> หมดเวลา
                                    </span>
                                </div>

                                @if($goal->activity_type)
                                <div class="mb-3 small">
                                    <i class="fas fa-running me-1 text-secondary"></i>
                                    {{ $goal->formattedActivityType }}
                                </div>
                                @endif

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center small mb-1">
                                        <span>ความคืบหน้า</span>
                                        <span class="fw-medium">{{ $goal->current_value }}/{{ $goal->target_value }}</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-secondary" role="progressbar"
                                            style="width: {{ $goal->progressPercentage }}%;"
                                            aria-valuenow="{{ $goal->progressPercentage }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="text-end small mt-1">{{ $goal->progressPercentage }}%</div>
                                </div>

                                <div class="small mb-3 text-secondary">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <span>เริ่ม: {{ $goal->start_date->format('d M Y') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-times me-2"></i>
                                        <span>หมดเวลา: {{ $goal->end_date->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('goals.show', $goal) }}" class="btn btn-outline-secondary flex-grow-1">
                                        <i class="fas fa-eye me-1"></i> รายละเอียด
                                    </a>
                                    <form action="{{ route('goals.destroy', $goal) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบเป้าหมายนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
