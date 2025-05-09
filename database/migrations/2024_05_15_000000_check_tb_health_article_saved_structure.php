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
        // Get the columns of the table
        $columns = DB::select("DESCRIBE tb_health_article_saved");

        // Output the columns
        foreach ($columns as $column) {
            echo $column->Field . " - " . $column->Type . "\n";
        }

        // Check if the filter columns exist
        $hasFilterName = Schema::hasColumn('tb_health_article_saved', 'filter_name');
        $hasFilterData = Schema::hasColumn('tb_health_article_saved', 'filter_data');
        $hasIsFilter = Schema::hasColumn('tb_health_article_saved', 'is_filter');

        echo "Has filter_name: " . ($hasFilterName ? 'Yes' : 'No') . "\n";
        echo "Has filter_data: " . ($hasFilterData ? 'Yes' : 'No') . "\n";
        echo "Has is_filter: " . ($hasIsFilter ? 'Yes' : 'No') . "\n";

        // If the columns don't exist, add them
        if (!$hasFilterName || !$hasFilterData || !$hasIsFilter) {
            Schema::table('tb_health_article_saved', function (Blueprint $table) use ($hasFilterName, $hasFilterData, $hasIsFilter) {
                if (!$hasFilterName) {
                    $table->string('filter_name')->nullable();
                }
                if (!$hasFilterData) {
                    $table->text('filter_data')->nullable();
                }
                if (!$hasIsFilter) {
                    $table->boolean('is_filter')->default(false);
                }
            });

            echo "Added missing columns\n";
        } else {
            echo "All filter columns already exist\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This doesn't need to do anything
    }
};
