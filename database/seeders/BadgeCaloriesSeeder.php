<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeCaloriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // เหรียญรางวัลสำหรับแคลอรี่
        $badges = [
            [
                'badge_name' => 'เผาผลาญขั้นต้น',
                'badge_desc' => 'เผาผลาญแคลอรี่รวม 100 แคลอรี่',
                'type' => 'calories',
                'criteria' => 100.0, // 100 แคลอรี่
                'badge_image' => 'badges/calories_100.png',
            ],
            [
                'badge_name' => 'เผาผลาญระดับกลาง',
                'badge_desc' => 'เผาผลาญแคลอรี่รวม 500 แคลอรี่',
                'type' => 'calories',
                'criteria' => 500.0, // 500 แคลอรี่
                'badge_image' => 'badges/calories_500.png',
            ],
            [
                'badge_name' => 'เผาผลาญระดับสูง',
                'badge_desc' => 'เผาผลาญแคลอรี่รวม 1,000 แคลอรี่',
                'type' => 'calories',
                'criteria' => 1000.0, // 1,000 แคลอรี่
                'badge_image' => 'badges/calories_1000.png',
            ],
            [
                'badge_name' => 'เผาผลาญเหรียญเงิน',
                'badge_desc' => 'เผาผลาญแคลอรี่รวม 2,500 แคลอรี่',
                'type' => 'calories',
                'criteria' => 2500.0, // 2,500 แคลอรี่
                'badge_image' => 'badges/calories_2500.png',
            ],
            [
                'badge_name' => 'เผาผลาญเหรียญทอง',
                'badge_desc' => 'เผาผลาญแคลอรี่รวม 5,000 แคลอรี่',
                'type' => 'calories',
                'criteria' => 5000.0, // 5,000 แคลอรี่
                'badge_image' => 'badges/calories_5000.png',
            ],
        ];

        $now = Carbon::now();

        foreach ($badges as $badge) {
            // ตรวจสอบว่ามีเหรียญนี้ในระบบแล้วหรือยัง
            $exists = DB::table('tb_badge')
                ->where('badge_name', $badge['badge_name'])
                ->where('type', 'calories')
                ->exists();

            if (!$exists) {
                $badge['created_at'] = $now;
                $badge['updated_at'] = $now;
                DB::table('tb_badge')->insert($badge);
                $this->command->info("เพิ่มเหรียญ: {$badge['badge_name']}");
            } else {
                $this->command->info("เหรียญ {$badge['badge_name']} มีอยู่แล้ว ข้ามการเพิ่ม");
            }
        }
    }
}
