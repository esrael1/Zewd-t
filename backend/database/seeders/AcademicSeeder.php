<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\Subject;

class AcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = Teacher::first();

        $grade9 = Grade::create([
            'teacher_id' => $teacher->id,
            'name' => 'Grade 9',
        ]);

        $grade10 = Grade::create([
            'teacher_id' => $teacher->id,
            'name' => 'Grade 10',
        ]);

        Subject::create([
            'teacher_id' => $teacher->id,
            'grade_id' => $grade9->id,
            'name' => 'Algebra 1',
        ]);

        Subject::create([
            'teacher_id' => $teacher->id,
            'grade_id' => $grade9->id,
            'name' => 'Python Basics',
        ]);

        Subject::create([
            'teacher_id' => $teacher->id,
            'grade_id' => $grade10->id,
            'name' => 'Geometry',
        ]);
    }
}
