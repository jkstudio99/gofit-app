<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'tb_session';
    protected $primaryKey = 'session_id';
    protected $fillable = ['user_id', 'session_token', 'ip_address', 'user_agent', 'expired_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'expired_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * รายละเอียดอุปกรณ์ที่ใช้งานในรูปแบบที่อ่านได้
     *
     * @return string
     */
    public function getDeviceInfoAttribute()
    {
        // ตรวจสอบ user agent และดึงข้อมูลอุปกรณ์
        $agent = $this->user_agent;
        $device = 'ไม่ทราบอุปกรณ์';

        // ตรวจสอบอุปกรณ์มือถือ
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $agent)) {
            $device = 'มือถือ';
        }
        // ตรวจสอบแท็บเล็ต
        else if (preg_match('/android|ipad|playbook|silk/i', $agent)) {
            $device = 'แท็บเล็ต';
        }
        // ตรวจสอบเดสก์ท็อป
        else {
            $device = 'คอมพิวเตอร์';
        }

        // ตรวจสอบระบบปฏิบัติการ
        $os = 'ไม่ทราบระบบปฏิบัติการ';
        if (preg_match('/windows/i', $agent)) {
            $os = 'Windows';
        } else if (preg_match('/macintosh|mac os x/i', $agent)) {
            $os = 'MacOS';
        } else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } else if (preg_match('/iphone|ipad/i', $agent)) {
            $os = 'iOS';
        } else if (preg_match('/android/i', $agent)) {
            $os = 'Android';
        }

        // ตรวจสอบเบราว์เซอร์
        $browser = 'ไม่ทราบเบราว์เซอร์';
        if (preg_match('/MSIE|Trident/i', $agent)) {
            $browser = 'Internet Explorer';
        } else if (preg_match('/Firefox/i', $agent)) {
            $browser = 'Firefox';
        } else if (preg_match('/Chrome/i', $agent)) {
            $browser = 'Chrome';
        } else if (preg_match('/Safari/i', $agent)) {
            $browser = 'Safari';
        } else if (preg_match('/Opera|OPR/i', $agent)) {
            $browser = 'Opera';
        } else if (preg_match('/Edge/i', $agent)) {
            $browser = 'Edge';
        }

        return "$device - $os - $browser";
    }

    /**
     * ตรวจสอบว่า session นี้คือ session ปัจจุบันหรือไม่
     *
     * @return bool
     */
    public function isCurrentSession()
    {
        $currentToken = request()->cookie('session_token');
        return $this->session_token === $currentToken;
    }
}