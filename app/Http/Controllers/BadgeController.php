<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    /**
     * Display all badges and the user's earned badges
     */
    public function index()
    {
        $user = Auth::user();

        // Get all badges
        $allBadges = Badge::all();

        // Get the user's earned badges
        $userBadgeIds = UserBadge::where('user_id', $user->user_id)
            ->pluck('badge_id')
            ->toArray();

        return view('badges.index', compact('allBadges', 'userBadgeIds'));
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
    public function show(Badge $badge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Badge $badge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        //
    }
}
