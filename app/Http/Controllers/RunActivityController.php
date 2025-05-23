<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Run;
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

        $query = Run::where('user_id', $user->user_id);

        // กรองเฉพาะกิจกรรมจริงหรือทดสอบถ้ามีการระบุ
        if ($isTestFilter !== null) {
            $query->where('is_test', $isTestFilter);
        }

        $recentActivities = $query->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $testActivities = Run::where('user_id', $user->user_id)
            ->where('is_test', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $realActivities = Run::where('user_id', $user->user_id)
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
        $unfinishedActivity = Run::where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->first();

        if ($unfinishedActivity) {
            return response()->json([
                'status' => 'error',
                'message' => 'กิจกรรมการวิ่งเริ่มต้นไปแล้ว กรุณาจบกิจกรรมปัจจุบันก่อนเริ่มกิจกรรมใหม่',
                'activity_id' => $unfinishedActivity->run_id
            ], 400);
        }

        // Check for old activities that might be stuck - activities older than 24 hours without end_time
        $stuckActivities = Run::where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->where('start_time', '<', Carbon::now()->subHours(24))
            ->get();

        // Auto-close these stuck activities
        foreach ($stuckActivities as $stuckActivity) {
            $stuckActivity->end_time = Carbon::now();
            $stuckActivity->save();

            Log::info('Auto-closed stuck activity', [
                'user_id' => $user->user_id,
                'activity_id' => $stuckActivity->run_id
            ]);
        }

        // Validate request
        $validated = $request->validate([
            'is_test' => 'boolean'
        ]);

        // Create new activity
        $activity = new Run();
        $activity->user_id = $user->user_id;
        $activity->start_time = Carbon::now();
        $activity->route_data = json_encode([]);
        $activity->is_test = $request->input('is_test', false); // ค่าเริ่มต้นเป็น false (วิ่งจริง)
        $activity->save();

        return response()->json([
            'status' => 'success',
            'activity_id' => $activity->run_id,
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
                'activity_id' => 'required|exists:tb_run,run_id',
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
            $activity = Run::where('run_id', $request->activity_id)
                ->where('user_id', $user->user_id)
                ->first();

            if (!$activity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบกิจกรรมที่ระบุ'
                ], 404);
            }

            // ตรวจสอบว่ากิจกรรมนี้ถูกบันทึกไปแล้วหรือไม่
            if (!is_null($activity->end_time)) {
                // แทนที่จะส่งข้อผิดพลาด ให้ส่งกลับข้อมูลเดิมพร้อมแจ้งว่าบันทึกแล้ว
                return response()->json([
                    'status' => 'success',
                    'message' => 'กิจกรรมนี้ถูกบันทึกไปแล้ว',
                    'activity' => $activity
                ]);
            }

            // ตรวจสอบว่ามีการบันทึกซ้ำซ้อนหรือไม่ (ตรวจจากเวลาเริ่มที่เหมือนกัน)
            $duplicateActivity = Run::where('user_id', $user->user_id)
                ->where('start_time', $activity->start_time)
                ->where('run_id', '!=', $activity->run_id)
                ->whereNotNull('end_time')
                ->first();

            if ($duplicateActivity) {
                Log::warning('พบการบันทึกซ้ำซ้อน', [
                    'user_id' => $user->user_id,
                    'original_activity_id' => $duplicateActivity->run_id,
                    'current_activity_id' => $activity->run_id
                ]);

                // บันทึกข้อมูลเพื่อไม่ให้เกิด error แต่แจ้งผู้ใช้ว่ามีการบันทึกซ้ำ
                $activity->end_time = Carbon::now();
                $activity->distance = $request->distance;
                $activity->duration = $request->duration;
                $activity->calories_burned = $request->calories;
                $activity->average_speed = $request->average_speed;
                $activity->route_data = $request->route_data;
                $activity->notes = "พบการบันทึกซ้ำซ้อนกับกิจกรรม ID: " . $duplicateActivity->run_id;
                $activity->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'ตรวจพบการบันทึกซ้ำซ้อน',
                    'activity' => $activity,
                    'is_duplicate' => true
                ]);
            }

            // คำนวณระยะเวลาใหม่
            $calculatedDuration = 0;

            // ตรวจสอบว่าเป็นการจบกิจกรรมที่ค้างอยู่หรือไม่
            $isEmergencyFinish = ($request->distance == 0 && $request->duration == 0 && $request->calories == 0);

            // Update activity details
            $activity->end_time = Carbon::now();

            // ถ้าเป็นการจบกิจกรรมที่ค้างอยู่ และไม่มีข้อมูลเพียงพอ ให้บันทึกข้อมูลน้อยที่สุด
            if ($isEmergencyFinish) {
                // คำนวณระยะเวลาตั้งแต่เริ่มจนถึงตอนนี้
                if (!empty($activity->start_time)) {
                    try {
                        $startTime = $activity->start_time instanceof Carbon
                            ? $activity->start_time
                            : Carbon::parse($activity->start_time);

                        $calculatedDuration = $startTime->diffInSeconds(Carbon::now());
                    } catch (\Exception $e) {
                        // บันทึกข้อผิดพลาดแต่ไม่หยุดการทำงาน
                        Log::error('Error calculating duration: ' . $e->getMessage());
                        $calculatedDuration = 0;
                    }
                }

                $activity->distance = 0;
                $activity->duration = $calculatedDuration;
                $activity->calories_burned = 0;
                $activity->average_speed = 0;
                $activity->route_data = "[]";
                $activity->notes = "กิจกรรมถูกจบโดยอัตโนมัติเนื่องจากไม่มีการบันทึกข้อมูล";

                Log::info('บันทึกการวิ่งฉุกเฉิน: เวลาที่ใช้ = ' . $calculatedDuration . ' วินาที');
            } else {
                // บันทึกข้อมูลปกติ
                $activity->distance = $request->distance;
                $activity->duration = $request->duration > 0 ? $request->duration : 0;
                $activity->calories_burned = $request->calories;
                $activity->average_speed = $request->average_speed;

                Log::info('บันทึกข้อมูลการวิ่ง: เวลาที่ใช้ = ' . $request->duration . ' วินาที');

                // Ensure route_data is a valid JSON string
                try {
                    $routeData = $request->route_data;
                    // Check if the route_data is already a JSON string
                    json_decode($routeData);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // If not a valid JSON, encode it
                        $routeData = json_encode($routeData);
                    }
                    $activity->route_data = $routeData;
                } catch (\Exception $e) {
                    // If there's an error, store empty route data
                    Log::error('Error parsing route data: ' . $e->getMessage());
                    $activity->route_data = "[]";
                }

                $activity->is_test = $request->input('is_test', false); // ค่าเริ่มต้นเป็น false (วิ่งจริง)
            }

            $activity->save();

            // Check for badges - เฉพาะการวิ่งจริงเท่านั้น และไม่ใช่การจบแบบฉุกเฉิน
            if (!$activity->is_test && !$isEmergencyFinish) {
                try {
                    $this->checkForBadges($user, $activity);

                    // อัปเดตความคืบหน้าของเป้าหมาย
                    $this->updateGoalProgress($activity);
                } catch (\Exception $e) {
                    // Just log badge checking errors, don't fail the whole request
                    Log::error('Error checking badges or updating goals: ' . $e->getMessage());
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
            'activity_id' => 'required|exists:tb_run,id',
            'route_data' => 'required|json',
            'current_distance' => 'required|numeric',
        ]);

        $user = Auth::user();
        $activity = Run::where('id', $request->activity_id)
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
        $activity->route_data = $request->route_data;
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
            $totalCalories = DB::table('tb_run')
                ->where('user_id', $user->user_id)
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
                'route_data' => 'required',
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

            // ป้องกันการบันทึกซ้ำซ้อนโดยตรวจสอบว่ามีกิจกรรมที่ถูกบันทึกในช่วงเวลาใกล้เคียงกันหรือไม่
            $startTime = Carbon::now()->subMinutes(30); // เวลาเริ่มต้นโดยประมาณ
            $endTime = Carbon::now(); // เวลาสิ้นสุด

            // ตรวจสอบว่ามีกิจกรรมที่ถูกบันทึกในช่วงเวลา +/- 2 นาทีหรือไม่
            $existingActivity = Run::where('user_id', $user->user_id)
                ->where(function($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime->copy()->subMinutes(2), $startTime->copy()->addMinutes(2)])
                        ->orWhereBetween('end_time', [$endTime->copy()->subMinutes(2), $endTime->copy()->addMinutes(2)]);
                })
                ->whereNotNull('end_time')
                ->first();

            if ($existingActivity) {
                Log::warning('พบการบันทึกซ้ำซ้อนในช่วงเวลาใกล้เคียง', [
                    'user_id' => $user->user_id,
                    'existing_activity_id' => $existingActivity->run_id,
                    'start_time' => $startTime->toDateTimeString(),
                    'end_time' => $endTime->toDateTimeString()
                ]);

                // ส่งกลับข้อมูลกิจกรรมที่มีอยู่แล้ว
                return response()->json([
                    'success' => true,
                    'message' => 'กิจกรรมนี้ถูกบันทึกไปแล้ว',
                    'activity' => $existingActivity,
                    'is_duplicate' => true
                ]);
            }

            // สร้างกิจกรรมใหม่
            $activity = new Run();
            $activity->user_id = $user->user_id;
            $activity->start_time = $startTime;
            $activity->end_time = $endTime;
            $activity->distance = $request->distance;
            $activity->calories_burned = $request->calories_burned;
            $activity->average_speed = $request->average_speed;
            $activity->is_test = $request->input('is_test', false); // ค่าเริ่มต้นเป็น false (วิ่งจริง)

            // ตรวจสอบและแปลงข้อมูล route_data ถ้าจำเป็น
            if (is_string($request->route_data)) {
                $activity->route_data = $request->route_data;
            } else {
                $activity->route_data = json_encode($request->route_data);
            }

            $activity->save();

            // ตรวจสอบเหรียญรางวัล - เฉพาะการวิ่งจริงเท่านั้น
            if (!$activity->is_test) {
                $this->checkForBadges($user, $activity);

                // อัปเดตความคืบหน้าของเป้าหมาย
                $this->updateGoalProgress($activity);
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
        $testActivities = Run::where('user_id', $user->user_id)
            ->where('is_test', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('run.test', compact('testActivities'));
    }

    /**
     * Check for active/unfinished activities
     */
    public function checkActiveActivity()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
                'has_active' => false
            ], 401);
        }

        // ค้นหากิจกรรมที่ยังไม่เสร็จสิ้น
        $activeActivity = Run::where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->first();

        if (!$activeActivity) {
            return response()->json([
                'status' => 'success',
                'has_active' => false,
                'message' => 'ไม่พบกิจกรรมที่ยังไม่เสร็จสิ้น'
            ]);
        }

        // คำนวณเวลาที่ผ่านไปตั้งแต่เริ่มกิจกรรม
        $elapsedTime = 0;
        try {
            if ($activeActivity->start_time) {
                $startTime = $activeActivity->start_time instanceof Carbon
                    ? $activeActivity->start_time
                    : Carbon::parse($activeActivity->start_time);

                $elapsedTime = $startTime->diffInSeconds(Carbon::now());
            }
        } catch (\Exception $e) {
            Log::error('Error calculating elapsed time: ' . $e->getMessage());
        }

        // ตรวจสอบว่ากิจกรรมนี้ถูกหยุดชั่วคราวหรือไม่ (ถ้ามี column is_paused)
        $isPaused = false;
        if (Schema::hasColumn('tb_run', 'is_paused')) {
            $isPaused = (bool)$activeActivity->is_paused;
        }

        // โหลดข้อมูลเส้นทาง
        $routeData = [];
        if ($activeActivity->route_data) {
            try {
                if (is_string($activeActivity->route_data)) {
                    $routeData = json_decode($activeActivity->route_data, true);
                } else {
                    $routeData = $activeActivity->route_data;
                }
            } catch (\Exception $e) {
                Log::error('Error parsing route data: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'has_active' => true,
            'activity_id' => $activeActivity->run_id,
            'start_time' => $activeActivity->start_time,
            'elapsed_time' => $elapsedTime,
            'distance' => $activeActivity->distance ?? 0,
            'average_speed' => $activeActivity->average_speed ?? 0,
            'calories_burned' => $activeActivity->calories_burned ?? 0,
            'is_paused' => $isPaused,
            'is_test' => (bool)$activeActivity->is_test,
            'route_data' => $routeData
        ]);
    }

    /**
     * Toggle pause status of an activity
     */
    public function togglePause(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'run_id' => 'required|exists:tb_run,id',
                'is_paused' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first()
                ], 400);
            }

            $user = Auth::user();
            $activity = Run::where('id', $request->run_id)
                ->where('user_id', $user->user_id)
                ->whereNull('end_time')
                ->first();

            if (!$activity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบกิจกรรมที่ระบุ'
                ], 404);
            }

            // Just log the pause status - we don't need to store it in DB
            Log::info('Activity pause status toggled', [
                'activity_id' => $activity->run_id,
                'user_id' => $user->user_id,
                'is_paused' => $request->is_paused
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $request->is_paused ? 'หยุดการวิ่งชั่วคราว' : 'กลับมาวิ่งต่อ',
                'is_paused' => $request->is_paused
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling pause status: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะการวิ่ง: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update activity data during run
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'run_id' => 'required|exists:tb_run,id',
                'distance' => 'required|numeric',
                'duration' => 'required|numeric',
                'calories' => 'required|numeric',
                'average_speed' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first()
                ], 400);
            }

            $user = Auth::user();
            $activity = Run::where('id', $request->run_id)
                ->where('user_id', $user->user_id)
                ->whereNull('end_time')
                ->first();

            if (!$activity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบกิจกรรมที่ระบุ'
                ], 404);
            }

            // ไม่จำเป็นต้องบันทึกข้อมูลในระหว่างการวิ่งทุกครั้ง แต่จะบันทึกล็อกไว้เพื่อการดีบัก
            Log::info('Activity update received', [
                'activity_id' => $activity->run_id,
                'user_id' => $user->user_id,
                'distance' => $request->distance,
                'duration' => $request->duration,
                'calories' => $request->calories,
                'average_speed' => $request->average_speed
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'บันทึกข้อมูลระหว่างวิ่งเรียบร้อย'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating activity data: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูลการวิ่ง: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * อัปเดตความคืบหน้าของเป้าหมาย
     *
     * @param Run $run ข้อมูลการวิ่ง
     * @return void
     */
    private function updateGoalProgress($run)
    {
        try {
            $user = $run->user;
            if (!$user) {
                return;
            }

            // ดึงเป้าหมายที่กำลังดำเนินการอยู่ของผู้ใช้
            $goals = DB::table('tb_activity_goals')
                ->where('user_id', $user->user_id)
                ->where('is_completed', false)
                ->whereDate('end_date', '>=', now())
                ->get();

            foreach ($goals as $goal) {
                $currentValue = $goal->current_value ?? 0;
                $updated = false;

                // อัปเดตความคืบหน้าตามประเภทเป้าหมาย
                switch ($goal->type) {
                    case 'distance':
                        if ($run->distance) {
                            $currentValue += $run->distance;
                            $updated = true;
                        }
                        break;
                    case 'duration':
                        if ($run->duration) {
                            $durationMinutes = $run->duration / 60; // แปลงวินาทีเป็นนาที
                            $currentValue += $durationMinutes;
                            $updated = true;
                        }
                        break;
                    case 'calories':
                        if ($run->calories_burned) {
                            $currentValue += $run->calories_burned;
                            $updated = true;
                        }
                        break;
                    case 'frequency':
                        $currentValue += 1;
                        $updated = true;
                        break;
                }

                if ($updated) {
                    // บันทึกการอัปเดตความก้าวหน้า
                    DB::table('tb_activity_goals')
                        ->where('id', $goal->id)
                        ->update([
                            'current_value' => $currentValue,
                            'updated_at' => now()
                        ]);

                    // ตรวจสอบว่าเป้าหมายสำเร็จหรือไม่
                    if ($currentValue >= $goal->target_value) {
                        DB::table('tb_activity_goals')
                            ->where('id', $goal->id)
                            ->update([
                                'is_completed' => true,
                                'completed_at' => now(),
                                'updated_at' => now()
                            ]);

                        // ล็อกการบรรลุเป้าหมาย
                        Log::info('Goal completed', [
                            'user_id' => $user->user_id,
                            'goal_id' => $goal->id,
                            'type' => $goal->type,
                            'target_value' => $goal->target_value,
                            'current_value' => $currentValue
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't stop execution
            Log::error('Error updating goal progress: ' . $e->getMessage());
        }
    }
}
