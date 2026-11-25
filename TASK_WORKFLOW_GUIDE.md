# مرشد سير العمل الكامل للتاسكات
# Complete Task Workflow Guide

## 📋 نظرة عامة على سير العمل
## Workflow Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         TASK LIFECYCLE FLOW                             │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  [1] DRAFT ──→ [2] SUBMITTED ──→ [3] ASSIGNED ──→ [4] IN_PROGRESS     │
│        ↑        TO_SURVEILLANCE      TO_MPFM           │                │
│        │                                               │                │
│        └──────────────────────────────────────────────┘                │
│        (Surveillance can reject for corrections)                        │
│                                                                          │
│  [4] IN_PROGRESS ──→ [5] MPFM_REVIEWED ──→ [6] SURVEILLANCE_REVIEWED  │
│       ↑                    ↑                        │                   │
│       │                    │                        │                   │
│       │                    └─ MPFM Supervisor      │                   │
│       │                       reviews & approves   │                   │
│       │                                             │                   │
│       └─ Operator uploads log file                 │                   │
│                                                     ↓                   │
│  [7] JOB_OFFICER_APPROVED ──→ [8] COMPLETED       │                   │
│            ↑                                        │                   │
│            └────────────────────────────────────────┘                   │
│            (Job Officer gives final approval)                           │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 1️⃣ المرحلة الأولى: Job Officer ينشئ خطة شهرية
## Phase 1: Job Officer Creates Monthly Plan

### الخطوات:
1. دخول الـ Dashboard
2. الذهاب إلى "Monthly Planning" → "Monthly Plans" → "Create New Plan"
3. تعبئة بيانات الخطة
4. إضافة التاسكات
5. الضغط على "Submit for Review"

### النتيجة:
- ✅ جميع التاسكات الحالة: `SUBMITTED_TO_SURVEILLANCE`
- ✅ تُرسل الإشعارات للـ Surveillance Supervisor

---

## 2️⃣ المرحلة الثانية: Surveillance Supervisor يصنف وينسق التاسكات
## Phase 2: Surveillance Supervisor Reviews & Assigns

### الخطوات:
1. دخول الـ Dashboard
2. الذهاب إلى الخطة تحت "Surveillance Supervisor"
3. مراجعة كل تاسك
4. الضغط على "Assign to MPFM" لكل تاسك

### النتيجة:
- ✅ التاسكات الحالة: `ASSIGNED_TO_MPFM`
- ✅ تُسند إلى أحد أو أكثر من الـ operators

---

## 3️⃣ المرحلة الثالثة: Operator يقوم بتنفيذ التاسك
## Phase 3: MPFM Operator Executes Task

### الخطوات الاساسية:

#### أ) عرض التاسكات المسندة (Operator Dashboard)
```
URL: http://localhost/MPFM/public/operator/dashboard
```

**ما يرى الـ Operator:**
- ✅ جميع التاسكات المسندة له فقط (من خلال many-to-many relationship)
- ✅ تقسيم التاسكات:
  - "My Tasks for Today" - التاسكات اليوم فقط
  - "All Pending Tasks" - جميع التاسكات الانتظار
  - "Performance Metrics" - إحصائيات الأداء
- ✅ لكل تاسك:
  - معلومات الـ Field و Tower و String
  - MPFM Device المطلوب
  - الأولوية (Priority)
  - الحالة (Status)
  - زر "Start Task" للتاسكات المعلقة

#### ب) بدء التاسك
```
1. الضغط على زر "Start Task" في Dashboard أو من جدول التاسكات
2. النظام يوجه المستخدم إلى: /upload?task_id=X
```

**ما يحدث تلقائياً:**
- ✅ حالة التاسك تتغير إلى: `IN_PROGRESS`
- ✅ يتم تسجيل تاريخ البدء
- ✅ يتم تحديث SLA status (إن كان معرفاً)

#### ج) تحميل ملف Log
```
URL: http://localhost/MPFM/public/upload?task_id=X
```

**ما يرى الـ Operator:**
```
┌─────────────────────────────────────────────┐
│        Upload Test Log                      │
├─────────────────────────────────────────────┤
│  Task Context Information:                  │
│  ├─ Field: Oil Field Onshore Field Alpha       │
│  ├─ Tower: Tower 1                         │
│  ├─ String: String A1                      │
│  └─ MPFM Device: MPFM Type A               │
│                                             │
│  Expected Values:                           │
│  ├─ Oil Rate: 100.00 STB/d                 │
│  ├─ Gas Rate: 250.00 MMScf/d               │
│  └─ Water Cut: 15.50%                      │
│                                             │
│  [File Upload Area]                        │
│  Drag & Drop or Click to Select File       │
│                                             │
│  [Upload Button]                           │
└─────────────────────────────────────────────┘
```

