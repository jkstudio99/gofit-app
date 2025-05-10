<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RunActivityController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RunController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityGoalController;
use App\Http\Controllers\HealthArticleController;
use App\Http\Controllers\Admin\HealthArticleController as AdminHealthArticleController;
use App\Http\Controllers\PublicHealthArticleController;

// หน้าหลัก
Route::get('/', function () {
    return view('welcome');
});

// หน้านโยบายความเป็นส่วนตัว
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

// Auth routes (มาจาก Laravel UI)
Auth::routes();

// Route สำหรับ Dashboard (หลังจาก login)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route สำหรับผู้ใช้ทั่วไป (เพื่อ backward compatibility)
Route::get('/home', function() {
    return redirect()->route('dashboard');
})->name('home');

// Route สำหรับผู้ใช้ทั่วไป
Route::middleware(['auth'])->group(function () {
    // การวิ่ง
    Route::get('/run', [RunController::class, 'index'])->name('run.index');
    Route::get('/run/test', [RunController::class, 'test'])->name('run.test');
    Route::post('/run/start', [RunActivityController::class, 'start'])->name('run.start');
    Route::get('/run/check-active', [RunActivityController::class, 'checkActiveActivity'])->name('run.check-active');
    Route::post('/run/toggle-pause', [RunActivityController::class, 'togglePause'])->name('run.toggle-pause');
    Route::post('/run/update', [RunActivityController::class, 'update'])->name('run.update');
    Route::get('/run/test-start', function() {
        return response()->json([
            'status' => 'success',
            'message' => 'Test route works',
            'time' => now()->format('H:i:s')
        ]);
    })->name('run.test.start');
    Route::post('/run/finish', [RunActivityController::class, 'finish'])->name('run.finish');
    Route::post('/run/updateRoute', [RunActivityController::class, 'updateRoute'])->name('run.updateRoute');
    Route::post('/run/store', [RunController::class, 'store'])->name('run.store');
    Route::get('/run/history', [RunController::class, 'history'])->name('run.history');
    Route::get('/run/show/{id}', [RunController::class, 'show'])->name('run.show');
    Route::post('/run/destroy', [RunController::class, 'destroy'])->name('run.destroy');

    // เหรียญตรา
    Route::get('/badges', [BadgeController::class, 'index'])->name('badges.index');
    Route::post('/badges/{badge}/unlock', [BadgeController::class, 'unlockBadge'])->name('badges.unlock');
    Route::get('/badges/history', [BadgeController::class, 'history'])->name('badges.history');

    // รางวัล
    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/{reward}/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');
    Route::get('/rewards/history', [App\Http\Controllers\RedeemController::class, 'index'])->name('rewards.history');
    Route::post('/rewards/redeem/{redeem}/cancel', [App\Http\Controllers\RedeemController::class, 'cancel'])->name('rewards.redeem.cancel');
    Route::get('/rewards/redeem/{redeem}', [App\Http\Controllers\RedeemController::class, 'show'])->name('rewards.redeem.show');

    // โปรไฟล์ผู้ใช้
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/image', [DashboardController::class, 'updateProfileImage'])->name('profile.update-image');
    Route::put('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.update-password');
    Route::put('/profile/health', [DashboardController::class, 'updateHealth'])->name('profile.update-health');
    Route::get('/profile/{username}', [DashboardController::class, 'show'])->name('profile.show');
    Route::get('/profile/delete/confirm', [DashboardController::class, 'showDeleteAccountForm'])->name('profile.delete.confirm');
    Route::delete('/profile/delete', [DashboardController::class, 'deleteAccount'])->name('profile.delete');

    // ส่วนของผู้ใช้ - กิจกรรม
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::post('/events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.my');

    // Activity Routes
    Route::resource('goals', ActivityGoalController::class);

    // Health Articles Routes for Users
    Route::get('/health-articles', [HealthArticleController::class, 'index'])
        ->name('health-articles.index');

    // Show single article
    Route::get('/health-articles/{id}', [HealthArticleController::class, 'show'])
        ->name('health-articles.show');

    // Article interactions
    Route::post('/health-articles/{id}/comment', [HealthArticleController::class, 'storeComment'])
        ->name('health-articles.comment');
    Route::post('/health-articles/{id}/like', [HealthArticleController::class, 'toggleLike'])
        ->name('health-articles.like');
    Route::post('/health-articles/{id}/save', [HealthArticleController::class, 'toggleSave'])
        ->name('health-articles.save');
    Route::post('/health-articles/save-filter', [HealthArticleController::class, 'saveFilter'])
        ->name('health-articles.save-filter');
    Route::post('/health-articles/{id}/share', [HealthArticleController::class, 'share'])
        ->name('health-articles.share');
    Route::delete('/health-articles/comments/{commentId}', [HealthArticleController::class, 'deleteComment'])
        ->name('health-articles.delete-comment');

    // Saved articles
    Route::get('/my-saved-articles', [HealthArticleController::class, 'savedArticles'])
        ->name('health-articles.saved');

    // Sessions routes
    Route::get('/sessions', [App\Http\Controllers\SessionController::class, 'userSessions'])->name('sessions');
    Route::delete('/sessions/{sessionId}', [App\Http\Controllers\SessionController::class, 'destroySpecificSession'])->name('sessions.destroy');
    Route::delete('/sessions', [App\Http\Controllers\SessionController::class, 'destroyAllUserSessions'])->name('sessions.destroy.all');
});

