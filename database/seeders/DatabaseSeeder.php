<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            MasterUserTypeSeeder::class,
            MasterUserStatusSeeder::class,
            RoleSeeder::class,
            BadgeSeeder::class,
            BadgeCaloriesSeeder::class,
            BadgeDistanceSeeder::class,
            RewardSeeder::class,
            UserSeeder::class,
            UserRoleSeeder::class,
            UserStatusSeeder::class,
            ActivitySeeder::class,
            ActivityGoalSeeder::class,
            EventSeeder::class,
        ]);
    }
}
