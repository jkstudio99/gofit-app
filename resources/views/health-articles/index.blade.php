@extends('layouts.app')

@section('title', 'บทความสุขภาพ - GoFit')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #2DC679 0%, #1aab6d 100%);
        padding: 4rem 0;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: url('{{ asset('images/health-patterns.png') }}');
        background-size: cover;
        opacity: 0.1;
    }

    .search-box {
        border-radius: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        border-color: transparent;
        padding-left: 20px;
        font-size: 0.95rem;
    }

    .search-box:focus {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border-color: #2DC679;
    }

    .search-btn {
        border-radius: 0 30px 30px 0;
        background-color: #2DC679;
        color: white;
        border: none;
        padding-right: 25px;
        padding-left: 25px;
    }

    .search-btn:hover {
        background-color: #25a866;
    }

    .article-card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .article-thumbnail {
        height: 180px;
        object-fit: cover;
    }

    .article-card .card-body {
        padding: 1.25rem;
    }

    .article-card .card-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.5rem;
    }

    .article-card .card-text {
        color: #6c757d;
        font-size: 0.9rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 1rem;
        height: 4rem;
    }

    .article-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .article-meta-item {
        margin-right: 1rem;
        display: inline-flex;
        align-items: center;
    }

    .article-meta-item i {
        margin-right: 0.35rem;
    }

    .category-badge {
        font-size: 0.75rem;
        font-weight: normal;
        padding: 0.35rem 0.75rem;
        border-radius: 30px;
        background-color: #f0f0f0;
        color: #555;
        transition: all 0.2s;
        text-decoration: none;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }

    .category-badge:hover {
        background-color: #e0e0e0;
        color: #333;
    }

    .category-badge.active {
        background-color: #2DC679;
        color: white;
    }

    .filter-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .tag-chip {
        font-size: 0.75rem;
        background-color: #f8f9fa;
        color: #495057;
        padding: 0.25rem 0.75rem;
        border-radius: 30px;
        text-decoration: none;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
        transition: all 0.2s;
    }

    .tag-chip:hover {
        background-color: #e9ecef;
        color: #212529;
    }

    .tag-chip.active {
        background-color: #2DC679;
        color: white;
    }

    .sort-btn {
        font-size: 0.85rem;
        color: #6c757d;
        background-color: transparent;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        border-radius: 30px;
        transition: all 0.2s;
        margin-right: 0.5rem;
    }

    .sort-btn:hover, .sort-btn.active {
        background-color: #2DC679;
        color: white;
        border-color: #2DC679;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 0;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state-text {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 767.98px) {
        .hero-section {
            padding: 3rem 0;
        }

        .article-thumbnail {
            height: 150px;
        }

        .filter-section {
            padding: 1rem !important;
            margin-bottom: 1.5rem !important;
        }

        .category-badges {
            white-space: nowrap;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            -webkit-overflow-scrolling: touch;
        }

        .category-badges::-webkit-scrollbar {
            height: 4px;
        }

        .category-badges::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold mb-4">บทความสุขภาพ</h1>
                <p class="lead mb-4">ค้นพบเคล็ดลับการดูแลสุขภาพ เทคนิคการออกกำลังกาย และความรู้ด้านโภชนาการ เพื่อชีวิตที่แข็งแรงและมีความสุขไปกับ GoFit</p>

                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <form action="{{ route('health-articles.index') }}" method="GET" id="search-form">
                            <div class="input-group">
                                <input type="text" class="form-control search-box"
                                       placeholder="ค้นหาบทความ..."
                                       name="search"
                                       value="{{ request('search') }}">
                                <button class="btn search-btn" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<div class="container py-4">
    <div class="bg-light rounded-3 p-4 mb-4 filter-section">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div class="filter-label">หมวดหมู่</div>
                <div class="category-badges">
                    <a href="{{ route('health-articles.index', array_merge(request()->except('category'), ['page' => 1])) }}"
                       class="category-badge {{ !request('category') ? 'active' : '' }}">
                        ทั้งหมด
                    </a>
                    @foreach($categories as $category)
                    <a href="{{ route('health-articles.index', array_merge(request()->except('category'), ['category' => $category->category_id, 'page' => 1])) }}"
                       class="category-badge {{ request('category') == $category->category_id ? 'active' : '' }}">
                        {{ $category->category_name }}
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="filter-label">เรียงตาม</div>
                <button class="sort-btn {{ request('sort') == 'latest' || !request('sort') ? 'active' : '' }}"
                        data-sort="latest">
                    <i class="fas fa-clock me-1"></i>ล่าสุด
                </button>
                <button class="sort-btn {{ request('sort') == 'popular' ? 'active' : '' }}"
                        data-sort="popular">
                    <i class="fas fa-fire me-1"></i>ยอดนิยม
                </button>
                <button class="sort-btn {{ request('sort') == 'most_liked' ? 'active' : '' }}"
                        data-sort="most_liked">
                    <i class="fas fa-heart me-1"></i>ถูกใจมากที่สุด
                </button>
            </div>
        </div>

        @if(request('search') || request('category') || request('tag'))
        <div class="mt-3 pt-3 border-top">
            <div class="d-flex align-items-center">
                <span class="filter-label me-2">กำลังแสดงผลสำหรับ:</span>
                <div>
                    @if(request('search'))
                    <div class="tag-chip">
                        <i class="fas fa-search me-1"></i> {{ request('search') }}
                        <a href="{{ route('health-articles.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="text-decoration-none ms-1 text-dark">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    </div>
                    @endif

                    @if(request('category'))
                    <div class="tag-chip">
                        <i class="fas fa-folder me-1"></i>
                        {{ $categories->firstWhere('category_id', request('category'))->category_name }}
                        <a href="{{ route('health-articles.index', array_merge(request()->except('category'), ['page' => 1])) }}" class="text-decoration-none ms-1 text-dark">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    </div>
                    @endif

                    @if(request('tag'))
                    <div class="tag-chip">
                        <i class="fas fa-tag me-1"></i>
                        {{ $tags->firstWhere('tag_id', request('tag'))->tag_name }}
                        <a href="{{ route('health-articles.index', array_merge(request()->except('tag'), ['page' => 1])) }}" class="text-decoration-none ms-1 text-dark">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    </div>
                    @endif

                    <a href="{{ route('health-articles.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>ล้างตัวกรอง
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Articles Section -->
    <div class="row g-4 mb-4">
        @forelse($articles as $article)
        <div class="col-md-6 col-lg-4">
            <div class="card article-card h-100">
                <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : asset('images/article-placeholders/placeholder-' . (($loop->index % 5) + 1) . '.jpg') }}"
                     class="article-thumbnail"
                     alt="{{ $article->title }}">

                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <a href="{{ route('health-articles.index', ['category' => $article->category->category_id]) }}"
                           class="badge bg-light text-dark text-decoration-none">
                            {{ $article->category->category_name }}
                        </a>
                    </div>

                    <h5 class="card-title">
                        <a href="{{ route('health-articles.show', $article->article_id) }}" class="text-decoration-none text-dark">
                            {{ $article->title }}
                        </a>
                    </h5>

                    <p class="card-text">
                        {{ Str::limit(strip_tags($article->content), 100) }}
                    </p>

                    <div class="article-meta mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="article-meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($article->published_at)->locale('th')->format('d M Y') }}
                                </span>
                            </div>
                            <div>
                                <span class="article-meta-item">
                                    <i class="fas fa-eye"></i>
                                    {{ $article->view_count }}
                                </span>
                                <span class="article-meta-item">
                                    <i class="fas fa-heart"></i>
                                    {{ $article->likes->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('health-articles.show', $article->article_id) }}" class="stretched-link"></a>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="far fa-file-alt"></i>
                </div>
                <h3>ไม่พบบทความที่ต้องการ</h3>
                <p class="empty-state-text">
                    ไม่พบบทความที่ตรงกับเงื่อนไขการค้นหาของคุณ กรุณาลองค้นหาด้วยคำค้นอื่น หรือดูบทความในหมวดหมู่อื่น
                </p>
                <a href="{{ route('health-articles.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-redo me-2"></i>ดูบทความทั้งหมด
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $articles->links() }}
    </div>

    <!-- Popular Tags Section -->
    @if(count($popularTags) > 0)
    <div class="bg-light rounded-3 p-4 mt-4">
        <h4 class="mb-3">แท็กยอดนิยม</h4>
        <div>
            @foreach($popularTags as $tag)
            <a href="{{ route('health-articles.index', array_merge(request()->except('tag'), ['tag' => $tag->tag_id, 'page' => 1])) }}"
               class="tag-chip {{ request('tag') == $tag->tag_id ? 'active' : '' }}">
                #{{ $tag->tag_name }}
                <span class="ms-1">({{ $tag->articles_count }})</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Save Filter Modal -->
