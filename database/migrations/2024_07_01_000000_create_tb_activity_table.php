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
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->onDelete('cascade');
            $table->string('activity_type')->default('running');
            $table->decimal('distance', 8, 2)->nullable();
            $table->integer('duration')->nullable();
            $table->integer('calories_burned')->nullable();
            $table->decimal('average_speed', 8, 2)->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->longText('route_gps_data')->nullable();
            $table->text('notes')->nullable();
            $table->json('details')->nullable();
            $table->integer('max_participants')->default(1);
            $table->boolean('is_group')->default(false);
            $table->boolean('is_test')->default(false);
            $table->string('status')->default('active');
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
