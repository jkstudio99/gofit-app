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
        Schema::create('tb_reward', function (Blueprint $table) {
            $table->id('reward_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('required_badge_count')->default(0);
            $table->integer('points_required');
            $table->integer('stock')->default(0);
            $table->string('image_url')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_reward');
    }
};
