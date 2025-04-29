<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BadgeController extends Controller
{
    /**
     * Display all badges and the user's earned badges
     */
    public function index()
    {
        $user = Auth::user();
        $badges = Badge::all();
        $userBadges = UserBadge::where('user_id', $user->id)->get();

        return view('badges.index', compact('badges', 'userBadges'));
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
        $badges = $query->withCount('users')->paginate(10);

        // Badge types for filter dropdown
        $badgeTypes = Badge::select('type')->distinct()->pluck('type');

        return view('admin.badges.index', compact('badges', 'badgeTypes', 'sortField', 'sortDirection'));
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
            'requirement_type' => 'required|string',
            'requirement_value' => 'required|numeric',
        ]);

        $badge = new Badge();
        $badge->badge_name = $request->badge_name;
        $badge->badge_description = $request->badge_description;
        $badge->requirement_type = $request->requirement_type;
        $badge->requirement_value = $request->requirement_value;

        if ($request->hasFile('badge_image')) {
            $path = $request->file('badge_image')->store('badges', 'public');
            $badge->badge_image = $path;
        }

        $badge->save();

        return redirect()->route('admin.badges')->with('success', 'Badge created successfully');
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
            'requirement_type' => 'required|string',
            'requirement_value' => 'required|numeric',
        ]);

        $badge->badge_name = $request->badge_name;
        $badge->badge_description = $request->badge_description;
        $badge->requirement_type = $request->requirement_type;
        $badge->requirement_value = $request->requirement_value;

        if ($request->hasFile('badge_image')) {
            // Delete old image if exists
            if ($badge->badge_image) {
                Storage::disk('public')->delete($badge->badge_image);
            }
            $path = $request->file('badge_image')->store('badges', 'public');
            $badge->badge_image = $path;
        }

        $badge->save();

        return redirect()->route('admin.badges')->with('success', 'Badge updated successfully');
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

        return redirect()->route('admin.badges')->with('success', 'Badge deleted successfully');
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
}
