<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalService extends Model
{
    use HasFactory;

    protected $table = 'tb_external_service';
    protected $primaryKey = 'service_id';
    protected $fillable = [
        'user_id',
        'service_name',
        'access_token',
        'refresh_token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}