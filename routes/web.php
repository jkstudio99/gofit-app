<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
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

// Route สำหรับผู้ใช้ทั่วไป
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

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
});

// Route สำหรับ Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // หน้าแดชบอร์ด
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // ในระยะแรกจะมีแค่ dashboard ก่อน เราจะเพิ่ม controllers อื่นๆ ในภายหลัง
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
