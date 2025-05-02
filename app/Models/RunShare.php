<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RunShare extends Model
{
    use HasFactory;

    /**
     * ตารางที่เชื่อมโยงกับโมเดล
     *
     * @var string
     */
    protected $table = 'tb_run_shares';

    /**
     * Primary key ของตาราง
     *
     * @var string
     */
    protected $primaryKey = 'share_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'run_id',
        'user_id',
        'shared_with_user_id',
        'share_message',
        'is_viewed'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_viewed' => 'boolean',
    ];

    /**
     * Get the run that was shared.
     */
    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class, 'run_id', 'run_id');
    }

    /**
     * Get the user who shared the run.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the user with whom the run was shared.
     */
    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id', 'user_id');
    }

    /**
     * Get unviewed shares.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnviewed($query)
    {
        return $query->where('is_viewed', false);
    }
}
