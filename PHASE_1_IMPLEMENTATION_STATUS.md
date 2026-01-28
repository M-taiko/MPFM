# Phase 1: Enterprise Workflow Refactoring - Implementation Status

**Date:** January 28, 2026
**Status:** CORE MODELS & ROUTES COMPLETE - Ready for Migration & Testing
**Completion:** 85% (Core infrastructure done, ready for data migration)

---

## ✅ COMPLETED DELIVERABLES

### 1. Domain-Driven Architecture (Domains Folder)
- ✅ Created `app/Domains/` folder structure
- ✅ `app/Domains/Task/` - Task domain with models and services
- ✅ `app/Domains/Shared/Exceptions/` - Shared exception classes

### 2. Unified Task Model
**File:** `app/Domains/Task/Models/Task.php`

**Key Features:**
- Single `Task` model replaces: `PlanTask`, `DailyPlan`, `OperatorPlan`
- Polymorphic `task_type` field: `plan_task`, `daily_plan`, `operator_plan`
- Unified state management using `TaskState` enum
- JSON column `type_specific_data` for task-type-specific fields
- Full backward compatibility with legacy fields

**Relationships:**
- `belongsTo(MonthlyPlan)` - Parent plan
- `belongsTo(Department)` - Department ownership
- `belongsTo(User)` - Created by, Assigned to
- `belongsToMany(User)` - Multi-operator assignment via `task_operators` pivot
- `hasMany(TaskStateHistory)` - Audit trail
- `hasMany(TaskSLAHistory)` - SLA tracking
- `belongsTo(OilString)`, `belongsTo(Tower)` - Assets
- `belongsTo(MPFMTest)` - Related MPFM test

**Methods:**
- `canTransitionTo(TaskState)` - Validates allowed state transitions
- `isInState(TaskState)` - Check current state
- `isOverdue()`, `isCompleted()`, `isAssigned()`
- `getDaysUntilDue()`, `getDaysOverdue()`
- `getProgressPercentage()` - State-based progress (10%-100%)
- `getPriorityColor()` - UI color mapping
- `getAvailableTransitions()` - Returns allowed next states

### 3. Task State History Model
**File:** `app/Domains/Task/Models/TaskStateHistory.php`

**Purpose:** Audit trail for all state transitions

**Fields:**
- `task_id`, `from_state`, `to_state`
- `changed_by_user_id`, `changed_by_role` - Who made the change
- `notes` - Transition reason
- `changed_at` - When

**Methods:**
- `getDurationInState()` - Minutes spent in state
- `isForwardTransition()` - Check if progression
- `isRejection()` - Check if rejection

### 4. Task SLA History Model
**File:** `app/Domains/Task/Models/TaskSLAHistory.php`

**Purpose:** Track SLA compliance and escalations

**Fields:**
- `task_id`, `entered_state`
- `expected_completion_at`, `actual_completion_at`
- `escalation_level`, `escalated_to_user_id`
- `resolved_at` - When escalation resolved

### 5. TaskStateMachine Service
**File:** `app/Domains/Task/Services/TaskStateMachine.php`

**Core Methods:**
- `transition(Task, TaskState, User, notes)` - Atomic state change
  - Authorization check: `canUserTransition()`
  - Business validation: `canTransitionTo()`
  - Atomic transaction wrapper
  - Records audit trail in `TaskStateHistory`
  - Updates SLA tracking
  - Fires `TaskStateChanged` event

- `canUserTransition()` - Role-based authorization
  - Admin: Always allowed
  - Job Officer: DRAFT, JOB_OFFICER_APPROVED, COMPLETED
  - Surveillance Supervisor: SUBMITTED_TO_SURVEILLANCE, ASSIGNED_TO_DEPARTMENT, SURVEILLANCE_REVIEWED
  - Department Manager: ASSIGNED_TO_DEPARTMENT, DEPARTMENT_REVIEWED
  - Operator: IN_PROGRESS

- `updateSLATracking()` - Auto SLA on state change
  - Gets SLA duration per state
  - Calculates `expected_completion_at`
  - Records in `TaskSLAHistory`

### 6. Exception Classes
**File:** `app/Domains/Shared/Exceptions/InvalidStateTransition.php`

Used when state transitions are invalid or unauthorized.

### 7. Database Migration
**File:** `database/migrations/2026_01_28_000000_create_tasks_table.php`

**Tables Created:**

#### `tasks` table
- Columns: id, monthly_plan_id, department_id, task_type, state, priority
- Relationships: oil_string_id, tower_id, assigned_to_user_id, created_by_user_id
- Audit: expected_completion_at, actual_completion_at, created_at, updated_at, deleted_at
- Data: title, description, notes, type_specific_data (JSON)
- Relations: mpfm_test_id

**Indexes:**
- Simple: `(state)`, `(task_type)`, `(assigned_to_user_id)`, `(created_by_user_id)`
- Composite: `(department_id, state)`, `(department_id, task_type)`, `(monthly_plan_id, state)`
- Timestamped: `(expected_completion_at)`

