@extends('layouts.admin')

@section('title', 'รายงานผู้ใช้งาน')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 fw-bold">รายงานผู้ใช้งาน</h1>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> กลับสู่หน้ารายงาน
        </a>
    </div>

    <div class="row mb-4">
        <!-- สรุปจำนวนผู้ใช้ -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">ภาพรวมผู้ใช้งาน</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="bg-light rounded p-3 text-center">
                                <h2 class="fw-bold text-primary mb-0">{{ number_format($usersCount) }}</h2>
                                <p class="text-muted mb-0">ผู้ใช้งานทั้งหมด</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="bg-light rounded p-3 text-center">
                                <h2 class="fw-bold text-success mb-0">{{ number_format($newUsersThisMonth) }}</h2>
                                <p class="text-muted mb-0">ผู้ใช้ใหม่เดือนนี้</p>
                            </div>
                        </div>
                    </div>
                    <div id="users-registration-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- ผู้ใช้งานที่มีกิจกรรมมากที่สุด -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">ผู้ใช้งานที่มีกิจกรรมมากที่สุด</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">ผู้ใช้งาน</th>
                                    <th class="text-center">จำนวนกิจกรรม</th>
                                    <th class="text-end pe-3">สัดส่วน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeUsers as $user)
                                <tr>
                                    <td class="ps-3 align-middle">
                                        <div class="d-flex align-items-center">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle me-2" width="40" height="40" alt="{{ $user->username }}">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $user->firstname }} {{ $user->lastname }}</div>
                                                <div class="small text-muted">{{ $user->username }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">{{ number_format($user->activities_count) }}</td>
                                    <td class="text-end pe-3 align-middle">
                                        @php
                                            $maxCount = $activeUsers->max('activities_count');
                                            $percentage = ($maxCount > 0) ? ($user->activities_count / $maxCount * 100) : 0;
                                        @endphp
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- ตารางข้อมูลผู้ใช้งานทั้งหมด -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 fw-bold">ข้อมูลผู้ใช้งานทั้งหมด</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ชื่อผู้ใช้</th>
                                    <th>อีเมล</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>วันที่สมัคร</th>
                                    <th>จำนวนกิจกรรม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeUsers as $user)
                                <tr>
                                    <td>{{ $user->user_id }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>{{ number_format($user->activities_count) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // กราฟแสดงการสมัครใช้งานรายเดือน
        const usersByMonth = @json($usersByMonth);

        const monthLabels = [];
        const userData = [];

        usersByMonth.forEach(item => {
            const monthNames = [
                'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
            ];
            monthLabels.push(monthNames[item.month - 1]);
            userData.push(item.count);
        });

        const options = {
            series: [{
                name: 'จำนวนผู้ใช้',
                data: userData
            }],
            chart: {
                height: 300,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#4e73df'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: monthLabels
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return Math.round(val) + " คน";
                    }
                }
            }
        };

        const chart = new ApexCharts(document.getElementById('users-registration-chart'), options);
        chart.render();

        // DataTable สำหรับตารางผู้ใช้
        $('#users-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    className: 'btn btn-sm btn-success',
                    title: 'รายงานผู้ใช้งาน GoFit',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv me-1"></i> CSV',
                    className: 'btn btn-sm btn-primary',
                    title: 'รายงานผู้ใช้งาน GoFit',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                    className: 'btn btn-sm btn-danger',
                    title: 'รายงานผู้ใช้งาน GoFit',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    },
                    customize: function(doc) {
                        doc.defaultStyle.font = 'THSarabunNew';
                        doc.styles.tableHeader.fontSize = 14;
                        doc.defaultStyle.fontSize = 12;
                    }
                }
            ]
        });
    });
</script>
@endsection
