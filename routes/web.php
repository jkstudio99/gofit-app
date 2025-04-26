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
    Route::get('/run', [RunActivityController::class, 'index'])->name('run.index');
    Route::post('/run/start', [RunActivityController::class, 'start'])->name('run.start');
    Route::post('/run/finish', [RunActivityController::class, 'finish'])->name('run.finish');
    Route::post('/run/updateRoute', [RunActivityController::class, 'updateRoute'])->name('run.updateRoute');

    // เหรียญตรา
    Route::get('/badges', [BadgeController::class, 'index'])->name('badges.index');

    // รางวัล
    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');

    // โปรไฟล์ผู้ใช้
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
});

// Route สำหรับ Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // หน้าแดชบอร์ด
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Users management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');

    // User activities
    Route::get('/user-activities', [AdminController::class, 'userActivities'])->name('user-activities');

    // Badges management
    Route::get('/badges', [AdminController::class, 'badges'])->name('badges.index');

    // Rewards management
    Route::get('/rewards', [AdminController::class, 'rewards'])->name('rewards.index');

    // Redeems history
    Route::get('/redeems', [AdminController::class, 'redeems'])->name('redeems');

    // ในระยะแรกจะมีแค่ dashboard ก่อน เราจะเพิ่ม controllers อื่นๆ ในภายหลัง
});
