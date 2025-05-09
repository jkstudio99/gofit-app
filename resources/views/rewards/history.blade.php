@extends('layouts.app')

@section('title', 'ประวัติการแลกรางวัล')

@section('content')
<div class="container py-4">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">ประวัติการแลกรางวัล</h2>
        <a href="{{ route('rewards.index') }}" class="btn btn-outline-primary mobile-history-btn">
            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-md-inline">กลับไปยังหน้ารางวัล</span><span class="d-inline d-md-none">กลับ</span>
        </a>
    </div>
    <p class="text-muted mb-3">ติดตามรางวัลที่คุณเคยแลกและสถานะการจัดส่ง</p>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 reward-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="reward-stat-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-exchange-alt text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">รางวัลทั้งหมด</h6>
                        <h4 class="mb-0">{{ $redeems->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 reward-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="reward-stat-icon bg-warning bg-opacity-10 me-3">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">รอดำเนินการ</h6>
                        <h4 class="mb-0">{{ $redeems->where('status', 'pending')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 reward-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="reward-stat-icon bg-success bg-opacity-10 me-3">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">เสร็จสิ้น</h6>
                        <h4 class="mb-0">{{ $redeems->where('status', 'completed')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card h-100 shadow-sm border-0 reward-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="reward-stat-icon bg-danger bg-opacity-10 me-3">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">ยกเลิก</h6>
                        <h4 class="mb-0">{{ $redeems->where('status', 'cancelled')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Tags -->
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('rewards.history', request()->except('status')) }}"
               class="badge bg-{{ request('status') ? 'light text-dark' : 'primary' }} py-2 px-3 filter-badge">
                <i class="fas fa-list me-1"></i> ทั้งหมด
            </a>

            <a href="{{ route('rewards.history', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
               class="badge bg-{{ request('status') == 'pending' ? 'warning' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-clock me-1"></i> รอดำเนินการ
            </a>

            <a href="{{ route('rewards.history', array_merge(request()->except('status'), ['status' => 'completed'])) }}"
               class="badge bg-{{ request('status') == 'completed' ? 'success' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-check-circle me-1"></i> เสร็จสิ้น
            </a>

            <a href="{{ route('rewards.history', array_merge(request()->except('status'), ['status' => 'cancelled'])) }}"
               class="badge bg-{{ request('status') == 'cancelled' ? 'danger' : 'light text-dark' }} py-2 px-3 filter-badge">
                <i class="fas fa-times-circle me-1"></i> ยกเลิก
            </a>
        </div>
    </div>

    <!-- Redemption History Table/Cards -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">รายการแลกรางวัล</h5>
            <div class="d-flex align-items-center mt-2 mt-sm-0">
                <select class="form-select form-select-sm" style="width: 150px;" onchange="window.location.href=this.value">
                    <option value="{{ route('rewards.history', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}"
                            {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                    <option value="{{ route('rewards.history', array_merge(request()->except('sort'), ['sort' => 'oldest'])) }}"
                            {{ request('sort') == 'oldest' ? 'selected' : '' }}>เก่าสุด</option>
                    <option value="{{ route('rewards.history', array_merge(request()->except('sort'), ['sort' => 'points-high'])) }}"
                            {{ request('sort') == 'points-high' ? 'selected' : '' }}>คะแนนมากไปน้อย</option>
                    <option value="{{ route('rewards.history', array_merge(request()->except('sort'), ['sort' => 'points-low'])) }}"
                            {{ request('sort') == 'points-low' ? 'selected' : '' }}>คะแนนน้อยไปมาก</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            @if($redeems->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-history fa-5x text-muted"></i>
                    </div>
                    <h5>ยังไม่มีประวัติการแลกรางวัล</h5>
                    <p class="text-muted mb-4">คุณยังไม่เคยแลกรางวัลใดๆ</p>
                    <a href="{{ route('rewards.index') }}" class="btn btn-primary">
                        <i class="fas fa-gift me-1"></i> ไปยังหน้ารางวัล
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col" class="d-none d-md-table-cell">#</th>
                                <th scope="col">รางวัล</th>
                                <th scope="col">คะแนน</th>
                                <th scope="col" class="d-none d-md-table-cell">วันที่แลก</th>
                                <th scope="col">สถานะ</th>
                                <th scope="col" class="d-none d-md-table-cell">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($redeems as $index => $redeem)
                                <tr>
                                    <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="reward-img-small me-2">
                                                @if($redeem->reward->image_path)
                                                    <img src="{{ asset('storage/' . $redeem->reward->image_path) }}" alt="{{ $redeem->reward->name }}"
                                                        class="reward-history-img">
                                                @else
                                                    <i class="fas fa-gift fa-2x text-primary"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $redeem->reward->name }}</div>
                                                <div class="small text-muted d-md-block d-none">{{ Str::limit($redeem->reward->description, 50) }}</div>
                                                <div class="small text-muted d-md-none">{{ $redeem->created_at->format('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-warning text-dark"><i class="fas fa-coins me-1"></i> {{ $redeem->points_used ?? $redeem->reward->points_required }}</span></td>
                                    <td class="d-none d-md-table-cell">{{ $redeem->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($redeem->status == 'pending')
                                            <span class="badge bg-warning">รอดำเนินการ</span>
                                        @elseif($redeem->status == 'completed')
                                            <span class="badge bg-success">เสร็จสิ้น</span>
                                        @elseif($redeem->status == 'cancelled')
                                            <span class="badge bg-danger">ยกเลิก</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $redeem->note ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-primary">
            <h5 class="mb-0">ข้อมูลการแลกรางวัล</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-info-circle me-2"></i> ขั้นตอนการแลกรางวัล</h6>
                    <ol class="ps-3 mt-2">
                        <li>เลือกรางวัลที่ต้องการจากหน้ารางวัล</li>
                        <li>ตรวจสอบคะแนนและกดปุ่ม "แลกรางวัล"</li>
                        <li>ยืนยันการแลกรางวัล</li>
                        <li>รอทีมงานดำเนินการติดต่อกลับ</li>
                        <li>รับรางวัลของคุณ!</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-question-circle me-2"></i> คำถามที่พบบ่อย</h6>
                    <div class="accordion mt-2" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                                    ฉันจะได้รับรางวัลเมื่อไร?
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    ทีมงานจะดำเนินการจัดส่งรางวัลภายใน 7-14 วันทำการหลังจากการแลกรางวัล
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    ฉันสามารถยกเลิกการแลกรางวัลได้หรือไม่?
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    คุณสามารถยกเลิกการแลกรางวัลได้เฉพาะรายการที่มีสถานะ "รอดำเนินการ" เท่านั้น โดยติดต่อทีมงานผ่านช่องทางติดต่อ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Filter Badges */
    .filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-badge:hover {
        background-color: #2DC679 !important;
        color: white;
    }

    /* Stats Cards */
    .reward-stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .reward-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }

    .reward-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 10px;
        font-size: 20px;
    }

    /* Table styles */
    .table > :not(caption) > * > * {
        padding: 0.75rem;
    }

    /* Image styles */
    .reward-history-img {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 6px;
        background-color: #f8f9fa;
        padding: 3px;
    }

    /* Accordion styles */
    .accordion-button:not(.collapsed) {
        background-color: rgba(45, 198, 121, 0.1);
        color: #2DC679;
    }

    .accordion-button:focus {
        border-color: #2DC679;
        box-shadow: 0 0 0 0.25rem rgba(45, 198, 121, 0.25);
    }

    /* Card styles */
    .card {
        border-radius: 12px !important;
        overflow: hidden;
        border: none !important;
    }

    .card-header {
        background-color: white !important;
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
    }

    /* Mobile & Tablet Responsive Adjustments */
    @media (max-width: 991.98px) {
        .container {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .d-flex.justify-content-between.align-items-center {
            flex-wrap: wrap;
            gap: 10px;
        }

        h2.mb-0 {
            font-size: 1.6rem;
            margin-bottom: 0.5rem !important;
        }

        .mobile-history-btn {
            width: auto;
            margin-left: auto;
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        /* Table adjustments */
        .table th, .table td {
            white-space: nowrap;
        }
    }

    /* Specific mobile adjustments */
    @media (max-width: 575.98px) {
        .container {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        h2.mb-0 {
            font-size: 1.5rem;
        }

        /* ปรับปุ่มกลับในโหมดมือถือ */
        .mobile-history-btn {
            font-size: 0.9rem;
            padding: 0.4rem 1rem;
            border-radius: 30px;
        }

        /* ปรับการแสดงผลการ์ดสถิติบนมือถือ */
        .row.mb-4 {
            margin-left: -8px;
            margin-right: -8px;
        }

        .col-6.col-md-3.mb-3 {
            padding-left: 8px;
            padding-right: 8px;
            margin-bottom: 16px;
        }

        .reward-stat-card {
            border-radius: 12px;
        }

        .reward-stat-icon {
            width: 45px !important;
            height: 45px !important;
            font-size: 20px;
            margin-right: 10px !important;
        }

        .reward-stat-card .card-body {
            padding: 15px !important;
        }

        .reward-stat-card h6 {
            font-size: 0.85rem !important;
            margin-bottom: 5px !important;
        }

        .reward-stat-card h4 {
            font-size: 1.4rem !important;
            font-weight: 600 !important;
        }

        /* ปรับหน้าตารางให้เหมาะกับมือถือ */
        .table th, .table td {
            padding: 0.5rem !important;
            font-size: 0.9rem;
        }

        .reward-history-img {
            width: 35px !important;
            height: 35px !important;
        }

        .filter-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.7rem !important;
        }

        /* Card adjustments */
        .card-body {
            padding: 1rem !important;
        }

        .card-header {
            padding: 0.75rem 1rem !important;
        }

        /* Form adjustments */
        .form-select-sm {
            font-size: 0.875rem;
            padding: 0.25rem 1.5rem 0.25rem 0.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle filter badge clicks
        document.querySelectorAll('.filter-badge').forEach(badge => {
            badge.addEventListener('click', function(e) {
                document.querySelectorAll('.filter-badge').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    });
</script>
@endsection
