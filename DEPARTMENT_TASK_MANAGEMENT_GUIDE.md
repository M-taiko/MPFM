# Department Task Management System - User Guide

## Overview

The Department Task Management System allows department managers and administrators to:
1. **Create dynamic tasks** with cascading dropdown selections
2. **View department-specific tasks** in a dedicated management panel
3. **Assign operators** to tasks using a Bootstrap modal with AJAX
4. **Remove operators** from tasks as needed
5. **Track task states** and progress

---

## System Architecture

### New Components

#### 1. **DepartmentTaskController** (`app/Http/Controllers/DepartmentTaskController.php`)
Handles all department task operations:
- `index($departmentId)` - Display tasks for a department
- `getAssignmentModal()` - AJAX endpoint to load assignment modal data
- `assignOperator()` - AJAX endpoint to assign operator to task
- `removeOperator()` - AJAX endpoint to remove operator from task

#### 2. **Department Tasks View** (`resources/views/departments/tasks.blade.php`)
- Responsive table showing all department tasks
- Task details: ID, Title, Priority, State, Assigned Operators, Due Date
- Action buttons with Bootstrap modal integration
- Pagination support for large task lists

#### 3. **JavaScript Module** (`public/js/department-tasks.js`)
Client-side functionality:
- Modal loading via AJAX
- Operator selection and assignment
- Real-time operator list updates
- Alert notifications for user feedback

---

## Database Schema

