<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class ActivityGoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบว่ามีตาราง activity_goals อยู่หรือไม่
        if (Schema::hasTable('activity_goals')) {
            // ข้อมูลเป้าหมายการออกกำลังกายตัวอย่าง
            $goals = [
                [
                    'user_id' => 1,
                    'type' => 'distance',
                    'activity_type' => 'run',
                    'target_value' => 100.0,
                    'current_value' => 25.5,
                    'period' => 'monthly',
                    'start_date' => Carbon::now()->startOfMonth(),
                    'end_date' => Carbon::now()->endOfMonth(),
                    'is_completed' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'user_id' => 1,
                    'type' => 'calories',
                    'activity_type' => null, // Any activity
                    'target_value' => 5000.0,
                    'current_value' => 1200.0,
                    'period' => 'weekly',
                    'start_date' => Carbon::now()->startOfWeek(),
                    'end_date' => Carbon::now()->endOfWeek(),
                    'is_completed' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'user_id' => 2,
                    'type' => 'duration',
                    'activity_type' => 'cycle',
                    'target_value' => 600.0, // 10 hours in minutes
                    'current_value' => 180.0, // 3 hours in minutes
                    'period' => 'monthly',
                    'start_date' => Carbon::now()->startOfMonth(),
                    'end_date' => Carbon::now()->endOfMonth(),
                    'is_completed' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'user_id' => 3,
                    'type' => 'frequency',
                    'activity_type' => 'walk',
                    'target_value' => 20.0,
                    'current_value' => 8.0,
                    'period' => 'monthly',
                    'start_date' => Carbon::now()->startOfMonth(),
                    'end_date' => Carbon::now()->endOfMonth(),
                    'is_completed' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ];

            // เพิ่มข้อมูลเป้าหมายการออกกำลังกาย
            foreach ($goals as $goal) {
                DB::table('activity_goals')->insert($goal);
            }
        }
    }
}
