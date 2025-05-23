<?php

namespace App\Http\Controllers;

use App\Models\Run;
use App\Models\RunShare;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RunController extends Controller
{
    /**
     * แสดงหน้าหลักการวิ่ง
     */
    public function index()
    {
        $userId = Auth::id() ?? 3; // Fallback if not authenticated

        // ดึงกิจกรรมการวิ่งล่าสุด 5 รายการ
        $recentRuns = Run::where('user_id', $userId)
            ->orderBy('start_time', 'desc')
            ->take(5)
            ->get();

        // ตำแหน่งเริ่มต้น (กรุงเทพฯ)
        $defaultLat = 13.736717;
        $defaultLng = 100.523186;

        return view('run.index', compact('recentRuns', 'defaultLat', 'defaultLng'));
    }

    /**
     * เริ่มการวิ่ง
     */
    public function start(Request $request)
    {
        $userId = Auth::id() ?? 3;

        $run = new Run();
        $run->user_id = $userId;
        $run->start_time = Carbon::now();
        $run->is_completed = false;
        $run->save();

        return response()->json(['success' => true, 'run_id' => $run->run_id]);
    }

    /**
     * อัปเดตตำแหน่งและข้อมูลระหว่างการวิ่ง
     */
    public function updatePosition(Request $request)
    {
        $request->validate([
            'run_id' => 'required|exists:tb_run,run_id',
            'distance' => 'required|numeric',
            'duration' => 'required|numeric',
            'speed' => 'required|numeric'
        ]);

        $run = Run::findOrFail($request->run_id);

        // ตรวจสอบความเร็วหรือการเคลื่อนไหวที่ผิดปกติ
        $movementValidity = $this->validateRunningMovement($request->speed, $request->distance, $request->duration);

        // บันทึกผลการตรวจสอบ
        $run->movement_validity = $movementValidity['valid'] ? 'valid' : 'suspicious';
        $run->validity_note = $movementValidity['note'];

        // อัปเดตข้อมูลล่าสุด
        $run->distance = $request->distance;
        $run->duration = $request->duration;
        $run->average_speed = $request->speed;

        // คำนวณแคลอรี่ (60 แคลอรี่ต่อ 1 กิโลเมตร)
        $run->calories_burned = round($request->distance * 60);

        // อัปเดตเส้นทาง GPS ถ้ามี
        if ($request->has('route_data')) {
            $run->route_data = json_decode($request->route_data, true);
        }

        $run->save();

        return response()->json([
            'success' => true,
            'movement_validity' => $movementValidity
        ]);
    }

    /**
     * จบการวิ่ง
     */
    public function finish(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
            'run_id' => 'required|exists:tb_run,run_id',
            'distance' => 'required|numeric',
            'duration' => 'required|numeric',
                'calories' => 'nullable|numeric',
                'average_speed' => 'required|numeric',
                'route_data' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first(),
                ], 400);
            }

            $userId = Auth::id() ?? 3;

            $run = Run::where('run_id', $request->run_id)
                ->where('user_id', $userId)
                ->first();

            if (!$run) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลการวิ่ง',
                ], 404);
            }

            // ตรวจสอบความเร็วและการเคลื่อนไหวที่ผิดปกติ
            $movementValidity = $this->validateRunningMovement(
                $request->average_speed,
                $request->distance,
                $request->duration,
                $request->route_data
            );

            // บันทึกผลการตรวจสอบ
            $run->movement_validity = $movementValidity['valid'] ? 'valid' : 'suspicious';
            $run->validity_note = $movementValidity['note'];

            // ดึงน้ำหนักของผู้ใช้
            $weight = 70; // ใช้ค่าเริ่มต้น 70 กิโลกรัม
            if ($run->user && $run->user->weight) {
                $weight = $run->user->weight;
            }

        // อัปเดตข้อมูลสุดท้าย
        $run->distance = $request->distance;
        $run->duration = $request->duration;
            $run->average_speed = $request->average_speed;

            // คำนวณแคลอรี่ด้วยสูตรที่แม่นยำกว่า ถ้าไม่มีข้อมูลแคลอรี่จาก request
            if ($request->has('calories') && $request->calories > 0) {
                $run->calories_burned = $request->calories;
            } else {
                $run->calories_burned = $this->calculateCalories($weight, $run->distance, $run->duration);
            }

        $run->end_time = Carbon::now();
        $run->is_completed = true;

            // Don't set is_paused directly - use a try/catch to handle missing column gracefully
            try {
                // Only set this property if the column exists
                if (Schema::hasColumn('tb_run', 'is_paused')) {
                    $run->is_paused = false;
                }
            } catch (\Exception $e) {
                // Ignore errors if column doesn't exist
                Log::warning('is_paused column not found: ' . $e->getMessage());
            }

            // Decode JSON route data
            try {
                $run->route_data = json_decode($request->route_data, true);
        $run->save();
            } catch (\Exception $e) {
                Log::error('Error saving run data: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage(),
                ], 500);
            }

            try {
                // อัปเดตความคืบหน้าของเป้าหมาย
                $this->updateGoalProgress($run);
            } catch (\Exception $e) {
                // Log error but continue with the rest of the function
                Log::error('Error updating goal progress: ' . $e->getMessage());
            }

            // คำนวณความสำเร็จ
            $achievements = $this->calculateAchievements($run);

            try {
                // ตรวจสอบและมอบเหรียญรางวัล
                $this->checkAndAwardBadges($userId);
            } catch (\Exception $e) {
                // Log error but continue with the rest of the function
                Log::error('Error checking badges: ' . $e->getMessage());
            }

            // ล้างแคชเพื่อให้แน่ใจว่าข้อมูลจะถูกอัปเดตในการแสดงผลครั้งถัดไป
            if (class_exists('Illuminate\Support\Facades\Cache')) {
                Cache::forget('user_badges_' . $run->user_id);
                Cache::forget('user_stats_' . $run->user_id);
            }

        return response()->json([
                'status' => 'success',
                'message' => 'บันทึกการวิ่งสำเร็จ',
                'activity' => $run,
                'achievements' => $achievements,
                'movement_validity' => $movementValidity
            ]);
        } catch (\Exception $e) {
            Log::error('Error in finish method: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * คำนวณความสำเร็จจากการวิ่ง
     */
    private function calculateAchievements($run)
    {
        $achievements = [];

        // ตรวจสอบระยะทาง
        if ($run->distance >= 5) {
            $achievements[] = [
                'type' => 'distance',
                'title' => 'วิ่งระยะไกล',
                'description' => 'วิ่งระยะทาง 5 กิโลเมตรหรือมากกว่า'
            ];
        }

        // ตรวจสอบความเร็ว
        if ($run->average_speed >= 10) {
            $achievements[] = [
                'type' => 'speed',
                'title' => 'นักวิ่งความเร็วสูง',
                'description' => 'วิ่งด้วยความเร็วเฉลี่ย 10 กม./ชม. หรือมากกว่า'
            ];
        }

        return $achievements;
    }

    /**
     * บันทึกข้อมูลการวิ่ง
     */
    public function store(Request $request)
    {
        $request->validate([
            'distance' => 'required|numeric',
            'duration' => 'required|numeric', // in seconds
            'calories_burned' => 'nullable|numeric',
            'average_speed' => 'required|numeric',
            'route_data' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
        $routeData = json_decode($request->route_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'รูปแบบข้อมูลเส้นทางไม่ถูกต้อง: ' . json_last_error_msg()
                ], 400);
            }

        $userId = Auth::id() ?? 3;
        $user = \App\Models\User::find($userId);
        $weight = $user->weight ?? 70; // ใช้ค่าเริ่มต้น 70 กิโลกรัม ถ้าไม่มีข้อมูล

        // สร้างรายการวิ่งใหม่
        $run = new Run();
        $run->user_id = $userId;
        $run->distance = $request->distance;
        $run->duration = $request->duration;

        // คำนวณแคลอรี่ด้วยสูตรที่แม่นยำกว่า ถ้าไม่มีข้อมูลแคลอรี่จาก request
        if ($request->has('calories_burned') && $request->calories_burned > 0) {
        $run->calories_burned = $request->calories_burned;
        } else {
            $run->calories_burned = $this->calculateCalories($weight, $request->distance, $request->duration);
        }

            // บันทึกสถานะการทดสอบ
            $run->is_test = $request->input('is_test', false);

        $run->average_speed = $request->average_speed;
        $run->route_data = $routeData;
        $run->notes = $request->notes;
        $run->start_time = Carbon::now()->subSeconds($request->duration);
        $run->end_time = Carbon::now();
        $run->is_completed = true;
        $run->save();

        // อัปเดตความคืบหน้าของเป้าหมาย
        $this->updateGoalProgress($run);

        // คำนวณความสำเร็จ
        $achievements = $this->calculateAchievements($run);

        // ตรวจสอบและมอบเหรียญรางวัล
        $this->checkAndAwardBadges($userId);

            // ส่งค่ากลับในรูปแบบที่ frontend ต้องการ
        return response()->json([
                'status' => 'success',
                'message' => 'บันทึกการวิ่งเรียบร้อยแล้ว',
            'run' => $run,
            'achievements' => $achievements
        ]);
        } catch (\Exception $e) {
            Log::error('Error in RunController::store: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * แสดงประวัติการวิ่งทั้งหมด
     */
    public function history()
    {
        $userId = Auth::id() ?? 3;

        // Debug: log a sample of route data for one activity
        $sampleActivity = Run::where('user_id', $userId)
            ->whereNotNull('end_time')
            ->orderBy('start_time', 'desc')
            ->first();

        if ($sampleActivity) {
            Log::debug('Sample route data type: ' . gettype($sampleActivity->route_data));
            Log::debug('Sample route data: ' . json_encode($sampleActivity->route_data));
        }

        // ดึงกิจกรรมทั้งหมดของผู้ใช้ปัจจุบัน (เฉพาะที่เสร็จสมบูรณ์แล้ว)
        $activities = Run::where('user_id', $userId)
            ->whereNotNull('end_time')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        // คำนวณสถิติรวม (เฉพาะกิจกรรมที่เสร็จสมบูรณ์)
        $totalDistance = Run::where('user_id', $userId)
            ->whereNotNull('end_time')
            ->sum('distance');
        $totalCalories = Run::where('user_id', $userId)
            ->whereNotNull('end_time')
            ->sum('calories_burned');

        // คำนวณ totalDuration ใหม่โดยตรวจสอบค่า duration และคำนวณจาก start_time และ end_time ถ้าจำเป็น
        $totalDuration = 0;
        $allCompletedActivities = Run::where('user_id', $userId)
            ->whereNotNull('end_time')
            ->get();

        foreach ($allCompletedActivities as $activity) {
            if ($activity->duration > 0) {
                $totalDuration += $activity->duration;
            } else {
                // คำนวณเวลาจาก start_time และ end_time
                if ($activity->start_time && $activity->end_time) {
                    $startTime = $activity->start_time instanceof Carbon
                        ? $activity->start_time
                        : Carbon::parse($activity->start_time);

                    $endTime = $activity->end_time instanceof Carbon
                        ? $activity->end_time
                        : Carbon::parse($activity->end_time);

                    $totalDuration += $startTime->diffInSeconds($endTime);
                }
            }
        }

        // แปลงวินาทีเป็นรูปแบบชั่วโมง:นาที:วินาที
        $totalHours = floor($totalDuration / 3600);
        $totalMinutes = floor(($totalDuration % 3600) / 60);
        $totalSeconds = $totalDuration % 60;
        $totalTime = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);

        return view('run.history', compact('activities', 'totalDistance', 'totalCalories', 'totalTime'));
    }

    /**
     * แสดงรายละเอียดการวิ่ง
     */
    public function show($id)
    {
        $run = Run::findOrFail($id);

        return view('run.show', compact('run'));
    }

    /**
     * แบ่งปันเส้นทางการวิ่งกับเพื่อน
     */
    public function share(Request $request)
    {
        $request->validate([
            'run_id' => 'required|exists:tb_run,run_id',
            'user_id' => 'required|exists:tb_users,user_id',
            'message' => 'nullable|string|max:255'
        ]);

        $userId = Auth::id() ?? 3;

        // ตรวจสอบว่าเป็นการวิ่งของตัวเอง
        $run = Run::where('run_id', $request->run_id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // อัปเดตสถานะการแชร์
        $run->is_shared = true;
        $run->save();

        // สร้างการแชร์
        $share = new RunShare();
        $share->run_id = $request->run_id;
        $share->user_id = $userId;
        $share->shared_with_user_id = $request->user_id;
        $share->share_message = $request->message;
        $share->save();

        return response()->json(['success' => true]);
    }

    /**
     * แสดงการวิ่งที่แชร์กับผู้ใช้
     */
    public function sharedWithMe()
    {
        $userId = Auth::id() ?? 3;

        $sharedRuns = RunShare::with(['run', 'user'])
            ->where('shared_with_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('run.shared', compact('sharedRuns'));
    }

    /**
     * ลบข้อมูลการวิ่ง
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'run_id' => 'required|exists:tb_run,run_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first(),
                ], 400);
            }

            $userId = Auth::id() ?? 3;

            // ตรวจสอบว่าเป็นการวิ่งของผู้ใช้คนนี้หรือไม่
            $run = Run::where('run_id', $request->run_id)
                ->where('user_id', $userId)
                ->first();

            if (!$run) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการวิ่ง หรือคุณไม่มีสิทธิ์ลบข้อมูลนี้',
                ], 404);
            }

            // ลบการแชร์ที่เกี่ยวข้อง (ถ้ามี)
            \App\Models\RunShare::where('run_id', $request->run_id)->delete();

            // ลบข้อมูลการวิ่ง
            $run->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลการวิ่งเรียบร้อยแล้ว',
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * คำนวณแคลอรี่ที่เผาผลาญจากการวิ่ง
     *
     * @param float $weight น้ำหนักในกิโลกรัม
     * @param float $distance ระยะทางในกิโลเมตร
     * @param int $duration เวลาในวินาที
     * @return int แคลอรี่ที่เผาผลาญ
     */
    private function calculateCalories($weight, $distance, $duration)
    {
        // ตรวจสอบข้อมูลนำเข้า
        if ($duration <= 0 || $distance <= 0 || $weight <= 0) {
            return 0;
        }

        // คำนวณความเร็วเฉลี่ย (กม./ชม.)
        $speed = $distance / ($duration / 3600);

        // กำหนด MET (Metabolic Equivalent of Task) ตามความเร็ว
        // อ้างอิงจาก Compendium of Physical Activities
        if ($speed < 6.4) { // การเดินเร็ว
            $met = 4.5;
        } elseif ($speed < 8.0) { // การวิ่งเหยาะ
            $met = 7.0;
        } elseif ($speed < 9.7) { // การวิ่งช้า
            $met = 9.0;
        } elseif ($speed < 11.3) { // การวิ่งปานกลาง
            $met = 11.0;
        } else { // การวิ่งเร็ว
            $met = 13.5;
        }

        // สูตรคำนวณแคลอรี่: MET × น้ำหนัก (kg) × เวลา (ชั่วโมง)
        $hours = $duration / 3600;
        $calories = $met * $weight * $hours;

        return round($calories);
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

            // ใช้ query builder แทนเพื่อหลีกเลี่ยงปัญหาความสัมพันธ์
            $goals = DB::table('tb_activity_goals')
                ->where('user_id', $user->user_id) // ใช้ user_id แทน user_user_id
                ->where('type', 'running')
                ->where('status', 'active')
                ->get();

            foreach ($goals as $goal) {
                $progress = $goal->progress ?? 0;

                // อัปเดตความคืบหน้าตามประเภทเป้าหมาย
                if ($goal->metric === 'distance') {
                    $progress += $run->distance;
                } elseif ($goal->metric === 'calories') {
                    $progress += $run->calories_burned;
                } elseif ($goal->metric === 'time') {
                    $progress += $run->duration;
                }

                // บันทึกความคืบหน้า
                DB::table('tb_activity_goals')
                    ->where('id', $goal->id)
                    ->update([
                        'progress' => $progress,
                        'updated_at' => now()
                    ]);

                // ตรวจสอบว่าเป้าหมายสำเร็จหรือไม่
                if ($progress >= $goal->target_value) {
                    DB::table('tb_activity_goals')
                        ->where('id', $goal->id)
                        ->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'updated_at' => now()
                        ]);

                    // ตรวจสอบเหรียญรางวัล
                    $this->checkAndAwardBadges($user->user_id);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't stop execution
            Log::error('Error updating goal progress: ' . $e->getMessage());
        }
    }

    /**
     * ตรวจสอบและมอบเหรียญรางวัล
     *
     * @param int $userId ID ของผู้ใช้
     * @return void
     */
    private function checkAndAwardBadges($userId)
    {
        try {
            // Check if Badge model exists
            if (!class_exists('\\App\\Models\\Badge') ||
                !class_exists('\\App\\Models\\UserBadge')) {
                Log::warning('Badge models not found');
                return;
            }

            // Check if tables exist
            if (!Schema::hasTable('tb_badge') ||
                !Schema::hasTable('tb_user_badge')) {
                Log::warning('Badge tables not found');
                return;
            }

            // Get latest user statistics
            $userStats = DB::table('tb_run')
                ->where('user_id', $userId)
                ->where('is_completed', true)
                ->selectRaw('SUM(distance) as total_distance, SUM(duration) as total_duration, SUM(calories_burned) as total_calories, COUNT(*) as total_runs')
                ->first();

            if (!$userStats) {
                Log::info('No completed runs found for user ' . $userId);
                return;
            }

            // Trace log for debugging
            Log::info('User stats for badge checking:', [
                'user_id' => $userId,
                'total_distance' => $userStats->total_distance,
                'total_calories' => $userStats->total_calories,
                'total_runs' => $userStats->total_runs
            ]);

            // Get all badges
            $badges = DB::table('tb_badge')->get();

            foreach ($badges as $badge) {
                // Check if user already has this badge
                $userHasBadge = DB::table('tb_user_badge')
                    ->where('user_id', $userId)
                    ->where('badge_id', $badge->badge_id)
                    ->exists();

                if ($userHasBadge) {
                    continue;
                }

                // Check badge criteria
                $shouldAwardBadge = false;

                if ($badge->type === 'distance' && $userStats->total_distance >= $badge->criteria) {
                    Log::info('Awarding distance badge: ' . $badge->badge_id . ' to user: ' . $userId);
                    $shouldAwardBadge = true;
                } elseif ($badge->type === 'calories' && $userStats->total_calories >= $badge->criteria) {
                    Log::info('Awarding calories badge: ' . $badge->badge_id . ' to user: ' . $userId);
                    $shouldAwardBadge = true;
                } elseif ($badge->type === 'streak') {
                    // Check consecutive run days
                    $consecutiveDays = $this->getConsecutiveRunDays($userId);
                    if ($consecutiveDays >= $badge->criteria) {
                        Log::info('Awarding streak badge: ' . $badge->badge_id . ' to user: ' . $userId);
                        $shouldAwardBadge = true;
                    }
                }

                // Award badge if criteria met
                if ($shouldAwardBadge) {
                    try {
                        DB::table('tb_user_badge')->insert([
                            'user_id' => $userId,
                            'badge_id' => $badge->badge_id,
                            'earned_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Update user points
                        $user = DB::table('tb_users')->where('user_id', $userId)->first();
                        if ($user) {
                            $points = ($user->points ?? 0) + 100; // Add 100 points per badge
                            DB::table('tb_users')
                                ->where('user_id', $userId)
                                ->update(['points' => $points]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error awarding badge: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't stop execution
            Log::error('Error in checkAndAwardBadges: ' . $e->getMessage());
        }
    }

    /**
     * คำนวณจำนวนวันที่วิ่งต่อเนื่อง
     *
     * @param int $userId ID ของผู้ใช้
     * @return int จำนวนวันที่วิ่งต่อเนื่อง
     */
    private function getConsecutiveRunDays($userId)
    {
        try {
            // ดึงวันที่วิ่งทั้งหมดเรียงตามวันที่ล่าสุด
            $runDates = DB::table('tb_run')
                ->where('user_id', $userId)
                ->where('is_completed', true)
                ->orderBy('start_time', 'desc')
                ->pluck('start_time')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->unique()
                ->values()
                ->toArray();

            if (empty($runDates)) {
                return 0;
            }

            // ตรวจสอบความต่อเนื่อง
            $consecutiveDays = 1;
            $today = Carbon::today();
            $lastRunDate = Carbon::parse($runDates[0]);

            // ถ้าไม่ได้วิ่งวันนี้ ให้เริ่มนับจากวันล่าสุดที่วิ่ง
            if (!$lastRunDate->isToday()) {
                $consecutiveDays = 0;
            }

            for ($i = 0; $i < count($runDates) - 1; $i++) {
                $currentDate = Carbon::parse($runDates[$i]);
                $nextDate = Carbon::parse($runDates[$i + 1]);

                // ตรวจสอบว่าเป็นวันติดกันหรือไม่
                if ($currentDate->subDay()->format('Y-m-d') === $nextDate->format('Y-m-d')) {
                    $consecutiveDays++;
                } else {
                    break;
                }
            }

            return $consecutiveDays;
        } catch (\Exception $e) {
            Log::error('Error in getConsecutiveRunDays: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * หยุดชั่วคราว/กลับมาวิ่งต่อ
     */
    public function togglePause(Request $request)
    {
        $request->validate([
            'run_id' => 'required|exists:tb_run,run_id',
            'is_paused' => 'required|boolean'
        ]);

        try {
            // Check if the run exists
            $run = DB::table('tb_run')
                ->where('run_id', $request->run_id)
                ->first();

            if (!$run) {
                return response()->json([
                    'success' => false,
                    'message' => 'Run not found'
                ], 404);
            }

            // Check if the is_paused column exists before trying to set it
            if (Schema::hasColumn('tb_run', 'is_paused')) {
                // Update using query builder instead of Eloquent to avoid model issues
                DB::table('tb_run')
                    ->where('run_id', $request->run_id)
                    ->update([
                        'is_paused' => $request->is_paused,
                        'updated_at' => now()
                    ]);

                return response()->json([
                    'success' => true,
                    'is_paused' => $request->is_paused
                ]);
            } else {
                // Column doesn't exist, just return success without modifying the database
                return response()->json([
                    'success' => true,
                    'is_paused' => $request->is_paused,
                    'message' => 'Pause state tracked in memory only (column not in database)'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error toggling pause state: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error toggling pause state: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a shared run as viewed
     */
    public function markAsViewed($id)
    {
        try {
            $share = RunShare::findOrFail($id);

            // Check if the share is for the current user
            if ($share->shared_with_id != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $share->is_viewed = true;
            $share->viewed_at = now();
            $share->save();

            return response()->json([
                'success' => true,
                'message' => 'Marked as viewed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ตรวจสอบการเคลื่อนไหวว่าเป็นการวิ่งจริงหรือใช้ยานพาหนะ
     *
     * @param float $speed ความเร็วในหน่วย กม./ชม.
     * @param float $distance ระยะทางในหน่วย กม.
     * @param int $duration ระยะเวลาในหน่วยวินาที
     * @param string|null $routeData ข้อมูลเส้นทาง GPS ในรูปแบบ JSON string (optional)
     * @return array ผลการตรวจสอบความถูกต้อง และคำอธิบาย
     */
    private function validateRunningMovement($speed, $distance, $duration, $routeData = null)
    {
        // 1. ตรวจสอบความเร็วเฉลี่ย
        $isSpeedValid = true;
        $speedNote = "";

        // ความเร็ววิ่งสูงสุดของมนุษย์ปกติ (ไม่ใช่นักกีฬาระดับโลก) ~ 25 กม./ชม.
        // นักวิ่งระดับโลกอาจวิ่งได้ถึง 36-38 กม./ชม. ในระยะสั้นๆ
        if ($speed > 25) {
            $isSpeedValid = false;
            $speedNote = "ความเร็วสูงเกินกว่าการวิ่งปกติ ({$speed} กม./ชม.)";
        }

        // 2. ตรวจสอบความสม่ำเสมอ/ความผิดปกติของการเคลื่อนไหว
        $isMovementValid = true;
        $movementNote = "";

        // ตรวจสอบความแปรปรวนของความเร็วจากข้อมูล GPS (ถ้ามี)
        if ($routeData) {
            try {
                $routePoints = json_decode($routeData, true);

                // ถ้ามีจุด GPS มากกว่า 10 จุด จึงจะวิเคราะห์ความแปรปรวน
                if (is_array($routePoints) && count($routePoints) > 10) {
                    $speedVariations = $this->analyzeSpeedVariation($routePoints);

                    // การวิ่งปกติจะมีความแปรปรวนของความเร็ว แต่ไม่เกิน 200%
                    if ($speedVariations['max_variation'] > 3.0) {
                        $isMovementValid = false;
                        $movementNote = "พบความแปรปรวนของความเร็วที่ผิดปกติ";
                    }

                    // ตรวจสอบช่วงความเร่งที่ผิดปกติ (การเร่งความเร็วแบบทันทีทันใด)
                    if ($speedVariations['abnormal_acceleration_count'] > 3) {
                        $isMovementValid = false;
                        $movementNote .= " พบการเร่งความเร็วที่ผิดปกติ {$speedVariations['abnormal_acceleration_count']} ครั้ง";
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Error analyzing route data: " . $e->getMessage());
            }
        }

        // 3. ตรวจสอบระยะเวลาเทียบกับระยะทาง
        $isTimeDistanceValid = true;
        $timeDistanceNote = "";

        // คำนวณเวลาต่อกิโลเมตร (นาที/กม.)
        $minutesPerKm = ($duration / 60) / $distance;

        // เวลาต่อกิโลเมตรที่เร็วเกินไป (น้อยกว่า 2:00 นาที/กม. หรือ 30 กม./ชม.)
        if ($minutesPerKm < 2.0) {
            $isTimeDistanceValid = false;
            $timeDistanceNote = "เวลาต่อกิโลเมตรเร็วเกินไป (" . number_format($minutesPerKm, 2) . " นาที/กม.)";
        }

        // เวลาต่อกิโลเมตรที่ช้าเกินไป (มากกว่า 15:00 นาที/กม. หรือ 4 กม./ชม.)
        // แต่นี่อาจเป็นการเดินได้ จึงไม่ถือว่าผิดปกติ

        // ตัดสินผลรวม
        $isValid = $isSpeedValid && $isMovementValid && $isTimeDistanceValid;
        $note = trim($speedNote . " " . $movementNote . " " . $timeDistanceNote);

        if ($isValid) {
            $note = "การวิ่งปกติ";
        }

        return [
            'valid' => $isValid,
            'note' => $note,
            'details' => [
                'speed_valid' => $isSpeedValid,
                'movement_valid' => $isMovementValid,
                'time_distance_valid' => $isTimeDistanceValid
            ]
        ];
    }

    /**
     * วิเคราะห์ความแปรปรวนของความเร็วจากข้อมูล GPS
     *
     * @param array $routePoints ข้อมูลจุด GPS
     * @return array ผลการวิเคราะห์ความแปรปรวนของความเร็ว
     */
    private function analyzeSpeedVariation($routePoints)
    {
        $speeds = [];
        $speedVariations = [];
        $abnormalAccelerationCount = 0;

        // คำนวณความเร็วระหว่างจุด
        for ($i = 1; $i < count($routePoints); $i++) {
            $currentPoint = $routePoints[$i];
            $prevPoint = $routePoints[$i-1];

            // ตรวจสอบว่ามีข้อมูลที่จำเป็นครบหรือไม่
            if (!isset($currentPoint['lat']) || !isset($currentPoint['lng']) ||
                !isset($prevPoint['lat']) || !isset($prevPoint['lng']) ||
                !isset($currentPoint['timestamp']) || !isset($prevPoint['timestamp'])) {
                continue;
            }

            // คำนวณระยะทางระหว่างจุด (เมตร)
            $distance = $this->calculateDistance(
                $prevPoint['lat'], $prevPoint['lng'],
                $currentPoint['lat'], $currentPoint['lng']
            );

            // คำนวณเวลาที่ใช้ (วินาที)
            $timeDiff = strtotime($currentPoint['timestamp']) - strtotime($prevPoint['timestamp']);
            if ($timeDiff <= 0) continue; // ป้องกันการหารด้วยศูนย์

            // คำนวณความเร็ว (กม./ชม.)
            $speedKmH = ($distance / 1000) / ($timeDiff / 3600);
            $speeds[] = $speedKmH;

            // ตรวจสอบการเร่งความเร็ว (เร่งจาก 0 เป็น 15+ กม./ชม. ในเวลาน้อยกว่า 2 วินาที)
            if ($i > 1) {
                $prevSpeed = $speeds[count($speeds) - 2];
                $acceleration = ($speedKmH - $prevSpeed) / ($timeDiff / 3600); // อัตราเร่งในหน่วย กม./ชม./ชม.

                // การเร่งของยานพาหนะมักสูงกว่า 40 กม./ชม./วินาที
                if ($acceleration > 40 && $speedKmH > 15) {
                    $abnormalAccelerationCount++;
                }
            }
        }

        // คำนวณค่าสถิติของความเร็ว
        if (count($speeds) < 2) {
            return [
                'max_variation' => 0,
                'abnormal_acceleration_count' => 0
            ];
        }

        $avgSpeed = array_sum($speeds) / count($speeds);
        $maxSpeed = max($speeds);
        $minSpeed = min($speeds);

        // คำนวณความแปรปรวนสัมพัทธ์
        $maxVariation = $avgSpeed > 0 ? ($maxSpeed - $minSpeed) / $avgSpeed : 0;

        return [
            'max_variation' => $maxVariation,
            'abnormal_acceleration_count' => $abnormalAccelerationCount,
            'avg_speed' => $avgSpeed,
            'max_speed' => $maxSpeed,
            'min_speed' => $minSpeed
        ];
    }

    /**
     * คำนวณระยะทางระหว่างพิกัด GPS สองจุด (Haversine formula)
     *
     * @param float $lat1 ละติจูดของจุดที่ 1
     * @param float $lon1 ลองจิจูดของจุดที่ 1
     * @param float $lat2 ละติจูดของจุดที่ 2
     * @param float $lon2 ลองจิจูดของจุดที่ 2
     * @return float ระยะทางในหน่วยเมตร
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // แปลงองศาเป็นเรเดียน
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // รัศมีของโลก (เมตร)
        $radius = 6371000;

        // Haversine formula
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $radius * $c;

        return $distance;
    }
}


