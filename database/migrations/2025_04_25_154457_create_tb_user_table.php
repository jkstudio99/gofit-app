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
        Schema::create('tb_user', function (Blueprint $table) {
            $table->id('user_id');
            $table->foreignId('user_type_id')->constrained('tb_master_user_type', 'user_type_id');
            $table->foreignId('user_status_id')->constrained('tb_master_user_status', 'user_status_id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('telephone', 10)->nullable();
            $table->string('gmail_user_id', 80)->nullable();
            $table->string('facebook_user_id', 80)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_user');
    }
};
