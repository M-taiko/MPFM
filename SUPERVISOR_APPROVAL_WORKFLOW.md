# Supervisor Approval Workflow Documentation

## نظام الموافقات متعدد المستويات للتيستات المختلفة

### 📋 النظرة العامة

نظام متطور للموافقات على تيستات MPFM من قبل المشرفين المختلفين مع تتبع كامل للحالات والإشعارات الآلية.

---

## 🔄 حالات الموافقة (Approval States)

### 1. **Draft** (المسودة)
- الحالة الأولية عند إنشاء التيست
- الأوبريتور يمكنه تعديل التيست
- غير مرئي للمشرفين

### 2. **Submitted** (مُرسل)
- التيست تم رفعه وجاهز للموافقة
- مرئي لـ Department Supervisor والـ Surveillance Supervisor

### 3. **Approved by Department** (موافق من رئيس القسم)
- تم الموافقة من قبل Department Supervisor
- ينتقل التيست إلى Surveillance Supervisor
- في انتظار مراجعة Surveillance Supervisor

### 4. **Approved by Surveillance** (موافق من السورفيلنس)
- تم الموافقة من قبل Surveillance Supervisor
- يتم تخطي Department Supervisor إذا وافق الـ Surveillance Supervisor أولاً
- ينتقل التيست مباشرة إلى Job Officer
- الحالة النهائية للموافقات

### 5. **Rejected** (مرفوض)
- تم رفض التيست من أحد المشرفين
- يُرسل إشعار للأوبريتور مع سبب الرفض
- يمكن إعادة التحميل والمحاولة مرة أخرى

### 6. **Retest Requested** (طلب إعادة اختبار)
- تم طلب إعادة اختبار بدلاً من الرفض
- يتم إنشاء task جديد للأوبريتور
- التيست الأصلي محفوظ للمرجعية

---

## 👥 الأدوار والمسؤوليات

### Department Supervisor (مشرف القسم)
**الصلاحيات:**
- عرض تيستات القسم الخاص به
- الموافقة على التيستات ✓
- الرفض مع تحديد السبب ✗
- طلب إعادة اختبار 🔄

**الإجراء عند الموافقة:**
```
submitted → approved_by_department
           ↓
    forwarded to Surveillance Supervisor
```

**الإشعارات المُرسلة:**
- ✉️ يُشعر Surveillance Supervisor عن الموافقة

---

### Surveillance Supervisor (مشرف السورفيلنس)
**الصلاحيات:**
- عرض جميع تيستات النطاق
- الموافقة على التيستات ✓
- الرفض مع تحديد السبب ✗
- طلب إعادة اختبار 🔄

**الإجراء عند الموافقة:**
```
submitted OR approved_by_department → approved_by_surveillance
                                     ↓
                         (skip Department level if needed)
                         forwarded to Job Officer
```

**الإشعارات المُرسلة:**
- ✉️ يُشعر Department Supervisor بالموافقة
- ✉️ يُشعر Job Officer أن التيست جاهز
- ✉️ يُشعر Operator أن التيست موافق عليه

---

### Operator (الأوبريتور)
**الصلاحيات:**
- رفع التيست الجديد
- مراجعة حالة التيست
- القيام بإعادة الاختبار عند الطلب

**الإشعارات المستقبلة:**
- ✉️ تأكيد الرفع الناجح
- ✉️ اشعار الرفض مع السبب
- ✉️ طلب إعادة الاختبار مع التعليمات

---

## 📊 سيناريوهات الموافقة

### السيناريو 1: موافقة العميل من مستويين
```
Step 1: Operator uploads test
   ↓
Step 2: Test appears to Department Supervisor
   ↓
Step 3: Department Supervisor reviews and APPROVES
   ├─ Status: approved_by_department
   └─ Notification: Surveillance Supervisor
   ↓
Step 4: Surveillance Supervisor receives notification
   ↓
Step 5: Surveillance Supervisor reviews and APPROVES
   ├─ Status: approved_by_surveillance
   ├─ Notification: Job Officer (test is ready)
   ├─ Notification: Department Supervisor (test approved)
   └─ Notification: Operator (test approved)
```

