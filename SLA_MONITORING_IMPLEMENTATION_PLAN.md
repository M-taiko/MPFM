# SLA/Timer Monitoring Implementation Plan

## Overview

Add SLA (Service Level Agreement) monitoring to the MPFM task management system. This feature will track task expected completion times, identify overdue tasks, send notifications to responsible actors, and maintain audit trail integration with the state machine.

## Requirements

1. **New Field**: `expected_completion_at` (optional datetime) on plan_tasks table
2. **Monitoring**: Detect when `current_time > expected_completion_at` and task is not in final state
3. **Actions**: Send notifications to responsible actor (respecting roles)
4. **Configuration**: Configurable default SLA durations, per-task override capability
5. **Implementation**: Laravel Scheduled Command (runs periodically via `schedule:run`)
6. **Concurrency**: Safe for concurrent execution (no race conditions)
7. **Audit Trail**: Update TaskStateHistory when SLA actions trigger
8. **Policies**: Never bypass role-based authorization

## Database Changes

### Migration 1: Add SLA fields to plan_tasks

**File**: `database/migrations/2026_01_24_200000_add_sla_fields_to_plan_tasks.php`

```sql
ALTER TABLE plan_tasks ADD COLUMN (
    expected_completion_at DATETIME NULLABLE AFTER surveillance_assigned_at,
    overdue_flag BOOLEAN DEFAULT FALSE AFTER expected_completion_at,
    sla_notified_at DATETIME NULLABLE AFTER overdue_flag
);

-- Indexes for SLA queries
ALTER TABLE plan_tasks ADD INDEX idx_expected_completion (expected_completion_at);
ALTER TABLE plan_tasks ADD INDEX idx_overdue (overdue_flag, state);
ALTER TABLE plan_tasks ADD INDEX idx_sla_check (state, expected_completion_at);
```

**Purpose:**
- `expected_completion_at`: When task should be completed
- `overdue_flag`: Boolean flag indicating task is overdue (for fast queries)
- `sla_notified_at`: When last SLA notification was sent (prevent duplicate notifications)

### Migration 2: Create task_sla_history table

**File**: `database/migrations/2026_01_24_200001_create_task_sla_histories_table.php`

```sql
CREATE TABLE task_sla_histories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    plan_task_id BIGINT UNSIGNED NOT NULL UNIQUE,
    sla_duration_hours INT,
    expected_completion_at DATETIME,
    overdue_detected_at DATETIME,
    notification_sent_at DATETIME,
    notified_to_role VARCHAR(255),
    escalation_level INT DEFAULT 0,
    resolution_at DATETIME NULLABLE,
    notes TEXT NULLABLE,
    metadata JSON NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (plan_task_id) REFERENCES plan_tasks(id) ON DELETE CASCADE,
    INDEX idx_plan_task (plan_task_id),
    INDEX idx_overdue_detected (overdue_detected_at),
    INDEX idx_notified_to_role (notified_to_role),
    INDEX idx_escalation_level (escalation_level)
);
```

## Core Classes

### 1. TaskSLAService

**File**: `app/Services/TaskSLAService.php`

```php
class TaskSLAService
{
    // Check if task is overdue
    public function isTaskOverdue(PlanTask $task): bool

    // Get hours until SLA expiry (negative if overdue)
    public function getHoursUntilSLAExpiry(PlanTask $task): ?float

    // Set expected completion time
    public function setExpectedCompletion(PlanTask $task, ?Carbon $datetime): void

    // Calculate SLA from current state
    public function calculateSLAFromState(PlanTask $task, TaskState $state): Carbon

    // Check and process all overdue tasks
    public function checkAndProcessOverdueTasks(): Collection

    // Get responsible actor for a task based on current state
    public function getResponsibleActorForState(PlanTask $task, TaskState $state): ?User

    // Send SLA notification
    public function sendSLANotification(PlanTask $task, string $reason): bool

    // Record SLA history
    public function recordSLAHistory(
        PlanTask $task,
        ?Carbon $overdue_at,
        ?string $role,
        ?string $notes
    ): TaskSLAHistory

    // Get SLA for state (from config)
    public function getSLADurationForState(TaskState $state): int // hours

    // Escalate overdue task
    public function escalateTask(PlanTask $task, int $level): void
}
```

### 2. CheckTaskSLAsCommand

