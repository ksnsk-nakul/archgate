<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendTaskDeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Task $task) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->due_date->toDateString(),
            'project_id' => $this->task->project_id,
        ];
    }
}
