@extends('layouts.admin')

@section('title', 'รายละเอียดกิจกรรม')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .event-image {
        max-height: 300px;
        width: 100%;
        object-fit: cover;
        border-radius: 0.5rem;
    }
    .participant-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
    }
    .status-label {
        width: 100px;
    }
    .nav-pills .nav-link.active {
        background-color: #007bff;
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
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">รายละเอียดกิจกรรม</h1>
                <div>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                    </a>
                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> แก้ไข
                    </a>
                    <button type="button" class="btn btn-danger" id="deleteEventBtn">
                        <i class="fas fa-trash me-1"></i> ลบกิจกรรม
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- รายละเอียดกิจกรรม -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    @if($event->event_image)
                        <img src="{{ asset(str_replace('storage/', '', 'storage/' . $event->event_image)) }}" alt="{{ $event->event_name }}" class="event-image mb-3" onerror="this.src='{{ asset('storage/events/default-event.png') }}';">
                    @else
                        <div class="bg-light d-flex justify-content-center align-items-center mb-3" style="height: 200px; border-radius: 0.5rem;">
                            <i class="fas fa-image fa-3x text-secondary"></i>
                        </div>
                    @endif

                    <h2 class="h4 mb-3">{{ $event->event_name }}</h2>

                    <div class="mb-3">
                        @if($event->end_datetime < now())
                            <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                        @elseif($event->start_datetime <= now() && $event->end_datetime >= now())
                            <span class="badge bg-success">กำลังดำเนินการ</span>
                        @elseif($event->status == 'published')
                            <span class="badge bg-primary">เผยแพร่</span>
                        @elseif($event->status == 'draft')
                            <span class="badge bg-info">ฉบับร่าง</span>
                        @elseif($event->status == 'cancelled')
                            <span class="badge bg-danger">ยกเลิก</span>
                        @endif

                        @if($event->distance)
                            <span class="badge bg-info text-dark">{{ $event->distance }} กม.</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                        <strong>วันที่:</strong>
                        <div class="mt-1">
                            <div>เริ่ม: {{ \Carbon\Carbon::parse($event->start_datetime)->thaiDate() }} น.</div>
                            <div>สิ้นสุด: {{ \Carbon\Carbon::parse($event->end_datetime)->thaiDate() }} น.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                        <strong>สถานที่:</strong>
                        <div class="mt-1">{{ $event->location }}</div>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-users me-2 text-success"></i>
                        <strong>ผู้เข้าร่วม:</strong>
                        <div class="mt-1">
                            <div class="d-flex align-items-center">
                                <div class="me-2">{{ $event->activeParticipants()->count() }} /
                                    {{ $event->max_participants > 0 ? $event->max_participants : 'ไม่จำกัด' }}</div>
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
                        </div>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-info-circle me-2 text-info"></i>
                        <strong>รายละเอียด:</strong>
                        <div class="mt-2 event-description">
                            {!! $event->event_desc !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- รายชื่อผู้เข้าร่วม -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i> รายชื่อผู้เข้าร่วมกิจกรรม
                        </h5>
                        <a href="{{ route('admin.events.export', $event) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel me-1"></i> ดาวน์โหลด CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- จำนวนตามสถานะ -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="card bg-primary bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $registeredCount }}</h3>
                                    <p class="mb-0">ลงทะเบียน</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="card bg-success bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $attendedCount }}</h3>
                                    <p class="mb-0">เข้าร่วมแล้ว</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="card bg-danger bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $cancelledCount }}</h3>
                                    <p class="mb-0">ยกเลิก</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $participants->count() }}</h3>
                                    <p class="mb-0">ทั้งหมด</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ค้นหาและกรอง -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="input-group">
                                <input type="text" id="searchParticipants" class="form-control" placeholder="ค้นหาผู้เข้าร่วม...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <ul class="nav nav-pills" id="participantsFilter">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#" data-filter="all">ทั้งหมด</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-filter="registered">ลงทะเบียน</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-filter="attended">เข้าร่วมแล้ว</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-filter="cancelled">ยกเลิก</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- ตารางผู้เข้าร่วม -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="participantsTable">
                            <thead>
                                <tr>
                                    <th scope="col" width="5%">#</th>
                                    <th scope="col" width="15%">ผู้ใช้</th>
                                    <th scope="col" width="25%">ชื่อ-นามสกุล</th>
                                    <th scope="col" width="15%">วันที่ลงทะเบียน</th>
                                    <th scope="col" width="15%">สถานะ</th>
                                    <th scope="col" width="25%">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($participants as $index => $participant)
                                <tr data-status="{{ $participant->status }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($participant->user)
                                            @if($participant->user->profile_image)
                                                <img src="{{ asset('storage/' . $participant->user->profile_image) }}"
                                                     class="participant-img me-2" alt="{{ $participant->user->username }}"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/default-profile.jpg') }}';">
                                            @else
                                                <div class="participant-img me-2 bg-secondary d-flex justify-content-center align-items-center text-white">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            <span>{{ $participant->user->username }}</span>
                                            @else
                                                <div class="participant-img me-2 bg-danger d-flex justify-content-center align-items-center text-white">
                                                    <i class="fas fa-user-slash"></i>
                                                </div>
                                                <span>ผู้ใช้ถูกลบไปแล้ว</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($participant->user)
                                            {{ $participant->user->firstname }} {{ $participant->user->lastname }}
                                        @else
                                            <span class="text-muted">ไม่พบข้อมูล</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($participant->registered_at)->thaiDate() }} น.</td>
                                    <td>
                                        @if($participant->status == 'registered')
                                            <span class="badge bg-primary status-label">ลงทะเบียนแล้ว</span>
                                        @elseif($participant->status == 'attended')
                                            <span class="badge bg-success status-label">เข้าร่วมแล้ว</span>
                                        @elseif($participant->status == 'cancelled')
                                            <span class="badge bg-secondary status-label">ยกเลิกแล้ว</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @if($participant->user)
                                            <a href="{{ route('profile.show', $participant->user->username) }}"
                                               class="btn btn-sm btn-info me-1" title="ดูโปรไฟล์">
                                                <i class="fas fa-user"></i>
                                            </a>
                                            @else
                                                <button class="btn btn-sm btn-secondary me-1" disabled title="ไม่พบผู้ใช้">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            @endif
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                                        id="statusDropdown{{ $participant->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                    เปลี่ยนสถานะ
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="statusDropdown{{ $participant->id }}">
                                                    <li>
                                                        <form action="{{ route('admin.events.participants.status', ['event' => $event, 'user' => $participant->user_id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="registered">
                                                            <button type="submit" class="dropdown-item {{ $participant->status == 'registered' ? 'active' : '' }}">
                                                                ลงทะเบียนแล้ว
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.events.participants.status', ['event' => $event, 'user' => $participant->user_id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="attended">
                                                            <button type="submit" class="dropdown-item {{ $participant->status == 'attended' ? 'active' : '' }}">
                                                                เข้าร่วมแล้ว
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.events.participants.status', ['event' => $event, 'user' => $participant->user_id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item {{ $participant->status == 'cancelled' ? 'active' : '' }}">
                                                                ยกเลิก
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users-slash fa-3x mb-3"></i>
                                            <p>ยังไม่มีผู้ลงทะเบียนเข้าร่วมกิจกรรมนี้</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ค้นหาผู้เข้าร่วม
        const searchInput = document.getElementById('searchParticipants');
        const table = document.getElementById('participantsTable');
        const rows = table.querySelectorAll('tbody tr');

        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const statusFilter = document.querySelector('#participantsFilter .nav-link.active').dataset.filter;

                const matchesSearch = rowText.includes(searchText);
                const matchesFilter = statusFilter === 'all' || row.dataset.status === statusFilter;

                row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
            });
        });

        // กรองตามสถานะ
        const filterLinks = document.querySelectorAll('#participantsFilter .nav-link');

        filterLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // เอาคลาส active ออกจากลิงก์ทั้งหมด
                filterLinks.forEach(l => l.classList.remove('active'));

                // เพิ่มคลาส active ให้ลิงก์ที่คลิก
                this.classList.add('active');

                const filter = this.dataset.filter;
                const searchText = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    const matchesSearch = rowText.includes(searchText);
                    const matchesFilter = filter === 'all' || row.dataset.status === filter;

                    row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
                });
            });
        });

        // จัดการการลบกิจกรรมด้วย SweetAlert2
        const deleteEventBtn = document.getElementById('deleteEventBtn');

        deleteEventBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'ยืนยันการลบกิจกรรม',
                html: `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        คำเตือน: การลบกิจกรรมไม่สามารถกู้คืนได้ และจะทำให้ข้อมูลการลงทะเบียนทั้งหมดถูกลบไปด้วย
                    </div>
                    <p>คุณแน่ใจหรือไม่ที่จะลบกิจกรรม "<strong>{{ $event->event_name }}</strong>"?</p>
                    <p>หากมีผู้ลงทะเบียนแล้ว ({{ $participants->count() }} คน) ระบบจะส่งการแจ้งเตือนให้ผู้ใช้ทราบถึงการยกเลิก</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2DC679',
                cancelButtonColor: '#FFFFFF',
                confirmButtonText: 'ใช่, ลบกิจกรรม',
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    confirmButton: 'swal2-confirm-gofit',
                    cancelButton: 'swal2-cancel-gofit'
                },
                buttonsStyling: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // สร้าง form สำหรับ submit แบบ POST พร้อม method DELETE
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.events.destroy', $event) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);

                    form.submit();
                }
            });
        });

        // เพิ่ม function เพื่อใช้ SweetAlert สำหรับฟอร์มอัพเดทสถานะผู้เข้าร่วม
        const statusForms = document.querySelectorAll('form[action*="participants"]');

        statusForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const statusValue = this.querySelector('input[name="status"]').value;
                let statusText = 'ลงทะเบียนแล้ว';
                let statusIcon = 'info';

                if (statusValue === 'attended') {
                    statusText = 'เข้าร่วมแล้ว';
                    statusIcon = 'success';
                } else if (statusValue === 'cancelled') {
                    statusText = 'ยกเลิก';
                    statusIcon = 'warning';
                }

                Swal.fire({
                    title: 'ยืนยันการเปลี่ยนสถานะ',
                    text: `คุณต้องการเปลี่ยนสถานะเป็น "${statusText}" ใช่หรือไม่?`,
                    icon: statusIcon,
                    showCancelButton: true,
                    confirmButtonColor: '#2DC679',
                    cancelButtonColor: '#FFFFFF',
                    confirmButtonText: 'ใช่, เปลี่ยนสถานะ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
