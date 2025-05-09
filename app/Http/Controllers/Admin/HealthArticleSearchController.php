<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthArticle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HealthArticleSearchController extends Controller
{
    /**
     * API endpoint สำหรับการค้นหาบทความแบบ Realtime AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSearch(Request $request)
    {
        // ตั้งค่าภาษาไทยสำหรับ Carbon
        Carbon::setLocale('th');

        $query = HealthArticle::with('category', 'author');

        // Filter by category if provided
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Filter by publication status
        if ($request->has('status') && $request->status != '') {
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
                  ->orWhere('content', 'like', "%{$keyword}%")
                  ->orWhere('excerpt', 'like', "%{$keyword}%");
            });
        }

        // Apply sorting
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'views':
                $query->orderBy('view_count', 'desc');
                break;
            case 'likes':
                $query->orderBy('like_count', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get articles with pagination
        $articles = $query->paginate(10);

        // Calculate statistics for the response
        $publishedCount = HealthArticle::where('is_published', true)->count();
        $topViewedCount = HealthArticle::where('view_count', '>', 0)->count();
        $topLikedCount = HealthArticle::where('like_count', '>', 0)->count();

        return response()->json([
            'success' => true,
            'html' => view('admin.health-articles.partials.article_list', compact('articles'))->render(),
            'pagination' => view('pagination.default', ['paginator' => $articles])->render(),
            'count' => $articles->total(),
            'stats' => [
                'total' => $articles->total(),
                'published' => $publishedCount,
                'topViewed' => $topViewedCount,
                'topLiked' => $topLikedCount
            ]
        ]);
    }
}
