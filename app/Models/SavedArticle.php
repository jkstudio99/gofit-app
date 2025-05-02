<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedArticle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_health_article_saved';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'saved_id';

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
     * Get the article that is saved.
     */
    public function article()
    {
        return $this->belongsTo(HealthArticle::class, 'article_id', 'article_id');
    }

    /**
     * Get the user that saved the article.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
