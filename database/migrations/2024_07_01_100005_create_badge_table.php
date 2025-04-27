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
            $table->string('badge_name');
            $table->string('badge_desc')->nullable();
            $table->string('type'); // 'distance', 'calories', 'streak', 'speed', 'event'
            $table->decimal('criteria', 10, 2); // เงื่อนไขในการได้รับเหรียญตรา
            $table->string('badge_image')->nullable();
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
