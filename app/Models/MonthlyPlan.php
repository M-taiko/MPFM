<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
