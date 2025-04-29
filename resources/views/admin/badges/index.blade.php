@extends('layouts.admin')

@section('title', 'จัดการเหรียญตรา')

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .badge-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .badge-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .badge-img-container {
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .badge-img {
        max-height: 100px;
        max-width: 100px;
        object-fit: contain;
    }

    .badge-type {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 0.7rem;
    }

    .badge-stats {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 0.7rem;
    }

    .search-box {
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        padding-left: 20px;
    }

    .search-box:focus {
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
        border-color: #2DC679;
    }

    .badge-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
    }

    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover, .filter-badge.active {
        background-color: #2DC679 !important;
        color: white;
    }

    .sort-icon {
        font-size: 0.8rem;
        margin-left: 5px;
    }

    /* Advanced filter panel */
    .filter-panel {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">จัดการเหรียญตรา</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.badges.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>เพิ่มเหรียญตราใหม่
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.badges.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box"
                               placeholder="ค้นหาตามชื่อหรือคำอธิบาย..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 text-md-end">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                        <i class="fas fa-filter me-1"></i> ตัวกรองขั้นสูง
                    </button>
                </div>

                <div class="col-12 collapse {{ request()->hasAny(['type', 'sort']) ? 'show' : '' }}" id="advancedFilters">
                    <div class="filter-panel mt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">ประเภทเหรียญตรา</label>
                                <select name="type" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach($badgeTypes as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            @if($type == 'distance')
                                                ระยะทาง
                                            @elseif($type == 'calories')
                                                แคลอรี่
                                            @elseif($type == 'streak')
                                                ต่อเนื่อง
                                            @elseif($type == 'speed')
                                                ความเร็ว
                                            @elseif($type == 'event')
                                                กิจกรรม
                                            @else
                                                {{ $type }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">เรียงตาม</label>
                                <select name="sort" class="form-select">
                                    <option value="created_at" {{ $sortField == 'created_at' ? 'selected' : '' }}>วันที่สร้าง</option>
                                    <option value="badge_name" {{ $sortField == 'badge_name' ? 'selected' : '' }}>ชื่อเหรียญตรา</option>
                                    <option value="type" {{ $sortField == 'type' ? 'selected' : '' }}>ประเภท</option>
                                    <option value="criteria" {{ $sortField == 'criteria' ? 'selected' : '' }}>เกณฑ์</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">ลำดับ</label>
                                <select name="direction" class="form-select">
                                    <option value="asc" {{ $sortDirection == 'asc' ? 'selected' : '' }}>น้อยไปมาก</option>
                                    <option value="desc" {{ $sortDirection == 'desc' ? 'selected' : '' }}>มากไปน้อย</option>
                                </select>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i> รีเซ็ตตัวกรอง
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> กรอง
                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Type Filter Tags -->
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.badges.index', request()->except('type')) }}"
               class="badge bg-{{ request('type') ? 'light text-dark' : 'primary' }} py-2 px-3 filter-badge">
                <i class="fas fa-medal me-1"></i> ทั้งหมด
            </a>

            @foreach($badgeTypes as $type)
                <a href="{{ route('admin.badges.index', array_merge(request()->except('type'), ['type' => $type])) }}"
                   class="badge bg-{{ request('type') == $type ? 'primary' : 'light text-dark' }} py-2 px-3 filter-badge">
                    @if($type == 'distance')
                        <i class="fas fa-route me-1"></i> ระยะทาง
                    @elseif($type == 'calories')
                        <i class="fas fa-fire-alt me-1"></i> แคลอรี่
                    @elseif($type == 'streak')
                        <i class="fas fa-calendar-check me-1"></i> ต่อเนื่อง
                    @elseif($type == 'speed')
                        <i class="fas fa-tachometer-alt me-1"></i> ความเร็ว
                    @elseif($type == 'event')
                        <i class="fas fa-calendar-day me-1"></i> กิจกรรม
                    @else
                        <i class="fas fa-medal me-1"></i> {{ $type }}
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Results -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">
                    <i class="fas fa-medal me-2 text-primary"></i>รายการเหรียญตรา
                    @if(request()->hasAny(['search', 'type', 'sort']))
                    <span class="badge bg-success ms-2">
                        <i class="fas fa-filter me-1"></i> กำลังแสดงผลลัพธ์: {{ $badges->total() }} รายการ
                    </span>
                    @endif
                </h5>
                <span class="badge bg-info rounded-pill">
                    <i class="fas fa-medal me-1"></i> เหรียญตราทั้งหมด: {{ $badges->total() }}
                </span>
            </div>
        </div>

        <div class="card-body">
            @if($badges->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-medal fa-4x"></i>
                    </div>
                    <h5>ไม่พบข้อมูลเหรียญตรา</h5>
                    <p class="text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</p>
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($badges as $badge)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm badge-card">
                            <div class="badge-type">
                                @if($badge->type == 'distance')
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-route me-1"></i> ระยะทาง
                                    </span>
                                @elseif($badge->type == 'calories')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-fire-alt me-1"></i> แคลอรี่
                                    </span>
                                @elseif($badge->type == 'streak')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-calendar-check me-1"></i> ต่อเนื่อง
                                    </span>
                                @elseif($badge->type == 'speed')
                                    <span class="badge bg-success">
                                        <i class="fas fa-tachometer-alt me-1"></i> ความเร็ว
                                    </span>
                                @elseif($badge->type == 'event')
                                    <span class="badge bg-primary">
                                        <i class="fas fa-calendar-day me-1"></i> กิจกรรม
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-medal me-1"></i> {{ $badge->type }}
                                    </span>
                                @endif
                            </div>

                            <div class="badge-stats">
                                <a href="{{ route('admin.badges.users', $badge) }}" class="badge bg-light text-dark">
                                    <i class="fas fa-users me-1"></i> {{ $badge->users_count }} คนได้รับ
                                </a>
                            </div>

                            <div class="badge-img-container">
                                @if($badge->badge_image)
                                    <img src="{{ asset('storage/' . $badge->badge_image) }}" alt="{{ $badge->badge_name }}" class="badge-img">
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-medal fa-3x"></i>
                                        <p class="small mt-2">ไม่มีรูปภาพ</p>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $badge->badge_name }}</h5>
                                <p class="card-text text-muted small">{{ Str::limit($badge->badge_description, 100) }}</p>

                                <div class="mt-2">
                                    <strong class="d-block">เงื่อนไข:</strong>
                                    <span class="badge bg-light text-dark">{{ $badge->getRequirementText() }}</span>
                                </div>
                            </div>

                            <div class="card-footer bg-white py-3 border-top-0">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('admin.badges.show', $badge) }}" class="btn btn-sm btn-info badge-action-btn me-2" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-sm btn-warning badge-action-btn me-2" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger badge-action-btn delete-badge"
                                            title="ลบ" data-badge-id="{{ $badge->badge_id }}"
                                            data-badge-name="{{ $badge->badge_name }}"
                                            data-users-count="{{ $badge->users_count }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    แสดง {{ $badges->firstItem() ?? 0 }} ถึง {{ $badges->lastItem() ?? 0 }} จาก {{ $badges->total() }} รายการ
                </div>
                <div class="pagination-container">
                    {{ $badges->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    // กำหนดค่าเริ่มต้นสำหรับ SweetAlert2 ทั้งหมด
    window.addEventListener('load', function() {
        Swal.mixin({
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            }
        });

        // กำหนดสี CSS สำหรับปุ่ม SweetAlert
        const style = document.createElement('style');
        style.innerHTML = `
            .swal2-confirm.swal-confirm-btn {
                background-color: #2DC679 !important;
                border-color: #2DC679 !important;
                box-shadow: none !important;
                margin-right: 10px;
        }
            .swal2-confirm:focus {
                box-shadow: 0 0 0 3px rgba(45, 198, 121, 0.3) !important;
            }
            .swal2-actions {
                justify-content: center !important;
                gap: 10px;
            }
        `;
        document.head.appendChild(style);
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation with SweetAlert2
        const deleteButtons = document.querySelectorAll('.delete-badge');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const badgeId = this.getAttribute('data-badge-id');
                const badgeName = this.getAttribute('data-badge-name');
                const usersCount = parseInt(this.getAttribute('data-users-count'));

                let warningText = 'คุณแน่ใจหรือไม่ที่จะลบเหรียญตรา?';
                if (usersCount > 0) {
                    warningText += ` มีผู้ใช้ ${usersCount} คนที่ได้รับเหรียญตรานี้ ซึ่งจะถูกลบออกด้วย`;
                }

                Swal.fire({
                    title: `ลบเหรียญตรา "${badgeName}"?`,
                    html: `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            คำเตือน: การลบเหรียญตราไม่สามารถกู้คืนได้
                        </div>
                        <p class="mt-3">${warningText}</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2DC679', // GoFit primary color
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> ลบเหรียญตรา',
                    cancelButtonText: 'ยกเลิก',
                    buttonsStyling: true,
                    reverseButtons: false,
                    customClass: {
                        confirmButton: 'swal-confirm-btn',
                        actions: 'justify-content-center gap-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // สร้าง form สำหรับ submit การลบ
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/badges/${badgeId}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
            });
        });
    </script>
@endsection