**معلومات التاسك تملأ تلقائياً من:**
- `$task->towerModel->oilField->name` → Field
- `$task->towerModel->name` → Tower
- `$task->oilString->name` → String
- `$task->mpfm_device` → MPFM Device
- `$task->expected_oil_rate` → Expected Oil Rate
- `$task->expected_gas_rate` → Expected Gas Rate
- `$task->expected_water_cut` → Expected Water Cut
- `$task->task_instructions` → Task Instructions

#### د) ما يحدث عند رفع الملف

```php
// في: VxLogController::processLogFile()

1. ✅ قراءة وتحليل الملف
2. ✅ استخراج البيانات من Log
3. ✅ عرض النتائج الأولية للمراجعة
4. ✅ تحديث حالة التاسك: ASSIGNED_TO_MPFM → IN_PROGRESS → MPFM_REVIEWED
```

**معادلة الحالة:**
```
Before Upload:  state = ASSIGNED_TO_MPFM, completion_status = pending
                ↓
During Upload:  - File is processed
                - Data is extracted
                ↓
After Upload:   state = MPFM_REVIEWED, completion_status = in_progress
                - Task waiting for MPFM Supervisor review
```

---

## 4️⃣ المرحلة الرابعة: MPFM Supervisor يراجع النتائج
## Phase 4: MPFM Supervisor Reviews Results

### الخطوات:
1. دخول الـ MPFM Supervisor Dashboard
2. الذهاب إلى "Tasks Pending Review"
3. مراجعة النتائج المرفوعة من الـ Operator
4. الخيارات:
   - ✅ "Approve" → نقل التاسك إلى `SURVEILLANCE_REVIEWED`
   - ❌ "Reject/Send Back" → نقل التاسك إلى `IN_PROGRESS` (للمعاودة)

### النتيجة:
- ✅ إذا وافق: `MPFM_REVIEWED` → `SURVEILLANCE_REVIEWED`
- ❌ إذا رفض: `MPFM_REVIEWED` → `IN_PROGRESS` (عودة للـ Operator)

---

## 5️⃣ المرحلة الخامسة: Surveillance Supervisor يوافق نهائياً
## Phase 5: Surveillance Supervisor Final Review

### الخطوات:
1. دخول Surveillance Supervisor Dashboard
2. مراجعة التاسكات في حالة `SURVEILLANCE_REVIEWED`
3. الموافقة النهائية

### النتيجة:
- ✅ `SURVEILLANCE_REVIEWED` → `JOB_OFFICER_APPROVED`

---

## 6️⃣ المرحلة السادسة: Job Officer يعتمد النتيجة النهائية
## Phase 6: Job Officer Final Approval

### الخطوات:
1. دخول Job Officer Dashboard
2. مراجعة التاسكات المنتظرة الاعتماد
3. الضغط على "Approve"

### النتيجة:
- ✅ `JOB_OFFICER_APPROVED` → `COMPLETED`
- ✅ التاسك انتهى بنجاح!

---

## 📊 ملخص الحالات (States Summary)

| الحالة | الوصف | من يرى؟ | الإجراء |
|--------|-------|---------|---------|
| DRAFT | تاسك جديد - مسودة | Job Officer | تعديل/حذف |
| SUBMITTED_TO_SURVEILLANCE | مرسل للمراجعة | Surveillance Sup. | تصنيف/تعديل |
| ASSIGNED_TO_MPFM | مسند للـ Operator | Operator | بدء التنفيذ |
| IN_PROGRESS | قيد التنفيذ | Operator | رفع الملف |
| MPFM_REVIEWED | في مراجعة المشرف | MPFM Supervisor | الموافقة/الرفض |
| SURVEILLANCE_REVIEWED | في مراجعة المراقبة | Surveillance Sup. | موافقة نهائية |
| JOB_OFFICER_APPROVED | معتمد | Job Officer | التأكيد النهائي |
| COMPLETED | منتهي | جميع المستخدمين | عرض النتيجة فقط |

---

## 🔄 تدفق قاعدة البيانات
## Database Flow

### جدول: `plan_tasks`
```
create_by: Job Officer
├─ monthly_plan_id: FK → monthly_plans
├─ assigned_operator_id: FK → users (Primary Operator)
├─ surveillance_supervisor_id: FK → users
├─ state: ENUM (TaskState)
├─ completion_status: STRING (pending/in_progress/completed)
├─ state_history: many-to-many relationship
└─ sla_history: one-to-one relationship
```

### جدول: `plan_task_operators`
```
Many-to-Many Relationship
├─ plan_task_id: FK → plan_tasks
├─ user_id: FK → users
├─ assigned_at: timestamp
├─ assigned_by: user_id
└─ is_primary: boolean
```

