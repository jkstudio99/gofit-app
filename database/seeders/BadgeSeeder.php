<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_badge')->insert([
            [
                'badge_id' => 1,
                'name' => 'นักวิ่งมือใหม่',
                'description' => 'เผาผลาญ 100 แคลอรี่แรก',
                'calories_required' => 100,
                'image_url' => 'badges/beginner.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'isenabled' => 'Y',
            ],
            [
                'badge_id' => 2,
                'name' => 'นักวิ่งระดับกลาง',
                'description' => 'เผาผลาญ 500 แคลอรี่',
                'calories_required' => 500,
                'image_url' => 'badges/intermediate.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'isenabled' => 'Y',
            ],
            [
                'badge_id' => 3,
                'name' => 'นักวิ่งมืออาชีพ',
                'description' => 'เผาผลาญ 1,000 แคลอรี่',
                'calories_required' => 1000,
                'image_url' => 'badges/pro.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'isenabled' => 'Y',
            ],
        ]);
    }
}