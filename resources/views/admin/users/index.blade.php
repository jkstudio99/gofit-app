@extends('layouts.admin')

@section('title', 'จัดการผู้ใช้งาน')

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
        background-color: #007bff;
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

    /* SweetAlert2 Custom Styles */
    .swal2-styled.swal2-confirm {
        background-color: #2DC679 !important;
        border-radius: 0.5rem !important;
        font-weight: 500 !important;
        padding: 0.75rem 1.5rem !important;
        box-shadow: 0 5px 10px rgba(45, 198, 121, 0.25) !important;
    }

    .swal2-styled.swal2-confirm:hover {
        background-color: #24A664 !important;
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

    .table th {
        font-weight: 600;
    }
    .pagination {
        margin-bottom: 0;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">จัดการผู้ใช้งาน</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>เพิ่มผู้ใช้ใหม่
        </a>
        </div>

    <!-- ตัวกรองสถานะ -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="status-filter">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') == 'all' || !request('status') ? 'active' : '' }}"
                                   href="{{ route('admin.users.index', ['status' => 'all']) }}">
                                    ทั้งหมด
                                </a>
                            </li>
                            @foreach($userStatuses as $status)
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') == $status->user_status_id ? 'active' : '' }}"
                                   href="{{ route('admin.users.index', ['status' => $status->user_status_id]) }}">
                                    {{ $status->user_status_name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-8">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="row">
                        @if(request('status') && request('status') != 'all')
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif

                        <div class="col-md-7 mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                       placeholder="ค้นหาชื่อผู้ใช้, อีเมล, ชื่อ-นามสกุล..."
                                       value="{{ request('search') }}">
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
                                    <div class="col-md-6">
                                        <label class="form-label small">ประเภทผู้ใช้</label>
                                        <select name="type" class="form-select">
                                            <option value="all" {{ request('type') == 'all' || !request('type') ? 'selected' : '' }}>ประเภทผู้ใช้ทั้งหมด</option>
                                            @foreach($userTypes as $type)
                                                <option value="{{ $type->user_type_id }}" {{ request('type') == $type->user_type_id ? 'selected' : '' }}>
                                                    {{ $type->user_typename }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">สถานะผู้ใช้</label>
                                        <select name="status" class="form-select">
                                            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>สถานะทั้งหมด</option>
                                            @foreach($userStatuses as $status)
                                                <option value="{{ $status->user_status_id }}" {{ request('status') == $status->user_status_id ? 'selected' : '' }}>
                                                    {{ $status->user_status_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 mt-3 text-end">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary me-2">
                                            <i class="fas fa-undo me-1"></i> รีเซ็ตตัวกรอง
                                        </a>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-search me-1"></i> ค้นหา
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



    <!-- ผลลัพธ์การค้นหา -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title m-0">
                <i class="fas fa-users me-2 text-primary"></i>รายชื่อผู้ใช้งานทั้งหมด
                @if(request('search') || request('type') != 'all' || request('status') != 'all')
                <span class="badge bg-success ms-2">
                    <i class="fas fa-filter me-1"></i> กำลังแสดงผลลัพธ์: {{ $users->count() }} รายการ
                </span>
                @endif
            </h5>
            <span class="badge bg-info rounded-pill">
                <i class="fas fa-user-check me-1"></i> ผู้ใช้ทั้งหมด: {{ $users->total() }} คน
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px" class="text-center">#</th>
                            <th>ข้อมูลผู้ใช้</th>
                            <th>ประเภท</th>
                            <th>สถานะ</th>
                        <th>วันที่สมัคร</th>
                            <th style="width: 140px" class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                    <tr>
                            <td class="text-center">{{ $users->firstItem() + $key }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        @if($user->profile_image)
                                            <img src="{{ asset('profile_images/' . $user->profile_image) }}" class="rounded-circle" width="48" height="48" alt="Profile" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0 fw-semibold">{{ $user->username }}</h6>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                        <div class="text-muted small">{{ $user->firstname }} {{ $user->lastname }}</div>
                                    </div>
                                </div>
                            </td>
                        <td>
                            @if($user->user_type_id == 1)
                                <span class="badge bg-primary">ผู้ใช้ทั่วไป</span>
                            @else
                                <span class="badge bg-admin">ผู้ดูแลระบบ</span>
                            @endif
                        </td>
                            <td>
                                @if($user->user_status_id == 1)
                                    <span class="badge badge-outline-success"><i class="fas fa-check-circle me-1"></i>ใช้งาน</span>
                                @else
                                    <span class="badge badge-outline-danger"><i class="fas fa-ban me-1"></i>ระงับการใช้งาน</span>
                                @endif
                            </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="d-flex">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info me-1" title="ดูข้อมูล">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning me-1" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.users.reset-password', $user) }}" class="btn btn-sm btn-secondary me-1" title="รีเซ็ตรหัสผ่าน">
                                    <i class="fas fa-key"></i>
                                </a>
                                @if(Auth::id() != $user->user_id)
                                <button type="button" class="btn btn-sm btn-danger delete-user"
                                        title="ลบ" data-user-id="{{ $user->user_id }}"
                                        data-username="{{ $user->username }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

            @if($users->isEmpty())
            <div class="text-center p-5">
                <div class="text-muted mb-3">
                    <i class="fas fa-users fa-4x"></i>
                </div>
                <h5>ไม่พบข้อมูลผู้ใช้งาน</h5>
                <p class="text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</p>
            </div>
            @endif
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-center">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
    <script>
        // จัดการปุ่มลบผู้ใช้งาน
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-user');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const username = this.getAttribute('data-username');

                    Swal.fire({
                        title: 'ยืนยันการลบผู้ใช้',
                        html: `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                คำเตือน: การลบผู้ใช้จะทำให้ข้อมูลทั้งหมดของผู้ใช้คนนี้ถูกลบไปด้วย ซึ่งรวมถึง:
                                <ul class="mt-2">
                                    <li>ข้อมูลกิจกรรมการวิ่ง</li>
                                    <li>การลงทะเบียนเข้าร่วมกิจกรรม</li>
                                    <li>เป้าหมายการออกกำลังกาย</li>
                                    <li>เหรียญตราและรางวัลที่ได้รับ</li>
                                </ul>
                            </div>
                            <p>คุณแน่ใจหรือไม่ที่จะลบผู้ใช้ "<strong>${username}</strong>"?</p>
                            <p>การกระทำนี้ไม่สามารถเรียกคืนได้</p>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#FFFFFF',
                        confirmButtonText: 'ใช่, ลบผู้ใช้',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // สร้าง form สำหรับ submit แบบ POST พร้อม method DELETE
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/admin/users/${userId}`;

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
            });

            // สคริปต์สำหรับการแสดง/ซ่อนตัวกรองขั้นสูง
            const hasAdvancedFilters = {{ (request('type') && request('type') != 'all') || (request('status') && request('status') != 'all') ? 'true' : 'false' }};

            if (hasAdvancedFilters) {
                const advancedFilters = document.getElementById('advancedFilters');
                if (advancedFilters) {
                    new bootstrap.Collapse(advancedFilters).show();
                }
            }
        });
    </script>
@stop
