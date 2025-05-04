@extends('layouts.admin')

@section('title', 'เพิ่มรางวัลใหม่')

@section('styles')
<style>
    .preview-container {
        width: 100%;
        height: 200px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f8f9fa;
        margin-bottom: 10px;
        text-align: center;
    }

    .preview-image {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .form-label {
        font-weight: 500;
    }

    .required-asterisk {
        color: #dc3545;
        margin-left: 2px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2DC679;
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
    }

    .form-floating label {
        color: #6c757d;
    }

    .image-upload-container {
        text-align: center;
    }

    .image-upload-btn {
        display: inline-block;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px 15px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .image-upload-btn:hover {
        background-color: #e9ecef;
    }

    .required-field:after {
        content: " *";
        color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">เพิ่มรางวัลใหม่</h1>
                <a href="{{ route('admin.rewards') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>กลับไปยังรายการ
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-gift text-primary me-2"></i>ข้อมูลรางวัล
                    </h5>
                </div>

                <form action="{{ route('admin.rewards.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="name" class="form-label required-field">ชื่อรางวัล</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label required-field">คำอธิบาย</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="points_required" class="form-label required-field">คะแนนที่ใช้แลก</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('points_required') is-invalid @enderror" id="points_required" name="points_required" min="1" value="{{ old('points_required', 100) }}" required>
                                                <span class="input-group-text">
                                                    <i class="fas fa-coins text-warning"></i>
                                                </span>
                                            </div>
                                            @error('points_required')
                                                <div class="text-danger mt-1 small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label required-field">จำนวนสินค้า</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" min="0" value="{{ old('quantity', 10) }}" required>
                                                <span class="input-group-text">
                                                    <i class="fas fa-cubes"></i>
                                                </span>
                                            </div>
                                            @error('quantity')
                                                <div class="text-danger mt-1 small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" checked>
                                    <label class="form-check-label" for="is_enabled">เปิดใช้งานรางวัลนี้</label>
                                    <div class="text-muted small">รางวัลที่เปิดใช้งานเท่านั้นที่จะแสดงให้ผู้ใช้เห็น</div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="image_path" class="form-label">รูปภาพรางวัล</label>
                                    <div class="preview-container" id="image-preview-container">
                                        <div id="placeholder-content" class="text-center">
                                            <i class="fas fa-gift fa-3x mb-3 text-muted"></i>
                                            <p class="mb-1 text-muted">คลิกเพื่ออัปโหลดรูปภาพ</p>
                                            <small class="text-muted d-block">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 2MB</small>
                                        </div>
                                        <img src="" id="preview-image" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>

                                    <div class="image-upload-container">
                                        <label for="image_path" class="image-upload-btn">
                                            <i class="fas fa-upload me-2"></i>เลือกรูปภาพ
                                        </label>
                                        <input type="file" id="image_path" name="image_path" class="d-none" accept="image/*">
                                    </div>

                                    <div id="file-info" class="mt-2 small text-center" style="display: none;">
                                        <span class="me-2"><i class="fas fa-check-circle text-success"></i></span>
                                        <span id="file-name"></span>
                                        <button type="button" id="remove-image" class="btn btn-link text-danger p-0 ms-2">
                                            <i class="fas fa-times"></i> ลบ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="{{ route('admin.rewards') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>บันทึกรางวัล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image_path');
        const previewContainer = document.getElementById('image-preview-container');
        const previewImage = document.getElementById('preview-image');
        const placeholderContent = document.getElementById('placeholder-content');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const removeImageBtn = document.getElementById('remove-image');

        // คลิกที่ container จะเปิดหน้าต่างเลือกไฟล์
        previewContainer.addEventListener('click', function() {
            imageInput.click();
        });

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
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
                    this.value = ''; // ล้างค่า input
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
                    this.value = ''; // ล้างค่า input
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    placeholderContent.style.display = 'none';
                    fileInfo.style.display = 'block';
                    fileName.textContent = file.name;
                };

                reader.readAsDataURL(file);
            } else {
                resetImagePreview();
            }
        });

        removeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // ป้องกันการ bubble event ไปที่ container
            imageInput.value = '';
            resetImagePreview();
        });

        function resetImagePreview() {
            previewImage.src = '';
            previewImage.style.display = 'none';
            placeholderContent.style.display = 'block';
            fileInfo.style.display = 'none';
            fileName.textContent = '';
        }
    });
</script>
@endsection
