<?php

use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('module seven frontend pages require authentication', function (string $route) {
    $this->get(route($route))->assertRedirect(route('login'));
})->with([
    'projects' => 'app.projects.index',
    'tasks' => 'app.tasks.index',
    'contacts' => 'app.contacts.index',
    'deals' => 'app.deals.index',
    'library' => 'app.library.index',
    'courses' => 'app.courses.index',
    'profile' => 'app.profile.show',
]);

test('authenticated users can visit module seven frontend pages', function (string $route, string $component) {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route($route))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    'projects' => ['app.projects.index', 'Projects/Index'],
    'project create' => ['app.projects.create', 'Projects/Create'],
    'tasks' => ['app.tasks.index', 'Tasks/Index'],
    'task create' => ['app.tasks.create', 'Tasks/Create'],
    'contacts' => ['app.contacts.index', 'Contacts/Index'],
    'deals' => ['app.deals.index', 'Deals/Index'],
    'library' => ['app.library.index', 'Library/Index'],
    'courses' => ['app.courses.index', 'Courses/Index'],
    'profile' => ['app.profile.show', 'Profile/Show'],
    'profile edit' => ['app.profile.edit', 'Profile/Edit'],
]);

test('csp allows nonce protected shell assets and font stylesheets', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('app.projects.index'));

    $response->assertOk();
    expect($response->headers->get('Content-Security-Policy'))
        ->toContain('script-src')
        ->toContain('style-src-elem')
        ->toContain('https://fonts.bunny.net')
        ->toContain("style-src-elem 'self' 'unsafe-inline' https://fonts.bunny.net")
        ->toContain("style-src-attr 'unsafe-inline'");
    expect($response->getContent())
        ->toContain('<script nonce=')
        ->toContain('<style nonce=');
});

test('authenticated users can create projects through web routes', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('app.projects.store'), [
        'name' => 'Launch portal',
        'description' => 'Frontend-created project',
    ]);

    $response->assertRedirect(route('app.projects.index'));
    $this->assertDatabaseHas(Project::class, [
        'name' => 'Launch portal',
        'owner_id' => $user->id,
    ]);
});

test('authenticated users can edit projects through web routes', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $project = Project::factory()->create([
        'owner_id' => $user->id,
        'name' => 'Old project',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get(route('app.projects.edit', $project))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Projects/Edit')
            ->where('project.data.id', $project->id));

    $response = $this->actingAs($user)->put(route('app.projects.update', $project), [
        'name' => 'Updated project',
        'description' => 'Updated description',
        'status' => 'completed',
    ]);

    $response->assertRedirect(route('app.projects.show', $project));
    expect($project->refresh())
        ->name->toBe('Updated project')
        ->status->toBe('completed');
});

test('authenticated users can create tasks and log the activity', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $project = Project::factory()->create([
        'owner_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('app.tasks.store'), [
        'title' => 'Prepare launch checklist',
        'description' => 'Write the rollout steps',
        'status' => 'pending',
        'priority' => 'high',
        'project_id' => $project->id,
    ]);

    $task = Task::query()->where('title', 'Prepare launch checklist')->firstOrFail();

    $response->assertRedirect(route('app.tasks.show', $task));
    $this->assertModelExists($task);
    $this->assertDatabaseHas(TaskLog::class, [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'action' => 'created',
    ]);
});

test('authenticated users can update tasks and log changed fields', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $project = Project::factory()->create([
        'owner_id' => $user->id,
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'title' => 'Initial title',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->put(route('app.tasks.update', $task), [
        'title' => 'Updated title',
        'description' => $task->description,
        'status' => 'completed',
        'priority' => $task->priority,
        'project_id' => $project->id,
    ]);

    $response->assertRedirect(route('app.tasks.show', $task));
    expect($task->refresh())
        ->title->toBe('Updated title')
        ->status->toBe('completed');

    $log = TaskLog::query()
        ->where('task_id', $task->id)
        ->where('action', 'updated')
        ->firstOrFail();

    expect($log->changes)
        ->toHaveKey('title')
        ->toHaveKey('status');
});

test('task detail hides activity until the history page is opened', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $project = Project::factory()->create([
        'owner_id' => $user->id,
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);
    TaskLog::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'action' => 'updated',
    ]);

    $this->actingAs($user)
        ->get(route('app.tasks.show', $task))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Show')
            ->missing('logs'));

    $this->actingAs($user)
        ->get(route('app.tasks.history', $task))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/History')
            ->has('logs.data', 1));
});

test('authenticated users can create subtasks and log the activity', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $project = Project::factory()->create([
        'owner_id' => $user->id,
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($user)->post(route('app.tasks.subtasks.store', $task), [
        'title' => 'Draft launch notes',
    ]);

    $response->assertRedirect(route('app.tasks.show', $task));
    $this->assertDatabaseHas(Subtask::class, [
        'task_id' => $task->id,
        'title' => 'Draft launch notes',
        'completed' => false,
    ]);
    $this->assertDatabaseHas(TaskLog::class, [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'action' => 'subtask_created',
    ]);
});

test('authenticated users can update subtasks and log the activity', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $project = Project::factory()->create([
        'owner_id' => $user->id,
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);
    $subtask = Subtask::factory()->create([
        'task_id' => $task->id,
        'title' => 'Initial subtask',
        'completed' => false,
    ]);

    $response = $this->actingAs($user)->put(route('app.tasks.subtasks.update', [$task, $subtask]), [
        'title' => 'Updated subtask',
        'completed' => true,
    ]);

    $response->assertRedirect(route('app.tasks.show', $task));
    expect($subtask->refresh())
        ->title->toBe('Updated subtask')
        ->completed->toBeTrue();
    $this->assertDatabaseHas(TaskLog::class, [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'action' => 'subtask_updated',
    ]);
});
