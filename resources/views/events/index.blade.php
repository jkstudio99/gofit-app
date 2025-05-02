@extends('layouts.app')

@section('title', 'กิจกรรมทั้งหมด')

@section('styles')
<style>
    .event-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .event-card .card-img-top {
        height: 180px;
        object-fit: cover;
    }

    .event-img-container {
        position: relative;
    }

    .event-badges {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .event-status {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .category-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .event-stat {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .event-stat i {
        width: 18px;
        margin-right: 8px;
        text-align: center;
    }

    .participants-progress {
        height: 6px;
        border-radius: 3px;
        margin-top: 5px;
    }

    .view-toggle .btn {
        border-radius: 4px;
        padding: 6px 12px;
    }

    .filter-badge {
        cursor: pointer;
        transition: all 0.2s;
        border: none !important;
    }

    .filter-badge:hover, .filter-badge.active {
        background-color: #2DC679 !important;
        color: white !important;
    }

    .filter-card {
        border-radius: 12px;
        border: none;
    }

    .hover-translate {
        transition: transform 0.3s ease;
    }

    .hover-translate:hover {
        transform: translateY(-5px);
    }

    .empty-state {
        padding: 50px 0;
        text-align: center;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d1d1;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">กิจกรรมทั้งหมด</h2>
            <p class="text-muted">ค้นหาและเข้าร่วมกิจกรรมออกกำลังกายต่างๆ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('events.my') }}" class="btn btn-outline-primary">
                <i class="fas fa-bookmark me-1"></i> กิจกรรมของฉัน
            </a>
            @can('create', App\Models\Event::class)
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> สร้างกิจกรรม
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- ตัวกรองและค้นหา -->
    <div class="card shadow-sm mb-4 filter-card">
        <div class="card-body p-4">
            <form action="{{ route('events.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                   placeholder="ค้นหากิจกรรม..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="category" class="form-select">
                            <option value="">ทุกประเภท</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="status" class="form-select">
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>กำลังจะมาถึง</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>ที่ผ่านมา</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="sort" class="form-select">
                            <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>วันที่: เร็วที่สุด</option>
                            <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>วันที่: ล่าสุด</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>ความนิยม</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-filter me-1"></i> กรอง
                            </button>
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary flex-grow-1">
                                <i class="fas fa-redo me-1"></i> รีเซ็ต
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ไม่พบกิจกรรม -->
    @if ($events->isEmpty())
    <div class="empty-state bg-light rounded">
        <i class="fas fa-calendar-times"></i>
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
        <div class="btn-group view-toggle">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
               class="btn {{ request('view', 'grid') == 'grid' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-th"></i>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
               class="btn {{ request('view') == 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-list"></i>
            </a>
        </div>
    </div>

    <!-- แสดงแบบ Grid -->
    @if (request('view', 'grid') == 'grid')
    <div class="row">
        @foreach ($events as $event)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm event-card">
                <div class="event-img-container">
                    <img src="{{ asset('storage/' . ($event->event_image ?? $event->image_url ?? 'events/default.jpg')) }}"
                        class="card-img-top" alt="{{ $event->title }}">

                    <div class="event-badges">
                        <span class="badge category-badge bg-primary">
                            {{ ucfirst($event->category ?? 'กิจกรรม') }}
                        </span>
                    </div>

                    <div class="event-status">
                        @if ($event->hasEnded())
                            <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                        @elseif ($event->isActive())
                            <span class="badge bg-success">กำลังดำเนินการ</span>
                        @elseif ($event->isFull())
                            <span class="badge bg-warning text-dark">เต็มแล้ว</span>
                        @else
                            <span class="badge bg-info">กำลังจะมาถึง</span>
                        @endif
                    </div>
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $event->title }}</h5>
                    <p class="card-text text-muted small mb-3" style="height: 3em; overflow: hidden;">
                        {{ Str::limit($event->description, 80) }}
                    </p>

                    <div class="mb-3">
                        <div class="event-stat">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span>{{ Carbon\Carbon::parse($event->start_datetime)->locale('th')->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="event-stat">
                            <i class="fas fa-clock text-primary"></i>
                            <span>{{ Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} น.</span>
                        </div>
                        <div class="event-stat">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <span class="text-truncate">{{ $event->location }}</span>
                        </div>
                        <div class="event-stat">
                            <i class="fas fa-users text-primary"></i>
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <span>ผู้เข้าร่วม</span>
                                    <span>{{ $event->participants_count ?? 0 }}/{{ $event->max_participants ?? $event->capacity ?? 'ไม่จำกัด' }}</span>
                                </div>
                                @if($event->capacity > 0)
                                <div class="progress participants-progress">
                                    @php
                                        $percentage = min(100, ($event->participants_count / max(1, $event->capacity)) * 100);
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $percentage }}%"
                                        aria-valuenow="{{ $event->participants_count }}"
                                        aria-valuemin="0"
                                        aria-valuemax="{{ $event->capacity }}">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto">
                        <a href="{{ route('events.show', $event->event_id) }}" class="btn btn-outline-primary flex-grow-1">
                            <i class="fas fa-info-circle me-1"></i> รายละเอียด
                        </a>
                        @if (!$event->hasEnded() && !$event->isFull())
                            @if (!$event->isRegistered(Auth::id()))
                                <form action="{{ route('events.register', $event->event_id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-calendar-check me-1"></i> เข้าร่วม
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('events.my') }}" class="btn btn-success flex-grow-1">
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
                    <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                        <img src="{{ asset('storage/' . ($event->event_image ?? $event->image_url ?? 'events/default.jpg')) }}"
                            class="img-fluid rounded" style="height: 100px; width: 100%; object-fit: cover;"
                            alt="{{ $event->title }}">
                    </div>
                    <div class="col-lg-7 col-md-6">
                        <h5 class="mb-1">{{ $event->title }}</h5>
                        <p class="text-muted mb-1 small">{{ Str::limit($event->description, 100) }}</p>
                        <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                            <span class="badge category-badge bg-primary">{{ ucfirst($event->category ?? 'กิจกรรม') }}</span>

                            @if ($event->hasEnded())
                                <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                            @elseif ($event->isActive())
                                <span class="badge bg-success">กำลังดำเนินการ</span>
                            @elseif ($event->isFull())
                                <span class="badge bg-warning text-dark">เต็มแล้ว</span>
                            @else
                                <span class="badge bg-info">กำลังจะมาถึง</span>
                            @endif

                            @if ($event->isRegistered(Auth::id()))
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> ลงทะเบียนแล้ว</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="event-stat">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span>{{ Carbon\Carbon::parse($event->start_datetime)->locale('th')->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="event-stat">
                            <i class="fas fa-clock text-primary"></i>
                            <span>{{ Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} น.</span>
                        </div>
                        <div class="event-stat">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <span class="text-truncate">{{ $event->location }}</span>
                        </div>
                        <div class="event-stat">
                            <i class="fas fa-users text-primary"></i>
                            <span>{{ $event->participants_count ?? 0 }}/{{ $event->max_participants ?? $event->capacity ?? 'ไม่จำกัด' }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $events->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection

