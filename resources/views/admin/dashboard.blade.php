@extends('layouts.admin')

@section('title', 'แดชบอร์ด')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">แดชบอร์ดผู้ดูแลระบบ</h2>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">
            <i class="fas fa-chart-line me-1"></i> รายงานและสถิติทั้งหมด
        </a>
    </div>

    <!-- ข้อมูลสรุปสำคัญ -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                                <h2 class="fs-1 fw-bold mb-0 text-primary">{{ $totalUsers ?? 0 }}</h2>
                                <p class="text-muted mb-0">ผู้ใช้งานทั้งหมด</p>
                        </div>
                            <div class="rounded-circle bg-primary bg-opacity-10 p-0" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('admin.run.stats') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                                <h2 class="fs-1 fw-bold mb-0 text-success">{{ $totalRuns ?? 0 }}</h2>
                                <p class="text-muted mb-0">การวิ่งทั้งหมด</p>
                        </div>
                            <div class="rounded-circle bg-success bg-opacity-10 p-0" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-running fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('admin.badges.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                                <h2 class="fs-1 fw-bold mb-0 text-warning">{{ $totalBadges ?? 0 }}</h2>
                                <p class="text-muted mb-0">เหรียญตราทั้งหมด</p>
                        </div>
                            <div class="rounded-circle bg-warning bg-opacity-10 p-0" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-medal fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('admin.health-articles.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                                <h2 class="fs-1 fw-bold mb-0 text-info">{{ $totalArticles ?? 0 }}</h2>
                                <p class="text-muted mb-0">บทความสุขภาพ</p>
                            </div>
                            <div class="rounded-circle bg-info bg-opacity-10 p-0" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-newspaper fa-2x text-info"></i>
                        </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
                        </div>

    <div class="row">
        <!-- กราฟสถิติกิจกรรม -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                    <h5 class="m-0 fw-bold">สถิติกิจกรรมรายสัปดาห์</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            สัปดาห์นี้
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="periodDropdown">
                            <li><a class="dropdown-item period-select" href="#" data-period="week">สัปดาห์นี้</a></li>
                            <li><a class="dropdown-item period-select" href="#" data-period="month">เดือนนี้</a></li>
                            <li><a class="dropdown-item period-select" href="#" data-period="quarter">3 เดือนล่าสุด</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <div id="activityChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- สรุปข้อมูลเดือนนี้ -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold">สรุปข้อมูลเดือนนี้</h5>
                    <span class="badge bg-light text-dark">{{ Carbon\Carbon::now()->locale('th')->translatedFormat('F') }} 2568</span>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-list-check text-primary"></i>
                                </div>
                                <span>กิจกรรมใหม่</span>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-primary fs-5">{{ $monthlyActivities ?? 0 }}</span>
                                <span class="text-muted ms-1 small">/ {{ $totalActivities ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $percentage = $totalActivities > 0 ? min(100, ($monthlyActivities ?? 0) / $totalActivities * 100) : 0;
                                $formattedPercentage = number_format($percentage, 0);
                            @endphp
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-end mt-1">
                            <span class="text-muted small">{{ $formattedPercentage }}% ของกิจกรรมทั้งหมด</span>
                        </div>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-running text-success"></i>
                                </div>
                                <span>การวิ่งใหม่</span>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-success fs-5">{{ $monthlyRuns ?? 0 }}</span>
                                <span class="text-muted ms-1 small">/ {{ $totalRuns ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $percentage = $totalRuns > 0 ? min(100, ($monthlyRuns ?? 0) / $totalRuns * 100) : 0;
                                $formattedPercentage = number_format($percentage, 0);
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-end mt-1">
                            <span class="text-muted small">{{ $formattedPercentage }}% ของการวิ่งทั้งหมด</span>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-gift text-warning"></i>
                                </div>
                                <span>การแลกรางวัล</span>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-warning fs-5">{{ $monthlyRedeems ?? 0 }}</span>
                                <span class="text-muted ms-1 small">/ {{ $totalRedeems ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $totalRedeems = isset($totalRedeems) ? $totalRedeems : 0;
                                $percentage = $totalRedeems > 0 ? min(100, ($monthlyRedeems ?? 0) / $totalRedeems * 100) : 0;
                                $formattedPercentage = number_format($percentage, 0);
                            @endphp
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-end mt-1">
                            <span class="text-muted small">{{ $formattedPercentage }}% ของการแลกรางวัลทั้งหมด</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
            <!-- ผู้ใช้ที่มีกิจกรรมมากที่สุด -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                    <h5 class="m-0 fw-bold">ผู้ใช้ที่มีกิจกรรมมากที่สุด</h5>
                    <a href="{{ route('admin.users.index', ['sort' => 'activity']) }}" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>ชื่อผู้ใช้</th>
                                    <th>กิจกรรม</th>
                                    <th class="text-end pe-3">สัดส่วน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers ?? [] as $index => $user)
                                    @if($index < 5)
                                    <tr>
                                        <td class="ps-3">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user fa-lg text-secondary"></i>
                                                </div>
                                                <div>{{ $user->firstname }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $user->activity_count }}</td>
                                        <td class="text-end pe-3">
                                            <div class="progress" style="height: 6px;">
                                                @php
                                                    $maxCount = $topUsers->max('activity_count');
                                                    $percentage = $maxCount > 0 ? ($user->activity_count / $maxCount * 100) : 0;
                                                @endphp
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                                            </div>
                                    </td>
                                </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(isset($topUsers) && count($topUsers) > 5)
                    <div class="py-3 px-2 border-top d-flex justify-content-center align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-primary show-more-users mx-auto">
                            <i class="fas fa-plus-circle me-1"></i> แสดงผู้ใช้เพิ่มเติม
                        </button>
                    </div>
                    @endif
                    </div>
                </div>
            </div>

        <!-- การวิ่งล่าสุด -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                    <h5 class="m-0 fw-bold">การวิ่งล่าสุด</h5>
                    <a href="{{ route('admin.run.stats') }}" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">ผู้ใช้</th>
                                    <th>ระยะทาง</th>
                                    <th>แคลอรี่</th>
                                    <th class="text-end pe-3">วันที่</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 0; @endphp
                                @foreach($latestRuns ?? [] as $run)
                                    @if($count < 5)
                                    <tr>
                                        <td class="ps-3">
                                            @if(isset($run->user) && $run->user->firstname)
                                                {{ $run->user->firstname }}
                                            @else
                                                ไม่ระบุชื่อ
                                            @endif
                                        </td>
                                        <td>{{ number_format($run->distance, 2) }} กม.</td>
                                        <td>{{ number_format($run->calories_burned) }} kcal</td>
                                        <td class="text-end pe-3">
                                            @php
                                                $thaiMonths = [
                                                    1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
                                                    5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
                                                    9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
                                                ];
                                                $day = $run->created_at->format('j');
                                                $month = $thaiMonths[$run->created_at->format('n')];
                                                $year = $run->created_at->format('Y') + 543 - 2500; // แปลงเป็น พ.ศ. 2 หลัก
                                                $time = $run->created_at->format('H:i');
                                                echo "{$day} {$month} {$year} {$time}";
                                            @endphp
                                        </td>
                                    </tr>
                                    @php $count++; @endphp
                                        @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(isset($latestRuns) && count($latestRuns) > 5)
                    <div class="py-3 px-2 border-top d-flex justify-content-center align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-primary show-more-runs mx-auto">
                            <i class="fas fa-plus-circle me-1"></i> แสดงกิจกรรมเพิ่มเติม
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- เมนูลัด -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">เมนูลัดจัดการระบบ</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 rounded bg-light hover-shadow">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-users fa-lg text-primary"></i>
                                    </div>
                                    <span>จัดการผู้ใช้งาน</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.badges.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 rounded bg-light hover-shadow">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-medal fa-lg text-warning"></i>
                                    </div>
                                    <span>จัดการเหรียญตรา</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.rewards') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 rounded bg-light hover-shadow">
                                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-gift fa-lg text-danger"></i>
                    </div>
                                    <span>จัดการรางวัล</span>
                        </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.health-articles.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 rounded bg-light hover-shadow">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-newspaper fa-lg text-info"></i>
                    </div>
                                    <span>จัดการบทความ</span>
                        </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ไม่ใช้ Modal แต่ใช้ SweetAlert2 แทน -->
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ใช้ SweetAlert2 แทน Modal

        // แสดงรายการผู้ใช้เพิ่มเติม
        document.querySelector('.show-more-users')?.addEventListener('click', function() {
            // เตรียมข้อมูลตาราง
            let tableHTML = `
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>ชื่อผู้ใช้</th>
                                <th>จำนวนกิจกรรม</th>
                                <th class="text-end pe-3">การมีส่วนร่วม</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($topUsers) && count($topUsers) > 0)
                            @foreach($topUsers as $index => $user)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light p-2 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user text-secondary"></i>
                                        </div>
                                        <div>{{ $user->firstname }}</div>
                                    </div>
                                </td>
                                <td>{{ $user->activity_count }}</td>
                                <td class="text-end pe-3">
                                    <div class="progress" style="height: 6px;">
                                        @php
                                            $maxCount = $topUsers->max('activity_count');
                                            $percentage = $maxCount > 0 ? ($user->activity_count / $maxCount * 100) : 0;
                                        @endphp
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center py-3">ไม่พบข้อมูลผู้ใช้</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            `;

            Swal.fire({
                title: 'ผู้ใช้ที่มีกิจกรรมมากที่สุด',
                html: tableHTML,
                width: '800px',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: 'ปิด',
                confirmButtonText: 'ดูทั้งหมด',
                cancelButtonColor: '#6c757d',
                confirmButtonColor: '#0d6efd'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('admin.users.index', ['sort' => 'activity']) }}";
                }
            });
        });

        // แสดงรายการวิ่งล่าสุดเพิ่มเติม
        document.querySelector('.show-more-runs')?.addEventListener('click', function() {
            // เตรียมข้อมูลตาราง
            let tableHTML = `
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ผู้ใช้</th>
                                <th>ระยะทาง</th>
                                <th>แคลอรี่</th>
                                <th class="text-end pe-3">วันที่</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($latestRuns) && count($latestRuns) > 0)
                            @foreach($latestRuns as $run)
                            <tr>
                                <td class="ps-3">
                                    @if(isset($run->user) && $run->user->firstname)
                                        {{ $run->user->firstname }}
                                    @else
                                        ไม่ระบุชื่อ
                                    @endif
                                </td>
                                <td>{{ number_format($run->distance, 2) }} กม.</td>
                                <td>{{ number_format($run->calories_burned) }} kcal</td>
                                <td class="text-end pe-3">
                                    @php
                                        $thaiMonths = [
                                            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
                                            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
                                            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
                                        ];
                                        $day = $run->created_at->format('j');
                                        $month = $thaiMonths[$run->created_at->format('n')];
                                        $year = $run->created_at->format('Y') + 543 - 2500; // แปลงเป็น พ.ศ. 2 หลัก
                                        $time = $run->created_at->format('H:i');
                                        echo "{$day} {$month} {$year} {$time}";
                                    @endphp
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center py-3">ไม่พบข้อมูลการวิ่ง</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            `;

            Swal.fire({
                title: 'การวิ่งล่าสุด',
                html: tableHTML,
                width: '800px',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: 'ปิด',
                confirmButtonText: 'ดูทั้งหมด',
                cancelButtonColor: '#6c757d',
                confirmButtonColor: '#0d6efd'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('admin.run.stats') }}";
                }
            });
        });
        // ชื่อวันในสัปดาห์
        var defaultLabelsWeek = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'];

        // รับข้อมูลจริงที่ส่งมาจาก Controller
        var activityLabels = {!! isset($activityLabels) ? json_encode($activityLabels) : json_encode(defaultLabelsWeek) !!};
        var activityDataWeek = {!! isset($activityData) ? json_encode($activityData) : json_encode([0, 0, 0, 0, 0, 0, 0]) !!};
        var runDataWeek = {!! isset($runData) ? json_encode($runData) : json_encode([0, 0, 0, 0, 0, 0, 0]) !!};

        // ข้อมูลรายเดือน
        var defaultLabelsMonth = ['สัปดาห์ที่ 1', 'สัปดาห์ที่ 2', 'สัปดาห์ที่ 3', 'สัปดาห์ที่ 4'];

        // รับข้อมูลรายเดือนจริงจาก server หรือใช้ข้อมูลจำลองถ้าไม่มี
        var activityDataMonth = {!! isset($activityDataMonth) ? json_encode($activityDataMonth) : json_encode([0, 0, 0, 0]) !!};
        var runDataMonth = {!! isset($runDataMonth) ? json_encode($runDataMonth) : json_encode([0, 0, 0, 0]) !!};

        // ข้อมูลรายปี (12 เดือน)
        var defaultLabelsQuarter = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

        // รับข้อมูลรายปีจริงจาก server หรือใช้ข้อมูลจำลองถ้าไม่มี
        var activityDataYear = {!! isset($activityDataYear) ? json_encode($activityDataYear) : json_encode([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]) !!};
        var runDataYear = {!! isset($runDataYear) ? json_encode($runDataYear) : json_encode([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]) !!};

        var currentLabels = activityLabels;
        var currentActivityData = activityDataWeek;
        var currentRunData = runDataWeek;
        var periodTitle = 'สถิติกิจกรรมรายสัปดาห์';

        // สร้างกราฟ
        var options = {
            series: [
                {
                    name: 'กิจกรรมทั้งหมด',
                    data: currentActivityData
                },
                {
                    name: 'การวิ่ง',
                    data: currentRunData
                }
            ],
            chart: {
                height: 300,
                type: 'area',
                fontFamily: 'Prompt, sans-serif',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                locales: [{
                    name: 'th',
                    options: {
                        months: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
                        shortMonths: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                        days: ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'],
                        shortDays: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส']
                    }
                }],
                defaultLocale: 'th'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#36A2EB', '#2DC679'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            markers: {
                size: 5,
                hover: {
                    size: 7
                }
            },
            xaxis: {
                categories: currentLabels,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'จำนวนกิจกรรม'
                },
                min: 0
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                offsetY: -15
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " กิจกรรม"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.getElementById('activityChart'), options);
        chart.render();

        // เปลี่ยนช่วงเวลาแสดงผล
        document.querySelectorAll('.period-select').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var period = this.getAttribute('data-period');

                // อัพเดทปุ่ม dropdown
                document.getElementById('periodDropdown').innerText = this.innerText;

                // อัพเดทข้อมูลตามช่วงเวลา
                if (period === 'week') {
                    currentLabels = defaultLabelsWeek;
                    currentActivityData = activityDataWeek;
                    currentRunData = runDataWeek;
                    periodTitle = 'สถิติกิจกรรมรายสัปดาห์';
                } else if (period === 'month') {
                    currentLabels = defaultLabelsMonth;
                    currentActivityData = activityDataMonth;
                    currentRunData = runDataMonth;
                    periodTitle = 'สถิติกิจกรรมรายเดือน';
                } else if (period === 'quarter') {
                    currentLabels = defaultLabelsQuarter;
                    currentActivityData = activityDataYear;
                    currentRunData = runDataYear;
                    periodTitle = 'สถิติกิจกรรมรายปี';
                }

                // อัพเดทชื่อหัวข้อ
                document.querySelector('.card-header h5').innerText = periodTitle;

                // อัพเดทกราฟ
                chart.updateOptions({
                    xaxis: {
                        categories: currentLabels
                    }
                });

                chart.updateSeries([
                    {
                        name: 'กิจกรรมทั้งหมด',
                        data: currentActivityData
                    },
                    {
                        name: 'การวิ่ง',
                        data: currentRunData
                    }
                ]);
            });
        });
    });
</script>
<style>
    body {
        background-color: #f9f9f9;
    }
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        transition: box-shadow 0.3s ease-in-out;
    }
    .card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.15) !important;
    }
    .apexcharts-tooltip {
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        border: none;
    }
    .apexcharts-tooltip-title {
        background: #f8f9fa !important;
        border-bottom: 1px solid #eee !important;
    }
    .apexcharts-xaxistooltip {
        border: none;
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
</style>
@endsection
