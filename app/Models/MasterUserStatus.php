<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUserStatus extends Model
{
    use HasFactory;

    protected $table = 'tb_master_user_status';
    protected $primaryKey = 'user_status_id';
    protected $fillable = ['user_status_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'user_status_id', 'user_status_id');
    }
}