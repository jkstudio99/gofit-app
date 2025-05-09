@extends('layouts.app')

@section('title', 'แก้ไขเป้าหมาย')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/white.css">

<style>
    label.required:after {
        content: " *";
        color: red;
    }

    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, .05);
        margin-bottom: 20px;
    }

    .card-header {
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, .05);
        padding: 15px 20px;
        font-weight: 600;
    }

    .btn-primary {
        background: #2ecc71;
        border-color: #2ecc71;
    }

    .btn-primary:hover {
        background: #27ae60;
        border-color: #27ae60;
    }

    .info-box {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .goal-summary {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .goal-summary .icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-right: 20px;
        background-color: rgba(46, 204, 113, 0.1);
        color: #2ecc71;
        font-size: 1.8rem;
    }

    .goal-summary .value {
        font-size: 1.6rem;
        font-weight: 700;
        color: #333;
        line-height: 1.2;
    }

    .goal-summary .label {
        font-size: 1rem;
        color: #6c757d;
    }

    /* Responsive fixes */
    @media (max-width: 767.98px) {
        .form-container {
            padding-left: 15px;
            padding-right: 15px;
        }

        .goal-summary {
            margin-left: 0;
            margin-right: 0;
            padding: 15px;
        }

        .goal-summary .icon {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            font-size: 1.5rem;
        }

        .goal-summary .value {
            font-size: 1.4rem;
        }

        /* Improved mobile button spacing */
        .d-grid .btn {
            padding: 10px 15px;
        }

        /* Better form field spacing */
        .card-body {
            padding: 15px;
        }

        .mb-4 {
            margin-bottom: 15px !important;
        }
    }

    /* Tablet responsiveness */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .form-container {
            padding-left: 15px;
            padding-right: 15px;
        }

        .goal-summary {
            margin-left: 0;
            margin-right: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="py-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">แก้ไขเป้าหมาย</h1>
                    <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการเป้าหมาย
                    </a>
                </div>
            </div>
        </div>

        <div class="row form-container">
            <div class="col-md-12">
                <form action="{{ route('goals.update', $goal) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="goal-summary d-flex align-items-center">
                                <div class="icon">
                                    @if($goal->type == 'distance')
                                        <i class="fas fa-road"></i>
                                    @elseif($goal->type == 'duration')
                                        <i class="fas fa-clock"></i>
                                    @elseif($goal->type == 'calories')
                                        <i class="fas fa-fire"></i>
                                    @elseif($goal->type == 'frequency')
                                        <i class="fas fa-redo"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="value">{{ $goal->target_value }} {{ $goal->getUnitLabel() }}</div>
                                    <div class="label">{{ $goal->getTypeLabel() }} / {{ $goal->getPeriodLabel() }}</div>
                                </div>
                            </div>

                            <!-- ข้อมูลพื้นฐาน -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bullseye me-2 text-primary"></i>ข้อมูลเป้าหมาย</h5>
                                </div>
                                <div class="card-body">
                                    <!-- ประเภทเป้าหมาย -->
                                    <div class="mb-4">
                                        <label for="type" class="form-label required">ประเภทเป้าหมาย</label>
                                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                            @foreach($goalTypes as $value => $label)
                                                <option value="{{ $value }}" {{ $goal->type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- ค่าเป้าหมาย -->
                                    <div class="mb-4">
                                        <label for="target_value" class="form-label required">ค่าเป้าหมาย</label>
                                        <div class="input-group">
                                            <input type="number" name="target_value" id="target_value" step="0.01" min="0.01"
                                                class="form-control @error('target_value') is-invalid @enderror" required
                                                value="{{ old('target_value', $goal->target_value) }}">
                                            <span class="input-group-text" id="unit-label">{{ $goal->getUnitLabel() }}</span>
                                        </div>
                                        <div class="form-text">กำหนดเป้าหมายที่ท้าทายแต่สามารถทำได้จริง</div>
                                        @error('target_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- ประเภทกิจกรรม -->
                                    <div class="mb-4">
                                        <label for="activity_type" class="form-label">ประเภทกิจกรรม</label>
                                        <select name="activity_type" id="activity_type" class="form-select @error('activity_type') is-invalid @enderror">
                                            @foreach($activityTypes as $value => $label)
                                                <option value="{{ $value }}" {{ $goal->activity_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('activity_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- ฟิลด์สำหรับกรอกรายละเอียดเพิ่มเติมเมื่อเลือก "วิ่งอื่นๆ" -->
                                    <div class="row mb-4" id="activity_type_other_container" style="display: none;">
                                        <div class="col-md-12">
                                            <label for="activity_type_other" class="form-label">รายละเอียดประเภทกิจกรรม</label>
                                            <input type="text" name="activity_type_other" id="activity_type_other"
                                                class="form-control @error('activity_type_other') is-invalid @enderror"
                                                value="{{ old('activity_type_other', $goal->activity_type_other) }}"
                                                placeholder="ระบุรายละเอียดประเภทกิจกรรมวิ่ง">
                                            @error('activity_type_other')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- ช่วงเวลา -->
                                    <div class="mb-4">
                                        <label for="period" class="form-label required">ช่วงเวลาเป้าหมาย</label>
                                        <select name="period" id="period" class="form-select @error('period') is-invalid @enderror" required>
                                            @foreach($periods as $value => $label)
                                                <option value="{{ $value }}" {{ $goal->period == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('period')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- วันที่ -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="start_date" class="form-label required">วันที่เริ่มต้น</label>
                                                <input type="text" name="start_date" id="start_date"
                                                    class="form-control flatpickr-date @error('start_date') is-invalid @enderror" required
                                                    value="{{ old('start_date', $goal->start_date->format('Y-m-d')) }}">
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="end_date_container">
                                            <div class="mb-4">
                                                <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                                                <input type="text" name="end_date" id="end_date"
                                                    class="form-control flatpickr-date @error('end_date') is-invalid @enderror"
                                                    value="{{ old('end_date', $goal->end_date ? $goal->end_date->format('Y-m-d') : '') }}">
                                                <div class="form-text">สำหรับช่วงเวลาแบบกำหนดเอง หรือเว้นว่างสำหรับช่วงเวลาอื่น</div>
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- ข้อมูลความคืบหน้า -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>ความคืบหน้า</h5>
                                </div>
                                <div class="card-body">
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $goal->getProgressPercentage() }}%;" aria-valuenow="{{ $goal->getProgressPercentage() }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="text-center mb-3">
                                        <strong>{{ $goal->getProgressPercentage() }}%</strong> ความสำเร็จ
                                    </p>
                                    <div class="d-flex justify-content-between mb-4">
                                        <div>ปัจจุบัน</div>
                                        <div class="fw-bold">{{ $goal->getCurrentValue() }} {{ $goal->getUnitLabel() }}</div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div>เป้าหมาย</div>
                                        <div class="fw-bold">{{ $goal->target_value }} {{ $goal->getUnitLabel() }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- ข้อมูลเพิ่มเติม -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>การดำเนินการ</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-box mb-4">
                                        <h6 class="mb-2">สถานะเป้าหมาย</h6>
                                        @if($goal->isExpired())
                                            <div class="alert alert-danger mb-0 p-2">
                                                <i class="fas fa-exclamation-circle me-2"></i>เป้าหมายนี้หมดอายุแล้ว
                                            </div>
                                        @elseif($goal->isCompleted())
                                            <div class="alert alert-success mb-0 p-2">
                                                <i class="fas fa-check-circle me-2"></i>เป้าหมายนี้สำเร็จแล้ว
                                            </div>
                                        @else
                                            <div class="alert alert-info mb-0 p-2">
                                                <i class="fas fa-spinner me-2"></i>กำลังดำเนินการ
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
                                        </button>
                                        <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>ยกเลิกและกลับ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configure Flatpickr for date inputs
    flatpickr(".flatpickr-date", {
        locale: "th",
        dateFormat: "Y-m-d",
        allowInput: true,
        disableMobile: "true"
    });

    // Get references to form elements
    const typeSelect = document.getElementById('type');
    const unitLabel = document.getElementById('unit-label');
    const endDateContainer = document.getElementById('end_date_container');

    // Handle activity type change
    const activityTypeSelect = document.getElementById('activity_type');
    const activityTypeOtherContainer = document.getElementById('activity_type_other_container');

    activityTypeSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            activityTypeOtherContainer.style.display = 'block';
        } else {
            activityTypeOtherContainer.style.display = 'none';
        }
    });

    // Initialize the activity_type_other field visibility
    if (activityTypeSelect.value === 'other') {
        activityTypeOtherContainer.style.display = 'block';
    }

    // Update unit label based on goal type
    typeSelect.addEventListener('change', function() {
        updateUnitLabel(this.value);
    });

    // Handle period change
    const periodSelect = document.getElementById('period');

    periodSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            endDateContainer.classList.remove('d-none');
            document.getElementById('end_date').setAttribute('required', 'required');
        } else {
            // ไม่ต้องซ่อน container เพราะเราแสดงคำอธิบายเพิ่มเติม
            document.getElementById('end_date').removeAttribute('required');
        }
    });

    // Function to update unit label based on goal type
    function updateUnitLabel(type) {
        switch(type) {
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
});
</script>
@endsection
