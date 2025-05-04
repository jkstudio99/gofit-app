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
        Schema::table('tb_health_articles', function (Blueprint $table) {
            // Check if the excerpt column already exists
            if (!Schema::hasColumn('tb_health_articles', 'excerpt')) {
                $table->text('excerpt')->after('slug')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_health_articles', function (Blueprint $table) {
            // Check if the column exists before dropping it
            if (Schema::hasColumn('tb_health_articles', 'excerpt')) {
                $table->dropColumn('excerpt');
            }
        });
    }
};
