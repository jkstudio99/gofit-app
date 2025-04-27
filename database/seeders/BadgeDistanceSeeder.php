<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeDistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // เหรียญรางวัลสำหรับระยะทาง
        $badges = [
            [
                'badge_name' => 'นักวิ่งมือใหม่',
                'badge_desc' => 'วิ่งได้ระยะทางรวม 5 กิโลเมตร',
                'type' => 'distance',
                'criteria' => 5.0, // 5 กิโลเมตร
                'badge_image' => 'badges/distance_5km.png',
            ],
            [
                'badge_name' => 'นักวิ่งหน้าใหม่',
                'badge_desc' => 'วิ่งได้ระยะทางรวม 10 กิโลเมตร',
                'type' => 'distance',
                'criteria' => 10.0, // 10 กิโลเมตร
                'badge_image' => 'badges/distance_10km.png',
            ],
            [
                'badge_name' => 'นักวิ่งระดับกลาง',
                'badge_desc' => 'วิ่งได้ระยะทางรวม 20 กิโลเมตร',
                'type' => 'distance',
                'criteria' => 20.0, // 20 กิโลเมตร
                'badge_image' => 'badges/distance_20km.png',
            ],
            [
                'badge_name' => 'นักวิ่งผู้พิชิต',
                'badge_desc' => 'วิ่งได้ระยะทางรวม 50 กิโลเมตร',
                'type' => 'distance',
                'criteria' => 50.0, // 50 กิโลเมตร
                'badge_image' => 'badges/distance_50km.png',
            ],
            [
                'badge_name' => 'นักวิ่งมาราธอน',
                'badge_desc' => 'วิ่งได้ระยะทางรวม 100 กิโลเมตร',
                'type' => 'distance',
                'criteria' => 100.0, // 100 กิโลเมตร
                'badge_image' => 'badges/distance_100km.png',
            ],
        ];

        $now = Carbon::now();

        foreach ($badges as $badge) {
            // ตรวจสอบว่ามีเหรียญนี้ในระบบแล้วหรือยัง
            $exists = DB::table('tb_badge')
                ->where('badge_name', $badge['badge_name'])
                ->where('type', 'distance')
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
