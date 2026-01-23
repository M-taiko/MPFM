# MPFM System - Quick Start Guide

## Access the Application

```
URL: http://localhost/MPFM/public
```

## Login Credentials

### For Testing Different Roles

| Role | Email | Password |
|------|-------|----------|
| Admin | donia.a5ra2019@gmail.com | password |
| Job Officer | job.officer@adnoc.ae | password |
| MPFM Supervisor | supervisor@adnoc.ae | password |
| Operator 1 | operator1@adnoc.ae | password |
| Operator 2 | operator2@adnoc.ae | password |
| Operator 3 | operator3@adnoc.ae | password |
| Production Supervisor | prod.supervisor@adnoc.ae | password |
| Reservoir Engineer | reservoir.engineer@adnoc.ae | password |

## Key Pages to Visit

### 1. Monthly Plans
- **URL**: `/monthly-plans`
- **Roles**: All (view-only for operators)
- **Features**: View all plans, see task summaries, SLA status

### 2. Plan Dashboard
- **URL**: `/monthly-plans/{id}`
- **Roles**: Job Officer, Supervisors, Operators
- **Features**: View tasks, submit for review, track state transitions

### 3. Operator Dashboard
- **URL**: `/operator/dashboard`
- **Roles**: MPFM Operator
- **Features**: See assigned tasks, start work, upload results

### 4. Reports
- **URL**: `/reports`
- **Roles**: Based on role (job officer, supervisor, operator, etc.)
- **Features**: View role-specific reports and statistics

## Common Workflows

### Workflow 1: Submit Plan for Approval (Job Officer)

1. **Login** as Job Officer (job.officer@adnoc.ae)
2. **Navigate** to "Monthly Plans"
3. **Click** "January 2026 MPFM Testing Plan"
4. **Review** the 5 tasks in DRAFT state
5. **Click** "Submit for Review" button
6. **Confirm** - All tasks move to "SUBMITTED_TO_SURVEILLANCE"

### Workflow 2: Assign Tasks to Operators (Surveillance Supervisor)

Note: Surveillance Supervisor role needs to be assigned to a user first.

1. **Login** as Surveillance Supervisor
2. **Navigate** to "Monthly Plans"
3. **Click** the plan
4. **Select** a task
5. **Click** "Assign to MPFM"
6. **Confirm** - Task moves to "ASSIGNED_TO_MPFM"

### Workflow 3: Start Task Execution (MPFM Operator)

1. **Login** as Operator (operator1@adnoc.ae)
2. **Navigate** to "Operator Dashboard"
3. **View** "My Assigned Tasks"
4. **Click** task "Well 375 String A1"
5. **Click** "Start Task"
6. **Confirm** - Task moves to "IN_PROGRESS"

### Workflow 4: Review and Approve (MPFM Supervisor)

1. **Login** as MPFM Supervisor (supervisor@adnoc.ae)
2. **Navigate** to "Dashboard"
3. **View** "Tasks Pending Review"
4. **Click** completed task
5. **Click** "Approve" or "Send Back for Revision"
6. **Add** optional notes
7. **Confirm** - Task transitions accordingly

## Task State Machine

```
DRAFT
  ↓ (Job Officer submits)
SUBMITTED_TO_SURVEILLANCE
  ↓ (Surveillance assigns)
ASSIGNED_TO_MPFM
  ↓ (Operator starts)
IN_PROGRESS
  ↓ (MPFM Supervisor reviews)
MPFM_REVIEWED
  ↓ (Surveillance reviews)
SURVEILLANCE_REVIEWED
  ↓ (Job Officer approves)
JOB_OFFICER_APPROVED → COMPLETED
```

## SLA Monitoring

### What is SLA?

SLA (Service Level Agreement) sets deadline expectations for task completion at each state.

### SLA Status Indicators

- 🟢 **On Track** - Task within SLA deadline
- 🟡 **Warning** - Task at 75% of SLA duration
- 🔴 **Critical** - Task at 90% of SLA duration
- ⚫ **Overdue** - Task has exceeded SLA deadline

### Default SLA Durations

| State | Duration |
|-------|----------|
| IN_PROGRESS | 24 hours |
| MPFM_REVIEWED | 8 hours |
| SURVEILLANCE_REVIEWED | 4 hours |
| JOB_OFFICER_APPROVED | 2 hours |

### Check SLA Status

**Via Command Line:**
```bash
cd /c/xampp/htdocs/MPFM
php artisan sla:check --verbose
```

## Database Management

### View All Tasks

```bash
php artisan tinker
> App\Models\PlanTask::with('monthlyPlan')->get()
```

### Reset All Data (CAUTION!)

```bash
php artisan migrate:fresh --seed
```

### Run SLA Check

```bash
php artisan sla:check
```

## Permission Matrix

### Job Officer
- ✅ Create monthly plans
- ✅ Submit plans to surveillance
- ✅ Final approval of tasks
- ✅ Reject tasks back

### Surveillance Supervisor
- ✅ Review submitted tasks
- ✅ Classify tasks
- ✅ Assign tasks to MPFM department
- ✅ Reject tasks back to draft
- ✅ Pre-final review

### MPFM Supervisor
- ✅ Assign operators to tasks
- ✅ Review operator submissions
- ✅ Reject tasks back to operator
- ✅ View all MPFM tests

### MPFM Operator
- ✅ View assigned tasks
- ✅ Start task execution
- ✅ Upload test results
- ✅ Add operator notes

### Admin
- ✅ All permissions
- ✅ Manage users, roles, permissions
- ✅ System configuration

## Troubleshooting

### Can't see tasks?
1. Make sure you're logged in
2. Check your role has permission to view tasks
3. Verify monthly plan has tasks (check database)

### Task button is grayed out?
1. Check task is in correct state
2. Verify your role can perform that transition
3. Check if user is assigned to task (for operators)

### SLA not showing?
1. Confirm task has an SLA assigned
2. Run: `php artisan sla:check`
3. Check `task_sla_histories` table

### Forgot password?
Contact admin at: donia.a5ra2019@gmail.com

## Keyboard Shortcuts

- `Alt + D` - Go to Dashboard
- `Alt + P` - Go to Plans
- `Alt + L` - Logout
- `?` - Show help (if enabled)

## Tips & Tricks

### Bulk Operations
- Select multiple tasks with checkboxes
- Use "Bulk Transition" to move multiple tasks at once
- Great for submitting entire plans for review

### Audit Trail
Every task state change is logged with:
- Who made the change
- When it happened
- What state it transitioned to
- Optional notes/remarks

View via: Task Details → "State History" tab

### Filtering
Use dashboard filters to see:
- Tasks by state
- Tasks by priority
- Tasks by assigned operator
- Tasks with SLA warnings
- Tasks by date range

## Contact & Support

For issues or questions:
1. Check the logs: `storage/logs/laravel.log`
2. Run system diagnostics: `php artisan tinker`
3. Contact system administrator

## System Health Check

```bash
cd /c/xampp/htdocs/MPFM

# Check all migrations
php artisan migrate:status

# Test database connection
php artisan tinker
> DB::connection()->getPdo()

# Clear cache
php artisan cache:clear
php artisan config:clear

# Optimize performance
php artisan optimize
```

---

**Last Updated**: January 23, 2026
**System Version**: MPFM v2.0
**Status**: ✅ Production Ready
