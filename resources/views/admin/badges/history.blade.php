@extends('layouts.admin')

@section('title', 'ประวัติการได้รับเหรียญตรา')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">ประวัติการได้รับเหรียญตรา</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.badges.index') }}">จัดการเหรียญตรา</a></li>
        <li class="breadcrumb-item active">ประวัติการได้รับเหรียญตรา</li>
    </ol>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i> ตัวกรอง</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.badges.history') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">ผู้ใช้</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">-- ทั้งหมด --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}" @if(request('user_id') == $user->user_id) selected @endif>
                                        {{ $user->username }} ({{ $user->firstname }} {{ $user->lastname }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="badge_type" class="form-label">ประเภทเหรียญ</label>
                            <select name="badge_type" id="badge_type" class="form-select">
                                <option value="">-- ทั้งหมด --</option>
                                @foreach($badgeTypes as $type)
                                    <option value="{{ $type->type }}" @if(request('badge_type') == $type->type) selected @endif>
                                        @if($type->type == 'distance')
                                            ระยะทาง
                                        @elseif($type->type == 'calories')
                                            แคลอรี่
                                        @elseif($type->type == 'streak')
                                            วิ่งต่อเนื่อง
                                        @elseif($type->type == 'speed')
                                            ความเร็ว
                                        @elseif($type->type == 'event')
                                            กิจกรรม
                                        @else
                                            {{ $type->type }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="date_start" class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" name="date_start" id="date_start" class="form-control" value="{{ request('date_start') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="date_end" class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" name="date_end" id="date_end" class="form-control" value="{{ request('date_end') }}">
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">กรอง</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-medal me-1"></i>
                ประวัติการได้รับเหรียญตรา
            </div>
            <div>
                <a href="{{ route('admin.badges.statistics') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-chart-bar me-1"></i> ดูสถิติ
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ลำดับ</th>
                            <th width="15%">ผู้ใช้</th>
                            <th width="10%">เหรียญตรา</th>
                            <th width="25%">รายละเอียด</th>
                            <th width="15%">ประเภท</th>
                            <th width="10%">คะแนนที่ได้</th>
                            <th width="10%">วันที่ได้รับ</th>
                            <th width="10%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($badgeHistory->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center py-4">ไม่พบข้อมูลประวัติการได้รับเหรียญตรา</td>
                            </tr>
                        @else
                            @foreach($badgeHistory as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $badgeHistory->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $item->user_id) }}" class="fw-bold text-primary">
                                            {{ $item->username }}
                                        </a>
                                        <div class="small text-muted">{{ $item->firstname }} {{ $item->lastname }}</div>
                                    </td>
                                    <td class="text-center">
                                        <img src="{{ asset('storage/' . $item->badge_image) }}" alt="{{ $item->badge_name }}" class="badge-image" width="60">
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $item->badge_name }}</div>
                                        <div class="small text-muted">{{ $item->badge_desc }}</div>
                                    </td>
                                    <td>
                                        @if($item->type == 'distance')
                                            <span class="badge bg-primary">ระยะทาง {{ $item->criteria }} กม.</span>
                                        @elseif($item->type == 'calories')
                                            <span class="badge bg-danger">แคลอรี่ {{ $item->criteria }} kcal</span>
                                        @elseif($item->type == 'streak')
                                            <span class="badge bg-success">วิ่งต่อเนื่อง {{ $item->criteria }} วัน</span>
                                        @elseif($item->type == 'speed')
                                            <span class="badge bg-info">ความเร็ว {{ $item->criteria }} กม./ชม.</span>
                                        @elseif($item->type == 'event')
                                            <span class="badge bg-warning text-dark">กิจกรรม {{ $item->criteria }} ครั้ง</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $item->type }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $key = $item->user_id . '_' . $item->badge_id;
                                            $points = isset($pointsHistory[$key]) ? $pointsHistory[$key][0]->points : '--';
                                        @endphp
                                        @if(is_numeric($points))
                                            <span class="badge bg-success fs-6">+{{ $points }}</span>
                                        @else
                                            <span class="badge bg-secondary fs-6">{{ $points }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->earned_at)->format('d/m/Y') }}
                                        <div class="small text-muted">{{ \Carbon\Carbon::parse($item->earned_at)->format('H:i:s') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.badges.show', $item->badge_id) }}" class="btn btn-sm btn-info mb-1" title="ดูรายละเอียดเหรียญตรา">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.show', $item->user_id) }}" class="btn btn-sm btn-primary mb-1" title="ดูข้อมูลผู้ใช้">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $badgeHistory->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // เพิ่ม Select2 สำหรับตัวกรองผู้ใช้
        $('#user_id').select2({
            placeholder: "-- เลือกผู้ใช้ --",
            allowClear: true
        });

        // ช่วยให้การเคลียร์ฟอร์มเป็นเรื่องง่าย
        $('#clearFilter').click(function(e) {
            e.preventDefault();
            window.location.href = "{{ route('admin.badges.history') }}";
        });
    });
</script>
@endsection

@section('styles')
<style>
    .badge-image {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endsection
