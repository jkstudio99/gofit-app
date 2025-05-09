@extends('layouts.app')

@section('title', $goal->getTypeLabel())

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .progress {
        height: 20px;
        border-radius: 10px;
    }

    .progress-bar {
        border-radius: 10px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 5px 12px rgba(0,0,0,0.05);
    }

    .delete-goal-form {
        display: inline;
    }

    .goal-card {
        border-radius: 15px;
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }

    .goal-card .card-header {
        background: linear-gradient(135deg, #2ecc71, #1abc9c);
        color: white;
        border-bottom: none;
        padding: 20px;
    }

    .goal-card .card-body {
        padding: 20px;
    }

    /* Progress circle */
    .progress-circle {
        position: relative;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        margin: 0 auto 20px;
        background: #f8f9fa;
    }

    .progress-circle::before {
        content: '';
        position: absolute;
        top: 10px;
        left: 10px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: white;
    }

    .progress-circle .progress-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
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

    /* Goal metrics */
    .goal-metric {
        text-align: center;
        margin-bottom: 15px;
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
    }

    .goal-metric .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1;
        color: #2ecc71;
    }

    .goal-metric .metric-label {
        font-size: 0.9rem;
        color: #666;
        margin-top: 5px;
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

    /* Progress history */
    .activity-item {
        padding: 12px 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }

    .activity-item:hover {
        background-color: #f0f0f0;
        transform: translateY(-2px);
    }

    .activity-date {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .activity-contribution {
        font-weight: 600;
        color: #2ecc71;
    }

    /* Responsive fixes */
    @media (max-width: 767.98px) {
        .py-4 {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        .container {
            padding-left: 0;
            padding-right: 0;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
            margin-bottom: 1rem;
        }

        .page-header .btn-group {
            margin-top: 1rem;
            align-self: flex-start;
        }

        .progress-circle {
            width: 140px;
            height: 140px;
        }

        .progress-circle::before {
            width: 120px;
            height: 120px;
        }

        .progress-circle .progress-value {
            font-size: 1.5rem;
        }

        .goal-metric .metric-value {
            font-size: 1.5rem;
        }

        .card-body {
            padding: 15px;
        }

        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .col-lg-8, .col-lg-4 {
            padding-left: 10px;
            padding-right: 10px;
        }

        /* Better activity item spacing */
        .activity-item {
            padding: 10px;
        }
    }

    /* Tablet responsiveness */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .py-4 {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        .goal-metric {
            padding: 10px;
        }

        /* Consistent spacing for tablet */
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="py-4">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">รายละเอียดเป้าหมาย</h1>
                <p class="text-muted mb-0">{{ $goal->getTypeLabel() }} - {{ $goal->target_value }} {{ $goal->getUnitLabel() }}</p>
            </div>
            <div class="btn-group  gap-2">
                <a href="{{ route('goals.edit', $goal) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> แก้ไข
                </a>
                <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Main Goal Info -->
            <div class="col-lg-8 mb-4">
                <div class="goal-card card position-relative">
                    @if($goal->isCompleted())
                        <div class="completed-badge">
                            <i class="fas fa-check"></i>
                        </div>
                    @endif

                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $goal->getTypeLabel() }}</h4>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                {{ $goal->getPeriodLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="progress-circle">
                                <svg width="160" height="160" viewBox="0 0 160 160">
                                    <circle class="progress-bar bg-light" cx="80" cy="80" r="70" />
                                    <circle class="progress-bar progress-bar-fill" cx="80" cy="80" r="70" style="--percent: {{ $goal->getProgressPercentage() }}" />
                                </svg>
                                <div class="progress-value">{{ $goal->getProgressPercentage() }}%</div>
                            </div>
                            <h3 class="mb-0">{{ $goal->getCurrentValue() }} / {{ $goal->target_value }} {{ $goal->getUnitLabel() }}</h3>
                            <p class="text-muted mt-2">{{ $goal->getActivityTypeLabel() }}</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="goal-metric">
                                    <div class="metric-value">{{ $goal->getProgressPercentage() }}%</div>
                                    <div class="metric-label">ความสำเร็จ</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="goal-metric">
                                    <div class="metric-value">{{ $goal->getRemaining() }}</div>
                                    <div class="metric-label">เหลืออีก {{ $goal->getUnitLabel() }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="goal-metric">
                                    <div class="metric-value">
                                        @if($goal->getRemainingDays() !== null)
                                            {{ $goal->getRemainingDays() }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="metric-label">วันที่เหลือ</div>
                                </div>
                            </div>
                        </div>

                        <div class="goal-dates mb-4">
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
                            @if($goal->completed_at)
                                <div class="goal-date">
                                    <i class="fas fa-trophy text-success"></i> สำเร็จเมื่อ: {{ $goal->completed_at->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge {{ $goal->getStatusBadgeClass() }} px-3 py-2 rounded-pill">
                                    {{ $goal->getStatusLabel() }}
                                </span>
                            </div>
                            @if(!$goal->isCompleted() && !$goal->isExpired())
                                <a href="{{ route('run.index') }}" class="btn btn-primary">
                                    <i class="fas fa-running me-1"></i> เริ่มวิ่ง
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress History -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>ความคืบหน้า</h5>
                    </div>
                    <div class="card-body">
                        @if($contributions->count() > 0)
                            <ul class="list-unstyled">
                                @foreach($contributions as $contribution)
                                    <li class="activity-item">
                                        <div class="activity-date">
                                            <i class="far fa-calendar-alt me-1"></i> {{ $contribution->created_at->format('d M Y, H:i') }}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ $contribution->activity_type_label }}</span>
                                            <span class="activity-contribution">+ {{ $contribution->contribution_value }} {{ $goal->getUnitLabel() }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            @if($contributions->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $contributions->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p>ยังไม่มีความคืบหน้าสำหรับเป้าหมายนี้</p>
                                <p class="text-muted">เริ่มออกกำลังกายเพื่อบันทึกผลลัพธ์</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // แสดงข้อความแจ้งเตือนเมื่อโหลดหน้าเสร็จหากมี session messages
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

        // Delete confirmation with SweetAlert2
        document.querySelectorAll('.delete-goal-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formEl = this;

                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: 'คุณต้องการลบเป้าหมายนี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้',
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
