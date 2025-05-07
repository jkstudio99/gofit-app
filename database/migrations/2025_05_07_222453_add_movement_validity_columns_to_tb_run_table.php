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
            $table->enum('movement_validity', ['valid', 'suspicious', 'invalid'])->nullable()->after('is_completed');
            $table->string('validity_note', 255)->nullable()->after('movement_validity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_run', function (Blueprint $table) {
            $table->dropColumn('movement_validity');
            $table->dropColumn('validity_note');
        });
    }
};
