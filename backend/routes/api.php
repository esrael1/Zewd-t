<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/update-password', [AuthController::class, 'updatePassword']);

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard']);
        Route::get('/teachers', [\App\Http\Controllers\AdminController::class, 'getTeachers']);
        Route::get('/students', [\App\Http\Controllers\AdminController::class, 'getStudents']);
        Route::get('/database-data', [\App\Http\Controllers\AdminController::class, 'getDatabaseData']);
        Route::post('/teachers/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveTeacher']);
        Route::post('/teachers/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectTeacher']);

        Route::get('/subscriptions', [\App\Http\Controllers\AdminController::class, 'getSubscriptions']);
        Route::post('/subscriptions/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveSubscription']);

        Route::get('/plans', [\App\Http\Controllers\AdminController::class, 'getPlans']);
        Route::post('/plans', [\App\Http\Controllers\AdminController::class, 'createPlan']);
        Route::put('/plans/{id}', [\App\Http\Controllers\AdminController::class, 'updatePlan']);
        Route::delete('/plans/{id}', [\App\Http\Controllers\AdminController::class, 'deletePlan']);
    });

    // Teacher Routes
    Route::middleware(['role:teacher'])->prefix('teacher')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TeacherController::class, 'dashboard']);
        
        Route::get('/subscription', [\App\Http\Controllers\TeacherController::class, 'getSubscription']);
        Route::get('/plans', [\App\Http\Controllers\TeacherController::class, 'getPlans']);
        Route::post('/subscription', [\App\Http\Controllers\TeacherController::class, 'createSubscription']);
        Route::put('/profile', [\App\Http\Controllers\TeacherController::class, 'updateProfile']);

        Route::middleware(['subscription'])->group(function () {
             Route::get('/students', [\App\Http\Controllers\TeacherController::class, 'getStudents']);
             Route::post('/students', [\App\Http\Controllers\TeacherController::class, 'addStudent']);
             Route::post('/students/{id}/toggle-status', [\App\Http\Controllers\TeacherController::class, 'toggleStudentStatus']);

             Route::get('/grades', [\App\Http\Controllers\TeacherController::class, 'getGrades']);
             Route::post('/grades', [\App\Http\Controllers\TeacherController::class, 'addGrade']);
             Route::put('/grades/{id}', [\App\Http\Controllers\TeacherController::class, 'updateGrade']);
             Route::delete('/grades/{id}', [\App\Http\Controllers\TeacherController::class, 'deleteGrade']);

             Route::get('/subjects', [\App\Http\Controllers\TeacherController::class, 'getSubjects']);
             Route::post('/subjects', [\App\Http\Controllers\TeacherController::class, 'addSubject']);
             Route::put('/subjects/{id}', [\App\Http\Controllers\TeacherController::class, 'updateSubject']);

             Route::get('/materials', [\App\Http\Controllers\TeacherController::class, 'getMaterials']);
             Route::post('/materials', [\App\Http\Controllers\TeacherController::class, 'uploadMaterial']);
             Route::delete('/materials/{id}', [\App\Http\Controllers\TeacherController::class, 'deleteMaterial']);

             Route::get('/live-classes', [\App\Http\Controllers\TeacherController::class, 'getLiveClasses']);
             Route::post('/live-classes', [\App\Http\Controllers\TeacherController::class, 'createLiveClass']);
             Route::put('/live-classes/{meetingId}', [\App\Http\Controllers\TeacherController::class, 'updateLiveClass']);
             Route::delete('/live-classes/{meetingId}', [\App\Http\Controllers\TeacherController::class, 'deleteLiveClass']);
             Route::post('/live-classes/{meetingId}/start', [\App\Http\Controllers\TeacherController::class, 'startClass']);
             Route::post('/live-classes/{meetingId}/end', [\App\Http\Controllers\TeacherController::class, 'endClass']);
             Route::get('/live-classes/{meetingId}/token', [\App\Http\Controllers\TeacherController::class, 'getClassToken']);
        });
    });

    // Student Routes
    Route::middleware(['role:student'])->prefix('student')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StudentController::class, 'dashboard']);
        Route::put('/profile', [\App\Http\Controllers\StudentController::class, 'updateProfile']);
        Route::get('/subjects', [\App\Http\Controllers\StudentController::class, 'getSubjects']);
        Route::get('/subjects/{id}/materials', [\App\Http\Controllers\StudentController::class, 'getMaterials']);
        Route::get('/live-classes', [\App\Http\Controllers\StudentController::class, 'getLiveClasses']);
        Route::get('/live-classes/{meetingId}/token', [\App\Http\Controllers\StudentController::class, 'getClassToken']);
    });
});
