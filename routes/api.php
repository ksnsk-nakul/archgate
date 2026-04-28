<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // --- Auth (Module 1) ---
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // --- Users (Module 2) ---
        Route::apiResource('users', \App\Http\Controllers\UserController::class);

        // --- Organizations (Module 2) ---
        Route::apiResource('organizations', \App\Http\Controllers\OrganizationController::class);

        // --- Roles & Permissions (Module 2) ---
        Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
        Route::post('user-role-assignments', [\App\Http\Controllers\UserRoleController::class, 'store']);

        // --- Projects (Module 3) ---
        Route::apiResource('projects', \App\Http\Controllers\ProjectController::class);

        // --- Tasks (Module 3) ---
        Route::get('tasks/today', [\App\Http\Controllers\TaskController::class, 'today']);
        Route::get('tasks/overdue', [\App\Http\Controllers\TaskController::class, 'overdue']);
        Route::get('tasks/upcoming', [\App\Http\Controllers\TaskController::class, 'upcoming']);
        Route::apiResource('tasks', \App\Http\Controllers\TaskController::class);
        Route::apiResource('tasks.subtasks', \App\Http\Controllers\SubtaskController::class)
            ->shallow();

        // --- CRM (Module 4) ---
        Route::apiResource('contacts', \App\Http\Controllers\ContactController::class);
        Route::apiResource('deals', \App\Http\Controllers\DealController::class);
        Route::apiResource('stages', \App\Http\Controllers\PipelineStageController::class)
            ->only(['index', 'store']);

        // --- Library (Module 5) ---
        Route::apiResource('content', \App\Http\Controllers\LibraryContentController::class);
        Route::apiResource('subscriptions', \App\Http\Controllers\SubscriptionController::class);

        // --- LMS (Module 6) ---
        Route::apiResource('courses', \App\Http\Controllers\CourseController::class);
        Route::apiResource('sections', \App\Http\Controllers\SectionController::class);
        Route::apiResource('lessons', \App\Http\Controllers\LessonController::class);
        Route::apiResource('enrollments', \App\Http\Controllers\EnrollmentController::class)
            ->only(['index', 'show', 'store']);
    });
});
