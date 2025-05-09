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
        Schema::create('tb_health_article_saved', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('article_id')->index();
            $table->timestamps();

            // Add foreign key constraints if applicable
            // $table->foreign('user_id')->references('id')->on('tb_user')->onDelete('cascade');
            // $table->foreign('article_id')->references('id')->on('health_articles')->onDelete('cascade');

            // Add unique constraint to prevent duplicate saves
            $table->unique(['user_id', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_health_article_saved');
    }
};
