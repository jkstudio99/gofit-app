<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HealthArticleSearchController;

// Health Articles Search API - สำหรับการค้นหาบทความ
Route::get('/admin/health-articles/search', [HealthArticleSearchController::class, 'apiSearch'])
    ->middleware(['web', 'auth', 'admin'])
    ->name('admin.health-articles.search.api');
