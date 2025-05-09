@extends('layouts.admin')

@section('title', 'แก้ไขรางวัล - ' . $reward->name)

@section('styles')
<style>
    .reward-img-preview {
        max-height: 150px;
        max-width: 150px;
        object-fit: contain;
    }

    .image-preview-container {
        width: 150px;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border: 1px dashed #ced4da;
        border-radius: 5px;
        overflow: hidden;
        position: relative;
    }

    .image-remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 24px;
        height: 24px;
        background-color: #ff4d4f;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        opacity: 0.9;
    }

    .image-remove-btn:hover {
        background-color: #ff1f1f;
        opacity: 1;
    }

    .required-field::after {
        content: "*";
        color: red;
        margin-left: 3px;
    }

    .feedback-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }

    .is-valid ~ .feedback-icon.valid-feedback-icon {
        display: block;
        color: #28a745;
    }

    .is-invalid ~ .feedback-icon.invalid-feedback-icon {
        display: block;
        color: #dc3545;
    }

    .feedback-icon {
        display: none;
    }

    .form-control, .form-select {
        padding-right: 40px;
    }

    .field-container {
        position: relative;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">แก้ไขรางวัล</h1>
                <div>
                    <a href="{{ route('admin.rewards.show', $reward) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                    </a>
                    <a href="{{ route('admin.rewards') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.rewards.update', $reward) }}" method="POST" enctype="multipart/form-data" id="reward-form" novalidate>
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <!-- ชื่อรางวัล -->
                        <div class="mb-3">
                            <label for="name" class="form-label required-field">ชื่อรางวัล</label>
                            <div class="field-container">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $reward->name) }}" required
                                       data-error-message="กรุณาระบุชื่อรางวัล">
                                <i class="fas fa-check-circle feedback-icon valid-feedback-icon"></i>
                                <i class="fas fa-exclamation-circle feedback-icon invalid-feedback-icon"></i>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="name-error">กรุณาระบุชื่อรางวัล</div>
                            </div>
                        </div>

                        <!-- คำอธิบายรางวัล -->
                        <div class="mb-3">
                            <label for="description" class="form-label required-field">คำอธิบายรางวัล</label>
                            <div class="field-container">
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                         id="description" name="description" rows="4" required
                                         data-error-message="กรุณาระบุคำอธิบายรางวัล">{{ old('description', $reward->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="description-error">กรุณาระบุคำอธิบายรางวัล</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- คะแนนที่ใช้แลก -->
                            <div class="col-md-6 mb-3">
                                <label for="points_required" class="form-label required-field">คะแนนที่ใช้แลก</label>
                                <div class="input-group has-validation">
                                    <input type="number" class="form-control @error('points_required') is-invalid @enderror"
                                           id="points_required" name="points_required" value="{{ old('points_required', $reward->points_required) }}" min="1" required
                                           data-error-message="กรุณาระบุคะแนนที่ใช้แลก">
                                    <span class="input-group-text">
                                        <i class="fas fa-coins text-warning"></i>
                                    </span>
                                    @error('points_required')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="points_required-error">กรุณาระบุคะแนนที่ใช้แลก</div>
                                </div>
                                <small class="form-text text-muted">
                                    จำนวนคะแนนที่ผู้ใช้ต้องใช้ในการแลกรับรางวัลนี้
                                </small>
                            </div>

                            <!-- จำนวนสินค้า -->
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label required-field">จำนวนสินค้า</label>
                                <div class="input-group has-validation">
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                           id="quantity" name="quantity" value="{{ old('quantity', $reward->quantity) }}" min="0" required
                                           data-error-message="กรุณาระบุจำนวนสินค้า">
                                    <span class="input-group-text">
                                        <i class="fas fa-cubes"></i>
                                    </span>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="quantity-error">กรุณาระบุจำนวนสินค้า</div>
                                </div>
                                <small class="form-text text-muted">
                                    จำนวนรางวัลที่มีให้แลก (0 = หมด)
                                </small>
                            </div>
                        </div>

                        <!-- สถานะการใช้งาน -->
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" {{ $reward->is_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_enabled">เปิดใช้งานรางวัลนี้</label>
                            <small class="form-text text-muted d-block">
                                รางวัลที่เปิดใช้งานเท่านั้นที่จะแสดงให้ผู้ใช้เห็น
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- รูปภาพรางวัล -->
                        <div class="mb-3">
                            <label for="image_path" class="form-label">รูปภาพรางวัล</label>
                            <div class="text-center mb-3">
                                <div class="image-preview-container mx-auto mb-2">
                                    @if($reward->image_path)
                                        <img src="{{ asset('storage/' . $reward->image_path) }}" id="imagePreview" class="reward-img-preview">
                                        <div id="removeImageBtn" class="image-remove-btn">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    @else
                                        <div class="text-center text-muted" id="placeholderImage">
                                            <i class="fas fa-gift fa-3x"></i>
                                            <p class="small mt-2">ไม่มีรูปภาพ</p>
                                        </div>
                                        <img src="" id="imagePreview" class="reward-img-preview d-none">
                                        <div id="removeImageBtn" class="image-remove-btn d-none">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-center">
                                    <input type="file" class="form-control @error('image_path') is-invalid @enderror"
                                           id="image_path" name="image_path" accept="image/*" style="max-width: 250px;">
                                </div>
                                @error('image_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="image_path-error" class="invalid-feedback text-center mt-2"></div>
                                <small class="form-text text-muted mt-2">
                                    อัพโหลดรูปภาพขนาดไม่เกิน 2MB (รองรับ: JPEG, PNG, GIF)
                                </small>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image">
                                <label class="form-check-label text-danger" for="remove_image">
                                    ลบรูปภาพปัจจุบัน
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save me-1"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ตรวจสอบการอัพโหลดรูปภาพใหม่
        const imageInput = document.getElementById('image_path');
        const imagePreview = document.getElementById('imagePreview');
        const placeholderImage = document.getElementById('placeholderImage');
        const removeImageBtn = document.getElementById('removeImageBtn');
        const removeImageCheck = document.getElementById('remove_image');
        const imageError = document.getElementById('image_path-error');

        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];

                // ตรวจสอบขนาดไฟล์ (ไม่เกิน 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    imageError.textContent = 'ขนาดไฟล์ต้องไม่เกิน 2MB';
                    imageError.style.display = 'block';
                    this.value = ''; // ล้างค่า input
                    imageInput.classList.add('is-invalid');
                    return;
                }

                // ตรวจสอบประเภทไฟล์
                const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!validImageTypes.includes(file.type)) {
                    imageError.textContent = 'กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPEG, PNG, GIF, WEBP)';
                    imageError.style.display = 'block';
                    this.value = ''; // ล้างค่า input
                    imageInput.classList.add('is-invalid');
                    return;
                }

                // ล้างข้อความผิดพลาดถ้ามี
                imageError.textContent = '';
                imageError.style.display = 'none';
                imageInput.classList.remove('is-invalid');
                imageInput.classList.add('is-valid');

                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    removeImageBtn.classList.remove('d-none');
                    if (placeholderImage) {
                        placeholderImage.classList.add('d-none');
                    }
                    // ถ้ามีการเลือกไฟล์ใหม่ ไม่ต้องลบไฟล์เก่า
                    removeImageCheck.checked = false;
                }

                reader.readAsDataURL(file);
            }
        });

        // ฟังก์ชันลบรูปภาพด้วยปุ่ม X
        removeImageBtn.addEventListener('click', function() {
            // ล้างค่า input file
            imageInput.value = '';

            if ("{{ $reward->image_path }}") {
                // ถ้ามีรูปภาพเดิม ให้เลือกช่อง "ลบรูปภาพปัจจุบัน"
                removeImageCheck.checked = true;
            }

            // ซ่อนรูปและปุ่มลบ
            imagePreview.classList.add('d-none');
            removeImageBtn.classList.add('d-none');
            // แสดง placeholder
            if (placeholderImage) {
                placeholderImage.classList.remove('d-none');
            }

            // ล้างสถานะการตรวจสอบ
            imageInput.classList.remove('is-valid');
            imageInput.classList.remove('is-invalid');
            imageError.style.display = 'none';
        });

        // การทำงานของ checkbox ลบรูปภาพ
        removeImageCheck.addEventListener('change', function() {
            if (this.checked) {
                // ถ้าเลือกลบรูปภาพ ให้ซ่อนรูปและแสดง placeholder
                imagePreview.classList.add('d-none');
                removeImageBtn.classList.add('d-none');
                if (placeholderImage) {
                    placeholderImage.classList.remove('d-none');
                }
                // ล้างค่า input file
                imageInput.value = '';
            } else {
                // ถ้ายกเลิกการลบรูปภาพ และมีรูปเดิมอยู่
                if ("{{ $reward->image_path }}") {
                    imagePreview.classList.remove('d-none');
                    removeImageBtn.classList.remove('d-none');
                    if (placeholderImage) {
                        placeholderImage.classList.add('d-none');
                    }
                }
            }
        });

        // ฟังก์ชันตรวจสอบการกรอกข้อมูล
        const form = document.getElementById('reward-form');
        const fields = form.querySelectorAll('input[required], select[required], textarea[required]');

        // เมื่อมีการกรอกข้อมูลในฟิลด์
        fields.forEach(field => {
            // ตรวจสอบค่าเริ่มต้นทุกฟิลด์
            if (field.value && field.value.trim() !== '') {
                field.classList.add('is-valid');
            }

            field.addEventListener('input', function() {
                validateField(field);
            });

            field.addEventListener('blur', function() {
                validateField(field);
            });

            // สำหรับ select
            if (field.tagName === 'SELECT') {
                field.addEventListener('change', function() {
                    validateField(field);
                });
            }
        });

        function validateField(field) {
            const errorElement = document.getElementById(`${field.id}-error`);

            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                if (errorElement) {
                    errorElement.textContent = field.getAttribute('data-error-message') || 'กรุณากรอกข้อมูลในช่องนี้';
                }
                return false;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                return true;
            }
        }

        // เมื่อกดปุ่ม submit
        form.addEventListener('submit', function(event) {
            let isValid = true;

            // ตรวจสอบทุกฟิลด์ที่จำเป็น
            fields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault();
                // แสดงข้อความผิดพลาดที่ส่วนบนของฟอร์ม
                window.scrollTo(0, 0);

                // แสดง SweetAlert แจ้งเตือน
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'กรุณาตรวจสอบข้อมูล',
                        text: 'กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง',
                        icon: 'warning',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#3085d6'
                    });
                }
            }
        });
    });
</script>
@endsection
