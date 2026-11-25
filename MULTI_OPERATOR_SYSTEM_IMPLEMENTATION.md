# MPFM Multi-Operator Workflow System - Implementation Guide
## Enterprise Production Testing Management - Oil Field Level

**Date**: 2026-01-06
**Status**: ✅ **CORE ARCHITECTURE IMPLEMENTED**
**System Type**: Multi-Operator Daily Plan Execution System

---

## 🎯 System Overview

This document describes the implementation of a real-world multi-operator workflow for MPFM (Multi-Phase Flow Meter) production testing, designed for enterprise oil & gas operations at Oil Field level.

### Core Concept

**DAILY PLAN** → Contains multiple **TASKS** → Each **TASK** assigned to ONE **OPERATOR** → Executes task → Creates **MPFM TEST**

---

## 👥 User Roles

### 1. Supervisor
- Creates daily plans
- Assigns multiple tasks to different operators
- Monitors real-time execution
- Reviews operator performance
- Compares results across operators

### 2. Operator
- Views ONLY assigned tasks
- Executes tasks in sequence
- Uploads MPFM log files
- Completes tasks with test data
- Cannot see other operators' tasks

---

## 📊 Database Architecture

### New Tables Created

#### 1. `daily_plans`
Main plan container for a specific date.

```sql
- id
- created_by (supervisor)
- plan_name
- plan_date
- oil_field_id
- tower_id
- status (draft/active/in_progress/completed/cancelled)
- plan_notes
- total_tasks
- completed_tasks
- pending_tasks
- in_progress_tasks
- started_at
- completed_at
- timestamps
```

**Key Features**:
- One plan per date per supervisor
- Automatic task counting
- Status auto-updates based on task completion
- Completion tracking

#### 2. `plan_tasks`
Individual test tasks assigned to operators.

```sql
- id
- daily_plan_id (FK to daily_plans)
- assigned_to (operator user_id)
- task_order (execution sequence)
- oil_string_id (what to test)
- mpfm_device
- device_type
- expected_oil_rate
- expected_gas_rate
- expected_water_cut
- expected_pressure
- expected_temperature
- task_instructions
- priority (low/normal/high/urgent)
- status (pending/in_progress/completed/failed/cancelled)
- started_at
- completed_at
- duration_minutes
- operator_notes
- completion_notes
- has_issues
- issues_description
- timestamps
```

**Key Features**:
- Each task = ONE MPFM test
- Expected values for comparison
- Priority management
- Issue tracking
- Duration tracking

#### 3. `mpfm_tests` (Updated)
Added foreign key to plan_tasks:

```sql
- plan_task_id (NEW - FK to plan_tasks)
```

**Relationship**: `PlanTask hasOne MPFMTest`

---

## 🔗 Model Relationships

### DailyPlan
```php
// Relationships
belongsTo(User, 'created_by')    // Supervisor
belongsTo(OilField)
belongsTo(Tower)
hasMany(PlanTask)

// Helper Methods
updateTaskCounts()               // Auto-update counters
getCompletionPercentageAttribute()
isCompleted()
canBeDeleted()
autoUpdateStatus()               // Auto-set status based on tasks
getAssignedOperators()           // Get unique operators
```

### PlanTask
```php
// Relationships
belongsTo(DailyPlan)
belongsTo(User, 'assigned_to')   // Operator
belongsTo(OilString)
hasOne(MPFMTest)

// Helper Methods
start()                          // Mark as in_progress
complete()                       // Mark as completed + duration
markAsFailed()
canBeStarted()
hasTest()
getDeviation()                   // Compare actual vs expected
getTaskIdentifierAttribute()     // e.g., "20260106-T001"
getLocationPathAttribute()       // "Field / Tower / String"
```

### MPFMTest
```php
// New Relationship
belongsTo(PlanTask)              // Task that created this test
```

---

## 🔧 Service Layer

### `DailyPlanService`
Handles all business logic for plan and task management.

**Key Methods**:

#### Plan Management
```php
createPlan(array $planData, array $tasks)
updatePlan(DailyPlan $plan, array $data)
activatePlan(DailyPlan $plan)
cancelPlan(DailyPlan $plan, string $reason)
clonePlan(DailyPlan $original, Carbon $newDate)
```