**File**: `app/Console/Commands/CheckTaskSLAsCommand.php`

Scheduled command that runs periodically (e.g., every 15 minutes):

```php
class CheckTaskSLAsCommand extends Command
{
    protected $signature = 'sla:check {--force : Force processing even if recently run}';
    protected $description = 'Check for overdue tasks and send notifications';

    public function handle(TaskSLAService $slaService): int
    {
        // Get all overdue tasks (safe for concurrent execution via database lock)
        $overdueTasks = $slaService->checkAndProcessOverdueTasks();

        foreach ($overdueTasks as $task) {
            $slaService->sendSLANotification($task, 'Task overdue - automatic notification');
        }

        return 0;
    }
}
```

### 3. SLATriggered Event

**File**: `app/Events/SLATriggered.php`

```php
class SLATriggered
{
    public PlanTask $task;
    public TaskSLAHistory $slaHistory;
    public User $triggeredBy;
    public string $reason;

    public function __construct(
        PlanTask $task,
        TaskSLAHistory $slaHistory,
        User $triggeredBy,
        string $reason
    )
}
```

### 4. TaskSLAHistory Model

**File**: `app/Models/TaskSLAHistory.php`

```php
class TaskSLAHistory extends Model
{
    protected $table = 'task_sla_histories';

    protected $fillable = [
        'plan_task_id', 'sla_duration_hours', 'expected_completion_at',
        'overdue_detected_at', 'notification_sent_at', 'notified_to_role',
        'escalation_level', 'resolution_at', 'notes', 'metadata'
    ];

    protected $casts = [
        'expected_completion_at' => 'datetime',
        'overdue_detected_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'resolution_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function task() { return $this->belongsTo(PlanTask::class, 'plan_task_id'); }

    public function getDurationInOverdue(): \DateInterval

    public function isResolved(): bool
}
```

### 5. SLA Configuration

**File**: `config/sla.php`

```php
return [
    // Enable/disable SLA monitoring globally
    'enabled' => env('SLA_MONITORING_ENABLED', true),

    // Default SLA durations per state (in hours)
    'state_durations' => [
        'draft' => 24,
        'submitted_to_surveillance' => 48,
        'assigned_to_mpfm' => 72,
        'in_progress' => 120, // 5 days
        'mpfm_reviewed' => 48,
        'surveillance_reviewed' => 48,
        'job_officer_approved' => 24,
    ],

    // Escalation rules
    'escalation' => [
        'level_1' => ['hours' => 1, 'action' => 'notify_supervisor'],
        'level_2' => ['hours' => 4, 'action' => 'notify_manager'],
        'level_3' => ['hours' => 24, 'action' => 'notify_director'],
    ],

    // Notification settings
    'notifications' => [
        'enabled' => true,
        'channels' => ['database', 'mail'], // Add SMS, Slack, etc
        'on_overdue_minutes' => 15, // Minimum interval between notifications
    ],

    // Scheduled command settings
    'check_interval' => '*/15 * * * *', // Every 15 minutes (cron format)
];
```

## PlanTask Model Updates

Add to `app/Models/PlanTask.php`:

```php
class PlanTask extends Model
{
    // Add to fillable
    protected $fillable = [
        // ... existing fields ...
        'expected_completion_at', 'overdue_flag', 'sla_notified_at',
    ];

    // Add to casts
    protected $casts = [
        // ... existing casts ...
        'expected_completion_at' => 'datetime',
        'overdue_flag' => 'boolean',
        'sla_notified_at' => 'datetime',
    ];

    // Add relationship
    public function slaHistory() {
        return $this->hasOne(TaskSLAHistory::class, 'plan_task_id');
    }

    // Add helper methods
    public function isOverdue(): bool
    public function getHoursUntilSLAExpiry(): ?float
    public function setSLAExpectation(?Carbon $datetime): void
    public function markSLANotified(): void
    public function clearSLANotification(): void
}
```

## Integration Points

### 1. TaskStateService Integration

When transitioning task state, automatically set expected completion time:

```php
// In TaskStateService::transition()
$this->setSLAExpectation($task, $newState);
```

### 2. Observer Updates

Update `PlanTaskObserver` to handle SLA-related state changes:

```php
// Clear overdue flag when task moved to final states
if ($task->state->in([TaskState::COMPLETED, TaskState::JOB_OFFICER_APPROVED])) {
    $task->update(['overdue_flag' => false]);
}
```

