<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
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
}
