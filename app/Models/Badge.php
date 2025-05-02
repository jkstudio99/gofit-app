<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\UserBadge;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Badge extends Model
{
    use HasFactory;

    protected $table = 'tb_badge';
    protected $primaryKey = 'badge_id';
    public $timestamps = true;

    protected $fillable = [
        'badge_name',
        'badge_desc',
        'type',
        'criteria',
        'points',
        'badge_image'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tb_user_badge', 'badge_id', 'user_id')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    /**
     * ตรวจสอบว่าผู้ใช้ปัจจุบันได้รับเหรียญตรานี้แล้วหรือไม่
     */
    public function isUnlocked()
    {
        $user = Auth::user();
        if (!$user) return false;

        return UserBadge::where('user_id', $user->user_id)
            ->where('badge_id', $this->badge_id)
            ->exists();
    }

    /**
     * คืนค่าความก้าวหน้าของการได้รับเหรียญตรา
     */
    public function progress()
    {
        $user = Auth::user();
        if (!$user) return 'ยังไม่ได้รับ';

        // ตรวจสอบว่าผู้ใช้ได้รับเหรียญนี้แล้วหรือไม่
        $isUnlocked = UserBadge::where('user_id', $user->user_id)
            ->where('badge_id', $this->badge_id)
            ->exists();

        // ดึงข้อมูลกิจกรรมทั้งหมดของผู้ใช้แบบ force refresh
        $userStats = DB::table('tb_run')
            ->where('user_id', $user->user_id)
            ->where('is_completed', true)
            ->selectRaw('SUM(distance) as total_distance, SUM(calories_burned) as total_calories, COUNT(*) as total_runs')
            ->first();

        // ไม่มีกิจกรรมการวิ่ง
        if (!$userStats || !$userStats->total_runs) {
            return 'ยังไม่มีกิจกรรม';
        }

        // แสดงเงื่อนไขที่แตกต่างกันตามประเภทของเหรียญ
        switch ($this->type) {
            case 'distance':
                // รวมระยะทางที่ผู้ใช้วิ่งได้
                $totalDistance = $userStats->total_distance;

                // แสดงความก้าวหน้า
                $goal = (float)$this->criteria;
                $progress = ($goal > 0) ? min(100, ($totalDistance / $goal) * 100) : 0;

                // เหรียญที่ยังไม่ได้ปลดล็อค ไม่ควรแสดงเป็น 100%
                if (!$isUnlocked && $progress >= 100) {
                    // เปลี่ยนเป็นแสดง 100% แต่ต้องกดปลดล็อค
                    $progress = 100;
                }

                return 'เป้าหมาย: ' . number_format($goal, 1) . ' กม. (' .
                       number_format($totalDistance, 1) . '/' . number_format($goal, 1) . ' กม. - ' .
                       number_format($progress, 0) . '%)';

            case 'calories':
                // รวมแคลอรี่ที่ผู้ใช้เผาผลาญได้
                $totalCalories = $userStats->total_calories;

                // แสดงความก้าวหน้า
                $goal = (float)$this->criteria;
                $progress = ($goal > 0) ? min(100, ($totalCalories / $goal) * 100) : 0;

                // เหรียญที่ยังไม่ได้ปลดล็อค ไม่ควรแสดงเป็น 100%
                if (!$isUnlocked && $progress >= 100) {
                    // เปลี่ยนเป็นแสดง 100% แต่ต้องกดปลดล็อค
                    $progress = 100;
                }

                return 'เป้าหมาย: ' . number_format($goal, 0) . ' แคลอรี่ (' .
                       number_format($totalCalories, 0) . '/' . number_format($goal, 0) . ' แคลอรี่ - ' .
                       number_format($progress, 0) . '%)';

            case 'streak':
                // คำนวณหาจำนวนวันที่วิ่งติดต่อกัน
                $consecutiveDays = $this->calculateStreak($user->user_id);
                $goal = (int)$this->criteria;
                $progress = ($goal > 0) ? min(100, ($consecutiveDays / $goal) * 100) : 0;

                return 'เป้าหมาย: วิ่งติดต่อกัน ' . (int)$this->criteria . ' วัน (' .
                       $consecutiveDays . '/' . $goal . ' วัน - ' .
                       number_format($progress, 0) . '%)';

            case 'speed':
                // หาความเร็วเฉลี่ยสูงสุดที่เคยทำได้
                $maxSpeed = DB::table('tb_run')
                    ->where('user_id', $user->user_id)
                    ->where('is_completed', true)
                    ->max('average_speed');

                $goal = (float)$this->criteria;
                $progress = ($goal > 0 && $maxSpeed) ? min(100, ($maxSpeed / $goal) * 100) : 0;

                return 'เป้าหมาย: วิ่งด้วยความเร็ว ' . number_format($this->criteria, 1) . ' กม./ชม. (' .
                       number_format($maxSpeed ?? 0, 1) . '/' . number_format($goal, 1) . ' กม./ชม. - ' .
                       number_format($progress, 0) . '%)';

            case 'event':
                // นับจำนวนกิจกรรมที่เข้าร่วม
                $eventCount = DB::table('tb_event_users')
                    ->where('user_id', $user->user_id)
                    ->where('status', 'attended')
                    ->count();

                $goal = (int)$this->criteria;
                $progress = ($goal > 0) ? min(100, ($eventCount / $goal) * 100) : 0;

                return 'เป้าหมาย: เข้าร่วมกิจกรรม ' . (int)$this->criteria . ' ครั้ง (' .
                       $eventCount . '/' . $goal . ' ครั้ง - ' .
                       number_format($progress, 0) . '%)';

            default:
                return 'ยังไม่ได้รับ';
        }
    }

    /**
     * คำนวณจำนวนวันที่วิ่งต่อเนื่อง
     */
    private function calculateStreak($userId)
    {
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
    }

    /**
     * รายละเอียดเพิ่มเติมสำหรับแสดงในหน้าเว็บ
     */
    public function getRequirementText()
    {
        switch ($this->type) {
            case 'distance':
                return 'วิ่งให้ได้ระยะทางรวม ' . number_format($this->criteria, 1) . ' กิโลเมตร';
            case 'calories':
                return 'เผาผลาญแคลอรี่รวม ' . number_format($this->criteria, 0) . ' แคลอรี่';
            case 'streak':
                return 'วิ่งติดต่อกัน ' . (int)$this->criteria . ' วัน';
            case 'speed':
                return 'วิ่งด้วยความเร็วเฉลี่ย ' . number_format($this->criteria, 1) . ' กม./ชม.';
            case 'event':
                return 'เข้าร่วมกิจกรรม ' . (int)$this->criteria . ' ครั้ง';
            default:
                return 'ไม่มีข้อมูลเงื่อนไข';
        }
    }

    /**
     * คำนวณเปอร์เซ็นต์ความก้าวหน้าของเหรียญตรา
     * @return int ค่าเปอร์เซ็นต์ความก้าวหน้า (0-100)
     */
    public function calculateProgressPercentage()
    {
        $user = Auth::user();
        if (!$user) return 0;

        // ตรวจสอบว่าผู้ใช้ได้รับเหรียญนี้แล้วหรือไม่
        $isUnlocked = UserBadge::where('user_id', $user->user_id)
            ->where('badge_id', $this->badge_id)
            ->exists();

        // ถ้าปลดล็อคแล้ว ให้คืนค่า 100%
        if ($isUnlocked) {
            return 100;
        }

        // ดึงข้อมูลกิจกรรมทั้งหมดของผู้ใช้
        $userStats = DB::table('tb_run')
            ->where('user_id', $user->user_id)
            ->where('is_completed', true)
            ->selectRaw('SUM(distance) as total_distance, SUM(calories_burned) as total_calories, COUNT(*) as total_runs, MAX(average_speed) as max_speed')
            ->first();

        // ไม่มีกิจกรรมการวิ่ง
        if (!$userStats || $userStats->total_runs == 0) {
            return 0;
        }

        // คำนวณความก้าวหน้าตามประเภทเหรียญ
        switch ($this->type) {
            case 'distance':
                $totalDistance = floatval($userStats->total_distance ?? 0);
                $progress = ($this->criteria > 0) ? ($totalDistance / floatval($this->criteria)) * 100 : 0;
                break;

            case 'calories':
                $totalCalories = floatval($userStats->total_calories ?? 0);
                $progress = ($this->criteria > 0) ? ($totalCalories / floatval($this->criteria)) * 100 : 0;
                break;

            case 'streak':
                $consecutiveDays = $this->calculateStreak($user->user_id);
                $progress = ($this->criteria > 0) ? ($consecutiveDays / floatval($this->criteria)) * 100 : 0;
                break;

            case 'speed':
                $maxSpeed = floatval($userStats->max_speed ?? 0);
                $progress = ($this->criteria > 0 && $maxSpeed) ? ($maxSpeed / floatval($this->criteria)) * 100 : 0;
                break;

            case 'event':
                $eventCount = DB::table('tb_event_users')
                    ->where('user_id', $user->user_id)
                    ->where('status', 'attended')
                    ->count();
                $progress = ($this->criteria > 0) ? ($eventCount / floatval($this->criteria)) * 100 : 0;
                break;

            default:
                $progress = 0;
        }

        // บันทึก Log เพื่อการตรวจสอบ
        Log::info('Badge Progress Calculation', [
            'user_id' => $user->user_id,
            'badge_id' => $this->badge_id,
            'badge_name' => $this->badge_name,
            'type' => $this->type,
            'criteria' => $this->criteria,
            'progress' => $progress
        ]);

        // ถ้าความก้าวหน้ามากกว่าหรือเท่ากับ 99.5 แต่ยังไม่ได้ปลดล็อค
        // ให้คืนค่า 100 เพื่อให้ปุ่มปลดล็อคแสดง
        if ($progress >= 99.5) {
            return 100;
        }

        // ป้องกันค่าติดลบหรือเกิน 100
        return max(0, min(99, $progress));
    }

    /**
     * ตรวจสอบว่าเงื่อนไขการปลดล็อคผ่านแล้วหรือไม่
     * ตรงนี้เป็นฟังก์ชันที่ใช้เพื่อตรวจสอบว่าผู้ใช้สามารถปลดล็อคได้หรือไม่
     */
    public function isEligibleToUnlock()
    {
        $user = Auth::user();
        if (!$user) return false;

        // ถ้าปลดล็อคแล้ว ไม่ต้องตรวจสอบอีก
        if ($this->isUnlocked()) {
            return false;
        }

        $result = false;
        $details = [];

        // ตรวจสอบเงื่อนไขตามประเภทเหรียญโดยตรง
        switch ($this->type) {
            case 'distance':
                $totalDistance = DB::table('tb_run')
                    ->where('user_id', $user->user_id)
                    ->where('is_completed', true)
                    ->sum('distance');

                $details['current'] = $totalDistance;
                $details['required'] = $this->criteria;
                $result = floatval($totalDistance) >= floatval($this->criteria);
                break;

            case 'calories':
                $totalCalories = DB::table('tb_run')
                    ->where('user_id', $user->user_id)
                    ->where('is_completed', true)
                    ->sum('calories_burned');

                $details['current'] = $totalCalories;
                $details['required'] = $this->criteria;
                $result = floatval($totalCalories) >= floatval($this->criteria);
                break;

            case 'streak':
                $consecutiveDays = $this->calculateStreak($user->user_id);
                $details['current'] = $consecutiveDays;
                $details['required'] = $this->criteria;
                $result = $consecutiveDays >= intval($this->criteria);
                break;

            case 'speed':
                $maxSpeed = DB::table('tb_run')
                    ->where('user_id', $user->user_id)
                    ->where('is_completed', true)
                    ->max('average_speed');

                $details['current'] = $maxSpeed;
                $details['required'] = $this->criteria;
                $result = $maxSpeed && floatval($maxSpeed) >= floatval($this->criteria);
                break;

            case 'event':
                $eventCount = DB::table('tb_event_users')
                    ->where('user_id', $user->user_id)
                    ->where('status', 'attended')
                    ->count();

                $details['current'] = $eventCount;
                $details['required'] = $this->criteria;
                $result = $eventCount >= intval($this->criteria);
                break;

            default:
                $details['error'] = 'Unknown badge type: ' . $this->type;
                $result = false;
        }

        // Log detailed information for debugging
        Log::info('Badge Unlock Eligibility Check', [
            'badge_id' => $this->badge_id,
            'badge_name' => $this->badge_name,
            'type' => $this->type,
            'criteria' => $this->criteria,
            'user_id' => $user->user_id,
            'details' => $details,
            'is_eligible' => $result
        ]);

        return $result;
    }
}
