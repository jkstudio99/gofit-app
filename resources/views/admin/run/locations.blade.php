@extends('layouts.admin')

@section('title', 'สถานที่ออกกำลังกาย - GoFit Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> สถานที่ออกกำลังกาย</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p class="mb-0"><i class="fas fa-info-circle me-2"></i> แสดงข้อมูลสถานที่ที่ผู้ใช้งานนิยมออกกำลังกาย และสถิติที่เกี่ยวข้อง</p>
                    </div>

                    <!-- ภาพรวมข้อมูล -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 bg-light">
                                <div class="card-body text-center">
                                    <h3 class="display-4 text-primary">{{ $popularLocations }}</h3>
                                    <h5>จำนวนกิจกรรมการวิ่งทั้งหมด</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 bg-light">
                                <div class="card-body text-center">
                                    <h3 class="display-4 text-success">{{ count($runsByArea) }}</h3>
                                    <h5>จำนวนพื้นที่ที่มีการวิ่ง</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 bg-light">
                                <div class="card-body text-center">
                                    <h3 class="display-4 text-warning">{{ $topRunnersInAreas->count() }}</h3>
                                    <h5>นักวิ่งที่มีกิจกรรมสูงสุด</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ตารางข้อมูลสถานที่ยอดนิยม -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> สถานที่ออกกำลังกายยอดนิยม</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>สถานที่</th>
                                            <th>จำนวนการวิ่ง</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($runsByArea as $index => $area)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $area['name'] }}</td>
                                                <td>{{ $area['count'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">ไม่พบข้อมูล</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ตารางข้อมูลนักวิ่ง -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-running me-2"></i> นักวิ่งที่มีกิจกรรมสูงสุด</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ชื่อผู้ใช้</th>
                                            <th>ชื่อ-นามสกุล</th>
                                            <th>จำนวนการวิ่ง</th>
                                            <th>ระยะทางรวม (กม.)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topRunnersInAreas as $index => $runner)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $runner->username }}</td>
                                                <td>{{ $runner->firstname }} {{ $runner->lastname }}</td>
                                                <td>{{ $runner->run_count }}</td>
                                                <td>{{ number_format($runner->total_distance, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">ไม่พบข้อมูล</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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
<script>
    $(document).ready(function() {
        // เพิ่ม DataTables และอื่นๆ ตามต้องการ
        $('table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json'
            }
        });
    });
</script>
@endsection
