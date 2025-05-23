@extends('layouts.admin')

@section('title', 'รายงานและสถิติ')

@section('styles')
<style>
    /* ทำให้ข้อความและไอคอนกลายเป็นสีขาวเมื่อ hover */
    .btn-outline-primary:hover,
    .btn-outline-success:hover,
    .btn-outline-info:hover,
    .btn-outline-danger:hover,
    .btn-outline-secondary:hover {
        color: white !important;
    }

    .btn-outline-primary:hover i,
    .btn-outline-success:hover i,
    .btn-outline-info:hover i,
    .btn-outline-danger:hover i,
    .btn-outline-secondary:hover i {
        color: white !important;
    }

    /* เพิ่มเอฟเฟกต์การเปลี่ยนสีที่นุ่มนวลขึ้น */
    .btn i, .btn {
        transition: all 0.3s ease;
    }
</style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0">รายงานและสถิติ</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> กลับสู่แดชบอร์ด
        </a>
    </div>

    <!-- ส่วนรายงานและสถิติทั้งหมด -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="p-4 bg-success bg-opacity-10">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-success bg-opacity-25 d-flex align-items-center justify-content-center p-3 me-3">
                                <i class="fas fa-running fa-2x text-success"></i>
                            </div>
                            <h5 class="fw-bold m-0">สถิติการวิ่ง</h5>
                        </div>
                        <p class="text-muted">วิเคราะห์ข้อมูลเกี่ยวกับการวิ่งของผู้ใช้ ระยะทาง ความเร็ว แคลอรี่</p>
                    </div>
                    <div class="p-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0 px-0 py-2">
                                <a href="{{ route('admin.run.stats') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-chart-line me-2 text-success"></i> สรุปภาพรวมการวิ่ง</span>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </a>
                            </li>
                            <li class="list-group-item border-0 px-0 py-2">
                                <a href="{{ route('admin.run.calendar') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-calendar-alt me-2 text-success"></i> ปฏิทินการวิ่ง</span>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="p-4 bg-warning bg-opacity-10">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center p-3 me-3">
                                <i class="fas fa-medal fa-2x text-warning"></i>
                            </div>
                            <h5 class="fw-bold m-0">เหรียญตราและรางวัล</h5>
                        </div>
                        <p class="text-muted">วิเคราะห์การมอบเหรียญตราและการแลกของรางวัลของผู้ใช้งาน</p>
                    </div>
                    <div class="p-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0 px-0 py-2">
                                <a href="{{ route('admin.badges.statistics') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-chart-pie me-2 text-warning"></i> สถิติเหรียญตรา</span>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </a>
                            </li>
                            <li class="list-group-item border-0 px-0 py-2">
                                <a href="{{ route('admin.rewards.statistics') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-gift me-2 text-warning"></i> สถิติรางวัล</span>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="p-4 bg-info bg-opacity-10">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-info bg-opacity-25 d-flex align-items-center justify-content-center p-3 me-3">
                                <i class="fas fa-newspaper fa-2x text-info"></i>
                            </div>
                            <h5 class="fw-bold m-0">บทความและเป้าหมาย</h5>
                        </div>
                        <p class="text-muted">วิเคราะห์การอ่านบทความและการตั้งเป้าหมายของผู้ใช้งาน</p>
                    </div>
                    <div class="p-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0 px-0 py-2">
                                <a href="{{ route('admin.health-articles.statistics') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-chart-bar me-2 text-info"></i> สถิติบทความสุขภาพ</span>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </a>
                            </li>
                            <li class="list-group-item border-0 px-0 py-2">
                                <a href="{{ route('admin.health-articles.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-list me-2 text-info"></i> จัดการบทความทั้งหมด</span>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ส่วนรายงานแบบลึก -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold">รายงานเชิงลึก</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold">รายงานผู้ใช้งาน</h6>
                                    <p class="text-muted small">ข้อมูลเชิงลึกเกี่ยวกับผู้ใช้งานในระบบ</p>
                                    <hr>
                                    <div class="d-grid">
                                        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('admin.reports.users') }}'">
                                            <i class="fas fa-users me-1"></i> ดูรายงาน
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold">รายงานกิจกรรมประจำเดือน</h6>
                                    <p class="text-muted small">ข้อมูลกิจกรรมในแต่ละเดือน</p>
                                    <hr>
                                    <div class="d-grid">
                                        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('admin.reports.monthly') }}'">
                                            <i class="fas fa-chart-line me-1"></i> ดูรายงาน
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold">รายงานสรุปประจำปี</h6>
                                    <p class="text-muted small">ข้อมูลสรุปภาพรวมของระบบทั้งปี</p>
                                    <hr>
                                    <div class="d-grid">
                                        <button class="btn btn-outline-info" onclick="window.location.href='{{ route('admin.reports.yearly') }}'">
                                            <i class="fas fa-file-alt me-1"></i> ดูรายงาน
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // รายงานผู้ใช้งาน
        document.querySelector('.btn-outline-success').addEventListener('click', function() {
            window.location.href = "{{ route('admin.reports.users') }}";
        });

        // รายงานรายเดือน
        document.querySelector('.btn-outline-primary').addEventListener('click', function() {
            window.location.href = "{{ route('admin.reports.monthly') }}";
        });

        // รายงานประจำปี
        document.querySelector('.btn-outline-danger').addEventListener('click', function() {
            window.location.href = "{{ route('admin.reports.yearly') }}";
        });

        // Export ข้อมูล
        $('#export-excel').click(function() {
            window.location.href = "{{ route('admin.reports.users') }}";
        });

        $('#export-csv').click(function() {
            window.location.href = "{{ route('admin.reports.monthly') }}";
        });
    });
</script>
@endsection
