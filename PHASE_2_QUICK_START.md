# Phase 2: Quick Start - Data Migration & Service Consolidation

**Status:** Ready to start (after Phase 1 stabilization)
**Estimated Duration:** 3-5 days
**Complexity:** Medium
**Risk:** Low (all reversible)

---

## ⚡ 5-MINUTE OVERVIEW

### What Phase 2 Does
Migrates data from old models and consolidates services:

**Before:**
```
plan_tasks table (old)     DailyPlan      OperatorPlan
     ↓                          ↓              ↓
Multiple separate models   Scattered logic  Multiple controllers
```

**After:**
```
tasks table (unified)
     ↓
One model, one service, one controller
All old data safely migrated
```

### Deliverables
1. ✅ Data migration (plan_tasks → tasks)
2. ✅ TaskWorkflowEngine service
3. ✅ Service layer updates
4. ✅ Deprecated file removal
5. ✅ Comprehensive tests

### Safety
- ✅ Backup created
- ✅ Migration reversible
- ✅ Data integrity verified
- ✅ Tests included

---

## 🚀 3-DAY DEPLOYMENT

### Day 1: Data Migration
```bash
# 1. Backup production
mysqldump -u root MPFM > backup_phase2_$(date +%Y%m%d).sql

# 2. Create migration file
php artisan make:migration migrate_plan_tasks_to_tasks

# 3. Run migration
php artisan migrate

# 4. Verify data
php artisan tinker
>>> DB::table('tasks')->where('task_type', 'plan_task')->count()
# Should match old plan_tasks count
```

### Day 2: Create TaskWorkflowEngine
**File:** `app/Domains/Task/Services/TaskWorkflowEngine.php`

```php
<?php
namespace App\Domains\Task\Services;

class TaskWorkflowEngine
{
    // Create from monthly plan
    public function createFromMonthlyPlan(MonthlyPlan $plan, array $tasksData): Collection { }

    // Bulk transitions
    public function transitionBulk(Collection $tasks, TaskState $newState, User $user): Collection { }

    // Check and escalate SLA
    public function checkAndEscalateSLA(): Collection { }

    // Complete task
    public function completeTask(Task $task, User $user): bool { }

    // Get stats
    public function getWorkflowStats(MonthlyPlan $plan): array { }
}
```

See `PHASE_2_IMPLEMENTATION_PLAN.md` for full code.

### Day 3: Update & Verify
```bash
# 1. Update services
# Edit app/Services/TaskSLAService.php
# Edit app/Services/TaskStateService.php
# Update controllers

# 2. Update routes
# Remove daily-plans, plan-tasks, operator-plans from routes/web.php

# 3. Run tests
php artisan test tests/Feature/TaskWorkflowTest.php
php artisan test tests/Feature/DataMigrationTest.php

# 4. Verify
php artisan route:list | grep daily-plans
# Should be: empty

# 5. Clear cache
php artisan cache:clear config:clear view:clear

# 6. Remove old files (next day after verification)
rm app/Models/PlanTask.php
rm app/Models/DailyPlan.php
rm app/Http/Controllers/PlanTaskController.php
# etc.
```

---

## 📋 CHECKLIST

### Before You Start
- [ ] Phase 1 deployed and stable (1 week)
- [ ] Production backup ready
- [ ] Testing environment ready
- [ ] Team notified
- [ ] Documentation reviewed

### During Implementation
- [ ] Data migration created
- [ ] Migration tested in dev
- [ ] TaskWorkflowEngine created
- [ ] Services updated
- [ ] Routes updated
- [ ] Tests created and passing
- [ ] Data integrity verified

### After Deployment
- [ ] Run full test suite
- [ ] Manual workflow testing
- [ ] Monitor logs
- [ ] Remove old files
- [ ] Update documentation
- [ ] Team training

---

## 🔄 DATA MIGRATION VERIFICATION

### Count Check
```bash
# Before
mysql> SELECT COUNT(*) FROM plan_tasks;
# e.g., 245

# After
mysql> SELECT COUNT(*) FROM tasks WHERE task_type = 'plan_task';
# Should be: 245
```

### Data Integrity
```bash
# Check for nulls
mysql> SELECT COUNT(*) FROM tasks WHERE state IS NULL;
# Should be: 0

mysql> SELECT COUNT(*) FROM tasks WHERE created_by_user_id IS NULL;
# Should be: 0
```

