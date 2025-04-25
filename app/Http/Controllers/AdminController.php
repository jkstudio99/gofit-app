<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activity;
use App\Models\Badge;
use App\Models\Reward;
use App\Models\Redeem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // ต้องการสร้าง middleware ตรวจสอบบทบาทของผู้ใช้ว่าเป็น admin
        // $this->middleware('admin');
    }

    /**
     * แสดงหน้าแดชบอร์ดหลักของแอดมิน
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // จำนวนผู้ใช้งานทั้งหมด
        $totalUsers = User::count();

        // จำนวนผู้ใช้งานที่ลงทะเบียนในเดือนนี้
        $newUsers = User::whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->count();

        // จำนวนกิจกรรมทั้งหมด
        $totalActivities = Activity::count();

        // จำนวนกิจกรรมในเดือนนี้
        $monthlyActivities = Activity::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count();

        // จำนวนเหรียญตราทั้งหมด
        $totalBadges = Badge::count();

        // จำนวนรางวัลทั้งหมด
        $totalRewards = Reward::count();

        // จำนวนการแลกรางวัลทั้งหมด
        $totalRedeems = Redeem::count();

        // จำนวนการแลกรางวัลในเดือนนี้
        $monthlyRedeems = Redeem::whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->count();

        // ผู้ใช้งานที่มีกิจกรรมมากที่สุด 5 อันดับ
        $topUsers = User::select('tb_user.user_id', 'tb_user.firstname', 'tb_user.lastname', 'tb_user.username', DB::raw('COUNT(tb_activity.activity_id) as activity_count'))
                    ->leftJoin('tb_activity', 'tb_user.user_id', '=', 'tb_activity.user_id')
                    ->groupBy('tb_user.user_id', 'tb_user.firstname', 'tb_user.lastname', 'tb_user.username')
                    ->orderBy('activity_count', 'desc')
                    ->limit(5)
                    ->get();

        // กิจกรรมล่าสุด 10 รายการ
        $latestActivities = Activity::with('user')
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();

        // แลกรางวัลล่าสุด 10 รายการ
        $latestRedeems = Redeem::with(['user', 'reward'])
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();

        // สถิติกิจกรรมรายวันในสัปดาห์นี้
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $dailyStats = Activity::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get()
                        ->keyBy('date');

        $dailyActivities = [];
        $dailyLabels = [];

        for ($day = clone $startOfWeek; $day <= $endOfWeek; $day->addDay()) {
            $dateString = $day->format('Y-m-d');
            $dailyLabels[] = $day->format('D'); // วันในสัปดาห์ (Mon, Tue, etc.)
            $dailyActivities[] = $dailyStats->has($dateString) ? $dailyStats[$dateString]->count : 0;
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'newUsers',
            'totalActivities',
            'monthlyActivities',
            'totalBadges',
            'totalRewards',
            'totalRedeems',
            'monthlyRedeems',
            'topUsers',
            'latestActivities',
            'latestRedeems',
            'dailyLabels',
            'dailyActivities'
        ));
    }
}
