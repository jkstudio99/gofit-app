<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RewardSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_reward')->insert([
            [
                'reward_id' => 1,
                'name' => 'คูปองส่วนลด 10%',
                'description' => 'ส่วนลด 10% สำหรับซื้อสินค้าในร้านค้าที่ร่วมรายการ',
                'required_badge_count' => 2,
                'stock' => 100,
                'image_url' => 'rewards/coupon10.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'isenabled' => 'Y',
            ],
            [
                'reward_id' => 2,
                'name' => 'คูปองส่วนลด 20%',
                'description' => 'ส่วนลด 20% สำหรับซื้อสินค้าในร้านค้าที่ร่วมรายการ',
                'required_badge_count' => 5,
                'stock' => 50,
                'image_url' => 'rewards/coupon20.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'isenabled' => 'Y',
            ],
            [
                'reward_id' => 3,
                'name' => 'รองเท้าวิ่ง',
                'description' => 'รองเท้าวิ่งรุ่นพิเศษสำหรับสมาชิก',
                'required_badge_count' => 10,
                'stock' => 20,
                'image_url' => 'rewards/shoes.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'isenabled' => 'Y',
            ],
        ]);
    }
}