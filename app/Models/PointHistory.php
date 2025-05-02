<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    protected $table = 'tb_point_history';
    protected $primaryKey = 'point_history_id';

    protected $fillable = [
        'user_id',
        'points',
        'description',
        'source_type',
        'source_id',
    ];

    /**
     * Get the user that owns the point history entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the source model (polymorphic relationship).
     */
    public function source()
    {
        if ($this->source_type === 'badge') {
            return $this->belongsTo(Badge::class, 'source_id', 'badge_id');
        } elseif ($this->source_type === 'reward') {
            return $this->belongsTo(Reward::class, 'source_id', 'reward_id');
        } elseif ($this->source_type === 'event') {
            return $this->belongsTo(Event::class, 'source_id', 'event_id');
        }

        return null;
    }
}
