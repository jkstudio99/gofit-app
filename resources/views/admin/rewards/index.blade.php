@extends('layouts.admin')

@section('title', 'จัดการรางวัล')

@section('content_header')
    <h1>จัดการรางวัล</h1>
@stop

@section('content')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-check"></i> สำเร็จ!</h5>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-ban"></i> ผิดพลาด!</h5>
        {{ session('error') }}
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">รายการรางวัลทั้งหมด</h3>
            <div class="card-tools">
                <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> เพิ่มรางวัลใหม่
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th style="width: 100px">รูปภาพ</th>
                        <th>ชื่อรางวัล</th>
                        <th>คำอธิบาย</th>
                        <th style="width: 100px">คะแนนที่ใช้</th>
                        <th style="width: 100px">จำนวนคงเหลือ</th>
                        <th>สถานะ</th>
                        <th style="width: 150px">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rewards as $key => $reward)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-center">
                            @if($reward->image_path)
                                <img src="{{ asset('storage/' . $reward->image_path) }}" alt="{{ $reward->name }}" class="img-thumbnail" style="height: 50px; object-fit: contain;">
                            @else
                                @if(strpos(strtolower($reward->name), 'bottle') !== false || strpos(strtolower($reward->name), 'ขวดน้ำ') !== false)
                                    <img src="{{ asset('images/rewards/bottle.png') }}" alt="{{ $reward->name }}" class="img-thumbnail" style="height: 50px; object-fit: contain;">
                                @elseif(strpos(strtolower($reward->name), 'cap') !== false || strpos(strtolower($reward->name), 'หมวก') !== false)
                                    <img src="{{ asset('images/rewards/cap.png') }}" alt="{{ $reward->name }}" class="img-thumbnail" style="height: 50px; object-fit: contain;">
                                @elseif(strpos(strtolower($reward->name), 'shirt') !== false || strpos(strtolower($reward->name), 'เสื้อ') !== false)
                                    <img src="{{ asset('images/rewards/tshirt.png') }}" alt="{{ $reward->name }}" class="img-thumbnail" style="height: 50px; object-fit: contain;">
                                @else
                                    <img src="{{ asset('images/rewards/gift.png') }}" alt="{{ $reward->name }}" class="img-thumbnail" style="height: 50px; object-fit: contain;">
                                @endif
                            @endif
                        </td>
                        <td>{{ $reward->name }}</td>
                        <td>{{ Str::limit($reward->description, 100) }}</td>
                        <td class="text-center">
                            <span class="badge bg-warning">
                                <i class="fas fa-coins"></i> {{ number_format($reward->points_required) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($reward->quantity > 10)
                                <span class="badge bg-success">{{ $reward->quantity }} ชิ้น</span>
                            @elseif($reward->quantity > 0)
                                <span class="badge bg-warning">{{ $reward->quantity }} ชิ้น</span>
                            @else
                                <span class="badge bg-danger">หมด</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($reward->is_enabled)
                                <span class="badge bg-success">เปิดใช้งาน</span>
                            @else
                                <span class="badge bg-secondary">ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.rewards.edit', $reward->reward_id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </a>
                                <form action="{{ route('admin.rewards.destroy', $reward->reward_id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรางวัลนี้?')">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    @if(count($rewards) == 0)
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-gift fa-3x mb-3"></i>
                                <p>ยังไม่มีรางวัลในระบบ</p>
                                <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-plus"></i> เพิ่มรางวัลใหม่
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if(method_exists($rewards, 'links'))
        <div class="card-footer clearfix">
            {{ $rewards->links() }}
        </div>
        @endif
    </div>
@stop

@section('css')
    <style>
        .btn-group .btn {
            margin-right: 5px;
        }
        .img-thumbnail {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
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
