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
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin111'),
            'role' => UserRole::Admin,
        ]);

        // Create EZGO account
        User::factory()->create([
            'name' => 'EZGO',
            'email' => 'ezgo@gmail.com',
            'password' => Hash::make('ezgo111'),
            'role' => UserRole::Ezgo,
        ]);
    }
}