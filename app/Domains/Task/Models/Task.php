<?php

namespace App\Domains\Task\Models;

use App\Enums\TaskState;
use App\Models\User;
use App\Models\Department;
use App\Models\OilString;
use App\Models\Tower;
use App\Models\MonthlyPlan;
use App\Models\MPFMTest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'title',              // Task title
        'monthly_plan_id',
        'department_id',
        'task_type',          // New: MPFM, wireline, production, drilling, etc.
        'state',              // DRAFT, SUBMITTED, ASSIGNED, IN_PROGRESS, REVIEWED, APPROVED, COMPLETED
        'priority',           // low, normal, high, critical
        'oil_string_id',
        'tower_id',
        'assigned_to_user_id',
        'created_by_user_id',
        'expected_completion_at',
        'actual_completion_at',
        'mpfm_test_id',
        'type_specific_data', // JSON: task-type-specific fields
        'description',
        'notes',
        // Legacy fields (for backward compatibility)
        'sno', 'well_number', 'string_number', 'well_full_name',
        'flow_route', 'reservoir_layer', 'test_location',
        'electrification_status', 'mpfm_type', 'dhsv_type',
        'sub_category', 'category', 'required_whfp', 'tested_whfp',
        'production_rate', 'scheduled_date', 'completion_status',
        'completion_result', 'completed_at', 'final_status',
        'is_recurring', 'parent_task_id', 'supervisor_remarks',
        'hq_notes', 'operator_notes', 'surveillance_supervisor_id',
        'surveillance_assigned_at', 'overdue_flag', 'sla_notified_at',
    ];

    protected $casts = [
        'state' => TaskState::class,
        'expected_completion_at' => 'datetime',
        'actual_completion_at' => 'datetime',
        'scheduled_date' => 'date',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'surveillance_assigned_at' => 'datetime',
        'is_recurring' => 'boolean',
        'overdue_flag' => 'boolean',
        'sla_notified_at' => 'datetime',
        'type_specific_data' => 'json',
    ];

    /**
     * RELATIONSHIPS
     */

    public function monthlyPlan(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlan::class, 'monthly_plan_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function operators(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'task_operators',
            'task_id',
            'user_id'
        )->withPivot('assigned_at', 'assigned_by', 'is_primary')
         ->withTimestamps();
    }

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class, 'oil_string_id')->nullable();
    }

    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class, 'tower_id')->nullable();
    }

    public function mpfmTest(): BelongsTo
    {
        return $this->belongsTo(MPFMTest::class, 'mpfm_test_id')->nullable();
    }

    public function stateHistories(): HasMany
    {
        return $this->hasMany(TaskStateHistory::class, 'plan_task_id')->orderBy('created_at', 'desc');
    }

    public function slaHistories(): HasMany
    {
        return $this->hasMany(TaskSLAHistory::class)->orderBy('created_at', 'desc');
    }

    public function surveillanceSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveillance_supervisor_id')->nullable();
    }

    /**
     * STATE MACHINE METHODS
     */

    public function isInState(TaskState $state): bool
    {
        return $this->state === $state;
    }

    public function canTransitionTo(TaskState $newState): bool
    {
        $allowedTransitions = [
            TaskState::DRAFT => [TaskState::SUBMITTED_TO_SURVEILLANCE],
            TaskState::SUBMITTED_TO_SURVEILLANCE => [
                TaskState::ASSIGNED_TO_DEPARTMENT,
                TaskState::DRAFT,
            ],
            TaskState::ASSIGNED_TO_DEPARTMENT => [TaskState::IN_PROGRESS],
            TaskState::IN_PROGRESS => [TaskState::DEPARTMENT_REVIEWED],
            TaskState::DEPARTMENT_REVIEWED => [
                TaskState::SURVEILLANCE_REVIEWED,
                TaskState::IN_PROGRESS,
            ],
            TaskState::SURVEILLANCE_REVIEWED => [
                TaskState::JOB_OFFICER_APPROVED,
                TaskState::DEPARTMENT_REVIEWED,
            ],
            TaskState::JOB_OFFICER_APPROVED => [
                TaskState::COMPLETED,
                TaskState::SURVEILLANCE_REVIEWED,
            ],
            TaskState::COMPLETED => [],
        ];

        return in_array(
            $newState,
            $allowedTransitions[$this->state->value] ?? []
        );
    }

    public function getTypeSpecificData(): array
    {
        return $this->type_specific_data ?? [];
    }

    public function setTypeSpecificData(array $data): void
    {
        $this->type_specific_data = array_merge(
            $this->getTypeSpecificData(),
            $data
        );
    }

    /**
     * SCOPES
     */

    public function scopeByState($query, TaskState $state)
    {
        return $query->where('state', $state->value);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('task_type', $type);
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_completion_at', '<', now())
            ->whereNotIn('state', [TaskState::COMPLETED->value]);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopePending($query)
    {
        return $query->whereIn('state', [
            TaskState::DRAFT->value,
            TaskState::SUBMITTED_TO_SURVEILLANCE->value,
            TaskState::ASSIGNED_TO_DEPARTMENT->value,
            TaskState::IN_PROGRESS->value,
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('state', TaskState::COMPLETED->value);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->whereHas('operators', fn($q) => $q->where('user_id', $userId));
    }

    /**
     * HELPER METHODS
     */

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to_user_id) || $this->operators()->exists();
    }

    public function isCompleted(): bool
    {
        return $this->state === TaskState::COMPLETED;
    }

    public function isOverdue(): bool
    {
        return $this->expected_completion_at &&
               $this->expected_completion_at < now() &&
               !$this->isCompleted();
    }

    public function isAssignedTo($userId): bool
    {
        return $this->operators()->where('user_id', $userId)->exists();
    }

    public function getDaysUntilDue(): ?int
    {
        if (!$this->expected_completion_at) {
            return null;
        }

        return now()->diffInDays($this->expected_completion_at, false);
    }

    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return $this->expected_completion_at->diffInDays(now());
    }

    public function getProgressPercentage(): int
    {
        $stateToProgress = [
            TaskState::DRAFT->value => 10,
            TaskState::SUBMITTED_TO_SURVEILLANCE->value => 20,
            TaskState::ASSIGNED_TO_DEPARTMENT->value => 40,
            TaskState::IN_PROGRESS->value => 60,
            TaskState::DEPARTMENT_REVIEWED->value => 75,
            TaskState::SURVEILLANCE_REVIEWED->value => 85,
            TaskState::JOB_OFFICER_APPROVED->value => 95,
            TaskState::COMPLETED->value => 100,
        ];

        return $stateToProgress[$this->state->value] ?? 0;
    }

    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'dark',
            default => 'secondary',
        };
    }

    public function getAvailableTransitions(): array
    {
        $allowedTransitions = [
            TaskState::DRAFT->value => [TaskState::SUBMITTED_TO_SURVEILLANCE],
            TaskState::SUBMITTED_TO_SURVEILLANCE->value => [
                TaskState::ASSIGNED_TO_DEPARTMENT,
                TaskState::DRAFT,
            ],
            TaskState::ASSIGNED_TO_DEPARTMENT->value => [TaskState::IN_PROGRESS],
            TaskState::IN_PROGRESS->value => [TaskState::DEPARTMENT_REVIEWED],
            TaskState::DEPARTMENT_REVIEWED->value => [
                TaskState::SURVEILLANCE_REVIEWED,
                TaskState::IN_PROGRESS,
            ],
            TaskState::SURVEILLANCE_REVIEWED->value => [
                TaskState::JOB_OFFICER_APPROVED,
                TaskState::DEPARTMENT_REVIEWED,
            ],
            TaskState::JOB_OFFICER_APPROVED->value => [
                TaskState::COMPLETED,
                TaskState::SURVEILLANCE_REVIEWED,
            ],
            TaskState::COMPLETED->value => [],
        ];

        return $allowedTransitions[$this->state->value] ?? [];
    }

    public function stateHistory()
    {
        return $this->stateHistories();
    }

    public function slAHistory()
    {
        return $this->slaHistories();
    }
}
