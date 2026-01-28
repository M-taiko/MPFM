<?php

namespace App\Domains\Task\Models;

use App\Enums\TaskState;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskStateHistory extends Model
{
    protected $table = 'task_state_histories';

    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'from_state',
        'to_state',
        'changed_by_user_id',
        'changed_by_role',
        'notes',
        'changed_at',
    ];

    protected $casts = [
        'from_state' => TaskState::class,
        'to_state' => TaskState::class,
        'changed_at' => 'datetime',
    ];

    /**
     * RELATIONSHIPS
     */

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    /**
     * SCOPES
     */

    public function scopeForTask($query, $taskId)
    {
        return $query->where('task_id', $taskId)->orderBy('changed_at', 'desc');
    }

    public function scopeToState($query, TaskState $state)
    {
        return $query->where('to_state', $state->value);
    }

    /**
     * HELPER METHODS
     */

    public function getDurationInState(): int
    {
        $nextHistory = TaskStateHistory::where('task_id', $this->task_id)
            ->where('changed_at', '>', $this->changed_at)
            ->orderBy('changed_at', 'asc')
            ->first();

        if ($nextHistory) {
            return $this->changed_at->diffInMinutes($nextHistory->changed_at);
        }

        // Still in this state
        return $this->changed_at->diffInMinutes(now());
    }

    public function isForwardTransition(): bool
    {
        $transitions = [
            TaskState::DRAFT => 1,
            TaskState::SUBMITTED_TO_SURVEILLANCE => 2,
            TaskState::ASSIGNED_TO_DEPARTMENT => 3,
            TaskState::IN_PROGRESS => 4,
            TaskState::DEPARTMENT_REVIEWED => 5,
            TaskState::SURVEILLANCE_REVIEWED => 6,
            TaskState::JOB_OFFICER_APPROVED => 7,
            TaskState::COMPLETED => 8,
        ];

        $fromLevel = $transitions[$this->from_state] ?? 0;
        $toLevel = $transitions[$this->to_state] ?? 0;

        return $toLevel > $fromLevel;
    }

    public function isRejection(): bool
    {
        return !$this->isForwardTransition();
    }
}
