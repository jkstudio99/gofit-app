@extends('layouts.app')

@section('title', 'กิจกรรมการออกกำลังกาย')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">กิจกรรมการออกกำลังกาย</h2>
            <p class="text-muted">บันทึกและติดตามกิจกรรมการออกกำลังกายของคุณ</p>
        </div>
        <a href="{{ route('activities.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> บันทึกกิจกรรมใหม่
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('activities.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="type" class="form-label">ประเภทกิจกรรม</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">ทั้งหมด</option>
                        @foreach($activityTypes as $value => $label)
                            <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> กรอง
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($activities->isEmpty())
        <div class="text-center py-5 bg-light rounded">
            <i class="fas fa-dumbbell fa-4x text-muted mb-3"></i>
            <h4>ยังไม่มีกิจกรรมการออกกำลังกาย</h4>
            <p class="text-muted">เริ่มบันทึกกิจกรรมการออกกำลังกายของคุณเพื่อติดตามความก้าวหน้า</p>
            <a href="{{ route('activities.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-1"></i> บันทึกกิจกรรมแรกของคุณ
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ประเภท</th>
                        <th>วันที่</th>
                        <th>ระยะทาง</th>
                        <th>ระยะเวลา</th>
                        <th>แคลอรี่</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($activity->type == 'run')
                                    <i class="fas fa-running text-primary fs-4 me-2"></i>
                                @elseif($activity->type == 'walk')
                                    <i class="fas fa-walking text-success fs-4 me-2"></i>
                                @elseif($activity->type == 'cycle')
                                    <i class="fas fa-bicycle text-danger fs-4 me-2"></i>
                                @elseif($activity->type == 'swim')
                                    <i class="fas fa-swimmer text-info fs-4 me-2"></i>
                                @elseif($activity->type == 'gym')
                                    <i class="fas fa-dumbbell text-warning fs-4 me-2"></i>
                                @elseif($activity->type == 'yoga')
                                    <i class="fas fa-om text-purple fs-4 me-2"></i>
                                @elseif($activity->type == 'hiit')
                                    <i class="fas fa-fire-alt text-danger fs-4 me-2"></i>
                                @else
                                    <i class="fas fa-heartbeat text-secondary fs-4 me-2"></i>
                                @endif
                                <div>
                                    <div class="fw-medium">
                                        @if($activity->type == 'run')
                                            วิ่ง
                                        @elseif($activity->type == 'walk')
                                            เดิน
                                        @elseif($activity->type == 'cycle')
                                            ปั่นจักรยาน
                                        @elseif($activity->type == 'swim')
                                            ว่ายน้ำ
                                        @elseif($activity->type == 'gym')
                                            ออกกำลังกายที่ยิม
                                        @elseif($activity->type == 'yoga')
                                            โยคะ
                                        @elseif($activity->type == 'hiit')
                                            HIIT
                                        @else
                                            อื่นๆ
                                        @endif
                                    </div>
                                    @if($activity->pace)
                                    <div class="small text-muted">{{ $activity->formattedPace }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($activity->started_at)
                                <div>{{ $activity->started_at->format('d M Y') }}</div>
                                <div class="small text-muted">{{ $activity->started_at->format('H:i') }}</div>
                            @else
                                <div class="text-muted">ไม่ระบุ</div>
                            @endif
                        </td>
                        <td>
                            @if($activity->distance)
                                {{ number_format($activity->distance, 1) }} กม.
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $hours = floor($activity->duration / 60);
                                $minutes = $activity->duration % 60;
                                $durationFormatted = '';
                                if ($hours > 0) {
                                    $durationFormatted .= $hours . ' ชม. ';
                                }
                                $durationFormatted .= $minutes . ' นาที';
                            @endphp
                            {{ $durationFormatted }}
                        </td>
                        <td>
                            @if($activity->calories)
                                {{ number_format($activity->calories) }} แคล
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-outline-primary" title="ดูรายละเอียด">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('activities.edit', $activity) }}" class="btn btn-sm btn-outline-secondary" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบกิจกรรมนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $activities->links() }}
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .text-purple {
        color: #6f42c1;
    }
</style>
@endsection
