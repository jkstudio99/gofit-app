<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'tb_user_progress';
    protected $primaryKey = 'progress_id';
    protected $fillable = [
        'user_id',
        'period_type',
        'period_start_date',
        'total_distance',
        'total_calories',
        'total_activities'
    ];

    protected $casts = [
        'total_distance' => 'float',
        'total_calories' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}