### Operators Migrated
```bash
# Count operator assignments
mysql> SELECT COUNT(*) FROM task_operators;
# Should match old plan_task_operators count
```

---

## 🧪 TESTING

### Run Data Migration Tests
```bash
php artisan test tests/Feature/DataMigrationTest.php -v
```

### Run Workflow Tests
```bash
php artisan test tests/Feature/TaskWorkflowEngineTest.php -v
```

### Full Test Suite
```bash
php artisan test

# Should show:
# ✓ 15 Phase 1 tests
# ✓ 12 Phase 2 tests (new)
# ✓ All passing
```

---

## 🚨 ROLLBACK (If Needed)

### Option 1: Migrate Down
```bash
php artisan migrate:rollback

# This will:
# 1. Delete tasks (migrated data)
# 2. Delete task_operators
# 3. Keep plan_tasks intact
```

### Option 2: Restore from Backup
```bash
# Restore entire database
mysql -u root MPFM < backup_phase2_20260211.sql

# Verify old data is back
mysql> SELECT COUNT(*) FROM plan_tasks;
```

### Option 3: Keep Both Systems
```bash
# Don't delete plan_tasks during Phase 2
# Run parallel: both systems operational
# Switch over gradually (feature flags)
```

---

## 🔧 KEY FILES TO CREATE/UPDATE

### New Files
1. **`database/migrations/2026_01_29_000000_migrate_plan_tasks_to_tasks.php`**
   - Data migration logic
   - ~100 lines
   - See PHASE_2_IMPLEMENTATION_PLAN.md

2. **`app/Domains/Task/Services/TaskWorkflowEngine.php`**
   - Orchestration service
   - ~250 lines
   - See PHASE_2_IMPLEMENTATION_PLAN.md

3. **`tests/Feature/DataMigrationTest.php`**
   - Migration verification tests
   - ~200 lines

### Files to Update
1. `app/Services/TaskSLAService.php` - Use new Task model
2. `app/Services/TaskStateService.php` - Use TaskStateMachine
3. `app/Http/Controllers/SurveillanceSupervisorController.php` - Update queries
4. `app/Http/Controllers/MPFMSupervisorController.php` - Use new services
5. `routes/web.php` - Remove old routes

### Files to Delete
1. `app/Models/PlanTask.php`
2. `app/Models/DailyPlan.php`
3. `app/Models/OperatorPlan.php`
4. `app/Http/Controllers/PlanTaskController.php`
5. `app/Http/Controllers/DailyPlanController.php`
6. `app/Http/Controllers/OperatorPlanController.php`
7. `resources/views/daily_plans/` (directory)
8. `resources/views/operator_plans/` (directory)

---

## 📊 EXPECTED CHANGES

### Database
```
Before:
├── plan_tasks (old, ~245 rows)
├── daily_plans (old)
└── operator_plans (old)

After:
├── plan_tasks (archive, unchanged)
├── daily_plans (archive, unchanged)
├── operator_plans (archive, unchanged)
└── tasks (new, ~245 rows with task_type='plan_task')
```

### Codebase
```
Removed:
- 3 controllers (~1,000 lines)
- 3 models (~500 lines)
- 3 view directories

Added:
- 1 service (~250 lines)
- 1 migration (~100 lines)
- 1 test file (~200 lines)

Updated:
- 5+ services (~200 lines total)
- routes/web.php (removals)
```

### Performance
```
Before: Multiple queries from different tables
After: Unified Task queries with proper indexes

Expected: 5-10% faster response times
```

---

## ⚙️ CONFIGURATION CHANGES

### Environment Variables
No new environment variables needed.

### Database Configuration
No changes needed (same MySQL instance).

### Service Provider Registration
Add to `config/app.php` providers (if using service container):
```php
App\Domains\Task\Services\TaskWorkflowEngine::class,
```

### Caching
Clear after migration:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📞 SUPPORT DURING PHASE 2

### Common Issues

**Issue:** Migration fails with "Duplicate entry"
```
Solution: Check if tasks already exist
mysql> SELECT COUNT(*) FROM tasks WHERE task_type = 'plan_task';
If > 0, need to clean up before re-running
```

