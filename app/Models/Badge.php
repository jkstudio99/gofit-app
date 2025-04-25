<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $table = 'tb_badge';
    protected $primaryKey = 'badge_id';
    protected $fillable = [
        'name',
        'description',
        'calories_required',
        'image_url',
        'isenabled'
    ];

    protected $casts = [
        'calories_required' => 'float',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tb_user_badge', 'badge_id', 'user_id', 'badge_id', 'user_id')
                    ->withPivot('user_badge_id', 'created_at', 'updated_at');
    }
}