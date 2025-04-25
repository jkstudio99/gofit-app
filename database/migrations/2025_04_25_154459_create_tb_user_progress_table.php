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
        Schema::create('tb_user_progress', function (Blueprint $table) {
            $table->id('progress_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id');
            $table->string('period_type'); // daily, weekly, monthly
            $table->timestamp('period_start_date');
            $table->float('total_distance')->default(0);
            $table->float('total_calories')->default(0);
            $table->integer('total_activities')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_user_progress');
    }
};
