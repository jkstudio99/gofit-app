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
            // Check if meta_title column already exists
            if (!Schema::hasColumn('tb_health_articles', 'meta_title')) {
                $table->string('meta_title', 255)->nullable()->after('content');
            }

            // Check if meta_description column already exists
            if (!Schema::hasColumn('tb_health_articles', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_health_articles', function (Blueprint $table) {
            // Check if the columns exist before dropping them
            if (Schema::hasColumn('tb_health_articles', 'meta_title')) {
                $table->dropColumn('meta_title');
            }

            if (Schema::hasColumn('tb_health_articles', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
        });
    }
};
