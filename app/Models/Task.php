<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable(['title', 'description', 'status', 'priority', 'due_date', 'project_id', 'assignee_id'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', Carbon::today());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', Carbon::today())
            ->where('status', '!=', 'completed');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('due_date', '>', Carbon::today())
            ->where('status', '!=', 'completed');
    }
}