@auth
<div class="modal fade" id="saveFilterModal" tabindex="-1" aria-labelledby="saveFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saveFilterModalLabel">บันทึกตัวกรอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="save-filter-form" action="{{ route('health-articles.save-filter') }}" method="POST">
                    @csrf
                    <input type="hidden" name="filter_data" id="filter-data">
                    <div class="mb-3">
                        <label for="filter-name" class="form-label">ชื่อตัวกรอง</label>
                        <input type="text" class="form-control" id="filter-name" name="filter_name" required>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth

@auth
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="toast-message"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endauth
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle sort buttons
        const sortButtons = document.querySelectorAll('.sort-btn');
        const searchForm = document.getElementById('search-form');

        sortButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sortValue = this.getAttribute('data-sort');

                // Create hidden input for sort
                let sortInput = document.querySelector('input[name="sort"]');
                if (!sortInput) {
                    sortInput = document.createElement('input');
                    sortInput.type = 'hidden';
                    sortInput.name = 'sort';
                    searchForm.appendChild(sortInput);
                }

                sortInput.value = sortValue;
                searchForm.submit();
            });
        });

        // Preserve page number when changing filters
        document.querySelectorAll('.category-badge, .tag-chip').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.href.includes('page=1')) {
                    // Skip if it's already set to page 1
                    return;
                }

                e.preventDefault();

                let url = new URL(this.href);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            });
        });

        @auth
        // Save filter functionality
        const saveFilterBtn = document.getElementById('save-filter-btn');
        if (saveFilterBtn) {
            saveFilterBtn.addEventListener('click', function() {
                const filterData = {
                    search: '{{ request('search') }}',
                    category: '{{ request('category') }}',
                    tag: '{{ request('tag') }}',
                    sort: '{{ request('sort') }}'
                };

                document.getElementById('filter-data').value = JSON.stringify(filterData);

                const modal = new bootstrap.Modal(document.getElementById('saveFilterModal'));
                modal.show();
            });
        }

        // Show success toast
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');
        if (successMessage) {
            const toast = new bootstrap.Toast(document.getElementById('successToast'));
            document.getElementById('toast-message').textContent = decodeURIComponent(successMessage);
            toast.show();
        }
        @endauth
    });
</script>
@endsection
