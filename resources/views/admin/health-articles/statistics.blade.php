@extends('layouts.admin')

@section('title', 'สถิติบทความสุขภาพ - GoFit')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
    .stat-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-card .card-body {
        display: flex;
        align-items: center;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-right: 1rem;
        font-size: 1.5rem;
    }
    .stat-info {
        flex: 1;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .bg-article {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
    .bg-view {
        background-color: rgba(45, 198, 121, 0.1);
        color: #2DC679;
    }
    .bg-like {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    .bg-comment {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    .chart-container {
        height: 300px;
        position: relative;
    }
    .top-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .top-list-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    .top-list-item:last-child {
        border-bottom: none;
    }
    .top-list-rank {
        font-size: 1.25rem;
        font-weight: 700;
        width: 2rem;
        text-align: center;
        color: #6c757d;
    }
    .top-list-img {
        width: 50px;
        height: 50px;
        border-radius: 5px;
        object-fit: cover;
        margin: 0 1rem;
    }
    .top-list-content {
        flex: 1;
    }
    .top-list-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .top-list-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: #6c757d;
    }
    .top-list-stats i {
        margin-right: 0.25rem;
    }
    .top-list-value {
        font-size: 1rem;
        font-weight: 600;
        text-align: right;
        min-width: 3rem;
    }
    .date-range-selector .btn-group {
        margin-bottom: 1rem;
    }
    .category-distribution-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .category-color {
        width: 15px;
        height: 15px;
        border-radius: 3px;
        margin-right: 0.5rem;
    }
    .category-name {
        flex: 1;
    }
    .category-value {
        font-weight: 600;
        text-align: right;
        min-width: 2.5rem;
    }
    .category-percent {
        min-width: 3rem;
        text-align: right;
        color: #6c757d;
        font-size: 0.875rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">สถิติบทความสุขภาพ</h1>
        <div>
            <a href="{{ route('admin.health-articles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการบทความ
            </a>
        </div>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.health-articles.index') }}">บทความสุขภาพ</a></li>
        <li class="breadcrumb-item active">สถิติบทความ</li>
    </ol>

    <!-- Date Range Selector -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-alt me-1"></i>
            เลือกช่วงเวลา
        </div>
        <div class="card-body date-range-selector">
            <form action="{{ route('admin.health-articles.statistics') }}" method="GET" class="row align-items-end">
                <div class="col-md-6 mb-3">
                    <div class="btn-group" role="group">
                        <button type="submit" name="range" value="week" class="btn {{ request('range', 'month') == 'week' ? 'btn-primary' : 'btn-outline-secondary' }}">7 วันล่าสุด</button>
                        <button type="submit" name="range" value="month" class="btn {{ request('range', 'month') == 'month' ? 'btn-primary' : 'btn-outline-secondary' }}">30 วันล่าสุด</button>
                        <button type="submit" name="range" value="quarter" class="btn {{ request('range') == 'quarter' ? 'btn-primary' : 'btn-outline-secondary' }}">3 เดือนล่าสุด</button>
                        <button type="submit" name="range" value="year" class="btn {{ request('range') == 'year' ? 'btn-primary' : 'btn-outline-secondary' }}">1 ปีล่าสุด</button>
                        <button type="submit" name="range" value="all" class="btn {{ request('range') == 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">ทั้งหมด</button>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="row">
                        <div class="col-md-5">
                            <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-5">
                            <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-article">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ number_format($totalArticles ?? 0) }}</div>
                        <div class="stat-label">บทความทั้งหมด</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-view">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ number_format($totalViews ?? 0) }}</div>
                        <div class="stat-label">ยอดเข้าชมทั้งหมด</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-like">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ number_format($totalLikes ?? 0) }}</div>
                        <div class="stat-label">ยอดถูกใจทั้งหมด</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-comment">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ number_format($totalComments ?? 0) }}</div>
                        <div class="stat-label">ความคิดเห็นทั้งหมด</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- View Trends -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    แนวโน้มการเข้าชมบทความ
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="viewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    สัดส่วนบทความตามหมวดหมู่
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-3">
                        @php
                            // Define category colors if not defined
                            $categoryColors = $categoryColors ?? [
                                '#2DC679', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6',
                                '#EC4899', '#10B981', '#6366F1', '#F97316', '#14B8A6'
                            ];
                        @endphp
                        @foreach($categoryStats as $index => $category)
                        <div class="category-distribution-item">
                            <div class="category-color" style="background-color: {{ $categoryColors[$index % count($categoryColors)] }}"></div>
                            <div class="category-name">{{ $category->category_name }}</div>
                            <div class="category-value">{{ $category->articles_count }}</div>
                            <div class="category-percent">{{ number_format(($category->articles_count / ($totalArticles ?: 1)) * 100, 1) }}%</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Most Viewed Articles -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-eye me-1"></i>
                    บทความที่มีผู้เข้าชมมากที่สุด
                </div>
                <div class="card-body">
                    @if(count($mostViewedArticles) > 0)
                    <ul class="top-list">
                        @foreach($mostViewedArticles as $index => $article)
                        <li class="top-list-item">
                            <div class="top-list-rank">{{ $index + 1 }}</div>
                            @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="top-list-img">
                            @else
                            <div class="top-list-img bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                            @endif
                            <div class="top-list-content">
                                <div class="top-list-title">{{ $article->title }}</div>
                                <div class="top-list-stats">
                                    <span><i class="fas fa-calendar-alt"></i> {{ $article->created_at->format('d/m/Y') }}</span>
                                    <span><i class="fas fa-folder"></i> {{ $article->category->category_name }}</span>
                                </div>
                            </div>
                            <div class="top-list-value">
                                {{ number_format($article->view_count) }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <p>ไม่มีข้อมูลการเข้าชมในช่วงเวลาที่เลือก</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Most Liked Articles -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-heart me-1"></i>
                    บทความที่มีผู้ถูกใจมากที่สุด
                </div>
                <div class="card-body">
                    @if(count($mostLikedArticles) > 0)
                    <ul class="top-list">
                        @foreach($mostLikedArticles as $index => $article)
                        <li class="top-list-item">
                            <div class="top-list-rank">{{ $index + 1 }}</div>
                            @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="top-list-img">
                            @else
                            <div class="top-list-img bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                            @endif
                            <div class="top-list-content">
                                <div class="top-list-title">{{ $article->title }}</div>
                                <div class="top-list-stats">
                                    <span><i class="fas fa-calendar-alt"></i> {{ $article->created_at->format('d/m/Y') }}</span>
                                    <span><i class="fas fa-folder"></i> {{ $article->category->category_name }}</span>
                                </div>
                            </div>
                            <div class="top-list-value">
                                {{ number_format($article->like_count) }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <p>ไม่มีข้อมูลการถูกใจในช่วงเวลาที่เลือก</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Articles -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-clock me-1"></i>
                    บทความล่าสุด
                </div>
                <div class="card-body">
                    @if(count($recentArticles) > 0)
                    <ul class="top-list">
                        @foreach($recentArticles as $article)
                        <li class="top-list-item">
                            @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="top-list-img">
                            @else
                            <div class="top-list-img bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                            @endif
                            <div class="top-list-content">
                                <div class="top-list-title">{{ $article->title }}</div>
                                <div class="top-list-stats">
                                    <span><i class="fas fa-folder"></i> {{ $article->category->category_name }}</span>
                                    <span><i class="fas fa-eye"></i> {{ number_format($article->view_count) }}</span>
                                    <span><i class="fas fa-heart"></i> {{ number_format($article->like_count) }}</span>
                                </div>
                            </div>
                            <div class="top-list-value">
                                {{ $article->created_at->format('d/m/Y') }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <p>ไม่มีบทความในช่วงเวลาที่เลือก</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Most Commented Articles -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-comments me-1"></i>
                    บทความที่มีความคิดเห็นมากที่สุด
                </div>
                <div class="card-body">
                    @if(count($mostCommentedArticles) > 0)
                    <ul class="top-list">
                        @foreach($mostCommentedArticles as $index => $article)
                        <li class="top-list-item">
                            <div class="top-list-rank">{{ $index + 1 }}</div>
                            @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="top-list-img">
                            @else
                            <div class="top-list-img bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                            @endif
                            <div class="top-list-content">
                                <div class="top-list-title">{{ $article->title }}</div>
                                <div class="top-list-stats">
                                    <span><i class="fas fa-calendar-alt"></i> {{ $article->created_at->format('d/m/Y') }}</span>
                                    <span><i class="fas fa-folder"></i> {{ $article->category->category_name }}</span>
                                </div>
                            </div>
                            <div class="top-list-value">
                                {{ number_format($article->comments_count) }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <p>ไม่มีข้อมูลความคิดเห็นในช่วงเวลาที่เลือก</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // การตั้งค่ากราฟทั่วไป
        Chart.defaults.font.family = 'Sarabun, sans-serif';
        Chart.defaults.color = '#6c757d';
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;

        // กราฟแนวโน้มการเข้าชมบทความ
        const viewsChartCtx = document.getElementById('viewsChart').getContext('2d');
        new Chart(viewsChartCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($viewsChart['labels'] ?? []) !!},
                datasets: [
                    {
                        label: 'ยอดเข้าชม',
                        data: {!! json_encode($viewsChart['views'] ?? []) !!},
                        borderColor: '#2DC679',
                        backgroundColor: 'rgba(45, 198, 121, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#2DC679',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('th-TH');
                            }
                        },
                        title: {
                            display: true,
                            text: 'จำนวนการเข้าชม',
                            font: {
                                size: 14,
                                weight: 'normal'
                            },
                            padding: {top: 10, bottom: 10}
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'วันที่',
                            font: {
                                size: 14,
                                weight: 'normal'
                            },
                            padding: {top: 10, bottom: 10}
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return `ยอดเข้าชม: ${context.parsed.y.toLocaleString('th-TH')} ครั้ง`;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // กราฟสัดส่วนบทความตามหมวดหมู่
        const categoryChartCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryChartCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryStats->pluck('category_name')->toArray()) !!},
                datasets: [{
                    data: {!! json_encode($categoryStats->pluck('articles_count')->toArray()) !!},
                    backgroundColor: {!! json_encode($categoryColors ?? ['#2DC679', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#10B981', '#6366F1', '#F97316', '#14B8A6']) !!},
                    borderWidth: 1,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            font: {
                                size: 13
                            }
                        }
                    },
                    tooltip: {
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const count = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((count / total) * 100).toFixed(1);
                                return `${context.label}: ${count.toLocaleString('th-TH')} บทความ (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endsection
