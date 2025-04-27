<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // กำหนด user_id ด้วยตัวเอง (ต้องแน่ใจว่ามี user นี้อยู่จริงในระบบ)
        $userId = 3; // ควรเป็น ID ของ user ที่มีอยู่จริงในตาราง tb_user

        // สร้างข้อมูลกิจกรรมตัวอย่าง
        $activities = [
            [
                'user_id' => $userId,
                'activity_type' => 'running',
                'distance' => 5.2,
                'calories_burned' => 320,
                'average_speed' => 12.5,
                'route_gps_data' => json_encode([
                    ['lat' => 13.756331, 'lng' => 100.501762],
                    ['lat' => 13.756998, 'lng' => 100.502535],
                    ['lat' => 13.757665, 'lng' => 100.503308],
                    ['lat' => 13.758332, 'lng' => 100.504081],
                    ['lat' => 13.758999, 'lng' => 100.504854],
                ]),
                'start_time' => Carbon::now()->subDays(5)->subHours(2),
                'end_time' => Carbon::now()->subDays(5)->subHours(1),
                'is_test' => 0
            ],
            [
                'user_id' => $userId,
                'activity_type' => 'running',
                'distance' => 3.7,
                'calories_burned' => 230,
                'average_speed' => 11.2,
                'route_gps_data' => json_encode([
                    ['lat' => 13.756331, 'lng' => 100.501762],
                    ['lat' => 13.755664, 'lng' => 100.500989],
                    ['lat' => 13.754997, 'lng' => 100.500216],
                    ['lat' => 13.754330, 'lng' => 100.499443],
                ]),
                'start_time' => Carbon::now()->subDays(3)->subHours(3),
                'end_time' => Carbon::now()->subDays(3)->subHours(2),
                'is_test' => 0
            ],
            [
                'user_id' => $userId,
                'activity_type' => 'running',
                'distance' => 7.8,
                'calories_burned' => 480,
                'average_speed' => 13.1,
                'route_gps_data' => json_encode([
                    ['lat' => 13.756331, 'lng' => 100.501762],
                    ['lat' => 13.756998, 'lng' => 100.502535],
                    ['lat' => 13.757665, 'lng' => 100.503308],
                    ['lat' => 13.758332, 'lng' => 100.504081],
                    ['lat' => 13.758999, 'lng' => 100.504854],
                    ['lat' => 13.759666, 'lng' => 100.505627],
                    ['lat' => 13.760333, 'lng' => 100.506400],
                ]),
                'start_time' => Carbon::now()->subDay()->subHours(1),
                'end_time' => Carbon::now()->subDay(),
                'is_test' => 0
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }

        $this->command->info('สร้างข้อมูลกิจกรรมการวิ่งสำเร็จ ' . count($activities) . ' รายการ');
    }
}
