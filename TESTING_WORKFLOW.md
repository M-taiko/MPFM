# Testing the Complete Upload Workflow

## 🧪 Quick Test Guide

Follow these steps to test the unified upload workflow from start to finish.

---

## Prerequisites

1. **Database has test data:**
   - At least one PlanTask with `completion_status = 'pending'`
   - Task is assigned to a user via `plan_task_operators` pivot table

2. **You are logged in as an operator:**
   - Email: `operator1@oilfield.com` (or any operator assigned to tasks)
   - Password: Your password

3. **You have a test file ready:**
   - A `.txt`, `.csv`, `.vxlog`, or `.log` file
   - Max 10MB in size
   - Can be empty or contain log data

---

## Test Procedure

### Phase 1: Operator Dashboard - View Tasks

**1.1 Navigate to Operator Dashboard**
```
URL: http://127.0.0.1:8000/operator/dashboard
Method: GET
Expected: Dashboard loads with task cards
```

**Check Points:**
- ✅ Can see "My Tasks for Today" section
- ✅ Can see "All Pending Tasks" table
- ✅ Pending tasks have a blue "Start Task" button
- ✅ Completed tasks show green checkmark
- ✅ In-progress tasks show cyan indicator

**1.2 Locate a Pending Task**
```
Look for: Task with status "pending"
Examples:
  - "Well 375/1" with status badge "PENDING"
  - Task with "Start Task" button visible
```

**Check Points:**
- ✅ Task shows task identifier (Well name)
- ✅ Task shows field location
- ✅ Task shows tower name
- ✅ Task shows device type
- ✅ Status badge shows yellow/orange "PENDING"

---

### Phase 2: Start Task - Initial POST

**2.1 Click "Start Task" Button**
```
Action: Click blue "Start Task" button on any pending task
Method: POST
Route: /operator/tasks/{task_id}/start
```

**Expected Behavior:**
- Button should become disabled briefly
- Page should redirect automatically
- URL should change to `/upload?task_id=X` (where X is the task ID)

**2.2 Verify Redirect Completed**
```
URL Check: http://127.0.0.1:8000/upload?task_id=X
Status Code: 200 (OK)
```

**Check Points:**
- ✅ URL shows `/upload` (not `/operator/tasks/...`)
- ✅ URL includes `?task_id=` parameter
- ✅ Page loads without errors
- ✅ No 403 or 404 errors

---

### Phase 3: Upload Form - Auto-Fill Verification

**3.1 Check Form Title**
```
Expected: "Upload Log for [Task ID]"
Example: "Upload Log for Well 375/1"
```

**3.2 Verify Auto-Fill Indicators**
```
Look for:
  ✅ "Auto-filled from task" indicator (green checkmark)
  Should appear on:
    - "Well Information" section header
    - "Test Configuration" section header
```

**3.3 Verify Auto-Filled Fields**

**Well Information Section:**
- [ ] Client: Should show "Oil Field" (readonly)
- [ ] Field: Should show field name (readonly)
- [ ] Well: Should show well identifier (readonly)

**Example:**
```
Client:   Oil Field ← Should be filled
Field:    Upper Zakum ← Should be filled
Well:     375/1 ← Should be filled
```

**Test Configuration Section:**
- [ ] Wellhead Tower: Should show tower name (readonly)
- [ ] Wellhead String: Should show string identifier (readonly)
- [ ] Operator: Should show your name (readonly)
- [ ] Test Date: Should show scheduled date (readonly)
- [ ] Test Type: Should show device type (readonly)

**Example:**
```
Wellhead Tower:   T-01 ← Should be filled
Wellhead String:  ST-01 ← Should be filled
Operator:         John Doe ← Should be filled
Test Date:        2026-01-08 ← Should be filled
Test Type:        Flow ← Should be filled
```

**3.4 Verify Fields Are Read-Only**
```
Try to: Click in any pre-filled field and type
Expected: Field should NOT accept input (readonly)
Visual: Field should appear grayed out or with readonly styling
```

**Check Points:**
- ✅ All auto-filled fields are read-only
- ✅ Cannot edit or delete auto-filled values
- ✅ Fields are clearly marked as readonly
- ✅ Form looks professional with consistent styling

