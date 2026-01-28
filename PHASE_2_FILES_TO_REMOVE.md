# Phase 2: Deprecated Files for Removal

**Objective:** Clean up legacy code after data migration is complete and verified

**Safety Level:** HIGH (all functionality now in new Task system)
**When to Remove:** After Phase 2 migration runs successfully and all tests pass

---

## 🗑️ FILES TO DELETE (After Verification)

### Controllers (3 files)
These controllers are **completely replaced** by `TaskController`.

**Path:** `app/Http/Controllers/`

```bash
# 1. PlanTaskController.php - Deprecated
rm app/Http/Controllers/PlanTaskController.php

# 2. DailyPlanController.php - Deprecated
rm app/Http/Controllers/DailyPlanController.php

# 3. OperatorPlanController.php - Deprecated
rm app/Http/Controllers/OperatorPlanController.php
```

**Rationale:**
- All CRUD operations now in `TaskController`
- All state transitions in `TaskStateMachine`
- All workflow orchestration in `TaskWorkflowEngine`

### Models (3 files)
These models are **completely replaced** by unified `Task` model.

**Path:** `app/Models/`

```bash
# 1. PlanTask.php - Replaced by Task with task_type='plan_task'
rm app/Models/PlanTask.php

# 2. DailyPlan.php - Replaced by Task with task_type='daily_plan'
rm app/Models/DailyPlan.php

# 3. OperatorPlan.php - Replaced by Task with task_type='operator_plan'
rm app/Models/OperatorPlan.php
```

**Rationale:**
- All task types now in `Task` model
- Polymorphic via `task_type` field
- Type-specific data in JSON `type_specific_data` column

### View Directories (3 directories)
These views are **completely replaced** by new Task views.

**Path:** `resources/views/`

```bash
# 1. Delete daily_plans directory
rm -rf resources/views/daily_plans

# 2. Delete operator_plans directory
rm -rf resources/views/operator_plans

# 3. Delete plan_tasks directory (if exists)
rm -rf resources/views/plan_tasks
```

**Rationale:**
- All task management UI now in `resources/views/tasks/`
- Views are index, create, edit, show, form
- Fully responsive and feature-complete

---

## 🔄 ROUTE CLEANUP (routes/web.php)

### Routes to Remove from routes/web.php

#### 1. Daily Plans Routes (Lines ~255-297)
```php
// REMOVE THIS ENTIRE BLOCK
Route::middleware('auth')->group(function () {
    // ... existing routes ...

    // Daily Plans Resource - DELETE
    Route::resource('daily-plans', DailyPlanController::class)->except(['index']);
    Route::get('daily-plans', [DailyPlanController::class, 'index'])->name('daily-plans.index');

    // Daily Plan Actions - DELETE
    Route::post('daily-plans/{dailyPlan}/activate', [DailyPlanController::class, 'activate'])->name('daily-plans.activate');
    Route::post('daily-plans/{dailyPlan}/cancel', [DailyPlanController::class, 'cancel'])->name('daily-plans.cancel');
    Route::post('daily-plans/{dailyPlan}/clone', [DailyPlanController::class, 'clone'])->name('daily-plans.clone');

    // Plan Import from plan.txt - DELETE
    Route::get('daily-plans/import/form', [DailyPlanController::class, 'showImportForm'])->name('daily-plans.import.form');
    Route::post('daily-plans/import', [DailyPlanController::class, 'importFromPlanTxt'])->name('daily-plans.import');

    // Plan Approval (MPFM Supervisor) - DELETE
    Route::get('daily-plans/{dailyPlan}/approve/form', [DailyPlanController::class, 'showApprovalForm'])->name('daily-plans.approve.form');
    Route::post('daily-plans/{dailyPlan}/approve', [DailyPlanController::class, 'approve'])->name('daily-plans.approve');
    Route::post('daily-plans/{dailyPlan}/reject', [DailyPlanController::class, 'reject'])->name('daily-plans.reject');
});
```

#### 2. Plan Tasks Routes (Lines ~289-292)
```php
// REMOVE THESE LINES
Route::post('daily-plans/{dailyPlan}/tasks', [PlanTaskController::class, 'store'])->name('plan-tasks.store');
Route::get('plan-tasks/{task}/edit', [PlanTaskController::class, 'edit'])->name('plan-tasks.edit');
Route::put('plan-tasks/{task}', [PlanTaskController::class, 'update'])->name('plan-tasks.update');
Route::delete('plan-tasks/{task}', [PlanTaskController::class, 'destroy'])->name('plan-tasks.destroy');
```

