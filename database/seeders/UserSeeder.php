<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->admin()
            ->create([
                'name' => 'Test Admin',
                'email' => 'test@admin.com',
            ]);

        User::factory()
            ->user()
            ->create([
                'name' => 'Test User',
                'email' => 'test@user.com',
            ]);
    }
}
