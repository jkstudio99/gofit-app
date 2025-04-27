<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_reward';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'reward_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'points_required',
        'quantity',
        'image_path',
        'is_enabled',
    ];

    /**
     * Get the redeems for the reward.
     */
    public function redeems()
    {
        return $this->hasMany(Redeem::class, 'reward_id', 'reward_id');
    }
}
