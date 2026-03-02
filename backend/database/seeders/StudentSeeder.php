<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('role', 'student')->first();
        $teacher = Teacher::first();
        $grade = Grade::where('teacher_id', $teacher->id)->first();

        Student::create([
            'user_id' => $user->id,
            'teacher_id' => $teacher->id,
            'grade_id' => $grade->id,
            'status' => 'active',
        ]);
    }
}
