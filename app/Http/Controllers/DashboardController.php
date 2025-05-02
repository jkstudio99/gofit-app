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
            ->where('activity_type', 'running')
            ->orderBy('start_time', 'desc')
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
            // ลบข้อมูลกิจกรรม
            DB::table('tb_activity')->where('user_id', $userId)->delete();

            // ลบข้อมูลการลงทะเบียนกิจกรรม
            DB::table('event_users')->where('user_id', $userId)->delete();

            // ลบข้อมูลเป้าหมาย
            DB::table('activity_goals')->where('user_id', $userId)->delete();

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

        // Get total activities
        $totalActivities = Activity::count();

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

        // Get monthly activities
        $monthlyActivities = Activity::whereMonth('created_at', date('m'))
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
        $topUsers = User::select('tb_user.*', DB::raw('COUNT(tb_activity.activity_id) as activity_count'))
            ->leftJoin('tb_activity', 'tb_user.user_id', '=', 'tb_activity.user_id')
            ->groupBy('tb_user.user_id')
            ->orderBy('activity_count', 'desc')
            ->take(5)
            ->get();

        // Get latest activities
        $latestActivities = Activity::with('user')
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

            $activityCount = Activity::whereDate('created_at', $date)->count();
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
