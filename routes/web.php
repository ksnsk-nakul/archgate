<?php

use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Frontend\ModulePageController;
use App\Http\Controllers\Public\PublicController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('services', [PublicController::class, 'services'])->name('public.services');
Route::get('about', [PublicController::class, 'about'])->name('public.about');
Route::get('careers', [PublicController::class, 'careers'])->name('public.careers');
Route::get('contact', [PublicController::class, 'contact'])->name('public.contact');
Route::post('landing/contact', [PublicController::class, 'submitContact'])->name('public.contact.submit')->middleware('throttle:5,1');
Route::get('library-preview', [PublicController::class, 'libraryPreview'])->name('public.library-preview');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [ModulePageController::class, 'dashboard'])->name('dashboard');

    Route::get('projects', [ModulePageController::class, 'projects'])->name('app.projects.index');
    Route::post('projects', [ModulePageController::class, 'storeProject'])->name('app.projects.store');
    Route::inertia('projects/create', 'Projects/Create')->name('app.projects.create');
    Route::get('projects/{project}', [ModulePageController::class, 'showProject'])->name('app.projects.show');
    Route::get('projects/{project}/edit', [ModulePageController::class, 'editProject'])->name('app.projects.edit');
    Route::put('projects/{project}', [ModulePageController::class, 'updateProject'])->name('app.projects.update');

    Route::get('tasks', [ModulePageController::class, 'tasks'])->name('app.tasks.index');
    Route::get('tasks/create', [ModulePageController::class, 'createTask'])->name('app.tasks.create');
    Route::post('tasks', [ModulePageController::class, 'storeTask'])->name('app.tasks.store');
    Route::get('tasks/{task}', [ModulePageController::class, 'showTask'])->name('app.tasks.show');
    Route::get('tasks/{task}/history', [ModulePageController::class, 'taskHistory'])->name('app.tasks.history');
    Route::get('tasks/{task}/edit', [ModulePageController::class, 'editTask'])->name('app.tasks.edit');
    Route::put('tasks/{task}', [ModulePageController::class, 'updateTask'])->name('app.tasks.update');
    Route::post('tasks/{task}/subtasks', [ModulePageController::class, 'storeSubtask'])->name('app.tasks.subtasks.store');
    Route::put('tasks/{task}/subtasks/{subtask}', [ModulePageController::class, 'updateSubtask'])->name('app.tasks.subtasks.update');

    Route::get('contacts', [ModulePageController::class, 'contacts'])->name('app.contacts.index');
    Route::get('contacts/create', [ModulePageController::class, 'createContact'])->name('app.contacts.create');
    Route::post('contacts', [ModulePageController::class, 'storeContact'])->name('app.contacts.store');
    Route::get('contacts/{contact}', [ModulePageController::class, 'showContact'])->name('app.contacts.show');
    Route::get('contacts/{contact}/edit', [ModulePageController::class, 'editContact'])->name('app.contacts.edit');
    Route::put('contacts/{contact}', [ModulePageController::class, 'updateContact'])->name('app.contacts.update');
    Route::delete('contacts/{contact}', [ModulePageController::class, 'destroyContact'])->name('app.contacts.destroy');

    Route::get('deals', [ModulePageController::class, 'deals'])->name('app.deals.index');
    Route::get('deals/create', [ModulePageController::class, 'createDeal'])->name('app.deals.create');
    Route::post('deals', [ModulePageController::class, 'storeDeal'])->name('app.deals.store');
    Route::delete('deals/{deal}', [ModulePageController::class, 'destroyDeal'])->name('app.deals.destroy');

    Route::get('library', [ModulePageController::class, 'library'])->name('app.library.index');
    Route::get('library/create', [ModulePageController::class, 'createLibraryItem'])->name('app.library.create');
    Route::post('library', [ModulePageController::class, 'storeLibraryItem'])->name('app.library.store');

    Route::get('courses', [ModulePageController::class, 'courses'])->name('app.courses.index');
    Route::get('courses/create', [ModulePageController::class, 'createCourse'])->name('app.courses.create');
    Route::post('courses', [ModulePageController::class, 'storeCourse'])->name('app.courses.store');
    Route::get('courses/{course}', [ModulePageController::class, 'showCourse'])->name('app.courses.show');
    Route::get('lessons/{lesson}', [ModulePageController::class, 'showLesson'])->name('app.lessons.show');

    Route::get('profile', [ModulePageController::class, 'profile'])->name('app.profile.show');
    Route::get('profile/edit', [ModulePageController::class, 'editProfile'])->name('app.profile.edit');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('settings/app', [SettingController::class, 'app'])->name('settings.app');
        Route::put('settings/app', [SettingController::class, 'updateApp'])->name('settings.app.update');
        Route::get('settings/third-party', [SettingController::class, 'thirdParty'])->name('settings.third-party');
        Route::put('settings/third-party', [SettingController::class, 'updateThirdParty'])->name('settings.third-party.update');
        Route::get('settings/landing', [SettingController::class, 'landing'])->name('settings.landing');
        Route::put('settings/landing', [SettingController::class, 'updateLanding'])->name('settings.landing.update');

        Route::get('roles', [RoleManagementController::class, 'index'])->name('roles.index');
        Route::post('roles/subadmin', [RoleManagementController::class, 'store'])->name('roles.subadmin.store');
        Route::get('roles/{role}/permissions', [RoleManagementController::class, 'editPermissions'])->name('roles.permissions.edit');
        Route::put('roles/{role}/permissions', [RoleManagementController::class, 'updatePermissions'])->name('roles.permissions.update');
    });
});

require __DIR__.'/settings.php';
