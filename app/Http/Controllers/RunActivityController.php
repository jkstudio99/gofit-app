<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Badge;
use App\Models\UserBadge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RunActivityController extends Controller
{
    /**
     * Display the running interface
     */
    public function index(Request $request)
    {
        // Get user's recent runs
        $user = Auth::user();

        // กรองตามประเภท (จริง/ทดสอบ) ถ้ามีการระบุ
        $isTestFilter = $request->has('is_test') ? $request->boolean('is_test') : null;

        $query = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running');

        // กรองเฉพาะกิจกรรมจริงหรือทดสอบถ้ามีการระบุ
        if ($isTestFilter !== null) {
            $query->where('is_test', $isTestFilter);
        }

        $recentActivities = $query->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $testActivities = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->where('is_test', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $realActivities = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->where('is_test', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('run.index', compact('recentActivities', 'testActivities', 'realActivities', 'isTestFilter'));
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

        // Validate request
        $validated = $request->validate([
            'is_test' => 'boolean'
        ]);

        // Create new activity
        $activity = new Activity();
        $activity->user_id = $user->user_id;
        $activity->activity_type = 'running';
        $activity->start_time = Carbon::now();
        $activity->route_gps_data = json_encode([]);
        $activity->is_test = $request->input('is_test', false); // ค่าเริ่มต้นเป็น false (วิ่งจริง)
        $activity->save();

        return response()->json([
            'status' => 'success',
            'activity_id' => $activity->activity_id,
            'message' => $activity->is_test ? 'การทดสอบวิ่งเริ่มต้นเรียบร้อยแล้ว' : 'กิจกรรมการวิ่งเริ่มต้นเรียบร้อยแล้ว',
            'is_test' => $activity->is_test
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
            'is_test' => 'boolean'
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
        $activity->is_test = $request->input('is_test', false); // ค่าเริ่มต้นเป็น false (วิ่งจริง)
        $activity->save();

        // Check for badges - เฉพาะการวิ่งจริงเท่านั้น
        if (!$activity->is_test) {
            $this->checkForBadges($user, $activity);
        }

        return response()->json([
            'status' => 'success',
            'message' => $activity->is_test ? 'บันทึกการทดสอบวิ่งเรียบร้อยแล้ว' : 'กิจกรรมการวิ่งถูกบันทึกเรียบร้อยแล้ว',
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
        try {
            // ตรวจสอบว่าคลาส Badge มีอยู่และตารางสร้างแล้ว
            if (!class_exists('App\\Models\\Badge') || !Schema::hasTable('tb_badge') || !Schema::hasTable('tb_user_badge')) {
                Log::info('Badge system is not initialized yet.');
                return;
            }

            // ไม่นับแคลอรี่จากการวิ่งทดสอบ
            // ตรวจสอบแคลอรี่สะสมเพื่อรับเหรียญ
            $totalCalories = DB::table('tb_activity')
                ->where('user_id', $user->user_id)
                ->where('activity_type', 'running')
                ->where('is_test', false) // เฉพาะการวิ่งจริงเท่านั้น
                ->sum('calories_burned');

            // ค้นหาเหรียญที่เข้าเกณฑ์จากแคลอรี่สะสม
            $eligibleBadges = Badge::where('calories_required', '<=', $totalCalories)
                ->where('isenabled', 'Y')
                ->get();

            foreach ($eligibleBadges as $badge) {
                // ตรวจสอบว่าผู้ใช้มีเหรียญนี้หรือยัง
                $exists = DB::table('tb_user_badge')
                    ->where('user_id', $user->user_id)
                    ->where('badge_id', $badge->badge_id)
                    ->exists();

                if (!$exists) {
                    // สร้างเหรียญรางวัลใหม่ให้กับผู้ใช้
                    DB::table('tb_user_badge')->insert([
                        'user_id' => $user->user_id,
                        'badge_id' => $badge->badge_id,
                        'earned_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // บันทึกการแจ้งเตือนได้รับเหรียญรางวัล
                    Log::info('User earned a badge', [
                        'user_id' => $user->user_id,
                        'badge_id' => $badge->badge_id,
                        'badge_name' => $badge->name,
                        'total_calories' => $totalCalories
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in checkForBadges: ' . $e->getMessage());
            // ข้ามการออกเหรียญหากเกิดข้อผิดพลาด แต่ยังให้บันทึกกิจกรรมได้
        }
    }

    /**
     * Store new activity from web form
     */
    public function store(Request $request)
    {
        try {
            // ตรวจสอบข้อมูลที่ส่งมาว่าถูกต้อง
            $validatedData = $request->validate([
                'distance' => 'required|numeric',
                'calories_burned' => 'required|numeric',
                'average_speed' => 'required|numeric',
                'route_gps_data' => 'required',
                'activity_type' => 'required|string',
                'is_test' => 'boolean'
            ]);

            // Log ข้อมูลเพื่อการตรวจสอบ
            Log::info('Storing activity data', [
                'user_id' => auth()->id(),
                'data' => $validatedData
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // สร้างกิจกรรมใหม่
            $activity = new Activity();
            $activity->user_id = $user->user_id;
            $activity->activity_type = $request->activity_type;
            $activity->start_time = Carbon::now()->subMinutes(30); // สมมติว่าวิ่งมา 30 นาที
            $activity->end_time = Carbon::now();
            $activity->distance = $request->distance;
            $activity->calories_burned = $request->calories_burned;
            $activity->average_speed = $request->average_speed;
            $activity->is_test = $request->input('is_test', false); // ค่าเริ่มต้นเป็น false (วิ่งจริง)

            // ตรวจสอบและแปลงข้อมูล route_gps_data ถ้าจำเป็น
            if (is_string($request->route_gps_data)) {
                $activity->route_gps_data = $request->route_gps_data;
            } else {
                $activity->route_gps_data = json_encode($request->route_gps_data);
            }

            $activity->save();

            // ตรวจสอบเหรียญรางวัล - เฉพาะการวิ่งจริงเท่านั้น
            if (!$activity->is_test) {
                $this->checkForBadges($user, $activity);
            }

            return response()->json([
                'success' => true,
                'message' => $activity->is_test ? 'บันทึกการทดสอบวิ่งเรียบร้อยแล้ว' : 'กิจกรรมการวิ่งถูกบันทึกเรียบร้อยแล้ว',
                'activity' => $activity
            ]);
        } catch (\Exception $e) {
            // Log ข้อผิดพลาด
            Log::error('Error saving activity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the test running interface
     */
    public function test(Request $request)
    {
        // Get user's recent test runs
        $user = Auth::user();

        // ดึงเฉพาะการวิ่งทดสอบ
        $testActivities = Activity::where('user_id', $user->user_id)
            ->where('activity_type', 'running')
            ->where('is_test', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('run.test', compact('testActivities'));
    }
}