---

### Phase 4: File Upload - Select and Submit

**4.1 Prepare Test File**
```
Create a simple test file:

Option 1: Simple TXT file
  ├─ Open Notepad
  ├─ Type: "Test log file contents"
  ├─ Save as: "test_log.txt"
  └─ Location: Desktop or Downloads

Option 2: Use existing VX log file
  ├─ Locate any .vxlog file
  ├─ Can be empty
  └─ Will be processed by system

Max Size: 10 MB
Allowed Types: txt, csv, vxlog, log
```

**4.2 Upload File**
```
Action 1: Drag & Drop method
  ├─ Locate file upload area (with upload icon)
  ├─ Drag test file onto upload area
  └─ File should appear in upload preview

Action 2: Click to Browse method
  ├─ Click on upload area
  ├─ Click "Select File" button
  ├─ Choose test file from file browser
  └─ File should appear in upload preview
```

**Check Points:**
- ✅ File appears in preview
- ✅ File name shown correctly
- ✅ File size shown
- ✅ Upload area shows selected file
- ✅ Can see file icon and name

**4.3 Submit Form**
```
Action: Click "Upload & Analyze Log File" button
Method: POST
Route: /process-log
Payload:
  - logfile: [file contents]
  - client: Oil Field
  - field: [auto-filled]
  - well: [auto-filled]
  - operator: [auto-filled]
  - test_date: [auto-filled]
  - task_id: [hidden field]
  - _token: CSRF token
```

**Expected Behavior:**
- Button should show loading state
- File should upload (may take a few seconds)
- Page should process the file
- Should see "Processing..." message

**Check Points:**
- ✅ Form submits successfully
- ✅ No validation errors
- ✅ File upload completes
- ✅ Processing begins
- ✅ No 500 or 422 errors

---

### Phase 5: Results Page - Confirm Completion

**5.1 Wait for Results**
```
Expected: Results page loads after file processing
Timeline: 2-5 seconds typical
```

**Check Points:**
- ✅ Page shows "Results" or "Analysis"
- ✅ File name appears
- ✅ Status shows upload successful
- ✅ Charts and analysis appear
- ✅ No errors shown

**5.2 Verify Task Completion**
```
On results page, you should see:
  ✅ Success message indicating file uploaded
  ✅ Analysis results or data tables
  ✅ Completion indicator
```

**5.3 Navigate Away**
```
Action: Click "Back to Dashboard" or navigate to:
  http://127.0.0.1:8000/operator/dashboard
Expected: Return to dashboard
```

---

### Phase 6: Verify Task Status - Confirm Completion

**6.1 Check Dashboard for Updated Task**
```
URL: http://127.0.0.1:8000/operator/dashboard
Refresh: Press F5 or reload page
```

**6.2 Find Your Task in "My Tasks for Today"**
```
Look for: Task you just completed
Status Should Show: ✅ COMPLETED (in green)
```

**Example:**
```
Before:
  Task: Well 375/1
  Status: 🔴 PENDING
  [Start Task] button

After (Refresh):
  Task: Well 375/1
  Status: ✅ COMPLETED (4 min)
  Duration shown
```

**Check Points:**
- ✅ Task no longer in "All Pending Tasks" section
- ✅ Task status changed to "COMPLETED"
- ✅ Green checkmark icon visible
- ✅ Duration time shown
- ✅ Completion badge visible

**6.3 Verify in All Pending Tasks Table**
```
URL: http://127.0.0.1:8000/operator/dashboard
Look: "All Pending Tasks" section
Expected: Task should NOT be listed (it's completed now)
```

**Check Points:**
- ✅ Task removed from pending list
- ✅ Table shows fewer items
- ✅ Badge count decreased

---

### Phase 7: Verify in Monthly Tasks View (Optional)

**7.1 Navigate to Monthly Tasks**
```
URL: http://127.0.0.1:8000/operator/monthly-tasks
Or: Click "Monthly Tasks" link from dashboard
```

