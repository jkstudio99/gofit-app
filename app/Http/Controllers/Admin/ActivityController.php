<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Requests\ActivityRequest;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * แสดงรายการกิจกรรมทั้งหมด
     */
    public function index(Request $request)
    {
        // ตัวแปรสำหรับการกรองและค้นหา
        $search = $request->input('search');
        $type = $request->input('activity_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');

        // สร้างคำสั่ง query พื้นฐาน
        $query = Activity::with('user');

        // ค้นหาตามเงื่อนไข
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('activity_type', 'like', "%$search%")
                  ->orWhere('notes', 'like', "%$search%");
            });
        }

        // กรองตามประเภทกิจกรรม
        if ($type) {
            $query->where('activity_type', $type);
        }

        // กรองตามช่วงวันที่
        if ($startDate) {
            $query->where('start_time', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->where('start_time', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // กรองตามผู้ใช้
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // จัดเรียงและแบ่งหน้า
        $activities = $query->orderBy('start_time', 'desc')
                           ->paginate(20)
                           ->appends($request->all());

        // รวบรวมข้อมูลสำหรับตัวกรอง
        $activityTypes = DB::table('tb_activity')
                            ->select('activity_type')
                            ->distinct()
                            ->pluck('activity_type')
                            ->toArray();

        $users = User::orderBy('firstname')->get(['user_id', 'firstname', 'lastname', 'username']);

        // ส่งข้อมูลไปยังหน้าแสดงผล
        return view('admin.activities.index', compact(
            'activities',
            'activityTypes',
            'users',
            'search',
            'type',
            'startDate',
            'endDate',
            'userId'
        ));
    }

    /**
     * แสดงหน้าเพิ่มกิจกรรมใหม่
     */
    public function create()
    {
        $users = User::orderBy('firstname')->get(['user_id', 'firstname', 'lastname', 'username']);
        $activityTypes = [
            'running' => 'วิ่ง',
            'cycling' => 'ปั่นจักรยาน',
            'swimming' => 'ว่ายน้ำ',
            'walking' => 'เดิน',
            'hiking' => 'เดินป่า',
            'yoga' => 'โยคะ',
            'gym' => 'ออกกำลังกายฟิตเนส',
            'other' => 'อื่นๆ'
        ];

        return view('admin.activities.create', compact('users', 'activityTypes'));
    }

    /**
     * บันทึกกิจกรรมใหม่
     */
    public function store(ActivityRequest $request)
    {
        $validated = $request->validated();

        // คำนวณค่าต่างๆ เพิ่มเติม
        if (!isset($validated['end_time']) && isset($validated['start_time']) && isset($validated['duration'])) {
            $startTime = Carbon::parse($validated['start_time']);
            $validated['end_time'] = $startTime->copy()->addMinutes($validated['duration']);
        }

        // บันทึกกิจกรรม
        $activity = Activity::create($validated);

        return redirect()->route('admin.activities.show', $activity->activity_id)
                        ->with('success', 'เพิ่มกิจกรรมสำเร็จแล้ว');
    }

    /**
     * แสดงรายละเอียดกิจกรรม
     */
    public function show(Activity $activity)
    {
        return view('admin.activities.show', compact('activity'));
    }

    /**
     * แสดงหน้าแก้ไขกิจกรรม
     */
    public function edit(Activity $activity)
    {
        $users = User::orderBy('firstname')->get(['user_id', 'firstname', 'lastname', 'username']);
        $activityTypes = [
            'running' => 'วิ่ง',
            'cycling' => 'ปั่นจักรยาน',
            'swimming' => 'ว่ายน้ำ',
            'walking' => 'เดิน',
            'hiking' => 'เดินป่า',
            'yoga' => 'โยคะ',
            'gym' => 'ออกกำลังกายฟิตเนส',
            'other' => 'อื่นๆ'
        ];

        return view('admin.activities.edit', compact('activity', 'users', 'activityTypes'));
    }

    /**
     * อัพเดทข้อมูลกิจกรรม
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $validated = $request->validated();

        // คำนวณค่าต่างๆ เพิ่มเติม
        if (isset($validated['start_time']) && isset($validated['duration'])) {
            $startTime = Carbon::parse($validated['start_time']);
            $validated['end_time'] = $startTime->copy()->addMinutes($validated['duration']);
        }

        // อัพเดทกิจกรรม
        $activity->update($validated);

        return redirect()->route('admin.activities.show', $activity->activity_id)
                        ->with('success', 'อัพเดทกิจกรรมสำเร็จแล้ว');
    }

    /**
     * ลบกิจกรรม
     */
    public function destroy(Activity $activity)
    {
        // ลบกิจกรรม
        $activity->delete();

        return redirect()->route('admin.activities.index')
                        ->with('success', 'ลบกิจกรรมสำเร็จแล้ว');
    }

    /**
     * แสดงสถิติกิจกรรมของผู้ใช้
     */
    public function statistics()
    {
        // สถิติทั่วไป
        $stats = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', Carbon::today())->count(),
            'week' => Activity::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'month' => Activity::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // กิจกรรมล่าสุด
        $latestActivities = Activity::with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->limit(10)
                                   ->get();

        // ผู้ใช้ที่มีกิจกรรมมากที่สุด
        $topUsers = User::select('tb_user.user_id', 'tb_user.firstname', 'tb_user.lastname', 'tb_user.username', DB::raw('COUNT(tb_activity.activity_id) as activity_count'))
                        ->leftJoin('tb_activity', 'tb_user.user_id', '=', 'tb_activity.user_id')
                        ->groupBy('tb_user.user_id', 'tb_user.firstname', 'tb_user.lastname', 'tb_user.username')
                        ->having('activity_count', '>', 0)
                        ->orderBy('activity_count', 'desc')
                        ->limit(10)
                        ->get();

        // ประเภทกิจกรรมที่นิยม
        $popularTypes = DB::table('tb_activity')
                         ->select('activity_type', DB::raw('COUNT(*) as count'))
                         ->groupBy('activity_type')
                         ->orderBy('count', 'desc')
                         ->get();

        return view('admin.activities.statistics', compact('stats', 'latestActivities', 'topUsers', 'popularTypes'));
    }
}
