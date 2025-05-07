<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\UserBadge;
use App\Models\Badge;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's running stats
        $totalActivities = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->count();

        $totalDistance = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->sum('distance');

        $totalCalories = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->sum('calories_burned');

        // Get recent activities
        $recentActivities = Activity::where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        // Get badges earned
        $badges = Badge::whereIn('badge_id', function($query) use ($user) {
            $query->select('badge_id')
                ->from('tb_user_badge')
                ->where('user_id', $user->user_id);
        })->get();

        // Get this week's activities
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $weeklyActivities = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->get();

        $weeklyDistance = $weeklyActivities->sum('distance');
        $weeklyCalories = $weeklyActivities->sum('calories_burned');

        // Calculate weekly goal progress (example goal: 20km per week)
        $weeklyGoal = 20; // km
        $weeklyGoalProgress = ($weeklyDistance / $weeklyGoal) * 100;
        if ($weeklyGoalProgress > 100) {
            $weeklyGoalProgress = 100;
        }

        // Get event data
        $userRegisteredEvents = \App\Models\EventUser::where('user_id', $user->user_id)
            ->where('status', 'registered')
            ->count();

        return view('home', compact(
            'totalActivities',
            'totalDistance',
            'totalCalories',
            'recentActivities',
            'badges',
            'weeklyDistance',
            'weeklyCalories',
            'weeklyGoal',
            'weeklyGoalProgress',
            'userRegisteredEvents'
        ));
    }
}
