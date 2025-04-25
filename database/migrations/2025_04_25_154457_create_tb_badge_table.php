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
        Schema::create('tb_badge', function (Blueprint $table) {
            $table->id('badge_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('calories_required');
            $table->string('image_url')->nullable();
            $table->char('isenabled', 1)->default('Y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_badge');
    }
};
