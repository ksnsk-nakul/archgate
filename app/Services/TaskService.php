<?php

namespace App\Services;

use App\Events\TaskChanged;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes, User $actor): Task
    {
        $task = Task::create($attributes);

        TaskChanged::dispatch($task, $actor, 'created', [
            'attributes' => $task->only($this->loggedFields()),
        ]);

        return $task;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Task $task, array $attributes, User $actor): Task
    {
        $before = $task->only($this->loggedFields());

        $task->update($attributes);
        $task->refresh();

        $after = $task->only($this->loggedFields());
        $changes = [];

        foreach ($after as $field => $value) {
            if (($before[$field] ?? null) !== $value) {
                $changes[$field] = [
                    'from' => $before[$field] ?? null,
                    'to' => $value,
                ];
            }
        }

        TaskChanged::dispatch($task, $actor, 'updated', $changes);

        return $task;
    }

    /**
     * @param  array{title: string, completed?: bool}  $attributes
     */
    public function createSubtask(Task $task, array $attributes, User $actor): Subtask
    {
        $subtask = $task->subtasks()->create([
            'title' => $attributes['title'],
            'completed' => $attributes['completed'] ?? false,
        ]);

        TaskChanged::dispatch($task, $actor, 'subtask_created', [
            'subtask' => $subtask->only(['id', 'title', 'completed']),
        ]);

        return $subtask;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateSubtask(Task $task, Subtask $subtask, array $attributes, User $actor): Subtask
    {
        $before = $subtask->only(['title', 'completed']);

        $subtask->update($attributes);
        $subtask->refresh();

        $after = $subtask->only(['title', 'completed']);
        $changes = [];

        foreach ($after as $field => $value) {
            if (($before[$field] ?? null) !== $value) {
                $changes[$field] = [
                    'from' => $before[$field] ?? null,
                    'to' => $value,
                ];
            }
        }

        TaskChanged::dispatch($task, $actor, 'subtask_updated', [
            'subtask_id' => $subtask->id,
            'changes' => $changes,
        ]);

        return $subtask;
    }

    /**
     * @return array<int, string>
     */
    private function loggedFields(): array
    {
        return [
            'title',
            'description',
            'status',
            'priority',
            'due_date',
            'project_id',
            'assignee_id',
        ];
    }
}
