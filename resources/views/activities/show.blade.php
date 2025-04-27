@extends('layouts.app')

@section('title', 'รายละเอียดกิจกรรมการออกกำลังกาย')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">รายละเอียดกิจกรรมการออกกำลังกาย</h2>
            <p class="text-muted">รายละเอียดและสถิติของกิจกรรมการออกกำลังกาย</p>
        </div>
        <div>
            <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> กลับ
            </a>
            @if($activity->user_id == auth()->id())
            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-outline-primary ms-2">
                <i class="fas fa-edit me-1"></i> แก้ไข
            </a>
            @endif
        </div>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        @if($activity->activity_type == 'run')
                            <div class="activity-icon bg-primary text-white">
                                <i class="fas fa-running"></i>
                            </div>
                            <h3 class="mb-0 ms-3">วิ่ง</h3>
                        @elseif($activity->activity_type == 'walk')
                            <div class="activity-icon bg-success text-white">
                                <i class="fas fa-walking"></i>
                            </div>
                            <h3 class="mb-0 ms-3">เดิน</h3>
                        @elseif($activity->activity_type == 'cycle')
                            <div class="activity-icon bg-danger text-white">
                                <i class="fas fa-bicycle"></i>
                            </div>
                            <h3 class="mb-0 ms-3">ปั่นจักรยาน</h3>
                        @elseif($activity->activity_type == 'swim')
                            <div class="activity-icon bg-info text-white">
                                <i class="fas fa-swimmer"></i>
                            </div>
                            <h3 class="mb-0 ms-3">ว่ายน้ำ</h3>
                        @elseif($activity->activity_type == 'gym')
                            <div class="activity-icon bg-warning text-dark">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                            <h3 class="mb-0 ms-3">ออกกำลังกายที่ยิม</h3>
                        @elseif($activity->activity_type == 'yoga')
                            <div class="activity-icon bg-purple text-white">
                                <i class="fas fa-om"></i>
                            </div>
                            <h3 class="mb-0 ms-3">โยคะ</h3>
                        @elseif($activity->activity_type == 'hiit')
                            <div class="activity-icon bg-danger text-white">
                                <i class="fas fa-fire-alt"></i>
                            </div>
                            <h3 class="mb-0 ms-3">HIIT</h3>
                        @else
                            <div class="activity-icon bg-secondary text-white">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <h3 class="mb-0 ms-3">กิจกรรมอื่นๆ</h3>
                        @endif
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card bg-light p-3 rounded">
                                <div class="text-muted mb-1">ระยะทาง</div>
                                <div class="d-flex align-items-baseline">
                                    <h3 class="mb-0 me-2">{{ $activity->distance ? number_format($activity->distance, 2) : '0' }}</h3>
                                    <span>กิโลเมตร</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card bg-light p-3 rounded">
                                <div class="text-muted mb-1">ระยะเวลา</div>
                                <div class="d-flex align-items-baseline">
                                    @php
                                        $hours = floor($activity->duration / 60);
                                        $minutes = $activity->duration % 60;
                                        $durationFormatted = '';
                                        if ($hours > 0) {
                                            $durationFormatted .= $hours . ' ชม. ';
                                        }
                                        $durationFormatted .= $minutes . ' นาที';
                                    @endphp
                                    <h3 class="mb-0 me-2">{{ $durationFormatted }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card bg-light p-3 rounded">
                                <div class="text-muted mb-1">แคลอรี่</div>
                                <div class="d-flex align-items-baseline">
                                    <h3 class="mb-0 me-2">{{ $activity->calories ? number_format($activity->calories) : '0' }}</h3>
                                    <span>แคล</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($activity->pace)
                    <div class="mb-4">
                        <div class="stat-card bg-light p-3 rounded">
                            <div class="text-muted mb-1">เพซ (Pace)</div>
                            <div class="d-flex align-items-baseline">
                                <h3 class="mb-0 me-2">{{ $activity->formattedPace }}</h3>
                                <span>ต่อกิโลเมตร</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted">วันที่และเวลาเริ่มต้น</div>
                                <div class="fw-medium">
                                    @if($activity->start_time)
                                        {{ $activity->start_time->format('d M Y H:i') }}
                                    @else
                                        ไม่ระบุ
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted">วันที่และเวลาสิ้นสุด</div>
                                <div class="fw-medium">
                                    @if($activity->end_time)
                                        {{ $activity->end_time->format('d M Y H:i') }}
                                    @else
                                        ไม่ระบุ
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($activity->is_group)
                    <div class="mb-4">
                        <h5 class="mb-3">ข้อมูลผู้เข้าร่วม</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="stat-card bg-light p-3 rounded">
                                    <div class="text-muted mb-1">จำนวนผู้เข้าร่วมสูงสุด</div>
                                    <div class="fw-medium">{{ $activity->max_participants }} คน</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card bg-light p-3 rounded">
                                    <div class="text-muted mb-1">จำนวนผู้เข้าร่วมปัจจุบัน</div>
                                    <div class="fw-medium">{{ $activity->registrations()->where('status', 'registered')->count() }} คน</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card bg-light p-3 rounded">
                                    <div class="text-muted mb-1">จำนวนที่เหลือ</div>
                                    <div class="fw-medium">{{ $spotsLeft }} คน</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($activity->notes)
                    <div class="mb-4">
                        <h5 class="mb-2">บันทึกเพิ่มเติม</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $activity->notes }}
                        </div>
                    </div>
                    @endif

                    @if($activity->details)
                    <div class="mb-4">
                        <h5 class="mb-2">รายละเอียดเพิ่มเติม</h5>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($activity->details)) !!}
                        </div>
                    </div>
                    @endif

                    @if($activity->user_id == auth()->id())
                    <div class="d-flex justify-content-end mt-4">
                        <form action="{{ route('activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบกิจกรรมนี้?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash-alt me-1"></i> ลบกิจกรรม
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($activity->is_group && $activity->status == 'active')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">การเข้าร่วมกิจกรรม</h5>

                    @if($activity->isFull() && !$isRegistered)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i> ขออภัย จำนวนผู้เข้าร่วมเต็มแล้ว
                    </div>
                    @endif

                    @if($isRegistered)
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle me-2"></i> คุณได้ลงทะเบียนเข้าร่วมกิจกรรมนี้แล้ว
                    </div>
                    <form action="{{ route('activities.cancel-registration', $activity) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกการเข้าร่วมกิจกรรมนี้?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-times-circle me-1"></i> ยกเลิกการเข้าร่วม
                        </button>
                    </form>
                    @else
                    <form action="{{ route('activities.register', $activity) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100" {{ $activity->isFull() ? 'disabled' : '' }}>
                            <i class="fas fa-user-plus me-1"></i> ลงทะเบียนเข้าร่วม
                        </button>
                    </form>
                    @endif

                    <div class="mt-4">
                        <h6>ข้อมูลผู้จัดกิจกรรม</h6>
                        <div class="d-flex align-items-center mt-2">
                            <div class="avatar-sm bg-primary text-white rounded-circle">
                                {{ substr($activity->user->name ?? 'User', 0, 1) }}
                            </div>
                            <div class="ms-2">
                                <div class="fw-medium">{{ $activity->user->name ?? 'ไม่ระบุ' }}</div>
                                <div class="small text-muted">ผู้จัดกิจกรรม</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">สถานะกิจกรรม</h5>
                    @if($activity->status == 'active')
                    <div class="status-badge bg-success text-white">
                        <i class="fas fa-check-circle me-1"></i> กำลังใช้งาน
                    </div>
                    @elseif($activity->status == 'completed')
                    <div class="status-badge bg-info text-white">
                        <i class="fas fa-flag-checkered me-1"></i> เสร็จสิ้น
                    </div>
                    @elseif($activity->status == 'cancelled')
                    <div class="status-badge bg-danger text-white">
                        <i class="fas fa-times-circle me-1"></i> ยกเลิก
                    </div>
                    @else
                    <div class="status-badge bg-secondary text-white">
                        <i class="fas fa-question-circle me-1"></i> ไม่ระบุ
                    </div>
                    @endif

                    <div class="mt-3">
                        <div class="text-muted mb-1">สร้างเมื่อ</div>
                        <div class="small">{{ $activity->created_at->format('d M Y H:i') }}</div>
                    </div>

                    <div class="mt-2">
                        <div class="text-muted mb-1">อัปเดตล่าสุด</div>
                        <div class="small">{{ $activity->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .activity-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 500;
    }
    .bg-purple {
        background-color: #6f42c1;
    }
    .stat-card {
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>
@endsection
