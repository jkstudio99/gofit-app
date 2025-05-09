<?php
// Script to add the missing columns to tb_health_article_saved table

// Load the application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check if the table exists
if (!Schema::hasTable('tb_health_article_saved')) {
    echo "Table tb_health_article_saved does not exist. Creating it...\n";

    // Create the table if it doesn't exist
    Schema::create('tb_health_article_saved', function ($table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->index();
        $table->unsignedBigInteger('article_id')->index();
        $table->timestamps();
        $table->unique(['user_id', 'article_id']);
    });

    echo "Table tb_health_article_saved created.\n";
}

// Get the columns of the table
$columns = DB::select("DESCRIBE tb_health_article_saved");
$columnNames = [];

foreach ($columns as $column) {
    $columnNames[] = $column->Field;
    echo $column->Field . " - " . $column->Type . "\n";
}

// Check if the filter columns exist
$hasFilterName = in_array('filter_name', $columnNames);
$hasFilterData = in_array('filter_data', $columnNames);
$hasIsFilter = in_array('is_filter', $columnNames);

echo "Has filter_name: " . ($hasFilterName ? 'Yes' : 'No') . "\n";
echo "Has filter_data: " . ($hasFilterData ? 'Yes' : 'No') . "\n";
echo "Has is_filter: " . ($hasIsFilter ? 'Yes' : 'No') . "\n";

// Add missing columns
$alterStatements = [];

if (!$hasFilterName) {
    $alterStatements[] = "ADD `filter_name` VARCHAR(191) NULL";
}

if (!$hasFilterData) {
    $alterStatements[] = "ADD `filter_data` TEXT NULL";
}

if (!$hasIsFilter) {
    $alterStatements[] = "ADD `is_filter` TINYINT(1) NOT NULL DEFAULT '0'";
}

if (count($alterStatements) > 0) {
    $sql = "ALTER TABLE `tb_health_article_saved` " . implode(", ", $alterStatements);
    echo "Executing SQL: " . $sql . "\n";

    try {
        DB::statement($sql);
        echo "Added missing columns successfully.\n";
    } catch (Exception $e) {
        echo "Error adding columns: " . $e->getMessage() . "\n";
    }
} else {
    echo "All required columns already exist.\n";
}

echo "Done.\n";
