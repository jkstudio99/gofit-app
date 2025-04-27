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
        Schema::table('tb_activity', function (Blueprint $table) {
            // เช็คว่ามีคอลัมน์ calories_burned หรือไม่
            if (!Schema::hasColumn('tb_activity', 'calories_burned')) {
                $table->integer('calories_burned')->nullable()->after('duration');
            }

            // เช็คว่ามีคอลัมน์ calories หรือไม่ (ถ้ามีและต้องการย้ายข้อมูล)
            if (Schema::hasColumn('tb_activity', 'calories')) {
                // ใน production อาจจะต้องทำ data migration ด้วย
                // ซึ่งต้องใช้ DB::statement เพื่อคัดลอกข้อมูลจาก calories ไป calories_burned
                // DB::statement('UPDATE tb_activity SET calories_burned = calories');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_activity', function (Blueprint $table) {
            if (Schema::hasColumn('tb_activity', 'calories_burned')) {
                $table->dropColumn('calories_burned');
            }
        });
    }
};
