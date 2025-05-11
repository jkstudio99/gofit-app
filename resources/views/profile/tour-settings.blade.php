@extends('layouts.app')

@section('title', 'ตั้งค่าการแนะนำการใช้งาน')

@section('styles')
<style>
    .tour-settings-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 0.75rem;
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
        padding: 1.5rem 1.25rem;
    }

    .tour-card-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .tour-card-body {
        padding: 1.5rem 1.25rem;
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
        flex-direction: column;
        margin-bottom: 1rem;
    }

    .page-tour-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
    }

    .page-tour-status {
        display: inline-block;
        padding: 0.3rem 0.7rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 500;
        align-self: flex-start;
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
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    .reset-all-button {
        margin-top: 1.5rem;
    }

    /* Button styling to match GoFit */
    .restart-tour-btn {
        border-radius: 50px;
        border-color: #2ecc71;
        color: #2ecc71;
    }

    .restart-tour-btn:hover {
        background-color: #2ecc71;
        color: white !important;
        border-color: #2ecc71;
    }

    .restart-tour-btn:hover i {
        color: white !important;
    }

    #reset-all-tours {
        border-radius: 50px;
        background-color: #f39c12;
        border-color: #f39c12;
        color: white !important;
    }

    #reset-all-tours i {
        color: white !important;
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

    /* Mobile responsiveness */
    @media (min-width: 576px) {
        .page-tour-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .page-tour-title {
            margin: 0;
        }
    }

    @media (max-width: 575.98px) {
        .tour-card-header {
            padding: 1.25rem 1rem;
        }

        .tour-card-header h3 {
            font-size: 1.3rem;
        }

        .tour-card-body {
            padding: 1.25rem 1rem;
        }

        .page-tour-actions {
            flex-direction: column;
            align-items: flex-start;
        }

        .form-check.form-switch {
            margin-left: 0 !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-3">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/tour-settings-fix.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ปุ่มเริ่มทัวร์ใหม่
        document.querySelectorAll('.restart-tour-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tourKey = this.getAttribute('data-tour-key');

                // Clear any previously set localStorage values for this tour
                localStorage.removeItem(`tour_${tourKey}_skipped`);
                localStorage.removeItem(`tour_${tourKey}_completed`);

                // Set the tour to pending and show it again
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
                        let tourPage = '/';

                        // Redirect to the appropriate page based on tour key
                        if (tourKey === 'dashboard') {
                            tourPage = '/';
                        } else if (tourKey === 'run') {
                            tourPage = '/run';
                        } else if (tourKey === 'badges') {
                            tourPage = '/badges';
                        } else if (tourKey === 'rewards') {
                            tourPage = '/rewards';
                        }

                        // Show success message with SweetAlert
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: 'รีเซ็ตการแนะนำเรียบร้อยแล้ว',
                            icon: 'success',
                            confirmButtonColor: '#2DC679',
                            confirmButtonText: 'ไปที่หน้านั้นเลย',
                            showCancelButton: true,
                            cancelButtonText: 'อยู่หน้านี้ต่อ',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = tourPage;
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                });
            });
        });

        // Toggle การแสดงทัวร์อีกครั้ง
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

                // Show toast notification
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: showAgain ? 'เปิดการแสดงอีกครั้ง' : 'ปิดการแสดงอีกครั้ง'
                });
            });
                });

        // ปุ่มรีเซ็ตทั้งหมด (now handled by tour-settings-fix.js)
    });
</script>
@endsection
