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

            if (!Schema::hasColumn('tb_run', 'paused_at')) {
                $table->timestamp('paused_at')->nullable()->after('is_paused');
            }

            if (!Schema::hasColumn('tb_run', 'resumed_at')) {
                $table->timestamp('resumed_at')->nullable()->after('paused_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_run', function (Blueprint $table) {
            $table->dropColumn(['is_paused', 'paused_at', 'resumed_at']);
        });
    }
};
