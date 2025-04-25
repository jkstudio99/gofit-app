<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_role')->insert([
            [
                'role_id' => 1,
                'role_name' => 'user',
                'description' => 'ผู้ใช้งานทั่วไป สามารถวิ่ง ดูแดชบอร์ด และแลกรางวัลได้',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => 2,
                'role_name' => 'admin',
                'description' => 'ผู้ดูแลระบบ สามารถจัดการผู้ใช้ เหรียญตรา รางวัล และดูรายงานได้',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}