// Route สำหรับ Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // หน้าแดชบอร์ด
    Route::get('/', [AdminController::class, 'index'])->name('home');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Users management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/users/{user}/reset-password', [AdminController::class, 'showResetPasswordForm'])->name('users.reset-password');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.update-password');
    Route::post('/users/{user}/update-profile-image', [AdminController::class, 'updateProfileImage'])->name('users.update-profile-image');

    // API สำหรับ Live Search ผู้ใช้
    Route::get('/api/users', [AdminController::class, 'apiUsers'])->name('api.users');

    // Image Upload for Summernote Editor
    Route::post('/upload/image', [AdminHealthArticleController::class, 'uploadImage'])->name('upload.image');

    // User activities
    Route::get('/user-activities', [AdminController::class, 'userActivities'])->name('user-activities');

    // Badges management - improved CRUD
    Route::get('/badges', [BadgeController::class, 'admin'])->name('badges.index');
    Route::get('/badges/api/search', [BadgeController::class, 'apiSearch'])->name('badges.api.search');
    Route::get('/badges/statistics', [BadgeController::class, 'statistics'])->name('badges.statistics');
    Route::get('/badges/history', [BadgeController::class, 'adminHistory'])->name('badges.history');
    Route::get('/badges/create', [BadgeController::class, 'create'])->name('badges.create');
    Route::post('/badges', [BadgeController::class, 'store'])->name('badges.store');
    Route::get('/badges/users/{badge}', [BadgeController::class, 'badgeUsers'])->name('badges.users');
    Route::get('/badges/{badge}', [BadgeController::class, 'show'])->name('badges.show');
    Route::get('/badges/{badge}/edit', [BadgeController::class, 'edit'])->name('badges.edit');
    Route::put('/badges/{badge}', [BadgeController::class, 'update'])->name('badges.update');
    Route::delete('/badges/{badge}', [BadgeController::class, 'destroy'])->name('badges.destroy');

    // Rewards management
    Route::get('/rewards', [RewardController::class, 'admin'])->name('rewards');
    Route::get('/rewards/api/search', [RewardController::class, 'apiSearch'])->name('rewards.api.search');
    Route::get('/rewards/statistics', [RewardController::class, 'statistics'])->name('rewards.statistics');
    Route::get('/rewards/create', [RewardController::class, 'create'])->name('rewards.create');
    Route::post('/rewards', [RewardController::class, 'store'])->name('rewards.store');
    Route::get('/rewards/{reward}', [RewardController::class, 'show'])->name('rewards.show');
    Route::get('/rewards/{reward}/edit', [RewardController::class, 'edit'])->name('rewards.edit');
    Route::put('/rewards/{reward}', [RewardController::class, 'update'])->name('rewards.update');
    Route::delete('/rewards/{reward}', [RewardController::class, 'destroy'])->name('rewards.destroy');
    Route::patch('/rewards/{reward}/toggle-active', [RewardController::class, 'toggleActive'])->name('rewards.toggle-active');

    // Redeems history
    Route::get('/redeems', [App\Http\Controllers\RedeemController::class, 'adminIndex'])->name('redeems');
    Route::get('/redeems/api/search', [App\Http\Controllers\RedeemController::class, 'apiSearch'])->name('redeems.api.search');
    Route::post('/redeems/{redeem}/status', [App\Http\Controllers\RedeemController::class, 'updateStatus'])->name('redeems.update-status');

    // สถิติเป้าหมายของผู้ใช้
    Route::get('/goals/statistics', [AdminController::class, 'goalStatistics'])->name('goals.statistics');

    // สถิติรางวัล
    Route::get('/rewards/statistics', [RewardController::class, 'statistics'])->name('rewards.statistics');

    // จัดการกิจกรรม
    // Place search routes before resource routes to ensure proper matching
    Route::get('/events/search/autocomplete', [AdminEventController::class, 'searchAutocomplete'])->name('events.search');
    Route::get('/events/search/test', function() {
        return response()->json([
            ['value' => 'Test Event 1', 'event_id' => 1],
            ['value' => 'Test Event 2', 'event_id' => 2]
        ]);
    })->name('events.search.test');

    // Regular resource routes
    Route::resource('events', AdminEventController::class);
    Route::post('/events/{event}/participants/{user}/status', [AdminEventController::class, 'updateParticipantStatus'])
        ->name('events.participants.status');
    Route::get('/events/{event}/export', [AdminEventController::class, 'exportParticipants'])->name('events.export');
    Route::get('/events/statistics', [AdminEventController::class, 'statistics'])->name('events.statistics');

    // Health Articles Management
    Route::get('/health-articles', [AdminHealthArticleController::class, 'index'])
        ->name('health-articles.index');
    Route::get('/health-articles/create', [AdminHealthArticleController::class, 'create'])
        ->name('health-articles.create');
    Route::post('/health-articles', [AdminHealthArticleController::class, 'store'])
        ->name('health-articles.store');
    Route::get('/health-articles/{id}', [AdminHealthArticleController::class, 'show'])
        ->name('health-articles.show');
    Route::get('/health-articles/{id}/edit', [AdminHealthArticleController::class, 'edit'])
        ->name('health-articles.edit');
    Route::put('/health-articles/{id}', [AdminHealthArticleController::class, 'update'])
        ->name('health-articles.update');
    Route::delete('/health-articles/{id}', [AdminHealthArticleController::class, 'destroy'])
        ->name('health-articles.destroy');
    Route::patch('/health-articles/{id}/toggle-published', [AdminHealthArticleController::class, 'togglePublished'])
        ->name('health-articles.toggle-published');

    // Health Articles Statistics
    Route::get('/health-articles-statistics', [AdminHealthArticleController::class, 'statistics'])
        ->name('health-articles.statistics');

    // Comments Management
    Route::get('/article-comments', [AdminHealthArticleController::class, 'manageComments'])
        ->name('health-articles.comments');
    Route::delete('/article-comments/{commentId}', [AdminHealthArticleController::class, 'deleteComment'])
        ->name('health-articles.delete-comment');

    // Article Categories Management
    Route::resource('article-categories', AdminHealthArticleController::class);

    // Run statistics routes
    Route::get('/run/stats', [App\Http\Controllers\Admin\RunStatisticsController::class, 'index'])->name('run.stats');
    Route::get('/run/statistics', [App\Http\Controllers\Admin\RunStatisticsController::class, 'locations'])->name('run.statistics');
    Route::get('/run/calendar', [App\Http\Controllers\Admin\RunStatisticsController::class, 'calendar'])->name('run.calendar');
    Route::get('/run/heatmap', [App\Http\Controllers\Admin\RunStatisticsController::class, 'heatmap'])->name('run.heatmap');
    Route::get('/run/heatmap/data', [App\Http\Controllers\Admin\RunStatisticsController::class, 'heatmapData'])->name('run.heatmap.data');
    Route::get('/run/export', [App\Http\Controllers\Admin\RunStatisticsController::class, 'exportData'])->name('run.export');
    Route::get('/run/user-stats/{userId}', [App\Http\Controllers\Admin\RunStatisticsController::class, 'userStats'])->name('user-run-stats');

    // ระบบการวิ่ง (Running System) สำหรับผู้ดูแลระบบ
    Route::prefix('run')->name('run.')->middleware(['auth'])->group(function () {
        Route::get('/', [App\Http\Controllers\RunController::class, 'index'])->name('index');
        Route::get('/history', [App\Http\Controllers\RunController::class, 'history'])->name('history');
        Route::get('/shared', [App\Http\Controllers\RunController::class, 'sharedWithMe'])->name('shared');
        Route::post('/destroy', [App\Http\Controllers\RunController::class, 'destroy'])->name('destroy');
        Route::post('/mark-as-viewed/{id}', [App\Http\Controllers\RunController::class, 'markAsViewed'])->name('mark-as-viewed');
        Route::post('/share', [App\Http\Controllers\RunController::class, 'share'])->name('share');

        // Test connectivity route
        Route::get('/test-start', function() {
            return response()->json([
                'status' => 'success',
                'message' => 'Test route works',
                'time' => now()->format('H:i:s')
            ]);
        })->name('test.start');

        // API endpoints for running
        Route::post('/start', [App\Http\Controllers\RunController::class, 'start'])->name('start');
        Route::post('/update-position', [App\Http\Controllers\RunController::class, 'updatePosition'])->name('update-position');
        Route::post('/toggle-pause', [App\Http\Controllers\RunActivityController::class, 'togglePause'])->name('toggle-pause');
        Route::post('/updateRoute', [App\Http\Controllers\RunActivityController::class, 'updateRoute'])->name('updateRoute');
        Route::post('/finish', [App\Http\Controllers\RunActivityController::class, 'finish'])->name('finish');
        Route::post('/store', [App\Http\Controllers\RunController::class, 'store'])->name('store');
    });

    // Sessions routes
    Route::get('/sessions', [App\Http\Controllers\SessionController::class, 'index'])->name('sessions.index');
    Route::delete('/sessions/{sessionId}', [App\Http\Controllers\SessionController::class, 'destroySpecificSession'])->name('sessions.destroy');
    Route::get('/sessions/clear-expired', [App\Http\Controllers\SessionController::class, 'clearExpiredSessions'])->name('sessions.clear-expired');
});

