<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $table = 'tb_reward';
    protected $primaryKey = 'reward_id';
    protected $fillable = [
        'name',
        'description',
        'required_badge_count',
        'stock',
        'image_url',
        'isenabled'
    ];

    public function redeems()
    {
        return $this->hasMany(Redeem::class, 'reward_id', 'reward_id');
    }
}