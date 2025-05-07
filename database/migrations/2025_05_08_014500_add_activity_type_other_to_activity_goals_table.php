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
        Schema::table('tb_activity_goals', function (Blueprint $table) {
            $table->string('activity_type_other', 100)->nullable()->after('activity_type')
                ->comment('รายละเอียดเพิ่มเติมสำหรับประเภทกิจกรรมอื่นๆ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_activity_goals', function (Blueprint $table) {
            $table->dropColumn('activity_type_other');
        });
    }
};
