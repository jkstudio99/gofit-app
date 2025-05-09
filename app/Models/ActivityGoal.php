<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityGoal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_activity_goals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'activity_type',
        'target_value',
        'current_value',
        'period',
        'start_date',
        'end_date',
        'is_completed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_completed' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'current_value' => 0,
        'is_completed' => false,
    ];

    /**
     * Get the user that owns the activity goal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formatted goal type.
     *
     * @return string
     */
    public function getFormattedTypeAttribute(): string
    {
        $types = [
            'distance' => 'ระยะทาง (กม.)',
            'duration' => 'ระยะเวลา (นาที)',
            'calories' => 'แคลอรี่ที่เผาผลาญ',
            'frequency' => 'จำนวนครั้งการออกกำลังกาย'
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Get the type label for display in UI.
     *
     * @return string
     */
    public function getTypeLabel(): string
    {
        $types = [
            'distance' => 'ระยะทาง (กม.)',
            'duration' => 'ระยะเวลา (นาที)',
            'calories' => 'แคลอรี่ที่เผาผลาญ',
            'frequency' => 'จำนวนครั้งการออกกำลังกาย'
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Get the formatted activity type.
     *
     * @return string
     */
    public function getFormattedActivityTypeAttribute(): string
    {
        if (empty($this->activity_type)) {
            return 'ทุกกิจกรรม';
        }

        $types = [
            'running' => 'วิ่ง',
            'walking' => 'เดิน',
            'cycling' => 'ปั่นจักรยาน',
            'swimming' => 'ว่ายน้ำ',
            'gym' => 'ออกกำลังกายในยิม',
            'yoga' => 'โยคะ',
            'hiit' => 'HIIT',
            'other' => 'อื่นๆ',
            // For backward compatibility with older data
            'run' => 'วิ่ง',
            'walk' => 'เดิน',
            'cycle' => 'ปั่นจักรยาน',
            'swim' => 'ว่ายน้ำ',
            // เพิ่มประเภทกิจกรรมวิ่ง
            'running_health' => 'วิ่งเพื่อสุขภาพ',
            'running_other' => 'วิ่งอื่นๆ'
        ];

        return $types[$this->activity_type] ?? $this->activity_type;
    }

    /**
     * Get the activity type label for display in UI.
     *
     * @return string
     */
    public function getActivityTypeLabel(): string
    {
        if (empty($this->activity_type)) {
            return 'ทุกกิจกรรม';
        }

        $types = [
            'running' => 'วิ่ง',
            'walking' => 'เดิน',
            'cycling' => 'ปั่นจักรยาน',
            'swimming' => 'ว่ายน้ำ',
            'gym' => 'ออกกำลังกายในยิม',
            'yoga' => 'โยคะ',
            'hiit' => 'HIIT',
            'other' => 'อื่นๆ',
            // For backward compatibility with older data
            'run' => 'วิ่ง',
            'walk' => 'เดิน',
            'cycle' => 'ปั่นจักรยาน',
            'swim' => 'ว่ายน้ำ',
            // เพิ่มประเภทกิจกรรมวิ่ง
            'running_health' => 'วิ่งเพื่อสุขภาพ',
            'running_other' => 'วิ่งอื่นๆ'
        ];

        return $types[$this->activity_type] ?? $this->activity_type;
    }

    /**
     * Get the unit label based on goal type.
     *
     * @return string
     */
    public function getUnitLabel(): string
    {
        $units = [
            'distance' => 'กม.',
            'duration' => 'นาที',
            'calories' => 'แคลอรี่',
            'frequency' => 'ครั้ง'
        ];

        return $units[$this->type] ?? '';
    }

    /**
     * Get the period label for display in UI.
     *
     * @return string
     */
    public function getPeriodLabel(): string
    {
        $periods = [
            'daily' => 'รายวัน',
            'weekly' => 'รายสัปดาห์',
            'monthly' => 'รายเดือน',
            'custom' => 'กำหนดเอง'
        ];

        return $periods[$this->period] ?? $this->period;
    }

    /**
     * Get the current value of the goal.
     *
     * @return float
     */
    public function getCurrentValue(): float
    {
        return (float) $this->current_value;
    }

    /**
     * Get the remaining amount to complete the goal.
     *
     * @return float
     */
    public function getRemaining(): float
    {
        $remaining = max(0, (float) $this->target_value - (float) $this->current_value);
        return round($remaining, 2);
    }

    /**
     * Get the number of days remaining until the goal's end date.
     *
     * @return int|null
     */
    public function getRemainingDays(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        $today = now()->startOfDay();
        $endDate = $this->end_date->startOfDay();

        if ($today > $endDate) {
            return 0;
        }

        return $today->diffInDays($endDate);
    }

    /**
     * Get the status label for display in UI.
     *
     * @return string
     */
    public function getStatusLabel(): string
    {
        if ($this->is_completed) {
            return 'สำเร็จแล้ว';
        }

        if ($this->isExpired()) {
            return 'หมดเวลา';
        }

        return 'กำลังดำเนินการ';
    }

    /**
     * Get the CSS class for the status badge.
     *
     * @return string
     */
    public function getStatusBadgeClass(): string
    {
        if ($this->is_completed) {
            return 'bg-success';
        }

        if ($this->isExpired()) {
            return 'bg-danger';
        }

        return 'bg-primary';
    }

    /**
     * Check if the goal is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    /**
     * Get the progress percentage.
     *
     * @return int
     */
    public function getProgressPercentage(): int
    {
        if ($this->target_value <= 0) {
            return 0;
        }

        return min(100, (int) round(($this->current_value / $this->target_value) * 100));
    }

    /**
     * Get the progress percentage.
     *
     * @return int
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->target_value <= 0) {
            return 0;
        }

        return min(100, (int) round(($this->current_value / $this->target_value) * 100));
    }

    /**
     * Check if the goal has expired.
     *
     * @return bool
     */
    public function getIsExpiredAttribute(): bool
    {
        return !$this->is_completed && $this->end_date && $this->end_date->endOfDay()->isPast();
    }

    /**
     * Check if the goal has expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return !$this->is_completed && $this->end_date && $this->end_date->endOfDay()->isPast();
    }

    /**
     * Update the goal's progress based on a new activity.
     *
     * @param Activity $activity
     * @return bool True if the goal was updated
     */
    public function updateProgress(Activity $activity): bool
    {
        // Skip if goal is already completed
        if ($this->is_completed) {
            return false;
        }

        // Check if activity is within the goal date range
        if ($this->start_date && $activity->started_at->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $activity->started_at->gt($this->end_date)) {
            return false;
        }

        // Check if activity type matches (if specific type is set)
        if ($this->activity_type) {
            // Map old activity types to new format for compatibility
            $activityTypeMap = [
                'run' => 'running',
                'walk' => 'walking',
                'cycle' => 'cycling',
                'swim' => 'swimming'
            ];

            $goalActivityType = $activityTypeMap[$this->activity_type] ?? $this->activity_type;
            $activityType = $activityTypeMap[$activity->activity_type] ?? $activity->activity_type;

            if ($goalActivityType !== $activityType) {
            return false;
            }
        }

        // Update progress based on goal type
        $updated = false;

        switch ($this->type) {
            case 'distance':
                if ($activity->distance) {
                    $this->current_value += $activity->distance;
                    $updated = true;
                }
                break;
            case 'duration':
                if ($activity->duration) {
                    $this->current_value += $activity->duration;
                    $updated = true;
                }
                break;
            case 'calories':
                if ($activity->calories) {
                    $this->current_value += $activity->calories;
                    $updated = true;
                }
                break;
            case 'frequency':
                $this->current_value += 1;
                $updated = true;
                break;
        }

        // Check if goal is now completed
        if ($updated && $this->current_value >= $this->target_value) {
            $this->is_completed = true;
        }

        if ($updated) {
            $this->save();
        }

        return $updated;
    }

    /**
     * Get goals by type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get active goals (not completed and not expired).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_completed', false)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }
}
