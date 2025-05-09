@extends('layouts.admin')

@section('title', $article->title . ' - GoFit')

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

function formatThaiDateTime($date)
{
    $day = date('j', strtotime($date));
    $month = getThaiMonth(date('n', strtotime($date)));
    $year = (date('Y', strtotime($date)) + 543);
    $shortYear = substr($year, -2);
    $time = date('H:i', strtotime($date));
    return "$day $month $shortYear $time น.";
}
@endphp

@section('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<style>
    .article-header {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .article-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .article-meta-item {
        display: flex;
        align-items: center;
    }
    .article-meta-item i {
        margin-right: 0.5rem;
        width: 16px;
        text-align: center;
    }
    .article-featured-image {
        width: 100%;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .article-image-placeholder {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 3rem;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 1.5rem;
        color: #adb5bd;
        margin-bottom: 1.5rem;
    }
    .article-content {
        font-size: 1.05rem;
        line-height: 1.7;
    }
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin: 1rem 0;
    }
    .article-content p {
        margin-bottom: 1.25rem;
    }
    .article-content h2, .article-content h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    .article-content ul, .article-content ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }
    .article-content blockquote {
        background-color: #f8f9fa;
        border-left: 4px solid #2DC679;
        padding: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
    }
    .article-sidebar-card {
        margin-bottom: 1.5rem;
    }
    .article-sidebar-card .card-header {
        font-weight: 600;
    }
    .article-info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    .article-info-item:last-child {
        border-bottom: none;
    }
    .article-info-label {
        color: #6c757d;
        font-weight: 500;
    }
    .article-stats-item {
        text-align: center;
        padding: 1rem;
    }
    .article-stats-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2DC679;
        margin-bottom: 0.25rem;
    }
    .article-stats-label {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .badge.bg-published {
        background-color: #198754;
    }
    .badge.bg-draft {
        background-color: #6c757d;
    }

    /* Action button styling */
    .article-action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        padding: 0;
    }

    .article-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .article-action-btn i {
        color: white;
        font-size: 15px;
    }

    /* ปุ่มลบบทความให้เป็นสีขาวเสมอ */
    .btn-danger, .btn-danger:hover, .btn-danger:focus, .btn-danger:active {
        color: white !important;
    }

    .btn-danger i, .btn-danger:hover i {
        color: white !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">รายละเอียดบทความ</h1>
        <div>
            <a href="{{ route('admin.health-articles.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการบทความ
            </a>
            <a href="{{ route('admin.health-articles.edit', $article->article_id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i> แก้ไข
            </a>
            <button type="button" class="btn btn-danger delete-article"
                    data-article-id="{{ $article->article_id }}"
                    data-article-title="{{ $article->title }}">
                <i class="fas fa-trash-alt me-1"></i> ลบบทความ
            </button>
        </div>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.health-articles.index') }}">บทความสุขภาพ</a></li>
        <li class="breadcrumb-item active">{{ $article->title }}</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="article-header">
                <h1 class="article-title">{{ $article->title }}</h1>
                <div class="article-meta">
                    <div class="article-meta-item">
                        <i class="fas fa-folder"></i> {{ $article->category->category_name }}
                    </div>
                    <div class="article-meta-item">
                        <i class="fas fa-user"></i> {{ $article->author->name ?? $article->author->username }}
                    </div>
                    <div class="article-meta-item">
                        <i class="fas fa-calendar-alt"></i> {{ formatThaiDate($article->created_at) }}
                    </div>
                    <div class="article-meta-item">
                        @if($article->status == 'published')
                            <span class="badge bg-published px-2 py-1">
                                <i class="fas fa-check-circle me-1"></i> เผยแพร่แล้ว
                            </span>
                        @else
                            <span class="badge bg-draft px-2 py-1">
                                <i class="fas fa-pencil-alt me-1"></i> ฉบับร่าง
                            </span>
                        @endif
                    </div>
                </div>
                <div>{{ $article->excerpt }}</div>
            </div>

            @if($article->thumbnail)
                <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="article-featured-image">
            @else
                <div class="article-image-placeholder">
                    <div class="text-center">
                        <i class="fas fa-image fa-4x mb-2"></i>
                        <p class="mb-0">ไม่มีรูปภาพปกบทความ</p>
                    </div>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-alt me-1"></i> เนื้อหาบทความ
                </div>
                <div class="card-body">
                    <div class="article-content">
                        {!! $article->content !!}
                    </div>
                </div>
            </div>

            @php
                $showTags = false;
                $articleTags = [];
                try {
                    if($article->tags && $article->tags->count() > 0) {
                        $showTags = true;
                        $articleTags = $article->tags;
                    }
                } catch (\Exception $e) {
                    // Silently fail if tags relationship encounters an error
                }
            @endphp

            @if($showTags)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tags me-1"></i> แท็ก
                </div>
                <div class="card-body">
                    @foreach($articleTags as $tag)
                        <a href="{{ route('admin.health-articles.index', ['tag' => $tag->tag_id]) }}" class="btn btn-sm btn-outline-secondary me-2 mb-2">
                            {{ $tag->tag_name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-comments me-1"></i> ความคิดเห็นล่าสุด
                </div>
                <div class="card-body">
                    @if($article->comments->count() > 0)
                        <div class="list-group">
                            @foreach($article->comments->take(5) as $comment)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('profile_images/' . ($comment->user->profile_image ?? 'default-profile.png')) }}"
                                                 class="rounded-circle me-2" width="30" height="30"
                                                 alt="{{ $comment->user->firstname ?? 'ผู้ใช้งาน' }}">
                                            <h6 class="mb-0">{{ $comment->user->firstname ?? 'ผู้ใช้งาน' }}</h6>
                                        </div>
                                        <small class="text-muted">{{ formatThaiDateTime($comment->created_at) }}</small>
                                    </div>
                                    <p class="mb-1 mt-2">{{ $comment->comment_text ?? $comment->content }}</p>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn"
                                                data-comment-id="{{ $comment->comment_id }}"
                                                data-bs-toggle="tooltip"
                                                title="ลบความคิดเห็นนี้">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($article->comments->count() > 5)
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-outline-primary btn-sm view-all-comments">
                                    ดูความคิดเห็นทั้งหมด ({{ $article->comments->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p>ยังไม่มีความคิดเห็นสำหรับบทความนี้</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card article-sidebar-card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i> ข้อมูลบทความ
                </div>
                <div class="card-body">
                    <div class="article-info-item">
                        <div class="article-info-label">URL (Slug)</div>
                        <div>
                            <a href="{{ url('/articles/' . $article->slug) }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                {{ $article->slug }}
                                <i class="fas fa-external-link-alt ms-1 small"></i>
                            </a>
                        </div>
                    </div>
                    <div class="article-info-item">
                        <div class="article-info-label">หมวดหมู่</div>
                        <div>{{ $article->category->category_name }}</div>
                    </div>
                    <div class="article-info-item">
                        <div class="article-info-label">สถานะ</div>
                        <div>
                            @if($article->status == 'published')
                                <span class="badge bg-published">เผยแพร่แล้ว</span>
                            @else
                                <span class="badge bg-draft">ฉบับร่าง</span>
                            @endif
                        </div>
                    </div>
                    <div class="article-info-item">
                        <div class="article-info-label">ผู้เขียน</div>
                        <div>{{ $article->author->name ?? $article->author->username }}</div>
                    </div>
                    <div class="article-info-item">
                        <div class="article-info-label">วันที่สร้าง</div>
                        <div>{{ formatThaiDateTime($article->created_at) }}</div>
                    </div>
                    <div class="article-info-item">
                        <div class="article-info-label">อัปเดตล่าสุด</div>
                        <div>{{ formatThaiDateTime($article->updated_at) }}</div>
                    </div>
                    @if($article->status == 'published' && $article->published_at)
                    <div class="article-info-item">
                        <div class="article-info-label">วันที่เผยแพร่</div>
                        <div>{{ formatThaiDateTime($article->published_at) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card article-sidebar-card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i> สถิติบทความ
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-4 article-stats-item border-end">
                            <div class="article-stats-value">{{ number_format($article->view_count) }}</div>
                            <div class="article-stats-label">เข้าชม</div>
                        </div>
                        <div class="col-4 article-stats-item border-end">
                            <div class="article-stats-value">{{ number_format($article->likes()->count()) }}</div>
                            <div class="article-stats-label">ถูกใจ</div>
                        </div>
                        <div class="col-4 article-stats-item">
                            <div class="article-stats-value">{{ number_format($article->comments()->count()) }}</div>
                            <div class="article-stats-label">ความคิดเห็น</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card article-sidebar-card">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i> การมีส่วนร่วม
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-6 article-stats-item border-end">
                            <div class="article-stats-value">{{ number_format($article->likes()->count()) }}</div>
                            <div class="article-stats-label">
                                <i class="fas fa-heart text-danger me-1"></i> ถูกใจ
                            </div>
                        </div>
                        <div class="col-6 article-stats-item">
                            <div class="article-stats-value">{{ number_format($article->savedBy()->count()) }}</div>
                            <div class="article-stats-label">
                                <i class="fas fa-bookmark text-primary me-1"></i> บันทึก
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($article->meta_title) || !empty($article->meta_description))
            <div class="card article-sidebar-card">
                <div class="card-header">
                    <i class="fas fa-search me-1"></i> ข้อมูล SEO
                </div>
                <div class="card-body">
                    @if(!empty($article->meta_title))
                    <div class="article-info-item">
                        <div class="article-info-label">Meta Title</div>
                        <div>{{ $article->meta_title }}</div>
                    </div>
                    @endif
                    @if(!empty($article->meta_description))
                    <div class="article-info-item">
                        <div class="article-info-label mb-2">Meta Description</div>
                        <div class="small" style="line-height: 1.6; word-wrap: break-word; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                            {{ $article->meta_description }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('admin.health-articles.edit', $article->article_id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> แก้ไขบทความ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete article button
        const deleteArticleBtn = document.querySelector('.delete-article');
        if (deleteArticleBtn) {
            deleteArticleBtn.addEventListener('click', function() {
                const articleId = this.dataset.articleId;
                const articleTitle = this.dataset.articleTitle;

                Swal.fire({
                    title: 'คุณต้องการลบบทความนี้ใช่หรือไม่?',
                    text: `"${articleTitle}" จะถูกลบออกจากระบบและไม่สามารถกู้คืนได้`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ลบบทความ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create form to submit deletion
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/health-articles/${articleId}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';
                        form.appendChild(method);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }

        // Delete comment buttons
        const deleteCommentBtns = document.querySelectorAll('.delete-comment-btn');
        deleteCommentBtns.forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;

                Swal.fire({
                    title: 'ลบความคิดเห็นนี้?',
                    html: `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            คำเตือน: การลบความคิดเห็นไม่สามารถเรียกคืนได้
                        </div>
                        <p>คุณต้องการลบความคิดเห็นนี้หรือไม่?</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> ลบความคิดเห็น',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create form to submit deletion
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/article-comments/${commentId}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';
                        form.appendChild(method);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // View all comments button
        const viewAllCommentsBtn = document.querySelector('.view-all-comments');
        if (viewAllCommentsBtn) {
            viewAllCommentsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // You can implement a modal or redirect to a comments management page
                Swal.fire({
                    title: 'ความคิดเห็นทั้งหมด',
                    text: 'หน้ารายละเอียดความคิดเห็นกำลังถูกพัฒนา',
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'ตกลง'
                });
            });
        }
    });
</script>
@endsection
