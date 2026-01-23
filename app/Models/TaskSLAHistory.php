<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use DateInterval;

class TaskSLAHistory extends Model
{
    use HasFactory;

    protected $table = 'task_sla_histories';

    protected $fillable = [
        'plan_task_id',
        'sla_duration_hours',
        'expected_completion_at',
        'overdue_detected_at',
        'notification_sent_at',
        'notified_to_role',
        'escalation_level',
        'resolution_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'expected_completion_at' => 'datetime',
        'overdue_detected_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'resolution_at' => 'datetime',
        'metadata' => 'json',
        'escalation_level' => 'integer',
        'sla_duration_hours' => 'integer',
    ];

    /**
     * Relationship to the task
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(PlanTask::class, 'plan_task_id');
    }

    /**
     * Get the duration the task was overdue (if it was resolved)
     *
     * @return DateInterval|null
     */
    public function getDurationInOverdue(): ?DateInterval
    {
        if ($this->overdue_detected_at === null) {
            return null;
        }

        $resolvedAt = $this->resolution_at ?? now();

        return $this->overdue_detected_at->diff($resolvedAt);
    }

    /**
     * Get the duration overdue in hours (if it was resolved)
     *
     * @return float|null
     */
    public function getHoursOverdue(): ?float
    {
        if ($this->overdue_detected_at === null) {
            return null;
        }

        $resolvedAt = $this->resolution_at ?? now();

        return $this->overdue_detected_at->diffInMinutes($resolvedAt) / 60;
    }

    /**
     * Check if the overdue issue has been resolved
     *
     * @return bool
     */
    public function isResolved(): bool
    {
        return $this->resolution_at !== null;
    }

    /**
     * Check if SLA notification was sent
     *
     * @return bool
     */
    public function wasNotified(): bool
    {
        return $this->notification_sent_at !== null;
    }

    /**
     * Get escalation level label
     *
     * @return string
     */
    public function getEscalationLabel(): string
    {
        return match($this->escalation_level) {
            0 => 'No Escalation',
            1 => 'Level 1 - Initial Notification',
            2 => 'Level 2 - Supervisor Escalation',
            3 => 'Level 3 - Management Escalation',
            default => 'Unknown Level',
        };
    }

    /**
     * Get current overdue duration in hours (for active overdue tasks)
     *
     * @return float|null
     */
    public function getCurrentOverdueDuration(): ?float
    {
        if ($this->overdue_detected_at === null || $this->isResolved()) {
            return null;
        }

        return $this->overdue_detected_at->diffInMinutes(now()) / 60;
    }

    /**
     * Mark as resolved (task brought back on track)
     *
     * @return void
     */
    public function markResolved(): void
    {
        $this->update([
            'resolution_at' => now(),
        ]);
    }

    /**
     * Update escalation level
     *
     * @param int $level
     * @return void
     */
    public function escalateTo(int $level): void
    {
        $this->update([
            'escalation_level' => $level,
        ]);
    }

    /**
     * Record that notification was sent
     *
     * @param string $role
     * @return void
     */
    public function recordNotification(string $role): void
    {
        $this->update([
            'notification_sent_at' => now(),
            'notified_to_role' => $role,
        ]);
    }

    /**
     * Scope: Find unresolved SLA events
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolution_at');
    }

    /**
     * Scope: Find notified SLA events
     */
    public function scopeNotified($query)
    {
        return $query->whereNotNull('notification_sent_at');
    }

    /**
     * Scope: Find by escalation level
     */
    public function scopeByEscalationLevel($query, int $level)
    {
        return $query->where('escalation_level', '>=', $level);
    }

    /**
     * Scope: Find recently overdue tasks (overdue in last N hours)
     */
    public function scopeRecentlyOverdue($query, int $hours = 24)
    {
        return $query->where('overdue_detected_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope: Find by task
     */
    public function scopeForTask($query, PlanTask $task)
    {
        return $query->where('plan_task_id', $task->id);
    }
}
