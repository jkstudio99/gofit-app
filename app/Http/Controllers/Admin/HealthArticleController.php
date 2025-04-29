<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthArticle;
use App\Models\ArticleCategory;
use App\Models\ArticleComment;
use App\Models\ArticleTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        return view('admin.health-articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new health article.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ArticleCategory::all();
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
            'category_id' => 'required|exists:article_categories,category_id',
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

        return view('admin.health-articles.edit', compact('article', 'categories'));
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
            'content' => 'required|string',
            'category_id' => 'required|exists:article_categories,category_id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
        ]);

        $article = HealthArticle::findOrFail($id);
        $data = $request->except(['thumbnail', '_token', '_method']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }

            $thumbnail = $request->file('thumbnail');
            $filename = 'article_' . time() . '.' . $thumbnail->getClientOriginalExtension();
            $path = $thumbnail->storeAs('article_thumbnails', $filename, 'public');
            $data['thumbnail'] = $path;
        }

        // Update published_at date if publishing for the first time
        if ($request->has('is_published') && $request->is_published && !$article->is_published) {
            $data['published_at'] = Carbon::now();
        }

        $article->update($data);

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

        // Get the most liked articles
        $mostLikedArticles = HealthArticle::published()
                              ->withCount('likes')
                              ->orderBy('likes_count', 'desc')
                              ->limit(5)
                              ->get();

        // Get the most commented articles
        $mostCommentedArticles = HealthArticle::published()
                                  ->withCount('comments')
                                  ->orderBy('comments_count', 'desc')
                                  ->limit(5)
                                  ->get();

        // Get the most shared articles
        $mostSharedArticles = HealthArticle::published()
                               ->withCount('shares')
                               ->orderBy('shares_count', 'desc')
                               ->limit(5)
                               ->get();

        // Get the most saved articles
        $mostSavedArticles = HealthArticle::published()
                              ->withCount('savedBy')
                              ->orderBy('saved_by_count', 'desc')
                              ->limit(5)
                              ->get();

        // Get stats by category
        $categoryStats = ArticleCategory::withCount(['articles' => function($query) {
                            $query->where('is_published', true);
                        }])
                        ->orderBy('articles_count', 'desc')
                        ->get();

        // Get total counts
        $totalArticles = HealthArticle::count();
        $publishedArticles = HealthArticle::where('is_published', true)->count();
        $totalViews = HealthArticle::sum('view_count');
        $totalLikes = DB::table('article_likes')->count();
        $totalComments = DB::table('article_comments')->count();
        $totalShares = DB::table('article_shares')->count();

        return view('admin.health-articles.statistics', compact(
            'mostViewedArticles',
            'mostLikedArticles',
            'mostCommentedArticles',
            'mostSharedArticles',
            'mostSavedArticles',
            'categoryStats',
            'totalArticles',
            'publishedArticles',
            'totalViews',
            'totalLikes',
            'totalComments',
            'totalShares'
        ));
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
     * Upload an image for the article editor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('file');
        $filename = 'article_content_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('article_content', $filename, 'public');

        return response()->json([
            'url' => asset('storage/' . $path)
        ]);
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
}