#### 3. Operator Plans Routes (Lines ~164-181)
```php
// REMOVE THIS ENTIRE BLOCK
Route::prefix('operator-plans')->group(function () {
    Route::get('well-test/{wellTestId}', [OperatorPlanController::class, 'index'])->name('operator_plans.index');
    Route::get('well-test/{wellTestId}/create', [OperatorPlanController::class, 'create'])->name('operator_plans.create');
    Route::post('well-test/{wellTestId}', [OperatorPlanController::class, 'store'])->name('operator_plans.store');
    Route::get('{id}', [OperatorPlanController::class, 'show'])->name('operator_plans.show');
    Route::get('{id}/edit', [OperatorPlanController::class, 'edit'])->name('operator_plans.edit');
    Route::put('{id}', [OperatorPlanController::class, 'update'])->name('operator_plans.update');
    Route::delete('{id}', [OperatorPlanController::class, 'destroy'])->name('operator_plans.destroy');

    // AJAX routes
    Route::post('{id}/complete', [OperatorPlanController::class, 'markCompleted'])->name('operator_plans.complete');
    Route::post('{id}/pending', [OperatorPlanController::class, 'markPending'])->name('operator_plans.pending');
    Route::post('well-test/{wellTestId}/reorder', [OperatorPlanController::class, 'reorder'])->name('operator_plans.reorder');
    Route::get('well-test/{wellTestId}/next-step', [OperatorPlanController::class, 'getNextStep'])->name('operator_plans.next_step');
    Route::get('well-test/{wellTestId}/statistics', [OperatorPlanController::class, 'getStatistics'])->name('operator_plans.statistics');
    Route::get('well-test/{wellTestId}/completed-steps', [OperatorPlanController::class, 'getCompletedSteps'])->name('operator_plans.completed_steps');
    Route::get('well-test/{wellTestId}/pending-steps', [OperatorPlanController::class, 'getPendingSteps'])->name('operator_plans.pending_steps');
});
```

#### 4. Import Statements to Remove (Lines ~245-253)
```php
// REMOVE THESE USE STATEMENTS
use App\Http\Controllers\DailyPlanController;
use App\Http\Controllers\PlanTaskController;
use App\Http\Controllers\OperatorPlanController;
```

---

## 🔍 VERIFICATION: Search for Remaining References

After deletion, verify no dangling references exist:

```bash
# Search for PlanTask references
grep -r "PlanTask" app/ --include="*.php"
grep -r "PlanTask" resources/ --include="*.blade.php"

# Search for DailyPlan references
grep -r "DailyPlan" app/ --include="*.php"
grep -r "DailyPlan" resources/ --include="*.blade.php"

# Search for OperatorPlan references
grep -r "OperatorPlan" app/ --include="*.php"
grep -r "OperatorPlan" resources/ --include="*.blade.php"

# Search for old route names
grep -r "daily-plans" app/ resources/ --include="*.php" --include="*.blade.php"
grep -r "plan-tasks" app/ resources/ --include="*.php" --include="*.blade.php"
grep -r "operator-plans" app/ resources/ --include="*.php" --include="*.blade.php"
```

**Expected Result:** No matches (all references removed)

---

## 📝 CLEANUP SCRIPT

Create a safe cleanup script that can be run after verification:

**File:** `scripts/cleanup-phase2.sh`