---

### السيناريو 2: موافقة من Surveillance مباشرة (تخطي Department)
```
Step 1: Operator uploads test
   ↓
Step 2: Test appears to both supervisors
   ↓
Step 3: Surveillance Supervisor reviews and APPROVES (first)
   ├─ Status: approved_by_surveillance (auto-approved)
   ├─ Notification: Job Officer (test is ready)
   ├─ Notification: Department Supervisor (test approved)
   └─ Notification: Operator (test approved)

Note: Department Supervisor notification arrives after approval
```

---

### السيناريو 3: الرفض مع إعادة التحميل
```
Step 1: Supervisor reviews test
   ↓
Step 2: Supervisor clicks "Reject Test"
   ├─ Status: rejected
   ├─ Reason: data_quality (مثال)
   ├─ Details: "تم اكتشاف نقاط بيانات غير صحيحة..."
   └─ Notification: Operator receives detailed rejection reason
   ↓
Step 3: Operator re-uploads corrected test
   ├─ Creates new test record
   ├─ Status: submitted
   └─ Cycle repeats from Step 2
```

---

### السيناريو 4: طلب إعادة اختبار كامل
```
Step 1: Supervisor reviews test
   ↓
Step 2: Supervisor clicks "Request Re-test"
   ├─ Selects reason (data_inconsistency, equipment_suspect, etc.)
   ├─ Provides detailed instructions
   ├─ Sets expected re-test date
   ├─ Sets priority level
   └─ Status: retest_requested
   ↓
Step 3: New Task created automatically
   ├─ Task ID: auto-generated
   ├─ Title: "Re-test MPFM - [String Name] (Test #123)"
   ├─ Assigned to: Original Operator
   ├─ Description: Supervisor's instructions
   ├─ Expected completion: Selected date
   └─ Priority: Selected level
   ↓
Step 4: Notifications sent
   ├─ Operator: "New re-test task with instructions"
   ├─ Department Supervisor: "Re-test task created"
   └─ Original Test marked as: retest_requested
   ↓
Step 5: Operator performs new test
   ├─ Creates new test record (with reference to original)
   ├─ Status: submitted (new cycle begins)
   └─ Original test remains in database for history
```

---

## 🔔 نظام الإشعارات

### أنواع الإشعارات

#### 1. **Test Awaiting Surveillance Approval**
- **المُستقبل:** Surveillance Supervisor
- **الحالة:** approved_by_department
- **المحتوى:**
  - معلومات التيست الأساسية
  - البيانات الرئيسية (معدلات الإنتاج)
  - رابط للمراجعة

#### 2. **Test Approved by Surveillance**
- **المُستقبل:** Department Supervisor
- **الحالة:** approved_by_surveillance
- **المحتوى:**
  - تأكيد الموافقة
  - معلومات التيست
  - حالة العملية

#### 3. **Test Ready for Job Officer Review**
- **المُستقبل:** Job Officer
- **الحالة:** approved_by_surveillance
- **المحتوى:**
  - معلومات التيست الكاملة
  - جميع البيانات المهمة
  - رابط للمراجعة

#### 4. **Test Approved Notification**
- **المُستقبل:** Operator
- **الحالة:** approved_by_surveillance
- **المحتوى:**
  - تأكيد الموافقة
  - معلومات التيست
  - الخطوة التالية

#### 5. **Test Rejected Notification**
- **المُستقبل:** Operator
- **الحالة:** rejected
- **المحتوى:**
  - سبب الرفض المفصل
  - تعليمات الإجراء المطلوب
  - أمثلة على ما يجب تصحيحه

#### 6. **Retest Requested Notification**
- **المُستقبل:** Operator
- **الحالة:** retest_requested
- **المحتوى:**
  - سبب طلب الإعادة
  - تعليمات مفصلة
  - تاريخ الاستحقاق
  - معلومات المهمة الجديدة

#### 7. **Retest Task Created Notification**
- **المُستقبل:** Department Supervisor
- **الحالة:** retest_requested
- **المحتوى:**
  - معلومات المهمة الجديدة
  - معلومات التيست الأصلي
  - تفاصيل المتابعة

