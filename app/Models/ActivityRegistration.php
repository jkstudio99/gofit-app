<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityRegistration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'activity_id',
        'status',
        'registration_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'registration_date' => 'datetime',
    ];

    /**
     * Get the user that owns the registration.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activity that this registration is for.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Check if the registration is active (not cancelled).
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'registered';
    }

    /**
     * Check if the registration is cancelled.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
