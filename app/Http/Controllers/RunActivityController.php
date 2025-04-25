<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Badge;
use App\Models\UserBadge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RunActivityController extends Controller
{
    /**
     * Display the running interface
     */
    public function index()
    {
        // Get user's recent runs
        $user = Auth::user();
        $recentActivities = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('run.index', compact('recentActivities'));
    }

    /**
     * Start a new running activity
     */
    public function start(Request $request)
    {
        $user = Auth::user();

        // Check if user has any unfinished activities
        $unfinishedActivity = Activity::where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->first();

        if ($unfinishedActivity) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have an unfinished activity. Please finish it first.'
            ]);
        }

        // Create new activity
        $activity = new Activity();
        $activity->user_id = $user->user_id;
        $activity->activity_type = 'running';
        $activity->start_time = Carbon::now();
        $activity->route_gps_data = json_encode([]);
        $activity->save();

        return response()->json([
            'status' => 'success',
            'activity_id' => $activity->activity_id,
            'message' => 'Running activity started successfully'
        ]);
    }

    /**
     * Finish a running activity
     */
    public function finish(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:tb_activity,activity_id',
            'route_data' => 'required|json',
            'distance' => 'required|numeric',
            'duration' => 'required|numeric',
            'calories' => 'required|numeric',
            'average_speed' => 'required|numeric',
        ]);

        $user = Auth::user();
        $activity = Activity::where('activity_id', $request->activity_id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$activity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Activity not found'
            ]);
        }

        if (!is_null($activity->end_time)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Activity already finished'
            ]);
        }

        // Update activity details
        $activity->end_time = Carbon::now();
        $activity->distance = $request->distance;
        $activity->calories_burned = $request->calories;
        $activity->average_speed = $request->average_speed;
        $activity->route_gps_data = $request->route_data;
        $activity->save();

        // Check for badges
        $this->checkForBadges($user, $activity);

        return response()->json([
            'status' => 'success',
            'message' => 'Running activity finished successfully',
            'activity' => $activity
        ]);
    }

    /**
     * Update activity data during run
     */
    public function updateRoute(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:tb_activity,activity_id',
            'route_data' => 'required|json',
            'current_distance' => 'required|numeric',
        ]);

        $user = Auth::user();
        $activity = Activity::where('activity_id', $request->activity_id)
            ->where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->first();

        if (!$activity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Active running session not found'
            ]);
        }

        // Update route data
        $activity->route_gps_data = $request->route_data;
        $activity->distance = $request->current_distance;
        $activity->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Route updated'
        ]);
    }

    /**
     * Check for badges based on activity
     */
    private function checkForBadges($user, $activity)
    {
        // First-time runner badge
        $firstRunBadge = Badge::where('badge_criteria', 'first_run')->first();
        if ($firstRunBadge) {
            $userHasBadge = UserBadge::where('user_id', $user->user_id)
                ->where('badge_id', $firstRunBadge->badge_id)
                ->exists();

            if (!$userHasBadge) {
                UserBadge::create([
                    'user_id' => $user->user_id,
                    'badge_id' => $firstRunBadge->badge_id
                ]);
            }
        }

        // Distance-based badges
        $totalDistance = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->sum('distance');

        // Example badges: 5km, 10km, 50km, 100km
        $distanceBadges = Badge::where('badge_criteria', 'like', 'distance_%')->get();

        foreach ($distanceBadges as $badge) {
            $threshold = (int) str_replace('distance_', '', $badge->badge_criteria);

            if ($totalDistance >= $threshold) {
                $userHasBadge = UserBadge::where('user_id', $user->user_id)
                    ->where('badge_id', $badge->badge_id)
                    ->exists();

                if (!$userHasBadge) {
                    UserBadge::create([
                        'user_id' => $user->user_id,
                        'badge_id' => $badge->badge_id
                    ]);
                }
            }
        }
    }
}
