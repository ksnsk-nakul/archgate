<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LibraryContentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PipelineStageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // --- Auth (Module 1) ---
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // --- Users (Module 2) ---
        Route::apiResource('users', UserController::class);

        // --- Organizations (Module 2) ---
        Route::apiResource('organizations', OrganizationController::class);

        // --- Roles & Permissions (Module 2) ---
        Route::apiResource('roles', RoleController::class);
        Route::post('user-role-assignments', [UserRoleController::class, 'store']);
        Route::delete('user-role-assignments', [UserRoleController::class, 'destroy']);

        // --- Projects (Module 3) ---
        Route::apiResource('projects', ProjectController::class);

        // --- Tasks (Module 3) ---
        Route::get('tasks/today', [TaskController::class, 'today']);
        Route::get('tasks/overdue', [TaskController::class, 'overdue']);
        Route::get('tasks/upcoming', [TaskController::class, 'upcoming']);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('tasks.subtasks', SubtaskController::class);

        // --- CRM (Module 4) ---
        Route::apiResource('contacts', ContactController::class);
        Route::apiResource('deals', DealController::class);
        Route::apiResource('stages', PipelineStageController::class)
            ->only(['index', 'store']);
        Route::get('reports/leads', [ReportController::class, 'leads']);

        // --- Library (Module 5) ---
        // Free routes: list all content, manage subscriptions, and admin CRUD
        Route::get('content', [LibraryContentController::class, 'index']);
        Route::post('content', [LibraryContentController::class, 'store']);
        Route::match(['put', 'patch'], 'content/{content}', [LibraryContentController::class, 'update']);
        Route::delete('content/{content}', [LibraryContentController::class, 'destroy']);
        Route::apiResource('subscriptions', SubscriptionController::class);

        // Premium-gated: viewing a specific content item requires an active subscription
        Route::middleware('subscription:premium')->group(function () {
            Route::get('content/{content}', [LibraryContentController::class, 'show']);
        });

        // --- LMS (Module 6) ---
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('lessons', LessonController::class);
        Route::apiResource('enrollments', EnrollmentController::class)
            ->only(['index', 'show', 'store']);
    });
});
