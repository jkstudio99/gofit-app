<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // เพิ่มผู้ดูแลระบบ
        DB::table('tb_user')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'email' => 'admin@gofit.com',
            'firstname' => 'Admin',
            'lastname' => 'System',
            'user_type_id' => 2, // ประเภทผู้ดูแลระบบ
            'user_status_id' => 1, // สถานะปกติ
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // เพิ่มผู้ใช้ทั่วไป
        DB::table('tb_user')->insert([
            'username' => 'user',
            'password' => Hash::make('user123'),
            'email' => 'user@gofit.com',
            'firstname' => 'User',
            'lastname' => 'Test',
            'user_type_id' => 1, // ประเภทผู้ใช้ทั่วไป
            'user_status_id' => 1, // สถานะปกติ
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
