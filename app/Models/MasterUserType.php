<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUserType extends Model
{
    use HasFactory;

    protected $table = 'tb_master_user_type';
    protected $primaryKey = 'user_type_id';
    protected $fillable = ['user_typename', 'keywords'];

    public function users()
    {
        return $this->hasMany(User::class, 'user_type_id', 'user_type_id');
    }
}