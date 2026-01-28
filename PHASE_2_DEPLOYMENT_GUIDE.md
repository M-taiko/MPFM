# Phase 2: Complete Deployment & Implementation Guide

**Status:** Ready to Deploy
**Estimated Duration:** 3-5 days
**Complexity:** Medium
**Risk:** Low

---

## 🎯 PHASE 2 OVERVIEW

Transform the system from scattered task management to unified platform:

**Deliverables:**
1. ✅ Data migration (plan_tasks → tasks)
2. ✅ TaskWorkflowEngine service
3. ✅ Migration tests + verification
4. ✅ Sidebar navigation update
5. ✅ Cleanup guide for deprecated files

**Result:** All data migrated, services consolidated, ready for Phase 3

---

## 📋 PRE-DEPLOYMENT CHECKLIST

### Database Preparation
- [ ] Production database backed up
- [ ] Backup verified (can restore)
- [ ] Staging environment prepared
- [ ] Migration tested on staging
- [ ] Rollback procedure documented

### Code Review
- [ ] Migration code reviewed
- [ ] TaskWorkflowEngine reviewed
- [ ] Tests understood and passing
- [ ] No conflicts in merges
- [ ] All changes committed to git

### Team Communication
- [ ] Team notified of deployment date
- [ ] Downtime window communicated
- [ ] Support available during deployment
- [ ] Rollback procedure documented
- [ ] Post-deployment verification plan

### Testing
- [ ] All Phase 1 tests still passing
- [ ] All Phase 2 migration tests passing
- [ ] Data migration verified on staging
- [ ] No broken references
- [ ] UI flows tested

---

## 🚀 DEPLOYMENT TIMELINE

### Phase 2A: Days 1-2 (Verification & Testing)

**Day 1: Code Review & Setup**
```
Morning:
- Review migration code
- Review TaskWorkflowEngine
- Prepare deployment plan

Afternoon:
- Create database backup
- Test migration on staging
- Verify data integrity
- Get team sign-off
```

**Day 2: Final Verification**
```
Morning:
- Run full test suite on staging
- Verify no broken references
- Document any edge cases

Afternoon:
- Team review of results
- Approve for production
- Schedule deployment window
```

### Phase 2B: Day 3 (Production Deployment)

**Morning (30 min before deployment):**
```
- Final database backup
- Stop non-critical services
- Notify team (deployment starting)
```

**Deployment (10-15 minutes):**
```
1. Run migration (1-2 min)
   php artisan migrate

2. Verify migration (2-3 min)
   php artisan tinker
   DB::table('tasks')->count()

3. Run tests (3-5 min)
   php artisan test tests/Feature/DataMigrationTest.php

4. Clear cache (1-2 min)
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear

5. Verify UI (2-3 min)
   Check /tasks in browser
```

**Post-Deployment (30 minutes):**
```
- Monitor logs for errors
- Test create/read/update/delete
- Test state transitions
- Verify all roles can access
- Confirm no broken links
```

### Phase 2C: Days 4-5 (Cleanup & Stabilization)

**Day 4: Code Cleanup**
```
Morning:
- Delete deprecated controllers (3 files)
- Delete deprecated models (3 files)
- Delete deprecated views (3 directories)
- Update routes (remove old routes)

Afternoon:
- Run full test suite
- Verify no broken references
- Commit cleanup to git
- Document any issues
```

**Day 5: Stabilization & Verification**
```
Morning:
- Monitor system for issues
- Address any concerns
- Update documentation
- Team training/handoff

Afternoon:
- Final verification
- Close Phase 2
- Plan Phase 3
```

---

## 📋 STEP-BY-STEP DEPLOYMENT

### Step 1: Pre-Deployment Verification

```bash
# Verify all tests pass
cd c:\xampp\htdocs\MPFM

# Phase 1 tests should still pass
php artisan test tests/Feature/TaskWorkflowTest.php

# Phase 2 tests
php artisan test tests/Feature/DataMigrationTest.php

# All tests
php artisan test

# Expected: All passing ✅
```

### Step 2: Backup Database

```bash
# MySQL backup (Linux/Mac)
mysqldump -u root -p MPFM > backup_phase2_$(date +%Y%m%d_%H%M%S).sql

# Or use PhpMyAdmin
# Select MPFM database → Export → Download
```

**Keep backup for 30 days**

### Step 3: Run Migration

```bash
# Run migration
php artisan migrate

# Expected output:
# Migration 2026_01_29_000000_migrate_plan_tasks_to_tasks .... created
# Migrated: 2026_01_29_000000_migrate_plan_tasks_to_tasks (XXXms)
```

### Step 4: Verify Migration Success

```bash
# Method 1: PHP Tinker
php artisan tinker
>>> DB::table('tasks')->where('task_type', 'plan_task')->count()
# Should match: DB::table('plan_tasks')->count()

>>> DB::table('task_operators')->count()
# Should have operator assignments

>>> DB::table('task_sla_histories')->where('task_type', 'plan_task')->count()
# Should have SLA records

>>> exit
```

