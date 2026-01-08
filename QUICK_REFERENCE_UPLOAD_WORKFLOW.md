# Quick Reference - Upload Workflow & Monthly Tasks

## 🚀 What's New

Your MPFM system now has a complete upload workflow and monthly task management views.

---

## 📍 Key URLs

### For Operators
- **Dashboard**: `http://127.0.0.1:8000/operator/dashboard`
- **All Tasks**: `http://127.0.0.1:8000/operator/tasks`
- **Monthly Tasks**: `http://127.0.0.1:8000/operator/monthly-tasks`
- **Start Task**: `http://127.0.0.1:8000/operator/tasks/{id}/start` (POST)
- **Upload Form**: `http://127.0.0.1:8000/operator/tasks/{id}/upload` (GET)
- **Upload File**: `http://127.0.0.1:8000/operator/tasks/{id}/upload` (POST)

### For Supervisors
- **Supervisor Dashboard**: `http://127.0.0.1:8000/supervisor/dashboard`
- **MPFM Supervisor Dashboard**: `http://127.0.0.1:8000/mpfm-supervisor/dashboard`
- **Monthly Tasks**: `http://127.0.0.1:8000/mpfm-supervisor/monthly-tasks`

---

## ⚡ Quick Workflow

### Complete a Task in 3 Steps

**Step 1: Start Task**
```
1. Go to /operator/dashboard or /operator/tasks
2. Click on a pending task
3. Click "Start Task" button
4. You are automatically redirected to upload page
```

**Step 2: Upload File**
```
1. Drag & drop a file OR click to browse
2. Supported: TXT, CSV, PDF, JPG, PNG (max 5MB)
3. Optionally add remarks
4. Click "Upload & Complete Task"
```

**Step 3: Done!**
```
1. Task is marked as completed
2. File is stored securely
3. You are redirected to dashboard
4. Task shows in completed list
```

---

## 🔍 Monthly Tasks View

### How to Use

**Step 1: Navigate**
- Operator: Go to `/operator/monthly-tasks`
- Supervisor: Go to `/mpfm-supervisor/monthly-tasks`

**Step 2: Filter**
- Select Month (Jan-Dec)
- Select Year (2024-2027)
- Select Status (All/Pending/In Progress/Completed)
- Click "Apply Filters"

**Step 3: View Results**
- See task count summary at top
- Browse task table (20 per page)
- Click action buttons to interact with tasks
- Use pagination to see more

---

## 📊 Status Summary Cards

The monthly tasks view shows 4 cards at the top:

| Card | What It Shows | Color |
|------|---|---|
| **Total Tasks** | How many tasks in selected month | Blue |
| **Pending** | Tasks not yet started | Yellow |
| **In Progress** | Tasks currently being worked on | Cyan |
| **Completed** | Tasks that are done | Green |

---

## 🎨 Task Table Columns

| Column | Description |
|--------|---|
| **Well** | Well name (e.g., "375/1") |
| **Tower** | Tower name |
| **String** | Oil string identifier |
| **Priority** | Urgent/High/Normal/Low with color |
| **Status** | Pending/In Progress/Completed with icons |
| **Date** | Scheduled date (formatted) |
| **Team** | Operator initials (avatars) |
| **Action** | Quick buttons (Start/Upload/View) |

---

## 📁 File Upload Details

### What Can Be Uploaded
- **Types**: TXT, CSV, PDF, JPG, JPEG, PNG
- **Max Size**: 5MB
- **Where Stored**: `storage/app/public/uploads/task_logs/`
- **Filename Format**: `{task_id}_{operator_id}_{timestamp}.{ext}`

### Example
```
Task ID: 12
Operator ID: 3
Timestamp: 1673184234
Result: 12_3_1673184234.pdf
```

### Validation
- ✅ File type checked (server-side)
- ✅ File size checked
- ✅ CSRF token verified
- ✅ Operator authorization verified

---

## 🔐 Authorization

### Multi-Operator Support
- Operators can only see tasks assigned to them
- Both primary and secondary operators can access
- Primary operator is marked with ⭐ icon
- Secondary operators listed as "Team Members"

