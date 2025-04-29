<?php

namespace App\Http\Controllers;

use App\Models\HealthArticle;
use Illuminate\Http\Request;

class PublicHealthArticleController extends Controller
{
    /**
     * Display the latest health articles for the welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function latestArticles()
    {
        $latestArticles = HealthArticle::published()
                           ->with('category')
                           ->orderBy('published_at', 'desc')
                           ->limit(3)
                           ->get();

        return response()->json([
            'success' => true,
            'articles' => $latestArticles,
        ]);
    }
}