#### Task Management
```php
addTasksToPlan(DailyPlan $plan, array $tasks)
startTask(PlanTask $task, User $operator)
completeTask(PlanTask $task, array $completionData)
reorderTasks(DailyPlan $plan, array $taskIdOrder)
```

#### Operator Views
```php
getOperatorTasksForDate(User $operator, Carbon $date)
getOperatorPendingTasks(User $operator)
getOperatorTasksGroupedByPlan(User $operator, $start, $end)
```

#### Analytics & Reporting
```php
getPlanStatistics(DailyPlan $plan)
getSupervisorPlanOverview(User $supervisor, $start, $end)
getOperatorPerformance(User $operator, $start, $end)
```

---

## 🔐 Role-Based Access Control

### Supervisor Permissions
```php
- daily_plan.create
- daily_plan.view_all
- daily_plan.edit
- daily_plan.delete
- daily_plan.activate
- daily_plan.cancel
- plan_task.create
- plan_task.assign
- plan_task.edit
- plan_task.delete
- plan_task.view_all
- report.operator_performance
- report.plan_comparison
```

### Operator Permissions
```php
- plan_task.view_assigned      // Only THEIR tasks
- plan_task.execute
- plan_task.complete
- mpfm_test.create             // From task execution
- mpfm_test.view_own
```

---

## 📱 User Interfaces

### Supervisor Dashboard

**Main Features**:
1. **Plan Calendar**
   - Month view with plan dates highlighted
   - Click date → View/Create plan

2. **Plan Management**
   - Create new plan
   - List of plans (filter by date, status)
   - Plan details with task list

3. **Task Assignment**
   - Add multiple tasks to plan
   - Assign each task to different operator
   - Set expected values
   - Reorder tasks

4. **Real-Time Monitoring**
   - See task status updates
   - View operator progress
   - Identify issues

5. **Analytics**
   - Operator performance comparison
   - Plan completion rates
   - Average task duration
   - Issue tracking

### Operator Dashboard

**Main Features**:
1. **My Tasks Today**
   - Tasks for current date
   - Grouped by plan
   - Status indicators

2. **Pending Tasks**
   - All pending tasks
   - Prioritized list
   - Filter by date

3. **Task Execution**
   - Click task → Auto-fill form:
     - Plan info
     - Well/Tower/String
     - Expected values
     - Device info
   - Upload MPFM log file
   - Preview parsed data
   - Add operator notes
   - Complete task

4. **Task History**
   - Completed tasks
   - Test results
   - Performance metrics

---

## 🔄 Workflow Diagrams

### Supervisor Workflow
```
1. Create Daily Plan
   ↓
2. Set plan date, field, tower
   ↓
3. Add multiple tasks
   ↓
4. For each task:
   - Select operator
   - Select string
   - Set MPFM device
   - Set expected values
   - Add instructions
   ↓
5. Activate plan
   ↓
6. Monitor execution in real-time
   ↓
7. Review completed tests
```

### Operator Workflow
```
1. Login → View assigned tasks
   ↓
2. See tasks for today
   ↓
3. Click task to execute
   ↓
4. System auto-fills:
   - Plan details
   - Location (field/tower/string)
   - Expected values
   - Device info
   ↓
5. Operator uploads MPFM log file
   ↓
6. System parses log dynamically
   ↓
7. System calculates KPIs
   ↓
8. Operator previews results
   ↓
9. Operator adds notes
   ↓
10. Operator completes task
    ↓
11. MPFM Test created
    ↓
12. Task marked as completed
    ↓
13. Plan progress updated
```

---

## 📊 MPFM Log File Processing

### Dynamic Parser Integration
The system uses the existing `MPFMParsingService` and `MPFMPreviewService` with enhancements:

**Task Execution Flow**:
1. Operator selects task
2. Form pre-filled from task data
3. Operator uploads log file
4. Parser detects columns dynamically
5. System calculates KPIs
6. Preview displayed with:
   - Task expected values
   - Actual values from log
   - Deviation percentage
   - Quality report
7. Operator confirms
8. Test saved + Task completed

