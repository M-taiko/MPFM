<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskState;

class MonthlyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_name',
        'plan_month',
        'total_tests',
        'monthly_target_kpi',
        'total_approved',
        'total_rejected',
        'total_pending',
        'total_completed',
        'total_issues',
        'kpi_progress',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'status',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user who created this plan (Job Officer)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the supervisor who reviewed this plan
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get all tasks in this plan
     */
    public function tasks()
    {
        return $this->hasMany(PlanTask::class, 'monthly_plan_id');
    }

    /**
     * Get pending tasks
     */
    public function pendingTasks()
    {
        return $this->hasMany(PlanTask::class, 'monthly_plan_id')
            ->where('completion_status', 'pending');
    }

    /**
     * Get completed tasks
     */
    public function completedTasks()
    {
        return $this->hasMany(PlanTask::class, 'monthly_plan_id')
            ->where('completion_status', 'completed');
    }

    /**
     * Get tasks assigned to a specific operator
     */
    public function tasksForOperator($operatorId)
    {
        return $this->hasMany(PlanTask::class, 'monthly_plan_id')
            ->where('assigned_operator_id', $operatorId);
    }

    /**
     * Calculate and update statistics
     */
    public function updateStatistics()
    {
        $this->total_tests = $this->tasks()->count();
        $this->total_completed = $this->tasks()->where('completion_result', 'YES')->count();
        $this->total_issues = $this->tasks()->where('completion_result', 'NO')->count();
        $this->total_pending = $this->tasks()->where('completion_status', 'pending')->count();
        $this->total_approved = $this->tasks()->where('final_status', 'approved')->count();
        $this->total_rejected = $this->tasks()->where('final_status', 'rejected')->count();
        $this->kpi_progress = $this->total_completed - $this->monthly_target_kpi;
        $this->save();
    }

    /**
     * Check if plan is editable
     */
    public function isEditable()
    {
        return in_array($this->status, ['draft', 'under_review']);
    }

    /**
     * Check if plan is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    //================================================================================
    // Workflow State Management
    //================================================================================

    /**
     * Get workflow progress breakdown by state
     *
     * @return array
     */
    public function getWorkflowProgress(): array
    {
        return [
            'draft' => $this->tasks()->where('state', TaskState::DRAFT)->count(),
            'submitted_to_surveillance' => $this->tasks()->where('state', TaskState::SUBMITTED_TO_SURVEILLANCE)->count(),
            'assigned_to_mpfm' => $this->tasks()->where('state', TaskState::ASSIGNED_TO_MPFM)->count(),
            'in_progress' => $this->tasks()->where('state', TaskState::IN_PROGRESS)->count(),
            'mpfm_reviewed' => $this->tasks()->where('state', TaskState::MPFM_REVIEWED)->count(),
            'surveillance_reviewed' => $this->tasks()->where('state', TaskState::SURVEILLANCE_REVIEWED)->count(),
            'job_officer_approved' => $this->tasks()->where('state', TaskState::JOB_OFFICER_APPROVED)->count(),
            'completed' => $this->tasks()->where('state', TaskState::COMPLETED)->count(),
        ];
    }

    /**
     * Get workflow completion percentage
     *
     * @return float
     */
    public function getWorkflowCompletionPercentage(): float
    {
        $total = $this->total_tests;
        if ($total === 0) {
            return 0.0;
        }

        $completed = $this->tasks()->where('state', TaskState::COMPLETED)->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get workflow bottlenecks (states with most tasks)
     *
     * @return array
     */
    public function getWorkflowBottlenecks(): array
    {
        $progress = $this->getWorkflowProgress();

        // Exclude draft and completed, focus on in-process states
        $inProcessStates = [
            'submitted_to_surveillance',
            'assigned_to_mpfm',
            'in_progress',
            'mpfm_reviewed',
            'surveillance_reviewed',
            'job_officer_approved',
        ];

        $bottlenecks = [];
        foreach ($inProcessStates as $state) {
            if ($progress[$state] > 0) {
                $bottlenecks[$state] = $progress[$state];
            }
        }

        // Sort by count descending
        arsort($bottlenecks);

        return $bottlenecks;
    }

    /**
     * Check if all tasks have been completed
     *
     * @return bool
     */
    public function areAllTasksCompleted(): bool
    {
        $total = $this->tasks()->count();
        $completed = $this->tasks()->where('state', TaskState::COMPLETED)->count();

        return $total > 0 && $total === $completed;
    }

    /**
     * Get tasks in specific workflow state
     *
     * @param TaskState $state
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasksInState(TaskState $state)
    {
        return $this->tasks()->where('state', $state);
    }
}
