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
            'distance' => 'Distance (km)',
            'duration' => 'Duration (minutes)',
            'calories' => 'Calories burned',
            'frequency' => 'Number of workouts'
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
            return 'Any activity';
        }

        $types = [
            'run' => 'Running',
            'walk' => 'Walking',
            'cycle' => 'Cycling',
            'swim' => 'Swimming',
            'gym' => 'Gym Workout',
            'yoga' => 'Yoga',
            'hiit' => 'HIIT',
            'other' => 'Other'
        ];

        return $types[$this->activity_type] ?? $this->activity_type;
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
        return !$this->is_completed && $this->end_date && $this->end_date->isPast();
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
        if ($this->activity_type && $activity->activity_type !== $this->activity_type) {
            return false;
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
