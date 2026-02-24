# حساب مدة الاختبار (Duration Calculation) في نظام MPFM

## النظرة العامة

تم إضافة حساب المدة الزمنية التلقائي من ملف اللوج. النظام يحسب:

1. **Total Duration** - المدة الكلية للاختبار
2. **Stable Duration** - المدة من لحظة استقرار الإنتاج
3. **Test Started At** - وقت بدء الاختبار
4. **Test Completed At** - وقت انتهاء الاختبار

---

## كيف يعمل الحساب

### 1. استخراج الأوقات من ملف اللوج
```php
$startTime = $this->parseDateTimeSafe($this->findTimeValue($firstRow), Carbon::today());
$endTime = $this->parseDateTimeSafe($this->findTimeValue(end($rows)), Carbon::today());
```

**المنطق:**
- يأخذ أول صف من الاختبار كـ وقت البداية
- يأخذ آخر صف من الاختبار كـ وقت النهاية
- يحاول تحويل القيم إلى كائنات Carbon (تاريخ وقت)

### 2. حساب المدة الكلية
```php
$durationMinutes = $startTime && $endTime ? $startTime->diffInMinutes($endTime) : null;
$durationHours = $durationMinutes ? round($durationMinutes / 60, 2) : null;
```

**المثال:**
- إذا بدأ الاختبار في 10:00 وانتهى في 13:30
- المدة = 3.5 ساعة

### 3. حساب مدة الاستقرار (Stable Duration)

هذا أكثر تعقيداً. يحاول النظام اكتشاف متى استقرّت معدلات الإنتاج:

#### الخطوة 1: البحث عن عمود معدل الزيت
```php
$oilRateColumn = $this->findColumnByName(array_keys($rows[0] ?? []),
    ['qo_sc[STB/d]', 'qo_lc[bbl/d]', 'oil_rate']);
```

#### الخطوة 2: استخراج معدلات الزيت
```php
foreach ($rows as $row) {
    if (isset($row[$oilRateColumn]) && is_numeric($row[$oilRateColumn])) {
        $oilRates[] = (float) $row[$oilRateColumn];
        $timestamps[] = $time;
    }
}
```

#### الخطوة 3: اكتشاف الاستقرار
```php
// استخدام نافذة متحركة (Rolling Window)
$windowSize = max(3, (int)(count($oilRates) * 0.1)); // 10% من البيانات

for ($i = $windowSize; $i < count($oilRates); $i++) {
    $window = array_slice($oilRates, $i - $windowSize, $windowSize);
    $avg = array_sum($window) / count($window);

    // حساب معامل التباين (Coefficient of Variation)
    $variance = array_sum(array_map(function($x) use ($avg) {
        return pow($x - $avg, 2);
    }, $window)) / count($window);
    $stdDev = sqrt($variance);
    $coefficientOfVariation = ($avg > 0) ? ($stdDev / $avg) * 100 : 0;

    // إذا كان التباين أقل من 5%، اعتبر هذه نقطة الاستقرار
    if ($coefficientOfVariation < 5) {
        $stabilityStartIndex = $i;
        break;
    }
}
```

**شرح المعادلة:**
```
معامل التباين (%) = (الانحراف المعياري / المتوسط) × 100

إذا كانت القيمة < 5%، تعني أن البيانات مستقرة
```

#### الخطوة 4: حساب المدة من نقطة الاستقرار
```php
if ($stabilityStartIndex && isset($timestamps[$stabilityStartIndex])) {
    $stableStartTime = $timestamps[$stabilityStartIndex];
    $stableDurationMinutes = $stableStartTime->diffInMinutes($endTime);
    return round($stableDurationMinutes / 60, 2);
}
```

---

## حالات الاستثناء والافتراضات

### إذا لم تتوفر بيانات كافية
```php
if (count($rows) < 5) {
    return null; // لا توجد بيانات كافية
}

if (!$oilRateColumn) {
    // إذا لم نجد عمود معدل الزيت، افترض أن 50% من الاختبار مستقر
    $totalMinutes = $startTime->diffInMinutes($endTime);
    return round(($totalMinutes * 0.5) / 60, 2);
}
```

### الافتراضات الافتراضية
```php
// إذا لم نستطع اكتشاف الاستقرار بوضوح
// افترض أن آخر 70% من الاختبار مستقر
$totalMinutes = $startTime->diffInMinutes($endTime);
return round(($totalMinutes * 0.7) / 60, 2);
```

---

## حقول قاعدة البيانات المستخدمة

| الحقل | النوع | الوصف | المثال |
|-------|-------|-------|--------|
| `test_started_at` | datetime | وقت بدء الاختبار | 2024-01-15 10:00:00 |
| `test_completed_at` | datetime | وقت انتهاء الاختبار | 2024-01-15 13:30:00 |
| `test_duration_hours` | decimal(8,2) | المدة الكلية بالساعات | 3.50 |
| `test_stable_duration_hours` | decimal(8,2) | مدة الاستقرار بالساعات | 2.45 |
| `test_total_data_points` | integer | عدد نقاط البيانات | 450 |