**Issue:** Tests fail after migration
```
Solution: Verify data integrity first
Run: SELECT COUNT(*) checks (see VERIFICATION section)
If counts match, issue is with test setup
```

**Issue:** Services not found after update
```
Solution: Run composer dump-autoload
php artisan cache:clear
```

### Getting Help
1. Check `PHASE_2_IMPLEMENTATION_PLAN.md` for detailed code
2. Review test files for examples
3. Check git history for changes
4. Ask team lead

---

## ✅ SUCCESS METRICS

### Data
- ✅ All rows migrated accurately
- ✅ No data loss
- ✅ Relationships preserved
- ✅ Operators assigned correctly

### Code
- ✅ All tests passing
- ✅ No broken references
- ✅ Services working
- ✅ Routes functional

### System
- ✅ No performance regression
- ✅ SLA tracking working
- ✅ Audit trail complete
- ✅ No user-facing issues

---

## 🎯 PHASE 2 TIMELINE

```
Day 1: Data Migration (4 hours)
├── 1h: Backup
├── 1h: Create migration
├── 1h: Run & verify
└── 1h: Document

Day 2: TaskWorkflowEngine (4 hours)
├── 1h: Create service
├── 1h: Update existing services
├── 1h: Write tests
└── 1h: Debug

Day 3: Cleanup & Verify (3 hours)
├── 1h: Update routes
├── 1h: Run full tests
└── 1h: Verification + doc

Day 4: Remove Old Files (2 hours)
├── 1h: Delete deprecated files
└── 1h: Final verification

Total: ~13 hours of focused work
```

---

## 📚 DOCUMENTATION HIERARCHY

For Phase 2, read in this order:

1. **This file** (PHASE_2_QUICK_START.md) ← Start here
2. **PHASE_2_IMPLEMENTATION_PLAN.md** ← Full implementation details
3. **ENTERPRISE_REFACTORING_ROADMAP.md** ← Project context
4. **PHASE_1_IMPLEMENTATION_STATUS.md** ← Reference for Task model

---

## 🚀 NEXT STEPS

### Immediate (Today)
- [ ] Read this document
- [ ] Review PHASE_2_IMPLEMENTATION_PLAN.md
- [ ] Schedule Phase 2 kickoff

### Week Before Phase 2
- [ ] Create database backup plan
- [ ] Prepare testing environment
- [ ] Brief team on changes
- [ ] Schedule pair programming

### Day Before Phase 2
- [ ] Backup production database
- [ ] Verify staging environment
- [ ] Review migration code once more
- [ ] Have rollback plan ready

### Day 1 of Phase 2
- [ ] Create and run migration
- [ ] Verify data
- [ ] Run tests
- [ ] Document results

---

## 💡 TIPS & BEST PRACTICES

### Before Running Migration
- ✅ Always backup first
- ✅ Test in development
- ✅ Have rollback plan
- ✅ Notify team
- ✅ Schedule for low-traffic time

### During Migration
- ✅ Monitor logs
- ✅ Don't stop process midway
- ✅ Have DBA on standby
- ✅ Document everything
- ✅ Take screenshots of before/after

### After Migration
- ✅ Verify data completely
- ✅ Run full test suite
- ✅ Monitor system closely
- ✅ Keep old tables for 2 weeks
- ✅ Document lessons learned

---

## 🎓 LEARNING OUTCOMES

After Phase 2, you'll understand:
- ✅ How to safely migrate data
- ✅ Service consolidation patterns
- ✅ Batch operations
- ✅ Workflow orchestration
- ✅ DDD service layer design

---

## 📞 READY TO START?

**Prerequisites Met:**
- ✅ Phase 1 complete
- ✅ Phase 1 tests passing
- ✅ Phase 1 in production
- ✅ Plan documented

**Can Start Phase 2:**
- ✅ Yes! After 1 week Phase 1 stabilization

**Questions Before Starting:**
- Review `PHASE_2_IMPLEMENTATION_PLAN.md`
- Check migration code carefully
- Ensure backup plan is solid
- Team understands changes

---

**Status:** ✅ READY TO DEPLOY
**Next Action:** Schedule Phase 2 after Phase 1 stabilization
**Target Start:** February 10, 2026 (after 1 week Phase 1 verification)

