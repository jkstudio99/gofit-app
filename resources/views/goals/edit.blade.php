@extends('layouts.app')

@section('title', 'แก้ไขเป้าหมาย')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">แก้ไขเป้าหมายการออกกำลังกาย</h5>
                    <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายละเอียดเป้าหมาย
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('goals.update', $goal) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="form-group">
                                    <label for="type" class="form-label fw-medium">ประเภทเป้าหมาย <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="" disabled>เลือกประเภทเป้าหมาย</option>
                                        @foreach($goalTypes as $value => $label)
                                            <option value="{{ $value }}" {{ (old('type', $goal->type) == $value) ? 'selected' : '' }}>{{ $label }}</option>
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
                                            <option value="{{ $value }}" {{ (old('activity_type', $goal->activity_type) == $value) ? 'selected' : '' }}>{{ $label }}</option>
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
                                            value="{{ old('target_value', $goal->target_value) }}">
                                        <span class="input-group-text" id="unit-label">
                                            @if($goal->type == 'distance')
                                                กิโลเมตร
                                            @elseif($goal->type == 'duration')
                                                นาที
                                            @elseif($goal->type == 'calories')
                                                แคลอรี่
                                            @elseif($goal->type == 'frequency')
                                                ครั้ง
                                            @else
                                                หน่วย
                                            @endif
                                        </span>
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
                                        <option value="" disabled>เลือกช่วงเวลา</option>
                                        @foreach($periods as $value => $label)
                                            <option value="{{ $value }}" {{ (old('period', $goal->period) == $value) ? 'selected' : '' }}>{{ $label }}</option>
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
                                        value="{{ old('start_date', $goal->start_date->format('Y-m-d')) }}">
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
                                        value="{{ old('end_date', ($goal->end_date ? $goal->end_date->format('Y-m-d') : null)) }}">
                                    <div class="form-text">สำหรับช่วงเวลาแบบกำหนดเอง หรือเว้นว่างสำหรับช่วงเวลาอื่น</div>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">คำเตือนเกี่ยวกับการแก้ไขเป้าหมาย</h6>
                                    <p class="mb-0">การเปลี่ยนแปลงประเภทเป้าหมายหรือค่าเป้าหมายอาจส่งผลต่อความคืบหน้าของเป้าหมาย ระบบจะคำนวณความคืบหน้าใหม่ตามค่าที่แก้ไข</p>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-title">ความคืบหน้าปัจจุบัน</h6>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>{{ $goal->current_value }}/{{ $goal->target_value }}</span>
                                    <span>{{ $goal->progressPercentage }}%</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $goal->progressPercentage }}%;"
                                        aria-valuenow="{{ $goal->progressPercentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> บันทึกการเปลี่ยนแปลง
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
