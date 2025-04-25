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
        Schema::create('tb_external_service', function (Blueprint $table) {
            $table->id('service_id');
            $table->foreignId('user_id')->constrained('tb_user', 'user_id');
            $table->string('service_name');
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_external_service');
    }
};
