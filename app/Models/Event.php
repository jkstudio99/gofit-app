<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    /**
     * ชื่อตาราง
     */
    protected $table = 'events';

    /**
     * Primary key
     */
    protected $primaryKey = 'event_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_datetime',
        'end_datetime',
        'capacity',
        'image_url',
        'created_by',
        'status'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'capacity' => 'integer',
    ];

    /**
     * อาจารย์หรือผู้ดูแลที่สร้างกิจกรรม
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * ผู้ใช้ที่ลงทะเบียนเข้าร่วมกิจกรรม
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_users', 'event_id', 'user_id')
            ->withPivot('status', 'registered_at')
            ->withTimestamps();
    }

    /**
     * ผู้ใช้ที่ลงทะเบียนและยังไม่ยกเลิก
     */
    public function activeParticipants()
    {
        return $this->participants()->wherePivot('status', 'registered');
    }

    /**
     * ข้อมูลการลงทะเบียนทั้งหมด
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventUser::class, 'event_id', 'event_id');
    }

    /**
     * นับจำนวนผู้ลงทะเบียนที่ยังไม่ยกเลิก
     */
    public function getActiveRegistrationsCountAttribute(): int
    {
        return $this->registrations()
            ->whereIn('status', ['registered', 'attended'])
            ->count();
    }

    /**
     * ตรวจสอบว่าสามารถลงทะเบียนได้หรือไม่
     */
    public function canRegister(): bool
    {
        // ต้องเป็นกิจกรรมที่เผยแพร่แล้ว
        if ($this->status !== 'published') {
            return false;
        }

        // ต้องยังไม่สิ้นสุดกิจกรรม
        if ($this->hasEnded()) {
            return false;
        }

        // ต้องยังมีที่ว่าง
        if ($this->capacity > 0 && $this->active_registrations_count >= $this->capacity) {
            return false;
        }

        return true;
    }

    /**
     * ตรวจสอบว่ากิจกรรมเริ่มแล้วหรือไม่
     */
    public function hasStarted(): bool
    {
        return $this->start_datetime <= now();
    }

    /**
     * ตรวจสอบว่ากิจกรรมสิ้นสุดแล้วหรือไม่
     */
    public function hasEnded(): bool
    {
        return $this->end_datetime < now();
    }

    /**
     * ตรวจสอบว่ากำลังจัดกิจกรรมอยู่หรือไม่
     */
    public function isActive(): bool
    {
        return $this->hasStarted() && !$this->hasEnded();
    }

    /**
     * คำนวณจำนวนที่ว่างเหลือ
     */
    public function getAvailableSpotsAttribute(): int
    {
        if ($this->capacity <= 0) {
            return PHP_INT_MAX; // ไม่จำกัดจำนวน
        }

        $remaining = $this->capacity - $this->active_registrations_count;
        return max(0, $remaining);
    }

    /**
     * แปลงวันที่เป็นรูปแบบภาษาไทย
     */
    public function getThaiDateRangeAttribute(): string
    {
        $start = $this->start_datetime->locale('th');
        $end = $this->end_datetime->locale('th');

        if ($start->isSameDay($end)) {
            // กรณีจัดในวันเดียวกัน
            return $start->translatedFormat('d M Y') .
                   ' เวลา ' . $start->format('H:i') . ' - ' . $end->format('H:i') . ' น.';
        } else {
            // กรณีจัดหลายวัน
            return $start->translatedFormat('d M Y H:i') . ' - ' .
                   $end->translatedFormat('d M Y H:i') . ' น.';
        }
    }

    /**
     * ช่วงเวลาที่เริ่มต้น (แบบย่อ)
     */
    public function getShortDateAttribute(): string
    {
        return $this->start_datetime->locale('th')->translatedFormat('d M Y');
    }

    /**
     * สถานะการแสดงผล
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->hasEnded()) {
            return '<span class="badge bg-secondary">สิ้นสุดแล้ว</span>';
        } elseif ($this->isActive()) {
            return '<span class="badge bg-success">กำลังดำเนินการ</span>';
        } elseif ($this->status === 'published' && $this->capacity > 0 && $this->active_registrations_count >= $this->capacity) {
            return '<span class="badge bg-warning">เต็มแล้ว</span>';
        } elseif ($this->status === 'published') {
            return '<span class="badge bg-primary">เปิดรับสมัคร</span>';
        } elseif ($this->status === 'draft') {
            return '<span class="badge bg-info">ร่าง</span>';
        } else {
            return '<span class="badge bg-dark">' . $this->status . '</span>';
        }
    }

    /**
     * ดึง URL ของรูปภาพกิจกรรม
     */
    public function getImageUrlAttribute()
    {
        if ($this->attributes['image_url'] ?? null) {
            return Storage::url($this->attributes['image_url']);
        }

        return asset('images/default-event.jpg');
    }

    /**
     * จำนวนผู้ลงทะเบียนปัจจุบัน
     */
    public function getCurrentParticipantsCountAttribute()
    {
        return $this->participants()
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    /**
     * ตรวจสอบว่ากิจกรรมมีที่นั่งเต็มแล้วหรือไม่
     */
    public function isFull()
    {
        return $this->active_registrations_count >= $this->capacity;
    }

    /**
     * สถานะของกิจกรรมเป็นภาษาไทย
     */
    public function getStatusInThaiAttribute()
    {
        switch ($this->status) {
            case 'published':
                return 'เผยแพร่';
            case 'draft':
                return 'ฉบับร่าง';
            case 'cancelled':
                return 'ยกเลิก';
            default:
                return $this->status;
        }
    }

    /**
     * จำนวนวันที่เหลือก่อนเริ่มกิจกรรม
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->hasStarted()) {
            return 0;
        }

        return Carbon::now()->diffInDays($this->start_datetime);
    }

    /**
     * ตรวจสอบว่าผู้ใช้ปัจจุบันได้ลงทะเบียนเข้าร่วมกิจกรรมนี้หรือไม่
     */
    public function isRegistered()
    {
        $user = Auth::user();
        if (!$user) return false;

        return $this->participants()->wherePivot('user_id', $user->user_id)->exists();
    }

    /**
     * คืนค่าจำนวนที่นั่งที่เหลือ
     */
    public function remainingSlots()
    {
        if ($this->capacity <= 0) return 'ไม่จำกัด';

        $currentParticipants = $this->participants()->where('status', '!=', 'cancelled')->count();
        return max(0, $this->capacity - $currentParticipants);
    }

    /**
     * คืนค่าบริเวณสถานะของกิจกรรม (ยังไม่เริ่ม, กำลังดำเนินการ, สิ้นสุดแล้ว)
     */
    public function getEventProgress()
    {
        $now = Carbon::now();

        if ($now->isBefore($this->start_datetime)) {
            return 'ยังไม่เริ่ม';
        } elseif ($now->isAfter($this->end_datetime)) {
            return 'สิ้นสุดแล้ว';
        } else {
            return 'กำลังดำเนินการ';
        }
    }
}
