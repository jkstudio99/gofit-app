<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $table = 'tb_user_badge';
    protected $primaryKey = 'user_badge_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at'
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id', 'badge_id');
    }
}
