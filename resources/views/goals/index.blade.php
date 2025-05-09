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

    /* Responsive fixes */
    @media (max-width: 991.98px) {
        .goals-container {
            padding-left: 15px;
            padding-right: 15px;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-container {
            padding-left: 15px;
            padding-right: 15px;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .page-header .create-goal-btn {
            margin-top: 15px;
            align-self: flex-start;
        }

        .goals-tab-content {
            padding-left: 0;
            padding-right: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="dashboard-container">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1>เป้าหมายการออกกำลังกาย</h1>
                <p>ตั้งเป้าหมายและติดตามความคืบหน้าของคุณ</p>
            </div>
            <a href="{{ route('goals.create') }}" class="btn btn-primary create-goal-btn">
                <i class="fas fa-plus me-2"></i> สร้างเป้าหมายใหม่
            </a>
        </div>

        <!-- ส่วนของ tabs -->
        <ul class="nav nav-tabs mb-4" id="goalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-goals" type="button" role="tab" aria-controls="active-goals" aria-selected="true">
                    <i class="fas fa-bolt me-1"></i> เป้าหมายที่กำลังดำเนินการ
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-goals" type="button" role="tab" aria-controls="completed-goals" aria-selected="false">
                    <i class="fas fa-check-circle me-1"></i> เป้าหมายที่สำเร็จแล้ว
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired-goals" type="button" role="tab" aria-controls="expired-goals" aria-selected="false">
                    <i class="fas fa-calendar-times me-1"></i> เป้าหมายที่หมดอายุ
                </button>
            </li>
        </ul>

        <!-- ส่วนของเนื้อหา tabs -->
        <div class="tab-content goals-tab-content" id="goalTabsContent">
            <!-- เป้าหมายที่กำลังดำเนินการ -->
            <div class="tab-pane fade show active" id="active-goals" role="tabpanel" aria-labelledby="active-tab">
                @if($activeGoals->count() > 0)
                    <div class="row goals-container">
                        @foreach($activeGoals as $goal)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card goal-card">
                                    @if($goal->isCompleted())
                                        <div class="completed-badge">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    @endif

                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">{{ $goal->getTypeLabel() }}</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="goal-icon">
                                                @if($goal->type == 'distance')
                                                    <i class="fas fa-road"></i>
                                                @elseif($goal->type == 'duration')
                                                    <i class="fas fa-clock"></i>
                                                @elseif($goal->type == 'calories')
                                                    <i class="fas fa-fire"></i>
                                                @elseif($goal->type == 'frequency')
                                                    <i class="fas fa-redo"></i>
                                                @endif
                                            </div>
                                            <h4 class="mb-2">{{ $goal->target_value }} {{ $goal->getUnitLabel() }}</h4>
                                            <p class="text-muted mb-3">{{ $goal->getActivityTypeLabel() }}</p>
                                        </div>

                                        <div class="progress-container mb-4">
                                            <div class="progress-circle">
                                                <svg width="120" height="120" viewBox="0 0 120 120">
                                                    <circle class="progress-bar bg-light" cx="60" cy="60" r="50" />
                                                    <circle class="progress-bar progress-bar-fill" cx="60" cy="60" r="50" style="--percent: {{ $goal->getProgressPercentage() }}" />
                                                </svg>
                                                <div class="progress-value">{{ $goal->getProgressPercentage() }}%</div>
                                            </div>
                                            <p class="text-center mb-0">
                                                <span class="fw-bold">{{ $goal->getCurrentValue() }}</span> จากเป้าหมาย <span class="fw-bold">{{ $goal->target_value }}</span> {{ $goal->getUnitLabel() }}
                                            </p>
                                        </div>

                                        <div class="goal-dates mb-3">
                                            <div class="goal-date">
                                                <i class="fas fa-calendar-alt"></i> ช่วงเวลา: {{ $goal->getPeriodLabel() }}
                                            </div>
                                            <div class="goal-date">
                                                <i class="fas fa-play"></i> เริ่ม: {{ $goal->start_date->format('d/m/Y') }}
                                            </div>
                                            @if($goal->end_date)
                                                <div class="goal-date">
                                                    <i class="fas fa-flag-checkered"></i> สิ้นสุด: {{ $goal->end_date->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="goal-actions d-flex justify-content-between mt-3">
                                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                            </a>
                                            <div>
                                                <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline-secondary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="delete-goal-form d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-goal-btn" data-goal-id="{{ $goal->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="mb-4">
                            <i class="fas fa-bullseye fa-4x text-muted"></i>
                        </div>
                        <h3 class="mb-3">ยังไม่มีเป้าหมายที่กำลังดำเนินการ</h3>
                        <p class="text-muted mb-4">เริ่มสร้างเป้าหมายใหม่เพื่อติดตามความคืบหน้าการออกกำลังกายของคุณ</p>
                        <a href="{{ route('goals.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> สร้างเป้าหมายใหม่
                        </a>
                    </div>
                @endif
            </div>

            <!-- เป้าหมายที่สำเร็จแล้ว -->
            <div class="tab-pane fade" id="completed-goals" role="tabpanel" aria-labelledby="completed-tab">
                @if($completedGoals->count() > 0)
                    <div class="row goals-container">
                        @foreach($completedGoals as $goal)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card goal-card">
                                    <div class="completed-badge">
                                        <i class="fas fa-check"></i>
                                    </div>

                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">{{ $goal->getTypeLabel() }}</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="goal-icon">
                                                @if($goal->type == 'distance')
                                                    <i class="fas fa-road"></i>
                                                @elseif($goal->type == 'duration')
                                                    <i class="fas fa-clock"></i>
                                                @elseif($goal->type == 'calories')
                                                    <i class="fas fa-fire"></i>
                                                @elseif($goal->type == 'frequency')
                                                    <i class="fas fa-redo"></i>
                                                @endif
                                            </div>
                                            <h4 class="mb-2">{{ $goal->target_value }} {{ $goal->getUnitLabel() }}</h4>
                                            <p class="text-muted mb-3">{{ $goal->getActivityTypeLabel() }}</p>
                                        </div>

                                        <div class="progress-container mb-4">
                                            <div class="progress-circle">
                                                <svg width="120" height="120" viewBox="0 0 120 120">
                                                    <circle class="progress-bar bg-light" cx="60" cy="60" r="50" />
                                                    <circle class="progress-bar progress-bar-fill" cx="60" cy="60" r="50" style="--percent: 100" />
                                                </svg>
                                                <div class="progress-value">100%</div>
                                            </div>
                                            <p class="text-center mb-0">
                                                <span class="fw-bold">{{ $goal->getCurrentValue() }}</span> จากเป้าหมาย <span class="fw-bold">{{ $goal->target_value }}</span> {{ $goal->getUnitLabel() }}
                                            </p>
                                        </div>

                                        <div class="goal-dates mb-3">
                                            <div class="goal-date">
                                                <i class="fas fa-calendar-alt"></i> ช่วงเวลา: {{ $goal->getPeriodLabel() }}
                                            </div>
                                            <div class="goal-date">
                                                <i class="fas fa-play"></i> เริ่ม: {{ $goal->start_date->format('d/m/Y') }}
                                            </div>
                                            @if($goal->completed_at)
                                                <div class="goal-date">
                                                    <i class="fas fa-trophy text-success"></i> สำเร็จเมื่อ: {{ $goal->completed_at->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="goal-actions d-flex justify-content-between mt-3">
                                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                            </a>
                                            <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="delete-goal-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-goal-btn" data-goal-id="{{ $goal->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="mb-4">
                            <i class="fas fa-trophy fa-4x text-muted"></i>
                        </div>
                        <h3 class="mb-3">ยังไม่มีเป้าหมายที่สำเร็จ</h3>
                        <p class="text-muted">เป้าหมายที่คุณบรรลุแล้วจะแสดงที่นี่</p>
                    </div>
                @endif
            </div>

            <!-- เป้าหมายที่หมดอายุ -->
            <div class="tab-pane fade" id="expired-goals" role="tabpanel" aria-labelledby="expired-tab">
                @if($expiredGoals->count() > 0)
                    <div class="row goals-container">
                        @foreach($expiredGoals as $goal)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card goal-card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">{{ $goal->getTypeLabel() }}</h5>
                                            <span class="badge bg-danger">หมดอายุ</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="goal-icon text-muted">
                                                @if($goal->type == 'distance')
                                                    <i class="fas fa-road"></i>
                                                @elseif($goal->type == 'duration')
                                                    <i class="fas fa-clock"></i>
                                                @elseif($goal->type == 'calories')
                                                    <i class="fas fa-fire"></i>
                                                @elseif($goal->type == 'frequency')
                                                    <i class="fas fa-redo"></i>
                                                @endif
                                            </div>
                                            <h4 class="mb-2">{{ $goal->target_value }} {{ $goal->getUnitLabel() }}</h4>
                                            <p class="text-muted mb-3">{{ $goal->getActivityTypeLabel() }}</p>
                                        </div>

                                        <div class="progress-container mb-4">
                                            <div class="progress-circle">
                                                <svg width="120" height="120" viewBox="0 0 120 120">
                                                    <circle class="progress-bar bg-light" cx="60" cy="60" r="50" />
                                                    <circle class="progress-bar progress-bar-fill" cx="60" cy="60" r="50" style="--percent: {{ $goal->getProgressPercentage() }}" />
                                                </svg>
                                                <div class="progress-value">{{ $goal->getProgressPercentage() }}%</div>
                                            </div>
                                            <p class="text-center mb-0">
                                                <span class="fw-bold">{{ $goal->getCurrentValue() }}</span> จากเป้าหมาย <span class="fw-bold">{{ $goal->target_value }}</span> {{ $goal->getUnitLabel() }}
                                            </p>
                                        </div>

                                        <div class="goal-dates mb-3">
                                            <div class="goal-date">
                                                <i class="fas fa-calendar-alt"></i> ช่วงเวลา: {{ $goal->getPeriodLabel() }}
                                            </div>
                                            <div class="goal-date">
                                                <i class="fas fa-play"></i> เริ่ม: {{ $goal->start_date->format('d/m/Y') }}
                                            </div>
                                            @if($goal->end_date)
                                                <div class="goal-date">
                                                    <i class="fas fa-flag-checkered text-danger"></i> สิ้นสุด: {{ $goal->end_date->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="goal-actions d-flex justify-content-between mt-3">
                                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                            </a>
                                            <div>
                                                <a href="{{ route('goals.create') }}" class="btn btn-sm btn-success me-1" title="สร้างเป้าหมายใหม่คล้ายกับเป้าหมายนี้">
                                                    <i class="fas fa-copy"></i>
                                                </a>
                                                <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="delete-goal-form d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-goal-btn" data-goal-id="{{ $goal->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="mb-4">
                            <i class="fas fa-calendar-times fa-4x text-muted"></i>
                        </div>
                        <h3 class="mb-3">ไม่มีเป้าหมายที่หมดอายุ</h3>
                        <p class="text-muted">เป้าหมายที่หมดกำหนดเวลาจะแสดงที่นี่</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ฟังก์ชันเมื่อคลิกปุ่มลบเป้าหมาย
    document.querySelectorAll('.delete-goal-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const goalId = this.getAttribute('data-goal-id');

            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณแน่ใจหรือไม่ว่าต้องการลบเป้าหมายนี้? การกระทำนี้ไม่สามารถย้อนกลับได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ลบเป้าหมาย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.querySelector(`.delete-goal-btn[data-goal-id="${goalId}"]`).closest('form');
                    form.submit();
                }
            });
        });
    });

    // แสดง tab ที่เลือกค้างไว้หลังจากรีเฟรชหน้า
    const url = new URL(window.location.href);
    const tab = url.searchParams.get('tab');

    if (tab) {
        const tabEl = document.querySelector(`#${tab}-tab`);
        if (tabEl) {
            new bootstrap.Tab(tabEl).show();
        }
    }
});
</script>
@endsection
