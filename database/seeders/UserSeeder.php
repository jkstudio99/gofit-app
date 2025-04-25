<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_user')->insert([
            [
                'user_id' => 1,
                'user_type_id' => 2, // ผู้ดูแลระบบ
                'user_status_id' => 1, // ใช้งาน
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'firstname' => 'ผู้ดูแล',
                'lastname' => 'ระบบ',
                'email' => 'admin@gofit.com',
                'telephone' => '0812345678',
                'gmail_user_id' => null,
                'facebook_user_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'last_login_at' => null,
            ],
            [
                'user_id' => 2,
                'user_type_id' => 1, // ผู้ใช้ทั่วไป
                'user_status_id' => 1, // ใช้งาน
                'username' => 'user1',
                'password' => Hash::make('user123'),
                'firstname' => 'วรงค์กรณ์',
                'lastname' => 'ฟักทองอยู่',
                'email' => 'user1@gofit.com',
                'telephone' => '0898765432',
                'gmail_user_id' => null,
                'facebook_user_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'last_login_at' => null,
            ],
            [
                'user_id' => 3,
                'user_type_id' => 1, // ผู้ใช้ทั่วไป
                'user_status_id' => 1, // ใช้งาน
                'username' => 'user2',
                'password' => Hash::make('user123'),
                'firstname' => 'สมศักดิ์',
                'lastname' => 'สมบูรณ์',
                'email' => 'user2@gofit.com',
                'telephone' => '0876543210',
                'gmail_user_id' => null,
                'facebook_user_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'last_login_at' => null,
            ],
        ]);
    }
}