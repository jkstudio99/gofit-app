<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RunController extends Controller
{
    /**
     * แสดงหน้าหลักการวิ่ง
     */
    public function index()
    {
        // ใช้ ID ที่แน่นอนแทนการใช้ auth()->id()
        $userId = 3; // ต้องแน่ใจว่า user นี้มีอยู่จริงในระบบ

        // ดึงกิจกรรมล่าสุด 5 รายการ
        $recentActivities = Activity::where('user_id', $userId)
            ->where('activity_type', 'running')
            ->orderBy('start_time', 'desc')
            ->take(5)
            ->get();

        return view('run.index', compact('recentActivities'));
    }

    /**
     * บันทึกข้อมูลการวิ่ง
     */
    public function store(Request $request)
    {
        $request->validate([
            'distance' => 'required|numeric',
            'calories_burned' => 'required|numeric',
            'average_speed' => 'required|numeric',
            'route_gps_data' => 'required|string',
        ]);

        $routeData = json_decode($request->route_gps_data, true);

        // ใช้ ID ที่แน่นอน
        $userId = 3;

        // สร้างกิจกรรมใหม่
        $activity = new Activity();
        $activity->user_id = $userId;
        $activity->activity_type = 'running';
        $activity->distance = $request->distance;
        $activity->calories_burned = $request->calories_burned;
        $activity->average_speed = $request->average_speed;
        $activity->route_gps_data = $request->route_gps_data;
        $activity->start_time = Carbon::now()->subMinutes(10); // สมมติว่าเริ่มวิ่งเมื่อ 10 นาทีที่แล้ว
        $activity->end_time = Carbon::now();
        $activity->is_test = 0;
        $activity->save();

        return response()->json(['success' => true, 'activity' => $activity]);
    }

    /**
     * หน้าทดสอบการวิ่ง
     */
    public function test()
    {
        return view('run.test');
    }

    /**
     * แสดงประวัติการวิ่งทั้งหมด
     */
    public function history()
    {
        // ใช้ ID ที่แน่นอน
        $userId = 3;

        // ดึงกิจกรรมทั้งหมดของผู้ใช้ปัจจุบัน
        $activities = Activity::where('user_id', $userId)
            ->where('activity_type', 'running')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        // คำนวณสถิติรวม
        $totalDistance = Activity::where('user_id', $userId)
            ->where('activity_type', 'running')
            ->sum('distance');

        $totalCalories = Activity::where('user_id', $userId)
            ->where('activity_type', 'running')
            ->sum('calories_burned');

        // คำนวณเวลารวม (เป็นชั่วโมง)
        $totalSeconds = 0;
        $allActivities = Activity::where('user_id', $userId)
            ->where('activity_type', 'running')
            ->whereNotNull('end_time')
            ->get();

        foreach ($allActivities as $activity) {
            if ($activity->start_time && $activity->end_time) {
                $start = $activity->start_time instanceof \Carbon\Carbon
                    ? $activity->start_time
                    : \Carbon\Carbon::parse($activity->start_time);

                $end = $activity->end_time instanceof \Carbon\Carbon
                    ? $activity->end_time
                    : \Carbon\Carbon::parse($activity->end_time);

                $totalSeconds += $end->diffInSeconds($start);
            }
        }

        $totalTime = gmdate('H:i', $totalSeconds);

        return view('run.history', compact('activities', 'totalDistance', 'totalCalories', 'totalTime'));
    }
}
