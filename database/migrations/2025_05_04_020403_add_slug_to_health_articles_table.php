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
        // Check if the slug column already exists
        if (!Schema::hasColumn('tb_health_articles', 'slug')) {
        Schema::table('tb_health_articles', function (Blueprint $table) {
                $table->string('slug', 191)->after('title');
        });
        }

        // Check if the index already exists
        $indexExists = DB::select("SHOW INDEX FROM tb_health_articles WHERE Key_name = 'tb_health_articles_slug_unique'");
        if (empty($indexExists)) {
            DB::statement('ALTER TABLE tb_health_articles ADD UNIQUE INDEX tb_health_articles_slug_unique (slug)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_health_articles', function (Blueprint $table) {
            // Check if the index exists before dropping it
            $indexExists = DB::select("SHOW INDEX FROM tb_health_articles WHERE Key_name = 'tb_health_articles_slug_unique'");
            if (!empty($indexExists)) {
                DB::statement('ALTER TABLE tb_health_articles DROP INDEX tb_health_articles_slug_unique');
            }

            // Check if the column exists before dropping it
            if (Schema::hasColumn('tb_health_articles', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};
