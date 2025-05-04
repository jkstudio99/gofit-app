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
    protected $table = 'tb_health_article_tag';

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
     * The articles that belong to the tag.
     */
    public function articles()
    {
        return $this->belongsToMany(HealthArticle::class, 'tb_health_article_tag', 'tag_id', 'article_id')
                    ->withTimestamps();
    }
}