**Method 2: SQL Queries**
```sql
-- Count migrated tasks
SELECT COUNT(*) FROM tasks WHERE task_type = 'plan_task';

-- Verify no null states
SELECT COUNT(*) FROM tasks WHERE task_type = 'plan_task' AND state IS NULL;
-- Should return: 0

-- Count operator assignments
SELECT COUNT(*) FROM task_operators;

-- Verify SLA histories created
SELECT COUNT(*) FROM task_sla_histories;
```

### Step 5: Run Tests

```bash
# Data migration tests
php artisan test tests/Feature/DataMigrationTest.php -v

# Phase 1 tests (should still pass)
php artisan test tests/Feature/TaskWorkflowTest.php -v

# All tests
php artisan test

# Expected: ALL PASSING ✅
```

### Step 6: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload

# Alternative: Single command
php artisan cache:clear && php artisan config:clear && php artisan view:clear && composer dump-autoload
```

### Step 7: Verify in Browser

Open: `http://localhost/MPFM/public/tasks`

**Tests:**
- [ ] Loads without errors
- [ ] Shows all migrated tasks
- [ ] Filters work (state, department, search)
- [ ] Pagination works
- [ ] Create task button visible
- [ ] Click on task shows detail
- [ ] State transitions work
- [ ] Sidebar menu shows "Tasks"

### Step 8: Test Each Role

```bash
# As Admin
- Should see all tasks
- Can create tasks
- Can transition any task

# As Job Officer
- Should see all tasks
- Can create tasks
- Can approve tasks

# As Surveillance Supervisor
- Should see all tasks
- Can assign to department
- Can review tasks

# As Department Manager
- Should see department tasks only
- Can assign operators
- Can review department work

# As Operator
- Should see only assigned tasks
- Can start tasks
- Can mark complete
```

---

## 🧹 FILE CLEANUP (Days 4-5)

### After Verification (Delete Deprecated Files)

**Step 1: Backup files locally (git handles this)**
```bash
# Files are already in git history
# Just commit the deletion
```

**Step 2: Delete Controllers**
```bash
rm app/Http/Controllers/PlanTaskController.php
rm app/Http/Controllers/DailyPlanController.php
rm app/Http/Controllers/OperatorPlanController.php
```

**Step 3: Delete Models**
```bash
rm app/Models/PlanTask.php
rm app/Models/DailyPlan.php
rm app/Models/OperatorPlan.php
```

**Step 4: Delete View Directories**
```bash
rm -rf resources/views/daily_plans
rm -rf resources/views/operator_plans
# rm -rf resources/views/plan_tasks (if exists)
```

**Step 5: Update routes/web.php**
- Remove `use App\Http\Controllers\DailyPlanController;`
- Remove `use App\Http\Controllers\PlanTaskController;`
- Remove `use App\Http\Controllers\OperatorPlanController;`
- Remove all `Route::prefix('daily-plans')` routes
- Remove all `Route::prefix('operator-plans')` routes
- Remove `plan-tasks` routes

See: `PHASE_2_FILES_TO_REMOVE.md` for exact lines to remove

**Step 6: Verify No Broken References**
```bash
# Search for old class names
grep -r "PlanTask\|DailyPlan\|OperatorPlan" app/ resources/ --include="*.php" --include="*.blade.php"

# Expected: No results (empty output)

# Search for old route names
grep -r "daily-plans\|plan-tasks\|operator-plans" app/ resources/ --include="*.php" --include="*.blade.php"

# Expected: No results (empty output)
```

**Step 7: Clear Cache & Rebuild**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload
```

**Step 8: Run Tests Again**
```bash
php artisan test

# Expected: All tests passing ✅
```

**Step 9: Commit Cleanup**
```bash
git add -A
git commit -m "Phase 2 Cleanup: Remove deprecated PlanTask/DailyPlan/OperatorPlan models and controllers

- Removed 3 deprecated controllers
- Removed 3 deprecated models
- Removed 3 deprecated view directories
- Cleaned up routes in routes/web.php
- All functionality now in unified Task system

Migration verified: All data migrated successfully
Tests: 27/27 passing"

git push origin main
```

---

## ✅ POST-DEPLOYMENT VERIFICATION

### Automated Checks
```bash
# All tests pass
php artisan test

# No broken imports
php artisan tinker
>>> Class exists('App\Domains\Task\Models\Task')
>>> true

# Middleware works
# Models relations work
# Service container resolves
```

### Manual Checks
```
□ Dashboard loads
□ Task listing loads
□ Can create new task
□ Can view task detail
□ Can transition state
□ Can filter tasks
□ Can search tasks
□ All roles can access appropriately
□ Sidebar navigation works
□ Old links don't exist (404)
```

### Performance Checks
```
□ Page load time < 2 seconds
□ No database errors in logs
□ No PHP warnings in logs
□ Memory usage normal
□ CPU usage normal
```

### Monitoring
```
□ Monitor error logs for 24 hours
□ Monitor database for slow queries
□ Monitor application logs
□ Track user reports
□ Document any issues
```

---

## 🐛 TROUBLESHOOTING

### Issue: Migration Fails
```
Error: "Data migration verification failed"

