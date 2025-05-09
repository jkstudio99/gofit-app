<?php

namespace App\Http\Controllers;

use App\Models\HealthArticle;
use App\Models\ArticleCategory;
use App\Models\ArticleComment;
use App\Models\ArticleLike;
use App\Models\SavedArticle;
use App\Models\ArticleShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $query = HealthArticle::published()->with(['category', 'author']);

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter by tag if provided
        if ($request->has('tag') && !empty($request->tag)) {
            $tagId = $request->tag;
            $query->whereExists(function ($query) use ($tagId) {
                $query->select(DB::raw(1))
                      ->from('tb_health_article_tag')
                      ->whereColumn('tb_health_article_tag.article_id', 'tb_health_articles.article_id')
                      ->where('tb_health_article_tag.tag_id', $tagId);
            });
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

        // Create default categories if none exist
        if ($categories->isEmpty()) {
            $this->createDefaultCategories();
            $categories = ArticleCategory::all();
        }

        // Try to get popular tags
        try {
            $popularTags = DB::table('tb_health_article_tag')
                ->selectRaw('tag_id, COUNT(*) as articles_count')
                ->groupBy('tag_id')
                ->orderBy('articles_count', 'desc')
                ->limit(5)
                ->get();

            // Get tag names in a separate query to avoid self-join
            $tagIds = $popularTags->pluck('tag_id')->toArray();
            $tagNames = DB::table('tb_health_article_tag')
                ->whereIn('tag_id', $tagIds)
                ->select('tag_id', 'tag_name')
                ->get()
                ->keyBy('tag_id');

            // Combine the data
            $popularTags = $popularTags->map(function($tag) use ($tagNames) {
                $tag->tag_name = $tagNames[$tag->tag_id]->tag_name ?? '';
                return $tag;
            });
        } catch (\Exception $e) {
            $popularTags = collect();
        }

        // Get the articles with pagination
        $articles = $query->orderBy('published_at', 'desc')->paginate(9);

        return view('health-articles.index', compact('articles', 'categories', 'popularTags'));
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

    /**
     * Display the specified health article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = HealthArticle::published()
                    ->with(['category', 'author', 'comments.user'])
                    ->findOrFail($id);

        // Manually load tags using a raw query to avoid join issues
        try {
            $tags = DB::table('tb_health_article_tag as pivot')
                ->join('tb_health_article_tag as tags', 'pivot.tag_id', '=', 'tags.tag_id')
                ->where('pivot.article_id', $id)
                ->select('tags.tag_id', 'tags.tag_name')
                ->get();

            // Add the tags to the article
            $article->setRelation('tags', $tags);
        } catch (\Exception $e) {
            // If there's an error, set an empty collection
            $article->setRelation('tags', collect([]));
        }

        // Increment view count
        $article->incrementViewCount();

        // Check if the user has liked or saved this article
        $userLiked = false;
        $userSaved = false;

        if (Auth::check()) {
            $userId = Auth::id();
            $userLiked = $article->isLikedByUser($userId);
            $userSaved = $article->isSavedByUser($userId);
        }

        // Get related articles in the same category
        $relatedArticles = HealthArticle::published()
                            ->where('category_id', $article->category_id)
                            ->where('article_id', '!=', $article->article_id)
                            ->orderBy('published_at', 'desc')
                            ->limit(3)
                            ->get();

        return view('health-articles.show', compact('article', 'userLiked', 'userSaved', 'relatedArticles'));
    }

    /**
     * Store a comment for an article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $article = HealthArticle::published()->findOrFail($id);

        $comment = new ArticleComment([
            'user_id' => Auth::id(),
            'article_id' => $id,
            'comment_text' => $request->content,
        ]);

        $comment->save();

        // Load the user relationship for the new comment
        $comment->load('user');

        // Add the can_delete flag
        $comment->can_delete = true;

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'comments_count' => $article->comments()->count()
            ]);
        }

        return redirect()->back()->with('success', 'ความคิดเห็นของคุณถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Toggle like status for an article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleLike(Request $request, $id)
    {
        $article = HealthArticle::published()->findOrFail($id);
        $userId = Auth::id();

        $existingLike = ArticleLike::where('article_id', $id)
                         ->where('user_id', $userId)
                         ->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $message = 'คุณได้ยกเลิกการกดถูกใจบทความนี้';
            $liked = false;
        } else {
            // Like
            ArticleLike::create([
                'article_id' => $id,
                'user_id' => $userId,
            ]);
            $message = 'คุณได้กดถูกใจบทความนี้';
            $liked = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likesCount' => $article->likes()->count(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle save status for an article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleSave(Request $request, $id)
    {
        $article = HealthArticle::published()->findOrFail($id);
        $userId = Auth::id();

        $saved = SavedArticle::where('article_id', $id)
                  ->where('user_id', $userId)
                  ->first();

        if ($saved) {
            // Unsave
            $saved->delete();
            $message = 'คุณได้ยกเลิกการบันทึกบทความนี้';
            $isSaved = false;
        } else {
            // Save
            SavedArticle::create([
                'article_id' => $id,
                'user_id' => $userId,
            ]);
            $message = 'คุณได้บันทึกบทความนี้';
            $isSaved = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'saved' => $isSaved,
                'saves_count' => $article->savedBy()->count()
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Share an article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function share(Request $request, $id)
    {
        $request->validate([
            'platform' => 'required|string|in:facebook,twitter,line,email',
        ]);

        $article = HealthArticle::published()->findOrFail($id);

        // Record the share
        ArticleShare::create([
            'article_id' => $id,
            'user_id' => Auth::check() ? Auth::id() : null,
            'platform' => $request->platform,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'sharesCount' => $article->shares()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'ขอบคุณสำหรับการแชร์บทความ');
    }

    /**
     * Display a listing of the user's saved articles.
     *
     * @return \Illuminate\Http\Response
     */
    public function savedArticles()
    {
        $savedArticleIds = SavedArticle::where('user_id', Auth::id())
            ->where('is_filter', false)
            ->pluck('article_id');

        $savedArticles = HealthArticle::whereIn('article_id', $savedArticleIds)
            ->with('category')
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('health-articles.saved', compact('savedArticles'));
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
        $article = HealthArticle::findOrFail($comment->article_id);

        // Check if the user is authorized to delete this comment
        $isOwner = Auth::id() === $comment->user_id;

        if (!$isOwner) {
            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์ลบความคิดเห็นนี้'
                ], 403);
            }
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลบความคิดเห็นนี้');
        }

        $comment->delete();

        if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'comments_count' => $article->comments()->count()
            ]);
        }

        return redirect()->back()->with('success', 'ลบความคิดเห็นเรียบร้อยแล้ว');
    }

    /**
     * Save filter preferences for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveFilter(Request $request)
    {
        $request->validate([
            'filter_name' => 'required|string|max:100',
            'filter_data' => 'required|string',
        ]);

        // Use SavedArticle model to store filter data
        // We repurpose the saved article functionality to store filters
        SavedArticle::create([
            'article_id' => 0, // Using 0 as a placeholder for filter
            'user_id' => Auth::id(),
            'filter_name' => $request->filter_name,
            'filter_data' => $request->filter_data,
            'is_filter' => true // Add a flag to identify this as a filter, not an article
        ]);

        return redirect()->route('health-articles.index')
            ->with('success', 'บันทึกตัวกรองเรียบร้อยแล้ว');
    }
}
