@extends('layouts.admin')

@section('title', 'จัดการบทความสุขภาพ - GoFit')

@php
function getThaiMonth($month)
{
    $thaiMonths = [
        1 => 'ม.ค.',
        2 => 'ก.พ.',
        3 => 'มี.ค.',
        4 => 'เม.ย.',
        5 => 'พ.ค.',
        6 => 'มิ.ย.',
        7 => 'ก.ค.',
        8 => 'ส.ค.',
        9 => 'ก.ย.',
        10 => 'ต.ค.',
        11 => 'พ.ย.',
        12 => 'ธ.ค.'
    ];
    return $thaiMonths[$month];
}

function formatThaiDate($date)
{
    $day = date('j', strtotime($date));
    $month = getThaiMonth(date('n', strtotime($date)));
    $year = (date('Y', strtotime($date)) + 543);
    $shortYear = substr($year, -2);
    return "$day $month $shortYear";
}
@endphp

@section('styles')
<style>
    .article-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .article-card {
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        border: none;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .article-thumbnail {
        height: 180px;
        background-color: #f8f9fa;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .article-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .article-card:hover .article-thumbnail img {
        transform: scale(1.05);
    }
    .article-status {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        z-index: 10;
    }
    .article-category {
        position: absolute;
        top: 0.75rem;
        left: 0.75rem;
        z-index: 10;
    }
    .article-meta {
        font-size: 0.85rem;
        color: #6c757d;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    .article-meta i {
        width: 16px;
        text-align: center;
        margin-right: 0.25rem;
    }
    .article-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.75rem;
    }
    .article-excerpt {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 4.5rem;
    }
    .article-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: auto;
        gap: 10px;
    }
    .article-action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0;
        transition: all 0.2s ease;
        padding: 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .article-action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .article-action-btn i {
        color: white;
        font-size: 15px;
    }
    .search-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    @media (max-width: 767.98px) {
        .search-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
    .badge.bg-draft {
        background-color: #6c757d;
    }
    .badge.bg-published {
        background-color: #2DC679;
    }
    /* Add badge-like styling for article images */
    .article-img-container {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 180px;
        padding: 1rem;
        overflow: hidden;
        background-color: #f8f9fa;
    }
    .article-img-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Stats Cards */
    .article-stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: none;
    }
    .article-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
    .article-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        font-size: 20px;
    }
    .article-stat-icon i {
        color: white;
    }

    /* Filter panel styling */
    .filter-panel {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    /* Search box */
    .search-box {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding-left: 20px;
    }
    .search-box:focus {
        box-shadow: 0 0 0 0.2rem rgba(45, 198, 121, 0.25);
        border-color: #2DC679;
    }

    /* Use primary color from design system */
    .btn-primary, .bg-primary {
        background-color: #2DC679 !important;
        border-color: #2DC679 !important;
    }
    .btn-primary:hover {
        background-color: #24A664 !important;
        border-color: #24A664 !important;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="mb-0">จัดการบทความสุขภาพ</h2>
        <a href="{{ route('admin.health-articles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>เพิ่มบทความใหม่
        </a>
    </div>
    <p class="text-muted">จัดการบทความสุขภาพสำหรับผู้ใช้งานในระบบ</p>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 article-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="article-stat-icon bg-primary me-3">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">บทความทั้งหมด</h6>
                        <h4 class="mb-0">{{ $articles->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 article-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="article-stat-icon bg-success me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">เผยแพร่แล้ว</h6>
                        <h4 class="mb-0">{{ $publishedCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 article-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="article-stat-icon bg-warning me-3">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">บทความเข้าชมมากที่สุด</h6>
                        <h4 class="mb-0">{{ $topViewedCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-0 article-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="article-stat-icon bg-info me-3">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">บทความถูกใจมากที่สุด</h6>
                        <h4 class="mb-0">{{ $topLikedCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.health-articles.index') }}" method="GET" class="row g-3">
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

                <div class="col-12 collapse {{ request()->hasAny(['category', 'status', 'sort']) ? 'show' : '' }}" id="advancedFilters">
                    <div class="filter-panel mt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">หมวดหมู่</label>
                                <select name="category" class="form-select">
                                    <option value="">ทุกหมวดหมู่</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">สถานะ</label>
                                <select name="status" class="form-select">
                                    <option value="">ทุกสถานะ</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>เผยแพร่แล้ว</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>ฉบับร่าง</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">เรียงตาม</label>
                                <select name="sort" class="form-select">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เก่าสุด</option>
                                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>ชื่อ A-Z</option>
                                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>ชื่อ Z-A</option>
                                    <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>ยอดเข้าชม</option>
                                    <option value="likes" {{ request('sort') == 'likes' ? 'selected' : '' }}>ยอดถูกใจ</option>
                                </select>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('admin.health-articles.index') }}" class="btn btn-outline-secondary me-2">
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

    <!-- Badges Grid -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">
                    <i class="fas fa-newspaper me-2 text-primary"></i>รายการบทความสุขภาพ
                    @if(request()->hasAny(['search', 'category', 'status', 'sort']))
                    <span class="badge bg-success ms-2">
                        <i class="fas fa-filter me-1"></i> กำลังแสดงผลลัพธ์: {{ $articles->total() }} รายการ
                    </span>
                    @endif
                </h5>
                <span class="badge bg-info text-white rounded-pill px-3 py-2">
                    <i class="fas fa-newspaper me-1"></i> บทความทั้งหมด: {{ $articles->total() }}
                </span>
            </div>
        </div>
        <div class="card-body pt-4">
            @if($articles->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h5>ไม่พบข้อมูลบทความ</h5>
                    <p class="text-muted">ยังไม่มีบทความในระบบ หรือไม่ตรงตามเงื่อนไขการค้นหา</p>
                    <a href="{{ route('admin.health-articles.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1 "></i> เพิ่มบทความใหม่
                    </a>
                </div>
            @else
                <div class="article-grid">
                    @foreach($articles as $article)
                        <div class="card article-card">
                            <div class="article-thumbnail">
                                <div class="article-status">
                                    @if($article->status == 'published' || $article->is_published)
                                        <span class="badge bg-published px-2 py-1">เผยแพร่</span>
                                    @else
                                        <span class="badge bg-draft px-2 py-1">ฉบับร่าง</span>
                                    @endif
                                </div>
                                <div class="article-category">
                                    <span class="badge bg-primary px-2 py-1">{{ $article->category->category_name }}</span>
                                </div>
                                @if($article->thumbnail)
                                    <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="img-fluid">
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-newspaper fa-3x"></i>
                                        <p class="small mt-2">ไม่มีรูปภาพ</p>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="article-title">{{ $article->title }}</h5>
                                <p class="article-excerpt">{{ $article->excerpt }}</p>
                                <div class="article-meta mb-3">
                                    <div><i class="fas fa-calendar-alt"></i> {{ formatThaiDate($article->created_at) }}</div>
                                    <div><i class="fas fa-eye"></i> {{ number_format($article->view_count) }} ครั้ง</div>
                                    <div><i class="fas fa-heart"></i> {{ number_format($article->likes()->count()) }} ถูกใจ</div>
                                    <div><i class="fas fa-comment"></i> {{ number_format($article->comments()->count()) }} ความคิดเห็น</div>
                                    <div><i class="fas fa-bookmark"></i> {{ number_format($article->savedBy()->count()) }} บันทึก</div>
                                </div>
                                <div class="article-actions">
                                    <a href="{{ route('admin.health-articles.show', $article->article_id) }}" class="btn btn-info article-action-btn" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.health-articles.edit', $article->article_id) }}" class="btn btn-warning article-action-btn" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger article-action-btn delete-article"
                                            data-article-id="{{ $article->article_id }}"
                                            data-article-title="{{ $article->title }}"
                                            title="ลบ">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $articles->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete article confirmation
        const deleteButtons = document.querySelectorAll('.delete-article');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const articleId = this.getAttribute('data-article-id');
                const articleTitle = this.getAttribute('data-article-title');

                Swal.fire({
                    title: `ลบบทความ "${articleTitle}"?`,
                    html: `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            คำเตือน: การลบบทความไม่สามารถเรียกคืนได้
                        </div>
                        <p>คุณต้องการลบบทความนี้หรือไม่?</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> ลบบทความ',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit form for delete
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ route('admin.health-articles.destroy', '') }}/${articleId}`;
                        form.style.display = 'none';

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection

