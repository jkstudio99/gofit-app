<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Run;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BadgeController extends Controller
{
    /**
     * Display all badges and the user's earned badges
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Badge::query();

        // ล้างแคชของผู้ใช้ (ถ้ามี) ทุกครั้งที่เข้าหน้านี้
        if (class_exists('Illuminate\Support\Facades\Cache')) {
            Cache::forget('user_badges_' . $user->user_id);
            Cache::forget('user_stats_' . $user->user_id);
        }

        // Check if user has any activities
        $hasActivities = \App\Models\Run::where('user_id', $user->user_id)
            ->where('is_test', false)
            ->exists();

        // คำนวณใหม่ทุกครั้ง: รวมระยะทางที่ผู้ใช้วิ่งได้
        $totalDistance = \App\Models\Run::where('user_id', $user->user_id)
            ->where('is_test', false)
            ->sum('distance');

        // คำนวณใหม่ทุกครั้ง: รวมแคลอรี่ที่ผู้ใช้เผาผลาญได้
        $totalCalories = \App\Models\Run::where('user_id', $user->user_id)
            ->where('is_test', false)
            ->sum('calories_burned');

        // บันทึก log เพื่อการตรวจสอบ
        Log::info('User badge progress stats:', [
            'user_id' => $user->user_id,
            'total_distance' => $totalDistance,
            'total_calories' => $totalCalories,
            'has_activities' => $hasActivities
        ]);

        // Filter by badge type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        // Get badges with a custom property indicating if it's unlocked
        $badges = $query->get()->map(function ($badge) use ($user) {
            // Check if the user has earned this badge
            $isUnlocked = UserBadge::where('user_id', $user->user_id)
                ->where('badge_id', $badge->badge_id)
                ->exists();

            // เพิ่มคุณสมบัติแบบ dynamic ให้กับอ็อบเจกต์ Badge
            $badge->setAttribute('isUnlocked', $isUnlocked);

            // ใช้เมธอดจาก Model ในการคำนวณความคืบหน้า
            $progressPercent = $badge->calculateProgressPercentage();

            // ตรวจสอบว่าเป็นค่าที่ถูกต้อง
            if (is_nan($progressPercent) || is_infinite($progressPercent)) {
                $progressPercent = 0;
            }

            $badge->setAttribute('calculatedProgress', round($progressPercent, 1));

            return $badge;
        });

        // Calculate statistics
        $totalBadges = Badge::count();
        $unlockedCount = UserBadge::where('user_id', $user->user_id)->distinct('badge_id')->count();
        $lockedCount = $totalBadges - $unlockedCount;
        $progressPercentage = $totalBadges > 0 ? round(($unlockedCount / $totalBadges) * 100, 0) : 0;

        // Get badge types for filter options
        $badgeTypes = Badge::select('type')->distinct()->pluck('type');

        return view('badges.index', compact(
            'badges',
            'totalBadges',
            'unlockedCount',
            'lockedCount',
            'progressPercentage',
            'badgeTypes',
            'hasActivities',
            'totalDistance',
            'totalCalories'
        ));
    }

    /**
     * Display badges in admin panel with additional filtering and sorting capabilities
     */
    public function admin(Request $request)
    {
        $query = Badge::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('badge_name', 'like', "%{$search}%")
                  ->orWhere('badge_description', 'like', "%{$search}%");
            });
        }

        // Filter by badge type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        // Sort options
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = ['badge_name', 'created_at', 'type', 'criteria'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        $query->orderBy($sortField, $sortDirection);

        // Get badges with count of users who earned each badge
        $badges = $query->withCount('users')->paginate(50); // Increased to 50 to show all badges together

        // Badge types for filter dropdown
        $badgeTypes = Badge::select('type')->distinct()->pluck('type');

        // Calculate statistics for cards
        $totalUsers = DB::table('tb_user_badge')
            ->select('user_id')
            ->distinct()
            ->count();

        $recentBadges = DB::table('tb_user_badge')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->count();

        $badgeCount = Badge::count();
        $userBadgeCount = DB::table('tb_user_badge')->count();
        $unlockRate = $badgeCount > 0 && $totalUsers > 0
            ? round(($userBadgeCount / ($badgeCount * $totalUsers)) * 100)
            : 0;

        return view('admin.badges.index', compact(
            'badges',
            'badgeTypes',
            'sortField',
            'sortDirection',
            'totalUsers',
            'recentBadges',
            'unlockRate'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'badge_name' => 'required|string|max:255',
            'badge_description' => 'required|string',
            'badge_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|string',
            'criteria' => 'required|numeric',
            'points' => 'required|integer|min:0',
        ]);

        $badge = new Badge();
        $badge->badge_name = $request->badge_name;
        $badge->badge_desc = $request->badge_description;
        $badge->type = $request->type;
        $badge->criteria = $request->criteria;
        $badge->points = $request->points;

        if ($request->hasFile('badge_image')) {
            $path = $request->file('badge_image')->store('badges', 'public');
            $badge->badge_image = $path;
        }

        $badge->save();

        return redirect()->route('admin.badges.index')->with('success', 'สร้างเหรียญตราใหม่สำเร็จแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Badge $badge)
    {
        return view('admin.badges.show', compact('badge'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Badge $badge)
    {
        $request->validate([
            'badge_name' => 'required|string|max:255',
            'badge_description' => 'required|string',
            'badge_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|string',
            'criteria' => 'required|numeric',
            'points' => 'required|integer|min:0',
        ]);

        $badge->badge_name = $request->badge_name;
        $badge->badge_desc = $request->badge_description;
        $badge->type = $request->type;
        $badge->criteria = $request->criteria;
        $badge->points = $request->points;

        if ($request->hasFile('badge_image')) {
            // Delete old image if exists
            if ($badge->badge_image) {
                Storage::disk('public')->delete($badge->badge_image);
            }
            $path = $request->file('badge_image')->store('badges', 'public');
            $badge->badge_image = $path;
        }

        $badge->save();

        return redirect()->route('admin.badges.index')->with('success', 'อัปเดตเหรียญตราสำเร็จแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        // Delete badge image if exists
        if ($badge->badge_image) {
            Storage::disk('public')->delete($badge->badge_image);
        }

        // Delete user badges relations
        UserBadge::where('badge_id', $badge->badge_id)->delete();

        // Delete badge
        $badge->delete();

        return redirect()->route('admin.badges.index')->with('success', 'ลบเหรียญตราสำเร็จแล้ว');
    }

    /**
     * Show users who earned a specific badge
     */
    public function badgeUsers(Badge $badge)
    {
        $users = $badge->users()->paginate(15);
        return view('admin.badges.users', compact('badge', 'users'));
    }

    /**
     * Display badge statistics and analytics
     */
    public function statistics()
    {
        // Total badges count
        $totalBadges = Badge::count();

        // Total badge assignments
        $totalAssignments = DB::table('tb_user_badge')->count();

        // Average badges per user
        $usersWithBadges = DB::table('tb_user_badge')
            ->select('user_id')
            ->distinct()
            ->count();

        $averageBadgesPerUser = $usersWithBadges > 0
            ? round($totalAssignments / $usersWithBadges, 2)
            : 0;

        // Badge distribution by type
        $badgesByType = Badge::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function($item) {
                $label = '';
                switch($item->type) {
                    case 'distance':
                        $label = 'ระยะทาง';
                        break;
                    case 'calories':
                        $label = 'แคลอรี่';
                        break;
                    case 'streak':
                        $label = 'ต่อเนื่อง';
                        break;
                    case 'speed':
                        $label = 'ความเร็ว';
                        break;
                    case 'event':
                        $label = 'กิจกรรม';
                        break;
                    default:
                        $label = $item->type;
                }
                return [
                    'type' => $item->type,
                    'count' => $item->count,
                    'label' => $label
                ];
            });

        // Most earned badges
        $mostEarnedBadges = Badge::withCount('users')
            ->orderBy('users_count', 'desc')
            ->take(10)
            ->get();

        // Least earned badges
        $leastEarnedBadges = Badge::withCount('users')
            ->orderBy('users_count', 'asc')
            ->take(10)
            ->get();

        // Recent badge assignments
        $recentAssignments = DB::table('tb_user_badge')
            ->join('tb_user', 'tb_user_badge.user_id', '=', 'tb_user.user_id')
            ->join('tb_badge', 'tb_user_badge.badge_id', '=', 'tb_badge.badge_id')
            ->select('tb_user_badge.*', 'tb_user.username', 'tb_badge.badge_name')
            ->orderBy('tb_user_badge.created_at', 'desc')
            ->take(20)
            ->get();

        // Monthly badge trends (last 6 months)
        $badgeTrends = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->startOfMonth()->format('Y-m-d');
            $monthEnd = $date->endOfMonth()->format('Y-m-d');
            $monthName = $date->translatedFormat('F Y');

            $assignmentsCount = DB::table('tb_user_badge')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $badgeTrends->push([
                'month' => $monthName,
                'count' => $assignmentsCount
            ]);
        }

        // Users with most badges
        $topUsers = DB::table('tb_user_badge')
            ->join('tb_user', 'tb_user_badge.user_id', '=', 'tb_user.user_id')
            ->select('tb_user.user_id', 'tb_user.username', DB::raw('count(*) as badges_count'))
            ->groupBy('tb_user.user_id', 'tb_user.username')
            ->orderBy('badges_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.badges.statistics', compact(
            'totalBadges',
            'totalAssignments',
            'usersWithBadges',
            'averageBadgesPerUser',
            'badgesByType',
            'mostEarnedBadges',
            'leastEarnedBadges',
            'recentAssignments',
            'badgeTrends',
            'topUsers'
        ));
    }

    /**
     * ปลดล็อคเหรียญตราสำหรับผู้ใช้ปัจจุบัน
     * เมื่อปลดล็อคแล้วผู้ใช้จะได้รับคะแนน
     */
    public function unlockBadge(Request $request, $badgeId)
    {
        $user = Auth::user();
        $badge = Badge::findOrFail($badgeId);

        // ตรวจสอบว่าผู้ใช้ปลดล็อคเหรียญนี้ไปแล้วหรือไม่
        $alreadyUnlocked = UserBadge::where('user_id', $user->user_id)
            ->where('badge_id', $badge->badge_id)
            ->exists();

        if ($alreadyUnlocked) {
            return redirect()->back()->with('error', 'คุณได้ปลดล็อคเหรียญตรานี้ไปแล้ว');
        }

        // ตรวจสอบว่าผู้ใช้มีคุณสมบัติที่จะปลดล็อคหรือไม่
        if (!$badge->isEligibleToUnlock()) {
            return redirect()->back()->with('error', 'คุณยังไม่บรรลุเงื่อนไขในการปลดล็อคเหรียญตรานี้');
        }

        // เริ่มทำ Transaction
        DB::beginTransaction();

        try {
            // สร้างรายการปลดล็อคเหรียญตรา
            UserBadge::create([
                'user_id' => $user->user_id,
                'badge_id' => $badge->badge_id,
                'earned_at' => now()
            ]);

            // ใช้คะแนนจากฐานข้อมูลแทนการคำนวณ
            $pointsEarned = $badge->points ?? 100;

            // บันทึกประวัติการได้รับคะแนน
            DB::table('tb_point_history')->insert([
                'user_id' => $user->user_id,
                'points' => $pointsEarned,
                'description' => 'ได้รับเหรียญตรา: ' . $badge->badge_name,
                'source_type' => 'badge',
                'source_id' => $badge->badge_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // อัพเดตคะแนนของผู้ใช้
            DB::table('tb_user')
                ->where('user_id', $user->user_id)
                ->increment('points', $pointsEarned);

            DB::commit();

            // ใช้ session เพื่อบอก SweetAlert ให้แสดงการแจ้งเตือนความสำเร็จ
            session()->flash('badge_unlocked', [
                'badge_name' => $badge->badge_name,
                'points' => $pointsEarned,
                'image' => $badge->badge_image
            ]);

            return redirect()->back()->with('success', 'ยินดีด้วย! คุณได้ปลดล็อคเหรียญตรา "' . $badge->badge_name . '" และได้รับ ' . $pointsEarned . ' คะแนน');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Badge Unlock Error', [
                'user_id' => $user->user_id,
                'badge_id' => $badge->badge_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการปลดล็อคเหรียญตรา: ' . $e->getMessage());
        }
    }

    /**
     * คำนวณจำนวนวันที่วิ่งต่อเนื่อง
     */
    private function calculateStreak($userId)
    {
        $activities = Run::where('user_id', $userId)
            ->where('is_test', false)
            ->orderBy('start_time', 'desc')
            ->get()
            ->groupBy(function($activity) {
                return date('Y-m-d', strtotime($activity->start_time));
            });

        if ($activities->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = now()->startOfDay();
        $checkDate = clone $today;

        foreach ($activities as $date => $activitiesOnDate) {
            $activityDate = \Carbon\Carbon::parse($date)->startOfDay();

            // ตรวจสอบว่าเป็นวันที่ต่อเนื่องหรือไม่
            if ($checkDate->diffInDays($activityDate) <= 1) {
                $streak++;
                $checkDate = $activityDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * แสดงประวัติการได้รับเหรียญตรา
     */
    public function history()
    {
        $user = Auth::user();

        // ดึงข้อมูลเหรียญตราที่ผู้ใช้ได้รับ พร้อมเรียงตามวันที่ได้รับล่าสุด
        $badgeHistory = UserBadge::where('user_id', $user->user_id)
            ->join('tb_badge', 'tb_user_badge.badge_id', '=', 'tb_badge.badge_id')
            ->select('tb_user_badge.*', 'tb_badge.badge_name', 'tb_badge.badge_desc', 'tb_badge.badge_image', 'tb_badge.type', 'tb_badge.criteria')
            ->orderBy('tb_user_badge.earned_at', 'desc')
            ->get();

        // ดึงข้อมูลเกี่ยวกับคะแนนที่ได้รับจากเหรียญตรา
        $pointsHistory = \App\Models\PointHistory::where('user_id', $user->user_id)
            ->where('source_type', 'badge')
            ->get()
            ->keyBy('source_id');

        return view('badges.history', compact('badgeHistory', 'pointsHistory'));
    }

    /**
     * แสดงประวัติการได้รับเหรียญตราสำหรับแอดมิน (ดูทั้งหมดหรือเฉพาะ user)
     */
    public function adminHistory(Request $request)
    {
        // ตัวกรอง
        $query = UserBadge::query()
            ->join('tb_badge', 'tb_user_badge.badge_id', '=', 'tb_badge.badge_id')
            ->join('tb_user', 'tb_user_badge.user_id', '=', 'tb_user.user_id')
            ->select(
                'tb_user_badge.*',
                'tb_badge.badge_name',
                'tb_badge.badge_desc',
                'tb_badge.badge_image',
                'tb_badge.type',
                'tb_badge.criteria',
                'tb_user.username',
                'tb_user.firstname',
                'tb_user.lastname'
            );

        // กรองตาม user ถ้ามีการระบุ
        if ($request->has('user_id') && $request->user_id) {
            $query->where('tb_user_badge.user_id', $request->user_id);
        }

        // กรองตามประเภทเหรียญ
        if ($request->has('badge_type') && $request->badge_type) {
            $query->where('tb_badge.type', $request->badge_type);
        }

        // กรองตามช่วงเวลา
        if ($request->has('date_start') && $request->date_start) {
            $query->whereDate('tb_user_badge.earned_at', '>=', $request->date_start);
        }

        if ($request->has('date_end') && $request->date_end) {
            $query->whereDate('tb_user_badge.earned_at', '<=', $request->date_end);
        }

        // เรียงลำดับ
        $query->orderBy('tb_user_badge.earned_at', 'desc');

        // ดึงข้อมูลพร้อมแบ่งหน้า
        $badgeHistory = $query->paginate(15);

        // ดึงข้อมูลคะแนนที่ได้รับจากเหรียญตรา
        $badgeIds = $badgeHistory->pluck('badge_id')->toArray();
        $userIds = $badgeHistory->pluck('user_id')->toArray();

        $pointsHistory = \App\Models\PointHistory::whereIn('user_id', $userIds)
            ->where('source_type', 'badge')
            ->whereIn('source_id', $badgeIds)
            ->get()
            ->groupBy(function($item) {
                return $item->user_id . '_' . $item->source_id;
            });

        // ดึงผู้ใช้ทั้งหมดสำหรับตัวกรอง
        $users = \App\Models\User::orderBy('username')->get();

        // ดึงประเภทเหรียญสำหรับตัวกรอง
        $badgeTypes = Badge::select('type')->distinct()->get();

        // ---------- คำนวณสถิติจากฐานข้อมูลจริง ----------

        // จำนวนเหรียญทั้งหมดที่ถูกปลดล็อค (จากตาราง tb_user_badge)
        $totalBadges = UserBadge::count();

        // จำนวนผู้ใช้ที่ได้รับเหรียญ (ไม่ซ้ำ)
        $uniqueUsers = UserBadge::select('user_id')->distinct()->count();

        // จำนวนเหรียญที่ได้รับในเดือนนี้
        $currentMonth = now()->startOfMonth();
        $monthlyBadges = UserBadge::whereDate('earned_at', '>=', $currentMonth)->count();

        // คะแนนที่ได้รับรวมจากเหรียญทั้งหมด
        $totalPoints = \App\Models\PointHistory::where('source_type', 'badge')->sum('points');

        return view('admin.badges.history', compact(
            'badgeHistory',
            'pointsHistory',
            'users',
            'badgeTypes',
            'totalBadges',
            'uniqueUsers',
            'monthlyBadges',
            'totalPoints'
        ));
    }
}

