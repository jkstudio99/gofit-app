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
        Schema::create('event_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events', 'event_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->onDelete('cascade');
            $table->enum('status', ['registered', 'attended', 'cancelled', 'absent'])->default('registered');
            $table->dateTime('registered_at');
            $table->dateTime('checked_in_at')->nullable();
            $table->dateTime('checked_out_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            // ผู้ใช้คนหนึ่งลงทะเบียนกิจกรรมนั้นได้ครั้งเดียว
            $table->unique(['event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_users');
    }
};
