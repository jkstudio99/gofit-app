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

// หน้าหลัก
Route::get('/', function () {
    return view('welcome');
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

    // เหรียญตรา
    Route::get('/badges', [BadgeController::class, 'index'])->name('badges.index');

    // รางวัล
    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::get('/rewards/{reward}/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');

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
});

// Route สำหรับ Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // หน้าแดชบอร์ด
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
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

    // User activities
    Route::get('/user-activities', [AdminController::class, 'userActivities'])->name('user-activities');

    // Badges management - improved CRUD
    Route::get('/badges', [BadgeController::class, 'admin'])->name('badges.index');
    Route::get('/badges/statistics', [BadgeController::class, 'statistics'])->name('badges.statistics');
    Route::get('/badges/create', [BadgeController::class, 'create'])->name('badges.create');
    Route::post('/badges', [BadgeController::class, 'store'])->name('badges.store');
    Route::get('/badges/users/{badge}', [BadgeController::class, 'badgeUsers'])->name('badges.users');
    Route::get('/badges/{badge}', [BadgeController::class, 'show'])->name('badges.show');
    Route::get('/badges/{badge}/edit', [BadgeController::class, 'edit'])->name('badges.edit');
    Route::put('/badges/{badge}', [BadgeController::class, 'update'])->name('badges.update');
    Route::delete('/badges/{badge}', [BadgeController::class, 'destroy'])->name('badges.destroy');

    // Rewards management
    Route::get('/rewards', [RewardController::class, 'admin'])->name('rewards');
    Route::get('/rewards/statistics', [RewardController::class, 'statistics'])->name('rewards.statistics');
    Route::get('/rewards/create', [RewardController::class, 'create'])->name('rewards.create');
    Route::post('/rewards', [RewardController::class, 'store'])->name('rewards.store');
    Route::get('/rewards/{reward}', [RewardController::class, 'show'])->name('rewards.show');
    Route::get('/rewards/{reward}/edit', [RewardController::class, 'edit'])->name('rewards.edit');
    Route::put('/rewards/{reward}', [RewardController::class, 'update'])->name('rewards.update');
    Route::delete('/rewards/{reward}', [RewardController::class, 'destroy'])->name('rewards.destroy');

    // Redeems history
    Route::get('/redeems', [AdminController::class, 'redeems'])->name('redeems');

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
