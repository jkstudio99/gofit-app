<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HealthArticleController;

// Fix for the route not found issue
Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::get('/admin/health-articles/api/search', [HealthArticleController::class, 'apiSearch'])
        ->name('admin.health-articles.api.search');
});
