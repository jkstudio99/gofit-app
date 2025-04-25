<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\Redeem;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RewardController extends Controller
{
    /**
     * Display the rewards page
     */
    public function index()
    {
        $user = Auth::user();

        // Calculate user points based on total distance
        $totalDistance = Activity::where('user_id', $user->user_id)
            ->whereNotNull('end_time')
            ->sum('distance');

        // Let's say 1 km = 1 point
        $userPoints = floor($totalDistance);

        // Get all available rewards
        $rewards = Reward::orderBy('points_required', 'asc')->get();

        // Get user's redeemed rewards
        $redeemedRewards = Redeem::where('user_id', $user->user_id)
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rewards.index', compact('rewards', 'redeemedRewards', 'userPoints'));
    }

    /**
     * Redeem a reward
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:tb_reward,reward_id'
        ]);

        $user = Auth::user();
        $reward = Reward::findOrFail($request->reward_id);

        // Calculate user points
        $totalDistance = Activity::where('user_id', $user->user_id)
            ->whereNotNull('end_time')
            ->sum('distance');

        $userPoints = floor($totalDistance);

        // Check if user has enough points
        if ($userPoints < $reward->points_required) {
            return redirect()->back()->with('error', 'Not enough points to redeem this reward');
        }

        // Check if reward is available (quantity)
        if ($reward->quantity <= 0) {
            return redirect()->back()->with('error', 'This reward is out of stock');
        }

        DB::beginTransaction();

        try {
            // Create redemption record
            $redeem = new Redeem();
            $redeem->user_id = $user->user_id;
            $redeem->reward_id = $reward->reward_id;
            $redeem->points_used = $reward->points_required;
            $redeem->status = 'pending';
            $redeem->save();

            // Reduce reward quantity
            $reward->quantity -= 1;
            $reward->save();

            DB::commit();

            return redirect()->back()->with('success', 'Reward redeemed successfully! It will be processed shortly.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reward $reward)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reward $reward)
    {
        //
    }
}
