<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthArticle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_health_articles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'article_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'thumbnail',
        'category_id',
        'user_id',
        'is_published',
        'published_at',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the category that owns the article.
     */
    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'category_id');
    }

    /**
     * Get the user that owns the article.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the comments for the article.
     */
    public function comments()
    {
        return $this->hasMany(ArticleComment::class, 'article_id', 'article_id');
    }

    /**
     * Get the likes for the article.
     */
    public function likes()
    {
        return $this->hasMany(ArticleLike::class, 'article_id', 'article_id');
    }

    /**
     * Get the users who saved this article.
     */
    public function savedBy()
    {
        return $this->belongsToMany(User::class, 'tb_health_article_saved', 'article_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Get the tags for the article.
     */
    public function tags()
    {
        return $this->belongsToMany(ArticleTag::class, 'tb_health_article_tag', 'article_id', 'tag_id')
                    ->withTimestamps();
    }

    /**
     * Get the shares for the article.
     */
    public function shares()
    {
        return $this->hasMany(ArticleShare::class, 'article_id', 'article_id');
    }

    /**
     * Scope a query to only include published articles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Check if a user has liked this article.
     *
     * @param int $userId
     * @return bool
     */
    public function isLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Check if a user has saved this article.
     *
     * @param int $userId
     * @return bool
     */
    public function isSavedByUser($userId)
    {
        return $this->savedBy()->where('user_id', $userId)->exists();
    }

    /**
     * Increment the view count.
     *
     * @return void
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
