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
        Schema::create('tb_run', function (Blueprint $table) {
            $table->id('run_id');
            $table->foreignId('user_id')->constrained('tb_users', 'user_id');
            $table->decimal('distance', 8, 2)->default(0);
            $table->integer('duration')->default(0); // in seconds
            $table->integer('calories_burned')->default(0);
            $table->decimal('average_speed', 5, 2)->default(0);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->json('route_data')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_paused')->default(false);
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_run');
    }
};
