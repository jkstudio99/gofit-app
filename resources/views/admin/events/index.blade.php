@extends('layouts.admin')

@section('title', 'จัดการกิจกรรม')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .status-filter .nav-link {
        color: #6c757d;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        margin-right: 0.5rem;
    }
    .status-filter .nav-link.active {
        color: #fff;
        background-color: #2DC679;
    }

    /* ปรับแต่งตัวกรองขั้นสูง */
    #advancedFilters {
        padding: 0.5rem;
        border-radius: 0.5rem;
        margin-top: 0.75rem;
    }

    #advancedFilters .card-body {
        padding: 1.25rem;
        background-color: #f8f9fa;
    }

    /* ปรับแต่ง Autocomplete */
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999 !important;
    }

    .ui-menu-item {
        padding: 8px 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .ui-menu-item:hover {
        background-color: #f8f9fa;
    }

    .ui-state-active,
    .ui-widget-content .ui-state-active {
        background-color: #2DC679 !important;
        border-color: #2DC679 !important;
        color: white !important;
    }

    /* Event cards styling */
    .event-stat-card {
        border: none;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .event-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }

    .event-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 10px;
        font-size: 20px;
    }

    /* Event image styling */
    .event-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .event-image:hover {
        transform: scale(1.2);
    }

    /* Action buttons styling */
    .event-action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        padding: 0;
    }

    .event-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .event-action-btn i {
        color: white;
        font-size: 15px;
    }

    /* Badge styles */
    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
    }

    /* SweetAlert2 Custom Styles */
    .swal2-styled.swal2-confirm {
        background-color: #2DC679 !important; /* Primary color */
        border-radius: 0.5rem !important;
        font-weight: 500 !important;
        padding: 0.75rem 1.5rem !important;
        box-shadow: 0 5px 10px rgba(45, 198, 121, 0.25) !important;
    }

    .swal2-styled.swal2-confirm:hover {
        background-color: #24A664 !important; /* Primary dark */
    }

    .swal2-styled.swal2-cancel {
        background-color: #FFFFFF !important;
        color: #4A4A4A !important;
        border: 1px solid #E9E9E9 !important;
        border-radius: 0.5rem !important;
        font-weight: 500 !important;
        padding: 0.75rem 1.5rem !important;
    }

    .swal2-styled.swal2-cancel:hover {
        background-color: #F8F8F8 !important;
    }

    .swal2-popup {
        border-radius: 0.75rem !important;
        padding: 1.5rem !important;
        font-family: 'Noto Sans Thai', -apple-system, sans-serif !important;
    }

    .swal2-title {
        color: #121212 !important;
        font-weight: 700 !important;
    }

    .swal2-html-container {
        color: #4A4A4A !important;
    }

    .swal2-icon.swal2-warning {
        border-color: #FFB800 !important;
        color: #FFB800 !important;
    }

    .swal2-icon.swal2-error {
        border-color: #FF4646 !important;
        color: #FF4646 !important;
    }

    .swal2-icon.swal2-success {
        border-color: #2DC679 !important;
        color: #2DC679 !important;
    }

    .swal2-icon.swal2-success [class^=swal2-success-line] {
        background-color: #2DC679 !important;
    }

    .swal2-icon.swal2-success .swal2-success-ring {
        border-color: rgba(45, 198, 121, 0.3) !important;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="mb-0">จัดการกิจกรรม</h2>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>เพิ่มกิจกรรมใหม่
        </a>
    </div>
    <p class="text-muted">จัดการกิจกรรมการวิ่งและการแข่งขันสำหรับผู้ใช้งานในระบบ</p>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-calendar-alt text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">กิจกรรมทั้งหมด</h6>
                        <h4 class="mb-0">{{ $totalEvents ?? $events->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-success bg-opacity-10 me-3">
                        <i class="fas fa-users text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">กำลังดำเนินการ</h6>
                        <h4 class="mb-0">{{ $activeEvents ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-warning bg-opacity-10 me-3">
                        <i class="fas fa-running text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">รอดำเนินการ</h6>
                        <h4 class="mb-0">{{ $upcomingEvents ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 event-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="event-stat-icon bg-info bg-opacity-10 me-3">
                        <i class="fas fa-user-check text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">ผู้เข้าร่วมรวม</h6>
                        <h4 class="mb-0">{{ $totalParticipants ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ตัวกรองสถานะ -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="status-filter">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" href="{{ route('admin.events.index', ['status' => 'all']) }}">
                                    ทั้งหมด
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'published' ? 'active' : '' }}" href="{{ route('admin.events.index', ['status' => 'published']) }}">
                                    เผยแพร่
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'draft' ? 'active' : '' }}" href="{{ route('admin.events.index', ['status' => 'draft']) }}">
                                    ฉบับร่าง
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'cancelled' ? 'active' : '' }}" href="{{ route('admin.events.index', ['status' => 'cancelled']) }}">
                                    ยกเลิก
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-8">
                    <form id="searchForm" action="{{ route('admin.events.index') }}" method="GET" class="row">
                        <input type="hidden" name="status" value="{{ $status }}">

                        <div class="col-md-7 mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text" name="search" id="liveSearch" class="form-control" placeholder="ค้นหากิจกรรม..." value="{{ $search ?? '' }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-5 text-end">
                            <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                                <i class="fas fa-filter me-1"></i> ตัวกรองขั้นสูง
                            </button>
                        </div>

                        <div class="collapse mt-3 w-100" id="advancedFilters">
                            <div class="card card-body border-light shadow-sm">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label for="start_date" class="form-label small">วันที่เริ่มต้น</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                              value="{{ $startDate ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_date" class="form-label small">วันที่สิ้นสุด</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                              value="{{ $endDate ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="location" class="form-label small">สถานที่</label>
                                        <input type="text" name="location" id="location" class="form-control"
                                              placeholder="ระบุสถานที่" value="{{ $location ?? '' }}">
                                    </div>
                                    <div class="col-12 mt-3 text-end">
                                        <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-secondary me-2">
                                            <i class="fas fa-undo me-1"></i> รีเซ็ตตัวกรอง
                                        </a>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check me-1"></i> ใช้ตัวกรอง
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ตารางกิจกรรม -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="5%">#</th>
                            <th scope="col" width="10%">รูปภาพ</th>
                            <th scope="col" width="22%">ชื่อกิจกรรม</th>
                            <th scope="col" width="15%">วันที่</th>
                            <th scope="col" width="10%">สถานที่</th>
                            <th scope="col" width="10%">ผู้เข้าร่วม</th>
                            <th scope="col" width="7%">สถานะกิจกรรม</th>
                            <th scope="col" width="8%">สถานะเผยแพร่</th>
                            <th scope="col" width="10%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $index => $event)
                        <tr>
                            <td>{{ $events->firstItem() + $index }}</td>
                            <td>
                                <img src="{{ asset('storage/' . ($event->event_image ?? 'events/default-event.png')) }}"
                                     alt="{{ $event->event_name }}"
                                     class="event-image"
                                     onerror="this.src='https://via.placeholder.com/120x80?text=GoFit+Event';">
                            </td>
                            <td>
                                <div class="fw-bold">{{ Str::limit($event->event_name, 40) }}</div>
                                <div class="small text-muted">
                                    @if($event->distance)
                                        <span class="badge bg-info text-white me-1">{{ $event->distance }} กม.</span>
                                    @endif
                                    ผู้สร้าง: {{ $event->creator ? $event->creator->firstname.' '.$event->creator->lastname : 'admin' }}
                                </div>
                            </td>
                            <td>
                                <div>เริ่ม: {{ \Carbon\Carbon::parse($event->start_datetime)->thaiDate() }} น.</div>
                                <div>สิ้นสุด: {{ \Carbon\Carbon::parse($event->end_datetime)->thaiDate() }} น.</div>
                            </td>
                            <td>{{ Str::limit($event->location, 20) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">{{ $event->activeParticipants()->count() }}</div>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        @php
                                            $percentage = $event->max_participants > 0
                                                ? min(100, round(($event->activeParticipants()->count() / $event->max_participants) * 100))
                                                : 0;
                                        @endphp
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"
                                             aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="small text-muted mt-1">{{ $event->max_participants > 0 ? 'สูงสุด: ' . $event->max_participants : 'ไม่จำกัด' }}</div>
                            </td>
                            <td>
                                @if($event->end_datetime < now())
                                    <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                                @elseif($event->start_datetime <= now() && $event->end_datetime >= now())
                                    <span class="badge bg-success">กำลังดำเนินการ</span>
                                @else
                                    <span class="badge bg-info text-white">รอดำเนินการ</span>
                                @endif
                            </td>
                            <td>
                                @if($event->status == 'published')
                                    <span class="badge bg-primary">เผยแพร่</span>
                                @elseif($event->status == 'draft')
                                    <span class="badge bg-warning text-dark">ฉบับร่าง</span>
                                @elseif($event->status == 'cancelled')
                                    <span class="badge bg-danger">ยกเลิก</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.events.show', $event) }}" class="btn event-action-btn btn-info" title="ดูรายละเอียด">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn event-action-btn btn-warning" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn event-action-btn btn-danger delete-event"
                                    data-event-id="{{ $event->event_id }}"
                                    data-event-name="{{ $event->event_name }}"
                                    title="ลบ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>ไม่พบกิจกรรมที่ตรงกับเงื่อนไข</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $events->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<!-- jQuery UI for autocomplete -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ปรับปรุงการค้นหาโดยใช้ Submit Form แทนที่จะเป็น live search
        const searchInput = document.getElementById('liveSearch');
        const searchForm = document.getElementById('searchForm');
        const searchButton = document.querySelector('button[type="submit"]');
        const advancedFilterToggle = document.querySelector('[data-bs-target="#advancedFilters"]');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const locationInput = document.getElementById('location');

        // Initialize advanced filters functionality
        if (startDateInput.value || endDateInput.value || locationInput.value) {
            console.log('Detected filter values, showing advanced filter panel');
            // Automatically open the advanced filters if they have values
            const advancedFilters = document.getElementById('advancedFilters');
            // Check if Bootstrap is available
            if (typeof bootstrap !== 'undefined') {
                const bsCollapse = new bootstrap.Collapse(advancedFilters);
                bsCollapse.show();
            } else {
                // Fallback if Bootstrap JS is not loaded
                advancedFilters.classList.add('show');
            }
        }

        // 1. การกด Enter จะทำการค้นหา
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.submit();
            }
        });

        // 2. การคลิกปุ่มค้นหาจะทำการค้นหา
        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            searchForm.submit();
        });

        // นำ jQuery Autocomplete มาใช้
        if (window.jQuery && $.ui) {
            // Try to fetch from the test route to verify everything is working
            $.ajax({
                url: "{{ route('admin.events.search.test') }}",
                dataType: "json",
                success: function(data) {
                    console.log('Test route successful:', data);
                },
                error: function(xhr, status, error) {
                    console.error('Test route error:', status, error);
                }
            });

            $(searchInput).autocomplete({
                source: function(request, response) {
                    console.log('Autocomplete URL:', "{{ route('admin.events.search') }}");
                    $.ajax({
                        url: "{{ route('admin.events.search') }}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function(xhr, status, error) {
                            console.error('Autocomplete error:', status, error);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    searchInput.value = ui.item.value;
                    searchForm.submit();
                    return false;
                }
            });
        }

        // จัดการปุ่มลบกิจกรรม
        const deleteButtons = document.querySelectorAll('.delete-event');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                const eventName = this.getAttribute('data-event-name');
                const participants = parseInt(this.getAttribute('data-participants'));

                Swal.fire({
                    title: 'ยืนยันการลบกิจกรรม',
                    html: `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            คำเตือน: การลบกิจกรรมไม่สามารถกู้คืนได้ และจะทำให้ข้อมูลการลงทะเบียนทั้งหมดถูกลบไปด้วย
                        </div>
                        <p>คุณแน่ใจหรือไม่ที่จะลบกิจกรรม "<strong>${eventName}</strong>"?</p>
                        <p>หากมีผู้ลงทะเบียนแล้ว (${participants} คน) ระบบจะส่งการแจ้งเตือนให้ผู้ใช้ทราบถึงการยกเลิก</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2DC679',
                    cancelButtonColor: '#FFFFFF',
                    confirmButtonText: 'ใช่, ลบกิจกรรม',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // สร้าง form สำหรับ submit แบบ POST พร้อม method DELETE
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ url('/admin/events') }}/${eventId}`;
                        form.style.display = 'none';

                        // เพิ่ม CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        // เพิ่ม method DELETE
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        // แนบ form เข้ากับเอกสารและส่ง
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
