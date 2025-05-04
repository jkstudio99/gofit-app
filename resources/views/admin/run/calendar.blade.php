@extends('layouts.admin')

@section('title', 'ปฏิทินการวิ่ง')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">ปฏิทินกิจกรรมการวิ่ง</h1>
        <div class="btn-group">
            <a href="{{ route('admin.run.stats') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-1"></i> สถิติการวิ่ง
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">สถิติประจำเดือน {{ $firstDayOfMonth->translatedFormat('F Y') }}</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>จำนวนการวิ่ง:</span>
                        <span class="fw-bold">{{ $monthlyStats['total_runs'] }} ครั้ง</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>ระยะทางรวม:</span>
                        <span class="fw-bold">{{ number_format($monthlyStats['total_distance'], 2) }} กม.</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>แคลอรี่รวม:</span>
                        <span class="fw-bold">{{ number_format($monthlyStats['total_calories']) }} แคล</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>ผู้ใช้ที่วิ่ง:</span>
                        <span class="fw-bold">{{ $monthlyStats['active_users'] }} คน</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">{{ $firstDayOfMonth->translatedFormat('F Y') }}</h5>
                    <div>
                        <a href="{{ route('admin.run.calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-chevron-left"></i> {{ $prevMonth->translatedFormat('F') }}
                        </a>
                        <a href="{{ route('admin.run.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="btn btn-sm btn-outline-secondary">
                            {{ $nextMonth->translatedFormat('F') }} <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">อาทิตย์</th>
                                <th class="text-center">จันทร์</th>
                                <th class="text-center">อังคาร</th>
                                <th class="text-center">พุธ</th>
                                <th class="text-center">พฤหัสบดี</th>
                                <th class="text-center">ศุกร์</th>
                                <th class="text-center">เสาร์</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dayOfWeek = $firstDayOfMonth->dayOfWeek;
                                $currentDay = 1;
                            @endphp
                            <tr>
                                @for ($i = 0; $i < 7; $i++)
                                    @if ($i < $dayOfWeek)
                                        <td class="bg-light"></td>
                                    @else
                                        <td class="position-relative" style="height: 100px; overflow-y: auto;">
                                            <div class="date-number mb-1">{{ $currentDay }}</div>
                                            @php
                                                $dateString = $year . '-' . sprintf("%02d", $month) . '-' . sprintf("%02d", $currentDay);
                                            @endphp

                                            @if(isset($runs[$dateString]))
                                                <div class="mt-1">
                                                    <span class="badge bg-success">{{ count($runs[$dateString]) }} วิ่ง</span>
                                                    <div class="mt-1">
                                                        @php
                                                            $totalDistance = $runs[$dateString]->sum('distance');
                                                        @endphp
                                                        <small class="text-muted">{{ number_format($totalDistance, 2) }} กม.</small>
                                                    </div>
                                                </div>
                                            @endif

                                            @php
                                                $currentDay++;
                                            @endphp
                                        </td>
                                    @endif
                                @endfor
                            </tr>

                            @for ($j = 1; $j < 6; $j++)
                                <tr>
                                    @for ($i = 0; $i < 7; $i++)
                                        @if ($currentDay <= $daysInMonth)
                                            <td class="position-relative" style="height: 100px; overflow-y: auto;">
                                                <div class="date-number mb-1">{{ $currentDay }}</div>
                                                @php
                                                    $dateString = $year . '-' . sprintf("%02d", $month) . '-' . sprintf("%02d", $currentDay);
                                                @endphp

                                                @if(isset($runs[$dateString]))
                                                    <div class="mt-1">
                                                        <span class="badge bg-success">{{ count($runs[$dateString]) }} วิ่ง</span>
                                                        <div class="mt-1">
                                                            @php
                                                                $totalDistance = $runs[$dateString]->sum('distance');
                                                            @endphp
                                                            <small class="text-muted">{{ number_format($totalDistance, 2) }} กม.</small>
                                                        </div>
                                                    </div>
                                                @endif

                                                @php
                                                    $currentDay++;
                                                @endphp
                                            </td>
                                        @else
                                            <td class="bg-light"></td>
                                        @endif
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">กิจกรรมการวิ่งในเดือนนี้</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>วันที่</th>
                            <th>ผู้ใช้</th>
                            <th>ระยะทาง</th>
                            <th>ระยะเวลา</th>
                            <th>แคลอรี่</th>
                            <th>ความเร็วเฉลี่ย</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allRuns = collect();
                            foreach($runs as $dateRuns) {
                                $allRuns = $allRuns->merge($dateRuns);
                            }
                            $allRuns = $allRuns->sortByDesc('created_at');
                        @endphp

                        @forelse($allRuns as $run)
                            <tr>
                                <td>{{ $run->user->firstname ?? 'ไม่ระบุ' }} {{ $run->user->lastname ?? '' }}</td>
                                <td>
                                    @php
                                        $thaiMonths = [
                                            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
                                            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
                                            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
                                        ];
                                        $date = Carbon\Carbon::parse($run->created_at);
                                        $day = $date->format('j');
                                        $month = $thaiMonths[$date->format('n')];
                                        $year = $date->format('Y') + 543 - 2500; // แปลงเป็น พ.ศ. 2 หลัก
                                        $time = $date->format('H:i');
                                        echo "{$day} {$month} {$year} {$time}";
                                    @endphp
                                </td>
                                <td>{{ number_format($run->distance, 2) }} กม.</td>
                                <td>
                                    @php
                                        $hours = floor($run->duration / 3600);
                                        $minutes = floor(($run->duration % 3600) / 60);
                                        $seconds = $run->duration % 60;
                                        echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                    @endphp
                                </td>
                                <td>{{ number_format($run->calories_burned) }} แคล</td>
                                <td>{{ number_format($run->average_speed, 2) }} กม./ชม.</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3">ไม่พบข้อมูลกิจกรรมการวิ่งในเดือนนี้</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .date-number {
        font-size: 0.9rem;
        font-weight: bold;
        color: #555;
    }
</style>
@endsection
