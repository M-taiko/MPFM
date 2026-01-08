# MPFM System Refactoring Plan
**Date:** 2026-01-07
**Objective:** Transform MPFM system to execute daily operational plans from plan.txt

## Current State Analysis

### Existing Infrastructure
✅ Models: User, WellTest, DailyPlan, PlanTask, MPFMTest, OperatorPlan, OilField, Tower, OilString
✅ Controllers: VxLogController, WellTestController, DailyPlanController, Operator/SupervisorDashboard
✅ Services: MPFMCalculationService, LogParserService, ChartService, MetricsService
✅ Authentication & Roles: Spatie Permission package implemented

### Issues to Fix
❌ Metrics key mismatch (oil_rate vs oil_rate_avg)
❌ Undefined index errors in views
❌ Duplicate/unused code in controllers
❌ Inconsistent UI across pages
❌ No notification system for task completion
❌ No auto-fill from plan data

## Implementation Phases

### PHASE 1: Database Schema Optimization
**Goal:** Ensure all necessary relationships and fields exist

#### 1.1 plan.txt Parser
- Create service to parse plan.txt Excel format
- Extract: SNO, WELL, Tower, Flow route, Reservoir Layer, MPFM Type, Required WHFP, Date, Operator
- Map to database structure

#### 1.2 Database Tables Review
```
daily_plans:
- id, plan_name, plan_date, status (draft/active/completed/cancelled)
- created_by, approved_by, approved_at
- monthly_target_kpi, total_tests, notes

plan_tasks:
- id, daily_plan_id, sno (from plan.txt)
- well_name, tower_id, oil_string_id
- flow_route, reservoir_layer, mpfm_type
- required_whfp, test_date
- assigned_to (operator user_id) - ALLOW MULTIPLE
- status (pending/in_progress/completed/rejected)
- priority, category, sub_category
- expected_oil_rate, expected_gas_rate, expected_water_cut
- completed_at, completion_notes, mpfm_test_id

well_tests:
- ... (existing structure is good)
- Add: plan_task_id (link back to plan)

mpfm_tests:
- ... (existing structure is good)

notifications:
- Laravel default notifications table
```

#### 1.3 Migration Files Needed
- Add `plan_task_id` to well_tests
- Add multiple operator assignment capability (pivot table or JSON)
- Add notification fields to users table
- Add expected values to plan_tasks

### PHASE 2: Daily Plan Workflow

#### 2.1 Production Supervisor Creates Plan
**Controller:** DailyPlanController
**Routes:**
- GET /daily-plans/create
- POST /daily-plans
- POST /daily-plans/import-from-txt (parse plan.txt)

**Features:**
- Manual plan creation form
- Upload plan.txt and auto-import
- Assign multiple operators to each task
- Set priorities and expected values
- Save as draft before activation

#### 2.2 MPFM Supervisor Reviews/Approves Plan
**Routes:**
- GET /supervisor/dashboard
- GET /daily-plans/{id}/review
- POST /daily-plans/{id}/approve
- POST /daily-plans/{id}/reject
- POST /daily-plans/{id}/edit

**Features:**
- View all plans (draft/active/completed)
- Edit plan tasks
- Reassign operators
- Approve/reject plans
- Monitor test progress
- Real-time status dashboard

### PHASE 3: Operator Workflow

