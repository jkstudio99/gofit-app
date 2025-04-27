@extends('layouts.app')

@section('title', 'ตั้งเป้าหมายใหม่')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">ตั้งเป้าหมายการออกกำลังกายใหม่</h5>
                    <a href="{{ route('goals.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการเป้าหมาย
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('goals.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="form-group">
                                    <label for="type" class="form-label fw-medium">ประเภทเป้าหมาย <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="" disabled selected>เลือกประเภทเป้าหมาย</option>
                                        @foreach($goalTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="activity_type" class="form-label fw-medium">ประเภทกิจกรรม</label>
                                    <select name="activity_type" id="activity_type" class="form-select @error('activity_type') is-invalid @enderror">
                                        @foreach($activityTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('activity_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">เลือก "Any activity" หากต้องการนับรวมทุกกิจกรรม</div>
                                    @error('activity_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="form-group">
                                    <label for="target_value" class="form-label fw-medium">ค่าเป้าหมาย <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="target_value" id="target_value" step="0.01" min="0.01"
                                            class="form-control @error('target_value') is-invalid @enderror" required
                                            value="{{ old('target_value') ?? '10' }}">
                                        <span class="input-group-text" id="unit-label">กิโลเมตร</span>
                                    </div>
                                    @error('target_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="period" class="form-label fw-medium">ช่วงเวลา <span class="text-danger">*</span></label>
                                    <select name="period" id="period" class="form-select @error('period') is-invalid @enderror" required>
                                        <option value="" disabled selected>เลือกช่วงเวลา</option>
                                        @foreach($periods as $value => $label)
                                            <option value="{{ $value }}" {{ old('period') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('period')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="form-group">
                                    <label for="start_date" class="form-label fw-medium">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror" required
                                        value="{{ old('start_date') ?? now()->format('Y-m-d') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6" id="end_date_container">
                                <div class="form-group">
                                    <label for="end_date" class="form-label fw-medium">วันที่สิ้นสุด</label>
                                    <input type="date" name="end_date" id="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date') }}">
                                    <div class="form-text">สำหรับช่วงเวลาแบบกำหนดเอง หรือเว้นว่างสำหรับช่วงเวลาอื่น</div>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">เกี่ยวกับเป้าหมายของคุณ</h6>
                                    <p class="mb-0">ระบบจะติดตามความคืบหน้าของเป้าหมายโดยอัตโนมัติจากกิจกรรมการออกกำลังกายที่คุณบันทึก เมื่อคุณบรรลุเป้าหมาย คุณจะได้รับการแจ้งเตือนและสามารถรับเหรียญตราได้</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> บันทึกเป้าหมาย
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const unitLabel = document.getElementById('unit-label');
    const periodSelect = document.getElementById('period');
    const endDateContainer = document.getElementById('end_date_container');
    const endDateInput = document.getElementById('end_date');

    // อัพเดทหน่วยตามประเภทเป้าหมาย
    function updateUnitLabel() {
        const selectedType = typeSelect.value;

        switch(selectedType) {
            case 'distance':
                unitLabel.textContent = 'กิโลเมตร';
                break;
            case 'duration':
                unitLabel.textContent = 'นาที';
                break;
            case 'calories':
                unitLabel.textContent = 'แคลอรี่';
                break;
            case 'frequency':
                unitLabel.textContent = 'ครั้ง';
                break;
            default:
                unitLabel.textContent = 'หน่วย';
        }
    }

    // อัพเดทการแสดงผล end_date ตามช่วงเวลาที่เลือก
    function toggleEndDateVisibility() {
        if (periodSelect.value === 'custom') {
            endDateContainer.classList.remove('d-none');
            endDateInput.setAttribute('required', 'required');
        } else {
            // ไม่ต้องซ่อน container เพราะเราแสดงคำอธิบายเพิ่มเติม
            endDateInput.removeAttribute('required');
        }
    }

    // เรียกใช้ฟังก์ชันเมื่อโหลดและเมื่อมีการเปลี่ยนแปลง
    updateUnitLabel();
    toggleEndDateVisibility();

    typeSelect.addEventListener('change', updateUnitLabel);
    periodSelect.addEventListener('change', toggleEndDateVisibility);
});
</script>
@endsection
