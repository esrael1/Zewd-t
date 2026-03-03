<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Material;
use App\Models\LiveClass;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\JitsiTokenService;

class TeacherController extends Controller
{
    private function getTeacher(Request $request)
    {
        return Teacher::where('user_id', $request->user()->id)->firstOrFail();
    }

    public function dashboard(Request $request)
    {
        $teacher = $this->getTeacher($request);
        
        return response()->json([
            'teacher_profile' => $teacher,
            'students_count' => Student::where('teacher_id', $teacher->id)->count(),
            'classes_count' => LiveClass::where('teacher_id', $teacher->id)->count(),
            'materials_count' => Material::where('teacher_id', $teacher->id)->count(),
            'subscription_status' => $teacher->subscriptions()->where('status', 'active')->first() ? 'Active' : 'Inactive',
        ]);
    }

    // --- Students ---

    public function getStudents(Request $request)
    {
        $teacher = $this->getTeacher($request);
        return response()->json(Student::with(['user', 'grade'])->where('teacher_id', $teacher->id)->get());
    }

    public function addStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'grade_id' => 'nullable|exists:grades,id',
        ]);

        $teacher = $this->getTeacher($request);

        return DB::transaction(function () use ($request, $teacher) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'student',
                'status' => 'active',
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'teacher_id' => $teacher->id,
                'grade_id' => $request->grade_id,
                'status' => 'active',
            ]);

            return response()->json(['message' => 'Student added', 'student' => $student], 201);
        });
    }

    public function toggleStudentStatus($id)
    {
        $teacher = $this->getTeacher(request());
        $student = Student::where('id', $id)->where('teacher_id', $teacher->id)->firstOrFail();
        
        $newStatus = $student->status === 'active' ? 'suspended' : 'active';
        $student->update(['status' => $newStatus]);
        
        // Also update the linked user status for consistency
        $student->user->update(['status' => $newStatus]);
        
        return response()->json(['message' => "Student is now {$newStatus}", 'status' => $newStatus]);
    }

    // --- Grades & Subjects ---

    public function getGrades(Request $request)
    {
        $teacher = $this->getTeacher($request);
        return response()->json(Grade::with('subjects')->where('teacher_id', $teacher->id)->get());
    }

    public function addGrade(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $teacher = $this->getTeacher($request);

        $grade = Grade::create([
            'teacher_id' => $teacher->id,
            'name' => $request->name,
        ]);

        return response()->json($grade, 201);
    }

    public function updateGrade(Request $request, $id)
    {
        $request->validate(['name' => 'required|string']);
        $teacher = $this->getTeacher($request);
        $grade = Grade::where('id', $id)->where('teacher_id', $teacher->id)->firstOrFail();
        
        $grade->update(['name' => $request->name]);
        return response()->json($grade);
    }

    public function deleteGrade($id)
    {
        $teacher = $this->getTeacher(request());
        Grade::where('id', $id)->where('teacher_id', $teacher->id)->delete();
        return response()->json(['message' => 'Grade deleted']);
    }

    public function getSubjects(Request $request)
    {
        $teacher = $this->getTeacher($request);
        return response()->json(Subject::with('grade')->where('teacher_id', $teacher->id)->get());
    }

    public function addSubject(Request $request)
    {
        $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'name' => 'required|string'
        ]);
        $teacher = $this->getTeacher($request);

        $subject = Subject::create([
            'teacher_id' => $teacher->id,
            'grade_id' => $request->grade_id,
            'name' => $request->name,
        ]);

        return response()->json($subject, 201);
    }

    public function updateSubject(Request $request, $id)
    {
        $request->validate(['name' => 'required|string']);
        $teacher = $this->getTeacher($request);
        $subject = Subject::where('id', $id)->where('teacher_id', $teacher->id)->firstOrFail();
        
        $subject->update(['name' => $request->name]);
        return response()->json($subject);
    }

    // --- Materials ---

    public function getMaterials(Request $request)
    {
        $teacher = $this->getTeacher($request);
        return response()->json(Material::where('teacher_id', $teacher->id)->get());
    }

    public function uploadMaterial(Request $request)
    {
         $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string',
            'file' => 'required|file|max:10240', // 10MB
            'type' => 'required|in:pdf,video,image,other'
        ]);

        $teacher = $this->getTeacher($request);
        
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materials', 'public');

            $material = Material::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $request->subject_id,
                'title' => $request->title,
                'file_path' => $path,
                'type' => $request->type
            ]);

            return response()->json($material, 201);
        }

        return response()->json(['message' => 'File upload failed'], 400);
    }

    public function deleteMaterial($id)
    {
        Material::destroy($id);
        return response()->json(['message' => 'Material deleted']);
    }

    // --- Live Classes ---

    public function getLiveClasses(Request $request)
    {
        $teacher = $this->getTeacher($request);
        return response()->json(LiveClass::where('teacher_id', $teacher->id)->orderBy('start_time', 'desc')->get());
    }

    public function createLiveClass(Request $request)
    {
         $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string',
            'start_time' => 'required|date',
        ]);

        $teacher = $this->getTeacher($request);
        
        do {
            $meetingId = 'zewd-' . Str::lower(Str::random(12));
        } while (LiveClass::where('meeting_id', $meetingId)->exists());

        $liveClass = LiveClass::create([
            'teacher_id' => $teacher->id,
            'grade_id' => $request->grade_id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'meeting_id' => $meetingId,
            'start_time' => $request->start_time,
            'status' => 'scheduled'
        ]);

        return response()->json($liveClass, 201);
    }

    public function startClass($meetingId)
    {
        $teacher = $this->getTeacher(request());
        $liveClass = LiveClass::where('meeting_id', $meetingId)->where('teacher_id', $teacher->id)->firstOrFail();
        
        $liveClass->update(['status' => 'live']);
        
        return response()->json(['message' => 'Class started successfully', 'class' => $liveClass]);
    }

    public function endClass($meetingId)
    {
        $teacher = $this->getTeacher(request());
        $liveClass = LiveClass::where('meeting_id', $meetingId)->where('teacher_id', $teacher->id)->firstOrFail();
        
        $liveClass->update(['status' => 'ended']);
        
        return response()->json(['message' => 'Class ended successfully', 'class' => $liveClass]);
    }

    public function getClassToken($meetingId)
    {
        // Add check if meeting exists and teacher owns it
        return response()->json([
            'token' => JitsiTokenService::generateToken(auth()->user()->name, $meetingId, true)
        ]);
    }

    // --- Subscription ---

    public function getSubscription(Request $request)
    {
        $teacher = $this->getTeacher($request);
        $sub = $teacher->subscriptions()->orderBy('created_at', 'desc')->first();
        return response()->json($sub);
    }

    public function getPlans()
    {
        return response()->json(Plan::orderBy('price')->get());
    }

    public function createSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'screenshot' => 'required|image'
        ]);

        $teacher = $this->getTeacher($request);

        if ($request->hasFile('screenshot')) {
             $path = $request->file('screenshot')->store('payments', 'public');

             $sub = Subscription::create([
                 'teacher_id' => $teacher->id,
                 'plan_id' => $request->plan_id,
                 'payment_screenshot' => $path,
                 'status' => 'pending'
             ]);

             return response()->json($sub, 201);
        }

        return response()->json(['message' => 'Screenshot required'], 400);

    }
    public function updateProfile(Request $request)
    {
        $teacher = $this->getTeacher($request);
        
        $request->validate([
            'academy_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'social_links' => 'nullable', // Can be JSON string from FormData
            'profile_image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only([
            'academy_name', 'bio', 'specialization', 'phone_number'
        ]);

        if ($request->has('social_links')) {
            $data['social_links'] = is_string($request->social_links) 
                ? json_decode($request->social_links, true) 
                : $request->social_links;
        }

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('avatars', 'public');
            $data['profile_image_path'] = asset('storage/' . $path);
        }

        $teacher->update($data);

        return response()->json(['message' => 'Profile updated successfully', 'teacher' => $teacher]);
    }
}
