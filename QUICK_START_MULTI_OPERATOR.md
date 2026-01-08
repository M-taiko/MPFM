# Multi-Operator System - Quick Start Guide

## 🚀 Setup Instructions

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will create:
- `daily_plans` table
- `plan_tasks` table
- Add `plan_task_id` to `mpfm_tests` table

### Step 2: Seed Test Data (Optional)
Create a seeder for testing:
```bash
php artisan make:seeder MultiOperatorSeeder
php artisan db:seed --class=MultiOperatorSeeder
```

### Step 3: Verify Models
Models are created:
- `App\Models\DailyPlan`
- `App\Models\PlanTask`
- `App\Models\MPFMTest` (updated)

### Step 4: Test Service Layer
```php
use App\Services\DailyPlanService;
use App\Models\User;
use Carbon\Carbon;

$service = app(DailyPlanService::class);

// Create a plan
$plan = $service->createPlan([
    'created_by' => 1, // Supervisor user ID
    'plan_name' => 'Daily Production Test - Field A',
    'plan_date' => Carbon::today(),
    'oil_field_id' => 1,
    'tower_id' => 1,
    'plan_notes' => 'Standard production testing',
], [
    // Task 1
    [
        'assigned_to' => 2, // Operator 1
        'oil_string_id' => 1,
        'mpfm_device' => 'MPFM-001',
        'expected_oil_rate' => 1000,
        'expected_gas_rate' => 5.5,
        'task_instructions' => 'Test string A in morning shift',
        'priority' => 'high',
    ],
    // Task 2
    [
        'assigned_to' => 3, // Operator 2
        'oil_string_id' => 2,
        'mpfm_device' => 'MPFM-002',
        'expected_oil_rate' => 850,
        'expected_gas_rate' => 4.2,
        'task_instructions' => 'Test string B in morning shift',
        'priority' => 'normal',
    ],
]);

// Activate the plan
$service->activatePlan($plan);

// Get operator's tasks
$operator = User::find(2);
$tasks = $service->getOperatorPendingTasks($operator);

// Start a task
$task = $tasks->first();
$service->startTask($task, $operator);

// Complete a task
$service->completeTask($task, [
    'completion_notes' => 'Test completed successfully',
]);
```

---

## 📊 Database Structure

### DailyPlan
```
daily_plans
├── id
├── created_by (supervisor_id)
├── plan_name
├── plan_date
├── oil_field_id
├── tower_id
├── status
├── plan_notes
├── total_tasks (auto-updated)
├── completed_tasks (auto-updated)
├── pending_tasks (auto-updated)
├── in_progress_tasks (auto-updated)
└── timestamps
```

### PlanTask
```
plan_tasks
├── id
├── daily_plan_id
├── assigned_to (operator_id)
├── task_order
├── oil_string_id
├── mpfm_device
├── expected_* (values for comparison)
├── task_instructions
├── priority
├── status
├── started_at
├── completed_at
├── duration_minutes
└── timestamps
```

### MPFMTest (Updated)
```
mpfm_tests
├── ...existing fields...
├── plan_task_id (NEW)
└── ...
```

---

## 🔄 Workflow Example

### Supervisor Creates Plan
```php
$service = app(DailyPlanService::class);

$plan = $service->createPlan([
    'created_by' => auth()->id(),
    'plan_name' => 'Field A - Daily Test',
    'plan_date' => '2026-01-07',
    'oil_field_id' => 1,
    'tower_id' => 1,
]);

// Add multiple tasks for different operators
$service->addTasksToPlan($plan, [
    [
        'assigned_to' => 2,
        'oil_string_id' => 1,
        'mpfm_device' => 'MPFM-001',
        'expected_oil_rate' => 1000,
        // ...
    ],
    [
        'assigned_to' => 3,
        'oil_string_id' => 2,
        'mpfm_device' => 'MPFM-002',
        'expected_oil_rate' => 850,
        // ...
    ],
]);

// Activate for operators to see
$service->activatePlan($plan);
```

### Operator Views Tasks
```php
$operator = auth()->user();

// Today's tasks
$tasks = $service->getOperatorTasksForDate($operator, Carbon::today());

// All pending tasks
$pendingTasks = $service->getOperatorPendingTasks($operator);

// Tasks grouped by plan
$tasksByPlan = $service->getOperatorTasksGroupedByPlan($operator);
```

### Operator Executes Task
```php
$task = PlanTask::find(1);

// Start the task
$service->startTask($task, auth()->user());

// ... operator uploads MPFM log file ...
// ... system parses and creates MPFMTest ...

// Complete the task
$service->completeTask($task, [
    'completion_notes' => 'Test completed successfully',
    'operator_notes' => 'Minor pressure fluctuation observed',
]);
```

