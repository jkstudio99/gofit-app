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
        Schema::create('activity_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->onDelete('cascade');
            $table->string('type'); // distance, duration, calories, frequency
            $table->string('activity_type')->nullable(); // Specific activity type or null for any
            $table->decimal('target_value', 10, 2);
            $table->decimal('current_value', 10, 2)->default(0);
            $table->string('period'); // daily, weekly, monthly, custom
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            // Indexes for faster queries
            $table->index('user_id');
            $table->index('type');
            $table->index('is_completed');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_goals');
    }
};
