<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if tb_activity exists
        if (Schema::hasTable('tb_activity')) {
            // Get running activities from tb_activity
            $runActivities = DB::table('tb_activity')
                ->where('activity_type', 'running')
                ->get();

            // Insert into tb_run
            foreach ($runActivities as $activity) {
                DB::table('tb_run')->insert([
                    'user_id' => $activity->user_id,
                    'distance' => $activity->distance ?? 0,
                    'duration' => $activity->duration ?? 0,
                    'calories_burned' => $activity->calories_burned ?? 0,
                    'average_speed' => $activity->average_speed ?? 0,
                    'start_time' => $activity->start_time,
                    'end_time' => $activity->end_time,
                    'route_data' => $activity->route_data ?? null,
                    'is_completed' => ($activity->end_time !== null),
                    'created_at' => $activity->created_at,
                    'updated_at' => $activity->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't need to revert this as it just copies data
    }
};
