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
        // บทความ
        Schema::rename('health_articles', 'tb_health_articles');
        Schema::rename('article_categories', 'tb_article_categories');
        Schema::rename('article_comments', 'tb_article_comments');
        Schema::rename('article_likes', 'tb_article_likes');
        Schema::rename('article_shares', 'tb_article_shares');
        Schema::rename('article_tag', 'tb_article_tag');
        Schema::rename('article_tags', 'tb_article_tags');
        Schema::rename('saved_articles', 'tb_saved_articles');

        // เป้าหมาย
        Schema::rename('activity_goals', 'tb_activity_goals');
        Schema::rename('activity_registrations', 'tb_activity_registrations');
        Schema::rename('events', 'tb_events');
        Schema::rename('event_users', 'tb_event_users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // บทความ
        Schema::rename('tb_health_articles', 'health_articles');
        Schema::rename('tb_article_categories', 'article_categories');
        Schema::rename('tb_article_comments', 'article_comments');
        Schema::rename('tb_article_likes', 'article_likes');
        Schema::rename('tb_article_shares', 'article_shares');
        Schema::rename('tb_article_tag', 'article_tag');
        Schema::rename('tb_article_tags', 'article_tags');
        Schema::rename('tb_saved_articles', 'saved_articles');

        // เป้าหมาย
        Schema::rename('tb_activity_goals', 'activity_goals');
        Schema::rename('tb_activity_registrations', 'activity_registrations');
        Schema::rename('tb_events', 'events');
        Schema::rename('tb_event_users', 'event_users');
    }
};