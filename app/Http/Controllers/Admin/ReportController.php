<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Run;
use App\Models\Badge;
use App\Models\Redeem;
use App\Models\HealthArticle;
use App\Models\ActivityGoal;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * แสดงหน้ารายงานและสถิติรวม
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * แสดงรายงานเกี่ยวกับผู้ใช้งาน
     */
    public function users()
    {
        $usersCount = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();

        $activeUsers = User::withCount('runs')
                        ->orderBy('runs_count', 'desc')
                        ->limit(10)
                        ->get();

        $usersByMonth = User::select(
                            DB::raw('MONTH(created_at) as month'),
                            DB::raw('YEAR(created_at) as year'),
                            DB::raw('COUNT(*) as count')
                        )
                        ->whereYear('created_at', now()->year)
                        ->groupBy('year', 'month')
                        ->orderBy('year')
                        ->orderBy('month')
                        ->get();

        return view('admin.reports.users', compact(
            'usersCount',
            'newUsersThisMonth',
            'activeUsers',
            'usersByMonth'
        ));
    }

    /**
     * แสดงรายงานประจำเดือน
     */
    public function monthly()
    {
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        $runActivities = Run::whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->count();

        $totalDistance = Run::whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->sum('distance');

        $totalCalories = Run::whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->sum('calories_burned');

        $badgesAwarded = Badge::whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->count();

        $rewards = Redeem::whereMonth('created_at', $currentMonth)
                        ->whereYear('created_at', $currentYear)
                        ->count();

        return view('admin.reports.monthly', compact(
            'runActivities',
            'totalDistance',
            'totalCalories',
            'badgesAwarded',
            'rewards',
            'currentMonth',
            'currentYear'
        ));
    }

    /**
     * แสดงรายงานประจำปี
     */
    public function yearly()
    {
        $currentYear = now()->format('Y');

        $usersCount = User::whereYear('created_at', $currentYear)->count();
        $activitiesCount = Run::whereYear('created_at', $currentYear)->count();

        $distanceByMonth = Run::select(
                                DB::raw('MONTH(created_at) as month'),
                                DB::raw('SUM(distance) as total_distance')
                            )
                            ->whereYear('created_at', $currentYear)
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();

        $caloriesByMonth = Run::select(
                                DB::raw('MONTH(created_at) as month'),
                                DB::raw('SUM(calories_burned) as total_calories')
                            )
                            ->whereYear('created_at', $currentYear)
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();

        // เพิ่มการนับจำนวนกิจกรรมรายเดือน
        $activitiesByMonth = Run::select(
                                DB::raw('MONTH(created_at) as month'),
                                DB::raw('COUNT(*) as count')
                            )
                            ->whereYear('created_at', $currentYear)
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();

        return view('admin.reports.yearly', compact(
            'usersCount',
            'activitiesCount',
            'distanceByMonth',
            'caloriesByMonth',
            'activitiesByMonth',
            'currentYear'
        ));
    }
}
