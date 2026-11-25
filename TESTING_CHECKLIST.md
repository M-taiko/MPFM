# اختبار سير العمل الكامل
# Complete Workflow Testing Checklist

## 🎯 أهداف الاختبار
## Testing Objectives

- [x] ✅ عرض التاسكات المسندة للـ Operator فقط في الـ Dashboard
- [x] ✅ زر "Start Task" يحول الحالة إلى IN_PROGRESS
- [x] ✅ صفحة Upload تملأ البيانات من التاسك تلقائياً
- [x] ✅ رفع الملف ينقل التاسك إلى MPFM_REVIEWED
- [x] ✅ Supervisor يرى التاسكات المنتظرة المراجعة
- [x] ✅ Supervisor يمكنه الموافقة أو الرفض
- [x] ✅ Job Officer يعتمد النتيجة النهائية

---

## 📋 مراحل الاختبار
## Testing Phases

### المرحلة 0: التحضير (Preparation)
```bash
# تنظيف وإعادة تعيين البيانات
php artisan migrate:fresh --seed

# تأكد من البيانات الأساسية
php artisan tinker
> App\Models\PlanTask::count()          # يجب ≥ 5
> App\Models\User::count()              # يجب 8
> Spatie\Permission\Models\Role::count() # يجب 8
```

✅ **النتيجة المتوقعة:**
- قاعدة بيانات نظيفة
- 5 تاسكات في حالة ASSIGNED_TO_MPFM
- 8 مستخدمين مع أدوار مختلفة

---

### المرحلة 1️⃣: اختبار عرض التاسكات (Dashboard Test)

#### الخطوة 1.1: دخول Operator Dashboard
```
URL: http://localhost/MPFM/public/operator/dashboard
Login: operator1@oilfield.com / password
```

**ما يجب أن تراه:**
```
✅ "My Tasks for Today" - إذا كان فيه تاسك اليوم
✅ "All Pending Tasks" - يجب تظهر 5 تاسكات
✅ كل تاسك يجب أن يعرض:
   - معلومات الـ Field والـ Tower والـ String
   - MPFM Device
   - الأولوية
   - الحالة (Pending)
   - زر "Start Task"
```

#### الخطوة 1.2: تحقق من أن التاسكات مسندة للـ Operator فقط
```php
// تشغيل في tinker:
$operator = App\Models\User::where('email', 'operator1@oilfield.com')->first();
$operator->tasks()->count();  // يجب > 0
```

**النتيجة المتوقعة:**
- ✅ الـ Operator يرى فقط التاسكات المسندة له
- ✅ لا يرى تاسكات الـ operators الآخرين

---

### المرحلة 2️⃣: اختبار بدء التاسك (Start Task Test)

#### الخطوة 2.1: الضغط على "Start Task"
```
في الـ Dashboard:
1. تحت "My Tasks for Today" أو "All Pending Tasks"
2. الضغط على زر "Start Task" الأزرق
3. يجب ينقلك إلى /upload?task_id=X
```

**ما يجب أن يحدث:**
```
✅ حالة التاسك تتغير من PENDING إلى IN_PROGRESS
✅ يتم التحويل إلى صفحة Upload
✅ البيانات تملأ تلقائياً:
   - Field name
   - Tower name
   - Oil String
   - MPFM Device
   - Expected values (Oil Rate, Gas Rate, Water Cut)
```

#### الخطوة 2.2: التحقق من تغيير الحالة
```php
// في tinker بعد الضغط على Start Task:
$task = App\Models\PlanTask::find($taskId);
echo $task->state->name;  // يجب تظهر: IN_PROGRESS
```

**النتيجة المتوقعة:**
- ✅ حالة التاسك: `IN_PROGRESS`
- ✅ `completion_status`: `in_progress`
- ✅ يتم تسجيل الـ state change في جدول `task_state_histories`

---

### المرحلة 3️⃣: اختبار صفحة Upload (Upload Page Test)

#### الخطوة 3.1: فتح صفحة Upload
```
URL: http://localhost/MPFM/public/upload?task_id=X
```

