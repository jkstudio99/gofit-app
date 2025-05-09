@extends('layouts.app')

@section('title', 'บทความที่บันทึก - GoFit')

@section('styles')
<style>
    .saved-articles-header {
        background-color: #f8f9fa;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }

    .article-card {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        height: 100%;
    }

    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .article-image {
        height: 180px;
        object-fit: cover;
    }

    .article-content {
        padding: 1.25rem;
    }

    .article-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
    }

    .article-meta {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
    }

    .article-meta i {
        margin-right: 0.25rem;
    }

    .article-meta-item {
        display: inline-flex;
        align-items: center;
        margin-right: 1rem;
    }

    .article-excerpt {
        font-size: 0.9rem;
        color: #495057;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.6;
    }

    .article-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .category-badge {
        background-color: #e9ecef;
        color: #495057;
        font-size: 0.8rem;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        margin-right: 0.5rem;
        display: inline-block;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }

    .empty-state-text {
        font-size: 1.25rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
    }

    .empty-state-subtext {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    .remove-btn {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
        display: flex;
        align-items: center;
        transition: all 0.2s;
    }

    .remove-btn:hover {
        background-color: #fff5f5;
    }

    .remove-btn i {
        margin-right: 0.35rem;
    }

    .read-btn {
        color: #2DC679;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .read-btn:hover {
        color: #249d60;
    }

    .read-btn i {
        margin-left: 0.35rem;
    }

    .date-saved {
        font-size: 0.8rem;
        color: #6c757d;
        font-style: italic;
        margin-top: 0.5rem;
    }

    @media (max-width: 767.98px) {
        .saved-articles-header {
            padding: 2rem 0;
        }

        .article-image {
            height: 160px;
        }
    }
</style>
@endsection

@section('content')
<!-- Header -->
<div class="saved-articles-header">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="mb-3">บทความที่บันทึกไว้</h1>
                <p class="lead text-muted">บทความด้านสุขภาพที่คุณสนใจและได้บันทึกไว้อ่านภายหลัง</p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Articles List -->
    @if(count($savedArticles) > 0)
    <div class="row">
        @foreach($savedArticles as $saved)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="article-card">
                <img src="{{ $saved->article->thumbnail ? asset('storage/' . $saved->article->thumbnail) : asset('images/article-placeholders/placeholder-' . (($loop->index % 5) + 1) . '.jpg') }}"
                     class="article-image w-100"
                     alt="{{ $saved->article->title }}">
                <div class="article-content">
                    <span class="category-badge mb-2">{{ $saved->article->category->category_name }}</span>
                    <h5 class="article-title">{{ $saved->article->title }}</h5>
                    <div class="article-meta">
                        <span class="article-meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            {{ \Carbon\Carbon::parse($saved->article->published_at)->locale('th')->thaiFormat('j M y') }}
                        </span>
                        <span class="article-meta-item">
                            <i class="fas fa-eye"></i>
                            {{ number_format($saved->article->view_count) }}
                        </span>
                    </div>
                    <p class="article-excerpt">
                        {{ \Illuminate\Support\Str::limit(strip_tags($saved->article->content), 150) }}
                    </p>
                    <div class="article-actions">
                        <a href="{{ route('health-articles.show', $saved->article->article_id) }}" class="read-btn">
                            อ่านบทความ <i class="fas fa-arrow-right"></i>
                        </a>
                        <form action="{{ route('health-articles.unsave', $saved->article->article_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="remove-btn" onclick="return confirm('คุณต้องการลบบทความนี้ออกจากรายการที่บันทึกหรือไม่?')">
                                <i class="fas fa-trash-alt"></i> ลบ
                            </button>
                        </form>
                    </div>
                    <div class="date-saved">
                        บันทึกเมื่อ {{ \Carbon\Carbon::parse($saved->created_at)->locale('th')->thaiFormat('j M y H:i') }} น.
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $savedArticles->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="far fa-bookmark"></i>
        </div>
        <div class="empty-state-text">ยังไม่มีบทความที่บันทึกไว้</div>
        <div class="empty-state-subtext">คุณยังไม่ได้บันทึกบทความใด ๆ เอาไว้ ไปสำรวจบทความดี ๆ กันเถอะ</div>
        <a href="{{ route('health-articles.index') }}" class="btn btn-primary px-4">
            ดูบทความทั้งหมด
        </a>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any client-side functionality here if needed
    });
</script>
@endsection
