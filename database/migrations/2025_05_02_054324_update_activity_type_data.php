<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    // คัดลอกข้อมูลจาก field type ไปยัง activity_type
        DB::statement('UPDATE tb_activity SET activity_type = "running" WHERE activity_type IS NULL OR activity_type = ""');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_activity', function (Blueprint $table) {
            //
        });
    }
};
