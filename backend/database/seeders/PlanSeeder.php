<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic Plan',
            'price' => 0.00,
            'duration_days' => 30,
            'max_students' => 10,
            'max_live_classes' => 5,
            'description' => 'A free plan for trial.',
        ]);

        Plan::create([
            'name' => 'Standard Plan',
            'price' => 29.99,
            'duration_days' => 30,
            'max_students' => 50,
            'max_live_classes' => 20,
            'description' => 'A standard plan for independent teachers.',
        ]);

        Plan::create([
            'name' => 'Premium Plan',
            'price' => 59.99,
            'duration_days' => 30,
            'max_students' => 200,
            'max_live_classes' => 100,
            'description' => 'A premium plan for growing academies.',
        ]);
    }
}
