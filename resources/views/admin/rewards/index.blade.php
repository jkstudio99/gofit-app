@extends('layouts.admin')

@section('title', 'จัดการรางวัล')

@section('content_header')
    <h1>จัดการรางวัล</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">รายการรางวัลทั้งหมด</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> เพิ่มรางวัลใหม่
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>รูปภาพ</th>
                        <th>ชื่อรางวัล</th>
                        <th>คำอธิบาย</th>
                        <th>คะแนนที่ใช้</th>
                        <th>จำนวนคงเหลือ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rewards as $key => $reward)
                    <tr>
                        <td>{{ $rewards->firstItem() + $key }}</td>
                        <td>
                            <img src="{{ asset('storage/'.$reward->reward_image) }}" alt="{{ $reward->reward_name }}" width="50">
                        </td>
                        <td>{{ $reward->reward_name }}</td>
                        <td>{{ $reward->reward_description }}</td>
                        <td>{{ $reward->point_cost }}</td>
                        <td>{{ $reward->stock_quantity }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-sm">แก้ไข</button>
                                <button type="button" class="btn btn-danger btn-sm">ลบ</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $rewards->links() }}
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