---

## مثال عملي

### سيناريو: اختبار MPFM لمدة 4 ساعات

**ملف اللوج يحتوي على:**
- 480 قياس (واحد كل 30 ثانية)
- معدل زيت يبدأ من 0 ويستقر بعد 30 دقيقة
- ينتهي الاختبار عند 4 ساعات

**الحسابات:**
```
Time = 10:00 إلى 14:00 = 4 ساعات ✓
Total Duration = 4.00 hours

Oil Rate:
- 10:00-10:30: تصاعدي (عدم استقرار)
- 10:30-14:00: مستقر (معامل تباين < 5%)

Stable Duration = (14:00 - 10:30) = 3.5 hours
```

---

## كيفية التحقق من البيانات

### استخدام Debug Script
```bash
php debug_mpfm_data_flow.php 1
```

**سيعرض:**
```
✅ test_started_at: 2024-01-15 10:00:00
✅ test_completed_at: 2024-01-15 14:00:00
✅ test_duration_hours: 4.00
✅ test_stable_duration_hours: 3.50
✅ test_total_data_points: 480
```

### التحقق المباشر من قاعدة البيانات
```sql
SELECT
    test_started_at,
    test_completed_at,
    test_duration_hours,
    test_stable_duration_hours,
    test_total_data_points
FROM mpfm_tests
WHERE id = 1;
```

---

## ملاحظات تقنية

### معامل التباين (Coefficient of Variation)
يتم حسابه بناءً على:
- **المتوسط الحسابي** للقيم في النافذة
- **الانحراف المعياري** للقيم
- **الصيغة:** CV = (σ / μ) × 100

```
مثال:
القيم: [100, 102, 101, 99, 100]
المتوسط = 100.4
الانحراف المعياري ≈ 1.14
معامل التباين ≈ 1.13% (< 5% = مستقر)
```

### حجم النافذة
```php
$windowSize = max(3, (int)(count($oilRates) * 0.1));
```

- الحد الأدنى: 3 قراءات
- الحد الأقصى: 10% من عدد القراءات
- المقصد: اكتشاف الاتجاهات المحلية

### الحد الأدنى للبيانات المطلوبة
- يجب أن يكون هناك **على الأقل 5 صفوف** من البيانات
- يجب أن يحتوي كل صف على **عمود الوقت**
- يجب أن يحتوي كل صف على **معدل الزيت** (إن أمكن)

---

## استكشاف الأخطاء والمشاكل

### المشكلة: تظهر المدة كـ "0 hours"

**الأسباب المحتملة:**
1. لم يتم إنشاء حقول الوقت في ملف اللوج
2. تنسيق التاريخ/الوقت غير صحيح
3. الصفوف الأولى والأخيرة لها نفس الوقت

**الحل:**
```bash
php debug_mpfm_data_flow.php <test_id>
```

تحقق من:
- هل `test_started_at` موجود؟
- هل `test_completed_at` موجود؟
- هل الأوقات مختلفة؟

### المشكلة: مدة الاستقرار أكبر من المدة الكلية

**السبب:** خطأ في منطق الحساب

**الحل:** تحديث الكود باستخدام:
```bash
php artisan mpfm:recalculate-kpis <test_id>
```

### المشكلة: ملف اللوج بدون حقول الوقت

**الحل:** النظام يفترض الوقت الحالي تلقائياً:
```php
$startTime = $this->parseDateTimeSafe($this->findTimeValue($firstRow), Carbon::today());
```

إذا لم يجد وقت، يستخدم `Carbon::today()` (منتصف الليل)

---

## أفضل الممارسات

### 1. تنسيق الوقت الموصى به في ملف اللوج
```
Date & Time: 2024-01-15 10:00:00
أو
DateTime: 2024-01-15T10:00:00Z
أو
time: 10:00:00 (يستخدم تاريخ اليوم الحالي)
```

### 2. التأكد من الدقة
- استخدم أقصى دقة زمنية ممكنة
- تجنب الأوقات المقربة

### 3. مراجعة البيانات
- دقق البيانات المستخرجة من اللوج
- تأكد من الأوقات المنطقية
- تحقق من معدلات الإنتاج المعقولة

---

## تحديثات وتحسينات مستقبلية

### قيد التطوير:
- [ ] اكتشاف ذكي للحالات الشاذة في البيانات
- [ ] حساب فترات محددة للاستقرار (تحديد الوقت بدقة)
- [ ] رسوم بيانية توضح فترة الاستقرار

### الاقتراحات المرحب بها:
- تحسين خوارزمية اكتشاف الاستقرار
- دعم تنسيقات وقت إضافية
- إضافة تنبيهات للمدة غير المتوقعة

---

## مراجع إضافية

- [MPFM Data Flow Fix Report](MPFM_DATA_FLOW_FIX_REPORT.md)
- [Debug Script](debug_mpfm_data_flow.php)
- [MPFMLogProcessingService.php](app/Services/MPFMLogProcessingService.php)
- [MPFMTest Model](app/Models/MPFMTest.php)

