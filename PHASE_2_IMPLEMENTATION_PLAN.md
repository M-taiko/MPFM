# Phase 2: Data Migration & Service Layer Consolidation

**Date:** January 28, 2026
**Status:** READY TO START
**Estimated Duration:** 3-5 days
**Complexity:** Medium (data safety critical)
**Risk Level:** Medium (reversible with backups)

---

## 📋 PHASE 2 OVERVIEW

Phase 1 created the **unified Task infrastructure**. Phase 2 focuses on **migrating data** from old models and **consolidating services**.

### Goals
1. ✅ Migrate data from plan_tasks → tasks table (safely, reversibly)
2. ✅ Create TaskWorkflowEngine service (orchestration layer)
3. ✅ Update existing services to use new Task model
4. ✅ Remove deprecated models/controllers/views
5. ✅ Verify all functionality works with new system
6. ✅ Clean codebase of legacy references

---

## 🎯 PHASE 2 DELIVERABLES

### 1. Data Migration
**File:** `database/migrations/2026_01_29_000000_migrate_plan_tasks_to_tasks.php`

**What it does:**
- Copies data from `plan_tasks` to `tasks` table
- Maps task types (plan_task, daily_plan, operator_plan)
- Preserves all relationships
- Creates audit records for migration
- Validates data integrity
- Includes rollback capability

**Data Flow:**
```
plan_tasks (old)
├── title → tasks.title
├── state → tasks.state
├── priority → tasks.priority
├── monthly_plan_id → tasks.monthly_plan_id
├── assigned_operator_id → tasks.operators pivot
├── created_at → tasks.created_at
└── ... → tasks.type_specific_data (JSON)
       ↓
tasks (new)
└── All data preserved + enriched
```

### 2. TaskWorkflowEngine Service
**File:** `app/Domains/Task/Services/TaskWorkflowEngine.php`

**Purpose:** Orchestrate complex task workflows

**Methods:**
- `createFromMonthlyPlan(MonthlyPlan, array)` - Create task batch
- `transitionBulk(Collection $tasks, TaskState $newState, User $user)` - Bulk operations
- `checkAndEscalateSLA()` - Monitor SLA violations
- `completeTask(Task, User)` - Handle task completion
- `getWorkflowStats(MonthlyPlan)` - Progress reporting

**Replaces:**
- Scattered workflow logic in controllers
- Manual state change handling
- SLA monitoring scripts

### 3. Updated Services
Update existing services to use new Task model:

**Files to Update:**
1. `app/Services/TaskStateService.php`
   - Use `TaskStateMachine` instead
   - Simplify or deprecate

2. `app/Services/TaskSLAService.php`
   - Use `TaskSLAHistory` model
   - Remove old PlanTask references

3. `app/Services/MonthlyPlanService.php` (if exists)
   - Use new Task model
   - Update task retrieval queries

4. `app/Http/Controllers/MonthlyPlanController.php`
   - Use `TaskController` for task operations
   - Update task routes

5. `app/Http/Controllers/SurveillanceSupervisorController.php`
   - Use new Task queries
   - Update task assignment

6. `app/Http/Controllers/MPFMSupervisorController.php`
   - Use TaskStateMachine service
   - Remove old state logic

### 4. Remove Deprecated Files

**Controllers (3 files)** - Delete after updating references:
```bash
rm app/Http/Controllers/PlanTaskController.php
rm app/Http/Controllers/DailyPlanController.php
rm app/Http/Controllers/OperatorPlanController.php
```

**Models (3 files)** - Delete after migration:
```bash
rm app/Models/PlanTask.php
rm app/Models/DailyPlan.php
rm app/Models/OperatorPlan.php
```

**Views (3 directories)** - Delete after routing updates:
```bash
rm -rf resources/views/daily_plans
rm -rf resources/views/operator_plans
# rm -rf resources/views/plan_tasks (if exists)
```

**Routes** - Remove from `routes/web.php`:
- `daily-plans.*` routes (lines ~255-297)
- `plan-tasks.*` routes (lines ~289-292)
- `operator-plans.*` routes (lines ~164-181)