### Auto-Linking Test to Task
When operator completes a task and creates an MPFM test:
```php
MPFMTest::create([
    'plan_task_id' => $task->id,
    'user_id' => $task->assigned_to,
    'oil_field_id' => $task->oilString->tower->oil_field_id,
    'tower_id' => $task->oilString->tower_id,
    'oil_string_id' => $task->oil_string_id,
    'test_date' => $task->dailyPlan->plan_date,
    'oil_rate' => 950, // from parsed log
    'gas_rate' => 5.2,  // from parsed log
    // ... other test data ...
]);
```

---

## 📈 Analytics Examples

### Plan Statistics
```php
$stats = $service->getPlanStatistics($plan);

/*
Returns:
[
    'total_tasks' => 5,
    'completed_tasks' => 3,
    'pending_tasks' => 1,
    'in_progress_tasks' => 1,
    'completion_percentage' => 60.0,
    'is_completed' => false,
    'operators_count' => 3,
    'average_task_duration' => 45.5,
    'tasks_with_issues' => 0,
]
*/
```

### Operator Performance
```php
$performance = $service->getOperatorPerformance(
    $operator,
    Carbon::parse('2026-01-01'),
    Carbon::parse('2026-01-31')
);

/*
Returns:
[
    'total_tasks' => 20,
    'completed_tasks' => 18,
    'pending_tasks' => 1,
    'in_progress_tasks' => 1,
    'failed_tasks' => 0,
    'completion_rate' => 90.0,
    'average_duration' => 42.3,
    'tasks_with_issues' => 2,
]
*/
```

### Deviation Analysis
```php
$task = PlanTask::find(1);
$deviation = $task->getDeviation();

/*
Returns (when task has test):
[
    'oil_rate' => -5.0,        // -5% from expected
    'gas_rate' => -5.45,       // -5.45% from expected
    'water_cut' => 10.0,       // +10% from expected
    'pressure' => 2.5,         // +2.5% from expected
    'temperature' => null,     // No expected value set
]
*/
```

---

## 🎨 UI Components Needed

### Supervisor Views
1. **Plan Calendar** (`/supervisor/plans/calendar`)
2. **Create Plan** (`/supervisor/plans/create`)
3. **Plan Details** (`/supervisor/plans/{id}`)
4. **Add Tasks** (`/supervisor/plans/{id}/tasks/create`)
5. **Monitor Dashboard** (`/supervisor/dashboard`)
6. **Performance Reports** (`/supervisor/reports/performance`)

### Operator Views
1. **My Dashboard** (`/operator/dashboard`)
2. **Today's Tasks** (`/operator/tasks/today`)
3. **Task Execution** (`/operator/tasks/{id}/execute`)
4. **Task History** (`/operator/tasks/history`)
5. **My Performance** (`/operator/performance`)

---

## 🔐 Permissions Setup

Add to `RolePermissionSeeder`:

```php
// Supervisor permissions
$supervisorPermissions = [
    'daily_plan.create',
    'daily_plan.view_all',
    'daily_plan.edit',
    'daily_plan.delete',
    'daily_plan.activate',
    'plan_task.create',
    'plan_task.assign',
    'plan_task.edit',
    'plan_task.delete',
    'plan_task.view_all',
    'report.operator_performance',
];

// Operator permissions
$operatorPermissions = [
    'plan_task.view_assigned',
    'plan_task.execute',
    'plan_task.complete',
    'mpfm_test.create',
    'mpfm_test.view_own',
];
```

---

## 🧪 Testing

### Test Plan Creation
```bash
php artisan tinker

use App\Services\DailyPlanService;
use App\Models\User;
use Carbon\Carbon;

$service = app(DailyPlanService::class);
$supervisor = User::where('email', 'supervisor@example.com')->first();

$plan = $service->createPlan([
    'created_by' => $supervisor->id,
    'plan_name' => 'Test Plan',
    'plan_date' => Carbon::tomorrow(),
    'oil_field_id' => 1,
    'tower_id' => 1,
    'plan_notes' => 'Test plan for development',
]);

$service->addTasksToPlan($plan, [
    [
        'assigned_to' => 2,
        'oil_string_id' => 1,
        'mpfm_device' => 'MPFM-001',
        'expected_oil_rate' => 1000,
        'expected_gas_rate' => 5.5,
        'priority' => 'high',
    ]
]);

$service->activatePlan($plan);
```

---

## 📝 Notes

### Data Isolation
- Operators can ONLY see tasks assigned to them
- Use `scopeAssignedTo()` in all operator queries
- Supervisors can only see plans they created
- Use `scopeCreatedBy()` in all supervisor queries

### Auto-Updates
- Task completion auto-updates plan counters
- Plan status auto-updates based on task statuses
- Duration auto-calculated from start/complete timestamps

### Expected vs Actual
- Tasks store expected values
- Tests store actual values
- System calculates deviation percentage
- Visual indicators for acceptable/warning/critical deviations

---

**Next**: Implement controllers and views for complete user interface.
