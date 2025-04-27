@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-0">เหรียญตรา</h2>
    <p class="text-muted">รวบรวมเหรียญตราจากการวิ่งของคุณ</p>

    <div class="row mb-4 g-4">
        <div class="col-md-12">
            <div class="card h-100 p-3 shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">เหรียญตราของคุณ</h4>
                    <p class="text-muted">รับเหรียญตราเมื่อคุณทำกิจกรรมต่างๆ ได้ตามเป้าหมาย</p>

                    <div class="row row-cols-2 row-cols-md-5 g-3 mt-2">
                        @foreach ($badges as $badge)
                            <div class="col">
                                <div class="text-center position-relative"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="{{ $badge->badge_desc }} - {{ $badge->isUnlocked() ? 'ปลดล็อคแล้ว' : $badge->progress() }}">
                                    <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                        class="img-fluid rounded-circle p-1 {{ $badge->isUnlocked() ? 'border border-2 border-success' : 'opacity-50' }}"
                                        style="width: 80px; height: 80px; object-fit: cover;"
                                        alt="{{ $badge->badge_name }}">
                                    <div class="small mt-2 text-truncate">{{ $badge->badge_name }}</div>
                                    <div class="small mt-1 text-muted">{{ $badge->getRequirementText() }}</div>

                                    @if (!$badge->isUnlocked())
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                            <div class="bg-dark bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                <i class="fas fa-lock text-white"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">เงื่อนไขการรับเหรียญตรา</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ประเภท</th>
                                    <th>เงื่อนไข</th>
                                    <th>คำอธิบาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-road me-2"></i> เหรียญจากระยะทาง</td>
                                    <td>วิ่งให้ได้ตามระยะทางที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อวิ่งได้ระยะทางสะสมตามที่กำหนด เช่น 5 กม., 10 กม., 20 กม., 50 กม., 100 กม.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-fire me-2"></i> เหรียญจากแคลอรี่</td>
                                    <td>เผาผลาญแคลอรี่ตามที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อเผาผลาญแคลอรี่สะสมตามที่กำหนด เช่น 100, 500, 1,000, 2,500, 5,000 แคลอรี่</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-check me-2"></i> เหรียญจากการวิ่งติดต่อกัน</td>
                                    <td>วิ่งติดต่อกันตามจำนวนวันที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อวิ่งติดต่อกันตามจำนวนวันที่กำหนด เช่น 3 วัน, 7 วัน, 14 วัน, 30 วัน</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-tachometer-alt me-2"></i> เหรียญจากความเร็ว</td>
                                    <td>วิ่งด้วยความเร็วเฉลี่ยตามที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อวิ่งด้วยความเร็วเฉลี่ยตามที่กำหนด เช่น 5 กม./ชม., 8 กม./ชม., 10 กม./ชม.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-medal me-2"></i> เหรียญจากการเข้าร่วมกิจกรรม</td>
                                    <td>เข้าร่วมกิจกรรมตามจำนวนครั้งที่กำหนด</td>
                                    <td>คุณจะได้รับเหรียญตราเมื่อเข้าร่วมกิจกรรมตามที่กำหนด เช่น เข้าร่วมกิจกรรม 1 ครั้ง, 3 ครั้ง, 5 ครั้ง</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* เพิ่ม CSS เพื่อแก้ไข dropdown ในหน้าเหรียญตรา */
.dropdown-menu.show {
    display: block !important;
    z-index: 9999 !important;
}
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // เพิ่ม event listener สำหรับ dropdown ในหน้าเหรียญตรา
        var dropdownBtns = document.querySelectorAll('.dropdown-toggle');
        dropdownBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    dropdownMenu.classList.toggle('show');
                }
            });
        });

        // ปิด dropdown เมื่อคลิกที่อื่น
        document.addEventListener('click', function(e) {
            var dropdownMenus = document.querySelectorAll('.dropdown-menu.show');
            dropdownMenus.forEach(function(menu) {
                if (!menu.previousElementSibling.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        });
    });
</script>
@endsection