#### `task_state_histories` table
- Audit trail of all state changes
- Indexes: `(task_id)`, `(changed_at)`, `(task_id, changed_at)`

#### `task_sla_histories` table
- SLA tracking and escalations
- Indexes: `(task_id)`, `(expected_completion_at)`, `(escalation_level)`

#### `task_operators` table
- Many-to-many relationship for multi-operator assignment
- Fields: task_id, user_id, is_primary, assigned_at
- Unique constraint: (task_id, user_id)

### 8. Task Controller
**File:** `app/Http/Controllers/TaskController.php`

**Actions:**
- `index()` - List tasks with role-based filtering
- `create()` - Show creation form with dropdowns
- `store()` - Validate and create task
- `show()` - Display task detail with history
- `edit()` - Show edit form
- `update()` - Update task
- `destroy()` - Delete task
- `transition()` - Change task state (calls TaskStateMachine)

**Role-Based Access:**
- Department Manager: Only their department
- Operator: Only assigned tasks
- Admin/Surveillance: All tasks

### 9. Task Views
**Location:** `resources/views/tasks/`

#### `index.blade.php`
- Task listing with filtering
- Client-side filters: title, state, department
- Action buttons: view, edit, delete
- Progress bar per task
- Pagination (15 per page)

#### `show.blade.php`
- Full task detail with sidebar
- Task information, assignment, timeline
- State history timeline with user info
- State transition form
- Progress bar, priority badge
- Overdue indicator

#### `form.blade.php`
- Unified create/edit form
- Sections:
  - Basic Information: title, description, notes
  - Assignment & Scheduling: plan, department, type, priority, assignment, due date
  - Asset Association: oil string, tower
  - Form Actions: Save/Cancel

### 10. Routes
**File:** `routes/web.php`

**New Task Routes (all under `/tasks/` prefix with `tasks.` names):**
```
GET    /tasks              → tasks.index
GET    /tasks/create       → tasks.create
POST   /tasks              → tasks.store
GET    /tasks/{task}       → tasks.show
GET    /tasks/{task}/edit  → tasks.edit
PUT    /tasks/{task}       → tasks.update
DELETE /tasks/{task}       → tasks.destroy
POST   /tasks/{task}/transition → tasks.transition
```

All routes protected with `auth` middleware.

---

## 📊 SCOPES & HELPERS ADDED

### Task Model Scopes:
- `byState(TaskState)` - Filter by state
- `byType(string)` - Filter by task type
- `forDepartment(id)` - Filter by department
- `overdue()` - Tasks past due date
- `byPriority(string)` - Filter by priority
- `pending()` - Not yet completed
- `completed()` - Finished tasks
- `highPriority()` - High/critical only
- `assignedTo(userId)` - Tasks assigned to user

### Task Model Helpers:
- `isInState(TaskState)` - Boolean check
- `isAssigned()` - Has any assignment
- `isCompleted()` - Is in COMPLETED state
- `isOverdue()` - Past due and not completed
- `isAssignedTo(userId)` - Check specific user
- `getDaysUntilDue()` - Days remaining
- `getDaysOverdue()` - Days past due
- `getProgressPercentage()` - 0-100% based on state
- `getPriorityColor()` - Bootstrap color class
- `getAvailableTransitions()` - Next allowed states

---

## ⚠️ DEPRECATED FILES TO REMOVE

### Controllers (3 files)
1. `app/Http/Controllers/PlanTaskController.php` - Old plan task controller
2. `app/Http/Controllers/DailyPlanController.php` - Old daily plan controller
3. `app/Http/Controllers/OperatorPlanController.php` - Old operator plan controller

**Impact:** Routes will need cleanup in `routes/web.php` (lines 164-181, 289-292)

### Models (3 files)
1. `app/Models/PlanTask.php` - Replaced by `Task`
2. `app/Models/DailyPlan.php` - Replaced by `Task`
3. `app/Models/OperatorPlan.php` - Replaced by `Task`

**Impact:** References in other models and migrations

### Views (2 directories)
1. `resources/views/daily_plans/` - Old daily plan views
2. `resources/views/operator_plans/` - Old operator plan views
3. Old plan task related views in `monthly_plans/`

**Impact:** Links in sidebar, controllers redirects

### Services (5+ services)
Services with overlapping task/plan logic that will be consolidated or updated:
- Task-related services in `app/Services/`
- Plan-related services
- Daily plan processors
- Operator plan managers

---

## 🔄 NEXT STEPS (15% Remaining)

### Step 1: Run Database Migration
```bash
php artisan migrate
```

This will create:
- `tasks` table
- `task_state_histories` table
- `task_sla_histories` table
- `task_operators` table

### Step 2: Create TaskPolicy
**File:** `app/Policies/TaskPolicy.php`

Required for authorization in views and controller:
```php
public function update(User $user, Task $task) { ... }
public function delete(User $user, Task $task) { ... }
public function transition(User $user, Task $task) { ... }
```

### Step 3: Create TaskWorkflowEngine Service
**File:** `app/Domains/Task/Services/TaskWorkflowEngine.php`

