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
        Schema::create('tb_redeem', function (Blueprint $table) {
            $table->id('redeem_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->onDelete('cascade');
            $table->foreignId('reward_id')->constrained('tb_reward', 'reward_id')->onDelete('cascade');
            $table->integer('points_spent');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_redeem');
    }
};