---

## 🛣️ Routes الـ Approval

### POST Routes

#### 1. Approve by Department Supervisor
```
POST /mpfm-tests/{mpfmTest}/approve-by-department
Middleware: can:approve-test-as-department

Request Body:
{
    "approval_notes": "Optional comments about the test"
}

Response:
- Status: 302 Redirect
- Message: "Test approved by Department. Forwarded to Surveillance Supervisor for review."
```

#### 2. Approve by Surveillance Supervisor
```
POST /mpfm-tests/{mpfmTest}/approve-by-surveillance
Middleware: can:approve-test-as-surveillance

Request Body:
{
    "approval_notes": "Optional comments about the test"
}

Response:
- Status: 302 Redirect
- Message: "Test approved by Surveillance. Automatically forwarded to Job Officer."
```

#### 3. Reject Test
```
POST /mpfm-tests/{mpfmTest}/reject
Middleware: can:reject-test

Request Body:
{
    "rejection_reason": "invalid_data|equipment_failure|test_interrupted|calibration_issue|environmental_conditions|data_quality|other",
    "rejection_details": "Detailed explanation for the operator"
}

Response:
- Status: 302 Redirect
- Message: "Test rejected. Operator has been notified to re-upload."
```

#### 4. Request Re-test
```
POST /mpfm-tests/{mpfmTest}/request-retest
Middleware: can:request-retest

Request Body:
{
    "retest_reason": "data_inconsistency|incomplete_test|equipment_suspect|validation_failed|trend_unusual|anomaly_detected|other",
    "retest_details": "Detailed instructions for the re-test",
    "expected_retest_date": "2026-02-15",
    "retest_priority": "high|normal|low"
}

Response:
- Status: 302 Redirect
- Message: "Re-test task created and operator notified."
- Side Effect: New Task created with auto-generated ID
```

---

## 💾 Database Fields

### MPFMTest Table - New Columns

```sql
-- Approval Status Fields
department_supervisor_id          BIGINT UNSIGNED NULL
surveillance_supervisor_id         BIGINT UNSIGNED NULL
approved_by_department_at         TIMESTAMP NULL
approved_by_surveillance_at       TIMESTAMP NULL

-- Rejection Fields
rejected_by_user_id               BIGINT UNSIGNED NULL
rejected_by_role                  VARCHAR(255) NULL
rejected_reason                   VARCHAR(255) NULL
rejected_details                  LONGTEXT NULL
rejected_at                       TIMESTAMP NULL

-- Re-test Request Fields
retest_requested_by_user_id       BIGINT UNSIGNED NULL
retest_reason                     VARCHAR(255) NULL
retest_details                    LONGTEXT NULL
retest_requested_at               TIMESTAMP NULL

-- General Fields
supervisor_remarks                LONGTEXT NULL
```

---

## 🔐 Authorization & Policies

### Controller Middleware

Policies need to be created in `app/Policies/`:

```php
// MPFMTestPolicy.php

public function approveTestAsDepartment(User $user, MPFMTest $test)
{
    return $user->hasRole('department_supervisor') &&
           $user->department_id === $test->department_id;
}

public function approveTestAsSurveillance(User $user, MPFMTest $test)
{
    return $user->hasRole('surveillance_supervisor');
}

public function rejectTest(User $user, MPFMTest $test)
{
    return $user->hasRole(['department_supervisor', 'surveillance_supervisor', 'hq_supervisor']) &&
           ($user->department_id === $test->department_id || $user->hasRole('hq_supervisor'));
}

public function requestRetest(User $user, MPFMTest $test)
{
    return $user->hasRole(['department_supervisor', 'surveillance_supervisor', 'hq_supervisor']) &&
           ($user->department_id === $test->department_id || $user->hasRole('hq_supervisor'));
}
```

---

## 📱 UI Components

### Supervisor Review Modal
**Location:** `resources/views/mpfm_tests/supervisor-review-modal.blade.php`

