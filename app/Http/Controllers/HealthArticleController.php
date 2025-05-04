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
        $query = HealthArticle::published()->with('category', 'author');

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
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

        // Get the articles with pagination
        $articles = $query->orderBy('published_at', 'desc')->paginate(9);

        return view('health-articles.index', compact('articles', 'categories'));
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
            'comment_text' => 'required|string|max:500',
        ]);

        $article = HealthArticle::published()->findOrFail($id);

        $comment = new ArticleComment([
            'user_id' => Auth::id(),
            'comment_text' => $request->comment_text,
        ]);

        $article->comments()->save($comment);

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
        $savedArticles = Auth::user()->savedArticles()
                           ->with('category')
                           ->orderBy('saved_articles.created_at', 'desc')
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

        // Check if the user is the owner of the comment
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์ลบความคิดเห็นนี้');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'ลบความคิดเห็นเรียบร้อยแล้ว');
    }
}
