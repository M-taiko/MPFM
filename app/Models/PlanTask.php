<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskState;

class PlanTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_plan_id', 'sno', 'well_number', 'string_number', 'well_full_name',
        'tower', 'tower_id', 'oil_string_id', 'flow_route', 'reservoir_layer',
        'test_location', 'electrification_status', 'mpfm_type', 'dhsv_type',
        'sub_category', 'category', 'required_whfp', 'tested_whfp', 'production_rate',
        'scheduled_date', 'assigned_operator_id', 'assigned_at', 'assigned_by',
        'completion_status', 'completion_result', 'completed_at', 'final_status',
        'priority', 'is_recurring', 'parent_task_id', 'mpfm_test_id',
        'supervisor_remarks', 'hq_notes', 'operator_notes',
        // State machine fields
        'state', 'surveillance_supervisor_id', 'surveillance_assigned_at',
        // SLA monitoring fields
        'expected_completion_at', 'overdue_flag', 'sla_notified_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'surveillance_assigned_at' => 'datetime',
        'is_recurring' => 'boolean',
        'state' => TaskState::class,
        // SLA monitoring casts
        'expected_completion_at' => 'datetime',
        'overdue_flag' => 'boolean',
        'sla_notified_at' => 'datetime',
    ];

    public function monthlyPlan() { return $this->belongsTo(MonthlyPlan::class, 'monthly_plan_id'); }

    // Many-to-many: Task can have multiple operators
    public function operators() {
        return $this->belongsToMany(User::class, 'plan_task_operators')
            ->withPivot('assigned_at', 'assigned_by', 'is_primary')
            ->withTimestamps();
    }

    // Get only the primary operator from the pivot table
    public function primaryOperator() {
        return $this->belongsToMany(User::class, 'plan_task_operators')
            ->wherePivot('is_primary', true)
            ->withPivot('assigned_at', 'assigned_by', 'is_primary')
            ->withTimestamps();
    }

    // Legacy single operator relationship (backward compatibility)
    public function operator() { return $this->belongsTo(User::class, 'assigned_operator_id'); }
    public function assigner() { return $this->belongsTo(User::class, 'assigned_by'); }
    public function towerModel() { return $this->belongsTo(Tower::class, 'tower_id'); }
    public function oilString() { return $this->belongsTo(OilString::class, 'oil_string_id'); }
    public function parentTask() { return $this->belongsTo(PlanTask::class, 'parent_task_id'); }
    public function childTasks() { return $this->hasMany(PlanTask::class, 'parent_task_id'); }
    public function mpfmTest() { return $this->belongsTo(WellTest::class, 'mpfm_test_id'); }

    // State machine relationships
    public function surveillanceSupervisor() { return $this->belongsTo(User::class, 'surveillance_supervisor_id'); }
    public function stateHistories() { return $this->hasMany(TaskStateHistory::class)->orderBy('created_at'); }

    // SLA monitoring relationship
    public function slaHistory() { return $this->hasOne(TaskSLAHistory::class, 'plan_task_id'); }

    public function scopeToday($query) { return $query->whereDate('scheduled_date', today()); }
    public function scopeThisWeek($query) { return $query->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()]); }
    public function scopePending($query) { return $query->where('completion_status', 'pending'); }
    public function scopeCompleted($query) { return $query->where('completion_status', 'completed'); }
    public function scopeHighPriority($query) { return $query->whereIn('priority', ['high', 'top']); }
    public function scopeAssignedTo($query, $operatorId) { return $query->where('assigned_operator_id', $operatorId); }

    public function isOverdue() { return $this->scheduled_date < today() && $this->completion_status !== 'completed'; }
    public function isAssigned() { return !is_null($this->assigned_operator_id) || $this->operators()->exists(); }
    public function isCompleted() { return $this->completion_status === 'completed'; }

    // Helper method to check if a user is assigned to this task
    public function isAssignedTo($userId) {
        return $this->operators()->where('user_id', $userId)->exists();
    }

    // Sync primary operator to main assigned_operator_id field
    public function syncPrimaryOperator() {
        $primary = $this->operators()->wherePivot('is_primary', true)->first();
        if ($primary) {
            $this->update(['assigned_operator_id' => $primary->id]);
        }
    }

    //================================================================================
    // SLA Monitoring Methods
    //================================================================================

    /**
     * Check if task is currently overdue
     */
    public function isOverdueTask(): bool
    {
        if ($this->expected_completion_at === null) {
            return false;
        }
        return now()->isAfter($this->expected_completion_at);
    }

    /**
     * Get hours until SLA expiry (negative if overdue)
     */
    public function getHoursUntilSLAExpiry(): ?float
    {
        if ($this->expected_completion_at === null) {
            return null;
        }
        return now()->diffInMinutes($this->expected_completion_at) / 60;
    }

    /**
     * Set expected completion time
     */
    public function setSLAExpectation(?\Carbon\Carbon $datetime = null): void
    {
        $this->update([
            'expected_completion_at' => $datetime,
            'overdue_flag' => false,
            'sla_notified_at' => null,
        ]);
    }

    /**
     * Mark task as SLA notified
     */
    public function markSLANotified(): void
    {
        $this->update([
            'sla_notified_at' => now(),
        ]);
    }

    /**
     * Clear SLA notification timestamp
     */
    public function clearSLANotification(): void
    {
        $this->update([
            'sla_notified_at' => null,
        ]);
    }

    /**
     * Get formatted overdue duration
     */
    public function getOverdueDurationFormatted(): ?string
    {
        if (!$this->overdue_flag || $this->expected_completion_at === null) {
            return null;
        }

        $hours = $this->expected_completion_at->diffInHours(now());
        if ($hours < 1) {
            $minutes = $this->expected_completion_at->diffInMinutes(now());
            return "{$minutes} minutes";
        }

        $days = intval($hours / 24);
        $remainingHours = $hours % 24;

        if ($days > 0) {
            return "{$days}d {$remainingHours}h";
        }

        return "{$hours}h";
    }

    //================================================================================
    // State Machine Methods
    //================================================================================

    /**
     * Get current state of the task
     */
    public function getCurrentState(): TaskState
    {
        return $this->state ?? TaskState::DRAFT;
    }

    /**
     * Check if task can transition to target state
     */
    public function canTransitionTo(TaskState $newState): bool
    {
        return in_array($newState, $this->getCurrentState()->allowedTransitions(), true);
    }

    /**
     * Check if task is in specific state
     */
    public function isInState(TaskState $state): bool
    {
        return $this->getCurrentState() === $state;
    }

    /**
     * Get human-readable state label
     */
    public function getStateLabel(): string
    {
        return $this->getCurrentState()->label();
    }

    /**
     * Get state color for UI
     */
    public function getStateColor(): string
    {
        return $this->getCurrentState()->color();
    }

    /**
     * Check if task can be edited by Job Officer
     */
    public function isEditableByJobOfficer(): bool
    {
        return $this->getCurrentState()->isEditableByJobOfficer();
    }

    /**
     * Check if task can be edited by Surveillance Supervisor
     */
    public function isEditableBySurveillance(): bool
    {
        return $this->isInState(TaskState::SUBMITTED_TO_SURVEILLANCE);
    }

    /**
     * Check if operator can start this task
     */
    public function canOperatorStart(): bool
    {
        return $this->getCurrentState()->allowsOperatorStart()
            && $this->completion_status === 'pending';
    }

    /**
     * Check if task is in a completed workflow state
     */
    public function isWorkflowCompleted(): bool
    {
        return $this->getCurrentState()->isCompleted();
    }

    /**
     * Get the latest state history entry
     */
    public function getLatestStateHistory()
    {
        return $this->stateHistories()->latest('created_at')->first();
    }

    /**
     * Scope: Filter tasks by state
     */
    public function scopeInState($query, TaskState $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope: Filter tasks by multiple states
     */
    public function scopeInStates($query, array $states)
    {
        return $query->whereIn('state', $states);
    }
}