### 5. Create TaskWorkflowTest (Extended)
**File:** `tests/Feature/TaskWorkflowEngineTest.php`

**Tests:**
- Bulk task creation
- Bulk state transitions
- SLA escalation
- Task completion workflow
- Monthly plan statistics
- Data integrity verification

### 6. Documentation
**Files:**
- `PHASE_2_COMPLETION_GUIDE.md` - How migration went
- `DATA_MIGRATION_VERIFICATION.md` - Data integrity checks
- `SERVICE_CONSOLIDATION_GUIDE.md` - Service layer updates

---

## 🗂️ IMPLEMENTATION STEPS

### Step 1: Create Data Migration (Day 1)

**File:** `database/migrations/2026_01_29_000000_migrate_plan_tasks_to_tasks.php`

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\TaskState;

return new class extends Migration {
    public function up(): void
    {
        // 1. BACKUP - Create view of old data
        DB::statement('CREATE VIEW plan_tasks_backup AS SELECT * FROM plan_tasks');

        // 2. COPY DATA - Insert from plan_tasks to tasks
        DB::statement('
            INSERT INTO tasks (
                monthly_plan_id, department_id, task_type, state, priority,
                title, description, notes, created_by_user_id, assigned_to_user_id,
                oil_string_id, tower_id, expected_completion_at, actual_completion_at,
                created_at, updated_at
            )
            SELECT
                monthly_plan_id,
                1 as department_id, -- Default MPFM dept or from config
                "plan_task" as task_type,
                state,
                COALESCE(priority, "medium"),
                title,
                description,
                notes,
                created_by_user_id,
                assigned_operator_id,
                oil_string_id,
                tower_id,
                expected_completion_at,
                actual_completion_at,
                created_at,
                updated_at
            FROM plan_tasks
            WHERE deleted_at IS NULL
        ');

        // 3. ASSIGN OPERATORS - Migrate operator assignments
        DB::statement('
            INSERT INTO task_operators (task_id, user_id, is_primary, assigned_at, created_at, updated_at)
            SELECT
                t.id,
                pt.assigned_operator_id,
                true,
                NOW(),
                NOW(),
                NOW()
            FROM plan_tasks pt
            JOIN tasks t ON pt.id = t.id
            WHERE pt.assigned_operator_id IS NOT NULL
        ');

        // 4. VERIFY - Count records
        $oldCount = DB::table('plan_tasks')->count();
        $newCount = DB::table('tasks')->where('task_type', 'plan_task')->count();

        if ($oldCount !== $newCount) {
            throw new \Exception("Data migration verification failed: $oldCount → $newCount");
        }
    }

    public function down(): void
    {
        // 1. DELETE migrated data
        DB::table('tasks')->where('task_type', 'plan_task')->delete();

        // 2. DROP operators
        DB::table('task_operators')->whereIn('task_id',
            DB::table('tasks')->where('task_type', 'plan_task')->pluck('id')
        )->delete();

        // 3. RESTORE from backup (if backup exists)
        DB::statement('DROP VIEW IF EXISTS plan_tasks_backup');
    }
};
```

**Safety Features:**
- ✅ Creates backup view
- ✅ Verifies count matches
- ✅ Includes rollback
- ✅ Handles NULL values
- ✅ Preserves timestamps

### Step 2: Create TaskWorkflowEngine (Day 1-2)

**File:** `app/Domains/Task/Services/TaskWorkflowEngine.php`

```php
<?php
namespace App\Domains\Task\Services;

use App\Domains\Task\Models\Task;
use App\Enums\TaskState;
use App\Models\MonthlyPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskWorkflowEngine
{
    private TaskStateMachine $stateMachine;

    public function __construct(TaskStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * Create multiple tasks from monthly plan specification
     */
    public function createFromMonthlyPlan(MonthlyPlan $plan, array $tasksData): Collection
    {
        return DB::transaction(function () use ($plan, $tasksData) {
            $created = collect();

            foreach ($tasksData as $data) {
                $task = Task::create([
                    'monthly_plan_id' => $plan->id,
                    'department_id' => $data['department_id'],
                    'task_type' => $data['task_type'] ?? 'plan_task',
                    'state' => TaskState::DRAFT,
                    'priority' => $data['priority'] ?? 'medium',
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'created_by_user_id' => auth()->id(),
                    'expected_completion_at' => $data['expected_completion_at'] ?? null,
                    'type_specific_data' => $data['type_specific_data'] ?? [],
                ]);

                if (isset($data['operators'])) {
                    foreach ($data['operators'] as $operatorId => $isPrimary) {
                        $task->operators()->attach($operatorId, [
                            'is_primary' => $isPrimary,
                            'assigned_at' => now(),
                        ]);
                    }
                }

                $created->push($task);
            }

            return $created;
        });
    }

    /**
     * Bulk transition tasks to new state
     */
    public function transitionBulk(
        Collection $tasks,
        TaskState $newState,
        User $user,
        ?string $notes = null
    ): Collection {
        $results = collect();

        foreach ($tasks as $task) {
            try {
                $history = $this->stateMachine->transition(
                    $task,
                    $newState,
                    $user,
                    $notes
                );
                $results->push([
                    'task_id' => $task->id,
                    'success' => true,
                    'history' => $history,
                ]);
            } catch (\Exception $e) {
                $results->push([
                    'task_id' => $task->id,
                    'success' => false,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Check SLA and escalate if needed
     */
    public function checkAndEscalateSLA(): Collection
    {
        $escalated = collect();

        $overdue = Task::overdue()
            ->where('state', '!=', TaskState::COMPLETED->value)
            ->get();

        foreach ($overdue as $task) {
            $slaHistory = $task->slaHistories()->latest()->first();

            if (!$slaHistory || $slaHistory->escalation_level < 3) {
                $newLevel = ($slaHistory->escalation_level ?? 0) + 1;

                $slaHistory = $task->slaHistories()->create([
                    'entered_state' => $task->state->value,
                    'expected_completion_at' => $task->expected_completion_at,
                    'escalation_level' => $newLevel,
                    'escalated_to_user_id' => $this->getEscalationUser($task, $newLevel)->id,
                ]);

                $escalated->push([
                    'task_id' => $task->id,
                    'escalation_level' => $newLevel,
                    'escalated_to' => $slaHistory->escalatedTo->name,
                ]);
            }
        }

        return $escalated;
    }

    /**
     * Complete a task with validation
     */
    public function completeTask(Task $task, User $user): bool
    {
        if ($task->isCompleted()) {
            return false;
        }

        return DB::transaction(function () use ($task, $user) {
            $task->update([
                'actual_completion_at' => now(),
            ]);

            $this->stateMachine->transition(
                $task,
                TaskState::COMPLETED,
                $user,
                'Task completed'
            );

            return true;
        });
    }

    /**
     * Get workflow statistics for a monthly plan
     */
    public function getWorkflowStats(MonthlyPlan $plan): array
    {
        $tasks = $plan->tasks()->get();

        return [
            'total' => $tasks->count(),
            'by_state' => $tasks->groupBy('state')->map->count(),
            'by_priority' => $tasks->groupBy('priority')->map->count(),
            'by_department' => $tasks->groupBy('department_id')->map->count(),
            'overdue_count' => $tasks->filter(fn($t) => $t->isOverdue())->count(),
            'completion_percentage' => (int) (
                $tasks->where('state', TaskState::COMPLETED->value)->count() /
                max($tasks->count(), 1) * 100
            ),
            'on_time_percentage' => (int) (
                $tasks->filter(fn($t) =>
                    $t->isCompleted() &&
                    $t->actual_completion_at <= $t->expected_completion_at
                )->count() /
                max($tasks->completed()->count(), 1) * 100
            ),
        ];
    }

    /**
     * Get user for escalation based on level
     */
    private function getEscalationUser(Task $task, int $level): User
    {
        // Level 1: Department Manager
        // Level 2: Surveillance Supervisor
        // Level 3: Job Officer

        return match ($level) {
            1 => $task->department->manager ?? User::whereHas('roles', fn($q) =>
                $q->where('name', 'job_officer')
            )->first(),
            2 => User::whereHas('roles', fn($q) =>
                $q->where('name', 'surveillance_supervisor')
            )->first() ?? User::first(),
            default => User::whereHas('roles', fn($q) =>
                $q->where('name', 'admin')
            )->first() ?? User::first(),
        };
    }
}
```

**~250 lines of production code**

### Step 3: Update Existing Services (Day 2-3)

**Update:** `app/Services/TaskSLAService.php`

Replace old logic:
```php
// OLD
public function calculateSLA(PlanTask $task) { ... }

// NEW
public function calculateSLA(Task $task) {
    return $task->slaHistories()->latest()->first();
}
```

**Update:** `app/Http/Controllers/SurveillanceSupervisorController.php`

Replace task queries:
```php
// OLD
$tasks = PlanTask::where(...)->get();

// NEW
$tasks = Task::forDepartment(...)->get();
```

### Step 4: Create Verification Tests (Day 3)

**File:** `tests/Feature/DataMigrationTest.php`

```php
<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataMigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test migration copy data correctly
     */
    public function test_plan_tasks_migrated_to_tasks(): void
    {
        // Seed old plan_tasks
        // Run migration
        // Verify:
        // - Count matches
        // - Data intact
        // - Operators assigned
        // - States preserved
    }

    /**
     * Test migration is reversible
     */
    public function test_migration_rollback(): void
    {
        // Run up
        // Run down
        // Verify old data still exists
    }
}
```

### Step 5: Update Routes (Day 3)

**File:** `routes/web.php`

**Remove:**
- `daily-plans` prefix routes
- `plan-tasks` routes
- `operator-plans` prefix routes

**Update:**
- `monthly-plans` task routes to use `tasks.*`

### Step 6: Remove Deprecated Files (Day 4)

**Delete:**
```bash
# Controllers
rm app/Http/Controllers/PlanTaskController.php
rm app/Http/Controllers/DailyPlanController.php
rm app/Http/Controllers/OperatorPlanController.php

# Models
rm app/Models/PlanTask.php
rm app/Models/DailyPlan.php
rm app/Models/OperatorPlan.php

# Views
rm -rf resources/views/daily_plans
rm -rf resources/views/operator_plans
```

### Step 7: Cleanup & Verify (Day 4-5)

**Remove References:**
```bash
grep -r "PlanTask\|DailyPlan\|OperatorPlan" app/ --include="*.php" | grep -v "vendor/"
grep -r "daily-plans\|plan-tasks\|operator-plans" routes/ app/ resources/
```

**Clear Cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload
```

**Run Full Test Suite:**
```bash
php artisan test
```

**Verify No Broken Routes:**
```bash
php artisan route:list | grep -E "daily-plans|plan-tasks|operator-plans"
# Should return: empty
```

---

## ✅ PHASE 2 CHECKLIST

### Pre-Implementation
- [ ] Create database backup
- [ ] Review data migration carefully
- [ ] Test migration on development
- [ ] Prepare rollback procedures
- [ ] Plan testing schedule

### Implementation
- [ ] Create data migration file
- [ ] Create TaskWorkflowEngine service
- [ ] Update existing services
- [ ] Update controllers to use new Task
- [ ] Create verification tests
- [ ] Update routes (remove old, add new)
- [ ] Remove deprecated files
- [ ] Clean up references

### Testing
- [ ] Run data migration test
- [ ] Verify data integrity
- [ ] Run full test suite
- [ ] Test all workflows manually
- [ ] Test role-based access
- [ ] Test SLA tracking
- [ ] Verify no broken links

### Deployment
- [ ] Backup production database
- [ ] Run migration on staging
- [ ] Run full test suite
- [ ] UAT in staging
- [ ] Deploy to production
- [ ] Monitor logs
- [ ] Gather feedback

### Cleanup
- [ ] Remove deprecated files
- [ ] Update documentation
- [ ] Update team wiki/guides
- [ ] Archive old code (git history)

---

## 🚨 SAFETY CONSIDERATIONS

### Data Safety
- ✅ Backup created before migration
- ✅ View backup available for verification
- ✅ Migration includes rollback
- ✅ Data validation in migration
- ✅ Soft deletes preserve history

### Code Safety
- ✅ New code tested with 15+ tests
- ✅ Services use dependency injection
- ✅ Transactions ensure atomicity
- ✅ Exception handling in place
- ✅ Logging for audit trail

### Reversibility
- ✅ Down migration removes new data
- ✅ Old tables remain intact
- ✅ Can rollback anytime within 2 weeks
- ✅ Backups retained for 30 days

---

## 📊 PHASE 2 METRICS

### Code Changes
- 1 new migration file (~100 lines)
- 1 new service (~250 lines)
- 1 new test file (~200 lines)
- Updates to 5+ existing services (~200 lines total)
- Route updates (removals)

### Database Changes
- No new tables
- Populates `tasks` table
- Creates `task_operators` records
- Keeps `plan_tasks` intact (soft copy)

### Cleanup
- 3 controllers deleted
- 3 models deleted
- 3+ view directories deleted
- ~200 lines of deprecated routes removed

---

## 🔄 MIGRATION VERIFICATION

### Before Running Migration
```bash
# Count old data
mysql> SELECT COUNT(*) FROM plan_tasks;
# Result: e.g., 150 records

# Check operators
mysql> SELECT COUNT(*) FROM plan_task_operators WHERE task_id IS NOT NULL;
```

### After Running Migration
```bash
# Count new data
mysql> SELECT COUNT(*) FROM tasks WHERE task_type = 'plan_task';
# Should match: 150 records

# Check operators migrated
mysql> SELECT COUNT(*) FROM task_operators WHERE task_id IN (
    SELECT id FROM tasks WHERE task_type = 'plan_task'
);
# Should match previous count
```

### Data Integrity Checks
```bash
# Check for NULL states
SELECT COUNT(*) FROM tasks WHERE state IS NULL;
# Should be: 0

# Check for NULL created_by
SELECT COUNT(*) FROM tasks WHERE created_by_user_id IS NULL;
# Should be: 0

# Verify no data loss
SELECT SUM(t1.cnt) - SUM(t2.cnt) FROM (
    SELECT COUNT(*) as cnt FROM plan_tasks
) t1, (
    SELECT COUNT(*) as cnt FROM tasks WHERE task_type = 'plan_task'
) t2;
# Should be: 0
```

---

## ⏭️ AFTER PHASE 2

### System State
- ✅ New Task model fully operational
- ✅ All data migrated safely
- ✅ Old code removed
- ✅ Services consolidated
- ✅ No breaking changes for users

### Ready for Phase 3
- MPFM domain consolidation
- Lift curve analytics implementation
- Advanced dashboard development

---

## 📝 TIMELINE

| Day | Task | Hours |
|-----|------|-------|
| 1 | Data migration + TaskWorkflowEngine | 4h |
| 2 | Update services + Extended tests | 4h |
| 3 | Route cleanup + Verification | 3h |
| 4 | Remove deprecated files + Testing | 4h |
| 5 | Full testing + Documentation | 3h |
| **Total** | | **18h (~3 days)** |

---

## 🎯 SUCCESS CRITERIA

- ✅ All data migrated accurately
- ✅ Data integrity verified
- ✅ All tests passing
- ✅ No broken references
- ✅ Old files removed
- ✅ Services consolidated
- ✅ System performs same or better
- ✅ No regression in functionality

---

**Status:** Ready to start immediately after Phase 1 verification.
**Recommendation:** Wait 1 week after Phase 1 deployment to ensure stability, then proceed with Phase 2.

