@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">กิจกรรมของฉัน</h2>
            <p class="text-muted">รายการกิจกรรมที่คุณลงทะเบียนเข้าร่วมหรือเป็นผู้จัด</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-1"></i> กิจกรรมทั้งหมด
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

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'participating' ? 'active' : '' }}"
               href="{{ route('events.my-events', ['tab' => 'participating']) }}">
                <i class="fas fa-calendar-check me-1"></i> กิจกรรมที่เข้าร่วม
                @if($participatingCount > 0)
                    <span class="badge bg-primary ms-1">{{ $participatingCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'created' ? 'active' : '' }}"
               href="{{ route('events.my-events', ['tab' => 'created']) }}">
                <i class="fas fa-edit me-1"></i> กิจกรรมที่ฉันจัด
                @if($createdCount > 0)
                    <span class="badge bg-primary ms-1">{{ $createdCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'past' ? 'active' : '' }}"
               href="{{ route('events.my-events', ['tab' => 'past']) }}">
                <i class="fas fa-history me-1"></i> กิจกรรมที่ผ่านมา
                @if($pastCount > 0)
                    <span class="badge bg-secondary ms-1">{{ $pastCount }}</span>
                @endif
            </a>
        </li>
    </ul>

    <!-- ถ้าไม่มีกิจกรรม -->
    @if ($events->isEmpty())
    <div class="text-center py-5 bg-light rounded">
        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
        <h4>ไม่พบกิจกรรม</h4>
        <p class="text-muted">
            @if ($activeTab == 'participating')
                คุณยังไม่ได้ลงทะเบียนเข้าร่วมกิจกรรมใดๆ
            @elseif ($activeTab == 'created')
                คุณยังไม่ได้สร้างกิจกรรมใดๆ
            @else
                คุณยังไม่มีกิจกรรมที่ผ่านมา
            @endif
        </p>
        @if($activeTab != 'created')
            <a href="{{ route('events.index') }}" class="btn btn-primary mt-3">
                <i class="fas fa-search me-1"></i> ค้นหากิจกรรมที่น่าสนใจ
            </a>
        @else
            <a href="{{ route('events.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-1"></i> สร้างกิจกรรมใหม่
            </a>
        @endif
    </div>

    <!-- รายการกิจกรรม -->
    @else
        @if ($activeTab == 'participating' || $activeTab == 'past')
            <div class="row">
                @foreach ($events as $event)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm hover-translate">
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . ($event->image_url ?? 'events/default.jpg')) }}"
                                 class="card-img-top" alt="{{ $event->title }}"
                                 style="height: 160px; object-fit: cover;">
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
                            <div class="mb-2 small text-muted">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('d M Y') }}
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} น.
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    <span class="text-truncate">{{ $event->location }}</span>
                                </div>
                            </div>

                            @if (!$event->hasEnded())
                                <div class="alert alert-light py-2 px-3 mb-3">
                                    @if ($event->isActive())
                                        <div class="small mb-1">กิจกรรมกำลังดำเนินการ</div>
                                        <div class="fw-bold">สิ้นสุดใน {{ \Carbon\Carbon::now()->diffForHumans(\Carbon\Carbon::parse($event->end_datetime), ['parts' => 1]) }}</div>
                                    @else
                                        <div class="small mb-1">เริ่มในอีก</div>
                                        <div class="fw-bold">{{ \Carbon\Carbon::now()->diffForHumans(\Carbon\Carbon::parse($event->start_datetime), ['parts' => 1]) }}</div>
                                    @endif
                                </div>
                            @endif

                            <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('events.show', $event->event_id) }}" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                </a>
                                @if (!$event->hasEnded())
                                    <form action="{{ route('events.unregister', $event->event_id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกการเข้าร่วม?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @elseif ($activeTab == 'created')
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">กิจกรรม</th>
                            <th scope="col">วันที่</th>
                            <th scope="col">สถานที่</th>
                            <th scope="col">ผู้เข้าร่วม</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($event->image_url ?? 'events/default.jpg')) }}"
                                         alt="{{ $event->title }}"
                                         class="rounded me-3"
                                         style="width: 48px; height: 48px; object-fit: cover;">
                                    <div>
                                        <div class="fw-medium">{{ $event->title }}</div>
                                        <div class="small text-muted">{{ ucfirst($event->category) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($event->start_datetime)->format('d M Y') }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }}</div>
                            </td>
                            <td>{{ $event->location }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">{{ $event->participant_count }}/{{ $event->max_participants }}</div>
                                    <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: {{ ($event->participant_count / $event->max_participants) * 100 }}%"
                                             aria-valuenow="{{ $event->participant_count }}"
                                             aria-valuemin="0"
                                             aria-valuemax="{{ $event->max_participants }}">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($event->hasEnded())
                                    <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                                @elseif ($event->isActive())
                                    <span class="badge bg-success">กำลังดำเนินการ</span>
                                @else
                                    <span class="badge bg-info text-white">กำลังจะมาถึง</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('events.show', $event->event_id) }}" class="btn btn-sm btn-outline-primary" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if (!$event->hasEnded())
                                        <a href="{{ route('events.edit', $event->event_id) }}" class="btn btn-sm btn-outline-warning" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('events.participants', $event->event_id) }}" class="btn btn-sm btn-outline-success" title="จัดการผู้เข้าร่วม">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('events.destroy', $event->event_id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบกิจกรรมนี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
        transition: transform 0.3s ease;
    }
    .hover-translate:hover {
        transform: translateY(-5px);
    }
</style>
@endpush