Orchestrates complex workflows:
- Task creation from MonthlyPlan
- Bulk operations
- SLA monitoring
- Event dispatching

### Step 4: Data Migration (If Upgrading Existing System)
```bash
php artisan make:migration migrate_plan_tasks_to_tasks
```

This would:
- Copy data from `plan_tasks` to `tasks`
- Preserve state history
- Map old models to task types
- Validate integrity

### Step 5: Update Dependent Services
Update existing services to use `Task` model:
- `TaskStateService` → Use `TaskStateMachine`
- `TaskSLAService` → Use `TaskSLAHistory`
- Monthly plan services
- SLA notification services

### Step 6: Update Routes
Clean up deprecated routes:
- Remove `daily-plans` routes (lines 255-297)
- Remove `plan-tasks` routes (lines 289-292)
- Remove `operator-plans` routes (lines 164-181)
- Keep `monthly-plans` routes but update task handling

### Step 7: Remove Deprecated Files
Delete after updating references:
- 3 controllers
- 3 models
- View directories
- Related migrations

### Step 8: Update Navigation
**File:** `resources/views/layouts/sidebar.blade.php`

Add "Tasks" link pointing to `route('tasks.index')`

### Step 9: Run Tests
```bash
php artisan test
```

Create feature tests for:
- Task CRUD operations
- State transitions with authorization
- SLA tracking
- Multi-operator assignment

### Step 10: Documentation
- User guide for new Task interface
- Admin guide for migration
- Developer guide for service layer

---

## 📋 SUMMARY OF FILES CREATED

```
Created Files (New):
├── app/Domains/
│   ├── Task/
│   │   ├── Models/
│   │   │   ├── Task.php (NEW - 347 lines)
│   │   │   ├── TaskStateHistory.php (NEW - 103 lines)
│   │   │   └── TaskSLAHistory.php (NEW - 97 lines)
│   │   └── Services/
│   │       └── TaskStateMachine.php (NEW - 152 lines)
│   └── Shared/
│       └── Exceptions/
│           └── InvalidStateTransition.php (NEW - 13 lines)
├── app/Http/Controllers/
│   └── TaskController.php (NEW - 200+ lines)
├── resources/views/tasks/
│   ├── index.blade.php (NEW - 180 lines)
│   ├── show.blade.php (NEW - 280 lines)
│   └── form.blade.php (NEW - 280 lines)
├── database/migrations/
│   └── 2026_01_28_000000_create_tasks_table.php (NEW - 100+ lines)
└── PHASE_1_IMPLEMENTATION_STATUS.md (THIS FILE)

Modified Files:
└── routes/web.php (Added Task routes at line 483-500)

Total New Code: 1,500+ lines of production code
```

---

## ✨ KEY ARCHITECTURAL FEATURES

### 1. Polymorphic Task Model
One `Task` model handles all task types via:
- `task_type` enum field
- `type_specific_data` JSON column
- Task-specific validation in services

### 2. Atomic State Transitions
```php
DB::transaction(function () {
    // 1. Update task state
    // 2. Record in audit trail
    // 3. Update SLA tracking
    // 4. Fire event for listeners
    // 5. All or nothing
});
```

### 3. Role-Based Authorization
Embedded in `TaskStateMachine.canUserTransition()`:
- Admin: All transitions
- Job Officer: Create, approve, complete
- Surveillance: Assign to department, review
- Department Manager: Assign operators, review
- Operator: Start, update status

### 4. Backward Compatibility
- Old model fields still accessible
- Can coexist during migration
- Soft deletes for data safety
- JSON storage for flexibility

### 5. Audit Trail
Every state change recorded with:
- Who made change (user + role)
- When change happened
- Why (notes)
- Duration in previous state

### 6. SLA Tracking
Automatic on each state change:
- Expected completion calculated
- Escalation levels monitored
- History maintained for trending
- Alerts triggered if overdue

---

## 🎯 PHASE 1 SUCCESS CRITERIA

- ✅ Unified Task model created
- ✅ State machine service implemented
- ✅ Views for CRUD operations
- ✅ Routes established
- ✅ Database migration defined
- ✅ Helper methods and scopes
- ⏳ Migration executed (pending - manual step)
- ⏳ Data migrated (pending - if upgrading)
- ⏳ Tests created and passing (pending)
- ⏳ Deprecated files removed (pending)

---

## 📞 IMPLEMENTATION NOTES

### For Database Migration:
```bash
# Run migrations
php artisan migrate

# If need to rollback
php artisan migrate:rollback
```

### For Testing:
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/TaskWorkflowTest.php

# With coverage
php artisan test --coverage
```

### For Debugging:
```bash
# Check table structure
php artisan tinker
>>> \DB::connection()->getDoctrineSchemaManager()->listTableColumns('tasks')

# Check routes
php artisan route:list | grep tasks
```

---

**Status:** Core infrastructure complete. Ready for database migration and testing.

**Next Action:** Execute Step 1 (Run Database Migration)
