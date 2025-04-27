<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventUser extends Model
{
    use HasFactory;

    /**
     * ชื่อตาราง
     */
    protected $table = 'event_users';

    /**
     * Primary key
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'registered_at',
        'checked_in_at',
        'checked_out_at',
        'admin_notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'registered_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    /**
     * ความสัมพันธ์กับกิจกรรม
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    /**
     * ความสัมพันธ์กับผู้ใช้
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * ตรวจสอบว่าลงทะเบียนแล้ว
     */
    public function isRegistered(): bool
    {
        return $this->status === 'registered';
    }

    /**
     * ตรวจสอบว่าเข้าร่วมกิจกรรมแล้ว
     */
    public function hasAttended(): bool
    {
        return $this->status === 'attended';
    }

    /**
     * ตรวจสอบว่ายกเลิกการลงทะเบียนแล้ว
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * ตรวจสอบว่าไม่ได้เข้าร่วม
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * สถานะการลงทะเบียนเป็นภาษาไทย
     */
    public function getStatusInThaiAttribute(): string
    {
        switch ($this->status) {
            case 'registered':
                return 'ลงทะเบียนแล้ว';
            case 'attended':
                return 'เข้าร่วมแล้ว';
            case 'absent':
                return 'ไม่ได้เข้าร่วม';
            case 'cancelled':
                return 'ยกเลิกแล้ว';
            default:
                return $this->status;
        }
    }

    /**
     * ตรวจสอบว่าสามารถเช็คอินได้หรือไม่
     */
    public function canCheckIn(): bool
    {
        // ต้องลงทะเบียนแล้ว และยังไม่เช็คอิน
        if (!$this->isRegistered() || $this->checked_in_at) {
            return false;
        }

        // ต้องเป็นวันที่จัดกิจกรรม
        return $this->event->isActive();
    }

    /**
     * สถานะที่เป็นไปได้
     */
    public static function getStatuses()
    {
        return [
            'registered' => 'ลงทะเบียนแล้ว',
            'cancelled' => 'ยกเลิกแล้ว',
            'attended' => 'เข้าร่วมแล้ว',
            'absent' => 'ไม่ได้เข้าร่วม'
        ];
    }

    /**
     * แปลงสถานะเป็นข้อความภาษาไทย
     */
    public function getStatusTextAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Badge HTML สำหรับสถานะ
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'registered':
                return '<span class="badge bg-primary">ลงทะเบียนแล้ว</span>';
            case 'cancelled':
                return '<span class="badge bg-secondary">ยกเลิกแล้ว</span>';
            case 'attended':
                return '<span class="badge bg-success">เข้าร่วมแล้ว</span>';
            case 'absent':
                return '<span class="badge bg-danger">ไม่ได้เข้าร่วม</span>';
            default:
                return '<span class="badge bg-dark">' . $this->status . '</span>';
        }
    }
}
