@extends('layouts.app')

@section('title', 'บันทึกกิจกรรมการออกกำลังกายใหม่')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">บันทึกกิจกรรมการออกกำลังกายใหม่</h2>
            <p class="text-muted">บันทึกรายละเอียดกิจกรรมการออกกำลังกายของคุณ</p>
        </div>
        <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> กลับ
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('activities.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="activity_type" class="form-label">ประเภทกิจกรรม <span class="text-danger">*</span></label>
                        <select name="activity_type" id="activity_type" class="form-select @error('activity_type') is-invalid @enderror" required>
                            <option value="" selected disabled>เลือกประเภทกิจกรรม</option>
                            <option value="run" {{ old('activity_type') == 'run' ? 'selected' : '' }}>วิ่ง</option>
                            <option value="walk" {{ old('activity_type') == 'walk' ? 'selected' : '' }}>เดิน</option>
                            <option value="cycle" {{ old('activity_type') == 'cycle' ? 'selected' : '' }}>ปั่นจักรยาน</option>
                            <option value="swim" {{ old('activity_type') == 'swim' ? 'selected' : '' }}>ว่ายน้ำ</option>
                            <option value="gym" {{ old('activity_type') == 'gym' ? 'selected' : '' }}>ออกกำลังกายที่ยิม</option>
                            <option value="yoga" {{ old('activity_type') == 'yoga' ? 'selected' : '' }}>โยคะ</option>
                            <option value="hiit" {{ old('activity_type') == 'hiit' ? 'selected' : '' }}>HIIT</option>
                            <option value="other" {{ old('activity_type') == 'other' ? 'selected' : '' }}>อื่นๆ</option>
                        </select>
                        @error('activity_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="is_group" class="form-label">ประเภทการเข้าร่วม</label>
                        <select name="is_group" id="is_group" class="form-select @error('is_group') is-invalid @enderror">
                            <option value="0" {{ old('is_group', '0') == '0' ? 'selected' : '' }}>ส่วนตัว (คนเดียว)</option>
                            <option value="1" {{ old('is_group') == '1' ? 'selected' : '' }}>กลุ่ม (หลายคน)</option>
                        </select>
                        @error('is_group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_time" class="form-label">เวลาเริ่มต้น <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror"
                               id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_time" class="form-label">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror"
                               id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="distance" class="form-label">ระยะทาง (กม.)</label>
                        <input type="number" step="0.01" class="form-control @error('distance') is-invalid @enderror"
                               id="distance" name="distance" value="{{ old('distance') }}" min="0">
                        @error('distance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="duration" class="form-label">ระยะเวลา (นาที)</label>
                        <input type="number" class="form-control @error('duration') is-invalid @enderror"
                               id="duration" name="duration" value="{{ old('duration') }}" min="0">
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="calories" class="form-label">แคลอรี่</label>
                        <input type="number" class="form-control @error('calories') is-invalid @enderror"
                               id="calories" name="calories" value="{{ old('calories') }}" min="0">
                        @error('calories')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3" id="group-fields" style="display: none;">
                    <div class="col-md-4">
                        <label for="max_participants" class="form-label">จำนวนผู้เข้าร่วมสูงสุด</label>
                        <input type="number" class="form-control @error('max_participants') is-invalid @enderror"
                               id="max_participants" name="max_participants" value="{{ old('max_participants', 1) }}" min="1">
                        @error('max_participants')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">บันทึกเพิ่มเติม</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="details" class="form-label">รายละเอียดเพิ่มเติม</label>
                    <textarea class="form-control @error('details') is-invalid @enderror" id="details" name="details" rows="4">{{ old('details') }}</textarea>
                    <div class="form-text">บันทึกข้อมูลเพิ่มเติม เช่น สถานที่, เส้นทาง, อุปกรณ์ที่ใช้</div>
                    @error('details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary me-md-2">
                        ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> บันทึกกิจกรรม
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isGroupSelect = document.getElementById('is_group');
        const groupFields = document.getElementById('group-fields');

        // Initial check
        if (isGroupSelect.value === '1') {
            groupFields.style.display = 'flex';
        }

        // Add change event listener
        isGroupSelect.addEventListener('change', function () {
            if (this.value === '1') {
                groupFields.style.display = 'flex';
            } else {
                groupFields.style.display = 'none';
            }
        });

        // Auto-calculate duration when start and end times change
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const durationInput = document.getElementById('duration');

        function updateDuration() {
            if (startTimeInput.value && endTimeInput.value) {
                const start = new Date(startTimeInput.value);
                const end = new Date(endTimeInput.value);

                if (end > start) {
                    // Calculate duration in minutes
                    const durationMs = end - start;
                    const durationMinutes = Math.round(durationMs / 60000);
                    durationInput.value = durationMinutes;
                }
            }
        }

        startTimeInput.addEventListener('change', updateDuration);
        endTimeInput.addEventListener('change', updateDuration);
    });
</script>
@endsection
