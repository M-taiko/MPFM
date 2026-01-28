<?php

namespace App\Models;

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
        'expected_completion_at' => 'datetime',
        'sla_notified_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'overdue_flag' => 'boolean',
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
     * Get the primary operator for this task
     */
    public function primaryOperator()
    {
        return $this->operators()->wherePivot('is_primary', true)->first();
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
}
