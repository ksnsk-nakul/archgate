<?php

use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;

uses(LazilyRefreshDatabase::class);

// --- Projects ---

test('authenticated user can list their projects', function () {
    $user = User::factory()->create();
    Project::factory(3)->create(['owner_id' => $user->id]);
    Project::factory(2)->create(); // other users' projects

    $this->actingAs($user)
        ->getJson('/api/v1/projects')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('unauthenticated user cannot list projects', function () {
    $this->getJson('/api/v1/projects')->assertUnauthorized();
});

test('user can create a project', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/projects', [
            'name' => 'New Project',
            'description' => 'A test project',
            'status' => 'active',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'New Project')
        ->assertJsonPath('data.owner_id', $user->id);

    expect(Project::where('name', 'New Project')->exists())->toBeTrue();
});

test('project creation requires a name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/projects', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('project status must be valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/projects', [
            'name' => 'Test',
            'status' => 'invalid-status',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('user can view their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $this->actingAs($user)
        ->getJson("/api/v1/projects/{$project->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $project->id);
});

test('user can update their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $this->actingAs($user)
        ->putJson("/api/v1/projects/{$project->id}", ['name' => 'Updated Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name');
});

test('user can delete their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $this->actingAs($user)
        ->deleteJson("/api/v1/projects/{$project->id}")
        ->assertNoContent();

    expect(Project::find($project->id))->toBeNull();
});

// --- Tasks ---

test('authenticated user can list tasks', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);
    Task::factory(3)->create(['project_id' => $project->id]);

    $this->actingAs($user)
        ->getJson('/api/v1/tasks')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('user can create a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson('/api/v1/tasks', [
            'title' => 'Fix the bug',
            'project_id' => $project->id,
            'priority' => 'high',
            'status' => 'pending',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Fix the bug')
        ->assertJsonPath('data.priority', 'high');
});

test('task creation requires title and project_id', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/tasks', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'project_id']);
});

test('task project_id must exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/tasks', [
            'title' => 'Task',
            'project_id' => 9999,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['project_id']);
});

test('user can view a task with subtasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()
        ->has(Subtask::factory(2))
        ->create();

    $this->actingAs($user)
        ->getJson("/api/v1/tasks/{$task->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $task->id)
        ->assertJsonCount(2, 'data.subtasks');
});

test('user can update a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();

    $this->actingAs($user)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'status' => 'completed',
            'priority' => 'low',
        ])
        ->assertOk()
        ->assertJsonPath('data.status', 'completed')
        ->assertJsonPath('data.priority', 'low');
});

test('user can delete a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/tasks/{$task->id}")
        ->assertNoContent();

    expect(Task::find($task->id))->toBeNull();
});

// --- Task Filters ---

test('today filter returns tasks due today', function () {
    $user = User::factory()->create();
    Task::factory()->create(['due_date' => Carbon::today()]);
    Task::factory()->create(['due_date' => Carbon::tomorrow()]);
    Task::factory()->create(['due_date' => Carbon::yesterday()]);

    $this->actingAs($user)
        ->getJson('/api/v1/tasks/today')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('overdue filter returns incomplete past-due tasks', function () {
    $user = User::factory()->create();
    Task::factory()->create(['due_date' => Carbon::yesterday(), 'status' => 'pending']);
    Task::factory()->create(['due_date' => Carbon::yesterday(), 'status' => 'completed']);
    Task::factory()->create(['due_date' => Carbon::tomorrow(), 'status' => 'pending']);

    $this->actingAs($user)
        ->getJson('/api/v1/tasks/overdue')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('upcoming filter returns future incomplete tasks', function () {
    $user = User::factory()->create();
    Task::factory()->create(['due_date' => Carbon::tomorrow(), 'status' => 'pending']);
    Task::factory()->create(['due_date' => Carbon::tomorrow(), 'status' => 'completed']);
    Task::factory()->create(['due_date' => Carbon::yesterday(), 'status' => 'pending']);

    $this->actingAs($user)
        ->getJson('/api/v1/tasks/upcoming')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

// --- Subtasks ---

test('user can list subtasks for a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->has(Subtask::factory(3))->create();

    $this->actingAs($user)
        ->getJson("/api/v1/tasks/{$task->id}/subtasks")
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('user can create a subtask', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();

    $this->actingAs($user)
        ->postJson("/api/v1/tasks/{$task->id}/subtasks", ['title' => 'Sub item'])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Sub item')
        ->assertJsonPath('data.completed', false);
});

test('subtask creation requires title', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();

    $this->actingAs($user)
        ->postJson("/api/v1/tasks/{$task->id}/subtasks", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('user can update a subtask', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    $subtask = Subtask::factory()->create(['task_id' => $task->id]);

    $this->actingAs($user)
        ->putJson("/api/v1/tasks/{$task->id}/subtasks/{$subtask->id}", ['completed' => true])
        ->assertOk()
        ->assertJsonPath('data.completed', true);
});

test('user can delete a subtask', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    $subtask = Subtask::factory()->create(['task_id' => $task->id]);

    $this->actingAs($user)
        ->deleteJson("/api/v1/tasks/{$task->id}/subtasks/{$subtask->id}")
        ->assertNoContent();

    expect(Subtask::find($subtask->id))->toBeNull();
});
