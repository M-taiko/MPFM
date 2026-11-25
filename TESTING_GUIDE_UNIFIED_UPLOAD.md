# Testing Guide - Unified Upload Workflow

**Status:** Ready to test (code changes applied, NOT committed yet)

---

## 🧪 Test Case 1: Basic Workflow

### Steps:
```
1. Go to: http://127.0.0.1:8000/operator/dashboard
2. Log in as operator1@oilfield.com (password: your password)
3. Find a PENDING task (blue "Pending" badge)
4. Click on the task to view details
5. Click the "Start Task" button
```

### Expected Result:
```
✅ Page redirects to: http://127.0.0.1:8000/upload?task_id=XX

Example: http://127.0.0.1:8000/upload?task_id=12
```

### What You Should See:
```
✓ Upload form appears
✓ "Auto-filled from task" label visible under form sections
✓ Form fields contain task information:
  - Client: Oil Field
  - Field: (tower name)
  - Well: (tower well name)
  - Tower: (tower name)
  - String: (oil string identifier)
  - Operator: (your name)
  - Test Date: (from task scheduled date)
  - Test Type: (from task category)
```

---

## 🧪 Test Case 2: Verify Auto-Fill Data

### Steps:
```
1. On upload page from Test Case 1
2. Look at each form field
3. Verify values match your task information
```

### Expected Result:
```
Form Section 1: Well Information
  ✓ Client = "Oil Field"
  ✓ Field = (actual tower name)
  ✓ Well = (actual well name)

Form Section 2: Test Configuration
  ✓ Tower = (actual tower name)
  ✓ String = (actual string ID)
  ✓ Operator = (your logged-in name)
  ✓ Test Date = (from task)
  ✓ Test Type = (from task category)
```

### Visual Check:
```
✓ Fields have gray background (readonly/disabled)
✓ Cannot type in auto-filled fields
✓ Green checkmark icons visible
✓ "Auto-filled from task" text visible
```

---

## 🧪 Test Case 3: File Upload

### Prepare:
```
Create a test VX log file:
  - Name: test_log.vxlog or test_log.txt
  - Content: Any valid VX log format OR just sample text
  - Size: < 10MB
```

### Steps:
```
1. On upload page
2. Scroll to "VX Log File" section
3. Either:
   a) Drag file onto the upload area, OR
   b) Click the upload area and browse to select file
4. See file name appear with checkmark
5. Click "Upload & Analyze Log File" button
```

### Expected Result:
```
✅ File accepted (no error message)
✅ Processing begins
✅ Redirected to results page showing analysis
✅ File data displayed in tables and charts
```

---

## 🧪 Test Case 4: Task Completion

### Steps:
```
1. After successful file upload (from Test Case 3)
2. You should see results page
3. Go back to: http://127.0.0.1:8000/operator/dashboard
4. Look at the task you just uploaded
```

### Expected Result:
```
✅ Task status changed to "COMPLETED" (green badge)
✅ Task appears in different section or filtered out
✅ Can go to monthly tasks view

Alternative check:
  Go to: http://127.0.0.1:8000/operator/history
  ✅ Should see the task you just completed
  ✅ Shows completion timestamp
```

---

## 🧪 Test Case 5: Multi-Operator Test

### Setup:
```
Ensure a task is assigned to:
  - Operator 1 (primary)
  - Operator 2 (team member)
```

### Steps as Operator 1:
```
1. Log in as operator1@oilfield.com
2. Go to dashboard
3. Find the shared task
4. Click "Start Task"
5. Upload a file
6. Task should be completed
```

### Steps as Operator 2:
```
1. Log out Operator 1
2. Log in as operator2@oilfield.com
3. Go to dashboard
4. Find the SAME task
5. Should see status is "COMPLETED" ✅
6. Can also start/upload if status was pending
```

### Expected Result:
```
✅ Both operators see the same task
✅ Both can start and upload
✅ Status is shared between them
✅ When one completes it, both see it as completed
```

---

## 🧪 Test Case 6: Authorization Check

### Steps:
```
1. Try to access upload directly:
   http://127.0.0.1:8000/upload?task_id=99
   (task you're NOT assigned to)
2. Or use curl/Postman to simulate
```

### Expected Result:
```
✅ Get 403 Forbidden error
✅ Message: "Unauthorized access to task"
✅ Cannot access form for tasks you're not assigned to
```

---

## 🧪 Test Case 7: Error Handling

### Test Invalid File:

**Steps:**
```
1. On upload page
2. Try to upload:
   - A .pdf file (should fail - not allowed)
   - An executable file (should fail)
   - A file > 10MB (should fail)
```

