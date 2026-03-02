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
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@zewd.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Teacher
        User::create([
            'name' => 'John Doe',
            'email' => 'teacher@zewd.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);

        // Student
        User::create([
            'name' => 'Jane Smith',
            'email' => 'student@zewd.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
        ]);
    }
}
