<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Plan;
use App\Models\User;
use App\Models\Grade;
use App\Models\LiveClass;
use App\Models\Material;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'teachers_total' => Teacher::count(),
            'teachers_pending' => Teacher::where('status', 'pending')->count(),
            'students_total' => \App\Models\Student::count(),
            'active_classes' => \App\Models\LiveClass::where('status', 'live')->count(),
            'total_plans' => Plan::count(),
            'pending_subscriptions' => \App\Models\Subscription::where('status', 'pending')->count(),
        ]);
    }

    public function getStudents()
    {
        return response()->json(Student::with(['user', 'teacher.user', 'grade'])->get());
    }

    public function getDatabaseData()
    {
        $tables = [
            'users' => User::orderBy('id')->get(),
            'teachers' => Teacher::with('user')->orderBy('id')->get(),
            'students' => Student::with(['user', 'teacher.user', 'grade'])->orderBy('id')->get(),
            'plans' => Plan::orderBy('id')->get(),
            'subscriptions' => Subscription::with(['plan', 'teacher.user'])->orderBy('id')->get(),
            'grades' => Grade::orderBy('id')->get(),
            'subjects' => Subject::with(['teacher.user', 'grade'])->orderBy('id')->get(),
            'materials' => Material::with('subject')->orderBy('id')->get(),
            'live_classes' => LiveClass::with(['subject', 'grade'])->orderBy('id')->get(),
        ];

        $counts = [];
        foreach ($tables as $name => $rows) {
            $counts[$name] = $rows->count();
        }

        return response()->json([
            'counts' => $counts,
            'tables' => $tables,
        ]);
    }

    // --- Teacher Management ---

    public function getTeachers(Request $request)
    {
        $status = $request->query('status');
        
        $query = Teacher::with('user');

        if ($status) {
            $query->where('status', $status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function approveTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->status = 'approved';
        $teacher->approved_by = auth()->id(); // Assuming admin is logged in
        $teacher->save();

        return response()->json(['message' => 'Teacher approved successfully', 'teacher' => $teacher]);
    }

    public function rejectTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->status = 'rejected';
        $teacher->save();

        return response()->json(['message' => 'Teacher rejected', 'teacher' => $teacher]);
    }

    // --- Plan Management ---

    public function getPlans()
    {
        return response()->json(Plan::all());
    }

    public function createPlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'max_students' => 'required|integer',
            'max_live_classes' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $plan = Plan::create($validated);

        return response()->json(['message' => 'Plan created', 'plan' => $plan], 201);
    }

    public function updatePlan(Request $request, $id)
    {
         $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'max_students' => 'required|integer',
            'max_live_classes' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $plan = Plan::findOrFail($id);
        $plan->update($validated);

        return response()->json(['message' => 'Plan updated', 'plan' => $plan]);
    }

    public function deletePlan($id)
    {
        Plan::destroy($id);
        return response()->json(['message' => 'Plan deleted']);
    }

    // --- Subscription Management ---

    public function getSubscriptions(Request $request)
    {
        $status = $request->query('status');
        $query = \App\Models\Subscription::with(['plan', 'teacher.user']);
        
        if ($status) {
            $query->where('status', $status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function approveSubscription($id)
    {
        $sub = \App\Models\Subscription::findOrFail($id);
        $sub->update([
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays($sub->plan->duration_days),
            'approved_by' => auth()->id()
        ]);

        return response()->json(['message' => 'Subscription approved', 'subscription' => $sub]);
    }
}
