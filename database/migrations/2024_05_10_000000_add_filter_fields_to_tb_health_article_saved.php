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
        Schema::table('tb_health_article_saved', function (Blueprint $table) {
            $table->string('filter_name')->nullable();
            $table->text('filter_data')->nullable();
            $table->boolean('is_filter')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_health_article_saved', function (Blueprint $table) {
            $table->dropColumn('filter_name');
            $table->dropColumn('filter_data');
            $table->dropColumn('is_filter');
        });
    }
};
