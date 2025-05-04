@extends('layouts.admin')

@section('title', 'แก้ไขกิจกรรม')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .custom-file-label::after {
        content: "เลือกไฟล์";
    }
    .image-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 0.5rem;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    label.required:after {
        content: " *";
        color: red;
    }

    /* Action button styling */
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

    .swal2-icon.swal2-question {
        border-color: #2DC679 !important;
        color: #2DC679 !important;
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
                <h1 class="h3 mb-0">แก้ไขกิจกรรม: {{ $event->event_name }}</h1>
                <div>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                    </a>
                    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-info text-white">
                        <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-8">
                                <!-- ข้อมูลพื้นฐาน -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">ข้อมูลพื้นฐาน</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- ชื่อกิจกรรม -->
                                        <div class="mb-3">
                                            <label for="event_name" class="form-label required">ชื่อกิจกรรม</label>
                                            <input type="text" class="form-control @error('event_name') is-invalid @enderror"
                                                   id="event_name" name="event_name" value="{{ old('event_name', $event->event_name) }}" required>
                                            @error('event_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- รายละเอียด -->
                                        <div class="mb-3">
                                            <label for="event_desc" class="form-label">รายละเอียด</label>
                                            <textarea id="event_desc" name="event_desc" class="form-control summernote @error('event_desc') is-invalid @enderror">{{ old('event_desc', $event->event_desc) }}</textarea>
                                            @error('event_desc')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- สถานที่ -->
                                        <div class="mb-3">
                                            <label for="location" class="form-label required">สถานที่</label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                                   id="location" name="location" value="{{ old('location', $event->location) }}" required>
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- วันที่และเวลา -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="start_datetime" class="form-label required">วันที่และเวลาเริ่มต้น</label>
                                                    <input type="text" class="form-control flatpickr-datetime @error('start_datetime') is-invalid @enderror"
                                                           id="start_datetime" name="start_datetime"
                                                           value="{{ old('start_datetime', $event->start_datetime->format('Y-m-d H:i')) }}" required>
                                                    @error('start_datetime')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="end_datetime" class="form-label required">วันที่และเวลาสิ้นสุด</label>
                                                    <input type="text" class="form-control flatpickr-datetime @error('end_datetime') is-invalid @enderror"
                                                           id="end_datetime" name="end_datetime"
                                                           value="{{ old('end_datetime', $event->end_datetime->format('Y-m-d H:i')) }}" required>
                                                    @error('end_datetime')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- การตั้งค่า -->
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">การตั้งค่า</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- รูปภาพปัจจุบัน และเปลี่ยนรูปภาพ -->
                                        <div class="mb-3">
                                            <label for="event_image" class="form-label">รูปภาพกิจกรรม</label>

                                            @if($event->event_image)
                                                <div class="mb-2" id="currentEventImage">
                                                    <img src="{{ asset('storage/' . $event->event_image) }}"
                                                         alt="{{ $event->event_name }}" class="image-preview">
                                                </div>
                                            @else
                                                <div class="mb-2 d-none" id="currentEventImage"></div>
                                            @endif

                                            <input class="form-control @error('event_image') is-invalid @enderror" type="file"
                                                  id="event_image" name="event_image" accept="image/*" onchange="handleImageSelect(this)">
                                            <small class="form-text text-muted">อัปโหลดรูปใหม่เพื่อเปลี่ยน (ขนาดไม่เกิน 2MB)</small>
                                            @error('event_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- ระยะทาง -->
                                        <div class="mb-3">
                                            <label for="distance" class="form-label">ระยะทาง (กม.)</label>
                                            <input type="number" class="form-control @error('distance') is-invalid @enderror"
                                                   id="distance" name="distance" value="{{ old('distance', $event->distance) }}" step="0.01" min="0">
                                            @error('distance')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- จำนวนผู้เข้าร่วมสูงสุด -->
                                        <div class="mb-3">
                                            <label for="max_participants" class="form-label required">จำนวนผู้เข้าร่วมสูงสุด</label>
                                            <input type="number" class="form-control @error('max_participants') is-invalid @enderror"
                                                   id="max_participants" name="max_participants"
                                                   value="{{ old('max_participants', $event->max_participants) }}" min="0" required>
                                            <small class="form-text text-muted">กำหนด 0 หากไม่ต้องการจำกัดจำนวน</small>
                                            @error('max_participants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- สถานะการเผยแพร่ -->
                                        <div class="mb-3">
                                            <label class="form-label required">สถานะการเผยแพร่</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status_published"
                                                       value="published" {{ old('status', $event->status) == 'published' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_published">
                                                    <span class="badge bg-primary">เผยแพร่</span> - แสดงกิจกรรมให้ผู้ใช้เห็น
                                                </label>
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="radio" name="status" id="status_draft"
                                                       value="draft" {{ old('status', $event->status) == 'draft' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_draft">
                                                    <span class="badge bg-info">ฉบับร่าง</span> - บันทึกไว้แต่ยังไม่เผยแพร่
                                                </label>
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="radio" name="status" id="status_cancelled"
                                                       value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_cancelled">
                                                    <span class="badge bg-danger">ยกเลิก</span> - ยกเลิกกิจกรรมนี้
                                                </label>
                                            </div>
                                            @error('status')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary me-2">ยกเลิก</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> อัปเดตกิจกรรม
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script>
    // ตัวเลือกวันที่และเวลา
    document.addEventListener('DOMContentLoaded', function() {
        // กำหนดค่า flatpickr สำหรับวันที่และเวลา
        flatpickr(".flatpickr-datetime", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            locale: "th",
        });

        // กำหนดค่า summernote สำหรับ rich text editor
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'help']]
            ],
            placeholder: 'รายละเอียดกิจกรรม...'
        });

        // แสดงข้อความแจ้งเตือนหากมี session
        @if(session('success'))
        Swal.fire({
            title: 'สำเร็จ!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'ตกลง'
        });
        @endif

        @if(session('error'))
        Swal.fire({
            title: 'เกิดข้อผิดพลาด!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
        @endif
    });

    // จัดการอัปโหลดรูปภาพด้วย SweetAlert2
    function handleImageSelect(input) {
        const file = input.files[0];
        if (!file) return;

        // ตรวจสอบประเภทไฟล์
        const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!validImageTypes.includes(file.type)) {
            Swal.fire({
                title: 'ไม่สามารถอัพโหลดได้',
                text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPEG, PNG, GIF, WEBP)',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
            input.value = ''; // ล้างค่า input
            return;
        }

        // ตรวจสอบขนาดไฟล์ (ไม่เกิน 2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                title: 'ไม่สามารถอัพโหลดได้',
                text: 'ขนาดไฟล์ต้องไม่เกิน 2MB',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
            input.value = ''; // ล้างค่า input
            return;
        }

        // แสดงตัวอย่างรูปภาพและยืนยันการอัพโหลด
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgSrc = e.target.result;

            // เตรียมอัปเดตรูปภาพที่แสดงในหน้า
            const currentEventImage = document.getElementById('currentEventImage');

            Swal.fire({
                title: 'ยืนยันการเปลี่ยนรูปภาพกิจกรรม',
                html: `
                    <div class="text-center mb-3">
                        <img src="${imgSrc}" style="max-width: 100%; max-height: 300px; border-radius: 8px;" class="img-fluid mb-2">
                    </div>
                    <p>คุณต้องการใช้รูปนี้เป็นรูปภาพกิจกรรมใหม่หรือไม่?</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ใช้รูปนี้',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ถ้าผู้ใช้ยืนยัน ให้อัปเดตรูปภาพที่แสดง
                    if (currentEventImage) {
                        currentEventImage.innerHTML = `<img src="${imgSrc}" class="image-preview" alt="รูปภาพกิจกรรม">`;
                        currentEventImage.classList.remove('d-none');
                    }
                } else {
                    // ถ้าผู้ใช้ยกเลิก ให้ล้างค่าอินพุตไฟล์
                    input.value = '';
                }
            });
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