**7.2 Set Filters**
```
Month: January (or current month)
Year: 2026 (or current year)
Status: Completed (or All)
Click: "Apply Filters"
```

**7.3 Verify Task Appears**
```
Look for: Your task in the table
Columns should show:
  - Well name: 375/1
  - Tower: T-01
  - String: ST-01
  - Status: ✅ COMPLETED
  - Date: Today's date
```

**Check Points:**
- ✅ Task appears in completed list
- ✅ Status shows "Completed"
- ✅ Date is today
- ✅ All task details visible
- ✅ Can see your name as operator

---

## 🔍 Troubleshooting

### Issue: Button Click Does Nothing
**Solution:**
1. Check console for JavaScript errors (F12)
2. Ensure CSRF token is present in form
3. Check server logs: `tail storage/logs/laravel.log`

### Issue: Redirect Fails (Still on dashboard)
**Solution:**
1. Clear browser cache
2. Check if route exists: `php artisan route:list | grep upload`
3. Verify controller method exists

### Issue: Form Not Auto-Filling
**Solution:**
1. Check URL has `?task_id=X`
2. Verify task exists in database
3. Check if you're assigned to task:
   ```sql
   SELECT * FROM plan_task_operators
   WHERE plan_task_id = X AND user_id = YOUR_ID
   ```

### Issue: File Upload Fails
**Solution:**
1. Check file size < 10 MB
2. Check file type: .txt, .csv, .vxlog, .log
3. Check `storage/app/public/` directory exists
4. Verify file upload permissions

### Issue: 403 Forbidden Error
**Solution:**
1. Verify you're logged in
2. Verify you're assigned to task
3. Check `plan_task_operators` table
4. Clear cache: `php artisan cache:clear`

### Issue: 404 Not Found
**Solution:**
1. Verify routes are registered: `php artisan route:list`
2. Check spelling of routes
3. Clear route cache: `php artisan route:cache`

---

## 📊 Database Verification

### Check Before Test
```sql
-- Verify you have pending tasks
SELECT * FROM plan_tasks
WHERE completion_status = 'pending'
LIMIT 5;

-- Verify you're assigned to a task
SELECT * FROM plan_task_operators
WHERE user_id = 1
AND plan_task_id = 1;
```

### Check After Test
```sql
-- Verify task status changed
SELECT id, completion_status, completed_at, operator_notes
FROM plan_tasks
WHERE id = 1;

-- Should show:
-- id: 1
-- completion_status: completed
-- completed_at: 2026-01-08 15:30:45
-- operator_notes: File uploaded via test_log.txt
```

---

## ✅ Complete Test Checklist

### Dashboard
- [ ] Dashboard loads
- [ ] Pending tasks visible
- [ ] Start Task button present
- [ ] Status badges show correctly

### Task Start
- [ ] Click Start Task button
- [ ] Page redirects to /upload?task_id=X
- [ ] No errors occur
- [ ] Redirect happens automatically

### Upload Form
- [ ] Form displays correctly
- [ ] Auto-fill indicator shows
- [ ] All fields pre-filled
- [ ] Fields are read-only
- [ ] Hidden task_id field present

### File Upload
- [ ] Can select file
- [ ] File preview shows
- [ ] Submit button works
- [ ] Form submits without errors
- [ ] No validation failures

### Results Page
- [ ] Results page displays
- [ ] Success message shown
- [ ] Analysis or data shown
- [ ] Can navigate away

### Completion Verification
- [ ] Dashboard shows task as completed
- [ ] Status badge shows green
- [ ] Duration time shown
- [ ] Not in pending list anymore
- [ ] Appears in monthly tasks (completed filter)

### Database
- [ ] Task status = 'completed'
- [ ] completed_at timestamp set
- [ ] operator_notes contains filename
- [ ] All changes saved

---

## 🎉 Success!

If all checks pass, your unified upload workflow is working perfectly!

**You can now:**
- ✅ Click "Start Task" to begin workflow
- ✅ Automatically redirect to upload form
- ✅ See auto-filled task data
- ✅ Upload log files
- ✅ Complete tasks automatically
- ✅ View completion status
- ✅ Track task history

**Congratulations!** 🎊
