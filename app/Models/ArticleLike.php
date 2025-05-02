<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleLike extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_health_article_likes';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'like_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id',
        'user_id',
    ];

    /**
     * Get the article that owns the like.
     */
    public function article()
    {
        return $this->belongsTo(HealthArticle::class, 'article_id', 'article_id');
    }

    /**
     * Get the user that liked the article.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
