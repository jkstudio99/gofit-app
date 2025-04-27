<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_reward')->insert([
            [
                'reward_id' => 1,
                'name' => 'GoFit T-Shirt',
                'description' => 'เสื้อยืด GoFit สีขาว',
                'required_badge_count' => 5,
                'points_required' => 500,
                'stock' => 10,
                'image_url' => 'rewards/tshirt.jpg',
                'is_enabled' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'reward_id' => 2,
                'name' => 'GoFit Cap',
                'description' => 'หมวก GoFit สีดำ',
                'required_badge_count' => 3,
                'points_required' => 300,
                'stock' => 15,
                'image_url' => 'rewards/cap.jpg',
                'is_enabled' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'reward_id' => 3,
                'name' => 'GoFit Water Bottle',
                'description' => 'ขวดน้ำ GoFit ความจุ 750ml',
                'required_badge_count' => 2,
                'points_required' => 200,
                'stock' => 20,
                'image_url' => 'rewards/bottle.jpg',
                'is_enabled' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
