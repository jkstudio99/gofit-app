<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tb_user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_type_id',
        'user_status_id',
        'username',
        'password',
        'firstname',
        'lastname',
        'email',
        'telephone',
        'gmail_user_id',
        'facebook_user_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'password' => 'hashed',
    ];

    public function userType()
    {
        return $this->belongsTo(MasterUserType::class, 'user_type_id', 'user_type_id');
    }

    public function userStatus()
    {
        return $this->belongsTo(MasterUserStatus::class, 'user_status_id', 'user_status_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'user_id', 'user_id');
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'tb_user_badge', 'user_id', 'badge_id', 'user_id', 'badge_id')
                    ->withPivot('user_badge_id', 'created_at', 'updated_at');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tb_user_role', 'user_id', 'role_id', 'user_id', 'role_id')
                    ->withPivot('user_role_id', 'created_at', 'updated_at');
    }

    public function redeems()
    {
        return $this->hasMany(Redeem::class, 'user_id', 'user_id');
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class, 'user_id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    public function externalServices()
    {
        return $this->hasMany(ExternalService::class, 'user_id', 'user_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับกิจกรรม (many-to-many)
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_users', 'user_id', 'event_id')
            ->withPivot('status', 'registered_at')
            ->withTimestamps();
    }

    /**
     * ความสัมพันธ์กับกิจกรรมที่ลงทะเบียน
     */
    public function eventRegistrations()
    {
        return $this->hasMany(EventUser::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับกิจกรรมที่สร้าง
     */
    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'created_by', 'user_id');
    }

    /**
     * Get all of the user's activity goals.
     */
    public function activityGoals(): HasMany
    {
        return $this->hasMany(ActivityGoal::class);
    }
}
