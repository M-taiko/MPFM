<?php

namespace App\Domains\Task\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskSLAHistory extends Model
{
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
    ];

    /**
     * RELATIONSHIPS
     */

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to_user_id')->nullable();
    }

    /**
     * SCOPES
     */

    public function scopeForTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('expected_completion_at')
            ->where('expected_completion_at', '<', now())
            ->whereNull('resolved_at');
    }

    /**
     * HELPER METHODS
     */

    public function getDurationInOverdue(): int
    {
        if (!$this->expected_completion_at || $this->expected_completion_at >= now()) {
            return 0;
        }

        $endDate = $this->resolved_at ?? now();

        return $this->expected_completion_at->diffInMinutes($endDate);
    }

    public function getHoursOverdue(): int
    {
        return intval($this->getDurationInOverdue() / 60);
    }

    public function isResolved(): bool
    {
        return !is_null($this->resolved_at);
    }

    public function getCurrentOverdueDuration(): int
    {
        if ($this->isResolved()) {
            return $this->getDurationInOverdue();
        }

        if (!$this->expected_completion_at || $this->expected_completion_at >= now()) {
            return 0;
        }

        return $this->expected_completion_at->diffInMinutes(now());
    }

    public function markResolved(): void
    {
        $this->update(['resolved_at' => now()]);
    }

    public function escalateTo(int $level, User $user): void
    {
        $this->update([
            'escalation_level' => $level,
            'escalated_at' => now(),
            'escalated_to_user_id' => $user->id,
        ]);
    }
}
