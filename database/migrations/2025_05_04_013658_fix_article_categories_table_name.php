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
        // Only create the table if it doesn't already exist
        if (!Schema::hasTable('tb_health_article_categories')) {
            Schema::create('tb_health_article_categories', function (Blueprint $table) {
                $table->id('category_id');
                $table->string('category_name', 100);
                $table->string('category_desc', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_health_article_categories');
    }
};