### 3. TaskStateHistory Integration

Add SLA context to state history records:

```php
// In TaskStateHistory migration/model
ALTER TABLE task_state_histories ADD sla_hours_at_transition INT NULLABLE;
```

### 4. Console Kernel Registration

Register scheduled command in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    if (config('sla.enabled')) {
        $schedule->command('sla:check')
            ->cron(config('sla.check_interval'))
            ->name('Check Task SLAs')
            ->withoutOverlapping(5) // 5-minute timeout for safety
            ->onFailure(function () {
                Log::error('SLA check command failed');
            });
    }
}
```

## Concurrency Safety

### Strategy: Pessimistic Locking

```php
// In TaskSLAService::checkAndProcessOverdueTasks()
$overdueTasks = PlanTask::query()
    ->where('overdue_flag', false)
    ->whereNotNull('expected_completion_at')
    ->where('expected_completion_at', '<', now())
    ->whereNotIn('state', [TaskState::COMPLETED, TaskState::JOB_OFFICER_APPROVED])
    ->lockForUpdate() // Prevent concurrent processing
    ->get();
```

### Prevention Measures

1. **Database Lock**: Use `lockForUpdate()` to prevent race conditions
2. **Timestamp Check**: `sla_notified_at` prevents duplicate notifications
3. **Flag Update**: `overdue_flag` set atomically in transaction
4. **Timeout**: Scheduled command has `withoutOverlapping(5)` timeout

## Notification System

### Notification Class

**File**: `app/Notifications/TaskOverdueNotification.php`

```php
class TaskOverdueNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['database', 'mail']; // Configurable
    }

    public function toDatabase($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->well_full_name,
            'state' => $this->task->state->label(),
            'expected_at' => $this->task->expected_completion_at,
            'overdue_since' => $this->task->expected_completion_at->diffInHours(now()),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Task Overdue: {$this->task->well_full_name}")
            ->line("Task is {$duration} hours overdue")
            ->action('View Task', route('tasks.show', $this->task))
            ->line('Please take action to complete this task.');
    }
}
```

## Audit Trail Integration

### TaskStateHistory Enhancement

When SLA triggers action, record in both:
1. **task_sla_histories**: SLA-specific event
2. **task_state_histories**: Audit trail with SLA metadata

```php
// Example: SLA escalation recorded as system action
$stateHistory = $task->stateHistories()->create([
    'from_state' => $task->state,
    'to_state' => $task->state,
    'changed_by' => null, // System action
    'changed_by_role' => 'system',
    'notes' => "SLA escalation level {$level}",
    'metadata' => [
        'sla_trigger' => true,
        'escalation_level' => $level,
        'reason' => 'Task overdue',
    ],
]);
```

## Implementation Sequence

1. ✅ Create SLA configuration file
2. ⏳ Create database migrations (2 migrations)
3. ⏳ Create TaskSLAHistory model
4. ⏳ Create TaskSLAService
5. ⏳ Create SLATriggered event
6. ⏳ Create SLA notification class
7. ⏳ Update PlanTask model
8. ⏳ Update TaskStateService (optional SLA hook)
9. ⏳ Update PlanTaskObserver (clear overdue flag)
10. ⏳ Create CheckTaskSLAsCommand
11. ⏳ Register in Console Kernel
12. ⏳ Create comprehensive documentation

## Testing Strategy

### Unit Tests
- SLA expiry calculation
- Overdue detection logic
- Notification eligibility

### Integration Tests
- Full SLA workflow (set → detect → notify → resolve)
- Concurrent execution safety
- Event firing and listener execution

### Feature Tests
- Scheduled command execution
- Notification delivery
- Audit trail recording

## Success Criteria

✅ SLA monitoring runs without manual intervention
✅ Overdue tasks detected accurately
✅ Notifications sent to correct roles (respecting authorization)
✅ No duplicate notifications (via sla_notified_at)
✅ Audit trail records all SLA events
✅ Concurrent execution is safe (no race conditions)
✅ SLA durations configurable per state
✅ System gracefully handles missing expected_completion_at
✅ Existing state machine functionality unaffected
✅ Feature toggleable via configuration

## Next Steps

1. Review and approve this plan
2. Create migrations
3. Implement TaskSLAService
4. Create console command
5. Add configuration and integrate with system
6. Write documentation
