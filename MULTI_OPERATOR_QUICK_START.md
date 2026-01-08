# Multi-Operator Task Assignment - Quick Start Guide

## For Supervisors: How to Assign Multiple Operators

### Step 1: Access Supervisor Dashboard
Navigate to: `http://localhost:8000/mpfm-supervisor/dashboard`

### Step 2: Locate Unassigned Tasks
Scroll to the **"Unassigned Tasks"** section (marked with orange header and warning icon)

### Step 3: Assign Operators to a Task
For each task you want to assign:

1. **Select Operators:** Click the "Operators" dropdown and select multiple operators
   - Hold CTRL (Windows) or CMD (Mac) to select multiple
   - Search for operator names

2. **Choose Primary Operator:** From the "Primary" dropdown, select the lead operator
   - Primary operator must be one of the selected operators
   - System will validate this automatically

3. **Confirm Assignment:** Click the **"Assign"** button

### Step 4: Verify Assignment
The task will:
- Move from "Unassigned Tasks" to assigned
- Appear in each operator's dashboard
- Show all team members when operators view it

---

## For Operators: How to View Your Tasks

### Step 1: Log In as Operator
Access the operator portal with your credentials

### Step 2: View Your Dashboard
Navigate to: `http://localhost:8000/operator/dashboard`

You'll see:
- **Today's Tasks** - Tasks scheduled for today
- **This Week's Tasks** - Tasks for the current week
- **Pending Tasks** - Tasks awaiting action
- **Overdue Tasks** - Tasks past their due date
- **Completed Tasks** - Your task history

### Step 3: View Task Details
- Click on any task card to see full details
- You'll see all team members assigned to the task
- Primary operator is marked with a star (★)

### Step 4: Work on a Task
1. **Start Task:** Click "Start Task" button (for pending tasks)
2. **Complete Task:** Click "Complete Task" button (for in-progress tasks)
3. **Upload Logs:** Use the upload feature for MPFM log files

### Step 5: View Task History
Navigate to: `http://localhost:8000/operator/history`

Shows:
- All completed tasks
- Completion dates
- Task duration
- Supervisor remarks

---

## Key Features

### Multi-Operator Assignment
✅ Supervisors can assign unlimited operators to one task
✅ Each operator automatically sees only their assigned tasks
✅ No task appears for operators not assigned to it

### Task Isolation
✅ Operators see only their own tasks
✅ Cannot access tasks assigned to other operators
✅ Cannot see other operators' task history

### Team Visibility
✅ Operators can see all team members on shared tasks
✅ Primary operator clearly marked
✅ Team composition visible on task cards

### Shared Status
✅ Any operator can start or complete the task
✅ Status updates immediately for all team members
✅ All operators see the same task status

### Authorization
✅ System prevents accessing unauthorized tasks
✅ Violating users get 403 Forbidden error
✅ Database constraints prevent duplicate assignments

---

## Common Scenarios

### Scenario 1: Assigning Multiple Operators to One Task

**Task:** You need both Operator 1 and Operator 2 to work on Well 375 test

**Steps:**
1. Go to supervisor dashboard
2. Find Well 375 in "Unassigned Tasks"
3. Select "Operator 1" and "Operator 2" from dropdown
4. Select "Operator 1" as primary
5. Click "Assign"

**Result:**
- Operator 1 sees task on dashboard (primary)
- Operator 2 sees task on dashboard (team member)
- Both can start/complete the task
- Both can see the other is assigned

---

### Scenario 2: Operator Completes a Shared Task

**Task:** Operator 2 completes a task shared with Operator 1

**Steps:**
1. Operator 2 logs in
2. Views task on dashboard
3. Starts the task
4. Performs the work
5. Marks task as "Complete Task"

**Result:**
- Task shows as completed
- Both operators see it in their history
- Status is shared (not duplicated)
- Supervisor sees task as complete

---

### Scenario 3: Checking Team Members on a Task

**Task:** You want to see who else is assigned to your task

**Steps:**
1. View your task on dashboard
2. Look at task card
3. Team members shown in badges
4. Primary operator marked with star (★)

**Result:**
- Clear visibility of team composition
- Primary operator easily identified
- No surprises when working collaboratively

---

## Important Notes

### Authorization
- **You can only see tasks assigned to you**
- If a task shows "403 Forbidden" when clicking it, you're not assigned
- Contact your supervisor to be added to the task

### Primary Operator
- Each task has one primary operator
- Primary operator is usually the lead/responsible person
- Other operators are team members
- Primary operator cannot be removed without reassigning

### Status is Shared
- If you complete a task, it's complete for all operators
- If another operator starts a task, you see it's started
- Status updates affect all team members immediately

### Task History
- You only see your own completed tasks
- Other operators' tasks don't appear in your history
- History shows tasks you personally contributed to

---

## Troubleshooting

### I Don't See a Task I Should Be Working On
**Solution:**
1. Check with your supervisor
2. Verify you're assigned to the task
3. Confirm the task is under your assigned monthly plan
4. Refresh the page (Ctrl+R)

### I Can't Access a Task (403 Error)
**Solution:**
1. You're likely not assigned to this task
2. Contact your supervisor for assignment
3. Check if task has the right date/plan assigned

### I See a Team Member But They Can't See My Changes
**Solution:**
1. Refresh their page (Ctrl+R)
2. Confirm both operators are assigned to the task
3. Check database is synced properly

### The Assignment Form Won't Submit
**Solution:**
1. Ensure primary operator is selected
2. Ensure at least one operator is selected
3. Ensure primary operator is in selected operators list
4. Try again with different operators

---

## Database Commands (Admin Only)

### Check Task Assignments
```bash
# Via Tinker
php artisan tinker
>>> use App\Models\PlanTask;
>>> PlanTask::with('operators')->find(3)->operators
```

### Manually Migrate Existing Assignments
```bash
php artisan tasks:migrate-assignments
```

### View Pivot Table Data
```bash
# Via SQL directly
SELECT * FROM plan_task_operators;
```

---

## Quick Reference

| Action | User Type | Location |
|--------|-----------|----------|
| Assign operators | Supervisor | /mpfm-supervisor/dashboard#unassigned |
| View my tasks | Operator | /operator/dashboard |
| View task list | Operator | /operator/tasks |
| View calendar | Operator | /operator/calendar |
| View history | Operator | /operator/history |
| View task details | Operator | /operator/tasks/{id} |

---

## Need Help?

### For Supervisors:
- Check that operator is active and has "mpfm_operator" role
- Verify operator appears in dropdown
- Ensure primary operator is in selected list

### For Operators:
- Confirm assignment with supervisor
- Check dashboard refreshes after assignment
- Verify team members can see you in pivot table

### For Administrators:
- Check `plan_task_operators` table
- Verify migration ran: `php artisan tasks:migrate-assignments`
- Check operator roles: `User::role('mpfm_operator')->get()`

---

**System Status:** ✅ FULLY OPERATIONAL

All multi-operator features are ready to use!
