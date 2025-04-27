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
        // Notification table
        Schema::create('tb_notification', function (Blueprint $table) {
            $table->id('notification_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable();
            $table->string('reference_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // External Service table
        Schema::create('tb_external_service', function (Blueprint $table) {
            $table->id('external_service_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->onDelete('cascade');
            $table->string('service_name');
            $table->string('service_user_id');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->dateTime('token_expires_at')->nullable();
            $table->timestamps();
        });

        // Session table
        Schema::create('tb_session', function (Blueprint $table) {
            $table->id('session_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->onDelete('cascade');
            $table->string('session_token');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->dateTime('expires_at');
            $table->timestamps();
        });

        // User Progress table
        Schema::create('tb_user_progress', function (Blueprint $table) {
            $table->id('user_progress_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id')->onDelete('cascade');
            $table->string('progress_type');
            $table->decimal('value', 10, 2);
            $table->decimal('previous_value', 10, 2)->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_user_progress');
        Schema::dropIfExists('tb_session');
        Schema::dropIfExists('tb_external_service');
        Schema::dropIfExists('tb_notification');
    }
};