**Deviation Calculation**:
```php
deviation = ((actual - expected) / expected) * 100

Example:
Expected Oil Rate: 1000 STB/d
Actual Oil Rate: 950 STB/d
Deviation: -5%
```

---

## 📈 Analytics & Comparisons

### Plan Analytics
```php
- Total tasks
- Completion rate
- Average duration per task
- Tasks with issues
- Operator distribution
- Field/Tower distribution
```

### Operator Performance
```php
- Total tasks assigned
- Completion rate
- Average task duration
- Tasks with issues
- Accuracy (deviation from expected)
- Productivity trends
```

### Comparison Features
```php
- Test vs Test (same string, different dates)
- Operator vs Operator (same task type)
- Plan vs Plan (compare daily execution)
- Expected vs Actual (deviation analysis)
```

---

## 🚀 Implementation Steps

### Phase 1: Database & Models ✅
- [x] Create migrations
- [x] Create DailyPlan model
- [x] Create PlanTask model
- [x] Update MPFMTest model
- [x] Define relationships

### Phase 2: Service Layer ✅
- [x] Create DailyPlanService
- [x] Implement plan CRUD
- [x] Implement task management
- [x] Implement operator views
- [x] Implement analytics

### Phase 3: Controllers (NEXT)
- [ ] DailyPlanController
- [ ] PlanTaskController
- [ ] Operator Dashboard Controller
- [ ] Supervisor Dashboard Controller

### Phase 4: Views
- [ ] Supervisor Plan Management UI
- [ ] Task Assignment UI
- [ ] Operator Dashboard UI
- [ ] Task Execution Form
- [ ] Analytics Dashboard

### Phase 5: Integration
- [ ] Integrate with MPFMParsingService
- [ ] Integrate with MPFMPreviewService
- [ ] Implement deviation calculations
- [ ] Implement real-time updates

### Phase 6: Policies & Authorization
- [ ] DailyPlanPolicy
- [ ] PlanTaskPolicy
- [ ] Role-based permissions
- [ ] Data isolation (operators see only their tasks)

### Phase 7: Testing & Optimization
- [ ] Unit tests
- [ ] Integration tests
- [ ] Performance optimization
- [ ] Security audit

---

## 📝 API Endpoints (Planned)

### Supervisor Endpoints
```
GET    /daily-plans                       # List all plans
POST   /daily-plans                       # Create plan
GET    /daily-plans/{id}                  # View plan details
PUT    /daily-plans/{id}                  # Update plan
DELETE /daily-plans/{id}                  # Delete plan
POST   /daily-plans/{id}/activate         # Activate plan
POST   /daily-plans/{id}/cancel           # Cancel plan
POST   /daily-plans/{id}/clone            # Clone to new date

POST   /daily-plans/{id}/tasks            # Add tasks to plan
PUT    /plan-tasks/{id}                   # Update task
DELETE /plan-tasks/{id}                   # Delete task
POST   /plan-tasks/reorder                # Reorder tasks

GET    /reports/supervisor                # Supervisor dashboard
GET    /reports/operator-performance      # Operator metrics
GET    /reports/plan-comparison           # Plan comparison
```

### Operator Endpoints
```
GET    /operator/tasks/today              # Today's tasks
GET    /operator/tasks/pending            # All pending tasks
GET    /operator/tasks/{id}               # Task details
POST   /operator/tasks/{id}/start         # Start task
POST   /operator/tasks/{id}/complete      # Complete task
POST   /operator/tasks/{id}/notes         # Add notes

GET    /operator/dashboard                # Operator dashboard
GET    /operator/performance              # My performance
GET    /operator/history                  # Task history
```

---

## 🎨 UI Components

### Components Needed

#### Supervisor Components
1. **PlanCalendar** - Monthly calendar with plan indicators
2. **PlanForm** - Create/Edit plan form
3. **TaskBuilder** - Add multiple tasks with operator assignment
4. **TaskList** - Sortable task list with drag-drop reordering
5. **PlanMonitor** - Real-time task status dashboard
6. **OperatorPerformanceChart** - Comparison charts
7. **DeviationHeatmap** - Visual deviation analysis

