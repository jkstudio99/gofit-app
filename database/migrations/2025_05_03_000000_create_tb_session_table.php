/<?php

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
        // ตรวจสอบว่าตาราง tb_session มีอยู่แล้วหรือไม่
        if (Schema::hasTable('tb_session')) {
            // ตรวจสอบว่ามีคอลัมน์ที่จำเป็นอยู่แล้วหรือไม่
            if (!Schema::hasColumn('tb_session', 'ip_address')) {
                Schema::table('tb_session', function (Blueprint $table) {
                    $table->string('ip_address')->nullable()->after('session_token');
                });
            }

            if (!Schema::hasColumn('tb_session', 'user_agent')) {
                Schema::table('tb_session', function (Blueprint $table) {
                    $table->text('user_agent')->nullable()->after('ip_address');
                });
            }
        } else {
            // สร้างตารางใหม่
            Schema::create('tb_session', function (Blueprint $table) {
                $table->bigIncrements('session_id');
                $table->unsignedBigInteger('user_id');
                $table->string('session_token', 255);
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('expired_at')->nullable();

                // กำหนด index สำหรับการค้นหาที่รวดเร็ว
                $table->index('user_id');
                $table->index('session_token');
                $table->index('expired_at');

                // กำหนด foreign key
                $table->foreign('user_id')
                    ->references('user_id')
                    ->on('tb_user')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ลบตารางในการ rollback เพื่อป้องกันข้อมูลสูญหาย
        // แต่ถ้าต้องการลบให้เปิดใช้งานโค้ดข้างล่างนี้

        // Schema::dropIfExists('tb_session');

        // ถ้าต้องการลบเฉพาะคอลัมน์ที่เพิ่มเข้ามา
        if (Schema::hasTable('tb_session')) {
            if (Schema::hasColumn('tb_session', 'ip_address')) {
                Schema::table('tb_session', function (Blueprint $table) {
                    $table->dropColumn('ip_address');
                });
            }

            if (Schema::hasColumn('tb_session', 'user_agent')) {
                Schema::table('tb_session', function (Blueprint $table) {
                    $table->dropColumn('user_agent');
                });
            }
        }
    }
};
