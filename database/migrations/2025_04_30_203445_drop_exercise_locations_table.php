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
        Schema::dropIfExists('tb_exercise_locations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tb_exercise_locations', function (Blueprint $table) {
            $table->id('location_id');
            $table->foreignId('user_id')->constrained('tb_users', 'user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location_type');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->text('address')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }
};
