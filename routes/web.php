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
    Route::post('/rewards/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');

    // โปรไฟล์ผู้ใช้
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/image', [DashboardController::class, 'updateProfileImage'])->name('profile.update-image');
    Route::put('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.update-password');
    Route::put('/profile/health', [DashboardController::class, 'updateHealth'])->name('profile.update-health');
    Route::get('/profile/{username}', [DashboardController::class, 'show'])->name('profile.show');

    // ส่วนของผู้ใช้ - กิจกรรม
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::post('/events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.my');

    // Activity Routes
    Route::resource('activities', ActivityController::class);
    Route::post('/activities/{activity}/register', [ActivityController::class, 'register'])->name('activities.register');
    Route::delete('/activities/{activity}/cancel-registration', [ActivityController::class, 'cancelRegistration'])->name('activities.cancel-registration');
    Route::get('/my-activities', [ActivityController::class, 'myActivities'])->name('activities.my');
    Route::resource('goals', ActivityGoalController::class);
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

    // จัดการกิจกรรม
    Route::resource('events', AdminEventController::class);
    Route::post('/events/{event}/participants/{user}/status', [AdminEventController::class, 'updateParticipantStatus'])
        ->name('events.participants.status');
    Route::get('/events/{event}/export', [AdminEventController::class, 'exportParticipants'])
        ->name('events.export');
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