#### Operator Components
1. **TaskCard** - Individual task card with status
2. **TaskExecutionForm** - Pre-filled form for task execution
3. **LogUploadWidget** - File upload with preview
4. **DeviationIndicator** - Show expected vs actual
5. **TaskTimeline** - Task history timeline
6. **PerformanceMetrics** - Personal performance dashboard

---

## 🔍 Search & Filtering

### Supervisor Filters
```php
- Date range
- Plan status
- Oil field
- Tower
- Operator
- Completion percentage
- Has issues
```

### Operator Filters
```php
- Date range
- Status (pending/in_progress/completed)
- Priority
- Has issues
```

---

## 📊 Reports

### Report Types

1. **Daily Plan Report**
   - Plan details
   - All tasks with status
   - Operator assignments
   - Completion metrics
   - Issues summary

2. **Operator Performance Report**
   - Tasks completed
   - Average duration
   - Accuracy (deviation)
   - Issues encountered
   - Trend analysis

3. **Field Production Report**
   - All tests by field
   - Production trends
   - String-level analysis
   - Deviation summary

4. **Comparison Report**
   - Multi-operator comparison
   - Multi-plan comparison
   - Expected vs Actual analysis
   - Performance benchmarking

---

## 🔔 Notifications

### Supervisor Notifications
- Task completed by operator
- Task failed or has issues
- Plan fully completed
- Deviation exceeds threshold

### Operator Notifications
- New task assigned
- Task priority changed
- Plan activated
- Plan cancelled

---

## 🛡️ Security Considerations

### Data Isolation
```php
// Operators can ONLY see their assigned tasks
public function scopeAccessibleByOperator($query, User $operator)
{
    return $query->assignedTo($operator->id);
}

// Supervisors can only see their created plans
public function scopeAccessibleBySupervisor($query, User $supervisor)
{
    return $query->createdBy($supervisor->id);
}
```

### Authorization
- Use Laravel Policies for all CRUD operations
- Middleware for role-based access
- Row-level security for tasks
- Audit logging for all actions

---

## 📱 Mobile Considerations

### Operator Mobile App (Future)
- View assigned tasks
- Start/Complete tasks on mobile
- Upload photos with test
- Offline mode support
- Push notifications

---

## 🧪 Testing Strategy

### Unit Tests
- Model relationships
- Service methods
- Calculation accuracy
- Deviation formulas

### Integration Tests
- Complete workflow (plan → task → test)
- Multi-operator scenarios
- Permission checks
- Data isolation

### E2E Tests
- Supervisor creates plan
- Operator executes task
- Test creation
- Analytics generation

---

## 📈 Performance Optimization

### Database Indexes
- Composite indexes on common queries
- Index on foreign keys
- Index on status columns
- Index on date columns

### Caching Strategy
```php
- Cache operator pending tasks (5 min)
- Cache plan statistics (1 min)
- Cache operator performance (1 hour)
- Invalidate on task completion
```

### Query Optimization
- Eager load relationships
- Use select() to limit columns
- Pagination on large lists
- Database views for complex queries

---

## 🎯 Success Criteria

The system is successful when:

✅ Supervisor can create multi-operator plans
✅ Each operator sees ONLY their tasks
✅ Task execution auto-fills from plan
✅ MPFM logs upload and parse correctly
✅ Deviation calculations are accurate
✅ Task completion updates plan status
✅ Performance metrics are actionable
✅ No data leakage between operators
✅ System handles 100+ tasks/day
✅ Mobile-ready interface

---

## 📚 Next Steps

### Immediate (Phase 3-4)
1. Create DailyPlanController
2. Create PlanTaskController
3. Create Supervisor Dashboard views
4. Create Operator Dashboard views
5. Implement task execution form

### Short-term (Phase 5-6)
1. Integrate MPFM log processing
2. Implement deviation analysis
3. Create policies
4. Implement real-time updates

### Long-term (Phase 7+)
1. Mobile app
2. Advanced analytics
3. AI-powered anomaly detection
4. Predictive maintenance
5. Integration with SCADA systems

---

**Status**: Core architecture implemented. Ready for controller and view development.

**Last Updated**: 2026-01-06
**Implemented By**: Claude Sonnet 4.5
**System Level**: Enterprise Production (Oil Field Level)
