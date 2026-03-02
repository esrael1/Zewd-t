<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Subscription;
use App\Models\Plan;
use Carbon\Carbon;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('role', 'teacher')->first();
        $plan = Plan::where('name', 'Standard Plan')->first();

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'academy_name' => 'Zewd Academy',
            'bio' => 'An expert tutor in coding and math.',
            'status' => 'approved',
            'approved_by' => 1, // Admin ID
        ]);

        Subscription::create([
            'teacher_id' => $teacher->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(30),
            'payment_screenshot' => 'seeder_dummy.jpg',
            'approved_by' => 1,
        ]);
    }
}
