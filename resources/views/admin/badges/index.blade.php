@extends('layouts.admin')

@section('title', 'จัดการเหรียญตรา')

@section('content_header')
    <h1>จัดการเหรียญตรา</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">รายการเหรียญตราทั้งหมด</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> เพิ่มเหรียญตราใหม่
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>รูปภาพ</th>
                        <th>ชื่อเหรียญตรา</th>
                        <th>คำอธิบาย</th>
                        <th>เงื่อนไข</th>
                        <th>คะแนน</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($badges as $key => $badge)
                    <tr>
                        <td>{{ $badges->firstItem() + $key }}</td>
                        <td>
                            <img src="{{ asset('storage/'.$badge->badge_image) }}" alt="{{ $badge->badge_name }}" width="50">
                        </td>
                        <td>{{ $badge->badge_name }}</td>
                        <td>{{ $badge->badge_description }}</td>
                        <td>{{ $badge->condition_text }}</td>
                        <td>{{ $badge->points }}</td>
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
            {{ $badges->links() }}
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
