<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Teacher;
use Carbon\Carbon;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->role !== 'teacher') {
            return $next($request);
        }

        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found'], 404);
        }

        if ($teacher->status !== 'approved') {
            return response()->json(['message' => 'Account not approved yet'], 403);
        }

        $activeSubscription = $teacher->subscriptions()
            ->where('status', 'active')
            ->whereDate('end_date', '>=', Carbon::now())
            ->first();

        if (!$activeSubscription) {
             return response()->json(['message' => 'No active subscription found or subscription expired'], 403);
        }

        return $next($request);
    }
}
