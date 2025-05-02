<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTestColumnToTbRunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_run', function (Blueprint $table) {
            // เพิ่มคอลัมน์ is_test เป็น boolean ค่าเริ่มต้นเป็น false
            $table->boolean('is_test')->default(false)->after('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_run', function (Blueprint $table) {
            $table->dropColumn('is_test');
        });
    }
}
