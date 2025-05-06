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
        Schema::create('onboarding_tours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('รหัสผู้ใช้');
            $table->string('tour_key')->comment('คีย์สำหรับทัวร์ เช่น dashboard, run, rewards');
            $table->enum('status', ['completed', 'skipped', 'pending'])->default('pending')->comment('สถานะการดูทัวร์');
            $table->timestamp('completed_at')->nullable()->comment('เวลาที่ดูทัวร์เสร็จสิ้น');
            $table->boolean('show_again')->default(false)->comment('แสดงทัวร์อีกครั้งหรือไม่');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('tb_user')->onDelete('cascade');
            $table->unique(['user_id', 'tour_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_tours');
    }
};