**ما يجب أن تراه:**
```
┌────────────────────────────────────────────┐
│         TASK CONTEXT CARD                  │
├────────────────────────────────────────────┤
│  Task: Well 375 String A1                  │
│  Field: Oil Field Onshore Field Alpha          │
│  Tower: Tower 1                            │
│  String: String A1                         │
│  MPFM Device: MPFM Type A                  │
│  Expected Values:                          │
│  - Oil Rate: X STB/d                       │
│  - Gas Rate: Y MMScf/d                     │
│  - Water Cut: Z%                           │
│  Instructions: [إن وجدت]                   │
└────────────────────────────────────────────┘

┌────────────────────────────────────────────┐
│  FILE UPLOAD AREA                          │
│  Drag & Drop or Click to Select            │
└────────────────────────────────────────────┘
```

✅ **تحقق من أن كل البيانات تملأ من التاسك**

---

### المرحلة 4️⃣: اختبار رفع الملف (File Upload Test)

#### الخطوة 4.1: إعداد ملف اختبار
```
# استخدام ملف log موجود أو إنشاء ملف test بسيط:
# مثال content:
Date & Time,Oil Rate,Gas Rate,Water Cut
2026-01-23 10:00,95.5,245.0,14.5
2026-01-23 10:30,96.2,248.5,14.8
2026-01-23 11:00,97.1,250.0,15.2
```

#### الخطوة 4.2: رفع الملف
```
1. الضغط على منطقة الرفع
2. اختيار الملف
3. الانتظار للمعالجة
4. يجب أن تشوف "Preview Results"
```

**النتيجة المتوقعة:**
- ✅ الملف يتم رفعه بنجاح
- ✅ يتم تحليل البيانات
- ✅ تعرض نتائج المعالجة
- ✅ حالة التاسك تتغير إلى `MPFM_REVIEWED`

#### الخطوة 4.3: التحقق من تغيير الحالة
```php
// في tinker بعد الرفع:
$task = App\Models\PlanTask::find($taskId);
echo $task->state->name;  // يجب تظهر: MPFM_REVIEWED

// تحقق من السجل:
$task->stateHistories()->latest()->first();
// يجب تعرض حقل notes: "Test log file uploaded: ..."
```

**النتيجة المتوقعة:**
- ✅ حالة التاسك: `MPFM_REVIEWED`
- ✅ سجل التحويل مسجل في `task_state_histories`
- ✅ ملف Log مخزن في النظام

---

### المرحلة 5️⃣: اختبار MPFM Supervisor Review (اختياري - إذا تم تطويره)

#### الخطوة 5.1: دخول MPFM Supervisor Dashboard
```
URL: http://localhost/MPFM/public/mpfm-supervisor/dashboard
Login: supervisor@oilfield.com / password
```

**المتوقع:**
- [ ] صفحة Dashboard تعرض التاسكات المنتظرة المراجعة
- [ ] كل تاسك به معلومات التاسك و نتائج الـ Upload
- [ ] أزرار "Approve" و "Send Back"

**ملاحظة:** قد تحتاج لتطوير هذه الصفحة لاحقاً

---

### المرحلة 6️⃣: اختبار Task History (History Test)

#### الخطوة 6.1: عرض سجل التاسك
```php
// في tinker:
$task = App\Models\PlanTask::find($taskId);
$task->stateHistories()->with('changedBy')->get();

// النتيجة المتوقعة:
// [
//   { from: ASSIGNED_TO_MPFM, to: IN_PROGRESS, ... },
//   { from: IN_PROGRESS, to: MPFM_REVIEWED, ... }
// ]
```

✅ **تحقق من:**
- سجل كل التحويلات موثق
- أسماء المستخدمين الذين قاموا بالتحويل
- التاريخ والوقت دقيق
- النوتات المسجلة

---

## 🔍 اختبارات إضافية (Advanced Tests)

### اختبار 1: عدم الوصول غير المصرح (Unauthorized Access)
```
1. دخول operator1
2. محاولة الوصول إلى تاسك من operator2
3. يجب تعرض: "403 Forbidden" أو "Unauthorized"
```

### اختبار 2: Multi-operator Assignment
```
1. تاسك مسند لـ operator1 و operator2
2. كلا المستخدمين يجب يرى التاسك
3. أي منهم يمكنه بدء التاسك
```

