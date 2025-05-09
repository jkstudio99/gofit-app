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
        'required_badge_count',
        'stock',
        'image_url',
        'is_enabled',
        'is_active',
    ];

    /**
     * Accessor สำหรับ point_cost
     *
     * @return int
     */
    public function getPointCostAttribute()
    {
        return $this->points_required;
    }

    /**
     * Mutator สำหรับ point_cost
     *
     * @param int $value
     * @return void
     */
    public function setPointCostAttribute($value)
    {
        $this->attributes['points_required'] = $value;
    }

    /**
     * Accessor สำหรับ quantity
     *
     * @return int
     */
    public function getQuantityAttribute()
    {
        return $this->stock;
    }

    /**
     * Mutator สำหรับ quantity
     *
     * @param int $value
     * @return void
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['stock'] = $value;
    }

    /**
     * Accessor สำหรับ image_path
     *
     * @return string|null
     */
    public function getImagePathAttribute()
    {
        return $this->image_url;
    }

    /**
     * Mutator สำหรับ image_path
     *
     * @param string $value
     * @return void
     */
    public function setImagePathAttribute($value)
    {
        $this->attributes['image_url'] = $value;
    }

    /**
     * Get the redeems for the reward.
     */
    public function redeems()
    {
        return $this->hasMany(Redeem::class, 'reward_id', 'reward_id');
    }
}