**Features:**
- Test summary display
- Key metrics preview
- Three action buttons:
  - ✓ Approve Test
  - ✗ Reject Test
  - 🔄 Request Re-test
- Optional remarks/comments field

**Visibility:**
- Shows only when user has `department_supervisor` or `surveillance_supervisor` role
- Only appears when test status is `submitted` or `approved_by_department`

### Modal sub-windows:
1. **Reject Reason Modal:** Select reason + provide details
2. **Re-test Request Modal:** Reason + instructions + date + priority
3. **Approve Confirmation Modal:** Review workflow, add comments

---

## 🔍 Activity Logging

Every action creates an Activity log entry:

```php
Activity::create([
    'subject_type' => MPFMTest::class,
    'subject_id' => $mpfmTest->id,
    'description' => 'Action description',
    'causer_id' => auth()->id(),
    'properties' => [
        'from_status' => 'old_status',
        'to_status' => 'new_status',
        'additional_data' => 'value',
    ],
]);
```

---

## 📋 Rejection Reasons

- **invalid_data** - البيانات غير صحيحة أو غير متسقة
- **equipment_failure** - فشل المعدات
- **test_interrupted** - التيست انقطع ولم يكمل
- **calibration_issue** - مشكلة في المعايرة
- **environmental_conditions** - الظروف البيئية السيئة
- **data_quality** - جودة البيانات منخفضة
- **other** - أخرى (مع شرح مفصل)

---

## 📋 Re-test Request Reasons

- **data_inconsistency** - عدم اتساق البيانات
- **incomplete_test** - التيست لم يكن مكتملاً
- **equipment_suspect** - أداء المعدات مريب
- **validation_failed** - فشل التحقق من الصحة
- **trend_unusual** - اتجاهات إنتاجية غير عادية
- **anomaly_detected** - تم اكتشاف شذوذ في البيانات
- **other** - أخرى (مع شرح مفصل)

---

## 🚀 القيام بالموافقة خطوة بخطوة

### للـ Department Supervisor:

1. الذهاب إلى `http://127.0.0.1:8000/mpfm-tests`
2. البحث عن التيست المُرسل
3. النقر على عنوان التيست لعرض التفاصيل
4. النقر على زر **"Supervisor Review"** (أخضر)
5. مراجعة البيانات والمقاييس
6. اختيار أحد الخيارات الثلاثة:
   - **Approve Test:** الموافقة والانتقال إلى Surveillance
   - **Reject Test:** الرفض مع تحديد السبب
   - **Request Re-test:** طلب إعادة اختبار كامل

### للـ Surveillance Supervisor:

نفس الخطوات، لكن النتيجة مختلفة:
- الموافقة: ينتقل مباشرة إلى Job Officer
- الرفض: إشعار الـ Operator
- الإعادة: إنشاء task جديد

---

## 🧪 Testing الـ Workflow

### تسلسل اختبار كامل:

```bash
# 1. Create test as Operator
POST /mpfm-tests (upload test)

# 2. Department Supervisor approves
POST /mpfm-tests/{id}/approve-by-department

# 3. Surveillance Supervisor approves
POST /mpfm-tests/{id}/approve-by-surveillance

# Verify notifications sent to all parties
Check notifications table and email logs

# 4. Verify test status transitioned correctly
GET /mpfm-tests/{id}
Response should show: status = "approved_by_surveillance"
```

---

## 📞 Support & Troubleshooting

### الأزرار لا تظهر:
- تأكد أن المستخدم لديه دور `department_supervisor` أو `surveillance_supervisor`
- تأكد أن التيست في الحالة `submitted` أو `approved_by_department`

### الإشعارات لا تصل:
- تحقق من تكوين البريد الإلكتروني
- تحقق من أن المستخدمين لديهم عناوين بريد صحيحة
- تحقق من سجلات الـ Activity

### Task لم يُنشأ عند طلب الإعادة:
- تحقق من صحة التاريخ المتوقع
- تحقق من صحة بيانات المستخدم المعين

---

**آخر تحديث:** 2026-01-29
**الإصدار:** 1.0
**الحالة:** ✅ Production Ready
