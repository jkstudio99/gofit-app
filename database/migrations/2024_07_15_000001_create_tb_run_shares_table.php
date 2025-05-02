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
        Schema::create('tb_run_shares', function (Blueprint $table) {
            $table->id('share_id');
            $table->foreignId('run_id')->constrained('tb_run', 'run_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('tb_users', 'user_id');
            $table->foreignId('shared_with_user_id')->constrained('tb_users', 'user_id');
            $table->text('share_message')->nullable();
            $table->boolean('is_viewed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_run_shares');
    }
};