### جدول: `task_state_histories`
```
Audit Trail Table
├─ plan_task_id: FK → plan_tasks
├─ from_state: TaskState enum
├─ to_state: TaskState enum
├─ changed_by: user_id
├─ changed_by_role: string
├─ notes: text
└─ created_at: timestamp
```

---

## 🔐 التصريحات (Permissions Matrix)

### Job Officer
```
✅ view_monthly_plans
✅ create_monthly_plans
✅ submit_for_review
✅ approve_final
✅ view_job_officer_reports
```

### Surveillance Supervisor
```
✅ view_submitted_plans
✅ assign_to_mpfm
✅ send_back_to_draft (for corrections)
✅ pre_final_review
```

### MPFM Supervisor
```
✅ view_assigned_tasks
✅ review_operator_work
✅ approve_mpfm_work
✅ reject_and_send_back
```

### MPFM Operator
```
✅ view_assigned_tasks
✅ start_task
✅ upload_log_file
✅ view_my_tasks
```

---

## ⚙️ كود التنفيذ (Implementation Code)

### 1. تحديث التاسك الحالة (في VxLogController)
```php
// عند رفع الملف:
$this->taskStateService->transition(
    $task,
    TaskState::MPFM_REVIEWED,  // الحالة الجديدة
    auth()->user(),            // من يقوم بالتحديث
    'Test log file uploaded: ' . $file->getClientOriginalName()
);
```

### 2. عرض التاسكات المسندة للـ Operator
```php
// في OperatorDashboardController:
$todaysTasks = PlanTask::whereHas('operators', function($q) use ($operator) {
    $q->where('user_id', $operator->id);
})
->today()
->with(['monthlyPlan', 'towerModel', 'oilString', 'operators'])
->get();
```

### 3. فحص إذا كان المستخدم مُسند لهذا التاسك
```php
// في showUploadForm:
if (!$task->isAssignedTo(auth()->id())) {
    abort(403, 'Unauthorized access to task');
}
```

---

## 🧪 اختبار التدفق

### سيناريو الاختبار الكامل:

```bash
# 1. دخول كـ Job Officer
Email: job.officer@oilfield.com
Password: password

# 2. إنشاء Monthly Plan
→ Monthly Plans → Create New Plan
→ Add 5 Tasks
→ Submit for Review

# 3. دخول كـ Surveillance Supervisor
Email: ?@oilfield.com (تحتاج تعيين هذا الدور)
Password: password

# 4. مراجعة والتصنيف
→ Plans for Review
→ Assign to MPFM

# 5. دخول كـ Operator
Email: operator1@oilfield.com
Password: password

# 6. تنفيذ التاسك
→ Operator Dashboard
→ "Start Task"
→ Upload Log File

# 7. دخول كـ MPFM Supervisor
Email: supervisor@oilfield.com
Password: password

# 8. مراجعة النتائج
→ Tasks Pending Review
→ Approve

# 9. دخول كـ Job Officer
→ Final Approval
→ Task Completed!
```

---

## 📱 الواجهات المستخدمة

| الصفحة | الـ URL | المستخدم | الوظيفة |
|-------|--------|---------|---------|
| Dashboard | `/dashboard` | الجميع | لوحة رئيسية |
| Monthly Plans | `/monthly-plans` | Job Officer | إنشاء خطط |
| Plan Details | `/monthly-plans/{id}` | الجميع | عرض التفاصيل |
| Operator Dashboard | `/operator/dashboard` | Operator | عرض تاسكاته |
| Upload Log | `/upload?task_id=X` | Operator | رفع الملف |
| MPFM Supervisor | `/mpfm-supervisor/dashboard` | MPFM Sup. | مراجعة العمل |
| SLA Monitoring | `/sla/dashboard` | Admin/Supervisors | مراقبة SLA |

---

## ✅ قائمة التحقق (Checklist)

### ✅ تم تنفيذه:
- [x] عرض التاسكات المسندة للـ Operator فقط
- [x] زر "Start Task" يحول الحالة إلى IN_PROGRESS
- [x] صفحة `/upload` تملأ البيانات تلقائياً من التاسك
- [x] رفع الملف ينقل التاسك إلى MPFM_REVIEWED
- [x] State machine workflow كامل

### 🔜 يمكن إضافته لاحقاً:
- [ ] بريد إلكتروني تنبيهات عند كل transition
- [ ] Dashboard لـ Supervisor يعرض التاسكات المنتظرة المراجعة
- [ ] أزرار الموافقة/الرفض على Dashboard
- [ ] تقارير مفصلة عن السير

---

**آخر تحديث**: 23 يناير 2026
**الإصدار**: 2.0
**الحالة**: ✅ جاهز للاستخدام
