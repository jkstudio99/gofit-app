@extends('layouts.app')

@section('title', 'ตั้งเป้าหมายใหม่')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/white.css">

<style>
    .image-preview {
        max-height: 200px;
        width: 100%;
        object-fit: cover;
        margin-top: 10px;
        border-radius: 5px;
    }

    label.required:after {
        content: " *";image.png
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

    .goal-type-card {
        padding: 15px;
        border-radius: 12px;
        border: 2px solid #eee;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    .goal-type-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .08);
    }

    .goal-type-card.selected {
        border-color: #2ecc71;
        background-color: rgba(46, 204, 113, 0.05);
    }

    .goal-type-card .icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-right: 15px;
        background-color: rgba(46, 204, 113, 0.1);
        color: #2ecc71;
        font-size: 1.5rem;
    }

    /* Responsive fixes */
    @media (max-width: 767.98px) {
        .goals-container {
            padding-left: 15px;
            padding-right: 15px;
        }

        .form-container {
            padding-left: 15px;
            padding-right: 15px;
        }

        .goal-type-card .icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        /* Improved mobile layout */
        .py-4 {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        .container {
            padding-left: 0;
            padding-right: 0;
        }

        .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .d-flex.justify-content-between a {
            margin-top: 1rem;
            align-self: flex-start;
        }

        /* Better form field spacing */
        .card-body {
            padding: 15px;
        }

        .mb-4 {
            margin-bottom: 15px !important;
        }

        /* Better goal type card layout */
        .col-md-6.col-lg-3 {
            margin-bottom: 10px;
        }
    }

    /* Tablet responsiveness */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .goals-container {
            padding-left: 15px;
            padding-right: 15px;
        }

        .form-container {
            padding-left: 15px;
            padding-right: 15px;
        }

        .py-4 {
            padding-left: 15px !important;
            padding-right: 15px !important;
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
                <h1 class="h3 mb-0">ตั้งเป้าหมายการออกกำลังกายใหม่</h1>
                <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการเป้าหมาย
                    </a>
                </div>
        </div>
    </div>

        <div class="row form-container">
        <div class="col-md-12">
                    <form action="{{ route('goals.store') }}" method="POST">
                        @csrf

                <div class="row">
                    <div class="col-md-8">
                        <!-- ข้อมูลพื้นฐาน -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bullseye me-2 text-primary"></i>ข้อมูลเป้าหมาย</h5>
                            </div>
                            <div class="card-body">
                                <!-- ประเภทเป้าหมาย -->
                                <div class="mb-4">
                                    <label for="type" class="form-label required">ประเภทเป้าหมาย</label>
                                    <div class="row">
                                        @foreach($goalTypes as $value => $label)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="goal-type-card d-flex align-items-center" onclick="selectGoalType('{{ $value }}')">
                                                    <div class="icon">
                                                        @if($value == 'distance')
                                                            <i class="fas fa-road"></i>
                                                        @elseif($value == 'duration')
                                                            <i class="fas fa-clock"></i>
                                                        @elseif($value == 'calories')
                                                            <i class="fas fa-fire"></i>
                                                        @elseif($value == 'frequency')
                                                            <i class="fas fa-redo"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $label }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="type" id="type" value="{{ old('type') }}" required>
                                    @error('type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                        </div>

                                <!-- ค่าเป้าหมาย -->
                                <div class="mb-4">
                                    <label for="target_value" class="form-label required">ค่าเป้าหมาย</label>
                                    <div class="input-group">
                                        <input type="number" name="target_value" id="target_value" step="0.01" min="0.01"
                                            class="form-control @error('target_value') is-invalid @enderror" required
                                            value="{{ old('target_value') ?? '10' }}">
                                        <span class="input-group-text" id="unit-label">กิโลเมตร</span>
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
                                            <option value="{{ $value }}" {{ old('activity_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">เลือกประเภทกิจกรรมที่ต้องการสร้างเป้าหมาย</div>
                                    @error('activity_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- ฟิลด์สำหรับกรอกรายละเอียดเพิ่มเติมเมื่อเลือก "วิ่งอื่นๆ" -->
                                <div class="mb-4" id="activity_type_other_container" style="display: none;">
                                    <label for="activity_type_other" class="form-label">รายละเอียดประเภทกิจกรรม</label>
                                    <input type="text" name="activity_type_other" id="activity_type_other"
                                        class="form-control @error('activity_type_other') is-invalid @enderror"
                                        value="{{ old('activity_type_other') }}"
                                        placeholder="ระบุรายละเอียดประเภทกิจกรรมวิ่ง">
                                    <div class="form-text">กรุณาระบุประเภทกิจกรรมวิ่งเพิ่มเติม</div>
                                    @error('activity_type_other')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- ช่วงเวลา -->
                                <div class="mb-4">
                                    <label for="period" class="form-label required">ช่วงเวลาเป้าหมาย</label>
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

                                <!-- วันที่ -->
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="start_date" class="form-label required">วันที่เริ่มต้น</label>
                                        <input type="text" name="start_date" id="start_date"
                                            class="form-control flatpickr-date @error('start_date') is-invalid @enderror" required
                                        value="{{ old('start_date') ?? now()->format('Y-m-d') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                    <div class="col-md-6 mb-4" id="end_date_container">
                                        <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                                        <input type="text" name="end_date" id="end_date"
                                            class="form-control flatpickr-date @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date') }}">
                                    <div class="form-text">สำหรับช่วงเวลาแบบกำหนดเอง หรือเว้นว่างสำหรับช่วงเวลาอื่น</div>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                    <div class="col-md-4">
                        <!-- ข้อมูลเพิ่มเติม -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>ข้อมูลเพิ่มเติม</h5>
                                </div>
                            <div class="card-body">
                                <div class="info-box">
                                    <h6><i class="fas fa-lightbulb text-warning me-2"></i>เกี่ยวกับเป้าหมายของคุณ</h6>
                                    <p class="mb-0">ระบบจะติดตามความคืบหน้าของเป้าหมายโดยอัตโนมัติจากกิจกรรมการออกกำลังกายที่คุณบันทึก เมื่อคุณบรรลุเป้าหมาย คุณจะได้รับการแจ้งเตือนและสามารถรับเหรียญตราได้</p>
                                </div>

                                <div class="info-box">
                                    <h6><i class="fas fa-trophy text-success me-2"></i>ประโยชน์ของการตั้งเป้าหมาย</h6>
                                    <ul class="mb-0 ps-3">
                                        <li>ช่วยให้คุณมีแรงจูงใจในการออกกำลังกาย</li>
                                        <li>ติดตามความก้าวหน้าได้อย่างเป็นรูปธรรม</li>
                                        <li>รับเหรียญตราเพื่อแลกของรางวัล</li>
                                        <li>พัฒนาสุขภาพอย่างมีประสิทธิภาพ</li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>บันทึกเป้าหมาย
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

    // Initial unit label setup
    updateUnitLabel(document.getElementById('type').value || 'distance');

    // Type selection
    const goalTypeCards = document.querySelectorAll('.goal-type-card');
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

    // Initialize based on currently selected period
    if (periodSelect.value === 'custom') {
        endDateContainer.classList.remove('d-none');
        document.getElementById('end_date').setAttribute('required', 'required');
    }

    // Select any pre-selected goal type from validation errors
    const typeValue = document.getElementById('type').value;
    if (typeValue) {
        goalTypeCards.forEach(card => {
            if (card.querySelector('.icon i').classList.contains(`fa-${getIconForType(typeValue)}`)) {
                card.classList.add('selected');
            }
        });
    }
});

// Function to select goal type
    function selectGoalType(type) {
        document.getElementById('type').value = type;

    // Update visual feedback
        document.querySelectorAll('.goal-type-card').forEach(card => {
            card.classList.remove('selected');
        });

    // Find the clicked card and mark it as selected
    const cards = document.querySelectorAll('.goal-type-card');
    cards.forEach(card => {
        const icon = card.querySelector('.icon i');
        if (icon.classList.contains(`fa-${getIconForType(type)}`)) {
                card.classList.add('selected');
            }
        });

        // Update unit label
    updateUnitLabel(type);
    }

// Helper function to get icon for goal type
function getIconForType(type) {
        switch(type) {
        case 'distance': return 'road';
        case 'duration': return 'clock';
        case 'calories': return 'fire';
        case 'frequency': return 'redo';
        default: return 'bullseye';
        }
    }

// Function to update unit label based on goal type
function updateUnitLabel(type) {
        const unitLabel = document.getElementById('unit-label');
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
</script>
@endsection
