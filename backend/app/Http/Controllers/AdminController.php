<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Plan;
use App\Models\User;
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
        return response()->json(\App\Models\Student::with(['user', 'teacher.user', 'grade'])->get());
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
