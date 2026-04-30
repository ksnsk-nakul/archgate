<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TaskResource::collection(Task::with('subtasks')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:pending,in_progress,completed'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $task = Task::create($validated);

        return (new TaskResource($task->load('subtasks')))->response()->setStatusCode(201);
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load('subtasks'));
    }

    public function update(Request $request, Task $task): TaskResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:pending,in_progress,completed'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $task->update($validated);

        return new TaskResource($task->load('subtasks'));
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }

    public function today(): AnonymousResourceCollection
    {
        return TaskResource::collection(Task::today()->with('subtasks')->get());
    }

    public function overdue(): AnonymousResourceCollection
    {
        return TaskResource::collection(Task::overdue()->with('subtasks')->get());
    }

    public function upcoming(): AnonymousResourceCollection
    {
        return TaskResource::collection(Task::upcoming()->with('subtasks')->get());
    }
}
