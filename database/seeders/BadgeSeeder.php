<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // เพิ่มเหรียญตราเริ่มต้น
        DB::table('tb_badge')->insert([
            [
                'badge_id' => 1,
                'badge_name' => 'นักวิ่งมือใหม่',
                'badge_desc' => 'เผาผลาญ 100 แคลอรี่แรก',
                'type' => 'calories',
                'criteria' => 100.00,
                'badge_image' => 'badges/beginner.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'badge_id' => 2,
                'badge_name' => 'นักวิ่งระดับกลาง',
                'badge_desc' => 'เผาผลาญ 500 แคลอรี่',
                'type' => 'calories',
                'criteria' => 500.00,
                'badge_image' => 'badges/intermediate.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'badge_id' => 3,
                'badge_name' => 'นักวิ่งมืออาชีพ',
                'badge_desc' => 'เผาผลาญ 1,000 แคลอรี่',
                'type' => 'calories',
                'criteria' => 1000.00,
                'badge_image' => 'badges/pro.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
