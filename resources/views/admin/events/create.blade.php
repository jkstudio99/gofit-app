@extends('layouts.admin')

@section('title', 'เพิ่มกิจกรรมใหม่')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    .custom-file-label::after {
        content: "เลือกไฟล์";
    }
    .image-preview {
        max-height: 200px;
        width: 100%;
        object-fit: cover;
        margin-top: 10px;
        border-radius: 5px;
    }
    label.required:after {
        content: " *";
        color: red;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">เพิ่มกิจกรรมใหม่</h1>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

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
                                                   id="event_name" name="event_name" value="{{ old('event_name') }}" required>
                                            @error('event_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- รายละเอียด -->
                                        <div class="mb-3">
                                            <label for="event_desc" class="form-label">รายละเอียด</label>
                                            <textarea id="event_desc" name="event_desc" class="form-control summernote @error('event_desc') is-invalid @enderror">{{ old('event_desc') }}</textarea>
                                            @error('event_desc')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- สถานที่ -->
                                        <div class="mb-3">
                                            <label for="location" class="form-label required">สถานที่</label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                                   id="location" name="location" value="{{ old('location') }}" required>
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
                                                           id="start_datetime" name="start_datetime" value="{{ old('start_datetime') }}" required>
                                                    @error('start_datetime')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="end_datetime" class="form-label required">วันที่และเวลาสิ้นสุด</label>
                                                    <input type="text" class="form-control flatpickr-datetime @error('end_datetime') is-invalid @enderror"
                                                           id="end_datetime" name="end_datetime" value="{{ old('end_datetime') }}" required>
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
                                        <!-- รูปภาพ -->
                                        <div class="mb-3">
                                            <label for="event_image" class="form-label">รูปภาพกิจกรรม</label>
                                            <input class="form-control @error('event_image') is-invalid @enderror" type="file"
                                                  id="event_image" name="event_image" accept="image/*" onchange="previewImage(this)">
                                            <small class="form-text text-muted">ขนาดไฟล์ไม่เกิน 2MB (แนะนำขนาด 1200x600px)</small>
                                            @error('event_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="mt-2">
                                                <img id="imagePreview" class="image-preview d-none" alt="ตัวอย่างรูปภาพ">
                                            </div>
                                        </div>

                                        <!-- ระยะทาง -->
                                        <div class="mb-3">
                                            <label for="distance" class="form-label">ระยะทาง (กม.)</label>
                                            <input type="number" class="form-control @error('distance') is-invalid @enderror"
                                                   id="distance" name="distance" value="{{ old('distance') }}" step="0.01" min="0">
                                            @error('distance')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- จำนวนผู้เข้าร่วมสูงสุด -->
                                        <div class="mb-3">
                                            <label for="max_participants" class="form-label required">จำนวนผู้เข้าร่วมสูงสุด</label>
                                            <input type="number" class="form-control @error('max_participants') is-invalid @enderror"
                                                   id="max_participants" name="max_participants" value="{{ old('max_participants', 0) }}" min="0" required>
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
                                                       value="published" {{ old('status') == 'published' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_published">
                                                    <span class="badge bg-primary">เผยแพร่</span> - แสดงกิจกรรมให้ผู้ใช้เห็นทันที
                                                </label>
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="radio" name="status" id="status_draft"
                                                       value="draft" {{ old('status', 'draft') == 'draft' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_draft">
                                                    <span class="badge bg-info">ฉบับร่าง</span> - บันทึกไว้แต่ยังไม่เผยแพร่
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
                                        <i class="fas fa-save me-1"></i> บันทึกกิจกรรม
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
            minDate: "today",
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
    });

    // แสดงตัวอย่างรูปภาพ
    function previewImage(input) {
        const file = input.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            // ตรวจสอบขนาดไฟล์ (ไม่เกิน 2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    title: 'ไม่สามารถอัพโหลดได้',
                    text: 'ขนาดไฟล์ต้องไม่เกิน 2MB',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'ตกลง'
                });
                input.value = ''; // ล้างค่า input
                preview.src = '';
                preview.classList.add('d-none');
                return;
            }

            // ตรวจสอบประเภทไฟล์
            const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!validImageTypes.includes(file.type)) {
                Swal.fire({
                    title: 'ไม่สามารถอัพโหลดได้',
                    text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPEG, PNG, GIF, WEBP)',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'ตกลง'
                });
                input.value = ''; // ล้างค่า input
                preview.src = '';
                preview.classList.add('d-none');
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }

            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.classList.add('d-none');
        }
    }
</script>
@endsection
