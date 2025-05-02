@extends('layouts.admin')

@section('title', 'จัดการ Sessions - GoFit Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shield-alt text-primary me-2"></i>
            จัดการ Sessions
        </h1>
        <div>
            <a href="{{ route('admin.sessions.index') }}" class="btn btn-primary me-2">
                <i class="fas fa-sync-alt me-1"></i>
                รีเฟรช
            </a>
            <a href="{{ route('admin.sessions.clear-expired') }}" class="btn btn-warning">
                <i class="fas fa-broom me-2"></i>
                ล้าง Sessions ที่หมดอายุ
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">รายการ Sessions ทั้งหมด <span class="badge bg-secondary ms-2">{{ $sessions->total() }}</span></h6>
            <form action="{{ route('admin.sessions.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="ค้นหาตาม IP หรือ ชื่อผู้ใช้" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px">ID</th>
                            <th style="width: 150px">ผู้ใช้</th>
                            <th>อุปกรณ์</th>
                            <th style="width: 120px">ที่อยู่ IP</th>
                            <th style="width: 150px">สร้างเมื่อ</th>
                            <th style="width: 150px">ล่าสุด</th>
                            <th style="width: 150px">หมดอายุ</th>
                            <th style="width: 100px">สถานะ</th>
                            <th style="width: 100px">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>{{ $session->session_id }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $session->user_id) }}">
                                        {{ $session->user->firstname ?? '' }} {{ $session->user->lastname ?? '' }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if(str_contains(strtolower($session->device_info), 'มือถือ'))
                                            <i class="fas fa-mobile-alt me-2 text-primary"></i>
                                        @elseif(str_contains(strtolower($session->device_info), 'แท็บเล็ต'))
                                            <i class="fas fa-tablet-alt me-2 text-primary"></i>
                                        @else
                                            <i class="fas fa-laptop me-2 text-primary"></i>
                                        @endif
                                        <span>{{ $session->device_info }}</span>
                                    </div>
                                </td>
                                <td>{{ $session->ip_address }}</td>
                                <td>{{ $session->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $session->updated_at->format('d M Y H:i') }}</td>
                                <td>{{ $session->expired_at->format('d M Y H:i') }}</td>
                                <td>
                                    @if($session->expired_at > now())
                                        <span class="badge bg-success">ใช้งานอยู่</span>
                                    @else
                                        <span class="badge bg-danger">หมดอายุ</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.sessions.destroy', $session->session_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ session นี้?')">
                                            <i class="fas fa-trash me-1"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-exclamation-circle me-2 text-muted"></i>
                                    ไม่พบข้อมูล Sessions
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $sessions->links() }}
            </div>

            <div class="mt-3">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success me-2">ใช้งานอยู่</span>
                    <span>Sessions ที่ยังไม่หมดอายุและสามารถใช้งานได้</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-danger me-2">หมดอายุ</span>
                    <span>Sessions ที่หมดอายุแล้วและไม่สามารถใช้งานได้ (ควรลบออกจากระบบ)</span>
                </div>
            </div>

            <div class="mt-4 text-muted small">
                <p>อัพเดทข้อมูลล่าสุด: {{ now()->format('d M Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
