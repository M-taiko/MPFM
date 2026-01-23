<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskState;

class TaskStateHistory extends Model
{
    /**
     * Disable updated_at timestamp (we only track created_at)
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_state_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'plan_task_id',
        'from_state',
        'to_state',
        'changed_by',
        'changed_by_role',
        'notes',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'metadata' => 'array',
        'from_state' => TaskState::class,
        'to_state' => TaskState::class,
    ];

    /**
     * Get the plan task that owns this history entry.
     */
    public function planTask()
    {
        return $this->belongsTo(PlanTask::class);
    }

    /**
     * Get the user who made this state change.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Calculate duration spent in the 'from_state' (in minutes).
     * Returns null if still in current state.
     *
     * @return int|null
     */
    public function getDurationInState(): ?int
    {
        // Find the next state transition
        $nextTransition = static::where('plan_task_id', $this->plan_task_id)
            ->where('created_at', '>', $this->created_at)
            ->orderBy('created_at')
            ->first();

        if ($nextTransition) {
            return $this->created_at->diffInMinutes($nextTransition->created_at);
        }

        // Still in this state - return duration from transition until now
        return $this->created_at->diffInMinutes(now());
    }

    /**
     * Check if this was a forward transition (progress).
     *
     * @return bool
     */
    public function isForwardTransition(): bool
    {
        if (!$this->from_state) {
            return true; // Initial state is always forward
        }

        // Define the expected order
        $stateOrder = [
            TaskState::DRAFT->value => 1,
            TaskState::SUBMITTED_TO_SURVEILLANCE->value => 2,
            TaskState::ASSIGNED_TO_MPFM->value => 3,
            TaskState::IN_PROGRESS->value => 4,
            TaskState::MPFM_REVIEWED->value => 5,
            TaskState::SURVEILLANCE_REVIEWED->value => 6,
            TaskState::JOB_OFFICER_APPROVED->value => 7,
            TaskState::COMPLETED->value => 8,
        ];

        $fromOrder = $stateOrder[$this->from_state->value] ?? 0;
        $toOrder = $stateOrder[$this->to_state->value] ?? 0;

        return $toOrder > $fromOrder;
    }

    /**
     * Check if this was a rejection (backward transition).
     *
     * @return bool
     */
    public function isRejection(): bool
    {
        return !$this->isForwardTransition() && $this->from_state !== null;
    }

    /**
     * Scope: Get history for a specific task.
     */
    public function scopeForTask($query, int $taskId)
    {
        return $query->where('plan_task_id', $taskId);
    }

    /**
     * Scope: Get history for a specific state.
     */
    public function scopeToState($query, TaskState $state)
    {
        return $query->where('to_state', $state);
    }

    /**
     * Scope: Get rejections only.
     */
    public function scopeRejections($query)
    {
        return $query->whereNotNull('from_state')
            ->whereRaw("
                CASE
                    WHEN from_state = 'submitted_to_surveillance' AND to_state = 'draft' THEN 1
                    WHEN from_state = 'mpfm_reviewed' AND to_state = 'in_progress' THEN 1
                    WHEN from_state = 'surveillance_reviewed' AND to_state = 'mpfm_reviewed' THEN 1
                    WHEN from_state = 'job_officer_approved' AND to_state = 'surveillance_reviewed' THEN 1
                    ELSE 0
                END = 1
            ");
    }
}
