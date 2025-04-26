<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\UserBadge;
use App\Models\Badge;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

        // ตรวจสอบว่าเป็น admin หรือไม่ (user_type_id = 2)
        if ($user->user_type_id == 2) {
            return redirect()->route('admin.dashboard');
        }

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
            ->take(5)
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

        // แสดงผลโดยใช้ AdminLTE template
        return view('dashboard', compact(
            'totalActivities',
            'totalDistance',
            'totalCalories',
            'recentActivities',
            'badges',
            'weeklyDistance',
            'weeklyCalories',
            'weeklyGoal',
            'weeklyGoalProgress'
        ));
    }

    /**
     * Show the user profile page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:tb_user,email,'.$user->user_id.',user_id',
            'telephone' => 'nullable|string|max:10',
        ]);

        // อัปเดตข้อมูลโดยใช้ DB::table
        DB::table('tb_user')
            ->where('user_id', $user->user_id)
            ->update([
                'firstname' => $validatedData['firstname'],
                'lastname' => $validatedData['lastname'],
                'email' => $validatedData['email'],
                'telephone' => $validatedData['telephone'],
                'updated_at' => now()
            ]);

        return redirect()->route('profile.edit')->with('success', 'ข้อมูลส่วนตัวได้รับการอัปเดตเรียบร้อยแล้ว');
    }
}
