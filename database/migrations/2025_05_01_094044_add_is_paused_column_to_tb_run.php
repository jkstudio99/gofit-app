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
            if (!Schema::hasColumn('tb_run', 'is_paused')) {
                $table->boolean('is_paused')->default(false)->after('is_completed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_run', function (Blueprint $table) {
            if (Schema::hasColumn('tb_run', 'is_paused')) {
                $table->dropColumn('is_paused');
            }
        });
    }
};
