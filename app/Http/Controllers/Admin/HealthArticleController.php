<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthArticle;
use App\Models\ArticleCategory;
use App\Models\ArticleComment;
use App\Models\ArticleTag;
use App\Models\ArticleView;
use App\Models\ArticleLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HealthArticleController extends Controller
{
    /**
     * Display a listing of the health articles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = HealthArticle::with('category', 'author');

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter by publication status
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Search by keyword if provided
        if ($request->has('search') && !empty($request->search)) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        // Get all categories for the filter
        $categories = ArticleCategory::all();

        // Get the articles with pagination
        $articles = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate statistics
        $publishedCount = HealthArticle::where('is_published', true)->count();
        $topViewedCount = HealthArticle::where('view_count', '>', 0)->count();

        // Since tb_article_likes doesn't exist yet, set to 0 or handle differently
        $topLikedCount = 0;

        return view('admin.health-articles.index', compact('articles', 'categories', 'publishedCount', 'topViewedCount', 'topLikedCount'));
    }

    /**
     * Show the form for creating a new health article.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ArticleCategory::all();

        // Create default categories if none exist
        if ($categories->isEmpty()) {
            $this->createDefaultCategories();
            $categories = ArticleCategory::all();
        }

        $tags = ArticleTag::all();
        return view('admin.health-articles.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created health article in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:tb_health_article_categories,category_id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
        ]);

        $data = $request->except('thumbnail');
        $data['user_id'] = Auth::id();

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = 'article_' . time() . '.' . $thumbnail->getClientOriginalExtension();
            $path = $thumbnail->storeAs('article_thumbnails', $filename, 'public');
            $data['thumbnail'] = $path;
        }

        // Set published_at date if article is published
        if ($request->has('is_published') && $request->is_published) {
            $data['published_at'] = Carbon::now();
        }

        $article = HealthArticle::create($data);

        return redirect()->route('admin.health-articles.index')
                         ->with('success', 'บทความถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified health article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = HealthArticle::with(['category', 'author', 'comments.user', 'likes', 'shares'])
                    ->findOrFail($id);

        // Get statistics
        $likesCount = $article->likes()->count();
        $commentsCount = $article->comments()->count();
        $sharesCount = $article->shares()->count();
        $savedCount = $article->savedBy()->count();

        return view('admin.health-articles.show', compact('article', 'likesCount', 'commentsCount', 'sharesCount', 'savedCount'));
    }

    /**
     * Show the form for editing the specified health article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article = HealthArticle::findOrFail($id);
        $categories = ArticleCategory::all();
        $tags = ArticleTag::all();

        // Create default categories if none exist
        if ($categories->isEmpty()) {
            $this->createDefaultCategories();
            $categories = ArticleCategory::all();
        }

        return view('admin.health-articles.edit', compact('article', 'categories', 'tags'));
    }

    /**
     * Update the specified health article in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tb_health_articles,slug,' . $id . ',article_id',
            'excerpt' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:tb_health_article_categories,category_id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tb_health_article_tag,tag_id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $article = HealthArticle::findOrFail($id);

        // Set basic fields
        $article->title = $request->title;
        $article->slug = $request->slug;
        $article->excerpt = $request->excerpt;
        $article->content = $request->content;
        $article->category_id = $request->category_id;
        $article->meta_title = $request->meta_title;
        $article->meta_description = $request->meta_description;

        // Handle status (is_published)
        $article->is_published = ($request->status === 'published');

        // Update published_at date if publishing for the first time
        if ($article->is_published && $article->isDirty('is_published')) {
            $article->published_at = now();
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }

            $thumbnail = $request->file('thumbnail');
            $filename = 'article_' . time() . '_' . Str::random(10) . '.' . $thumbnail->getClientOriginalExtension();
            $path = $thumbnail->storeAs('health-articles/thumbnails', $filename, 'public');
            $article->thumbnail = $path;
        }

        $article->save();

        // Sync tags
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        } else {
            $article->tags()->detach();
        }

        return redirect()->route('admin.health-articles.index')
                         ->with('success', 'บทความถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified health article from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = HealthArticle::findOrFail($id);

        // Delete thumbnail if exists
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }

        $article->delete();

        return redirect()->route('admin.health-articles.index')
                         ->with('success', 'บทความถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Display the statistics for all health articles.
     *
     * @return \Illuminate\Http\Response
     */
    public function statistics()
    {
        // Get the most viewed articles
        $mostViewedArticles = HealthArticle::published()
                               ->orderBy('view_count', 'desc')
                               ->limit(5)
                               ->get();

        // Since we don't have the likes table yet, just use the published articles
        $mostLikedArticles = HealthArticle::published()
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get();

        // Since we don't have the comments table yet, just use the published articles
        $mostCommentedArticles = HealthArticle::published()
                                  ->orderBy('created_at', 'desc')
                                  ->limit(5)
                                  ->get();

        // Get recent articles
        $recentArticles = HealthArticle::published()
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        // Get stats by category
        $categoryStats = ArticleCategory::withCount(['articles' => function($query) {
                            $query->where('is_published', true);
                        }])
                        ->orderBy('articles_count', 'desc')
                        ->get();

        // Define category colors for the chart
        $categoryColors = [
            '#2DC679', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#10B981', '#6366F1', '#F97316', '#14B8A6'
        ];

        // Create views chart data (last 7 days)
        $viewsChart = $this->getViewsChartData();

        // Get total counts
        $totalArticles = HealthArticle::count();
        $publishedArticles = HealthArticle::where('is_published', true)->count();
        $totalViews = HealthArticle::sum('view_count');

        // Set placeholders for counts from tables that don't exist yet
        $totalLikes = 0;
        $totalComments = 0;
        $totalShares = 0;

        return view('admin.health-articles.statistics', compact(
            'mostViewedArticles',
            'mostLikedArticles',
            'mostCommentedArticles',
            'recentArticles',
            'categoryStats',
            'categoryColors',
            'viewsChart',
            'totalArticles',
            'publishedArticles',
            'totalViews',
            'totalLikes',
            'totalComments',
            'totalShares'
        ));
    }

    /**
     * Get views chart data for the last 7 days
     *
     * @return array
     */
    private function getViewsChartData()
    {
        $days = 7;
        $range = [];
        $views = [];

        // Get date range
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $range[] = Carbon::now()->subDays($i)->format('d/m');

            // Count views for each day (placeholder for now)
            $views[] = rand(10, 100); // Random placeholder data
        }

        return [
            'labels' => $range,
            'views' => $views
        ];
    }

    /**
     * Manage the comments for all articles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function manageComments(Request $request)
    {
        $query = ArticleComment::with(['article', 'user']);

        // Search by keyword if provided
        if ($request->has('search') && !empty($request->search)) {
            $keyword = $request->search;
            $query->where('comment_text', 'like', "%{$keyword}%");
        }

        // Filter by article if provided
        if ($request->has('article_id') && $request->article_id) {
            $query->where('article_id', $request->article_id);
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get articles for filter dropdown
        $articles = HealthArticle::select('article_id', 'title')->get();

        return view('admin.health-articles.comments', compact('comments', 'articles'));
    }

    /**
     * Delete a comment.
     *
     * @param  int  $commentId
     * @return \Illuminate\Http\Response
     */
    public function deleteComment($commentId)
    {
        $comment = ArticleComment::findOrFail($commentId);
        $comment->delete();

        return redirect()->back()->with('success', 'ความคิดเห็นถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Upload image for summernote editor
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/health-articles/content', $filename);
            $url = asset('storage/health-articles/content/' . $filename);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'No image provided'], 422);
    }

    /**
     * Toggle the published status of an article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function togglePublished($id)
    {
        $article = HealthArticle::findOrFail($id);

        $article->is_published = !$article->is_published;

        // Set published_at date if publishing for the first time
        if ($article->is_published && !$article->published_at) {
            $article->published_at = Carbon::now();
        }

        $article->save();

        $status = $article->is_published ? 'เผยแพร่' : 'ฉบับร่าง';

        return redirect()->back()->with('success', "บทความถูกเปลี่ยนสถานะเป็น {$status} เรียบร้อยแล้ว");
    }

    /**
     * Create default categories if none exist.
     */
    private function createDefaultCategories()
    {
        $defaultCategories = [
            ['category_name' => 'สุขภาพทั่วไป', 'category_desc' => 'บทความเกี่ยวกับสุขภาพทั่วไป'],
            ['category_name' => 'โภชนาการ', 'category_desc' => 'บทความเกี่ยวกับอาหารและโภชนาการ'],
            ['category_name' => 'การออกกำลังกาย', 'category_desc' => 'บทความเกี่ยวกับการออกกำลังกายและการเล่นกีฬา'],
            ['category_name' => 'สุขภาพจิต', 'category_desc' => 'บทความเกี่ยวกับสุขภาพจิตและการพัฒนาจิตใจ'],
            ['category_name' => 'การนอนหลับ', 'category_desc' => 'บทความเกี่ยวกับการนอนหลับและการพักผ่อน']
        ];

        foreach ($defaultCategories as $category) {
            ArticleCategory::create($category);
        }
    }
}
