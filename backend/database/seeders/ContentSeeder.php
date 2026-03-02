<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Material;
use App\Models\LiveClass;
use Carbon\Carbon;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = Teacher::first();
        $subject = Subject::where('teacher_id', $teacher->id)->first();

        Material::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'title' => 'Python Intro PDF',
            'file_path' => 'seeder/py_intro.pdf',
            'type' => 'pdf',
        ]);

        Material::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'title' => 'Variables Video',
            'file_path' => 'seeder/vars.mp4',
            'type' => 'video',
        ]);

        LiveClass::create([
            'teacher_id' => $teacher->id,
            'grade_id' => $subject->grade_id,
            'subject_id' => $subject->id,
            'title' => 'First Live Class',
            'meeting_id' => 'zewd-meeting-789',
            'meeting_link' => 'https://meet.jit.si/zewd-meeting-789',
            'start_time' => Carbon::now()->addHours(2),
            'status' => 'scheduled',
        ]);
    }
}
