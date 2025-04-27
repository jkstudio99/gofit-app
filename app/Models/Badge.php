<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\UserBadge;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;

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

        // แสดงเงื่อนไขที่แตกต่างกันตามประเภทของเหรียญ
        switch ($this->type) {
            case 'distance':
                // รวมระยะทางที่ผู้ใช้วิ่งได้
                $totalDistance = Activity::where('user_id', $user->user_id)
                    ->where('activity_type', 'running')
                    ->where('is_test', false)
                    ->sum('distance');

                // แสดงความก้าวหน้า
                $goal = $this->criteria;
                $progress = min(100, ($totalDistance / $goal) * 100);

                return 'เป้าหมาย: ' . number_format($goal, 1) . ' กม. (' .
                       number_format($totalDistance, 1) . '/' . number_format($goal, 1) . ' กม. - ' .
                       number_format($progress, 0) . '%)';

            case 'calories':
                // รวมแคลอรี่ที่ผู้ใช้เผาผลาญได้
                $totalCalories = Activity::where('user_id', $user->user_id)
                    ->where('activity_type', 'running')
                    ->where('is_test', false)
                    ->sum('calories_burned');

                // แสดงความก้าวหน้า
                $goal = $this->criteria;
                $progress = min(100, ($totalCalories / $goal) * 100);

                return 'เป้าหมาย: ' . number_format($goal, 0) . ' แคลอรี่ (' .
                       number_format($totalCalories, 0) . '/' . number_format($goal, 0) . ' แคลอรี่ - ' .
                       number_format($progress, 0) . '%)';

            case 'streak':
                return 'เป้าหมาย: วิ่งติดต่อกัน ' . (int)$this->criteria . ' วัน';

            case 'speed':
                return 'เป้าหมาย: วิ่งด้วยความเร็ว ' . number_format($this->criteria, 1) . ' กม./ชม.';

            case 'event':
                return 'เป้าหมาย: เข้าร่วมกิจกรรม ' . (int)$this->criteria . ' ครั้ง';

            default:
                return 'ยังไม่ได้รับ';
        }
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
}
