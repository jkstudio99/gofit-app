@extends('layouts.admin')

@section('title', 'จัดการการแลกรางวัล')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    /* แก้ไข SweetAlert z-index */
    .swal2-container {
        z-index: 999999 !important;
    }
    .search-box {
        border-radius:8px;
        border: 1px solid #e0e0e0;
        padding-left: 20px;
    }

    .search-box:focus {
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
        border-color: #2DC679;
    }

    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover, .filter-badge.active {
        background-color: #2DC679 !important;
        color: white;
    }

    /* Redeem status indicator */
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .status-pending {
        background-color: #ffc107;
    }

    .status-completed {
        background-color: #28a745;
    }

    .status-cancelled {
        background-color: #dc3545;
    }

    /* Loading indicator */
    .loading-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">ประวัติการแลกรางวัลล่าสุด</h1>
        <a href="{{ route('admin.rewards') }}" class="btn btn-outline-primary">
            <i class="fas fa-gift me-1"></i> จัดการรางวัล
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="search-input" class="form-control search-box"
                               placeholder="ค้นหาตามชื่อผู้ใช้หรือรางวัล..." value="{{ request('search') }}">
                        <button class="btn btn-primary" id="search-button" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="status-filter" class="form-select">
                        <option value="">สถานะทั้งหมด</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="sort-filter" class="form-select">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>เรียง: ล่าสุด</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เรียง: เก่าสุด</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Tags -->
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-{{ request('status') ? 'light text-dark' : 'primary' }} py-2 px-3 filter-badge status-filter" data-status="">
                <i class="fas fa-list me-1"></i> ทั้งหมด
            </span>

            <span class="badge bg-{{ request('status') == 'pending' ? 'warning' : 'light text-dark' }} py-2 px-3 filter-badge status-filter" data-status="pending">
                <i class="fas fa-clock me-1"></i> รอดำเนินการ
            </span>

            <span class="badge bg-{{ request('status') == 'completed' ? 'success' : 'light text-dark' }} py-2 px-3 filter-badge status-filter" data-status="completed">
                <i class="fas fa-check-circle me-1"></i> เสร็จสิ้น
            </span>

            <span class="badge bg-{{ request('status') == 'cancelled' ? 'danger' : 'light text-dark' }} py-2 px-3 filter-badge status-filter" data-status="cancelled">
                <i class="fas fa-times-circle me-1"></i> ยกเลิก
            </span>
        </div>
    </div>

    <!-- Redeems Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">รายการแลกรางวัลทั้งหมด</h5>
            <span class="badge bg-primary" id="total-count">{{ $redeems->total() }} รายการ</span>
        </div>
        <div class="card-body position-relative">
            <!-- Loading spinner -->
            <div id="loading-spinner" class="loading-container d-none">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">กำลังโหลด...</span>
                    </div>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            </div>

            <!-- Redeems list container -->
            <div id="redeems-list-container">
                @include('admin.redeems.partials.redeems_list')
            </div>
        </div>
        <div class="card-footer clearfix">
            <div id="pagination-container">
                {{ $redeems->links() }}
            </div>
        </div>
    </div>
</div>

