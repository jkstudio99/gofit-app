<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            MasterUserTypeSeeder::class,
            MasterUserStatusSeeder::class,
            RoleSeeder::class,
            BadgeSeeder::class,
            RewardSeeder::class,
            UserSeeder::class,
            UserRoleSeeder::class,
        ]);
    }
}