@extends('layouts.user')

@section('title', 'จัดการอุปกรณ์ที่ล็อกอิน - GoFit')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">
        <i class="fas fa-shield-alt text-primary me-2"></i>
        จัดการอุปกรณ์ที่ล็อกอิน
    </h1>

    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        หากคุณพบอุปกรณ์ที่ไม่คุ้นเคย คุณสามารถล็อกเอาท์จากอุปกรณ์นั้นได้ทันที เพื่อความปลอดภัยของบัญชีของคุณ
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

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-laptop me-2 text-primary"></i>
                อุปกรณ์ที่ล็อกอินปัจจุบัน
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>อุปกรณ์</th>
                            <th>ที่อยู่ IP</th>
                            <th>เริ่มใช้งานเมื่อ</th>
                            <th>ใช้งานล่าสุด</th>
                            <th>หมดอายุ</th>
                            <th>สถานะ</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr class="{{ $session->isCurrentSession() ? 'table-primary' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if(str_contains(strtolower($session->device_info), 'มือถือ'))
                                            <i class="fas fa-mobile-alt me-2 text-primary"></i>
                                        @elseif(str_contains(strtolower($session->device_info), 'แท็บเล็ต'))
                                            <i class="fas fa-tablet-alt me-2 text-primary"></i>
                                        @else
                                            <i class="fas fa-laptop me-2 text-primary"></i>
                                        @endif
                                        <div>
                                            <div>{{ $session->device_info }}</div>
                                            @if($session->isCurrentSession())
                                                <span class="badge bg-primary">อุปกรณ์ปัจจุบัน</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $session->ip_address }}</td>
                                <td>{{ $session->created_at->locale('th')->diffForHumans() }}</td>
                                <td>{{ $session->updated_at->locale('th')->diffForHumans() }}</td>
                                <td>{{ $session->expired_at->locale('th')->format('d M Y H:i') }}</td>
                                <td>
                                    @if($session->expired_at > now())
                                        <span class="badge bg-success">ใช้งานอยู่</span>
                                    @else
                                        <span class="badge bg-danger">หมดอายุ</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$session->isCurrentSession())
                                        <form action="{{ route('user.sessions.destroy', $session->session_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการล็อกเอาท์จากอุปกรณ์นี้?')">
                                                <i class="fas fa-sign-out-alt me-1"></i> ล็อกเอาท์
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-exclamation-circle me-2 text-muted"></i>
                                    ไม่พบข้อมูลอุปกรณ์ที่ล็อกอิน
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <form action="{{ route('user.sessions.destroy.all') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('ยืนยันการล็อกเอาท์จากทุกอุปกรณ์? (ยกเว้นอุปกรณ์ปัจจุบัน)')">
                <i class="fas fa-sign-out-alt me-2"></i> ล็อกเอาท์จากทุกอุปกรณ์
            </button>
        </form>
    </div>
</div>
@endsection
