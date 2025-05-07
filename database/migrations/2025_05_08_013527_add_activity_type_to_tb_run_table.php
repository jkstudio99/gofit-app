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
        Schema::table('tb_run', function (Blueprint $table) {
            $table->string('activity_type')->default('running')->after('user_id')->comment('ประเภทกิจกรรม เช่น running, cycling, swimming');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_run', function (Blueprint $table) {
            $table->dropColumn('activity_type');
        });
    }
};
