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
use Illuminate\Support\Facades\Validator;

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
                'message' => 'กิจกรรมการวิ่งเริ่มต้นไปแล้ว กรุณาจบกิจกรรมปัจจุบันก่อนเริ่มกิจกรรมใหม่',
                'activity_id' => $unfinishedActivity->activity_id
            ], 400);
        }

        // Check for old activities that might be stuck - activities older than 24 hours without end_time
        $stuckActivities = Activity::where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->where('start_time', '<', Carbon::now()->subHours(24))
            ->get();

        // Auto-close these stuck activities
        foreach ($stuckActivities as $stuckActivity) {
            $stuckActivity->end_time = Carbon::now();
            $stuckActivity->save();

            Log::info('Auto-closed stuck activity', [
                'user_id' => $user->user_id,
                'activity_id' => $stuckActivity->activity_id
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
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'activity_id' => 'required|exists:tb_activity,activity_id',
                'route_data' => 'required|string',
                'distance' => 'required|numeric',
                'duration' => 'required|numeric',
                'calories' => 'required|numeric',
                'average_speed' => 'required|numeric',
                'is_test' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first()
                ], 400);
            }

            // Log the incoming request for debugging
            Log::info('Finish activity request', [
                'user_id' => Auth::id(),
                'activity_id' => $request->activity_id,
                'request_data' => $request->all()
            ]);

            $user = Auth::user();
            $activity = Activity::where('activity_id', $request->activity_id)
                ->where('user_id', $user->user_id)
                ->first();

            if (!$activity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบกิจกรรมที่ระบุ'
                ], 404);
            }

            if (!is_null($activity->end_time)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'กิจกรรมนี้จบไปแล้ว'
                ], 400);
            }

            // ตรวจสอบว่าเป็นการจบกิจกรรมที่ค้างอยู่หรือไม่
            $isEmergencyFinish = ($request->distance == 0 && $request->duration == 0 && $request->calories == 0);

            // Update activity details
            $activity->end_time = Carbon::now();

            // ถ้าเป็นการจบกิจกรรมที่ค้างอยู่ และไม่มีข้อมูลเพียงพอ ให้บันทึกข้อมูลน้อยที่สุด
            if ($isEmergencyFinish) {
                // คำนวณระยะเวลาตั้งแต่เริ่มจนถึงตอนนี้
                $durationSeconds = $activity->start_time->diffInSeconds(Carbon::now());
                $activity->distance = 0;
                $activity->calories_burned = 0;
                $activity->average_speed = 0;
                $activity->route_gps_data = "[]";
                $activity->notes = "กิจกรรมถูกจบโดยอัตโนมัติเนื่องจากไม่มีการบันทึกข้อมูล";
            } else {
                // บันทึกข้อมูลปกติ
                $activity->distance = $request->distance;
                $activity->calories_burned = $request->calories;
                $activity->average_speed = $request->average_speed;

                // Ensure route_data is a valid JSON string
                try {
                    $routeData = $request->route_data;
                    // Check if the route_data is already a JSON string
                    json_decode($routeData);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // If not a valid JSON, encode it
                        $routeData = json_encode($routeData);
                    }
                    $activity->route_gps_data = $routeData;
                } catch (\Exception $e) {
                    // If there's an error, store empty route data
                    Log::error('Error parsing route data: ' . $e->getMessage());
                    $activity->route_gps_data = "[]";
                }

                $activity->is_test = $request->input('is_test', false); // ค่าเริ่มต้นเป็น false (วิ่งจริง)
            }

            $activity->save();

            // Check for badges - เฉพาะการวิ่งจริงเท่านั้น และไม่ใช่การจบแบบฉุกเฉิน
            if (!$activity->is_test && !$isEmergencyFinish) {
                try {
                    $this->checkForBadges($user, $activity);
                } catch (\Exception $e) {
                    // Just log badge checking errors, don't fail the whole request
                    Log::error('Error checking badges: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => $isEmergencyFinish ? 'จบกิจกรรมที่ค้างอยู่เรียบร้อยแล้ว' :
                    ($activity->is_test ? 'บันทึกการทดสอบวิ่งเรียบร้อยแล้ว' : 'กิจกรรมการวิ่งถูกบันทึกเรียบร้อยแล้ว'),
                'activity' => $activity
            ]);
        } catch (\Exception $e) {
            // Log the error for server-side debugging
            Log::error('Error in finish method: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            // Return a JSON response even when there's an error
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดภายในระบบ กรุณาลองใหม่อีกครั้ง: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Check if user has any active (unfinished) activities
     */
    public function checkActiveActivity()
    {
        $user = Auth::user();

        // Check if user has any unfinished activities
        $unfinishedActivity = Activity::where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->first();

        if ($unfinishedActivity) {
            return response()->json([
                'has_active' => true,
                'activity_id' => $unfinishedActivity->activity_id,
                'start_time' => $unfinishedActivity->start_time
            ]);
        }

        return response()->json([
            'has_active' => false
        ]);
    }
}
