<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubtaskResource;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubtaskController extends Controller
{
    public function index(Task $task): AnonymousResourceCollection
    {
        return SubtaskResource::collection($task->subtasks);
    }

    public function store(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'completed' => ['sometimes', 'boolean'],
        ]);

        $subtask = $task->subtasks()->create([
            'title' => $validated['title'],
            'completed' => $validated['completed'] ?? false,
        ]);

        return (new SubtaskResource($subtask))->response()->setStatusCode(201);
    }

    public function show(Task $task, Subtask $subtask): SubtaskResource
    {
        return new SubtaskResource($subtask);
    }

    public function update(Request $request, Task $task, Subtask $subtask): SubtaskResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'completed' => ['sometimes', 'boolean'],
        ]);

        $subtask->update($validated);

        return new SubtaskResource($subtask);
    }

    public function destroy(Task $task, Subtask $subtask): JsonResponse
    {
        $subtask->delete();

        return response()->json(null, 204);
    }
}