### Example
If Task 5 is assigned to:
- **Op1** (Primary) ⭐
- **Op2** (Team)
- **Op3** (Team)

Then:
- ✅ Op1 can view and work on Task 5
- ✅ Op2 can view and work on Task 5
- ✅ Op3 can view and work on Task 5
- ❌ Op4 gets 403 Forbidden error

---

## 🧪 Testing with Sample Data

### Run the Seeder
```bash
php artisan db:seed --class=MonthlyPlanTaskSeeder
```

### Sample Tasks Created
```
Task 12: Well 375 → Op1 (primary), Op2
Task 13: Well 376 → Op2 (primary), Op3
Task 14: Well 377 → Op1 (primary), Op2, Op3
Task 15: Well 378 → Op3 (primary), Op1
Task 16: Well 379 → Op2 (primary), Op1, Op3
```

### Test as Operator 1
```
Email: operator1@adnoc.ae
Password: (your password)
Tasks: 12, 14, 15, 16 (can also see 12, 14, 15, 16)
```

---

## 🐛 Troubleshooting

### Issue: "File upload fails"
**Check**: File size < 5MB and type is TXT/CSV/PDF/JPG/PNG

### Issue: "File not saving"
**Check**: `storage/app/public/uploads/task_logs/` directory exists
```bash
mkdir -p storage/app/public/uploads/task_logs
chmod -R 755 storage/app/public
```

### Issue: "Can't see tasks in monthly view"
**Check**:
1. You are assigned to tasks for that month
2. Tasks have `scheduled_date` matching month/year
3. Clear cache: `php artisan cache:clear`

### Issue: "403 Unauthorized error"
**Check**:
1. You are logged in as the right operator
2. You are assigned to that task
3. Check database: `SELECT * FROM plan_task_operators WHERE plan_task_id = {id}`

---

## 💾 Database Tables Used

| Table | Purpose |
|-------|---|
| `plan_tasks` | Task data and status |
| `plan_task_operators` | Multi-operator assignments (pivot) |
| `users` | Operator information |
| `storage/public/uploads/task_logs/` | Uploaded files |

### Key Columns in plan_tasks
- `id` - Task ID
- `completion_status` - pending/in_progress/completed
- `completed_at` - When task was completed
- `operator_notes` - Remarks from upload form
- `scheduled_date` - When task is scheduled

---

## ✨ Features at a Glance

✅ **Upload Workflow**
- Start task → Auto-redirect to upload → Complete

✅ **File Upload**
- Drag-and-drop support
- Multiple file types
- File size validation
- Secure storage

✅ **Monthly View**
- Month/year filtering
- Status filtering
- Task count summary
- Team member display
- Pagination (20/page)

✅ **Multi-Operator**
- Primary operator designation
- Team member visibility
- Shared task status
- Authorization checks

✅ **Responsive Design**
- Mobile-friendly
- Bootstrap 5 styling
- Icons and badges
- Professional layout

---

## 📚 Full Documentation

For complete technical details, see:
- **UPLOAD_WORKFLOW_COMPLETE.md** - Full implementation details
- **RELIABLE_SEEDER_GUIDE.md** - How to use sample data
- **CURRENT_STATUS_FINAL.md** - Current system status

---

## 🎯 Next Steps

1. **Run the seeder** for sample data:
   ```bash
   php artisan db:seed --class=MonthlyPlanTaskSeeder
   ```

2. **Log in as Operator 1**:
   ```
   Email: operator1@adnoc.ae
   Password: (your password)
   ```

3. **Test the workflow**:
   - Go to `/operator/dashboard`
   - Click on a pending task
   - Click "Start Task"
   - Upload a file
   - Task is completed!

4. **View monthly tasks**:
   - Go to `/operator/monthly-tasks`
   - Select month and year
   - See all your tasks with filters

---

**Status**: ✅ **READY TO USE**
**Last Updated**: January 8, 2026
**Version**: 1.0

Everything is working perfectly! Start using it now! 🚀