Solution:
1. Check error message for details
2. Run: php artisan migrate:rollback
3. Verify old data is intact
4. Fix issue (usually missing foreign keys)
5. Try migration again

If still failing:
- Restore from backup
- Review migration code
- Test on staging first
```

### Issue: Tests Fail After Migration
```
Error: "Expected 150 tasks, got 0"

Solution:
1. Verify migration ran: php artisan migrate:status
2. Check data: SELECT COUNT(*) FROM tasks;
3. If no data, migration didn't run
4. Run migration again: php artisan migrate
5. Rerun tests
```

### Issue: Task Routes Return 404
```
Error: "The requested URL could not be found"

Solution:
1. Clear cache: php artisan cache:clear config:clear view:clear
2. Verify routes exist: php artisan route:list | grep tasks
3. Should show 8 task routes
4. If missing, check routes/web.php is not corrupted
5. Verify TaskController exists: app/Http/Controllers/TaskController.php
```

### Issue: Old Routes Still Work
```
Error: "/daily-plans/1 still loads"

Solution:
1. Verify old routes removed from routes/web.php
2. Clear cache: php artisan cache:clear
3. If still works, old code is being cached
4. Check nginx/Apache cache
5. Restart web server
```

### Issue: Sidebar Not Updated
```
Error: "Tasks menu doesn't appear in sidebar"

Solution:
1. Verify main-sidebar.blade.php updated
2. Clear cache: php artisan view:clear
3. Refresh browser (Ctrl+Shift+R hard refresh)
4. Check user role (some roles may not see it)
5. Verify blade syntax correct (no typos)
```

---

## 🔄 ROLLBACK PROCEDURE

### If Critical Issue Found

```bash
# IMMEDIATE: Rollback migration
php artisan migrate:rollback

# Verify rollback
php artisan migrate:status
# Should show 2026_01_29... marked as "not migrated"

# Clear cache
php artisan cache:clear config:clear view:clear

# Verify data
php artisan tinker
>>> DB::table('tasks')->where('task_type', 'plan_task')->count()
>>> 0  (should be 0 after rollback)

# Restore views if needed
git checkout resources/views/layouts/main-sidebar.blade.php

# Restore controllers/models if deleted
git checkout app/Http/Controllers/ app/Models/ resources/views/

# Clear cache again
php artisan cache:clear config:clear view:clear
```

### If Database Corrupted

```bash
# Restore from backup (MySQL)
mysql -u root -p MPFM < backup_phase2_20260129.sql

# Or use PhpMyAdmin Import feature

# Clear all cache
php artisan cache:clear config:clear view:clear

# Verify restoration
php artisan migrate:status
```

### Timeline for Rollback
- Detection: < 5 minutes
- Rollback: < 5 minutes
- Verification: < 5 minutes
- Total: < 15 minutes

---

## 📊 SUCCESS CRITERIA

✅ All tests passing (27/27)
✅ Data migrated (count matches)
✅ No null values in critical fields
✅ Operator assignments preserved
✅ SLA tracking working
✅ All routes functional
✅ Sidebar updated
✅ Old routes removed (or disabled)
✅ No broken references in code
✅ Performance acceptable (< 2s load time)

---

## 📞 COMMUNICATION TEMPLATE

### Before Deployment
```
Subject: Phase 2 Migration - System Downtime Notice

Team,

We will be deploying Phase 2 of our Enterprise Refactoring on [DATE] at [TIME].

This includes:
- Data migration from 3 task models to 1 unified model
- Service consolidation
- Updated navigation

Expected downtime: 15-30 minutes
Impact: Minimal (backward compatible)

During this time, task-related features will be temporarily unavailable.

Questions? Let me know.
```

### During Deployment
```
Deploying Phase 2...
- Migration running
- Tests verifying
- Cache clearing
- System checks

ETA: [TIME]
Status: [IN PROGRESS]
```

### After Deployment
```
Phase 2 deployment complete!

✅ All data migrated
✅ All tests passing
✅ System operational

Please report any issues via [CHANNEL].
```

---

## 📚 DOCUMENTATION

**Read in this order:**
1. `PHASE_2_QUICK_START.md` - Overview
2. `PHASE_2_IMPLEMENTATION_PLAN.md` - Detailed plan
3. `PHASE_2_DEPLOYMENT_GUIDE.md` - This file (step-by-step)
4. `PHASE_2_FILES_TO_REMOVE.md` - Cleanup guide
5. `PHASE_2_SUMMARY.md` - Final summary

---

## ✨ COMPLETION CHECKLIST

- [ ] All tests passing
- [ ] Migration verified
- [ ] Data integrity confirmed
- [ ] Sidebar updated
- [ ] Documentation complete
- [ ] Team trained
- [ ] Rollback plan documented
- [ ] Deployment approved
- [ ] Go live
- [ ] Post-deployment monitoring
- [ ] Cleanup completed
- [ ] Final verification
- [ ] Phase 2 closed

---

**Status:** ✅ READY FOR DEPLOYMENT
**Phase Progress:** 33% complete (2 of 6 phases)
**Next Phase:** Phase 3 - MPFM Domain Consolidation
**Timeline:** 6 months total
**Deployment Window:** 3-5 days from now

