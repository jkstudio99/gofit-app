@extends('layouts.app')

@section('title', 'ตั้งค่าการแนะนำการใช้งาน')

@section('styles')
<style>
    .tour-settings-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .tour-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .tour-card-header {
        background: linear-gradient(135deg, #3498db, #2ecc71);
        color: white;
        padding: 1.5rem;
    }

    .tour-card-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .tour-card-body {
        padding: 1.5rem;
    }

    .page-tour-item {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f1f1f1;
    }

    .page-tour-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .page-tour-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .page-tour-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    .page-tour-status {
        display: inline-block;
        padding: 0.3rem 0.7rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-completed {
        background-color: #e8f5e9;
        color: #2ecc71;
    }

    .status-skipped {
        background-color: #fff8e1;
        color: #f39c12;
    }

    .status-pending {
        background-color: #e3f2fd;
        color: #3498db;
    }

    .page-tour-actions {
        margin-top: 1rem;
    }

    .page-tour-actions .btn {
        margin-right: 0.5rem;
    }

    .reset-all-button {
        margin-top: 1rem;
    }

    /* Button styling to match GoFit */
    .restart-tour-btn {
        border-radius: 50px;
        border-color: #2ecc71;
        color: #2ecc71;
    }

    .restart-tour-btn:hover {
        background-color: #2ecc71;
        color: white;
        border-color: #2ecc71;
    }

    #reset-all-tours {
        border-radius: 50px;
        background-color: #f39c12;
        border-color: #f39c12;
    }

    #reset-all-tours:hover {
        background-color: #e67e22;
        border-color: #e67e22;
    }

    /* Switch styling */
    .form-check-input:checked {
        background-color: #2ecc71;
        border-color: #2ecc71;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="tour-settings-container">
        <div class="tour-card">
            <div class="tour-card-header">
                <h3>ตั้งค่าการแนะนำการใช้งาน</h3>
                <p class="mb-0">คุณสามารถเริ่มการแนะนำการใช้งานใหม่ หรือเปลี่ยนการตั้งค่าได้ที่นี่</p>
            </div>
            <div class="tour-card-body">
                <div class="tours-list">
                    @forelse($tours as $tour)
                        <div class="page-tour-item">
                            <div class="page-tour-header">
                                <h4 class="page-tour-title">
                                    @if($tour->tour_key == 'dashboard')
                                        การแนะนำหน้าแดชบอร์ด
                                    @elseif($tour->tour_key == 'run')
                                        การแนะนำหน้าวิ่ง
                                    @elseif($tour->tour_key == 'badges')
                                        การแนะนำหน้าเหรียญตรา
                                    @elseif($tour->tour_key == 'rewards')
                                        การแนะนำหน้ารางวัล
                                    @else
                                        การแนะนำ {{ $tour->tour_key }}
                                    @endif
                                </h4>
                                <span class="page-tour-status status-{{ $tour->status }}">
                                    @if($tour->status == 'completed')
                                        เสร็จสิ้น
                                    @elseif($tour->status == 'skipped')
                                        ข้าม
                                    @else
                                        รอการแสดง
                                    @endif
                                </span>
                            </div>
                            <div class="page-tour-info">
                                @if($tour->completed_at)
                                    <p class="text-muted small">ดูเมื่อ: {{ $tour->completed_at->diffForHumans() }}</p>
                                @endif
                            </div>
                            <div class="page-tour-actions">
                                <button class="btn btn-sm btn-outline-primary restart-tour-btn"
                                        data-tour-key="{{ $tour->tour_key }}">
                                    เริ่มใหม่
                                </button>
                                <div class="form-check form-switch d-inline-block ms-3">
                                    <input class="form-check-input show-again-toggle" type="checkbox"
                                           id="showAgain{{ $loop->index }}"
                                           data-tour-key="{{ $tour->tour_key }}"
                                           {{ $tour->show_again ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showAgain{{ $loop->index }}">
                                        แสดงอีกครั้ง
                                    </label>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">ยังไม่มีข้อมูลการแนะนำการใช้งาน</p>
                    @endforelse
                </div>

                <div class="text-center reset-all-button">
                    <button id="reset-all-tours" class="btn btn-warning">
                        <i class="fas fa-redo me-1"></i> รีเซ็ตการแนะนำทั้งหมด
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ปุ่มเริ่มทัวร์ใหม่
        document.querySelectorAll('.restart-tour-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tourKey = this.getAttribute('data-tour-key');
                const url = tourKey === 'dashboard' ? '/dashboard' :
                            tourKey === 'run' ? '/run' :
                            tourKey === 'badges' ? '/badges' :
                            tourKey === 'rewards' ? '/rewards' : '/';

                // อัปเดตสถานะเป็น pending
                fetch('/tour/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        tour_key: tourKey,
                        status: 'pending',
                        show_again: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ไปยังหน้าที่ต้องการพร้อมแสดงทัวร์
                        window.location.href = url + '?tour=show';
                    }
                });
            });
        });

        // Toggle แสดงอีกครั้ง
        document.querySelectorAll('.show-again-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const tourKey = this.getAttribute('data-tour-key');
                const showAgain = this.checked;

                fetch('/tour/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        tour_key: tourKey,
                        status: 'pending',
                        show_again: showAgain
                    })
                });
            });
        });

        // ปุ่มรีเซ็ตทั้งหมด
        document.getElementById('reset-all-tours').addEventListener('click', function() {
            if (confirm('คุณต้องการรีเซ็ตการแนะนำการใช้งานทั้งหมดใช่หรือไม่?')) {
                fetch('/tour/reset', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }
        });
    });
</script>
@endsection
