<?php

namespace App\Http\Controllers;

use App\Models\ActivityGoal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ActivityGoalController extends Controller
{
    /**
     * Display a listing of the user's goals.
     */
    public function index()
    {
        $activeGoals = ActivityGoal::where('user_id', Auth::id())
            ->where('is_completed', false)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderBy('end_date')
            ->get();

        $completedGoals = ActivityGoal::where('user_id', Auth::id())
            ->where('is_completed', true)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $expiredGoals = ActivityGoal::where('user_id', Auth::id())
            ->where('is_completed', false)
            ->where('end_date', '<', now())
            ->orderBy('end_date', 'desc')
            ->limit(10)
            ->get();

        return view('goals.index', compact('activeGoals', 'completedGoals', 'expiredGoals'));
    }

    /**
     * Show the form for creating a new goal.
     */
    public function create()
    {
        $goalTypes = [
            'distance' => 'Distance (km)',
            'duration' => 'Duration (minutes)',
            'calories' => 'Calories burned',
            'frequency' => 'Number of workouts'
        ];

        $activityTypes = [
            '' => 'Any activity',
            'run' => 'Running',
            'walk' => 'Walking',
            'cycle' => 'Cycling',
            'swim' => 'Swimming',
            'gym' => 'Gym Workout',
            'yoga' => 'Yoga',
            'hiit' => 'HIIT',
            'other' => 'Other'
        ];

        $periods = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'custom' => 'Custom period'
        ];

        return view('goals.create', compact('goalTypes', 'activityTypes', 'periods'));
    }

    /**
     * Store a newly created goal in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['distance', 'duration', 'calories', 'frequency'])],
            'activity_type' => ['nullable', 'string', Rule::in(['', 'run', 'walk', 'cycle', 'swim', 'gym', 'yoga', 'hiit', 'other'])],
            'target_value' => ['required', 'numeric', 'min:1'],
            'period' => ['required', 'string', Rule::in(['daily', 'weekly', 'monthly', 'custom'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // Set default end_date based on period if not custom
        if ($validated['period'] !== 'custom' && (!isset($validated['end_date']) || $validated['end_date'] === null)) {
            $startDate = Carbon::parse($validated['start_date']);

            switch ($validated['period']) {
                case 'daily':
                    $validated['end_date'] = $startDate->copy()->endOfDay();
                    break;
                case 'weekly':
                    $validated['end_date'] = $startDate->copy()->addWeek()->subDay();
                    break;
                case 'monthly':
                    $validated['end_date'] = $startDate->copy()->addMonth()->subDay();
                    break;
            }
        }

        $goal = Auth::user()->activityGoals()->create($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Fitness goal created successfully!');
    }

    /**
     * Display the specified goal.
     */
    public function show(ActivityGoal $goal)
    {
        $this->authorize('view', $goal);

        // Calculate progress percentage
        $progressPercentage = 0;
        if ($goal->target_value > 0) {
            $progressPercentage = min(100, round(($goal->current_value / $goal->target_value) * 100));
        }

        return view('goals.show', compact('goal', 'progressPercentage'));
    }

    /**
     * Show the form for editing the specified goal.
     */
    public function edit(ActivityGoal $goal)
    {
        $this->authorize('update', $goal);

        $goalTypes = [
            'distance' => 'Distance (km)',
            'duration' => 'Duration (minutes)',
            'calories' => 'Calories burned',
            'frequency' => 'Number of workouts'
        ];

        $activityTypes = [
            '' => 'Any activity',
            'run' => 'Running',
            'walk' => 'Walking',
            'cycle' => 'Cycling',
            'swim' => 'Swimming',
            'gym' => 'Gym Workout',
            'yoga' => 'Yoga',
            'hiit' => 'HIIT',
            'other' => 'Other'
        ];

        $periods = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'custom' => 'Custom period'
        ];

        return view('goals.edit', compact('goal', 'goalTypes', 'activityTypes', 'periods'));
    }

    /**
     * Update the specified goal in storage.
     */
    public function update(Request $request, ActivityGoal $goal)
    {
        $this->authorize('update', $goal);

        // Only allow updating if the goal is not completed
        if ($goal->is_completed) {
            return redirect()->route('goals.show', $goal)
                ->with('error', 'Completed goals cannot be modified.');
        }

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['distance', 'duration', 'calories', 'frequency'])],
            'activity_type' => ['nullable', 'string', Rule::in(['', 'run', 'walk', 'cycle', 'swim', 'gym', 'yoga', 'hiit', 'other'])],
            'target_value' => ['required', 'numeric', 'min:1'],
            'period' => ['required', 'string', Rule::in(['daily', 'weekly', 'monthly', 'custom'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // Set default end_date based on period if not custom
        if ($validated['period'] !== 'custom' && (!isset($validated['end_date']) || $validated['end_date'] === null)) {
            $startDate = Carbon::parse($validated['start_date']);

            switch ($validated['period']) {
                case 'daily':
                    $validated['end_date'] = $startDate->copy()->endOfDay();
                    break;
                case 'weekly':
                    $validated['end_date'] = $startDate->copy()->addWeek()->subDay();
                    break;
                case 'monthly':
                    $validated['end_date'] = $startDate->copy()->addMonth()->subDay();
                    break;
            }
        }

        // Check if the goal will be completed with the new target
        $validated['is_completed'] = $goal->current_value >= $validated['target_value'];

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Fitness goal updated successfully!');
    }

    /**
     * Remove the specified goal from storage.
     */
    public function destroy(ActivityGoal $goal)
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Fitness goal deleted successfully!');
    }
}
