<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if foreign keys exist before dropping them
        // For tb_run table
        if (Schema::hasTable('tb_run')) {
            Schema::table('tb_run', function (Blueprint $table) {
                // Check if foreign key exists and drop it
                if (DB::getSchemaBuilder()->getColumnListing('tb_run')) {
                    $foreignKeys = DB::select("SHOW CREATE TABLE tb_run");
                    if (isset($foreignKeys[0])) {
                        $createTableSql = $foreignKeys[0]->{'Create Table'};
                        if (strpos($createTableSql, 'CONSTRAINT `tb_run_user_id_foreign`') !== false) {
                            $table->dropForeign('tb_run_user_id_foreign');
                        }
                    }
                }
            });

            // Add the corrected foreign key
            Schema::table('tb_run', function (Blueprint $table) {
                $table->foreign('user_id')->references('user_id')->on('tb_user');
            });
        }

        // For tb_run_shares table
        if (Schema::hasTable('tb_run_shares')) {
            Schema::table('tb_run_shares', function (Blueprint $table) {
                // Drop foreign keys if they exist
                if (DB::getSchemaBuilder()->getColumnListing('tb_run_shares')) {
                    $foreignKeys = DB::select("SHOW CREATE TABLE tb_run_shares");
                    if (isset($foreignKeys[0])) {
                        $createTableSql = $foreignKeys[0]->{'Create Table'};
                        if (strpos($createTableSql, 'CONSTRAINT `tb_run_shares_user_id_foreign`') !== false) {
                            $table->dropForeign('tb_run_shares_user_id_foreign');
                        }
                        if (strpos($createTableSql, 'CONSTRAINT `tb_run_shares_shared_with_user_id_foreign`') !== false) {
                            $table->dropForeign('tb_run_shares_shared_with_user_id_foreign');
                        }
                    }
                }
            });

            // Add the corrected foreign keys
            Schema::table('tb_run_shares', function (Blueprint $table) {
                $table->foreign('user_id')->references('user_id')->on('tb_user');
                $table->foreign('shared_with_user_id')->references('user_id')->on('tb_user');
            });
        }

        // For tb_exercise_locations table
        if (Schema::hasTable('tb_exercise_locations')) {
            Schema::table('tb_exercise_locations', function (Blueprint $table) {
                // Drop foreign key if it exists
                if (DB::getSchemaBuilder()->getColumnListing('tb_exercise_locations')) {
                    $foreignKeys = DB::select("SHOW CREATE TABLE tb_exercise_locations");
                    if (isset($foreignKeys[0])) {
                        $createTableSql = $foreignKeys[0]->{'Create Table'};
                        if (strpos($createTableSql, 'CONSTRAINT `tb_exercise_locations_user_id_foreign`') !== false) {
                            $table->dropForeign('tb_exercise_locations_user_id_foreign');
                        }
                    }
                }
            });

            // Add the corrected foreign key
            Schema::table('tb_exercise_locations', function (Blueprint $table) {
                $table->foreign('user_id')->references('user_id')->on('tb_user');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration fixes references, so there's no need to reverse it
    }
};
