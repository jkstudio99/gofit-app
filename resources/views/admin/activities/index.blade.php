@extends('layouts.admin')

@section('title', 'กิจกรรมของผู้ใช้')

@section('content_header')
    <h1>กิจกรรมของผู้ใช้ทั้งหมด</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">รายการกิจกรรมทั้งหมด</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>ผู้ใช้</th>
                        <th>ประเภทกิจกรรม</th>
                        <th>ระยะทาง</th>
                        <th>ระยะเวลา</th>
                        <th>วันที่</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $key => $activity)
                    <tr>
                        <td>{{ $activities->firstItem() + $key }}</td>
                        <td>{{ $activity->user->username }}</td>
                        <td>{{ $activity->activity_type }}</td>
                        <td>{{ number_format($activity->distance, 2) }} กม.</td>
                        <td>
                            @if($activity->end_time)
                                @php
                                if(is_string($activity->start_time) || is_string($activity->end_time)) {
                                    $start = is_string($activity->start_time) ? strtotime($activity->start_time) : $activity->start_time->timestamp;
                                    $end = is_string($activity->end_time) ? strtotime($activity->end_time) : $activity->end_time->timestamp;
                                    echo gmdate('H:i:s', $end - $start);
                                } else {
                                    echo gmdate('H:i:s', $activity->end_time->diffInSeconds($activity->start_time));
                                }
                                @endphp
                            @else
                                <span class="badge badge-warning">กำลังดำเนินการ</span>
                            @endif
                        </td>
                        <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-sm">รายละเอียด</button>
                                <button type="button" class="btn btn-danger btn-sm">ลบ</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $activities->links() }}
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
