<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tour_key',
        'status',
        'completed_at',
        'show_again',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'show_again' => 'boolean',
    ];

    /**
     * ความสัมพันธ์กับผู้ใช้
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * ตรวจสอบว่าทัวร์เสร็จสิ้นแล้วหรือไม่
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * ตรวจสอบว่าทัวร์ถูกข้ามหรือไม่
     */
    public function isSkipped(): bool
    {
        return $this->status === 'skipped';
    }

    /**
     * ตรวจสอบว่าทัวร์รอการแสดงหรือไม่
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * ทำเครื่องหมายว่าทัวร์เสร็จสิ้นแล้ว
     */
    public function markAsCompleted(): self
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();

        return $this;
    }

    /**
     * ทำเครื่องหมายว่าทัวร์ถูกข้าม
     */
    public function markAsSkipped(): self
    {
        $this->status = 'skipped';
        $this->save();

        return $this;
    }
}
