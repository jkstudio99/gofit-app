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
    }

    .required-field::after {
        content: "*";
        color: red;
        margin-left: 3px;
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
            <form action="{{ route('admin.badges.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <!-- ชื่อเหรียญตรา -->
                        <div class="mb-3">
                            <label for="badge_name" class="form-label required-field">ชื่อเหรียญตรา</label>
                            <input type="text" class="form-control @error('badge_name') is-invalid @enderror"
                                   id="badge_name" name="badge_name" value="{{ old('badge_name') }}" required>
                            @error('badge_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- คำอธิบายเหรียญตรา -->
                        <div class="mb-3">
                            <label for="badge_description" class="form-label required-field">คำอธิบายเหรียญตรา</label>
                            <textarea class="form-control @error('badge_description') is-invalid @enderror"
                                     id="badge_description" name="badge_description" rows="4" required>{{ old('badge_description') }}</textarea>
                            @error('badge_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- ประเภทเหรียญตรา -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label required-field">ประเภทเหรียญตรา</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="" disabled selected>เลือกประเภท</option>
                                    <option value="distance" {{ old('type') == 'distance' ? 'selected' : '' }}>ระยะทาง</option>
                                    <option value="calories" {{ old('type') == 'calories' ? 'selected' : '' }}>แคลอรี่</option>
                                    <option value="streak" {{ old('type') == 'streak' ? 'selected' : '' }}>ต่อเนื่อง</option>
                                    <option value="speed" {{ old('type') == 'speed' ? 'selected' : '' }}>ความเร็ว</option>
                                    <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>กิจกรรม</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- เกณฑ์การได้รับ -->
                            <div class="col-md-6 mb-3">
                                <label for="criteria" class="form-label required-field">เกณฑ์การได้รับ</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('criteria') is-invalid @enderror"
                                           id="criteria" name="criteria" value="{{ old('criteria') }}" step="0.01" min="0" required>
                                    <span class="input-group-text criteria-unit">หน่วย</span>
                                    @error('criteria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    <span id="criteria-help">ค่าเกณฑ์ขั้นต่ำในการได้รับเหรียญตรา</span>
                                </small>
                            </div>
                        </div>

                        <!-- คะแนนที่จะได้รับ -->
                        <div class="mb-3">
                            <label for="points" class="form-label required-field">คะแนนที่จะได้รับ</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('points') is-invalid @enderror"
                                       id="points" name="points" value="{{ old('points', 100) }}" min="0" required>
                                <span class="input-group-text">คะแนน</span>
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                </div>
                                <div class="d-flex justify-content-center">
                                    <input type="file" class="form-control @error('badge_image') is-invalid @enderror"
                                           id="badge_image" name="badge_image" accept="image/*" style="max-width: 250px;">
                                </div>
                                @error('badge_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted mt-2">
                                    อัพโหลดรูปภาพขนาดไม่เกิน 2MB (รองรับ: JPEG, PNG, GIF)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
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

        badgeImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    if (placeholderImage) {
                        placeholderImage.classList.add('d-none');
                    }
                }

                reader.readAsDataURL(this.files[0]);
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
                    criteriaHelp.textContent = 'จำนวนวันติดต่อกันขั้นต่ำในการได้รับเหรียญตรา';
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
        }

        // อัพเดทหน่วยเมื่อโหลดหน้า
        updateCriteriaUnit();

        // อัพเดทหน่วยเมื่อเปลี่ยนประเภท
        typeSelect.addEventListener('change', updateCriteriaUnit);
    });
</script>
@endsection
