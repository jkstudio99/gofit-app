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
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->integer('capacity')->default(0); // 0 = ไม่จำกัดจำนวน
            $table->string('image_url')->nullable();
            $table->foreignId('created_by')->constrained('tb_user', 'user_id');
            $table->enum('status', ['published', 'draft', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