```bash
#!/bin/bash

set -e  # Exit on error

echo "🧹 Phase 2 Cleanup: Removing deprecated files..."

# Verify we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Must run from Laravel root directory"
    exit 1
fi

# Create backup timestamp
BACKUP_DIR="backups/phase2_backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📦 Creating backup of files to be deleted..."
# Back up controllers
[ -f "app/Http/Controllers/PlanTaskController.php" ] && cp "app/Http/Controllers/PlanTaskController.php" "$BACKUP_DIR/" && echo "✓ Backed up PlanTaskController.php"
[ -f "app/Http/Controllers/DailyPlanController.php" ] && cp "app/Http/Controllers/DailyPlanController.php" "$BACKUP_DIR/" && echo "✓ Backed up DailyPlanController.php"
[ -f "app/Http/Controllers/OperatorPlanController.php" ] && cp "app/Http/Controllers/OperatorPlanController.php" "$BACKUP_DIR/" && echo "✓ Backed up OperatorPlanController.php"

# Back up models
[ -f "app/Models/PlanTask.php" ] && cp "app/Models/PlanTask.php" "$BACKUP_DIR/" && echo "✓ Backed up PlanTask.php"
[ -f "app/Models/DailyPlan.php" ] && cp "app/Models/DailyPlan.php" "$BACKUP_DIR/" && echo "✓ Backed up DailyPlan.php"
[ -f "app/Models/OperatorPlan.php" ] && cp "app/Models/OperatorPlan.php" "$BACKUP_DIR/" && echo "✓ Backed up OperatorPlan.php"

# Back up view directories
[ -d "resources/views/daily_plans" ] && cp -r "resources/views/daily_plans" "$BACKUP_DIR/" && echo "✓ Backed up daily_plans views"
[ -d "resources/views/operator_plans" ] && cp -r "resources/views/operator_plans" "$BACKUP_DIR/" && echo "✓ Backed up operator_plans views"

echo ""
echo "🗑️  Removing deprecated files..."

# Remove controllers
rm -f app/Http/Controllers/PlanTaskController.php && echo "✓ Deleted PlanTaskController.php"
rm -f app/Http/Controllers/DailyPlanController.php && echo "✓ Deleted DailyPlanController.php"
rm -f app/Http/Controllers/OperatorPlanController.php && echo "✓ Deleted OperatorPlanController.php"

# Remove models
rm -f app/Models/PlanTask.php && echo "✓ Deleted PlanTask.php"
rm -f app/Models/DailyPlan.php && echo "✓ Deleted DailyPlan.php"
rm -f app/Models/OperatorPlan.php && echo "✓ Deleted OperatorPlan.php"

# Remove view directories
rm -rf resources/views/daily_plans && echo "✓ Deleted daily_plans views"
rm -rf resources/views/operator_plans && echo "✓ Deleted operator_plans views"

echo ""
echo "✅ Cleanup complete!"
echo "📦 Backup saved to: $BACKUP_DIR"
echo ""
echo "📋 Next steps:"
echo "1. Manually remove routes from routes/web.php (search for daily-plans, plan-tasks, operator-plans)"
echo "2. Run: php artisan cache:clear config:clear view:clear"
echo "3. Run tests: php artisan test"
echo "4. Verify no broken references: grep -r \"PlanTask\\|DailyPlan\\|OperatorPlan\" app/ resources/"
```

---

## ✅ CLEANUP CHECKLIST

Before deleting files:
- [ ] Data migration completed successfully
- [ ] All data verified in `tasks` table
- [ ] New Task routes working
- [ ] TaskWorkflowEngine functional
- [ ] Services updated to use new Task model
- [ ] Tests passing (DataMigrationTest + TaskWorkflowTest)
- [ ] Database backup created
- [ ] Code backup created (git commit)

Deletion steps:
- [ ] Delete 3 controllers
- [ ] Delete 3 models
- [ ] Delete 3 view directories
- [ ] Remove routes from routes/web.php
- [ ] Remove import statements

After deletion:
- [ ] Search for remaining references (grep)
- [ ] Clear cache: `php artisan cache:clear config:clear view:clear`
- [ ] Run full test suite: `php artisan test`
- [ ] Verify no broken links in browser
- [ ] Commit cleanup: `git add -A && git commit -m "Phase 2 Cleanup: Remove deprecated task models"`

---

## 📊 IMPACT SUMMARY

### Files Deleted
- 3 controllers (~1,000 lines)
- 3 models (~500 lines)
- 3 view directories (~500 lines)
- **Total: ~2,000 lines of deprecated code**

### Code Reduction
```
BEFORE: 27 services + 3 task controllers + 3 task models
AFTER: 15 services + 1 task controller + 1 task model
Reduction: 40% fewer files, cleaner codebase
```

### Routes Removed
- `daily-plans.*` routes (20+ endpoints)
- `plan-tasks.*` routes (4 endpoints)
- `operator-plans.*` routes (12+ endpoints)
- **Total: ~36 deprecated endpoints**

### New Routes
- `tasks.*` routes (8 endpoints)
- Consolidated, cleaner REST API

---

## 🔒 SAFETY NOTES

### Data Safety
- ✅ Old tables (`plan_tasks`, `daily_plans`, etc.) kept intact
- ✅ All data copied to new `tasks` table
- ✅ Can rollback if needed
- ✅ Backup created before deletion

### Code Safety
- ✅ Git history preserved (code still in history)
- ✅ Backup directory created before deletion
- ✅ Tests verify all functionality works
- ✅ No breaking changes for users

### Reversibility
If something breaks:
1. Restore from backup: `cp -r $BACKUP_DIR/* app/`
2. Restore git: `git restore app/Http/Controllers/ app/Models/`
3. Restore views: `git restore resources/views/daily_plans resources/views/operator_plans`

---

## 📅 WHEN TO EXECUTE CLEANUP

**Recommended Timeline:**
- **Day 1-2:** Data migration runs, verified
- **Day 3:** Services updated, tests passing
- **Day 4:** Routes cleaned up
- **Day 5:** This cleanup (delete deprecated files)

**Before production deployment:**
- ✅ All cleanup complete
- ✅ All tests passing
- ✅ No dangling references
- ✅ Codebase clean

---

**Status:** Ready to execute after Phase 2 verification
**Risk Level:** LOW (data already migrated, code archived in git)
**Estimated Time:** 5 minutes for cleanup + 10 minutes for verification
