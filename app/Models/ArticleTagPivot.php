<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleTagPivot extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_health_article_tag';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}
