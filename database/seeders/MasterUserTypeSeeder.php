<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterUserTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_master_user_type')->insert([
            [
                'user_type_id' => 1,
                'user_typename' => 'ผู้ใช้ทั่วไป',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'user_type_id' => 2,
                'user_typename' => 'ผู้ดูแลระบบ',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
