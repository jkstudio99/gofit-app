@extends('layouts.admin')

@section('title', 'จัดการผู้ใช้งาน')

@section('content_header')
    <h1>จัดการผู้ใช้งาน</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">รายชื่อผู้ใช้งานทั้งหมด</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>ชื่อผู้ใช้</th>
                        <th>อีเมล</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>ประเภทผู้ใช้</th>
                        <th>วันที่สมัคร</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $key }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                        <td>
                            @if($user->user_type_id == 1)
                                <span class="badge bg-success">ผู้ดูแลระบบ</span>
                            @else
                                <span class="badge bg-primary">ผู้ใช้ทั่วไป</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
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
            {{ $users->links() }}
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
