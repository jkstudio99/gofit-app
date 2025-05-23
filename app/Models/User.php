<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\OnboardingTour;

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
        'points',
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

    /**
     * Get the runs for the user
     */
    public function runs(): HasMany
    {
        return $this->hasMany(Run::class, 'user_id', 'user_id');
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

    public function pointHistory()
    {
        return $this->hasMany(PointHistory::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับกิจกรรม (many-to-many)
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'tb_event_users', 'user_id', 'event_id')
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

    /**
     * Get the health articles authored by the user.
     */
    public function healthArticles()
    {
        return $this->hasMany(HealthArticle::class, 'user_id', 'user_id');
    }

    /**
     * Get the article comments created by the user.
     */
    public function articleComments()
    {
        return $this->hasMany(ArticleComment::class, 'user_id', 'user_id');
    }

    /**
     * Get the article likes by the user.
     */
    public function articleLikes()
    {
        return $this->hasMany(ArticleLike::class, 'user_id', 'user_id');
    }

    /**
     * Get the articles saved by the user.
     */
    public function savedArticles()
    {
        return $this->belongsToMany(HealthArticle::class, 'tb_health_article_saved', 'user_id', 'article_id')
                    ->withTimestamps();
    }

    /**
     * Get the article shares by the user.
     */
    public function articleShares()
    {
        return $this->hasMany(ArticleShare::class, 'user_id', 'user_id');
    }

    /**
     * ตรวจสอบว่าผู้ใช้มีสิทธิ์ Admin หรือไม่
     */
    public function isAdmin()
    {
        return $this->user_type_id == 1;
    }

    /**
     * Get the available points for this user (current points that can be used)
     *
     * @return int
     */
    public function getAvailablePoints()
    {
        // Get the points from the user's current points value
        return $this->points ?? 0;
    }

    /**
     * ความสัมพันธ์กับการแนะนำการใช้งาน
     */
    public function onboardingTours()
    {
        return $this->hasMany(OnboardingTour::class, 'user_id', 'user_id');
    }

    /**
     * ตรวจสอบว่าทัวร์ถูกเสร็จสิ้นแล้วหรือไม่
     */
    public function hasTourCompleted(string $tourKey): bool
    {
        return $this->onboardingTours()
            ->where('tour_key', $tourKey)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * ตรวจสอบว่าควรแสดงทัวร์หรือไม่
     */
    public function shouldShowTour(string $tourKey): bool
    {
        $tour = $this->onboardingTours()
            ->where('tour_key', $tourKey)
            ->first();

        if (!$tour) {
            return true;
        }

        return $tour->status === 'pending' || $tour->show_again;
    }
}
