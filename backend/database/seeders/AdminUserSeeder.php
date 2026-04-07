<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Demo admin for the Blade back office (/admin). Change the password after deploy.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ],
        );

        // Test admin (Blade /admin + API is_admin).
        User::query()->updateOrCreate(
            ['email' => 'edwardfan706@gmail.com'],
            [
                'name' => 'edwardfkl',
                'password' => Hash::make('123456'),
                'is_admin' => true,
            ],
        );
    }
}
