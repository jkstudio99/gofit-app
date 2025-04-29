<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTag extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article_tags';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'tag_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_name',
        'tag_slug',
    ];

    /**
     * Get the articles that belong to the tag.
     */
    public function articles()
    {
        return $this->belongsToMany(HealthArticle::class, 'article_tag', 'tag_id', 'article_id')
                    ->withTimestamps();
    }
}
