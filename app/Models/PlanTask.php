<?php

namespace App\Models;

use App\Enums\TaskState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * PlanTask Model
 *
 * This model represents legacy plan tasks from the MPFM system.
 * It uses the plan_tasks table which has a different schema from the refactored Task model.
 *
 * @see App\Domains\Task\Models\Task (the new refactored Task model)
 */
class PlanTask extends Model
{
    use HasFactory;

    protected $table = 'plan_tasks';

    protected $fillable = [
        'monthly_plan_id',
        'department_id',
        'sno',
        'well_number',
        'string_number',
        'well_full_name',
        'tower',
        'tower_id',
        'oil_string_id',
        'flow_route',
        'reservoir_layer',
        'test_location',
        'electrification_status',
        'mpfm_type',
        'dhsv_type',
        'sub_category',
        'category',
        'required_whfp',
        'tested_whfp',
        'production_rate',
        'scheduled_date',
        'assigned_operator_id',
        'assigned_at',
        'assigned_by',
        'surveillance_supervisor_id',
        'surveillance_assigned_at',
        'assigned_by_surveillance_at',
        'expected_completion_at',
        'overdue_flag',
        'sla_notified_at',
        'completion_status',
        'state',
        'completion_result',
        'completed_at',
        'final_status',
        'priority',
        'is_recurring',
        'parent_task_id',
        'mpfm_test_id',
        'supervisor_remarks',
        'hq_notes',
        'operator_notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'assigned_at' => 'datetime',
        'surveillance_assigned_at' => 'datetime',
        'assigned_by_surveillance_at' => 'datetime',
        'expected_completion_at' => 'datetime',
        'sla_notified_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'overdue_flag' => 'boolean',
        'state' => \App\Enums\TaskState::class,
    ];

    protected $appends = [
        'task_identifier',
        'status',
        'status_badge_class',
        'priority_badge_class',
    ];

    /**
     * Get the monthly plan this task belongs to
     */
    public function monthlyPlan(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlan::class, 'monthly_plan_id');
    }

    /**
     * Get the department this task is assigned to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the assigned operator
     */
    public function assignedOperator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_operator_id');
    }

    /**
     * Alias for assignedOperator for backward compatibility
     */
    public function operator(): BelongsTo
    {
        return $this->assignedOperator();
    }

    /**
     * Get the operator who assigned this task
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the surveillance supervisor
     */
    public function surveillanceSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveillance_supervisor_id');
    }

    /**
     * Get the tower for this task
     */
    public function towerModel(): BelongsTo
    {
        return $this->belongsTo(Tower::class, 'tower_id');
    }

    /**
     * Get the oil string for this task
     */
    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class, 'oil_string_id');
    }

    /**
     * Get the MPFM test associated with this task
     */
    public function mpfmTest(): BelongsTo
    {
        return $this->belongsTo(MPFMTest::class, 'mpfm_test_id');
    }

    /**
     * Get operators assigned to this task (many-to-many)
     */
    public function operators(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'task_operators',
            'task_id',
            'user_id'
        )->withPivot('assigned_at', 'is_primary')
         ->withTimestamps();
    }

    /**
     * Get state history records
     */
    public function stateHistories(): HasMany
    {
        return $this->hasMany(TaskStateHistory::class, 'plan_task_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get SLA history records
     */
    public function slaHistories(): HasMany
    {
        return $this->hasMany(TaskSLAHistory::class, 'plan_task_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Check if a specific user is assigned to this task
     */
    public function isAssignedTo($userId): bool
    {
        return $this->operators()->where('user_id', $userId)->exists();
    }

    /**
     * Get the primary operator relationship for this task
     */
    public function primaryOperator(): BelongsToMany
    {
        return $this->operators()->wherePivot('is_primary', true);
    }

    /**
     * Get the first primary operator (helper method)
     */
    public function getPrimaryOperator()
    {
        return $this->primaryOperator()->first();
    }

    /**
     * Get task identifier (well number or SNO)
     */
    public function getTaskIdentifierAttribute(): string
    {
        return $this->well_number ?? ('Task #' . $this->sno);
    }

    /**
     * Get MPFM device display (uses mpfm_type or category)
     */
    public function getMpfmDeviceAttribute(): ?string
    {
        return $this->attributes['mpfm_type'] ?? $this->attributes['category'] ?? null;
    }

    /**
     * Get status attribute (alias for completion_status)
     */
    public function getStatusAttribute(): string
    {
        return $this->completion_status ?? 'pending';
    }

    /**
     * Get status badge class for display
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->completion_status) {
            'completed' => 'success',
            'in_progress' => 'info',
            'failed' => 'danger',
            'pending' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get priority badge class for display
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'normal' => 'secondary',
            'high' => 'warning',
            'top' => 'danger',
            'low' => 'light',
            default => 'secondary',
        };
    }

    /**
     * Get the priority color for badge display
     */
    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            'normal' => 'secondary',
            'high' => 'warning',
            'top' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get hours until SLA expiry
     * Returns null if no SLA deadline is set
     */
    public function getHoursUntilSLAExpiry(): ?float
    {
        if ($this->expected_completion_at === null) {
            return null;
        }

        $hoursRemaining = now()->diffInMinutes($this->expected_completion_at) / 60;
        return $hoursRemaining;
    }

    /**
     * Get formatted overdue duration string
     */
    public function getOverdueDurationFormatted(): string
    {
        if ($this->expected_completion_at === null) {
            return 'No SLA set';
        }

        $minutesOverdue = $this->expected_completion_at->diffInMinutes(now());
        if ($minutesOverdue <= 0) {
            return 'On track';
        }

        $hoursOverdue = intdiv($minutesOverdue, 60);
        $daysOverdue = intdiv($hoursOverdue, 24);

        if ($daysOverdue > 0) {
            return $daysOverdue . 'd ' . ($hoursOverdue % 24) . 'h overdue';
        } elseif ($hoursOverdue > 0) {
            return $hoursOverdue . 'h overdue';
        } else {
            return $minutesOverdue . 'm overdue';
        }
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->expected_completion_at === null) {
            return false;
        }

        return now()->greaterThan($this->expected_completion_at);
    }

    /**
     * Scope: Get tasks for a specific department
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope: Get tasks by completion status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('completion_status', $status);
    }

    /**
     * Scope: Get tasks by state
     */
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope: Get today's tasks
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    /**
     * Scope: Get this week's tasks
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_date', [
            today()->startOfWeek(),
            today()->endOfWeek()
        ]);
    }

    /**
     * Scope: Get pending tasks
     */
    public function scopePending($query)
    {
        return $query->where('completion_status', 'pending');
    }

    /**
     * Scope: Get completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('completion_status', 'completed');
    }
}
