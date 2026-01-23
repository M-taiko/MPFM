# SLA Monitoring - Quick Reference Guide

## Files Added

| File | Purpose |
|------|---------|
| `config/sla.php` | Configuration for SLA durations, escalation, notifications |
| `database/migrations/2026_01_24_200000_*` | Add SLA fields to plan_tasks table |
| `database/migrations/2026_01_24_200001_*` | Create task_sla_histories table |
| `app/Models/TaskSLAHistory.php` | Model for SLA audit trail |
| `app/Services/TaskSLAService.php` | Core SLA business logic |
| `app/Events/SLATriggered.php` | Event fired when SLA is triggered |
| `app/Listeners/NotifySLAOverdueListener.php` | Handles SLA notifications |
| `app/Console/Commands/CheckTaskSLAsCommand.php` | Scheduled command (runs every 15 min) |
| `app/Console/Kernel.php` | Registers scheduled command |

## Files Modified

| File | Changes |
|------|---------|
| `app/Models/PlanTask.php` | Added SLA fields, casts, relationship, 6 helper methods |
| `app/Providers/AppServiceProvider.php` | Registered TaskSLAService and SLATriggered listener |

## Database Changes

### New Columns in plan_tasks
- `expected_completion_at` (datetime nullable) - SLA deadline
- `overdue_flag` (boolean) - Quick flag for overdue tasks
- `sla_notified_at` (datetime nullable) - Last notification time

### New Table: task_sla_histories
Complete audit trail of SLA events with 13 columns tracking detection, notification, escalation.

## Deployment Steps

```bash
# 1. Run migrations
php artisan migrate

# 2. Test command
php artisan sla:check

# 3. Ensure cron is running
# Add to crontab: * * * * * cd /path/to/mpfm && php artisan schedule:run

# 4. Verify in logs
tail -f storage/logs/laravel.log
```

## Configuration

Edit `config/sla.php` to customize:
- **State durations**: How long tasks can stay in each state (hours)
- **Escalation levels**: When to escalate (after 4h, 24h)
- **Notification channels**: database, mail, etc.
- **Priority multipliers**: Different SLAs for critical vs low priority

## Common Commands

```php
// Check if task is overdue
$task->isOverdueTask()

// Get hours until SLA expiry
$task->getHoursUntilSLAExpiry()

// Get formatted overdue duration
$task->getOverdueDurationFormatted() // "2d 3h"

// View SLA history
$task->slaHistory // TaskSLAHistory model

// Set custom SLA
$slaService = app(TaskSLAService::class);
$slaService->setExpectedCompletion($task, now()->addDays(3));

// Get SLA summary
$summary = $slaService->getSLASummary();
echo $summary['compliance_percentage']; // 85.5
```

## Key Classes & Methods

**TaskSLAService** (Core Logic)
- `isTaskOverdue()` - Is task past deadline?
- `checkAndProcessOverdueTasks()` - Find & process overdue (run by scheduled command)
- `sendSLANotification()` - Send notification to responsible actor
- `recordSLAHistory()` - Audit trail
- `escalateTask()` - Update escalation level
- `getSLASummary()` - Compliance statistics

**TaskSLAHistory** (Audit Trail)
- `getDurationInOverdue()` - How long was it overdue?
- `isResolved()` - Was the issue fixed?
- `getEscalationLabel()` - Human-readable escalation level
- `markResolved()` - Mark as fixed
- `recordNotification()` - Track notification sent

**PlanTask** (Model Helpers)
- `isOverdueTask()` - Is currently overdue?
- `getHoursUntilSLAExpiry()` - Hours remaining (negative = overdue)
- `setSLAExpectation()` - Set deadline
- `markSLANotified()` / `clearSLANotification()` - Track notification
- `getOverdueDurationFormatted()` - Human-readable ("2d 3h")

## How It Works

1. **Task State Changes** → Calculate SLA based on state, set `expected_completion_at`
2. **Every 15 Minutes** → `CheckTaskSLAsCommand` runs via scheduler
3. **Check for Overdue** → Find tasks where `expected_completion_at < now()`
4. **Mark Overdue** → Set `overdue_flag = true`, record in `task_sla_histories`
5. **Notify Actor** → Get responsible actor for task state, send notification
6. **Fire Event** → `SLATriggered` event → `NotifySLAOverdueListener` processes
7. **Escalate** → After 4h → escalation level 2, after 24h → level 3
8. **Audit Trail** → All tracked in `task_sla_histories`

## Responsible Actors by State

| State | Notified |
|-------|----------|
| DRAFT | Job Officer |
| SUBMITTED_TO_SURVEILLANCE | Surveillance Supervisor |
| ASSIGNED_TO_MPFM | MPFM Supervisor |
| IN_PROGRESS | MPFM Operator |
| MPFM_REVIEWED | Surveillance Supervisor |
| SURVEILLANCE_REVIEWED | Job Officer |
| JOB_OFFICER_APPROVED | Job Officer |

## Default SLA Durations

| State | Duration |
|-------|----------|
| Draft | 24h (1 day) |
| Submitted to Surveillance | 48h (2 days) |
| Assigned to MPFM | 72h (3 days) |
| In Progress | 120h (5 days) |
| MPFM Reviewed | 48h (2 days) |
| Surveillance Reviewed | 48h (2 days) |
| Job Officer Approved | 24h (1 day) |

## Environment Variables

Add to `.env`:
```env
SLA_MONITORING_ENABLED=true
```

## Testing

```bash
# Run command manually
php artisan sla:check

# View with verbose output
php artisan sla:check -v

# Force recheck (skip throttling)
php artisan sla:check --force

# Check a specific task
$task = PlanTask::find(1);
$slaService = app(TaskSLAService::class);
$slaService->isTaskOverdue($task)
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Scheduled command not running | Check cron: `crontab -l` |
| Notifications not sending | Verify `SLA_MONITORING_ENABLED=true` and mail config |
| Tasks not marked overdue | Check `expected_completion_at` is set |
| Too many notifications | Increase `on_overdue_minutes` in config |
| Concurrent execution errors | Already handled with `lockForUpdate()` and `withoutOverlapping()` |

## Integration with State Machine

When task transitions to new state via `TaskStateService`:
1. State changes
2. SLA deadline automatically calculated and set
3. `expected_completion_at` updated
4. On next scheduled check, SLA monitoring begins

No manual setup needed - fully automatic integration!

## Next Steps (Optional)

- Create SLA dashboard showing compliance statistics
- Send weekly SLA reports to management
- Implement automatic escalation actions
- Add Slack/SMS notifications
- Create historical analytics

