@extends('layouts.app')

@section('content')
<div class="container py-4">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ URL::previous() }}" class="btn btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
            </a>
            <h2 class="mb-0">{{ $event->title }}</h2>
            <p class="text-muted">{{ ucfirst($event->category) }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('update', $event)
                <a href="{{ route('events.edit', $event->event_id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> แก้ไข
                </a>
            @endcan
            @can('delete', $event)
                <form action="{{ route('events.destroy', $event->event_id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบกิจกรรมนี้?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash-alt me-1"></i> ลบ
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <div class="row">
        <!-- คอลัมน์ซ้าย: รูปภาพและข้อมูลหลัก -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <img src="{{ asset('storage/' . ($event->event_image ?? $event->image_url ?? 'events/default.jpg')) }}"
                     class="card-img-top" alt="{{ $event->title }}"
                     style="height: 350px; object-fit: cover;">
                <div class="card-body p-4">
                    <!-- สถานะกิจกรรม -->
                    <div class="mb-4">
                        @if ($event->hasEnded())
                            <div class="badge bg-secondary p-2 px-3">
                                <i class="fas fa-flag-checkered me-1"></i> สิ้นสุดแล้ว
                            </div>
                        @elseif ($event->isActive())
                            <div class="badge bg-success p-2 px-3">
                                <i class="fas fa-play-circle me-1"></i> กำลังดำเนินการ
                            </div>
                        @else
                            <div class="badge bg-info text-white p-2 px-3">
                                <i class="fas fa-hourglass-start me-1"></i> กำลังจะมาถึง
                            </div>
                        @endif
                    </div>

                    <!-- รายละเอียดกิจกรรม -->
                    <h4 class="mb-3">รายละเอียด</h4>
                    <div class="mb-4">
                        {{ $event->description }}
                    </div>

                    <!-- วันเวลาและสถานที่ -->
                    <h4 class="mb-3">วันเวลาและสถานที่</h4>
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-calendar-alt text-primary me-2"></i> วันและเวลา</h5>
                                        <div class="mb-2">
                                            <strong>เริ่ม:</strong> {{ \Carbon\Carbon::parse($event->start_datetime)->thaiDate() }} น.
                                        </div>
                                        <div>
                                            <strong>สิ้นสุด:</strong> {{ \Carbon\Carbon::parse($event->end_datetime)->thaiDate() }} น.
                                        </div>
                                        @if (!$event->hasEnded())
                                            <div class="mt-3 small">
                                                @if ($event->isActive())
                                                    <i class="fas fa-clock text-success me-1"></i> กำลังดำเนินการ - เหลือเวลาอีก
                                                    {{ \Carbon\Carbon::now()->diffForHumans(\Carbon\Carbon::parse($event->end_datetime), ['parts' => 2]) }}
                                                @else
                                                    <i class="fas fa-clock text-info me-1"></i> จะเริ่มใน
                                                    {{ \Carbon\Carbon::now()->diffForHumans(\Carbon\Carbon::parse($event->start_datetime), ['parts' => 2]) }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-map-marker-alt text-primary me-2"></i> สถานที่</h5>
                                        <p class="mb-0">{{ $event->location }}</p>
                                        @if ($event->location_details)
                                            <p class="text-muted small mt-2">{{ $event->location_details }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ข้อมูลเพิ่มเติม -->
                    <h4 class="mb-3">ข้อมูลเพิ่มเติม</h4>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-trophy text-primary me-2"></i> ประเภท</h5>
                                    <p class="mb-0">{{ ucfirst($event->category) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-flag text-primary me-2"></i> ระดับความยาก</h5>
                                    <p class="mb-0">
                                        @if ($event->difficulty == 'beginner')
                                            <span class="text-success">เริ่มต้น</span>
                                        @elseif ($event->difficulty == 'intermediate')
                                            <span class="text-warning">ปานกลาง</span>
                                        @elseif ($event->difficulty == 'advanced')
                                            <span class="text-danger">ขั้นสูง</span>
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-tags text-primary me-2"></i> ผู้จัด</h5>
                                    <a href="{{ route('profile.show', $event->creator->username) }}" class="text-decoration-none">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . ($event->creator->profile_image ?? 'profile/default.jpg')) }}"
                                                alt="{{ $event->creator->name }}"
                                                class="rounded-circle me-2"
                                                style="width: 24px; height: 24px; object-fit: cover;">
                                            <span>{{ $event->creator->name }}</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ข้อกำหนดเพิ่มเติม -->
                    @if ($event->requirements)
                    <h4 class="mb-3">ข้อกำหนดในการเข้าร่วม</h4>
                    <div class="mb-4 ps-3 border-start border-primary border-3">
                        {{ $event->requirements }}
                    </div>
                    @endif

                    <!-- แชร์กิจกรรม -->
                    <div class="d-flex align-items-center mt-4">
                        <span class="me-3">แชร์กิจกรรมนี้:</span>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('events.show', $event->event_id)) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('events.show', $event->event_id)) }}&text={{ urlencode($event->title) }}"
                               target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://line.me/R/msg/text/?{{ urlencode($event->title . ' ' . route('events.show', $event->event_id)) }}"
                               target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="fab fa-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- คอลัมน์ขวา: ข้อมูลการลงทะเบียนและผู้เข้าร่วม -->
        <div class="col-lg-4">
            <!-- การลงทะเบียน -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="mb-3">การลงทะเบียน</h4>

                    <!-- จำนวนผู้เข้าร่วม -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>ผู้เข้าร่วม</span>
                            <span class="fw-bold">{{ $event->active_registrations_count }}/{{ $event->capacity }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ ($event->active_registrations_count / $event->capacity) * 100 }}%"
                                aria-valuenow="{{ $event->active_registrations_count }}"
                                aria-valuemin="0"
                                aria-valuemax="{{ $event->capacity }}">
                            </div>
                        </div>
                        <div class="small mt-2">
                            @if ($event->hasEnded())
                                <span class="text-muted">กิจกรรมสิ้นสุดแล้ว</span>
                            @elseif ($event->isFull())
                                <span class="text-danger">กิจกรรมเต็มแล้ว</span>
                            @else
                                <span class="text-success">เหลือที่ว่าง {{ $event->capacity - $event->active_registrations_count }} ที่</span>
                            @endif
                        </div>
                    </div>

                    <!-- ปุ่มลงทะเบียน -->
                    @if (!$event->hasEnded())
                        @if (auth()->check())
                            @if ($isRegistered)
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle me-2"></i> คุณได้ลงทะเบียนเข้าร่วมกิจกรรมนี้แล้ว
                                </div>
                                <form action="{{ route('events.unregister', $event->event_id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกการลงทะเบียน?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-times-circle me-1"></i> ยกเลิกการลงทะเบียน
                                    </button>
                                </form>
                            @elseif (!$event->isFull())
                                <form action="{{ route('events.register', $event->event_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 py-2">
                                        <i class="fas fa-calendar-check me-1"></i> ลงทะเบียนเข้าร่วม
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i> กิจกรรมเต็มแล้ว
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i> กรุณาเข้าสู่ระบบเพื่อลงทะเบียน
                            </div>
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-1"></i> เข้าสู่ระบบเพื่อลงทะเบียน
                            </a>
                        @endif
                    @else
                        <div class="alert alert-secondary mb-3">
                            <i class="fas fa-clock me-2"></i> กิจกรรมนี้สิ้นสุดแล้ว
                        </div>
                    @endif

                    <!-- กำหนดการลงทะเบียน -->
                    <div class="mt-3 border-top pt-3">
                        <div class="text-muted small mb-2">ข้อมูลการลงทะเบียน</div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            <div>
                                <div class="small text-muted">เปิดรับสมัคร</div>
                                <div>{{ \Carbon\Carbon::parse($event->registration_start_datetime)->format('d M Y H:i') }} น.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-times me-2 text-primary"></i>
                            <div>
                                <div class="small text-muted">ปิดรับสมัคร</div>
                                <div>{{ \Carbon\Carbon::parse($event->registration_end_datetime)->format('d M Y H:i') }} น.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ผู้เข้าร่วม -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">ผู้เข้าร่วม ({{ $participants->count() }})</h4>
                        @if ($participants->count() > 5 && $participants->count() < $event->capacity)
                            <a href="{{ route('events.participants', $event->event_id) }}" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                        @endif
                    </div>

                    @if ($participants->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach ($participants->take(5) as $participant)
                                <li class="list-group-item px-0 py-2 border-0">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . ($participant->user->profile_image ?? 'profile/default.jpg')) }}"
                                            alt="{{ $participant->user->name }}"
                                            class="rounded-circle me-3"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <a href="{{ route('profile.show', $participant->user->username) }}" class="text-decoration-none">
                                                <div class="fw-medium">{{ $participant->user->name }}</div>
                                            </a>
                                            <div class="small text-muted">ลงทะเบียนเมื่อ {{ $participant->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        @if ($participants->count() > 5)
                            <div class="text-center mt-2">
                                <a href="{{ route('events.participants', $event->event_id) }}" class="text-decoration-none">
                                    ดูผู้เข้าร่วมทั้งหมด ({{ $participants->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users text-muted fa-3x mb-3"></i>
                            <p class="text-muted">ยังไม่มีผู้เข้าร่วมในขณะนี้</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- กิจกรรมอื่นที่น่าสนใจ -->
            @if ($relatedEvents->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">กิจกรรมอื่นที่น่าสนใจ</h4>
                    <div class="list-group list-group-flush">
                        @foreach ($relatedEvents as $relatedEvent)
                            <a href="{{ route('events.show', $relatedEvent->event_id) }}" class="list-group-item list-group-item-action px-0 py-3 border-bottom">
                                <div class="d-flex">
                                    <img src="{{ asset('storage/' . ($relatedEvent->image_url ?? 'events/default.jpg')) }}"
                                        alt="{{ $relatedEvent->title }}"
                                        class="rounded me-3"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1">{{ $relatedEvent->title }}</h6>
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($relatedEvent->start_datetime)->format('d M Y') }}
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2">{{ ucfirst($relatedEvent->category) }}</span>
                                            <span class="badge {{ $relatedEvent->hasEnded() ? 'bg-secondary' : ($relatedEvent->isActive() ? 'bg-success' : 'bg-info text-white') }}">
                                                {{ $relatedEvent->hasEnded() ? 'สิ้นสุดแล้ว' : ($relatedEvent->isActive() ? 'กำลังดำเนินการ' : 'กำลังจะมาถึง') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
