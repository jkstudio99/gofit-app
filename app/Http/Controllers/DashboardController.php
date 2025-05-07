<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\UserBadge;
use App\Models\Badge;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Reward;
use App\Models\Run;
use App\Models\User;
use App\Models\Redeem;

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

        // เพิ่มการตรวจสอบผู้ใช้ใหม่
        $isNewUser = ($user->login_count <= 1);

        // ถ้าเป็นผู้ใช้ใหม่ เพิ่ม login_count
        if ($isNewUser) {
            DB::table('tb_user')
                ->where('user_id', $user->user_id)
                ->update([
                    'login_count' => DB::raw('login_count + 1'),
                    'updated_at' => now()
                ]);
        }

        // Get user's running stats
        $totalActivities = Run::where('user_id', $user->user_id)
            ->count();

        $totalDistance = Run::where('user_id', $user->user_id)
            ->sum('distance');

        $totalCalories = Run::where('user_id', $user->user_id)
            ->sum('calories_burned');

        // Get recent activities
        $recentActivities = Run::where('user_id', $user->user_id)
            ->orderBy('start_time', 'desc')
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

        $weeklyActivities = Run::where('user_id', $user->user_id)
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->get();

        $weeklyDistance = $weeklyActivities->sum('distance');
        $weeklyCalories = $weeklyActivities->sum('calories_burned');
        $weeklyRunCount = $weeklyActivities->count();

        // Get user's weekly distance goal from the database
        $weeklyGoal = 20; // Default fallback value
        $weeklyGoalObj = null;

        // ดึงเป้าหมายระยะทางรายสัปดาห์ล่าสุด (มีการเรียงลำดับตาม created_at)
        $weeklyGoalObj = \App\Models\ActivityGoal::where('user_id', $user->user_id)
            ->where('type', 'distance')
            ->where('period', 'weekly')
            ->where('is_completed', false)
            ->whereDate('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($weeklyGoalObj) {
            $weeklyGoal = $weeklyGoalObj->target_value;
        }

        // ดึงเป้าหมายทั้งหมดที่กำลังดำเนินการของผู้ใช้
        $activeGoals = \App\Models\ActivityGoal::where('user_id', $user->user_id)
            ->where('is_completed', false)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->startOfDay());
            })
            ->orderBy('created_at', 'desc') // เรียงตามเวลาที่สร้าง (ล่าสุดก่อน)
            ->take(3) // แสดงแค่ 3 อันล่าสุด
            ->get();

        // Calculate weekly goal progress
        $weeklyGoalProgress = 0;
        if ($weeklyGoal > 0) {
            $weeklyGoalProgress = min(100, ($weeklyDistance / $weeklyGoal) * 100);
        }

        // Generate weekly distance data for chart
        $weeklyDistanceChart = [];
        $daysOfWeek = [0, 1, 2, 3, 4, 5, 6]; // 0 = Sunday, 6 = Saturday

        foreach ($daysOfWeek as $day) {
            $dayOfWeek = Carbon::now()->startOfWeek()->addDays($day);
            $dayActivities = $weeklyActivities->filter(function ($activity) use ($dayOfWeek) {
                $activityDate = Carbon::parse($activity->start_time)->startOfDay();
                return $activityDate->isSameDay($dayOfWeek);
            });

            // Use numeric indexed array for ApexCharts
            $weeklyDistanceChart[] = round($dayActivities->sum('distance'), 1);
        }

        // Get count of events the user has registered for
        $userRegisteredEvents = \App\Models\EventUser::where('user_id', $user->user_id)
            ->where('status', 'registered')
            ->count();

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
            'weeklyGoalProgress',
            'weeklyDistanceChart',
            'isNewUser',
            'weeklyGoalObj',
            'userRegisteredEvents',
            'activeGoals',
            'weeklyRunCount'
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

        if (!$user) {
            return redirect()->route('login')->with('error', 'คุณต้องเข้าสู่ระบบก่อนเข้าถึงหน้านี้');
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * Display a user's public profile.
     */
    public function show($username)
    {
        $user = \App\Models\User::where('username', $username)->firstOrFail();

        // Load activity statistics
        $user->activities_count = $user->activities()->count();
        $user->total_distance = $user->activities()->sum('distance');
        $user->fitness_score = $user->points ?? 0;

        // Load recent activities
        $user->recent_activities = $user->activities()
            ->orderBy('started_at', 'desc')
            ->take(5)
            ->get();

        // Load badges
        $user->loadMissing('badges');

        return view('profile.show', compact('user'));
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

        // อัปเดตข้อมูลโดยใช้ DB::table เนื่องจากมี linter error กับ update method
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

    /**
     * Update the user's profile image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfileImage(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // อัปโหลดไฟล์และบันทึกไว้ใน public/profile_images
        if ($request->hasFile('profile_image')) {
            // สร้างโฟลเดอร์หากยังไม่มี
            $uploadPath = public_path('profile_images');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // ลบรูปเก่าหากมี
            if ($user->profile_image) {
                $oldImagePath = public_path('profile_images/' . $user->profile_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // บันทึกรูปใหม่
            $imageName = $user->user_id . '_' . time() . '.' . $request->profile_image->extension();
            $request->profile_image->move($uploadPath, $imageName);

            // อัปเดตฐานข้อมูล
            DB::table('tb_user')
                ->where('user_id', $user->user_id)
                ->update([
                    'profile_image' => $imageName,
                    'updated_at' => now()
                ]);
        }

        return redirect()->route('profile.edit')->with('success', 'รูปโปรไฟล์ได้รับการอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Update user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // ตรวจสอบรหัสผ่านปัจจุบัน
        if (!password_verify($validatedData['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง']);
        }

        // อัปเดตรหัสผ่านใหม่
        DB::table('tb_user')
            ->where('user_id', $user->user_id)
            ->update([
                'password' => bcrypt($validatedData['password']),
                'updated_at' => now()
            ]);

        return redirect()->route('profile.edit')->with('success', 'รหัสผ่านได้รับการอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Update user health information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateHealth(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'weight' => 'nullable|numeric|min:0|max:300',
            'height' => 'nullable|numeric|min:0|max:300',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
        ]);

        // อัปเดตข้อมูลสุขภาพ
        DB::table('tb_user')
            ->where('user_id', $user->user_id)
            ->update([
                'weight' => $validatedData['weight'],
                'height' => $validatedData['height'],
                'birthdate' => $validatedData['birthdate'],
                'gender' => $validatedData['gender'],
                'updated_at' => now()
            ]);

        return redirect()->route('profile.edit')->with('success', 'ข้อมูลสุขภาพได้รับการอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * แสดงหน้ายืนยันการลบบัญชีผู้ใช้
     */
    public function showDeleteAccountForm()
    {
        $user = Auth::user();
        return view('profile.delete-account', compact('user'));
    }

    /**
     * ลบบัญชีผู้ใช้
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        // ตรวจสอบรหัสผ่าน
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();
        $userId = $user->user_id;

        // ยืนยันรหัสผ่าน
        if (!password_verify($validated['password'], $user->password)) {
            return back()->withErrors(['password' => 'รหัสผ่านไม่ถูกต้อง']);
        }

        // ลบข้อมูลที่เกี่ยวข้อง
        DB::transaction(function () use ($userId) {
            // ลบข้อมูลกิจกรรมการวิ่ง
            DB::table('tb_run')->where('user_id', $userId)->delete();

            // ลบข้อมูลการลงทะเบียนกิจกรรม
            DB::table('event_users')->where('user_id', $userId)->delete();

            // ลบข้อมูลเป้าหมาย
            DB::table('tb_activity_goals')->where('user_id', $userId)->delete();

            // ลบความสัมพันธ์กับเหรียญรางวัล
            DB::table('tb_user_badge')->where('user_id', $userId)->delete();

            // ลบความสัมพันธ์กับบทบาท
            DB::table('tb_user_role')->where('user_id', $userId)->delete();

            // ลบการแลกรางวัล
            DB::table('tb_redeem')->where('user_id', $userId)->delete();

            // ลบบัญชีผู้ใช้
            DB::table('tb_user')->where('user_id', $userId)->delete();
        });

        // ออกจากระบบ
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'บัญชีของคุณถูกลบออกจากระบบเรียบร้อยแล้ว');
    }

    public function adminDashboard()
    {
        // Get total users
        $totalUsers = User::count();

        // ใช้จำนวน Run แทน Activity เนื่องจากข้อมูลถูกย้ายไปแล้ว
        $totalActivities = Run::count();

        // Get total runs
        $totalRuns = Run::count();

        // Get total badges
        $totalBadges = Badge::count();

        // Get total rewards
        $totalRewards = Reward::count();

        // Get new users this month
        $newUsers = User::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();

        // ใช้จำนวน Run แทน Activity เนื่องจากข้อมูลถูกย้ายไปแล้ว
        $monthlyActivities = Run::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();

        // Get monthly runs
        $monthlyRuns = Run::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();

        // Get monthly redeems
        $monthlyRedeems = Redeem::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();

        // Get top users
        $topUsers = User::select('tb_user.*', DB::raw('COUNT(tb_run.id) as activity_count'))
            ->leftJoin('tb_run', 'tb_user.user_id', '=', 'tb_run.user_id')
            ->groupBy('tb_user.user_id')
            ->orderBy('activity_count', 'desc')
            ->take(5)
            ->get();

        // Get latest activities (ใช้ Run แทน Activity)
        $latestActivities = Run::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get latest runs
        $latestRuns = Run::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get latest redeems
        $latestRedeems = Redeem::with(['user', 'reward'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get weekly activity data
        $activityLabels = [];
        $activityData = [];
        $runData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $activityLabels[] = $date->locale('th')->format('D d');

            // เนื่องจากมีการย้ายจาก tb_activity ไป tb_run
            // ให้นับกิจกรรม run แทน activity
            $activityCount = Run::whereDate('created_at', $date)->count();
            $activityData[] = $activityCount;

            $runCount = Run::whereDate('created_at', $date)->count();
            $runData[] = $runCount;
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalActivities',
            'totalRuns',
            'totalBadges',
            'totalRewards',
            'newUsers',
            'monthlyActivities',
            'monthlyRuns',
            'monthlyRedeems',
            'topUsers',
            'latestActivities',
            'latestRuns',
            'latestRedeems',
            'activityLabels',
            'activityData',
            'runData'
        ));
    }
}