### Tasks Table (`tasks`)
```sql
CREATE TABLE tasks (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,           -- Task title
  monthly_plan_id BIGINT FOREIGN KEY,    -- Associated monthly plan
  department_id BIGINT FOREIGN KEY,      -- Task's department
  tower_id BIGINT FOREIGN KEY NULLABLE,  -- Location tower
  oil_string_id BIGINT FOREIGN KEY NULLABLE, -- Oil string reference
  state VARCHAR(100) NOT NULL,           -- Task state (DRAFT, ASSIGNED, IN_PROGRESS, etc.)
  priority VARCHAR(50) NOT NULL,         -- Priority level (low, medium, high, critical)
  task_type VARCHAR(255) NOT NULL,       -- Department type (mpfm_operations, wire_line_services, etc.)
  assigned_to_user_id BIGINT NULLABLE,   -- Primary assigned user (legacy support)
  created_by_user_id BIGINT FOREIGN KEY, -- User who created task
  expected_completion_at DATETIME NULLABLE,
  type_specific_data JSON NULLABLE,      -- Department-specific field values
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Task Operators Pivot Table (`task_operators`)
```sql
CREATE TABLE task_operators (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  task_id BIGINT FOREIGN KEY,           -- Task being assigned
  user_id BIGINT FOREIGN KEY,           -- Operator (user)
  assigned_at DATETIME,                 -- When assignment was made
  assigned_by BIGINT FOREIGN KEY,       -- User who made assignment
  is_primary BOOLEAN DEFAULT false,     -- Primary operator flag
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

---

## Workflow: Task Creation to Assignment

### Step 1: Create Task (Job Officer)
**Route:** `POST /tasks/store-advanced`

Navigate to **Tasks → Create Task (Advanced)**

Form fields:
- **Title** - Task name/description
- **Description** - Additional details
- **Tower** - Select oil field tower (triggers oil string dropdown)
- **Oil String** - Auto-populated based on tower
- **Department** - Select department (triggers requirements display)
- **Test Type** - Auto-populated from department
- **Priority** - Task priority level
- **Expected Completion** - Due date/time

**What happens:**
1. Form validates all inputs
2. Oil string must belong to selected tower
3. Test type must belong to selected department
4. Monthly plan auto-created if doesn't exist
5. Task created with state = DRAFT
6. Department-specific data saved in `type_specific_data` JSON field

### Step 2: View Department Tasks (Department Manager)
**Route:** `GET /department-tasks/{department_id}`

Navigate to **Department Operations → [Your Department] → Dashboard**

The tasks table displays:
- Unassigned tasks with "Not Assigned" badge
- Assigned tasks with operator name badges
- Task priority with color coding
- Task state with badge
- Due date with overdue highlighting

### Step 3: Open Assignment Modal (Department Manager)
**Click:** "Assign" button on task row

**What happens:**
1. AJAX request to `/department-tasks/api/get-assignment-modal`
2. Modal loads with:
   - Task name
   - Dropdown of available operators in this department
   - List of already-assigned operators
   - "Assign Selected Operator" button

### Step 4: Assign Operator (Department Manager)
**Action:** Select operator → Click "Assign Selected Operator"

**Validation:**
- Operator must be in the same department as task
- Cannot assign same operator twice
- Only operators with role: `operator` or `mpfm_operator`

**What happens:**
1. AJAX POST to `/department-tasks/api/assign-operator`
2. Operator attached via `task_operators` pivot table with:
   - `assigned_at` = current timestamp
   - `assigned_by` = current user ID
   - `is_primary` = false (default)
3. Task state auto-updated from DRAFT → ASSIGNED_TO_DEPARTMENT
4. Modal refreshes showing updated operator list
5. Success alert displayed

### Step 5: Remove Operator (Department Manager)
**Click:** "Remove" button on assigned operator in modal

**Confirmation:** Browser confirms action

**What happens:**
1. AJAX POST to `/department-tasks/api/remove-operator`
2. Operator detached from task
3. Modal refreshes showing updated list
4. Success alert displayed

---

## Authorization & Access Control

### Role-Based Access

| Role | Can View | Can Assign | Can Create |
|------|----------|-----------|-----------|
| Admin | All | All departments | Yes |
| Department Manager | Own dept only | Own dept only | No |
| Operator | Own assignments | No | No |
| Job Officer | All | No | Yes |
| Surveillance Supervisor | All (read-only) | No | No |

### Authorization Checks

**In DepartmentTaskController:**
```php
private function authorizeDepartmentAccess(Department $department)
{
    // Admin: Full access
    if (auth()->user()->hasRole('admin')) {
        return true;
    }

    // Department Manager: Only own department
    if (auth()->user()->hasRole('department_manager')
        && auth()->user()->department_id === $department->id) {
        return true;
    }

    // Deny all others
    abort(403, 'Unauthorized access to this department.');
}
```

---

## API Endpoints

### 1. Get Assignment Modal Data
**Endpoint:** `POST /department-tasks/api/get-assignment-modal`

**Request:**
```json
{
  "task_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "task": {
    "id": 1,
    "title": "MPFM Test",
    "department_id": 1
  },
  "operators": [
    {"id": 5, "name": "Ahmed Hassan", "email": "ahmed@example.com"},
    {"id": 6, "name": "Sara Ali", "email": "sara@example.com"}
  ],
  "assignedOperators": [5]
}
```

### 2. Assign Operator to Task
**Endpoint:** `POST /department-tasks/api/assign-operator`

**Request:**
```json
{
  "task_id": 1,
  "operator_id": 6,
  "is_primary": false
}
```

**Response:**
```json
{
  "success": true,
  "message": "Operator assigned successfully.",
  "operator": {
    "id": 6,
    "name": "Sara Ali"
  }
}
```

**Error Response (cross-department):**
```json
{
  "success": false,
  "message": "Operator is not in the same department as the task."
}
```

### 3. Remove Operator from Task
**Endpoint:** `POST /department-tasks/api/remove-operator`

**Request:**
```json
{
  "task_id": 1,
  "operator_id": 5
}
```

**Response:**
```json
{
  "success": true,
  "message": "Operator removed successfully."
}
```

---

## Task State Machine

```
DRAFT
  ↓
SUBMITTED_TO_SURVEILLANCE
  ├→ ASSIGNED_TO_DEPARTMENT (when operator assigned)
  ├→ DRAFT (revert)
  ↓
ASSIGNED_TO_DEPARTMENT
  ↓
IN_PROGRESS
  ↓
DEPARTMENT_REVIEWED
  ├→ SURVEILLANCE_REVIEWED
  ├→ IN_PROGRESS (revert)
  ↓
SURVEILLANCE_REVIEWED
  ├→ JOB_OFFICER_APPROVED
  ├→ DEPARTMENT_REVIEWED (revert)
  ↓
JOB_OFFICER_APPROVED
  ├→ COMPLETED
  ├→ SURVEILLANCE_REVIEWED (revert)
  ↓
COMPLETED (final state)
```

---

## Data Storage

### Type-Specific Data (JSON)
Department-specific fields are stored in `type_specific_data` JSON column:

```json
{
  "bore_size": "3.5",
  "test_duration": "4.5",
  "comments": "Standard testing procedure",
  "fish_type": "Tubing",
  "recovery_status": "Partial"
}
```

### Task Operators Pivot Data
Additional metadata stored in pivot table:
```php
$task->operators()->attach($operator_id, [
  'assigned_at' => now(),
  'assigned_by' => auth()->id(),
  'is_primary' => true/false
]);
```

---

## Common Workflows

### Scenario 1: Create and Assign MPFM Test
1. Job Officer creates task:
   - Select Tower: ZK-100
   - Oil String: Auto-loaded (e.g., PS-1, PS-2)
   - Department: MPFM Operations
   - Test Type: Auto-loaded (MPFM Test)
   - Priority: High
   - Due: Tomorrow 2 PM

2. MPFM Department Manager assigns:
   - Navigate to MPFM Operations → Tasks
   - Click Assign on new task
   - Select operator: Ahmed Hassan
   - Task state: DRAFT → ASSIGNED_TO_DEPARTMENT

### Scenario 2: Multi-Operator Assignment
1. Create task with multiple operators needed
2. Assignment modal stays open after assignment
3. Select another operator
4. Click "Assign Selected Operator"
5. Both operators now appear in "Assigned Operators" list
6. Each can be removed independently

### Scenario 3: Remove Operator Due to Unavailability
1. Open assignment modal
2. See "Ahmed Hassan" in assigned list
3. Click "Remove" button next to name
4. Confirm removal
5. Operator removed, task still assigned to remaining operators

---

## Troubleshooting

### Modal Not Loading
- **Cause:** CSRF token missing in form
- **Solution:** Ensure `<meta name="csrf-token">` in HTML head
- **Check:** Browser console for fetch errors

### Operators Not Showing
- **Cause:** No operators assigned to department
- **Solution:**
  1. Go to Users management
  2. Assign user to department with `operator` role
  3. Refresh page

### Cannot Assign Cross-Department
- **Cause:** Operator from different department
- **Solution:** System prevents this intentionally for data isolation
- **Action:** Use operators from task's department only

### Task Not State-Updating
- **Cause:** Manual state update needed
- **Solution:** Task auto-updates from DRAFT → ASSIGNED_TO_DEPARTMENT
- **Check:** Refresh page to see latest state

---

## Performance Considerations

### Database Indexes
```sql
CREATE INDEX idx_tasks_department ON tasks(department_id);
CREATE INDEX idx_tasks_state ON tasks(state);
CREATE INDEX idx_task_operators_task ON task_operators(task_id);
CREATE INDEX idx_task_operators_user ON task_operators(user_id);
CREATE INDEX idx_users_department ON users(department_id);
```

### Query Optimization
- Eager load relationships: `with(['department', 'operators'])`
- Filter by department before querying operators
- Use pagination for large task lists

---

## Future Enhancements

1. **Bulk Assignment**: Select multiple tasks → assign to operator
2. **Operator Availability**: Calendar view of operator availability
3. **Assignment History**: Audit trail of who assigned whom when
4. **Notifications**: Notify operators when assigned
5. **Template Tasks**: Save/reuse task configurations per department
6. **Capacity Planning**: Show operator workload/capacity
7. **Mobile Interface**: Mobile-friendly assignment UI
8. **Task Dependencies**: Block assignments until prerequisites met

---

## Support & Questions

For issues or questions:
1. Check this guide's Troubleshooting section
2. Review API response error messages
3. Check browser console for JavaScript errors
4. Contact system administrator

---

**Version:** 1.0
**Last Updated:** 2026-01-28
**Created By:** Claude Haiku 4.5
