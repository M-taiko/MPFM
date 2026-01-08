<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'plan_name',
        'plan_date',
        'oil_field_id',
        'tower_id',
        'status',
        'plan_notes',
        'total_tasks',
        'completed_tasks',
        'pending_tasks',
        'in_progress_tasks',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'plan_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_tasks' => 'integer',
        'completed_tasks' => 'integer',
        'pending_tasks' => 'integer',
        'in_progress_tasks' => 'integer',
    ];

    /**
     * Relationships
     */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function oilField(): BelongsTo
    {
        return $this->belongsTo(OilField::class);
    }

    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PlanTask::class);
    }

    /**
     * Scopes
     */

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('plan_date', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'in_progress']);
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Helper Methods
     */

    public function updateTaskCounts(): void
    {
        $this->total_tasks = $this->tasks()->count();
        $this->completed_tasks = $this->tasks()->where('status', 'completed')->count();
        $this->pending_tasks = $this->tasks()->where('status', 'pending')->count();
        $this->in_progress_tasks = $this->tasks()->where('status', 'in_progress')->count();
        $this->save();
    }

    public function getCompletionPercentageAttribute(): float
    {
        if ($this->total_tasks == 0) {
            return 0;
        }

        return round(($this->completed_tasks / $this->total_tasks) * 100, 2);
    }

    public function isCompleted(): bool
    {
        return $this->total_tasks > 0 && $this->total_tasks == $this->completed_tasks;
    }

    public function canBeDeleted(): bool
    {
        // Can only delete if no tasks have been started
        return $this->in_progress_tasks == 0 && $this->completed_tasks == 0;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'badge-secondary',
            'active' => 'badge-primary',
            'in_progress' => 'badge-info',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get all unique operators assigned to tasks in this plan
     */
    public function getAssignedOperators()
    {
        return User::whereIn('id', $this->tasks()->pluck('assigned_to')->unique())->get();
    }

    /**
     * Check if plan can be activated
     */
    public function canBeActivated(): bool
    {
        return $this->status === 'draft' && $this->tasks()->count() > 0;
    }

    /**
     * Auto-update status based on task completion
     */
    public function autoUpdateStatus(): void
    {
        $this->updateTaskCounts();

        if ($this->isCompleted() && $this->status !== 'completed') {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } elseif ($this->in_progress_tasks > 0 && $this->status === 'active') {
            $this->update([
                'status' => 'in_progress',
                'started_at' => $this->started_at ?? now(),
            ]);
        }
    }
}
