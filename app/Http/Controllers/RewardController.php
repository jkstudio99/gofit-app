<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\Redeem;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RewardController extends Controller
{
    /**
     * Display the rewards page
     */
    public function index()
    {
        $user = Auth::user();
        $rewards = Reward::where('is_enabled', 1)
                  ->orderBy('points_required', 'asc')
                  ->get();

        $userPoints = $user->points;
        $redeemedRewards = Redeem::where('user_id', $user->id)->get();

        return view('rewards.index', compact('rewards', 'userPoints', 'redeemedRewards'));
    }

    /**
     * Redeem a reward
     */
    public function redeem(Reward $reward)
    {
        $user = Auth::user();

        // Check if user has enough points
        if ($user->points < $reward->points_required) {
            return redirect()->route('rewards.index')->with('error', 'คะแนนของคุณไม่เพียงพอที่จะแลกรางวัลนี้');
        }

        // Check if reward is available
        if ($reward->quantity <= 0) {
            return redirect()->route('rewards.index')->with('error', 'รางวัลนี้หมดแล้ว');
        }

        // Begin transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Create redeem record
            Redeem::create([
                'user_id' => $user->user_id,
                'reward_id' => $reward->reward_id,
                'status' => 'pending'
            ]);

            // Deduct points from user
            DB::table('tb_user')
                ->where('user_id', $user->user_id)
                ->decrement('points', $reward->points_required);

            // Decrease reward quantity
            DB::table('tb_reward')
                ->where('reward_id', $reward->reward_id)
                ->decrement('quantity', 1);

            DB::commit();
            return redirect()->route('rewards.index')->with('success', 'คุณได้แลกรางวัล ' . $reward->name . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('rewards.index')->with('error', 'เกิดข้อผิดพลาดในการแลกรางวัล โปรดลองอีกครั้ง: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.rewards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'points_required' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:0',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_enabled' => 'nullable',
        ]);

        $reward = new Reward();
        $reward->name = $request->name;
        $reward->description = $request->description;
        $reward->points_required = $request->points_required;
        $reward->quantity = $request->quantity;
        $reward->is_enabled = $request->has('is_enabled') ? 1 : 0;

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('rewards', 'public');
            $reward->image_path = $path;
        }

        $reward->save();

        return redirect()->route('admin.rewards')->with('success', 'เพิ่มรางวัลใหม่เรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reward $reward)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reward $reward)
    {
        return view('admin.rewards.edit', compact('reward'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reward $reward)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'points_required' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:0',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_enabled' => 'nullable',
        ]);

        $reward->name = $request->name;
        $reward->description = $request->description;
        $reward->points_required = $request->points_required;
        $reward->quantity = $request->quantity;
        $reward->is_enabled = $request->has('is_enabled') ? 1 : 0;

        // Handle image upload or removal
        if ($request->hasFile('image_path')) {
            // Delete old image if exists
            if ($reward->image_path) {
                Storage::disk('public')->delete($reward->image_path);
            }
            $path = $request->file('image_path')->store('rewards', 'public');
            $reward->image_path = $path;
        } elseif ($request->has('remove_image') && $request->remove_image) {
            // Remove existing image if checkbox is checked
            if ($reward->image_path) {
                Storage::disk('public')->delete($reward->image_path);
                $reward->image_path = null;
            }
        }

        $reward->save();

        return redirect()->route('admin.rewards')->with('success', 'อัปเดตรางวัลเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reward $reward)
    {
        // Check if reward has been redeemed
        $redeemedCount = Redeem::where('reward_id', $reward->reward_id)->count();

        if ($redeemedCount > 0) {
            return redirect()->route('admin.rewards')->with('error', 'ไม่สามารถลบรางวัลที่มีการแลกแล้วได้');
        }

        // Delete reward image if exists
        if ($reward->image_path) {
            Storage::disk('public')->delete($reward->image_path);
        }

        // Delete reward
        $reward->delete();

        return redirect()->route('admin.rewards')->with('success', 'ลบรางวัลเรียบร้อยแล้ว');
    }

    /**
     * Display admin rewards page
     */
    public function admin(Request $request)
    {
        $query = Reward::query();

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'enabled') {
                $query->where('is_enabled', 1);
            } elseif ($request->status === 'disabled') {
                $query->where('is_enabled', 0);
            }
        }

        // Filter by points
        if ($request->has('min_points') && !empty($request->min_points)) {
            $query->where('points_required', '>=', $request->min_points);
        }

        if ($request->has('max_points') && !empty($request->max_points)) {
            $query->where('points_required', '<=', $request->max_points);
        }

        // Filter by stock
        if ($request->has('stock') && !empty($request->stock)) {
            if ($request->stock === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock === 'low_stock') {
                $query->whereBetween('stock', [1, 10]);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock', 0);
            }
        }

        // Sorting
        if ($request->has('sort') && !empty($request->sort)) {
            switch ($request->sort) {
                case 'points-asc':
                    $query->orderBy('points_required', 'asc');
                    break;
                case 'points-desc':
                    $query->orderBy('points_required', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $rewards = $query->paginate(15);
        $rewards->appends($request->query());

        return view('admin.rewards.index', compact('rewards'));
    }

    /**
     * Display rewards statistics
     */
    public function statistics()
    {
        // รวมจำนวนรางวัลทั้งหมด
        $totalRewards = Reward::count();

        // รางวัลที่ยังมีของเหลืออยู่
        $availableRewards = Reward::where('stock', '>', 0)->count();

        // รางวัลที่หมดแล้ว
        $outOfStockRewards = Reward::where('stock', 0)->count();

        // จำนวนรางวัลที่มีการแลกทั้งหมด
        $totalRedeems = Redeem::count();

        // สถิติการแลกรางวัลรายเดือน (6 เดือนล่าสุด)
        $monthlyRedeems = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->startOfMonth()->format('Y-m-d');
            $monthEnd = $date->endOfMonth()->format('Y-m-d');
            $monthName = $date->translatedFormat('F Y');

            $count = Redeem::whereBetween('created_at', [$monthStart, $monthEnd])->count();

            $monthlyRedeems->push([
                'month' => $monthName,
                'count' => $count
            ]);
        }

        // รางวัลที่มีการแลกมากที่สุด
        $topRedeemed = Reward::withCount('redeems')
            ->orderBy('redeems_count', 'desc')
            ->take(5)
            ->get();

        // ผู้ใช้ที่แลกรางวัลมากที่สุด
        $topUsers = DB::table('tb_user')
            ->select('tb_user.user_id', 'tb_user.username', DB::raw('COUNT(tb_redeem.redeem_id) as redeem_count'))
            ->join('tb_redeem', 'tb_user.user_id', '=', 'tb_redeem.user_id')
            ->groupBy('tb_user.user_id', 'tb_user.username')
            ->orderBy('redeem_count', 'desc')
            ->take(5)
            ->get();

        // สถานะการแลกรางวัล
        $redeemStatuses = DB::table('tb_redeem')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
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
            'topRedeemed',
            'topUsers',
            'redeemStatuses',
            'recentRedeems'
        ));
    }
}