// Events routes
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [App\Http\Controllers\EventController::class, 'index'])->name('index');
    Route::get('/my-events', [App\Http\Controllers\EventController::class, 'myEvents'])
        ->middleware('auth')->name('my-events');
    Route::get('/{event}', [App\Http\Controllers\EventController::class, 'show'])->name('show');

    // Registration routes (ต้องเข้าสู่ระบบ)
    Route::middleware('auth')->group(function () {
        Route::post('/{event}/register', [App\Http\Controllers\EventController::class, 'register'])->name('register');
        Route::post('/{event}/cancel', [App\Http\Controllers\EventController::class, 'cancel'])->name('cancel');
    });
});

// Public health articles route for the welcome page
Route::get('/latest-health-articles', [PublicHealthArticleController::class, 'latestArticles'])
    ->name('public.latest-health-articles');

// Admin Report Routes
Route::prefix('admin/reports')->name('admin.reports.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
    Route::get('/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('users');
    Route::get('/monthly', [App\Http\Controllers\Admin\ReportController::class, 'monthly'])->name('monthly');
    Route::get('/yearly', [App\Http\Controllers\Admin\ReportController::class, 'yearly'])->name('yearly');
});

// Admin Health Article Routes
Route::group(['prefix' => 'admin/health-articles', 'as' => 'admin.health-articles.', 'middleware' => ['auth', 'admin']], function() {
    Route::get('/', [App\Http\Controllers\Admin\HealthArticleController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\HealthArticleController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\HealthArticleController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\Admin\HealthArticleController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\Admin\HealthArticleController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\Admin\HealthArticleController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\Admin\HealthArticleController::class, 'destroy'])->name('destroy');
    Route::post('/upload-image', [App\Http\Controllers\Admin\HealthArticleController::class, 'uploadImage'])->name('upload-image');
});

// Routes for Onboarding Tour
Route::prefix('tour')->middleware(['auth'])->group(function () {
    Route::get('/status', [App\Http\Controllers\OnboardingController::class, 'checkTourStatus'])
        ->name('tour.status');
    Route::post('/update', [App\Http\Controllers\OnboardingController::class, 'updateTourStatus'])
        ->name('tour.update');
    Route::post('/reset', [App\Http\Controllers\OnboardingController::class, 'resetAllTours'])
        ->name('tour.reset');
    Route::get('/settings', [App\Http\Controllers\OnboardingController::class, 'showTourSettings'])
        ->name('tour.settings');
    Route::get('/user-settings', [App\Http\Controllers\OnboardingController::class, 'getUserTourSettings'])
        ->name('tour.user-settings');
});

// Admin Routes for badges
Route::get('/admin/badges', [BadgeController::class, 'admin'])->name('admin.badges.index');
Route::get('/admin/badges/api/search', [BadgeController::class, 'apiSearch'])->name('admin.badges.api.search');
Route::get('/admin/badges/create', [BadgeController::class, 'create'])->name('admin.badges.create');
Route::get('/admin/badges/history/api/search', [BadgeController::class, 'apiSearchHistory'])->name('admin.badges.history.api.search');
