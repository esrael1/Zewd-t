<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@zewd.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@zewd.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'student@zewd.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
            ]
        );
    }
}