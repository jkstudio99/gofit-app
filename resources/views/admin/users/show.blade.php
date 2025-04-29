@extends('layouts.admin')

@section('title', 'ข้อมูลผู้ใช้ - ' . $user->username)

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .profile-image {
        max-height: 120px;
        max-width: 120px;
        object-fit: cover;
        border-radius: 50%;
    }
    .badge {
        font-weight: 500;
    }
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        margin-right: 0.5rem;
    }
    .nav-pills .nav-link.active {
        color: #fff;
        background-color: #007bff;
    }
    .tab-pane {
        padding: 1rem 0;
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
</style>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">ข้อมูลผู้ใช้</h1>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> แก้ไข
                    </a>
                    <a href="{{ route('admin.users.reset-password', $user) }}" class="btn btn-info me-2">
                        <i class="fas fa-key me-1"></i> รีเซ็ตรหัสผ่าน
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ข้อมูลผู้ใช้ -->
        <div class="col-lg-4">
            <!-- User Profile Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-4 position-relative">
                    <div class="mb-4 position-relative mx-auto" style="width: 150px; height: 150px;">
                        @if($user->profile_image)
                            <img src="{{ asset('profile_images/' . $user->profile_image) }}" alt="{{ $user->username }}" class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-4x text-secondary"></i>
                            </div>
                        @endif

                        <!-- ปุ่มเปลี่ยนรูปโปรไฟล์ -->
                        <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute" style="bottom: 0; right: 0;" data-bs-toggle="modal" data-bs-target="#changeProfileModal">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>

                    <h3 class="mb-1">{{ $user->firstname }} {{ $user->lastname }}</h3>
                    <h5 class="text-muted mb-3">{{ $user->username }}</h5>

                    <div class="mb-3">
                        @if($user->user_type_id == 1)
                            <span class="badge bg-primary px-3 py-2"><i class="fas fa-user me-1"></i> ผู้ใช้ทั่วไป</span>
                        @else
                            <span class="badge bg-admin px-3 py-2"><i class="fas fa-user-shield me-1"></i> ผู้ดูแลระบบ</span>
                        @endif

                        @if($user->user_status_id == 1)
                            <span class="badge badge-outline-success px-3 py-2 ms-1"><i class="fas fa-check-circle me-1"></i> ใช้งาน</span>
                        @else
                            <span class="badge badge-outline-danger px-3 py-2 ms-1"><i class="fas fa-ban me-1"></i> ระงับการใช้งาน</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-0">{{ $runningActivities->count() }}</h5>
                            <small class="text-muted">กิจกรรม</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">{{ $events->count() }}</h5>
                            <small class="text-muted">อีเวนต์</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">{{ $badges->count() }}</h5>
                            <small class="text-muted">เหรียญตรา</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0"><i class="fas fa-info-circle me-2 text-primary"></i>ข้อมูลส่วนตัว</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-envelope me-2"></i>อีเมล</span>
                            <span>{{ $user->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-phone me-2"></i>เบอร์โทรศัพท์</span>
                            <span>{{ $user->telephone ?? 'ไม่ระบุ' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>วันเกิด</span>
                            <span>{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('d/m/Y') : 'ไม่ระบุ' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-weight me-2"></i>น้ำหนัก</span>
                            <span>{{ $user->weight ? $user->weight . ' กก.' : 'ไม่ระบุ' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-ruler-vertical me-2"></i>ส่วนสูง</span>
                            <span>{{ $user->height ? $user->height . ' ซม.' : 'ไม่ระบุ' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-venus-mars me-2"></i>เพศ</span>
                            <span>
                                @if($user->gender == 'male')
                                    ชาย
                                @elseif($user->gender == 'female')
                                    หญิง
                                @elseif($user->gender == 'other')
                                    อื่นๆ
                                @else
                                    ไม่ระบุ
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- System Information Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0"><i class="fas fa-cogs me-2 text-primary"></i>ข้อมูลระบบ</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-id-card me-2"></i>รหัสผู้ใช้</span>
                            <span>{{ $user->user_id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-calendar-plus me-2"></i>วันที่สมัคร</span>
                            <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-calendar-check me-2"></i>แก้ไขล่าสุด</span>
                            <span>{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบล่าสุด</span>
                            <span>{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d/m/Y H:i') : 'ไม่เคยเข้าสู่ระบบ' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- User Activity Details Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title m-0"><i class="fas fa-chart-line me-2 text-primary"></i>ข้อมูลกิจกรรม</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        ข้อมูลกิจกรรมและประวัติการใช้งานของผู้ใช้
                    </div>

                    <!-- กิจกรรมล่าสุด -->
                    <h6 class="mb-3"><i class="fas fa-running me-2"></i>กิจกรรมการวิ่งล่าสุด</h6>
                    @if($runningActivities->isEmpty())
                        <div class="text-center py-4">
                            <div class="text-muted mb-3">
                                <i class="fas fa-running fa-3x"></i>
                            </div>
                            <p>ผู้ใช้ยังไม่มีประวัติการทำกิจกรรมการวิ่ง</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>วันที่</th>
                                        <th>ระยะทาง</th>
                                        <th>ระยะเวลา</th>
                                        <th>แคลอรี่</th>
                                        <th class="text-center">รายละเอียด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($runningActivities->take(5) as $key => $activity)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($activity->activity_date)->format('d/m/Y') }}</td>
                                            <td>{{ number_format($activity->distance, 2) }} กม.</td>
                                            <td>{{ $activity->duration }} นาที</td>
                                            <td>{{ number_format($activity->calories) }} แคล</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.activities.show', $activity->running_activity_id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal เปลี่ยนรูปโปรไฟล์ -->
<div class="modal fade" id="changeProfileModal" tabindex="-1" aria-labelledby="changeProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeProfileModalLabel">เปลี่ยนรูปโปรไฟล์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.update-profile-image', $user->user_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">เลือกรูปโปรไฟล์ใหม่</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" required>
                        <div class="form-text">รองรับไฟล์ภาพ JPG, PNG, GIF ขนาดไม่เกิน 2MB</div>
                    </div>

                    <div class="mt-3 text-center d-none" id="imagePreviewContainer">
                        <p>ตัวอย่างรูปที่เลือก:</p>
                        <img id="imagePreview" src="#" alt="Image Preview" class="img-thumbnail" style="max-height: 200px; max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> อัพโหลดรูป
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    // กำหนดค่าเริ่มต้นสำหรับ SweetAlert2 ทั้งหมด
    window.addEventListener('load', function() {
        Swal.mixin({
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            }
        });

        // กำหนดสี CSS สำหรับปุ่ม SweetAlert
        const style = document.createElement('style');
        style.innerHTML = `
            .swal2-confirm.swal-confirm-btn {
                background-color: #2DC679 !important;
                border-color: #2DC679 !important;
                box-shadow: none !important;
                margin-right: 10px;
            }
            .swal2-confirm:focus {
                box-shadow: 0 0 0 3px rgba(45, 198, 121, 0.3) !important;
            }
            .swal2-actions {
                justify-content: center !important;
                gap: 10px;
            }
        `;
        document.head.appendChild(style);
    });

    document.addEventListener('DOMContentLoaded', function() {
        // แสดงตัวอย่างรูปก่อนอัพโหลด
        const profileImageInput = document.getElementById('profile_image');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');

        if (profileImageInput) {
            profileImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.classList.remove('d-none');
                    }

                    reader.readAsDataURL(this.files[0]);
                } else {
                    imagePreviewContainer.classList.add('d-none');
                }
            });
        }

        // ทำให้ tab ที่เลือกยังคงแสดงหลังจาก refresh
        const hash = window.location.hash;
        if (hash) {
            const tab = document.querySelector(`[data-bs-target="${hash}"]`);
            if (tab) {
                const bsTab = new bootstrap.Tab(tab);
                bsTab.show();
            }
        }

        // บันทึก tab ที่เลือกใน URL hash
        const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                const target = event.target.getAttribute('data-bs-target');
                window.location.hash = target;
            });
        });
    });
</script>
@stop
