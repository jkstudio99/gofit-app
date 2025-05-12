@extends('layouts.admin')

@section('title', 'เพิ่มเหรียญตราใหม่')

@section('styles')
<style>
    .badge-img-preview {
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

    /* ซ่อนไอคอนเครื่องหมายตกใจสำหรับฟิลด์ที่ระบุ */
    #badge_name-container .invalid-feedback-icon,
    #type-container .invalid-feedback-icon {
        display: none !important;
    }

    /* ซ่อนไอคอน feedback ทั้งหมดสำหรับ dropdown เพื่อไม่ให้บังตัวเลือก */
    #type-container .feedback-icon {
        display: none !important;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">เพิ่มเหรียญตราใหม่</h1>
                <div>
                    <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.badges.store') }}" method="POST" enctype="multipart/form-data" id="badge-form" novalidate>
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <!-- ชื่อเหรียญตรา -->
                        <div class="mb-3">
                            <label for="badge_name" class="form-label required-field">ชื่อเหรียญตรา</label>
                            <div class="field-container" id="badge_name-container">
                                <input type="text" class="form-control @error('badge_name') is-invalid @enderror"
                                       id="badge_name" name="badge_name" value="{{ old('badge_name') }}" required
                                       data-error-message="กรุณาระบุชื่อเหรียญตรา">
                                <i class="fas fa-check-circle feedback-icon valid-feedback-icon"></i>
                                <i class="fas fa-exclamation-circle feedback-icon invalid-feedback-icon"></i>
                                @error('badge_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="badge_name-error">กรุณาระบุชื่อเหรียญตรา</div>
                            </div>
                        </div>

                        <!-- คำอธิบายเหรียญตรา -->
                        <div class="mb-3">
                            <label for="badge_description" class="form-label required-field">คำอธิบายเหรียญตรา</label>
                            <div class="field-container">
                                <textarea class="form-control @error('badge_description') is-invalid @enderror"
                                         id="badge_description" name="badge_description" rows="4" required
                                         data-error-message="กรุณาระบุคำอธิบายเหรียญตรา">{{ old('badge_description') }}</textarea>
                                @error('badge_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="badge_description-error">กรุณาระบุคำอธิบายเหรียญตรา</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- ประเภทเหรียญตรา -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label required-field">ประเภทเหรียญตรา</label>
                                <div class="field-container" id="type-container">
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required
                                           data-error-message="กรุณาเลือกประเภทเหรียญตรา">
                                        <option value="" disabled selected>เลือกประเภท</option>
                                        <option value="distance" {{ old('type') == 'distance' ? 'selected' : '' }}>ระยะทาง</option>
                                        <option value="calories" {{ old('type') == 'calories' ? 'selected' : '' }}>แคลอรี่</option>
                                        <option value="streak" {{ old('type') == 'streak' ? 'selected' : '' }}>ต่อเนื่อง</option>
                                        <option value="speed" {{ old('type') == 'speed' ? 'selected' : '' }}>ความเร็ว</option>
                                        <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>กิจกรรม</option>
                                    </select>
                                    <i class="fas fa-check-circle feedback-icon valid-feedback-icon"></i>
                                    <i class="fas fa-exclamation-circle feedback-icon invalid-feedback-icon"></i>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="type-error">กรุณาเลือกประเภทเหรียญตรา</div>
                                </div>
                            </div>

                            <!-- เกณฑ์การได้รับ -->
                            <div class="col-md-6 mb-3">
                                <label for="criteria" class="form-label required-field">เกณฑ์การได้รับ</label>
                                <div class="input-group has-validation">
                                    <input type="number" class="form-control @error('criteria') is-invalid @enderror"
                                           id="criteria" name="criteria" value="{{ old('criteria') }}" step="0.01" min="0" required
                                           data-error-message="กรุณาระบุเกณฑ์การได้รับเหรียญตรา">
                                    <span class="input-group-text criteria-unit">หน่วย</span>
                                    @error('criteria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="criteria-error">กรุณาระบุเกณฑ์การได้รับเหรียญตรา</div>
                                </div>
                                <small class="form-text text-muted">
                                    <span id="criteria-help">ค่าเกณฑ์ขั้นต่ำในการได้รับเหรียญตรา</span>
                                </small>
                            </div>
                        </div>

                        <!-- คะแนนที่จะได้รับ -->
                        <div class="mb-3">
                            <label for="points" class="form-label required-field">คะแนนที่จะได้รับ</label>
                            <div class="input-group has-validation">
                                <input type="number" class="form-control @error('points') is-invalid @enderror"
                                       id="points" name="points" value="{{ old('points', 100) }}" min="0" required
                                       data-error-message="กรุณาระบุคะแนนที่จะได้รับ">
                                <span class="input-group-text">คะแนน</span>
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="points-error">กรุณาระบุคะแนนที่จะได้รับ</div>
                            </div>
                            <small class="form-text text-muted">
                                คะแนนที่ผู้ใช้จะได้รับเมื่อปลดล็อคเหรียญตรานี้
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- รูปภาพเหรียญตรา -->
                        <div class="mb-3">
                            <label for="badge_image" class="form-label">รูปภาพเหรียญตรา</label>
                            <div class="text-center mb-3">
                                <div class="image-preview-container mx-auto mb-2">
                                    <div class="text-center text-muted" id="placeholderImage">
                                        <i class="fas fa-medal fa-3x"></i>
                                        <p class="small mt-2">ไม่มีรูปภาพ</p>
                                    </div>
                                    <img src="" id="imagePreview" class="badge-img-preview d-none">
                                    <div id="removeImageBtn" class="image-remove-btn d-none">
                                        <i class="fas fa-times"></i>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <input type="file" class="form-control @error('badge_image') is-invalid @enderror"
                                           id="badge_image" name="badge_image" accept="image/*" style="max-width: 250px;">
                                </div>
                                @error('badge_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="badge_image-error" class="invalid-feedback text-center mt-2"></div>
                                <small class="form-text text-muted mt-2">
                                    อัพโหลดรูปภาพขนาดไม่เกิน 2MB (รองรับ: JPEG, PNG, GIF)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-plus me-1"></i> เพิ่มเหรียญตรา
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
        const badgeImageInput = document.getElementById('badge_image');
        const imagePreview = document.getElementById('imagePreview');
        const placeholderImage = document.getElementById('placeholderImage');
        const removeImageBtn = document.getElementById('removeImageBtn');
        const imageError = document.getElementById('badge_image-error');

        badgeImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];

                // ตรวจสอบขนาดไฟล์ (ไม่เกิน 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    imageError.textContent = 'ขนาดไฟล์ต้องไม่เกิน 2MB';
                    imageError.style.display = 'block';
                    this.value = ''; // ล้างค่า input
                    badgeImageInput.classList.add('is-invalid');
                    return;
                }

                // ตรวจสอบประเภทไฟล์
                const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!validImageTypes.includes(file.type)) {
                    imageError.textContent = 'กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPEG, PNG, GIF, WEBP)';
                    imageError.style.display = 'block';
                    this.value = ''; // ล้างค่า input
                    badgeImageInput.classList.add('is-invalid');
                    return;
                }

                // ล้างข้อความผิดพลาดถ้ามี
                imageError.textContent = '';
                imageError.style.display = 'none';
                badgeImageInput.classList.remove('is-invalid');
                badgeImageInput.classList.add('is-valid');

                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    removeImageBtn.classList.remove('d-none');
                    if (placeholderImage) {
                        placeholderImage.classList.add('d-none');
                    }
                }

                reader.readAsDataURL(file);
            }
        });

        // ฟังก์ชันลบรูปภาพด้วยปุ่ม X
        removeImageBtn.addEventListener('click', function() {
            // ล้างค่า input file
            badgeImageInput.value = '';
            // ซ่อนรูปและปุ่มลบ
            imagePreview.classList.add('d-none');
            removeImageBtn.classList.add('d-none');
            // แสดง placeholder
            placeholderImage.classList.remove('d-none');
            // ล้างสถานะการตรวจสอบ
            badgeImageInput.classList.remove('is-valid');
            badgeImageInput.classList.remove('is-invalid');
            imageError.style.display = 'none';
        });

        // ฟังก์ชันตรวจสอบการกรอกข้อมูล
        const form = document.getElementById('badge-form');
        const fields = form.querySelectorAll('input[required], select[required], textarea[required]');

        // เมื่อมีการกรอกข้อมูลในฟิลด์
        fields.forEach(field => {
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

        // อัพเดทหน่วยตามประเภทของเหรียญตรา
        const typeSelect = document.getElementById('type');
        const criteriaUnit = document.querySelector('.criteria-unit');
        const criteriaHelp = document.getElementById('criteria-help');

        function updateCriteriaUnit() {
            switch(typeSelect.value) {
                case 'distance':
                    criteriaUnit.textContent = 'กิโลเมตร';
                    criteriaHelp.textContent = 'ระยะทางขั้นต่ำในการได้รับเหรียญตรา (กิโลเมตร)';
                    break;
                case 'calories':
                    criteriaUnit.textContent = 'แคลอรี่';
                    criteriaHelp.textContent = 'จำนวนแคลอรี่ขั้นต่ำในการได้รับเหรียญตรา';
                    break;
                case 'streak':
                    criteriaUnit.textContent = 'วัน';
                    criteriaHelp.textContent = 'จำนวนวันต่อเนื่องขั้นต่ำในการได้รับเหรียญตรา';
                    break;
                case 'speed':
                    criteriaUnit.textContent = 'กม./ชม.';
                    criteriaHelp.textContent = 'ความเร็วขั้นต่ำในการได้รับเหรียญตรา (กม./ชม.)';
                    break;
                case 'event':
                    criteriaUnit.textContent = 'กิจกรรม';
                    criteriaHelp.textContent = 'จำนวนกิจกรรมขั้นต่ำในการได้รับเหรียญตรา';
                    break;
                default:
                    criteriaUnit.textContent = 'หน่วย';
                    criteriaHelp.textContent = 'ค่าเกณฑ์ขั้นต่ำในการได้รับเหรียญตรา';
            }

            // ตรวจสอบการเลือกประเภทเมื่อมีการเปลี่ยนแปลง
            validateField(typeSelect);
        }

        updateCriteriaUnit();
        typeSelect.addEventListener('change', updateCriteriaUnit);
    });
</script>
@endsection
