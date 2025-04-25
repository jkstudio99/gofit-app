<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'tb_session';
    protected $primaryKey = 'session_id';
    protected $fillable = ['user_id', 'session_token', 'expired_at'];

    protected $dates = [
        'created_at',
        'expired_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}