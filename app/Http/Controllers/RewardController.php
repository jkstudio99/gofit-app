<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\Redeem;
use App\Models\Activity;
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
            return redirect()->route('rewards.index')->with('error', 'Not enough points to redeem this reward');
        }

        // Check if reward is available
        if ($reward->quantity > 0) {
            // Create redeem record
            $redeem = new Redeem();
            $redeem->user_id = $user->id;
            $redeem->reward_id = $reward->reward_id;
            $redeem->points_used = $reward->points_required;
            $redeem->status = 'pending';
            $redeem->save();

            // Deduct points from user
            $user->points -= $reward->points_required;
            $user->save();

            // Decrease reward quantity
            $reward->quantity -= 1;
            $reward->save();

            return redirect()->route('rewards.index')->with('success', 'Reward redeemed successfully');
        }

        return redirect()->route('rewards.index')->with('error', 'This reward is out of stock');
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

        return redirect()->route('admin.rewards')->with('success', 'Reward created successfully');
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

        if ($request->hasFile('image_path')) {
            // Delete old image if exists
            if ($reward->image_path) {
                Storage::disk('public')->delete($reward->image_path);
            }
            $path = $request->file('image_path')->store('rewards', 'public');
            $reward->image_path = $path;
        }

        $reward->save();

        return redirect()->route('admin.rewards')->with('success', 'Reward updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reward $reward)
    {
        // Check if reward has been redeemed
        $redeemedCount = Redeem::where('reward_id', $reward->reward_id)->count();

        if ($redeemedCount > 0) {
            return redirect()->route('admin.rewards')->with('error', 'Cannot delete reward that has been redeemed');
        }

        // Delete reward image if exists
        if ($reward->image_path) {
            Storage::disk('public')->delete($reward->image_path);
        }

        // Delete reward
        $reward->delete();

        return redirect()->route('admin.rewards')->with('success', 'Reward deleted successfully');
    }

    public function admin()
    {
        $rewards = Reward::orderBy('created_at', 'desc')->get();
        return view('admin.rewards.index', compact('rewards'));
    }
}