#### 3.1 Operator Dashboard
**Controller:** OperatorDashboardController
**Routes:**
- GET /operator/dashboard
- GET /operator/tasks (today's tasks)
- GET /operator/tasks/history (previous assignments)
- GET /operator/tasks/{task}

**Features:**
- Show today's assigned tests
- Show previous test history with dates
- Filter by status (pending/in-progress/completed)
- Quick-start test button
- Calendar view of assignments

#### 3.2 Auto-Fill Upload Form
**When operator selects a task:**
- Pre-fill client (ADNOC by default)
- Pre-fill field (from oil_fields.name)
- Pre-fill well (from plan_task.well_name)
- Pre-fill tower (from towers.name)
- Pre-fill string (from oil_strings.name)
- Pre-fill operator name (auth()->user()->name)
- Pre-fill test_date (from plan_task.test_date)
- Pre-fill expected values (oil_rate, gas_rate, water_cut)
- Pre-fill MPFM device type

#### 3.3 Upload & Process Log File
**Controller:** VxLogController (refactored)
**Route:** POST /process-log?task_id={task_id}

**Features:**
- Accept log file upload
- Dynamic column detection
- Extract ALL available metrics
- Display results BEFORE saving
- Allow operator to review and confirm
- Mark task as completed on save

### PHASE 4: Log Processing Unification

#### 4.1 Refactor VxLogController
**Current Issues:**
- Mix of MPFMCalculationService and MetricsService
- Inconsistent metric keys
- Duplicate code

**Solution:**
- Use MPFMCalculationService for ALL calculations
- Standardize metric keys: oil_rate, gas_rate, gor, bsw, wlr, gvf
- Create single calculateStandardizedMetrics() method
- Reuse across VxLogController and WellTestController

#### 4.2 Support Dynamic Columns
**MPFMParsingService Enhancement:**
```php
// Detect any column that matches patterns:
- Oil: qo*, oil*, OILC, Oil_Rate
- Water: qw*, water*, WATC, Water_Rate
- Gas: qg*, gas*, GASC, Gas_Rate
- GOR: gor*, GOR*, Gas-Oil Ratio
- Pressure: pressure*, WHP*, pres*
- Temperature: temp*, temperature*, TL*
- H2S: h2s*, H2S*
```

#### 4.3 Unified Calculation Flow
```
1. Upload log file
2. Parse with dynamic column detection
3. Extract rows and identify columns
4. Calculate standardized metrics
5. Store raw log + processed data + metrics
6. Display results in modern UI
7. Allow operator to confirm before save
8. Link to plan_task
9. Update task status
10. Send notification
```

### PHASE 5: Notification System

#### 5.1 Database Notifications (NO websockets)
**Laravel Notifications:**
```php
php artisan make:notification TaskCompletedNotification
```

**Flow:**
1. Operator completes test
2. System creates notification record
3. Notification sent to supervisor who assigned task
4. Supervisor sees notification bell icon
5. Click to view details
6. Mark as read

#### 5.2 Notification Types
- Task Completed
- Task Rejected
- Plan Approved
- Plan Modified
- Test Results Available

### PHASE 6: UI Modernization

#### 6.1 Unified Design System
**Colors:**
- Primary: #3b82f6 (blue)
- Success: #10b981 (green)
- Warning: #f59e0b (orange)
- Danger: #ef4444 (red)
- Info: #06b6d4 (cyan)

**Components:**
- Card-based layouts
- Modern gradients
- Consistent spacing
- Responsive grids
- Interactive charts (Chart.js)
- Data tables (DataTables)

#### 6.2 Pages to Modernize
1. Upload page (/upload)
2. Process results page (/process-log)
3. Well test show page (/well-tests/{id})
4. Compare tests page (/well-tests/compare)
5. Operator dashboard
6. Supervisor dashboard
7. Daily plan pages

#### 6.3 Sidebar Structure
**Remove:**
- Duplicate menu items
- Unused features

**Keep:**
- Dashboard
- Daily Plans (Supervisor)
- My Tasks (Operator)
- Well Tests
- Upload Log
- Reports
- Settings (Roles/Permissions)

### PHASE 7: Comparison Features

#### 7.1 Compare Same Well Different Dates
- Select well
- Choose two different test dates
- Side-by-side comparison
- Show difference indicators
- Charts overlay

#### 7.2 Compare Different Wells
- Select two wells
- Choose dates
- Side-by-side metrics
- Performance analysis

### PHASE 8: Code Quality & Documentation

#### 8.1 Remove Dead Code
- Unused controllers
- Duplicate methods
- Old migration files
- Unused views

#### 8.2 Consistent CRUD Operations
- Use Resource Controllers
- Validation via Form Requests
- Service layer for business logic
- Repository pattern for complex queries

#### 8.3 Code Documentation
- PHPDoc blocks
- Method comments
- Inline explanations
- README updates

## Implementation Order

### Week 1: Core Infrastructure
1. ✅ Fix metric key mismatches
2. Create plan.txt parser service
3. Update database migrations
4. Refactor VxLogController

### Week 2: Daily Plan Workflow
1. Production Supervisor plan creation
2. Plan.txt import feature
3. MPFM Supervisor approval workflow
4. Multi-operator assignment

### Week 3: Operator Features
1. Operator dashboard
2. Task listing with history
3. Auto-fill upload form
4. Test-plan linking

### Week 4: Processing & Notifications
1. Unified log processing
2. Database notifications
3. Review-before-save workflow
4. Task status updates

### Week 5: UI Modernization
1. Design system implementation
2. Modernize all views
3. Chart improvements
4. Responsive design

### Week 6: Comparison & Polish
1. Comparison features
2. Remove dead code
3. Testing & bug fixes
4. Documentation

## Success Criteria

✅ Plan.txt can be imported and parsed
✅ Operators see only their assigned tasks
✅ Upload form auto-fills from plan data
✅ All log formats are parsed correctly
✅ Metrics are calculated consistently
✅ Notifications work without websockets
✅ UI is modern and consistent
✅ Comparison features work
✅ No undefined index errors
✅ Code is clean and documented
