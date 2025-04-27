@extends('layouts.app')

@section('title', 'รายละเอียดเป้าหมาย')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">รายละเอียดเป้าหมาย</h2>
                    <p class="text-muted">ติดตามความคืบหน้าของเป้าหมายการออกกำลังกาย</p>
                </div>
                <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการเป้าหมาย
                </a>
            </div>

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="card-title mb-0">{{ $goal->formattedType }}</h3>
                        @if($goal->is_completed)
                            <span class="badge bg-success py-2 px-3">
                                <i class="fas fa-check-circle me-1"></i> สำเร็จแล้ว
                            </span>
                        @elseif($goal->isExpired)
                            <span class="badge bg-secondary py-2 px-3">
                                <i class="fas fa-calendar-times me-1"></i> หมดเวลา
                            </span>
                        @else
                            <span class="badge bg-primary py-2 px-3">
                                <i class="fas fa-running me-1"></i> กำลังดำเนินการ
                            </span>
                        @endif
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card h-100 bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">รายละเอียดเป้าหมาย</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <div class="d-flex">
                                                <div class="text-muted" style="width: 120px;"><i class="fas fa-chart-line me-2 text-primary"></i>ประเภท:</div>
                                                <div class="fw-medium">{{ $goal->formattedType }}</div>
                                            </div>
                                        </li>
                                        <li class="mb-2">
                                            <div class="d-flex">
                                                <div class="text-muted" style="width: 120px;"><i class="fas fa-running me-2 text-primary"></i>กิจกรรม:</div>
                                                <div class="fw-medium">{{ $goal->formattedActivityType }}</div>
                                            </div>
                                        </li>
                                        <li class="mb-2">
                                            <div class="d-flex">
                                                <div class="text-muted" style="width: 120px;"><i class="fas fa-bullseye me-2 text-primary"></i>เป้าหมาย:</div>
                                                <div class="fw-medium">{{ $goal->target_value }}
                                                    @if($goal->type == 'distance')
                                                        กิโลเมตร
                                                    @elseif($goal->type == 'duration')
                                                        นาที
                                                    @elseif($goal->type == 'calories')
                                                        แคลอรี่
                                                    @elseif($goal->type == 'frequency')
                                                        ครั้ง
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-2">
                                            <div class="d-flex">
                                                <div class="text-muted" style="width: 120px;"><i class="fas fa-hourglass-half me-2 text-primary"></i>ช่วงเวลา:</div>
                                                <div class="fw-medium">{{ ucfirst($goal->period) }}</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">ระยะเวลาเป้าหมาย</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <div class="d-flex">
                                                <div class="text-muted" style="width: 120px;"><i class="fas fa-calendar-alt me-2 text-primary"></i>เริ่มต้น:</div>
                                                <div class="fw-medium">{{ $goal->start_date->format('d M Y') }}</div>
                                            </div>
                                        </li>
                                        @if($goal->end_date)
                                        <li class="mb-2">
                                            <div class="d-flex">
                                                <div class="text-muted" style="width: 120px;"><i class="fas fa-calendar-check me-2 text-primary"></i>สิ้นสุด:</div>
                                                <div class="fw-medium">{{ $goal->end_date->format('d M Y') }}</div>
                                            </div>
                                        </li>
                                        @endif
                                        @if($goal->is_completed)
                                        <li class="mb-2">
                                            <div class="d-flex">
                                                <div class="text-muted" style="width: 120px;"><i class="fas fa-flag-checkered me-2 text-success"></i>สำเร็จเมื่อ:</div>
                                                <div class="fw-medium text-success">{{ $goal->updated_at->format('d M Y') }}</div>
                                            </div>
                                        </li>
                                        @elseif($goal->end_date && !$goal->isExpired)
                                        <li class="mb-2">
                                            <div class="d-flex">
                                                <div class="text-muted" style="width: 120px;"><i class="fas fa-hourglass-end me-2 text-warning"></i>เหลือเวลา:</div>
                                                <div class="fw-medium text-warning">{{ $goal->end_date->diffForHumans(['parts' => 1]) }}</div>
                                            </div>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-3">ความคืบหน้า</h5>
                        <div class="card bg-white border p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-medium fs-5">{{ $goal->current_value }} / {{ $goal->target_value }}
                                    @if($goal->type == 'distance')
                                        กิโลเมตร
                                    @elseif($goal->type == 'duration')
                                        นาที
                                    @elseif($goal->type == 'calories')
                                        แคลอรี่
                                    @elseif($goal->type == 'frequency')
                                        ครั้ง
                                    @endif
                                </div>
                                <div class="fw-bold fs-5 {{ $goal->is_completed ? 'text-success' : '' }}">{{ $progressPercentage }}%</div>
                            </div>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar {{ $goal->is_completed ? 'bg-success' : 'bg-primary' }}" role="progressbar"
                                    style="width: {{ $progressPercentage }}%;"
                                    aria-valuenow="{{ $progressPercentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                            <div class="small text-muted">
                                @if($goal->is_completed)
                                    คุณบรรลุเป้าหมายนี้แล้ว ขอแสดงความยินดี!
                                @elseif($goal->isExpired)
                                    เป้าหมายนี้หมดเวลาแล้ว คุณสามารถตั้งเป้าหมายใหม่ได้
                                @else
                                    คุณต้องทำกิจกรรมเพิ่มอีก {{ $goal->target_value - $goal->current_value }}
                                    @if($goal->type == 'distance')
                                        กิโลเมตร
                                    @elseif($goal->type == 'duration')
                                        นาที
                                    @elseif($goal->type == 'calories')
                                        แคลอรี่
                                    @elseif($goal->type == 'frequency')
                                        ครั้ง
                                    @endif
                                    เพื่อบรรลุเป้าหมายนี้
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($goal->is_completed)
                    <div class="alert alert-success mb-4" role="alert">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-trophy fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">ยินดีด้วย! คุณบรรลุเป้าหมายนี้สำเร็จแล้ว</h5>
                                <p class="mb-0">คุณได้รับเหรียญตราสำหรับความสำเร็จนี้แล้ว คุณสามารถตั้งเป้าหมายใหม่ที่ท้าทายขึ้นได้</p>
                            </div>
                        </div>
                    </div>
                    @elseif($goal->isExpired)
                    <div class="alert alert-secondary mb-4" role="alert">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-calendar-times fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">เป้าหมายนี้หมดเวลาแล้ว</h5>
                                <p class="mb-0">คุณสามารถลบเป้าหมายนี้และตั้งเป้าหมายใหม่ได้ อย่าเสียกำลังใจ พยายามต่อไป!</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        @if(!$goal->is_completed)
                            <a href="{{ route('goals.edit', $goal) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> แก้ไขเป้าหมาย
                            </a>
                        @endif

                        <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบเป้าหมายนี้? การกระทำนี้ไม่สามารถย้อนกลับได้')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-1"></i> ลบเป้าหมาย
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">คำแนะนำสำหรับการบรรลุเป้าหมาย</h5>
                </div>
                <div class="card-body p-4">
                    @if($goal->type == 'distance')
                        <div class="mb-3">
                            <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning me-2"></i>เคล็ดลับสำหรับเป้าหมายระยะทาง:</h6>
                            <ul class="mb-0">
                                <li>พยายามวิ่งหรือเดินเพิ่มขึ้นอย่างน้อย 10% ต่อสัปดาห์</li>
                                <li>ใช้แอปติดตามการวิ่งเพื่อบันทึกเส้นทางและระยะทาง</li>
                                <li>หาเส้นทางใหม่ๆ เพื่อเพิ่มความน่าสนใจ</li>
                                <li>ตั้งเป้าหมายย่อยรายวันหรือรายสัปดาห์เพื่อให้ง่ายต่อการบรรลุ</li>
                            </ul>
                        </div>
                    @elseif($goal->type == 'duration')
                        <div class="mb-3">
                            <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning me-2"></i>เคล็ดลับสำหรับเป้าหมายระยะเวลา:</h6>
                            <ul class="mb-0">
                                <li>เพิ่มเวลาออกกำลังกายทีละน้อย ประมาณ 5-10 นาทีต่อครั้ง</li>
                                <li>แบ่งการออกกำลังกายเป็นช่วงสั้นๆ หากไม่สามารถทำได้ต่อเนื่อง</li>
                                <li>ฟังเพลงหรือพอดแคสต์ระหว่างออกกำลังกายเพื่อความเพลิดเพลิน</li>
                                <li>ตั้งเวลาเตือนประจำวันเพื่อให้แน่ใจว่าได้ออกกำลังกายตามแผน</li>
                            </ul>
                        </div>
                    @elseif($goal->type == 'calories')
                        <div class="mb-3">
                            <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning me-2"></i>เคล็ดลับสำหรับเป้าหมายแคลอรี่:</h6>
                            <ul class="mb-0">
                                <li>เพิ่มความเข้มข้นในการออกกำลังกายเพื่อเผาผลาญแคลอรี่มากขึ้น</li>
                                <li>ทำการออกกำลังกายแบบ HIIT เพื่อเผาผลาญแคลอรี่ได้มากในเวลาสั้น</li>
                                <li>ใช้อุปกรณ์ติดตามการออกกำลังกายเพื่อวัดแคลอรี่ที่เผาผลาญได้อย่างแม่นยำ</li>
                                <li>ผสมผสานการออกกำลังกายแบบคาร์ดิโอและการฝึกความแข็งแรง</li>
                            </ul>
                        </div>
                    @elseif($goal->type == 'frequency')
                        <div class="mb-3">
                            <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning me-2"></i>เคล็ดลับสำหรับเป้าหมายความถี่:</h6>
                            <ul class="mb-0">
                                <li>จัดตารางการออกกำลังกายล่วงหน้าและปฏิบัติตามอย่างเคร่งครัด</li>
                                <li>หาคู่ออกกำลังกายเพื่อสร้างแรงจูงใจและความรับผิดชอบ</li>
                                <li>เตรียมชุดและอุปกรณ์ออกกำลังกายไว้ล่วงหน้า</li>
                                <li>เลือกกิจกรรมที่คุณชื่นชอบเพื่อให้รู้สึกว่าการออกกำลังกายเป็นความสนุก ไม่ใช่ภาระ</li>
                            </ul>
                        </div>
                    @endif

                    <div>
                        <h6 class="fw-bold"><i class="fas fa-book text-primary me-2"></i>รู้หรือไม่?</h6>
                        <p class="mb-0">การตั้งเป้าหมายที่เฉพาะเจาะจง วัดผลได้ บรรลุได้จริง สมเหตุสมผล และมีกำหนดเวลาชัดเจน (SMART Goals) จะช่วยเพิ่มโอกาสความสำเร็จถึง 80% เมื่อเทียบกับการไม่มีเป้าหมายที่ชัดเจน</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
