<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function admin()
    {
        $badges = Badge::all();
        return view('admin.badges.index', compact('badges'));
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
        //
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
}
