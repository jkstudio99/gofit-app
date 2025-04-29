<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleShare extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article_shares';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'share_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id',
        'user_id',
        'platform',
    ];

    /**
     * Get the article that is shared.
     */
    public function article()
    {
        return $this->belongsTo(HealthArticle::class, 'article_id', 'article_id');
    }

    /**
     * Get the user that shared the article.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