<!-- ไม่มี Modal ใช้ SweetAlert แทน -->
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle filter badge clicks
        const statusFilterBadges = document.querySelectorAll('.status-filter');
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        const sortFilter = document.getElementById('sort-filter');
        const searchButton = document.getElementById('search-button');

        // Timer สำหรับ debounce
        let typingTimer;
        const doneTypingInterval = 500; // เวลารอ 500 ms หลังจากพิมพ์เสร็จ

        // Event สำหรับ debounce การพิมพ์
        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            if (searchInput.value) {
                typingTimer = setTimeout(fetchRedeems, doneTypingInterval);
            }
        });

        // ให้ทุก element ที่เป็นตัวกรองทำงานแบบ live search
        statusFilter.addEventListener('change', fetchRedeems);
        sortFilter.addEventListener('change', fetchRedeems);

        // ปุ่มค้นหา
        searchButton.addEventListener('click', fetchRedeems);

        // Status filter badges
        statusFilterBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                const status = this.getAttribute('data-status');
                if (statusFilter) {
                    statusFilter.value = status;
                }
                // อัปเดต UI ของ badges
                statusFilterBadges.forEach(b => {
                    b.classList.remove('bg-primary', 'bg-warning', 'bg-success', 'bg-danger');
                    b.classList.add('bg-light', 'text-dark');
                });

                // กำหนดสีตาม status
                if (status === '') {
                    this.classList.remove('bg-light', 'text-dark');
                    this.classList.add('bg-primary');
                } else if (status === 'pending') {
                    this.classList.remove('bg-light', 'text-dark');
                    this.classList.add('bg-warning');
                } else if (status === 'completed') {
                    this.classList.remove('bg-light', 'text-dark');
                    this.classList.add('bg-success');
                } else if (status === 'cancelled') {
                    this.classList.remove('bg-light', 'text-dark');
                    this.classList.add('bg-danger');
                }

                fetchRedeems();
            });
        });

        // Pagination จะถูกจัดการใน fetchRedeems และจะเป็นแบบ Ajax ด้วย
        document.addEventListener('click', function(e) {
            // ตรวจสอบว่า element ที่ click เป็นลิงก์หน้าหรือไม่
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const href = e.target.closest('a').getAttribute('href');
                if (href) {
                    fetchRedeemsFromUrl(href);
                }
            }
        });

        // ฟังก์ชันดึงรายการแลกรางวัลจากพารามิเตอร์ปัจจุบัน
        function fetchRedeems() {
            const searchValue = searchInput.value.trim();
            const statusValue = statusFilter ? statusFilter.value : '';
            const sortValue = sortFilter ? sortFilter.value : 'newest';

            // แสดง loading spinner
            document.getElementById('loading-spinner').classList.remove('d-none');

            // สร้าง URL พร้อม query parameters
            const url = new URL('{{ route("admin.redeems.api.search") }}');
            if (searchValue) url.searchParams.append('search', searchValue);
            if (statusValue) url.searchParams.append('status', statusValue);
            if (sortValue) url.searchParams.append('sort', sortValue);

            // ส่ง AJAX request
            fetch(url.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // อัปเดต redeems list
                        document.getElementById('redeems-list-container').innerHTML = data.html;

                        // อัปเดต pagination
                        document.getElementById('pagination-container').innerHTML = data.pagination;

                        // อัปเดต count
                        document.getElementById('total-count').textContent = data.count + ' รายการ';

                        // ติดตั้ง event listeners สำหรับปุ่มอัปเดตสถานะ
                        setupStatusButtons();
                    }
                })
                .catch(error => {
                    console.error('Error fetching redeems:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถดึงข้อมูลรายการแลกรางวัลได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonColor: '#dc3545'
                    });
                })
                .finally(() => {
                    // ซ่อน loading spinner
                    document.getElementById('loading-spinner').classList.add('d-none');
                });
        }

        // ฟังก์ชันดึงรายการแลกรางวัลจาก URL ที่กำหนด (สำหรับ pagination)
        function fetchRedeemsFromUrl(url) {
            // แสดง loading spinner
            document.getElementById('loading-spinner').classList.remove('d-none');

            // ส่ง AJAX request
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // อัปเดต redeems list
                        document.getElementById('redeems-list-container').innerHTML = data.html;

                        // อัปเดต pagination
                        document.getElementById('pagination-container').innerHTML = data.pagination;

                        // อัปเดต count
                        document.getElementById('total-count').textContent = data.count + ' รายการ';

                        // ติดตั้ง event listeners สำหรับปุ่มอัปเดตสถานะ
                        setupStatusButtons();
                    }
                })
                .catch(error => {
                    console.error('Error fetching redeems:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถดึงข้อมูลรายการแลกรางวัลได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonColor: '#dc3545'
                    });
                })
                .finally(() => {
                    // ซ่อน loading spinner
                    document.getElementById('loading-spinner').classList.add('d-none');
                });
        }

        // ฟังก์ชันตั้งค่า event listeners สำหรับปุ่มอัปเดตสถานะ
        function setupStatusButtons() {
            // ใช้ SweetAlert แทน Modal
            document.querySelectorAll('.btn-update-status').forEach(button => {
                button.addEventListener('click', function() {
                    const redeemId = this.getAttribute('data-redeem-id');
                    const status = this.getAttribute('data-status');
                    const isCompleted = status === 'completed';
                    const title = isCompleted ? 'ยืนยันการจัดส่งรางวัล' : 'ยกเลิกการแลกรางวัล';
                    const confirmButtonText = isCompleted ? 'ยืนยันการจัดส่ง' : 'ยกเลิกรางวัล';
                    const confirmButtonColor = isCompleted ? '#28a745' : '#dc3545';
                    const icon = isCompleted ? 'success' : 'warning';

                    Swal.fire({
                        title: title,
                        icon: icon,
                        html: `
                            <form id="updateStatusForm">
                                <div class="mb-3 text-start">
                                    <label for="note" class="form-label">หมายเหตุ (ถ้ามี)</label>
                                    <textarea id="swal-note" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="text-start">
                                    <small class="text-muted">รหัสรายการแลกรางวัล: ${redeemId}</small>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonColor: '#2DC679',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: confirmButtonText,
                        cancelButtonText: 'ยกเลิก',
                        focusConfirm: false,
                        customClass: {
                            container: 'my-swal-container'
                        },
                        didOpen: () => {
                            document.querySelector('.my-swal-container').style.zIndex = "999999";
                        },
                        preConfirm: () => {
                            return {
                                note: document.getElementById('swal-note').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // สร้าง form เพื่อส่งข้อมูล
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `{{ url('admin/redeems') }}/${redeemId}/status`;
                            form.style.display = 'none';

                            // CSRF Token
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = "{{ csrf_token() }}";

                            // Status
                            const statusInput = document.createElement('input');
                            statusInput.type = 'hidden';
                            statusInput.name = 'status';
                            statusInput.value = status;

                            // Note
                            const noteInput = document.createElement('input');
                            noteInput.type = 'hidden';
                            noteInput.name = 'note';
                            noteInput.value = result.value.note;

                            // เพิ่ม input เข้าไปใน form
                            form.appendChild(csrfToken);
                            form.appendChild(statusInput);
                            form.appendChild(noteInput);

                            // เพิ่ม form เข้าไปใน document และส่งข้อมูล
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        }

        // ตั้งค่า event listeners สำหรับปุ่มอัปเดตสถานะเมื่อโหลดหน้าครั้งแรก
        setupStatusButtons();

        // Show flash message if exists
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#28a745',
                customClass: {
                    container: 'my-swal-container'
                },
                didOpen: () => {
                    document.querySelector('.my-swal-container').style.zIndex = "999999";
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc3545',
                customClass: {
                    container: 'my-swal-container'
                },
                didOpen: () => {
                    document.querySelector('.my-swal-container').style.zIndex = "999999";
                }
            });
        @endif
    });
</script>
@endsection
