<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthArticlesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create article categories table
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name', 100);
            $table->string('category_desc', 255)->nullable();
            $table->timestamps();
        });

        // Create health articles table
        Schema::create('health_articles', function (Blueprint $table) {
            $table->id('article_id');
            $table->string('title', 255);
            $table->text('content');
            $table->string('thumbnail', 255)->nullable();
            $table->foreignId('category_id')->constrained('article_categories', 'category_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });

        // Create article comments table
        Schema::create('article_comments', function (Blueprint $table) {
            $table->id('comment_id');
            $table->foreignId('article_id')->constrained('health_articles', 'article_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id');
            $table->text('comment_text');
            $table->timestamps();
        });

        // Create article likes table
        Schema::create('article_likes', function (Blueprint $table) {
            $table->id('like_id');
            $table->foreignId('article_id')->constrained('health_articles', 'article_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id');
            $table->timestamps();
            $table->unique(['article_id', 'user_id']);
        });

        // Create saved articles table
        Schema::create('saved_articles', function (Blueprint $table) {
            $table->id('saved_id');
            $table->foreignId('article_id')->constrained('health_articles', 'article_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id');
            $table->timestamps();
            $table->unique(['article_id', 'user_id']);
        });

        // Create article shares table
        Schema::create('article_shares', function (Blueprint $table) {
            $table->id('share_id');
            $table->foreignId('article_id')->constrained('health_articles', 'article_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->nullable();
            $table->string('platform', 50); // e.g., 'facebook', 'twitter', 'email'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_shares');
        Schema::dropIfExists('saved_articles');
        Schema::dropIfExists('article_likes');
        Schema::dropIfExists('article_comments');
        Schema::dropIfExists('health_articles');
        Schema::dropIfExists('article_categories');
    }
}
