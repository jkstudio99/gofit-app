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
        Schema::table('tb_badge', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(100)->after('criteria')
                  ->comment('คะแนนที่จะได้รับเมื่อปลดล็อคเหรียญตรานี้');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_badge', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    }
};
