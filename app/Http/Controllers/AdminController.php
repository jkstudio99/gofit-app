<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use App\Models\Reward;
use App\Models\Redeem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityGoal;
use App\Models\Event;
use App\Http\Controllers\DashboardController;

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
        $this->middleware('admin');
    }

    /**
     * แสดงหน้าแดชบอร์ดหลักของแอดมิน
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        \Illuminate\Support\Facades\Log::info('Admin dashboard is being accessed', [
            'user_id' => auth()->id(),
            'user_type_id' => auth()->user()->user_type_id,
            'route' => request()->path()
        ]);

        // จำนวนผู้ใช้งานทั้งหมด
        $totalUsers = User::count();

        // จำนวนผู้ใช้งานที่ลงทะเบียนในเดือนนี้
        $newUsers = User::whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->count();

        // จำนวนกิจกรรมทั้งหมด (ใช้ Run แทน Activity)
        $totalActivities = \App\Models\Run::count();

        // จำนวนกิจกรรมในเดือนนี้ (ใช้ Run แทน Activity)
        $monthlyActivities = \App\Models\Run::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count();

        // จำนวนการวิ่งทั้งหมด
        $totalRuns = \App\Models\Run::count();

        // จำนวนการวิ่งในเดือนนี้
        $monthlyRuns = \App\Models\Run::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count();

        // จำนวนเหรียญตราทั้งหมด
        $totalBadges = Badge::count();

        // จำนวนบทความทั้งหมด
        $totalArticles = \App\Models\HealthArticle::count();

        // จำนวนรางวัลทั้งหมด
        $totalRewards = Reward::count();

        // จำนวนการแลกรางวัลทั้งหมด
        $totalRedeems = Redeem::count();

        // จำนวนการแลกรางวัลในเดือนนี้
        $monthlyRedeems = Redeem::whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->count();

        // ผู้ใช้งานที่มีกิจกรรมมากที่สุด 5 อันดับ
        $topUsers = User::select('tb_user.user_id', 'tb_user.firstname', 'tb_user.lastname', 'tb_user.username', DB::raw('COUNT(tb_run.run_id) as activity_count'))
                    ->leftJoin('tb_run', 'tb_user.user_id', '=', 'tb_run.user_id')
                    ->groupBy('tb_user.user_id', 'tb_user.firstname', 'tb_user.lastname', 'tb_user.username')
                    ->orderBy('activity_count', 'desc')
                    ->limit(5)
                    ->get();

        // กิจกรรมล่าสุด 10 รายการ (ใช้ Run แทน Activity)
        $latestActivities = \App\Models\Run::with('user')
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();

        // แลกรางวัลล่าสุด 10 รายการ
        $latestRedeems = Redeem::with(['user', 'reward'])
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();

        // การวิ่งล่าสุด 10 รายการ
        $latestRuns = \App\Models\Run::with('user')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();

        // สถิติกิจกรรมรายวันในสัปดาห์นี้
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $dailyStats = \App\Models\Run::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get()
                        ->keyBy('date');

        $dailyActivities = [];
        $dailyLabels = [];

        for ($day = clone $startOfWeek; $day <= $endOfWeek; $day->addDay()) {
            $dateString = $day->format('Y-m-d');
            $dailyLabels[] = $day->translatedFormat('D'); // วันในสัปดาห์ในภาษาไทย (จ, อ, พ, etc.)
            $dailyActivities[] = $dailyStats->has($dateString) ? $dailyStats[$dateString]->count : 0;
        }

        // สร้างตัวแปรสำหรับใช้ในกราฟ
        $activityLabels = $dailyLabels;
        $activityData = $dailyActivities;
        $runData = [];

        // สถิติการวิ่งรายวันในสัปดาห์นี้
        $runStats = \App\Models\Run::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->keyBy('date');

        for ($day = clone $startOfWeek; $day <= $endOfWeek; $day->addDay()) {
            $dateString = $day->format('Y-m-d');
            $runData[] = $runStats->has($dateString) ? $runStats[$dateString]->count : 0;
        }

        // สถิติรายเดือน (แบ่งเป็น 4 สัปดาห์)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $activityDataMonth = [0, 0, 0, 0];
        $runDataMonth = [0, 0, 0, 0];

        // คำนวณสัปดาห์ทั้ง 4 สัปดาห์ในเดือน
        $weeks = [];
        $currentDay = clone $startOfMonth;
        $weekNumber = 0;

        while ($currentDay <= $endOfMonth) {
            $weekStartDay = clone $currentDay;
            $weekEndDay = (clone $currentDay)->endOfWeek();

            // ถ้าสัปดาห์นี้เกินเดือนปัจจุบัน ให้เป็นวันสุดท้ายของเดือน
            if ($weekEndDay > $endOfMonth) {
                $weekEndDay = clone $endOfMonth;
            }

            $weeks[] = [
                'start' => $weekStartDay,
                'end' => $weekEndDay,
                'number' => ++$weekNumber
            ];

            // ข้ามไปวันแรกของสัปดาห์ถัดไป
            $currentDay = (clone $weekEndDay)->addDay();
        }

        // นับจำนวนกิจกรรมในแต่ละสัปดาห์
        foreach ($weeks as $index => $week) {
            if ($index < 4) { // จำกัดที่ 4 สัปดาห์
                $activityCount = \App\Models\Run::whereBetween('created_at', [$week['start'], $week['end']])
                                       ->count();
                $runCount = \App\Models\Run::whereBetween('created_at', [$week['start'], $week['end']])
                                         ->count();

                $activityDataMonth[$index] = $activityCount;
                $runDataMonth[$index] = $runCount;
            }
        }

        // สถิติรายปี (12 เดือน)
        $activityDataYear = [];
        $runDataYear = [];
        $now = Carbon::now();

        for ($i = 0; $i < 12; $i++) {
            $month = (clone $now)->subMonths(11 - $i);

            $activityCount = \App\Models\Run::whereYear('created_at', $month->year)
                                   ->whereMonth('created_at', $month->month)
                                   ->count();

            $runCount = \App\Models\Run::whereYear('created_at', $month->year)
                                     ->whereMonth('created_at', $month->month)
                                     ->count();

            $activityDataYear[] = $activityCount;
            $runDataYear[] = $runCount;
        }

        // แสดงผลโดยใช้ AdminLTE template
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
            'dailyActivities',
            'totalRuns',
            'monthlyRuns',
            'totalArticles',
            'latestRuns',
            'activityLabels',
            'activityData',
            'runData',
            'activityDataMonth',
            'runDataMonth',
            'activityDataYear',
            'runDataYear'
        ));
    }

    /**
     * แสดงหน้าแดชบอร์ดผู้ดูแลระบบ
     */
    public function dashboard()
    {
        return app(DashboardController::class)->adminDashboard();
    }

    /**
     * แสดงรายการผู้ใช้ทั้งหมด
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function users(Request $request)
    {
        $query = User::query()->with(['userType', 'userStatus']);

        // ค้นหาตามชื่อหรืออีเมล
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            });
        }

        // กรองตามสถานะ
        if ($request->has('status') && $request->status != 'all') {
            $query->where('user_status_id', $request->status);
        }

        // กรองตามประเภทผู้ใช้
        if ($request->has('type') && $request->type != 'all') {
            $query->where('user_type_id', $request->type);
        }

        $users = $query->orderBy('user_id', 'asc')->paginate(20);
        $userTypes = \App\Models\MasterUserType::all();
        $userStatuses = \App\Models\MasterUserStatus::all();

        return view('admin.users.index', compact('users', 'userTypes', 'userStatuses'));
    }

    /**
     * แสดงฟอร์มสร้างผู้ใช้งานใหม่
     */
    public function createUser()
    {
        $userTypes = \App\Models\MasterUserType::all();
        $userStatuses = \App\Models\MasterUserStatus::all();

        return view('admin.users.create', compact('userTypes', 'userStatuses'));
    }

    /**
     * บันทึกผู้ใช้งานใหม่
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:191|unique:tb_user,username',
            'email' => 'required|string|email|max:191|unique:tb_user,email',
            'password' => 'required|string|min:8|confirmed',
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'telephone' => 'nullable|string|max:10',
            'user_type_id' => 'required|exists:tb_master_user_type,user_type_id',
            'user_status_id' => 'required|exists:tb_master_user_status,user_status_id',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'สร้างผู้ใช้งานใหม่สำเร็จแล้ว');
    }

    /**
     * แสดงรายละเอียดผู้ใช้งาน
     */
    public function showUser(User $user)
    {
        // โหลดข้อมูลที่เกี่ยวข้อง
        $user->load(['userType', 'userStatus', 'badges', 'eventRegistrations']);

        // ข้อมูลของเหรียญตราที่ได้รับ
        $badges = $user->badges;

        // สร้างคอลเลคชั่นว่างสำหรับกิจกรรมและอีเวนต์เพื่อแก้ปัญหา undefined variable
        $runningActivities = collect();
        $events = collect();
        $rewards = collect();
        $goals = collect();

        return view('admin.users.show', compact(
            'user',
            'badges',
            'runningActivities',
            'events',
            'rewards',
            'goals'
        ));
    }

    /**
     * แสดงฟอร์มแก้ไขผู้ใช้งาน
     */
    public function editUser(User $user)
    {
        $userTypes = \App\Models\MasterUserType::all();
        $userStatuses = \App\Models\MasterUserStatus::all();

        return view('admin.users.edit', compact('user', 'userTypes', 'userStatuses'));
    }

    /**
     * อัพเดทข้อมูลผู้ใช้งาน
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:191|unique:tb_user,email,'.$user->user_id.',user_id',
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'telephone' => 'nullable|string|max:10',
            'user_type_id' => 'required|exists:tb_master_user_type,user_type_id',
            'user_status_id' => 'required|exists:tb_master_user_status,user_status_id',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'อัพเดทข้อมูลผู้ใช้สำเร็จแล้ว');
    }

    /**
     * ลบผู้ใช้งาน
     */
    public function destroyUser(User $user)
    {
        // ตรวจสอบว่าไม่ใช่การลบตัวเอง
        if ($user->user_id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        // ลบข้อมูลที่เกี่ยวข้อง (หรือใช้ cascading delete ในฐานข้อมูล)
        DB::transaction(function () use ($user) {
            // ลบข้อมูลกิจกรรมและการลงทะเบียน
            $user->activities()->delete();
            $user->eventRegistrations()->delete();
            $user->activityGoals()->delete();

            // ลบความสัมพันธ์ many-to-many
            $user->badges()->detach();
            $user->roles()->detach();

            // ลบผู้ใช้
            $user->delete();
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'ลบผู้ใช้งานสำเร็จแล้ว');
    }

    /**
     * รีเซ็ตรหัสผ่านของผู้ใช้
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($validated['password'])
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'รีเซ็ตรหัสผ่านสำเร็จแล้ว');
    }

    /**
     * API endpoint สำหรับค้นหาผู้ใช้แบบ AJAX (Live Search)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiUsers(Request $request)
    {
        $query = User::query()->with(['userType', 'userStatus']);

        // ค้นหาตามชื่อหรืออีเมล
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            });
        }

        // กรองตามสถานะ
        if ($request->has('status') && $request->status != 'all') {
            $query->where('user_status_id', $request->status);
        }

        // กรองตามประเภทผู้ใช้
        if ($request->has('type') && $request->type != 'all') {
            $query->where('user_type_id', $request->type);
        }

        $users = $query->orderBy('user_id', 'asc')->paginate(20);

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    /**
     * แสดงฟอร์มรีเซ็ตรหัสผ่าน
     */
    public function showResetPasswordForm(User $user)
    {
        return view('admin.users.reset-password', compact('user'));
    }

    /**
     * อัพเดทรูปโปรไฟล์ของผู้ใช้
     */
    public function updateProfileImage(Request $request, $userId)
    {
        // ตรวจสอบว่ามีผู้ใช้นี้หรือไม่
        $user = User::findOrFail($userId);

        // ตรวจสอบไฟล์รูปภาพ
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ], [
            'profile_image.required' => 'กรุณาเลือกรูปภาพ',
            'profile_image.image' => 'ไฟล์ที่อัพโหลดต้องเป็นรูปภาพเท่านั้น',
            'profile_image.mimes' => 'รูปภาพต้องเป็นประเภท: jpeg, png, jpg, gif หรือ webp',
            'profile_image.max' => 'ขนาดไฟล์ต้องไม่เกิน 5MB',
        ]);

        try {
            // เตรียม path ที่จะเก็บไฟล์
            $uploadPath = public_path('profile_images');

            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists($uploadPath)) {
                if (!mkdir($uploadPath, 0777, true)) {
                    throw new \Exception('ไม่สามารถสร้างโฟลเดอร์ได้');
                }
            }

            // ลบรูปเก่า (ถ้ามี)
            if ($user->profile_image && file_exists($uploadPath . '/' . $user->profile_image)) {
                @unlink($uploadPath . '/' . $user->profile_image);
            }

            // เตรียมชื่อไฟล์ใหม่
            $image = $request->file('profile_image');
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '_' . str_replace(' ', '_', $user->username) . '.' . $extension;

            // อัพโหลดไฟล์
            if ($image->move($uploadPath, $imageName)) {
                // อัพเดทข้อมูลในฐานข้อมูล
                $user->profile_image = $imageName;
                $user->save();

                return redirect()->route('admin.users.show', $user)
                    ->with('success', 'อัปโหลดรูปภาพสำเร็จแล้ว');
            } else {
                throw new \Exception('ไม่สามารถอัพโหลดไฟล์ได้');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Profile image upload error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('error', 'เกิดข้อผิดพลาดในการอัพเดทรูปโปรไฟล์: ' . $e->getMessage());
        }
    }

    /**
     * แสดงรายการกิจกรรมของผู้ใช้ทั้งหมด
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function userActivities()
    {
        $activities = \App\Models\Run::with('user')
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
        return view('admin.activities.index', compact('activities'));
    }

    /**
     * แสดงรายการเหรียญตราทั้งหมด
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function badges()
    {
        $badges = Badge::orderBy('created_at', 'desc')->paginate(50); // Increased to 50 to show all badges together
        return view('admin.badges.index', compact('badges'));
    }

    /**
     * แสดงรายการรางวัลทั้งหมด
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function rewards()
    {
        $rewards = Reward::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.rewards.index', compact('rewards'));
    }

    /**
     * แสดงรายการการแลกรางวัลทั้งหมด
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function redeems()
    {
        $redeems = Redeem::with(['user', 'reward'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
        return view('admin.redeems.index', compact('redeems'));
    }

    /**
     * แสดงสถิติการตั้งเป้าหมายของผู้ใช้
     */
    public function goalStatistics()
    {
        // จำนวนเป้าหมายทั้งหมด
        $totalGoals = ActivityGoal::count();

        // จำนวนเป้าหมายที่สำเร็จ
        $completedGoals = ActivityGoal::where('status', 'completed')->count();

        // จำนวนเป้าหมายที่กำลังดำเนินการ
        $inProgressGoals = ActivityGoal::where('status', 'in_progress')->count();

        // อัตราความสำเร็จ
        $completionRate = $totalGoals > 0 ? round(($completedGoals / $totalGoals) * 100) : 0;

        // ประเภทเป้าหมายที่นิยม
        $goalTypes = DB::table('activity_goals')
            ->select('goal_type', DB::raw('count(*) as count'))
            ->groupBy('goal_type')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function($item) {
                $label = '';
                switch($item->goal_type) {
                    case 'distance':
                        $label = 'ระยะทาง';
                        break;
                    case 'time':
                        $label = 'เวลา';
                        break;
                    case 'frequency':
                        $label = 'ความถี่';
                        break;
                    default:
                        $label = $item->goal_type;
                }
                return [
                    'type' => $item->goal_type,
                    'count' => $item->count,
                    'label' => $label
                ];
            });

        // แนวโน้มเป้าหมายรายเดือน (6 เดือนล่าสุด)
        $goalTrends = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->startOfMonth()->format('Y-m-d');
            $monthEnd = $date->endOfMonth()->format('Y-m-d');
            $monthName = $date->translatedFormat('F Y');

            $created = ActivityGoal::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $completed = ActivityGoal::where('status', 'completed')
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();

            $goalTrends->push([
                'month' => $monthName,
                'created' => $created,
                'completed' => $completed
            ]);
        }

        // ผู้ใช้ที่มีเป้าหมายมากที่สุด
        $topUsers = User::withCount(['activityGoals as goals_count', 'activityGoals as completed_goals_count' => function($query) {
                $query->where('status', 'completed');
            }])
            ->having('goals_count', '>', 0)
            ->orderBy('goals_count', 'desc')
            ->take(10)
            ->get();

        // เป้าหมายล่าสุด
        $latestGoals = ActivityGoal::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.goals.statistics', compact(
            'totalGoals',
            'completedGoals',
            'inProgressGoals',
            'completionRate',
            'goalTypes',
            'goalTrends',
            'topUsers',
            'latestGoals'
        ));
    }

    /**
     * แสดงสถิติรางวัลของระบบ
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function rewardStatistics()
    {
        // จำนวนรางวัลทั้งหมด
        $totalRewards = Reward::count();

        // จำนวนรางวัลที่มีของเหลืออยู่
        $availableRewards = Reward::where('quantity', '>', 0)->count();

        // จำนวนรางวัลที่หมดแล้ว
        $outOfStockRewards = Reward::where('quantity', 0)->count();

        // จำนวนการแลกรางวัลทั้งหมด
        $totalRedeems = Redeem::count();

        // แนวโน้มการแลกรางวัลรายเดือน (6 เดือนล่าสุด)
        $monthlyRedeems = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->startOfMonth()->format('Y-m-d');
            $monthEnd = $date->endOfMonth()->format('Y-m-d');
            $monthName = $date->translatedFormat('F Y');

            $count = Redeem::whereBetween('created_at', [$monthStart, $monthEnd])->count();

            $monthlyRedeems->push([
                'month' => $monthName,
                'count' => $count,
            ]);
        }

        // สถานะการแลกรางวัล
        $redeemStatuses = DB::table('tb_redeem')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();

        // รางวัลที่ได้รับความนิยมสูงสุด (แลกมากที่สุด)
        $topRedeemed = Reward::withCount('redeems')
            ->orderBy('redeems_count', 'desc')
            ->take(10)
            ->get();

        // ผู้ใช้ที่แลกรางวัลบ่อยที่สุด
        $topUsers = DB::table('tb_redeem')
            ->join('tb_user', 'tb_redeem.user_id', '=', 'tb_user.user_id')
            ->select('tb_user.user_id', 'tb_user.username', DB::raw('count(*) as redeem_count'))
            ->groupBy('tb_user.user_id', 'tb_user.username')
            ->orderBy('redeem_count', 'desc')
            ->take(10)
            ->get();

        // การแลกรางวัลล่าสุด
        $recentRedeems = Redeem::with(['user', 'reward'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.rewards.statistics', compact(
            'totalRewards',
            'availableRewards',
            'outOfStockRewards',
            'totalRedeems',
            'monthlyRedeems',
            'redeemStatuses',
            'topRedeemed',
            'topUsers',
            'recentRedeems'
        ));
    }
}
