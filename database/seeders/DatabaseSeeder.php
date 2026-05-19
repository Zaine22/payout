<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create Admin account
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin111'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        // Create EZGO account
        User::updateOrCreate(
            ['email' => 'ezgo@gmail.com'],
            [
                'name' => 'EZGO',
                'password' => Hash::make('ezgo111'),
                'role' => UserRole::Ezgo,
                'email_verified_at' => now(),
            ]
        );
    }
}
