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

    /* Button styling for user management actions - matching badges style */
    .btn-info.badge-action-btn {
        background-color: #3B82F6 !important;
        border-color: #3B82F6 !important;
        color: white !important;
    }

    .btn-info.badge-action-btn:hover {
        background-color: #2563EB !important;
        border-color: #2563EB !important;
    }

    .btn-warning.badge-action-btn {
        background-color: #F59E0B !important;
        border-color: #F59E0B !important;
    }

    .btn-warning.badge-action-btn:hover {
        background-color: #D97706 !important;
        border-color: #D97706 !important;
    }

    .btn-secondary.badge-action-btn {
        background-color: #6B7280 !important;
        border-color: #6B7280 !important;
    }

    .btn-secondary.badge-action-btn:hover {
        background-color: #4B5563 !important;
        border-color: #4B5563 !important;
    }

    .btn-danger.badge-action-btn {
        background-color: #EF4444 !important;
        border-color: #EF4444 !important;
    }

    .btn-danger.badge-action-btn:hover {
        background-color: #DC2626 !important;
        border-color: #DC2626 !important;
    }

    .btn-danger i, .btn-danger.badge-action-btn i,
    .btn-info.badge-action-btn i,
    .btn-secondary.badge-action-btn i {
        color: white !important;
    }

    /* Make buttons circular and ensure proper icon alignment */
    .badge-action-btn {
        border-radius: 50% !important;
        width: 36px !important;
        height: 36px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 3px !important;
    }

    .badge-action-btn i {
        font-size: 0.875rem !important;
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
                    <div class="row">
                        @if(request('status') && request('status') != 'all')
                        <input type="hidden" id="status_filter" value="{{ request('status') }}">
                        @else
                        <input type="hidden" id="status_filter" value="all">
                        @endif

                        <div class="col-md-7 mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text" id="live_search" class="form-control"
                                       placeholder="ค้นหาชื่อผู้ใช้, อีเมล, ชื่อ-นามสกุล..."
                                       value="{{ request('search') }}">
                                <button type="button" class="btn btn-primary">
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
                                        <select name="type" id="type_filter" class="form-select">
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
                                        <select name="status" id="status_filter_advanced" class="form-select">
                                            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>สถานะทั้งหมด</option>
                                            @foreach($userStatuses as $status)
                                                <option value="{{ $status->user_status_id }}" {{ request('status') == $status->user_status_id ? 'selected' : '' }}>
                                                    {{ $status->user_status_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 mt-3 text-end">
                                        <button type="button" id="reset_filters" class="btn btn-sm btn-secondary me-2">
                                            <i class="fas fa-undo me-1"></i> รีเซ็ตตัวกรอง
                                        </button>
                                        <button type="button" id="apply_filters" class="btn btn-sm btn-primary">
                                            <i class="fas fa-search me-1"></i> ค้นหา
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ผลลัพธ์การค้นหา -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title m-0">
                <i class="fas fa-users me-2 text-primary"></i>รายชื่อผู้ใช้งานทั้งหมด
                <span id="search_results_count" class="badge bg-success ms-2 {{ request('search') || request('type') != 'all' || request('status') != 'all' ? '' : 'd-none' }}">
                    <i class="fas fa-filter me-1"></i> กำลังแสดงผลลัพธ์: {{ $users->count() }} รายการ
                </span>
            </h5>
            <span class="badge bg-info rounded-pill">
                <i class="fas fa-user-check me-1"></i> ผู้ใช้ทั้งหมด: <span id="total_users">{{ $users->total() }}</span> คน
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
                    <tbody id="users_table_body">
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
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info badge-action-btn me-2" title="ดูข้อมูล">
                                    <i class="fas fa-eye"></i>
                                </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning badge-action-btn me-2" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </a>
                                    <a href="{{ route('admin.users.reset-password', $user) }}" class="btn btn-sm btn-secondary badge-action-btn me-2" title="รีเซ็ตรหัสผ่าน">
                                    <i class="fas fa-key"></i>
                                </a>
                                @if(Auth::id() != $user->user_id)
                                    <button type="button" class="btn btn-sm btn-danger badge-action-btn delete-user"
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

            <div id="no_results" class="text-center p-5 d-none">
                <div class="text-muted mb-3">
                    <i class="fas fa-users fa-4x"></i>
                </div>
                <h5>ไม่พบข้อมูลผู้ใช้งาน</h5>
                <p class="text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</p>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-center" id="pagination_container">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // จัดการปุ่มลบผู้ใช้งาน
            const setupDeleteButtons = function() {
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
            };

            // เรียกใช้งานฟังก์ชันตั้งค่าปุ่มลบ
            setupDeleteButtons();

            // สำหรับฟีเจอร์ Live Search

            // ตั้งค่าตัวแปรสำหรับ throttle เพื่อไม่ให้ส่งรีเควสบ่อยเกินไป
            let searchTimeout = null;
            const searchDelay = 500; // 500ms

            // ฟังก์ชันสำหรับดึงข้อมูลผู้ใช้ด้วย AJAX
            const fetchUsers = function(searchQuery = '', status = 'all', type = 'all', page = 1) {
                // แสดง loading spinner หรือข้อความ
                const tableBody = document.getElementById('users_table_body');
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">กำลังโหลด...</span>
                            </div>
                            <p class="mt-2 mb-0">กำลังค้นหาข้อมูล...</p>
                        </td>
                    </tr>
                `;

                // สร้าง URL สำหรับ API request
                const url = new URL(`${window.location.origin}/admin/api/users`);
                url.searchParams.append('search', searchQuery);
                url.searchParams.append('status', status);
                url.searchParams.append('type', type);
                url.searchParams.append('page', page);

                // ส่ง AJAX request
                fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // อัพเดตตาราง
                    updateTable(data);

                    // อัพเดตการแสดงผลจำนวนข้อมูล
                    const countBadge = document.getElementById('search_results_count');
                    if (data.users.data.length > 0) {
                        countBadge.textContent = `กำลังแสดงผลลัพธ์: ${data.users.data.length} รายการ`;
                        countBadge.classList.remove('d-none');
                    } else {
                        countBadge.classList.add('d-none');
                    }

                    // อัพเดตจำนวนผู้ใช้ทั้งหมด
                    document.getElementById('total_users').textContent = data.users.total;

                    // อัพเดตการแสดง pagination
                    updatePagination(data.users);
                })
                .catch(error => {
                    console.error('เกิดข้อผิดพลาดในการค้นหา:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-4 text-danger">
                                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                <p>เกิดข้อผิดพลาดในการค้นหา กรุณาลองใหม่อีกครั้ง</p>
                            </td>
                        </tr>
                    `;
                });
            };

            // ฟังก์ชันอัพเดตตาราง
            const updateTable = function(data) {
                const tableBody = document.getElementById('users_table_body');
                const noResults = document.getElementById('no_results');

                // ถ้าไม่มีข้อมูล
                if (data.users.data.length === 0) {
                    tableBody.innerHTML = '';
                    noResults.classList.remove('d-none');
                    return;
                }

                // ถ้ามีข้อมูล
                noResults.classList.add('d-none');

                // สร้าง HTML สำหรับแต่ละแถว
                let html = '';
                data.users.data.forEach((user, index) => {
                    let profileImage = '';
                    if (user.profile_image) {
                        profileImage = `<img src="/profile_images/${user.profile_image}" class="rounded-circle" width="48" height="48" alt="Profile" style="object-fit: cover;">`;
                    } else {
                        profileImage = `<div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px;"><i class="fas fa-user"></i></div>`;
                    }

                    let userType = user.user_type_id == 1
                        ? '<span class="badge bg-primary">ผู้ใช้ทั่วไป</span>'
                        : '<span class="badge bg-admin">ผู้ดูแลระบบ</span>';

                    let userStatus = user.user_status_id == 1
                        ? '<span class="badge badge-outline-success"><i class="fas fa-check-circle me-1"></i>ใช้งาน</span>'
                        : '<span class="badge badge-outline-danger"><i class="fas fa-ban me-1"></i>ระงับการใช้งาน</span>';

                    let deleteButton = '';
                    if (user.user_id != {{ Auth::id() }}) {
                        deleteButton = `
                            <button type="button" class="btn btn-sm btn-danger badge-action-btn delete-user"
                                title="ลบ" data-user-id="${user.user_id}"
                                data-username="${user.username}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }

                    const createdAt = new Date(user.created_at);
                    const formattedDate = createdAt.toLocaleDateString('th-TH', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    html += `
                        <tr>
                            <td class="text-center">${data.users.from + index}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        ${profileImage}
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0 fw-semibold">${user.username}</h6>
                                        <div class="text-muted small">${user.email}</div>
                                        <div class="text-muted small">${user.firstname} ${user.lastname}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${userType}</td>
                            <td>${userStatus}</td>
                            <td>${formattedDate}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="/admin/users/${user.user_id}" class="btn btn-sm btn-info badge-action-btn me-2" title="ดูข้อมูล">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/users/${user.user_id}/edit" class="btn btn-sm btn-warning badge-action-btn me-2" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/admin/users/${user.user_id}/reset-password" class="btn btn-sm btn-secondary badge-action-btn me-2" title="รีเซ็ตรหัสผ่าน">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    ${deleteButton}
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;

                // เรียกใช้งานฟังก์ชันตั้งค่าปุ่มลบอีกครั้ง
                setupDeleteButtons();
            };

            // ฟังก์ชันอัพเดต pagination
            const updatePagination = function(paginationData) {
                const paginationContainer = document.getElementById('pagination_container');

                if (paginationData.last_page <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                let html = '<ul class="pagination">';

                // Previous link
                html += paginationData.prev_page_url
                    ? `<li class="page-item"><a class="page-link" href="#" data-page="${paginationData.current_page - 1}">«</a></li>`
                    : `<li class="page-item disabled"><span class="page-link">«</span></li>`;

                // Page numbers
                for (let i = 1; i <= paginationData.last_page; i++) {
                    if (i === paginationData.current_page) {
                        html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                    } else {
                        html += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                    }
                }

                // Next link
                html += paginationData.next_page_url
                    ? `<li class="page-item"><a class="page-link" href="#" data-page="${paginationData.current_page + 1}">»</a></li>`
                    : `<li class="page-item disabled"><span class="page-link">»</span></li>`;

                html += '</ul>';

                paginationContainer.innerHTML = html;

                // เพิ่ม event listeners สำหรับปุ่ม pagination
                const pageLinks = paginationContainer.querySelectorAll('.page-link');
                pageLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();

                        const page = this.getAttribute('data-page');
                        if (page) {
                            const searchQuery = document.getElementById('live_search').value;
                            const status = document.getElementById('status_filter_advanced').value;
                            const type = document.getElementById('type_filter').value;

                            fetchUsers(searchQuery, status, type, page);
                        }
                    });
                });
            };

            // Event listener สำหรับช่องค้นหา
            const searchInput = document.getElementById('live_search');
            searchInput.addEventListener('input', function() {
                // ยกเลิก timeout ก่อนหน้า
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }

                // สร้าง timeout ใหม่
                searchTimeout = setTimeout(() => {
                    const searchQuery = this.value;
                    const status = document.getElementById('status_filter_advanced').value;
                    const type = document.getElementById('type_filter').value;

                    fetchUsers(searchQuery, status, type);
                }, searchDelay);
            });

            // Event listener สำหรับปุ่มค้นหาขั้นสูง
            document.getElementById('apply_filters').addEventListener('click', function() {
                const searchQuery = document.getElementById('live_search').value;
                const status = document.getElementById('status_filter_advanced').value;
                const type = document.getElementById('type_filter').value;

                fetchUsers(searchQuery, status, type);
            });

            // Event listener สำหรับปุ่มรีเซ็ตตัวกรอง
            document.getElementById('reset_filters').addEventListener('click', function() {
                // รีเซ็ตค่าในฟอร์ม
                document.getElementById('live_search').value = '';
                document.getElementById('status_filter_advanced').value = 'all';
                document.getElementById('type_filter').value = 'all';

                // ดึงข้อมูลใหม่
                fetchUsers('', 'all', 'all');
            });

            // แก้ไขการแสดง/ซ่อนตัวกรองขั้นสูง
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
