<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Run extends Model
{
    use HasFactory;

    /**
     * ตารางที่เชื่อมโยงกับโมเดล
     *
     * @var string
     */
    protected $table = 'tb_run';

    /**
     * Primary key ของตาราง
     *
     * @var string
     */
    protected $primaryKey = 'run_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'distance',
        'duration',
        'calories_burned',
        'average_speed',
        'start_time',
        'end_time',
        'route_data',
        'is_shared',
        'is_completed',
        'is_paused',
        'is_test'
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
        'average_speed' => 'decimal:2',
        'calories_burned' => 'integer',
        'route_data' => 'array',
        'is_shared' => 'boolean',
        'is_completed' => 'boolean',
        'is_paused' => 'boolean',
    ];

    /**
     * Get the user that owns the run.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Calculate the pace for the run.
     * Returns pace in minutes per kilometer.
     *
     * @return float|null
     */
    public function getPaceAttribute(): ?float
    {
        if ($this->distance && $this->distance > 0 && $this->duration) {
            // Duration is expected to be in seconds
            return round(($this->duration / 60) / $this->distance, 2);
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
     * Get runs between dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }

    /**
     * Get shared runs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }

    /**
     * Get completed runs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }
}
