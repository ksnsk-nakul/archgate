<?php

namespace App\Listeners;

use App\Events\TaskChanged;
use App\Models\TaskLog;

class LogTaskChange
{
    /**
     * Handle the event.
     */
    public function handle(TaskChanged $event): void
    {
        TaskLog::create([
            'task_id' => $event->task->id,
            'user_id' => $event->user?->id,
            'action' => $event->action,
            'changes' => $event->changes,
        ]);
    }
}
