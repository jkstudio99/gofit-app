<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_user_role')->insert([
            [
                'user_role_id' => 1,
                'user_id' => 1,
                'role_id' => 2, // admin
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_role_id' => 2,
                'user_id' => 2,
                'role_id' => 1, // user
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_role_id' => 3,
                'user_id' => 3,
                'role_id' => 1, // user
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}