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
        Schema::create('article_tags', function (Blueprint $table) {
            $table->id('tag_id');
            $table->string('tag_name', 50)->unique();
            $table->string('tag_slug', 50)->unique();
            $table->timestamps();
        });

        Schema::create('article_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('health_articles', 'article_id')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('article_tags', 'tag_id')->onDelete('cascade');
            $table->timestamps();

            // Make sure article_id and tag_id combination is unique
            $table->unique(['article_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_tag');
        Schema::dropIfExists('article_tags');
    }
};
