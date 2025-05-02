<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // เปลี่ยนชื่อตารางบทความทั้งหมดเป็น tb_health_article_xxx
        Schema::rename('tb_article_categories', 'tb_health_article_categories');
        Schema::rename('tb_article_comments', 'tb_health_article_comments');
        Schema::rename('tb_article_likes', 'tb_health_article_likes');
        Schema::rename('tb_article_shares', 'tb_health_article_shares');
        Schema::rename('tb_article_tag', 'tb_health_article_tag');
        Schema::rename('tb_article_tags', 'tb_health_article_tags');
        Schema::rename('tb_saved_articles', 'tb_health_article_saved');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // เปลี่ยนชื่อกลับเป็น tb_article_xxx
        Schema::rename('tb_health_article_categories', 'tb_article_categories');
        Schema::rename('tb_health_article_comments', 'tb_article_comments');
        Schema::rename('tb_health_article_likes', 'tb_article_likes');
        Schema::rename('tb_health_article_shares', 'tb_article_shares');
        Schema::rename('tb_health_article_tag', 'tb_article_tag');
        Schema::rename('tb_health_article_tags', 'tb_article_tags');
        Schema::rename('tb_health_article_saved', 'tb_saved_articles');
    }
};
