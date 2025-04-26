@extends('layouts.admin')

@section('title', 'ประวัติการแลกรางวัล')

@section('content_header')
    <h1>ประวัติการแลกรางวัล</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">รายการแลกรางวัลทั้งหมด</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>ผู้ใช้</th>
                        <th>รางวัล</th>
                        <th>คะแนนที่ใช้</th>
                        <th>สถานะ</th>
                        <th>วันที่แลก</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($redeems as $key => $redeem)
                    <tr>
                        <td>{{ $redeems->firstItem() + $key }}</td>
                        <td>{{ $redeem->user->username }}</td>
                        <td>{{ $redeem->reward->reward_name }}</td>
                        <td>{{ $redeem->points_used }}</td>
                        <td>
                            @if($redeem->status == 'pending')
                                <span class="badge bg-warning">รอดำเนินการ</span>
                            @elseif($redeem->status == 'completed')
                                <span class="badge bg-success">เสร็จสิ้น</span>
                            @elseif($redeem->status == 'cancelled')
                                <span class="badge bg-danger">ยกเลิก</span>
                            @endif
                        </td>
                        <td>{{ $redeem->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-sm">อนุมัติ</button>
                                <button type="button" class="btn btn-danger btn-sm">ยกเลิก</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $redeems->links() }}
        </div>
    </div>
@stop

@section('css')
    <style>
        .btn-group .btn {
            margin-right: 5px;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            $('.table').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@stop
