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
        Schema::create('tb_user_badge', function (Blueprint $table) {
            $table->id('user_badge_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id');
            $table->foreignId('badge_id')->constrained('tb_badge', 'badge_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_user_badge');
    }
};
