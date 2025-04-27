<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    /**
     * ตารางที่เชื่อมโยงกับโมเดล
     *
     * @var string
     */
    protected $table = 'tb_activity';

    /**
     * Primary key ของตาราง
     *
     * @var string
     */
    protected $primaryKey = 'activity_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'distance',
        'duration',
        'calories',
        'started_at',
        'ended_at',
        'notes',
        'details',
    ];

    /**
     * คุณลักษณะที่ควรจะแปลงเป็นวันที่
     *
     * @var array
     */
    protected $dates = [
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'distance' => 'decimal:2',
        'duration' => 'integer',
        'calories' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'details' => 'array',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the pace for distance-based activities.
     * Returns pace in minutes per kilometer.
     *
     * @return float|null
     */
    public function getPaceAttribute(): ?float
    {
        if ($this->distance && $this->distance > 0 && $this->duration) {
            return round($this->duration / $this->distance, 2);
        }

        return null;
    }

    /**
     * Get the formatted pace (minutes:seconds per km)
     *
     * @return string|null
     */
    public function getFormattedPaceAttribute(): ?string
    {
        $pace = $this->getPaceAttribute();

        if ($pace) {
            $minutes = floor($pace);
            $seconds = round(($pace - $minutes) * 60);

            return sprintf('%d:%02d /km', $minutes, $seconds);
        }

        return null;
    }

    /**
     * Get activities by type.
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
     * Get activities within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }
}
