<?php

namespace App\Http\Controllers;

use App\Models\ActivityGoal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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
                    ->orWhere('end_date', '>=', now()->startOfDay());
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
            ->where('end_date', '<', now()->startOfDay())
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
            'distance' => 'ระยะทาง (กม.)',
            'duration' => 'ระยะเวลา (นาที)',
            'calories' => 'แคลอรี่ที่เผาผลาญ',
            'frequency' => 'จำนวนครั้งการออกกำลังกาย'
        ];

        $activityTypes = [
            '' => 'เลือกกิจกรรม',
            'running_health' => 'วิ่งเพื่อสุขภาพ',
            'running_marathon' => 'วิ่งมาราธอน',
            'running_mini' => 'วิ่งมินิมาราธอน',
            'running_trail' => 'วิ่งเทรล',
            'running_training' => 'วิ่งฝึกซ้อม',
            'running_event' => 'วิ่งงานอีเวนต์',
            'running_other' => 'วิ่งอื่นๆ (ระบุเอง)'
        ];

        $periods = [
            'daily' => 'รายวัน',
            'weekly' => 'รายสัปดาห์',
            'monthly' => 'รายเดือน',
            'custom' => 'กำหนดเอง'
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
            'activity_type' => ['required', 'string', Rule::in(['', 'running_health', 'running_marathon', 'running_mini', 'running_trail', 'running_training', 'running_event', 'running_other'])],
            'activity_type_other' => ['nullable', 'string', 'max:100', 'required_if:activity_type,running_other'],
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
                    // กำหนดเวลาสิ้นสุดของวันให้ชัดเจน (23:59:59)
                    $validated['end_date'] = $startDate->copy()->setTime(23, 59, 59);
                    break;
                case 'weekly':
                    $validated['end_date'] = $startDate->copy()->addWeek()->subDay();
                    break;
                case 'monthly':
                    $validated['end_date'] = $startDate->copy()->addMonth()->subDay();
                    break;
            }
        }

        $goal = ActivityGoal::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'activity_type' => $validated['activity_type'],
            'activity_type_other' => $validated['activity_type_other'],
            'target_value' => $validated['target_value'],
            'period' => $validated['period'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'เพิ่มเป้าหมายการออกกำลังกายสำเร็จ!');
    }

    /**
     * Display the specified goal.
     */
    public function show(ActivityGoal $goal)
    {
        // ตรวจสอบสิทธิ์โดยตรงแทนการใช้ authorize
        if (Auth::id() !== $goal->user_id) {
            abort(403, 'ไม่มีสิทธิ์ในการดูเป้าหมายนี้');
        }

        // Calculate progress percentage
        $progressPercentage = 0;
        if ($goal->target_value > 0) {
            $progressPercentage = min(100, round(($goal->current_value / $goal->target_value) * 100));
        }

        // Get activity contributions for this goal
        // This would typically come from a related model that tracks contributions
        // For now, we'll create an empty collection to prevent the undefined variable error
        $contributions = collect();

        // If you have a real GoalContribution model, you would use something like:
        // $contributions = GoalContribution::where('goal_id', $goal->id)
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);

        return view('goals.show', compact('goal', 'progressPercentage', 'contributions'));
    }

    /**
     * Show the form for editing the specified goal.
     */
    public function edit(ActivityGoal $goal)
    {
        // ตรวจสอบสิทธิ์โดยตรงแทนการใช้ authorize
        if (Auth::id() !== $goal->user_id) {
            abort(403, 'ไม่มีสิทธิ์ในการแก้ไขเป้าหมายนี้');
        }

        $goalTypes = [
            'distance' => 'ระยะทาง (กม.)',
            'duration' => 'ระยะเวลา (นาที)',
            'calories' => 'แคลอรี่ที่เผาผลาญ',
            'frequency' => 'จำนวนครั้งการออกกำลังกาย'
        ];

        $activityTypes = [
            '' => 'เลือกกิจกรรม',
            'running_health' => 'วิ่งเพื่อสุขภาพ',
            'running_marathon' => 'วิ่งมาราธอน',
            'running_mini' => 'วิ่งมินิมาราธอน',
            'running_trail' => 'วิ่งเทรล',
            'running_training' => 'วิ่งฝึกซ้อม',
            'running_event' => 'วิ่งงานอีเวนต์',
            'running_other' => 'วิ่งอื่นๆ (ระบุเอง)'
        ];

        $periods = [
            'daily' => 'รายวัน',
            'weekly' => 'รายสัปดาห์',
            'monthly' => 'รายเดือน',
            'custom' => 'กำหนดเอง'
        ];

        return view('goals.edit', compact('goal', 'goalTypes', 'activityTypes', 'periods'));
    }

    /**
     * Update the specified goal in storage.
     */
    public function update(Request $request, ActivityGoal $goal)
    {
        // ตรวจสอบสิทธิ์โดยตรงแทนการใช้ authorize
        if (Auth::id() !== $goal->user_id) {
            abort(403, 'ไม่มีสิทธิ์ในการแก้ไขเป้าหมายนี้');
        }

        // Only allow updating if the goal is not completed
        if ($goal->is_completed) {
            return redirect()->route('goals.show', $goal)
                ->with('error', 'Completed goals cannot be modified.');
        }

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['distance', 'duration', 'calories', 'frequency'])],
            'activity_type' => ['required', 'string', Rule::in(['', 'running_health', 'running_marathon', 'running_mini', 'running_trail', 'running_training', 'running_event', 'running_other'])],
            'activity_type_other' => ['nullable', 'string', 'max:100', 'required_if:activity_type,running_other'],
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
                    // กำหนดเวลาสิ้นสุดของวันให้ชัดเจน (23:59:59)
                    $validated['end_date'] = $startDate->copy()->setTime(23, 59, 59);
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
            ->with('success', 'อัปเดตเป้าหมายการออกกำลังกายสำเร็จแล้ว!');
    }

    /**
     * Remove the specified goal from storage.
     */
    public function destroy($id)
    {
        $goal = ActivityGoal::findOrFail($id);

        // Check if the goal belongs to the authenticated user
        if (Auth::id() !== $goal->user_id) {
            abort(403, 'ไม่มีสิทธิ์ในการลบเป้าหมายนี้');
        }

        // Delete the goal
        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'เป้าหมายถูกลบเรียบร้อยแล้ว');
    }
}
