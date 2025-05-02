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
        Schema::create('tb_point_history', function (Blueprint $table) {
            $table->id('point_history_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('points');
            $table->string('description');
            $table->string('source_type')->nullable(); // badge, reward, event, etc.
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('tb_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_point_history');
    }
};
