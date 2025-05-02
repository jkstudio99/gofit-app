<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Run;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RunStatsController extends Controller
{
    /**
     * แสดงหน้าสถิติการวิ่งรวม
     */
    public function index()
    {
        // สถิติการวิ่งรวมทั้งหมด
        $totalRuns = Run::count();
        $totalDistance = Run::sum('distance');
        $totalCalories = Run::sum('calories_burned');
        $totalDuration = Run::sum('duration');

        // แปลงวินาทีเป็นรูปแบบชั่วโมง:นาที
        $durationHours = floor($totalDuration / 3600);
        $durationMinutes = floor(($totalDuration % 3600) / 60);
        $formattedTotalDuration = "{$durationHours} ชม. {$durationMinutes} นาที";

        // ข้อมูลสำหรับกราฟแสดงการวิ่งต่อวัน (7 วันล่าสุด)
        $lastWeekStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = Run::whereDate('created_at', $date)->count();
            $lastWeekStats[] = [
                'date' => Carbon::now()->subDays($i)->format('d/m'),
                'count' => $count
            ];
        }

        // ผู้ใช้ที่วิ่งมากที่สุด 10 คน
        $topRunners = User::select('tb_user.user_id', 'tb_user.username', 'tb_user.firstname', 'tb_user.lastname', DB::raw('COUNT(tb_run.run_id) as run_count'), DB::raw('SUM(tb_run.distance) as total_distance'))
            ->join('tb_run', 'tb_user.user_id', '=', 'tb_run.user_id')
            ->groupBy('tb_user.user_id', 'tb_user.username', 'tb_user.firstname', 'tb_user.lastname')
            ->orderBy('total_distance', 'desc')
            ->take(10)
            ->get();

        // การวิ่งล่าสุด 10 รายการ
        $latestRuns = Run::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.run.stats', compact(
            'totalRuns',
            'totalDistance',
            'totalCalories',
            'formattedTotalDuration',
            'lastWeekStats',
            'topRunners',
            'latestRuns'
        ));
    }

    /**
     * แสดงสถิติการวิ่งของผู้ใช้แต่ละคน
     */
    public function userStats($id)
    {
        $user = User::findOrFail($id);

        // สถิติการวิ่งของผู้ใช้
        $userRuns = Run::where('user_id', $id)->get();
        $totalRuns = $userRuns->count();
        $totalDistance = $userRuns->sum('distance');
        $totalCalories = $userRuns->sum('calories_burned');
        $totalDuration = $userRuns->sum('duration');

        // แปลงวินาทีเป็นรูปแบบชั่วโมง:นาที
        $durationHours = floor($totalDuration / 3600);
        $durationMinutes = floor(($totalDuration % 3600) / 60);
        $formattedTotalDuration = "{$durationHours} ชม. {$durationMinutes} นาที";

        // ข้อมูลสำหรับกราฟแสดงการวิ่งต่อเดือน (12 เดือนล่าสุด)
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Run::where('user_id', $id)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $distance = Run::where('user_id', $id)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('distance');

            $monthlyStats[] = [
                'month' => $month->format('m/Y'),
                'count' => $count,
                'distance' => $distance
            ];
        }

        // การวิ่งล่าสุด 10 รายการ
        $latestRuns = Run::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.run.user-stats', compact(
            'user',
            'totalRuns',
            'totalDistance',
            'totalCalories',
            'formattedTotalDuration',
            'monthlyStats',
            'latestRuns'
        ));
    }
}
