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
        Schema::create('tb_activity', function (Blueprint $table) {
            $table->id('activity_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id');
            $table->string('activity_type');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->float('distance')->default(0);
            $table->float('calories_burned')->default(0);
            $table->float('average_speed')->nullable();
            $table->integer('heart_rate_avg')->nullable();
            $table->json('route_gps_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_activity');
    }
};
