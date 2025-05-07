@extends('layouts.app')

@section('title', 'เป้าหมายของฉัน')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* Modern Dashboard Styling */
    .dashboard-container {
        padding: 1rem 0;
    }

    /* Goal card styles */
    .goal-card {
        border-radius: 16px;
        box-shadow: 0 6px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: none;
        height: 100%;
        overflow: hidden;
    }

    .goal-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.12);
    }

    .goal-card .card-header {
        background: linear-gradient(135deg, #2ecc71, #1abc9c);
        color: white;
        border-bottom: none;
        padding: 15px 20px;
    }

    .goal-card .card-body {
        padding: 20px;
    }

    .goal-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #2ecc71;
    }

    /* Progress circle */
    .progress-circle {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 20px;
        background: #f8f9fa;
    }

    .progress-circle::before {
        content: '';
        position: absolute;
        top: 10px;
        left: 10px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: white;
    }

    .progress-circle .progress-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
    }

    .progress-circle .progress-bar {
        fill: none;
        stroke-width: 10;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    .progress-circle .progress-bar.bg-light {
        stroke: #e9ecef;
    }

    .progress-circle .progress-bar.progress-bar-fill {
        stroke: #2ecc71;
        stroke-dasharray: 314;
        stroke-dashoffset: calc(314 - (314 * var(--percent)) / 100);
        transition: stroke-dashoffset 1s ease;
    }

    /* Navigation tabs */
    .nav-tabs .nav-link {
        font-weight: 500;
        color: #666;
        border: none;
        padding: 12px 20px;
        border-radius: 0;
        position: relative;
    }

    .nav-tabs .nav-link.active {
        color: #2ecc71;
        border: none;
        background: transparent;
    }

    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        border-radius: 3px 3px 0 0;
        background: #2ecc71;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f9f9f9;
        border-radius: 16px;
    }

    /* Goal badges */
    .goal-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
    }

    .completed-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 45px;
        height: 45px;
        background: #2ecc71;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 3px 10px rgba(46, 204, 113, 0.4);
    }

    /* Goal metrics */
    .goal-metric {
        text-align: center;
        margin-bottom: 15px;
    }

    .goal-metric .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1;
        color: #2ecc71;
    }

    .goal-metric .metric-label {
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
    }

    /* Goal summary */
    .goal-summary {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px 15px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .goal-summary-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .goal-summary-icon.distance {
        background: rgba(46, 204, 113, 0.1);
        color: #2ecc71;
    }

    /* Header */
    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .page-header p {
        color: #6c757d;
        margin-bottom: 0;
    }

    /* Create goal button */
    .create-goal-btn {
        border-radius: 30px;
        padding: 10px 25px;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(46, 204, 113, 0.25);
    }

    /* Delete goal form */
    .delete-goal-form {
        display: inline;
    }

    /* Goal date */
    .goal-date {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .goal-date i {
        width: 20px;
        text-align: center;
        margin-right: 5px;
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>เป้าหมายการออกกำลังกาย</h1>
            <p>ตั้งเป้าหมายและติดตามความคืบหน้าของคุณ</p>
        </div>
        <a href="{{ route('goals.create') }}" class="btn btn-primary create-goal-btn">
            <i class="fas fa-plus me-2"></i> สร้างเป้าหมายใหม่
        </a>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" id="active-tab" data-bs-toggle="tab" href="#active" role="tab">
                <i class="fas fa-chart-line me-2"></i> เป้าหมายที่กำลังดำเนินการ
                @if($activeGoals->count() > 0)
                    <span class="badge bg-primary ms-1">{{ $activeGoals->count() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab">
                <i class="fas fa-check-circle me-2"></i> เป้าหมายที่สำเร็จแล้ว
                @if($completedGoals->count() > 0)
                    <span class="badge bg-success ms-1">{{ $completedGoals->count() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="expired-tab" data-bs-toggle="tab" href="#expired" role="tab">
                <i class="fas fa-calendar-times me-2"></i> เป้าหมายที่หมดเวลา
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
                <div class="empty-state">
                    <div class="goal-icon">
                    <i class="fas fa-bullseye fa-4x text-muted mb-3"></i>
                    </div>
                    <h4>ยังไม่มีเป้าหมายที่กำลังดำเนินการ</h4>
                    <p class="text-muted mb-4">เริ่มตั้งเป้าหมายการออกกำลังกายเพื่อติดตามความคืบหน้าของคุณ</p>
                    <a href="{{ route('goals.create') }}" class="btn btn-primary btn-lg px-5 create-goal-btn">
                        <i class="fas fa-plus me-2"></i> ตั้งเป้าหมายแรกของคุณ
                    </a>
                </div>
            @else
                <div class="row">
                    @foreach($activeGoals as $goal)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="goal-card card h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $goal->formattedType }}</h5>
                                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                        @if($goal->period == 'daily')
                                            รายวัน
                                        @elseif($goal->period == 'weekly')
                                            รายสัปดาห์
                                        @elseif($goal->period == 'monthly')
                                            รายเดือน
                                        @else
                                            กำหนดเอง
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="progress-circle" style="--percent: {{ $goal->progressPercentage }}">
                                        <svg width="120" height="120" viewBox="0 0 120 120">
                                            <circle class="progress-bar bg-light" cx="60" cy="60" r="50"></circle>
                                            <circle class="progress-bar progress-bar-fill" cx="60" cy="60" r="50"></circle>
                                        </svg>
                                        <div class="progress-value">{{ $goal->progressPercentage }}%</div>
                                    </div>
                                    <div class="fw-bold">{{ $goal->current_value }}/{{ $goal->target_value }} {{ $goal->type == 'distance' ? 'กม.' : '' }}</div>
                                </div>

                                @if($goal->activity_type)
                                <div class="goal-summary">
                                    <div class="goal-summary-icon distance">
                                        <i class="fas fa-running"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">ประเภทกิจกรรม</div>
                                        <div class="small">{{ $goal->formattedActivityType }}</div>
                                    </div>
                                </div>
                                @endif

                                <div class="goal-date mb-1">
                                    <i class="fas fa-calendar-day text-primary"></i>
                                    <span>เริ่ม: {{ \Carbon\Carbon::parse($goal->start_date)->locale('th')->translatedFormat('d M') }} {{ intval(\Carbon\Carbon::parse($goal->start_date)->format('Y')) + 543 - 2500 }}</span>
                                </div>

                                    @if($goal->end_date)
                                <div class="goal-date mb-3">
                                    <i class="fas fa-hourglass-half text-warning"></i>
                                    <span>สิ้นสุด: {{ \Carbon\Carbon::parse($goal->end_date)->locale('th')->translatedFormat('d M') }} {{ intval(\Carbon\Carbon::parse($goal->end_date)->format('Y')) + 543 - 2500 }}</span>
                                </div>
                                @endif

                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('goals.show', $goal) }}" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-eye me-1"></i> รายละเอียด
                                    </a>
                                    <a href="{{ route('goals.edit', $goal) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('goals.destroy', $goal->id) }}" method="POST" class="delete-goal-form">
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

        <!-- เป้าหมายที่สำเร็จแล้ว -->
        <div class="tab-pane fade" id="completed" role="tabpanel">
            @if($completedGoals->isEmpty())
                <div class="empty-state">
                    <div class="goal-icon">
                    <i class="fas fa-trophy fa-4x text-muted mb-3"></i>
                    </div>
                    <h4>ยังไม่มีเป้าหมายที่สำเร็จแล้ว</h4>
                    <p class="text-muted">ความสำเร็จของคุณจะแสดงที่นี่ ตั้งเป้าหมายและบรรลุผลสำเร็จ!</p>
                </div>
            @else
                <div class="row">
                    @foreach($completedGoals as $goal)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="goal-card card h-100">
                            <div class="completed-badge">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $goal->formattedType }}</h5>
                                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                        @if($goal->period == 'daily')
                                            รายวัน
                                        @elseif($goal->period == 'weekly')
                                            รายสัปดาห์
                                        @elseif($goal->period == 'monthly')
                                            รายเดือน
                                        @else
                                            กำหนดเอง
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="progress-circle" style="--percent: 100">
                                        <svg width="120" height="120" viewBox="0 0 120 120">
                                            <circle class="progress-bar bg-light" cx="60" cy="60" r="50"></circle>
                                            <circle class="progress-bar progress-bar-fill" cx="60" cy="60" r="50" style="stroke: #27ae60;"></circle>
                                        </svg>
                                        <div class="progress-value">100%</div>
                                    </div>
                                    <div class="fw-bold">{{ $goal->target_value }} {{ $goal->type == 'distance' ? 'กม.' : '' }}</div>
                                </div>

                                @if($goal->activity_type)
                                <div class="goal-summary">
                                    <div class="goal-summary-icon distance">
                                        <i class="fas fa-running"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">ประเภทกิจกรรม</div>
                                        <div class="small">{{ $goal->formattedActivityType }}</div>
                                    </div>
                                </div>
                                @endif

                                <div class="goal-date mb-1">
                                    <i class="fas fa-calendar-day text-success"></i>
                                    <span>เริ่ม: {{ \Carbon\Carbon::parse($goal->start_date)->locale('th')->translatedFormat('d M') }} {{ intval(\Carbon\Carbon::parse($goal->start_date)->format('Y')) + 543 - 2500 }}</span>
                                </div>

                                <div class="goal-date mb-3">
                                    <i class="fas fa-flag-checkered text-success"></i>
                                    <span>สำเร็จเมื่อ: {{ \Carbon\Carbon::parse($goal->updated_at)->locale('th')->translatedFormat('d M') }} {{ intval(\Carbon\Carbon::parse($goal->updated_at)->format('Y')) + 543 - 2500 }}</span>
                                </div>

                                <a href="{{ route('goals.show', $goal) }}" class="btn btn-success w-100">
                                    <i class="fas fa-trophy me-1"></i> ดูความสำเร็จ
                                </a>
                                <div class="d-flex gap-2 mt-2">
                                    <form action="{{ route('goals.destroy', $goal->id) }}" method="POST" class="delete-goal-form d-grid">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash me-1"></i> ลบเป้าหมาย
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

        <!-- เป้าหมายที่หมดเวลา -->
        <div class="tab-pane fade" id="expired" role="tabpanel">
            @if($expiredGoals->isEmpty())
                <div class="empty-state">
                    <div class="goal-icon">
                    <i class="fas fa-hourglass-end fa-4x text-muted mb-3"></i>
                    </div>
                    <h4>ไม่มีเป้าหมายที่หมดเวลา</h4>
                    <p class="text-muted">ดีมาก! คุณไม่มีเป้าหมายที่หมดเวลาโดยไม่สำเร็จ</p>
                </div>
            @else
                <div class="row">
                    @foreach($expiredGoals as $goal)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="goal-card card h-100" style="opacity: 0.8;">
                            <div class="card-header bg-secondary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $goal->formattedType }}</h5>
                                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                        @if($goal->period == 'daily')
                                            รายวัน
                                        @elseif($goal->period == 'weekly')
                                            รายสัปดาห์
                                        @elseif($goal->period == 'monthly')
                                            รายเดือน
                                        @else
                                            กำหนดเอง
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="progress-circle" style="--percent: {{ $goal->progressPercentage }}">
                                        <svg width="120" height="120" viewBox="0 0 120 120">
                                            <circle class="progress-bar bg-light" cx="60" cy="60" r="50"></circle>
                                            <circle class="progress-bar progress-bar-fill" cx="60" cy="60" r="50" style="stroke: #6c757d;"></circle>
                                        </svg>
                                        <div class="progress-value">{{ $goal->progressPercentage }}%</div>
                                    </div>
                                    <div class="fw-bold">{{ $goal->current_value }}/{{ $goal->target_value }} {{ $goal->type == 'distance' ? 'กม.' : '' }}</div>
                                </div>

                                @if($goal->activity_type)
                                <div class="goal-summary">
                                    <div class="goal-summary-icon distance" style="background: rgba(108, 117, 125, 0.1); color: #6c757d;">
                                        <i class="fas fa-running"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">ประเภทกิจกรรม</div>
                                        <div class="small">{{ $goal->formattedActivityType }}</div>
                                    </div>
                                </div>
                                @endif

                                <div class="goal-date mb-1">
                                    <i class="fas fa-calendar-day text-secondary"></i>
                                    <span>เริ่ม: {{ \Carbon\Carbon::parse($goal->start_date)->locale('th')->translatedFormat('d M') }} {{ intval(\Carbon\Carbon::parse($goal->start_date)->format('Y')) + 543 - 2500 }}</span>
                                </div>

                                <div class="goal-date mb-3">
                                    <i class="fas fa-calendar-times text-danger"></i>
                                    <span>หมดเวลา: {{ \Carbon\Carbon::parse($goal->end_date)->locale('th')->translatedFormat('d M') }} {{ intval(\Carbon\Carbon::parse($goal->end_date)->format('Y')) + 543 - 2500 }}</span>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('goals.show', $goal) }}" class="btn btn-secondary flex-grow-1">
                                        <i class="fas fa-eye me-1"></i> รายละเอียด
                                    </a>
                                    <form action="{{ route('goals.destroy', $goal->id) }}" method="POST" class="delete-goal-form">
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // แสดง SweetAlert2 สำหรับข้อความแจ้งเตือน
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#28a745'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '{{ session('error') }}',
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#dc3545'
            });
        @endif

        // Animate the progress circles when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.strokeDashoffset = `calc(314 - (314 * ${entry.target.parentElement.parentElement.style.getPropertyValue('--percent')}) / 100)`;
                    }, 100);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.progress-bar-fill').forEach(circle => {
            observer.observe(circle);
        });

        // Delete confirmation with SweetAlert2
        document.querySelectorAll('.delete-goal-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formEl = this;

                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: 'คุณต้องการลบเป้าหมายนี้ใช่หรือไม่?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ลบเป้าหมาย',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formEl.submit();
    }
                });
            });
        });
    });
</script>
@endsection
