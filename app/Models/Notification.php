<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'tb_notification';
    protected $primaryKey = 'notification_id';
    protected $fillable = ['user_id', 'message', 'read_at'];

    protected $dates = [
        'created_at',
        'read_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}