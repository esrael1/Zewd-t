<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Material;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use App\Services\JitsiTokenService;

class StudentController extends Controller
{
    private function getStudent(Request $request)
    {
        return Student::where('user_id', $request->user()->id)->firstOrFail();
    }

    public function dashboard(Request $request)
    {
        $student = $this->getStudent($request)->load('grade');
        
        return response()->json([
            'student_profile' => $student,
            'teacher' => $student->teacher->user->name,
            'grade' => $student->grade->name ?? 'Not Assigned',
            'subjects_count' => Subject::where('grade_id', $student->grade_id)->count(),
            'live_classes_count' => LiveClass::where('grade_id', $student->grade_id)->whereIn('status', ['live', 'scheduled'])->count(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $student = $this->getStudent($request);
        
        $request->validate([
            'bio' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['bio', 'phone_number']);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('avatars', 'public');
            $data['profile_image_path'] = asset('storage/' . $path);
        }

        $student->update($data);

        return response()->json(['message' => 'Profile updated successfully', 'student' => $student]);
    }

    public function getSubjects(Request $request)
    {
        $student = $this->getStudent($request);

        if (!$student->grade_id) {
            return response()->json([]);
        }

        return response()->json(Subject::where('grade_id', $student->grade_id)->get());
    }

    public function getMaterials(Request $request, $subjectId)
    {
         $student = $this->getStudent($request);
         // Ensure subject belongs to student's grade
         $subject = Subject::where('id', $subjectId)->where('grade_id', $student->grade_id)->firstOrFail();

         return response()->json(Material::where('subject_id', $subject->id)->get());
    }

    public function getLiveClasses(Request $request)
    {
        $student = $this->getStudent($request);
        
        if (!$student->grade_id) {
             return response()->json([]);
        }

        return response()->json(LiveClass::where('grade_id', $student->grade_id)
            ->whereIn('status', ['live', 'scheduled'])
            ->orderBy('start_time', 'asc')
            ->get());
    }

    public function getClassToken($meetingId)
    {
        return response()->json([
            'token' => JitsiTokenService::generateToken(auth()->user()->name, $meetingId, false)
        ]);
    }
}
