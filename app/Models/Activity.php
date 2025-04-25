<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'tb_activity';
    protected $primaryKey = 'activity_id';
    protected $fillable = [
        'user_id',
        'activity_type',
        'start_time',
        'end_time',
        'distance',
        'calories_burned',
        'average_speed',
        'heart_rate_avg',
        'route_gps_data'
    ];

    protected $casts = [
        'route_gps_data' => 'json',
        'distance' => 'float',
        'calories_burned' => 'float',
        'average_speed' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}