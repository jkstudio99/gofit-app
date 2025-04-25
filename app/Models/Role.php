<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'tb_role';
    protected $primaryKey = 'role_id';
    protected $fillable = ['role_name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tb_user_role', 'role_id', 'user_id', 'role_id', 'user_id')
                    ->withPivot('user_role_id', 'created_at', 'updated_at');
    }
}