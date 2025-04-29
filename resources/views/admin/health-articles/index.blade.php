@extends('layouts.admin')

@section('title', 'จัดการบทความสุขภาพ - GoFit')

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
    }
    .article-action-btn {
        padding: 0.25rem 0.5rem;
        margin-left: 0.25rem;
    }
    .dropdown-item i {
        width: 1rem;
        text-align: center;
        margin-right: 0.5rem;
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
        background-color: #198754;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">จัดการบทความสุขภาพ</h1>
        <a href="{{ route('admin.health-articles.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> เพิ่มบทความใหม่
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
        <li class="breadcrumb-item active">บทความสุขภาพ</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i>
            ค้นหาและกรองบทความ
        </div>
        <div class="card-body">
            <form action="{{ route('admin.health-articles.index') }}" method="GET">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">ค้นหา</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="ค้นหาตามชื่อบทความ...">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">หมวดหมู่</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">ทุกหมวดหมู่</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">สถานะ</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">ทุกสถานะ</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>เผยแพร่แล้ว</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>ฉบับร่าง</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort" class="form-label">เรียงตาม</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เก่าสุด</option>
                            <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>ชื่อ A-Z</option>
                            <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>ชื่อ Z-A</option>
                            <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>ยอดเข้าชม</option>
                            <option value="likes" {{ request('sort') == 'likes' ? 'selected' : '' }}>ยอดถูกใจ</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex search-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> ค้นหา
                    </button>
                    <a href="{{ route('admin.health-articles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> รีเซ็ต
                    </a>
                    <div class="ms-auto">
                        <a href="{{ route('admin.health-articles.statistics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar me-1"></i> สถิติบทความ
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-newspaper me-1"></i>
                รายการบทความสุขภาพ
            </div>
            <div class="small text-muted">
                แสดง {{ $articles->firstItem() ?? 0 }} ถึง {{ $articles->lastItem() ?? 0 }} จาก {{ $articles->total() ?? 0 }} รายการ
            </div>
        </div>
        <div class="card-body">
            @if($articles->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h5>ไม่พบข้อมูลบทความ</h5>
                    <p class="text-muted">ยังไม่มีบทความในระบบ หรือไม่ตรงตามเงื่อนไขการค้นหา</p>
                    <a href="{{ route('admin.health-articles.create') }}" class="btn btn-success mt-3">
                        <i class="fas fa-plus me-1"></i> เพิ่มบทความใหม่
                    </a>
                </div>
            @else
                <div class="article-grid">
                    @foreach($articles as $article)
                        <div class="card article-card">
                            <div class="article-thumbnail">
                                <div class="article-status">
                                    @if($article->status == 'published')
                                        <span class="badge bg-published px-2 py-1">เผยแพร่</span>
                                    @else
                                        <span class="badge bg-draft px-2 py-1">ฉบับร่าง</span>
                                    @endif
                                </div>
                                <div class="article-category">
                                    <span class="badge bg-primary px-2 py-1">{{ $article->category->category_name }}</span>
                                </div>
                                @if($article->thumbnail)
                                    <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="article-title">{{ $article->title }}</h5>
                                <p class="article-excerpt">{{ $article->excerpt }}</p>
                                <div class="article-meta mb-3">
                                    <div><i class="fas fa-calendar-alt"></i> {{ $article->created_at->format('d/m/Y') }}</div>
                                    <div><i class="fas fa-eye"></i> {{ number_format($article->view_count) }} ครั้ง</div>
                                    <div><i class="fas fa-heart"></i> {{ number_format($article->like_count) }} ถูกใจ</div>
                                    <div><i class="fas fa-comment"></i> {{ number_format($article->comments_count) }} ความคิดเห็น</div>
                                </div>
                                <div class="article-actions">
                                    <a href="{{ route('admin.health-articles.show', $article->article_id) }}" class="btn btn-sm btn-info article-action-btn" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.health-articles.edit', $article->article_id) }}" class="btn btn-sm btn-primary article-action-btn" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger article-action-btn delete-article"
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
                    title: 'ยืนยันการลบบทความ',
                    html: `คุณต้องการลบบทความ <strong>"${articleTitle}"</strong> หรือไม่?<br>
                           <span class="text-danger">การกระทำนี้ไม่สามารถเรียกคืนได้</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ลบบทความ',
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
    });
</script>
@endsection

