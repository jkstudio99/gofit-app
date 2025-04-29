@extends('layouts.admin')

@section('title', 'เพิ่มรางวัลใหม่')

@section('styles')
<style>
    .preview-container {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin-top: 10px;
        background-color: #f9f9f9;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preview-image {
        max-height: 200px;
        max-width: 100%;
        object-fit: contain;
    }

    .custom-file-button {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .custom-file-button input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: pointer;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0,0,0,.125);
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
                                    <label for="name" class="form-label">ชื่อรางวัล<span class="required-asterisk">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">คำอธิบาย<span class="required-asterisk">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="points_required" class="form-label">คะแนนที่ใช้แลก<span class="required-asterisk">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('points_required') is-invalid @enderror" id="points_required" name="points_required" min="1" value="{{ old('points_required', 100) }}" required>
                                                <span class="input-group-text">
                                                    <i class="fas fa-coins text-warning"></i>
                                                </span>
                                            </div>
                                            @error('points_required')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">จำนวนสินค้า<span class="required-asterisk">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" min="0" value="{{ old('quantity', 10) }}" required>
                                                <span class="input-group-text">
                                                    <i class="fas fa-cubes"></i>
                                                </span>
                                            </div>
                                            @error('quantity')
                                                <div class="text-danger mt-1">{{ $message }}</div>
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
                                    <div class="custom-file-button d-grid">
                                        <input type="file" class="d-none" id="image_path" name="image_path" accept="image/*">
                                        <label for="image_path" class="btn btn-outline-primary">
                                            <i class="fas fa-upload me-2"></i>เลือกรูปภาพ
                                        </label>
                                    </div>
                                    <div id="image-preview-container" class="preview-container mt-2 d-none">
                                        <img id="preview-image" class="preview-image">
                                    </div>
                                    <div id="no-image-selected" class="preview-container">
                                        <div class="text-muted">
                                            <i class="fas fa-image fa-3x mb-3"></i>
                                            <p>ยังไม่ได้เลือกรูปภาพ</p>
                                            <small>รูปภาพควรมีขนาด 500x500 พิกเซล (สูงสุด 2MB)</small>
                                        </div>
                                    </div>
                                    <div id="file-selected-info" class="mt-2 small d-none">
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
        const noImageSelected = document.getElementById('no-image-selected');
        const fileSelectedInfo = document.getElementById('file-selected-info');
        const fileName = document.getElementById('file-name');
        const removeImageBtn = document.getElementById('remove-image');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                    noImageSelected.classList.add('d-none');
                    fileSelectedInfo.classList.remove('d-none');
                    fileName.textContent = file.name;
                };

                reader.readAsDataURL(file);
            } else {
                resetImagePreview();
            }
        });

        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            resetImagePreview();
        });

        function resetImagePreview() {
            previewImage.src = '';
            previewContainer.classList.add('d-none');
            noImageSelected.classList.remove('d-none');
            fileSelectedInfo.classList.add('d-none');
            fileName.textContent = '';
        }
    });
</script>
@endsection
