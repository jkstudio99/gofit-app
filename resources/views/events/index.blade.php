@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">กิจกรรมทั้งหมด</h2>
            <p class="text-muted">ค้นหาและเข้าร่วมกิจกรรมออกกำลังกายต่างๆ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('events.my-events') }}" class="btn btn-outline-primary">
                <i class="fas fa-bookmark me-1"></i> กิจกรรมของฉัน
            </a>
            @can('create', App\Models\Event::class)
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> สร้างกิจกรรม
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- ตัวกรองและค้นหา -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('events.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                   placeholder="ค้นหากิจกรรม..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-select">
                            <option value="">ทุกประเภท</option>
                            <option value="running" {{ request('category') == 'running' ? 'selected' : '' }}>วิ่ง</option>
                            <option value="cycling" {{ request('category') == 'cycling' ? 'selected' : '' }}>จักรยาน</option>
                            <option value="swimming" {{ request('category') == 'swimming' ? 'selected' : '' }}>ว่ายน้ำ</option>
                            <option value="workout" {{ request('category') == 'workout' ? 'selected' : '' }}>ออกกำลังกาย</option>
                            <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>อื่นๆ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>กำลังจะมาถึง</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>ที่ผ่านมา</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sort" class="form-select">
                            <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>วันที่: เร็วที่สุด</option>
                            <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>วันที่: ล่าสุด</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>ความนิยม</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> กรอง
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ไม่พบกิจกรรม -->
    @if ($events->isEmpty())
    <div class="text-center py-5 bg-light rounded">
        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
        <h4>ไม่พบกิจกรรม</h4>
        <p class="text-muted">
            @if (request('search'))
                ไม่พบกิจกรรมที่ตรงกับคำค้นหา "{{ request('search') }}"
            @else
                ไม่มีกิจกรรมที่ตรงกับตัวกรอง โปรดลองเปลี่ยนตัวกรอง
            @endif
        </p>
        <a href="{{ route('events.index') }}" class="btn btn-primary mt-3">
            <i class="fas fa-sync me-1"></i> รีเซ็ตตัวกรอง
        </a>
    </div>

    @else
    <!-- จำนวนกิจกรรมที่พบและการแสดงผล -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">พบ {{ $events->total() }} กิจกรรม</p>
        <div class="btn-group">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
               class="btn btn-sm {{ request('view', 'grid') == 'grid' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-th"></i>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
               class="btn btn-sm {{ request('view') == 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-list"></i>
            </a>
        </div>
    </div>

    <!-- แสดงแบบ Grid -->
    @if (request('view', 'grid') == 'grid')
    <div class="row">
        @foreach ($events as $event)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm hover-translate">
                <div class="position-relative">
                    <img src="{{ asset('storage/' . ($event->image_url ?? 'events/default.jpg')) }}"
                        class="card-img-top" alt="{{ $event->title }}"
                        style="height: 180px; object-fit: cover;">
                    <div class="position-absolute top-0 start-0 p-2">
                        <span class="badge bg-primary">{{ ucfirst($event->category) }}</span>
                    </div>
                    <div class="position-absolute top-0 end-0 p-2">
                        @if ($event->hasEnded())
                            <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                        @elseif ($event->isActive())
                            <span class="badge bg-success">กำลังดำเนินการ</span>
                        @else
                            <span class="badge bg-info text-white">กำลังจะมาถึง</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-truncate">{{ $event->title }}</h5>
                    <p class="card-text text-muted small mb-3" style="height: 3em; overflow: hidden;">
                        {{ Str::limit($event->description, 80) }}
                    </p>

                    <div class="mb-3 small text-muted">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            {{ \Carbon\Carbon::parse($event->start_datetime)->format('d M Y') }}
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} น.
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            <span class="text-truncate">{{ $event->location }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users me-2 text-primary"></i>
                            <div class="d-flex align-items-center flex-grow-1">
                                <span class="me-2">{{ $event->participants_count ?? 0 }}/{{ $event->max_participants }}</span>
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ ($event->active_registrations_count / $event->capacity) * 100 }}%"
                                        aria-valuenow="{{ $event->active_registrations_count }}"
                                        aria-valuemin="0"
                                        aria-valuemax="{{ $event->capacity }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('events.show', $event->event_id) }}" class="btn btn-outline-primary flex-grow-1">
                            <i class="fas fa-info-circle me-1"></i> รายละเอียด
                        </a>
                        @if (!$event->hasEnded())
                            @if (!$event->isRegistered(Auth::id()))
                                <form action="{{ route('events.register', $event->event_id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-calendar-check me-1"></i> เข้าร่วม
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('events.my-events') }}" class="btn btn-success flex-grow-1">
                                    <i class="fas fa-check-circle me-1"></i> ลงทะเบียนแล้ว
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- แสดงแบบ List -->
    @else
    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @foreach ($events as $event)
            <a href="{{ route('events.show', $event->event_id) }}" class="list-group-item list-group-item-action p-3">
                <div class="row align-items-center">
                    <div class="col-md-2 col-lg-1">
                        <img src="{{ asset('storage/' . ($event->image_url ?? 'events/default.jpg')) }}"
                            class="img-fluid rounded" alt="{{ $event->title }}"
                            style="width: 70px; height: 70px; object-fit: cover;">
                    </div>
                    <div class="col-md-7 col-lg-8">
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="mb-0 me-2">{{ $event->title }}</h5>
                            <span class="badge bg-primary">{{ ucfirst($event->category) }}</span>
                            @if ($event->hasEnded())
                                <span class="badge bg-secondary ms-1">สิ้นสุดแล้ว</span>
                            @elseif ($event->isActive())
                                <span class="badge bg-success ms-1">กำลังดำเนินการ</span>
                            @else
                                <span class="badge bg-info text-white ms-1">กำลังจะมาถึง</span>
                            @endif
                        </div>
                        <p class="mb-1 text-muted small">{{ Str::limit($event->description, 100) }}</p>
                        <div class="d-flex text-muted small">
                            <div class="me-3">
                                <i class="fas fa-calendar-alt me-1 text-primary"></i>
                                {{ \Carbon\Carbon::parse($event->start_datetime)->format('d M Y H:i') }}
                            </div>
                            <div class="me-3">
                                <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                {{ $event->location }}
                            </div>
                            <div>
                                <i class="fas fa-users me-1 text-primary"></i>
                                {{ $event->participants_count ?? 0 }}/{{ $event->max_participants }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 text-md-end mt-3 mt-md-0">
                        @if (!$event->hasEnded())
                            @if (!$event->isRegistered(Auth::id()))
                                <form action="{{ route('events.register', $event->event_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-calendar-check me-1"></i> เข้าร่วม
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-success p-2">
                                    <i class="fas fa-check-circle me-1"></i> ลงทะเบียนแล้ว
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .hover-translate {
        transition: transform 0.2s ease;
    }
    .hover-translate:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