**Expected Result:**
```
✅ Error message displayed
✅ Form reloaded
✅ Task NOT completed
✅ Can try again with valid file
```

### Test Missing File:

**Steps:**
```
1. On upload page
2. Click "Upload & Analyze" without selecting file
```

**Expected Result:**
```
✅ Browser validation error
✅ Cannot submit form
```

---

## 📋 Checklist Summary

### Before Testing:
- [ ] Database seeded with test tasks
- [ ] Operators are assigned to tasks
- [ ] VX log test file prepared
- [ ] Browser cache cleared (Ctrl+Shift+R)

### Test Case 1: Basic Workflow
- [ ] Can start a task
- [ ] Redirects to /upload?task_id=XX
- [ ] Upload form displays

### Test Case 2: Auto-Fill
- [ ] Client shows Oil Field
- [ ] Field shows tower name
- [ ] Well shows tower well name
- [ ] Operator shows logged-in name
- [ ] Test Date shows scheduled date
- [ ] Fields are read-only
- [ ] Green checkmarks visible

### Test Case 3: File Upload
- [ ] Can drag-drop file
- [ ] Can browse and select file
- [ ] File name displays with checkmark
- [ ] Can submit form

### Test Case 4: Task Completion
- [ ] Task status changes to COMPLETED
- [ ] Task appears in history
- [ ] Completion timestamp recorded

### Test Case 5: Multi-Operator
- [ ] Both operators see task
- [ ] Both can upload
- [ ] Status is shared

### Test Case 6: Authorization
- [ ] Cannot access unassigned tasks
- [ ] Get 403 error

### Test Case 7: Error Handling
- [ ] Invalid files rejected
- [ ] Errors displayed clearly
- [ ] Can retry

---

## 🔍 Debugging Tips

If something doesn't work:

### 1. Check Database:
```sql
-- Verify task exists and is assigned
SELECT * FROM plan_tasks WHERE id = 12;

-- Verify operator assignment
SELECT * FROM plan_task_operators WHERE plan_task_id = 12;

-- Check task status
SELECT id, completion_status, completed_at FROM plan_tasks WHERE id = 12;
```

### 2. Check Logs:
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log
```

### 3. Clear Cache:
```bash
php artisan cache:clear
php artisan config:clear
```

### 4. Browser Console:
```javascript
// Open Browser DevTools (F12)
// Check Console for JavaScript errors
// Check Network tab for failed requests
```

### 5. Check Authorization:
```php
// In tinker, check if operator is assigned:
$task = PlanTask::find(12);
$task->isAssignedTo(auth()->id());  // Should return true
```

---

## 📊 Expected Data Flow

```
Request: POST /operator/tasks/3/start
  ↓
OperatorDashboardController@startTask()
  ├─ Check: isAssignedTo() ✓
  ├─ Update: status = in_progress ✓
  └─ Redirect: /upload?task_id=3
  ↓
Request: GET /upload?task_id=3
  ↓
VxLogController@showUploadForm()
  ├─ Get: task with relationships
  ├─ Check: isAssignedTo() ✓
  ├─ Build: autoFillData array
  └─ Return: view('logs.upload', compact('task', 'autoFillData'))
  ↓
Blade Renders:
  ├─ Display form
  ├─ Fill fields from autoFillData
  ├─ Show "Auto-filled" indicators
  └─ Make fields readonly
  ↓
Request: POST /process-log
  ↓
VxLogController@processLogFile()
  ├─ Validate file ✓
  ├─ Parse file ✓
  ├─ If task_id:
  │  ├─ Find task
  │  ├─ Check: isAssignedTo() ✓
  │  ├─ Update: status = completed ✓
  │  ├─ Set: completed_at = now() ✓
  │  └─ Save: notes with filename ✓
  ├─ Prepare: results data
  └─ Return: view('logs.results')
  ↓
User Sees:
  ✅ Results page with analysis
  ✅ Task marked as completed
```

---

## 🎯 Success Criteria

All test cases pass when:

✅ Operator can start any assigned task
✅ Start action redirects to /upload?task_id=XX
✅ Upload form is pre-filled with task data
✅ All auto-filled fields are read-only
✅ "Auto-filled from task" labels visible
✅ File upload works (drag-drop and browse)
✅ Task automatically completed after upload
✅ Completion timestamp recorded
✅ Task appears in history
✅ Other operators see updated status
✅ Unassigned operators get 403 error
✅ Invalid files rejected with error message

---

## 📝 Notes

- Changes are **NOT committed to git** yet
- All functionality is **code-level only**
- When ready, you can commit and push
- No database schema changes needed
- Backward compatible with existing uploads
- Multi-operator support already working

---

**Ready to test!** 🚀

Start with Test Case 1 to verify the complete workflow.
