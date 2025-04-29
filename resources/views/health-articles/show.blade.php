@extends('layouts.app')

@section('title', $article->title . ' - GoFit')

@section('styles')
<style>
    .article-header {
        position: relative;
        background-color: #f8f9fa;
        padding-top: 2rem;
        padding-bottom: 6rem;
        margin-bottom: -4rem;
    }

    .article-header-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .article-meta {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .article-meta-item {
        margin-right: 1.2rem;
        display: inline-flex;
        align-items: center;
    }

    .article-meta-item i {
        margin-right: 0.5rem;
        opacity: 0.8;
    }

    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #343a40;
    }

    .article-content p {
        margin-bottom: 1.5rem;
    }

    .article-content h2 {
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .article-content h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
    }

    .article-content ul, .article-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.2rem;
    }

    .article-content li {
        margin-bottom: 0.5rem;
    }

    .article-content blockquote {
        margin: 1.5rem 0;
        padding: 1rem 1.5rem;
        border-left: 4px solid #2DC679;
        background-color: #f8f9fa;
        font-style: italic;
    }

    .article-actions {
        position: sticky;
        top: 5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 3.5rem;
    }

    .article-action-btn {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.8rem;
        border: none;
        background-color: #f8f9fa;
        color: #6c757d;
        transition: all 0.2s;
        cursor: pointer;
    }

    .article-action-btn:hover {
        background-color: #e9ecef;
        color: #343a40;
    }

    .article-action-btn.active {
        background-color: #2DC679;
        color: white;
    }

    .article-action-btn i {
        font-size: 1.25rem;
    }

    .article-action-counter {
        font-size: 0.8rem;
        margin-top: 0.25rem;
        color: #6c757d;
        text-align: center;
    }

    .toc {
        position: sticky;
        top: 5rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        font-size: 0.9rem;
    }

    .toc-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #343a40;
    }

    .toc-list {
        list-style-type: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .toc-item {
        margin-bottom: 0.75rem;
        padding-left: 1rem;
        border-left: 2px solid #dee2e6;
    }

    .toc-item a {
        color: #495057;
        text-decoration: none;
        transition: all 0.2s;
        display: block;
        line-height: 1.4;
    }

    .toc-item a:hover {
        color: #2DC679;
    }

    .toc-item.active {
        border-left-color: #2DC679;
    }

    .toc-item.active a {
        color: #2DC679;
        font-weight: 500;
    }

    .toc-secondary {
        padding-left: 1rem;
        margin-top: 0.5rem;
    }

    .tags-container {
        margin: 2rem 0;
    }

    .tag-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        background-color: #f1f3f5;
        color: #495057;
        border-radius: 2rem;
        font-size: 0.85rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .tag-badge:hover {
        background-color: #e9ecef;
        color: #212529;
    }

    .author-card {
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin: 2rem 0;
    }

    .author-image {
        width: 4rem;
        height: 4rem;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1.25rem;
    }

    .author-info {
        flex: 1;
    }

    .author-name {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .author-bio {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .comments-section {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #dee2e6;
    }

    .comment-form {
        margin-bottom: 2rem;
    }

    .comment-list {
        margin-top: 1.5rem;
    }

    .comment-item {
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        background-color: #f8f9fa;
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .comment-author {
        display: flex;
        align-items: center;
    }

    .comment-author-image {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0.75rem;
    }

    .comment-author-name {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .comment-date {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .comment-meta {
        font-size: 0.85rem;
        color: #adb5bd;
    }

    .comment-content {
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .comment-actions {
        margin-top: 0.75rem;
        display: flex;
        align-items: center;
    }

    .comment-action-btn {
        color: #6c757d;
        background: none;
        border: none;
        padding: 0;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        margin-right: 1rem;
    }

    .comment-action-btn i {
        margin-right: 0.25rem;
    }

    .comment-action-btn:hover {
        color: #2DC679;
    }

    .related-articles {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #dee2e6;
    }

    .related-article-card {
        display: flex;
        margin-bottom: 1.5rem;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .related-article-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .related-article-image {
        width: 8rem;
        height: 100%;
        object-fit: cover;
    }

    .related-article-content {
        padding: 1rem;
        flex: 1;
    }

    .related-article-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .related-article-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .mobile-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: white;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        padding: 0.75rem;
        display: none;
        z-index: 100;
        justify-content: space-around;
    }

    .mobile-action-btn {
        background: none;
        border: none;
        color: #6c757d;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 0.75rem;
    }

    .mobile-action-btn i {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    .mobile-action-btn.active {
        color: #2DC679;
    }

    .share-dropdown {
        position: absolute;
        bottom: calc(100% + 0.5rem);
        left: 50%;
        transform: translateX(-50%);
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        padding: 0.75rem;
        width: 12rem;
        display: none;
        z-index: 101;
    }

    .share-dropdown.active {
        display: block;
    }

    .share-option {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 0.25rem;
    }

    .share-option:hover {
        background-color: #f8f9fa;
    }

    .share-option i {
        width: 1.5rem;
        margin-right: 0.75rem;
        text-align: center;
        font-size: 1.25rem;
    }

    .share-option-text {
        font-size: 0.9rem;
    }

    @media (max-width: 991.98px) {
        .article-actions-desktop {
            display: none;
        }

        .mobile-actions {
            display: flex;
        }

        .article-header-image {
            height: 300px;
        }
    }

    @media (max-width: 767.98px) {
        .article-header {
            padding-top: 1rem;
            padding-bottom: 5rem;
            margin-bottom: -3rem;
        }

        .article-header-image {
            height: 200px;
        }

        .article-content {
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Article Header -->
<header class="article-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <!-- Category -->
                <a href="{{ route('health-articles.index', ['category' => $article->category->category_id]) }}" class="badge bg-primary mb-3 px-3 py-2 text-decoration-none">
                    {{ $article->category->category_name }}
                </a>

                <!-- Title -->
                <h1 class="mb-4">{{ $article->title }}</h1>

                <!-- Meta information -->
                <div class="article-meta">
                    <span class="article-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($article->published_at)->locale('th')->format('d M Y H:i') }}
                    </span>
                    <span class="article-meta-item">
                        <i class="fas fa-eye"></i>
                        {{ number_format($article->view_count) }} ครั้ง
                    </span>
                    <span class="article-meta-item">
                        <i class="fas fa-comment"></i>
                        {{ $article->comments->count() }} ความคิดเห็น
                    </span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Article Content -->
<div class="container mt-5">
    <div class="row">
        <!-- Article Actions (Desktop) -->
        <div class="col-1 d-none d-lg-block position-relative article-actions-desktop">
            <div class="article-actions">
                <!-- Like Button -->
                <button class="article-action-btn {{ $article->isLikedByUser() ? 'active' : '' }}" id="like-btn" data-article-id="{{ $article->article_id }}">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="article-action-counter" id="like-counter">
                    {{ $article->likes->count() }}
                </div>

                <!-- Save Button -->
                <button class="article-action-btn {{ $article->isSavedByUser() ? 'active' : '' }}" id="save-btn" data-article-id="{{ $article->article_id }}">
                    <i class="fas fa-bookmark"></i>
                </button>
                <div class="article-action-counter" id="save-counter">
                    {{ $article->saves->count() }}
                </div>

                <!-- Share Button -->
                <button class="article-action-btn" id="share-btn-desktop">
                    <i class="fas fa-share-alt"></i>
                </button>

                <!-- Comment Button (scrolls to comments) -->
                <a href="#comments" class="article-action-btn">
                    <i class="fas fa-comment"></i>
                </a>
                <div class="article-action-counter">
                    {{ $article->comments->count() }}
                </div>
            </div>
        </div>

        <!-- Main Article -->
        <div class="col-lg-8 col-md-9">
            <!-- Article Image -->
            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : asset('images/article-placeholders/placeholder-1.jpg') }}"
                 class="article-header-image mb-4"
                 alt="{{ $article->title }}">

            <!-- Author Info (Mobile) -->
            <div class="d-md-none mb-4">
                <div class="d-flex align-items-center">
                    <img src="{{ $article->author->profile_image ? asset('storage/' . $article->author->profile_image) : asset('images/default-profile.png') }}"
                         class="rounded-circle me-2"
                         alt="{{ $article->author->name }}"
                         width="40" height="40">
                    <div>
                        <div class="fw-bold">{{ $article->author->name }}</div>
                        <div class="text-muted small">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ \Carbon\Carbon::parse($article->published_at)->locale('th')->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Article Content -->
            <div class="article-content">
                {!! $article->content !!}
            </div>

            <!-- Tags -->
            @if(count($article->tags) > 0)
            <div class="tags-container">
                @foreach($article->tags as $tag)
                <a href="{{ route('health-articles.index', ['tag' => $tag->tag_id]) }}" class="tag-badge">
                    #{{ $tag->tag_name }}
                </a>
                @endforeach
            </div>
            @endif

            <!-- Author Info (Desktop) -->
            <div class="author-card">
                <img src="{{ $article->author->profile_image ? asset('storage/' . $article->author->profile_image) : asset('images/default-profile.png') }}"
                     class="author-image"
                     alt="{{ $article->author->name }}">
                <div class="author-info">
                    <h5 class="author-name">{{ $article->author->name }}</h5>
                    <p class="author-bio">{{ $article->author->bio ?? 'ผู้เขียนบทความด้านสุขภาพและการออกกำลังกาย' }}</p>
                </div>
            </div>

            <!-- Comments Section -->
            <section class="comments-section" id="comments">
                <h3 class="mb-4">ความคิดเห็น ({{ $article->comments->count() }})</h3>

                <!-- Comment Form -->
                @auth
                <div class="comment-form">
                    <form action="{{ route('health-articles.comment.store', $article->article_id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control" id="comment" name="content" rows="3" placeholder="แสดงความคิดเห็นของคุณ..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">โพสต์ความคิดเห็น</button>
                    </form>
                </div>
                @else
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    กรุณา <a href="{{ route('login') }}" class="alert-link">เข้าสู่ระบบ</a> เพื่อแสดงความคิดเห็น
                </div>
                @endauth

                <!-- Comments List -->
                <div class="comment-list">
                    @forelse($article->comments->sortByDesc('created_at') as $comment)
                    <div class="comment-item">
                        <div class="comment-header">
                            <div class="comment-author">
                                <img src="{{ $comment->user->profile_image ? asset('storage/' . $comment->user->profile_image) : asset('images/default-profile.png') }}"
                                     class="comment-author-image"
                                     alt="{{ $comment->user->name }}">
                                <div>
                                    <div class="comment-author-name">{{ $comment->user->name }}</div>
                                    <div class="comment-date">{{ \Carbon\Carbon::parse($comment->created_at)->locale('th')->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="comment-meta">
                                @if(Auth::check() && (Auth::id() == $comment->user_id || Auth::user()->isAdmin()))
                                <form action="{{ route('health-articles.comment.destroy', $comment->comment_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger p-0 border-0" onclick="return confirm('คุณต้องการลบความคิดเห็นนี้ใช่หรือไม่?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        <p class="comment-content">{{ $comment->content }}</p>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <div class="text-muted mb-2"><i class="far fa-comment-dots fa-3x mb-3"></i></div>
                        <h5>ยังไม่มีความคิดเห็น</h5>
                        <p class="text-muted">เป็นคนแรกที่แสดงความคิดเห็นในบทความนี้</p>
                    </div>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3 col-md-3">
            <!-- Table of Contents -->
            <div class="toc d-none d-md-block" id="toc">
                <h5 class="toc-title">สารบัญ</h5>
                <ul class="toc-list" id="toc-content">
                    <!-- Generated dynamically by JS -->
                </ul>
            </div>

            <!-- Related Articles -->
            @if(count($relatedArticles) > 0)
            <div class="related-articles">
                <h5 class="mb-3">บทความที่เกี่ยวข้อง</h5>
                @foreach($relatedArticles as $relatedArticle)
                <a href="{{ route('health-articles.show', $relatedArticle->article_id) }}" class="text-decoration-none">
                    <div class="related-article-card mb-3">
                        <img src="{{ $relatedArticle->thumbnail ? asset('storage/' . $relatedArticle->thumbnail) : asset('images/article-placeholders/placeholder-' . (($loop->index % 5) + 1) . '.jpg') }}"
                             class="related-article-image"
                             alt="{{ $relatedArticle->title }}">
                        <div class="related-article-content">
                            <h6 class="related-article-title">{{ $relatedArticle->title }}</h6>
                            <div class="related-article-meta">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($relatedArticle->published_at)->locale('th')->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Mobile Actions Bar -->
<div class="mobile-actions">
    <!-- Like Button -->
    <button class="mobile-action-btn {{ $article->isLikedByUser() ? 'active' : '' }}" id="like-btn-mobile" data-article-id="{{ $article->article_id }}">
        <i class="fas fa-heart"></i>
        <span>ถูกใจ</span>
    </button>

    <!-- Comment Button -->
    <a href="#comments" class="mobile-action-btn">
        <i class="fas fa-comment"></i>
        <span>แสดงความคิดเห็น</span>
    </a>

    <!-- Save Button -->
    <button class="mobile-action-btn {{ $article->isSavedByUser() ? 'active' : '' }}" id="save-btn-mobile" data-article-id="{{ $article->article_id }}">
        <i class="fas fa-bookmark"></i>
        <span>บันทึก</span>
    </button>

    <!-- Share Button -->
    <button class="mobile-action-btn" id="share-btn-mobile">
        <i class="fas fa-share-alt"></i>
        <span>แชร์</span>

        <!-- Share Options Dropdown -->
        <div class="share-dropdown" id="share-dropdown-mobile">
            <div class="share-option" data-platform="facebook">
                <i class="fab fa-facebook-f text-primary"></i>
                <span class="share-option-text">Facebook</span>
            </div>
            <div class="share-option" data-platform="twitter">
                <i class="fab fa-twitter text-info"></i>
                <span class="share-option-text">Twitter</span>
            </div>
            <div class="share-option" data-platform="line">
                <i class="fab fa-line text-success"></i>
                <span class="share-option-text">Line</span>
            </div>
            <div class="share-option" data-platform="copy">
                <i class="fas fa-link text-secondary"></i>
                <span class="share-option-text">คัดลอกลิงก์</span>
            </div>
        </div>
    </button>
</div>

<!-- Desktop Share Dropdown -->
<div class="share-dropdown" id="share-dropdown-desktop" style="position: fixed;">
    <div class="share-option" data-platform="facebook">
        <i class="fab fa-facebook-f text-primary"></i>
        <span class="share-option-text">Facebook</span>
    </div>
    <div class="share-option" data-platform="twitter">
        <i class="fab fa-twitter text-info"></i>
        <span class="share-option-text">Twitter</span>
    </div>
    <div class="share-option" data-platform="line">
        <i class="fab fa-line text-success"></i>
        <span class="share-option-text">Line</span>
    </div>
    <div class="share-option" data-platform="copy">
        <i class="fas fa-link text-secondary"></i>
        <span class="share-option-text">คัดลอกลิงก์</span>
    </div>
</div>

<!-- Toast for Copy Link Success -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="copyLinkToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>คัดลอกลิงก์เรียบร้อยแล้ว
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate Table of Contents
        function generateTOC() {
            const articleContent = document.querySelector('.article-content');
            const tocContent = document.getElementById('toc-content');

            if (!articleContent || !tocContent) return;

            // Get all headings
            const headings = articleContent.querySelectorAll('h2, h3');

            if (headings.length === 0) {
                document.getElementById('toc').style.display = 'none';
                return;
            }

            headings.forEach((heading, index) => {
                // Add ID to the heading if it doesn't have one
                if (!heading.id) {
                    heading.id = `heading-${index}`;
                }

                const listItem = document.createElement('li');
                listItem.className = 'toc-item';

                if (heading.tagName === 'H3') {
                    listItem.classList.add('toc-secondary');
                }

                const link = document.createElement('a');
                link.href = `#${heading.id}`;
                link.textContent = heading.textContent;
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const targetElement = document.getElementById(heading.id);
                    const offset = 100; // Offset for fixed header

                    window.scrollTo({
                        top: targetElement.offsetTop - offset,
                        behavior: 'smooth'
                    });
                });

                listItem.appendChild(link);
                tocContent.appendChild(listItem);
            });

            // Highlight TOC item on scroll
            const tocItems = document.querySelectorAll('.toc-item a');

            window.addEventListener('scroll', function() {
                let current = '';
                const offset = 200;

                headings.forEach(heading => {
                    const sectionTop = heading.offsetTop - offset;

                    if (window.pageYOffset >= sectionTop) {
                        current = heading.id;
                    }
                });

                tocItems.forEach(item => {
                    item.parentElement.classList.remove('active');
                    if (item.getAttribute('href') === `#${current}`) {
                        item.parentElement.classList.add('active');
                    }
                });
            });
        }

        generateTOC();

        // Like article functionality
        const likeBtn = document.getElementById('like-btn');
        const likeBtnMobile = document.getElementById('like-btn-mobile');
        const likeCounter = document.getElementById('like-counter');

        function toggleLike(button) {
            const articleId = button.dataset.articleId;

            fetch(`/health-articles/${articleId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update active state for both buttons
                    likeBtn.classList.toggle('active');
                    likeBtnMobile.classList.toggle('active');

                    // Update counter
                    likeCounter.textContent = data.likes_count;
                }
            })
            .catch(error => console.error('Error:', error));
        }

        if (likeBtn) {
            likeBtn.addEventListener('click', () => toggleLike(likeBtn));
        }

        if (likeBtnMobile) {
            likeBtnMobile.addEventListener('click', () => toggleLike(likeBtnMobile));
        }

        // Save article functionality
        const saveBtn = document.getElementById('save-btn');
        const saveBtnMobile = document.getElementById('save-btn-mobile');
        const saveCounter = document.getElementById('save-counter');

        function toggleSave(button) {
            const articleId = button.dataset.articleId;

            fetch(`/health-articles/${articleId}/save`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update active state for both buttons
                    saveBtn.classList.toggle('active');
                    saveBtnMobile.classList.toggle('active');

                    // Update counter
                    saveCounter.textContent = data.saves_count;
                }
            })
            .catch(error => console.error('Error:', error));
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', () => toggleSave(saveBtn));
        }

        if (saveBtnMobile) {
            saveBtnMobile.addEventListener('click', () => toggleSave(saveBtnMobile));
        }

        // Share functionality
        const shareBtnDesktop = document.getElementById('share-btn-desktop');
        const shareBtnMobile = document.getElementById('share-btn-mobile');
        const shareDropdownDesktop = document.getElementById('share-dropdown-desktop');
        const shareDropdownMobile = document.getElementById('share-dropdown-mobile');
        const shareOptions = document.querySelectorAll('.share-option');

        function toggleShareDropdown(dropdown, button) {
            const isActive = dropdown.classList.contains('active');

            // Close any open dropdowns
            shareDropdownDesktop.classList.remove('active');
            shareDropdownMobile.classList.remove('active');

            if (!isActive) {
                dropdown.classList.add('active');

                // Position the desktop dropdown near the button
                if (dropdown === shareDropdownDesktop) {
                    const buttonRect = button.getBoundingClientRect();
                    dropdown.style.left = `${buttonRect.left - 70}px`;
                    dropdown.style.top = `${buttonRect.top - 10}px`;
                }
            }
        }

        if (shareBtnDesktop) {
            shareBtnDesktop.addEventListener('click', () => {
                toggleShareDropdown(shareDropdownDesktop, shareBtnDesktop);
            });
        }

        if (shareBtnMobile) {
            shareBtnMobile.addEventListener('click', () => {
                toggleShareDropdown(shareDropdownMobile, shareBtnMobile);
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#share-btn-desktop') &&
                !event.target.closest('#share-dropdown-desktop') &&
                !event.target.closest('#share-btn-mobile') &&
                !event.target.closest('#share-dropdown-mobile')) {
                shareDropdownDesktop.classList.remove('active');
                shareDropdownMobile.classList.remove('active');
            }
        });

        // Handle share options
        shareOptions.forEach(option => {
            option.addEventListener('click', function() {
                const platform = this.dataset.platform;
                const articleUrl = window.location.href;
                const articleTitle = '{{ $article->title }}';

                let shareUrl;

                switch(platform) {
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(articleUrl)}`;
                        window.open(shareUrl, '_blank');
                        break;
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(articleUrl)}&text=${encodeURIComponent(articleTitle)}`;
                        window.open(shareUrl, '_blank');
                        break;
                    case 'line':
                        shareUrl = `https://social-plugins.line.me/lineit/share?url=${encodeURIComponent(articleUrl)}`;
                        window.open(shareUrl, '_blank');
                        break;
                    case 'copy':
                        // Copy to clipboard
                        navigator.clipboard.writeText(articleUrl).then(() => {
                            // Show toast notification
                            const toast = new bootstrap.Toast(document.getElementById('copyLinkToast'));
                            toast.show();
                        });
                        break;
                }

                // Close dropdowns
                shareDropdownDesktop.classList.remove('active');
                shareDropdownMobile.classList.remove('active');
            });
        });
    });
</script>
@endsection