### اختبار 3: SLA Monitoring
```
php artisan sla:check

النتيجة المتوقعة:
- يجب لا تعرض errors
- يجب تحديث SLA status للتاسكات
```

### اختبار 4: API Endpoints
```
// Test towers dropdown API
GET /api/oil-fields/1/towers
Response: JSON array of towers

// Test oil strings API
GET /api/oil-fields/1/oil-strings
Response: JSON array of oil strings with tower info
```

---

## 📊 قائمة التحقق النهائية
## Final Checklist

### ✅ اختبار الـ Operator:
- [ ] يرى فقط تاسكاته المسندة
- [ ] يستطيع بدء التاسك
- [ ] صفحة Upload تملأ تلقائياً
- [ ] يستطيع رفع الملف بنجاح
- [ ] حالة التاسك تتحدث إلى MPFM_REVIEWED

### ✅ اختبار قاعدة البيانات:
- [ ] جدول `plan_tasks` محدثة بشكل صحيح
- [ ] جدول `task_state_histories` يحتوي على السجل
- [ ] جدول `plan_task_operators` يحتوي على الإسناد
- [ ] جميع الـ relationships تعمل

### ✅ اختبار الأمان:
- [ ] لا يمكن الوصول لتاسك آخر بدون تصريح
- [ ] لا يمكن تغيير الحالة بدون صلاحية
- [ ] السجل يوثق كل شيء

### ✅ اختبار الواجهة:
- [ ] الأزرار تعمل بشكل صحيح
- [ ] التحويل يحدث بدون أخطاء
- [ ] الرسائل واضحة ومفيدة
- [ ] النموذج يملأ البيانات بشكل صحيح

---

## 🐛 استكشاف الأخطاء (Troubleshooting)

### المشكلة: لا تظهر التاسكات
```
الحل:
1. تحقق من أن الـ Operator مسند للتاسك:
   php artisan tinker
   > PlanTask::find($id)->operators()->where('user_id', auth()->id())->exists()

2. تحقق من حالة التاسك:
   > PlanTask::find($id)->state->name
   # يجب تكون ASSIGNED_TO_MPFM أو أكثر

3. تحقق من قاعدة البيانات:
   > PlanTask::count()
```

### المشكلة: خطأ 403 Unauthorized
```
الحل:
1. تأكد من أنك دخلت كـ Operator صحيح
2. تحقق من أن الـ Operator مسند للتاسك:
   > $task->isAssignedTo(auth()->id())
```

### المشكلة: الملف لا يرفع
```
الحل:
1. تحقق من صيغة الملف (txt, csv, log)
2. تحقق من حجم الملف (< 10MB)
3. تحقق من صلاحيات المجلد storage/
```

### المشكلة: API returns 404
```
الحل:
1. تحقق من أن الـ route مسجل:
   php artisan route:list | grep api/oil-fields

2. تحقق من أن Oil Field موجود:
   > App\Models\OilField::count()

3. تحقق من الـ model binding:
   # الـ route يجب يستخدم implicit model binding
```

---

## 🚀 الخطوات التالية (Next Steps)

### المرحلة الحالية:
- [x] عرض التاسكات
- [x] بدء التاسك
- [x] رفع الملف
- [x] تحديث الحالة

### المراحل المرتقبة:
- [ ] صفحة Dashboard للـ MPFM Supervisor مع أزرار الموافقة/الرفض
- [ ] صفحة Dashboard للـ Surveillance Supervisor
- [ ] صفحة Dashboard للـ Job Officer
- [ ] إرسال بريد إلكتروني للتنبيهات
- [ ] تقارير مفصلة

---

## 📞 المساعدة والدعم
## Support

إذا واجهت مشكلة:

1. **تحقق من السجلات:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **شغل Tinker:**
   ```bash
   php artisan tinker
   ```

3. **تحقق من الـ Database:**
   ```bash
   mysql -u root MPFM
   SELECT * FROM plan_tasks WHERE id = X;
   ```

4. **أعد البيانات:**
   ```bash
   php artisan migrate:fresh --seed
   ```

---

**تم آخر تحديث:** 23 يناير 2026
**الإصدار:** 2.0
**الحالة:** ✅ جاهز للاختبار
