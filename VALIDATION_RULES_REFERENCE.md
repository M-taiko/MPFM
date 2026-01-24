# MPFM Validation Rules Reference Card

## Quick Overview

| Rule Name | Type | Parameter | Severity | Status | Purpose |
|-----------|------|-----------|----------|--------|---------|
| Oil Rate Range | Range | oil_rate | Critical | ✅ Active | Prevent physically impossible values |
| Water Cut Range | Range | water_cut | Major | ✅ Active | Physical limits (0-100%) |
| Water Cut Change | % Change | water_cut | Major | ✅ Active | Sudden changes indicate problems |
| Water Cut Anomaly | Anomaly | water_cut | Critical | ✅ Active | Flag statistical outliers |
| GOR Range | Range | gor | Major | ✅ Active | Prevent unrealistic GOR values |
| GOR Anomaly | Anomaly | gor | Major | ✅ Active | Flag sudden GOR spikes |
| Pressure Range | Range | pressure | Minor | ✅ Active | Safe operating pressure range |
| Temperature Range | Range | temperature | Minor | ✅ Active | Equipment operating limits |
| Gas Rate Range | Range | gas_rate | Major | ✅ Active | Maximum gas rate capacity |
| Oil Rate Trend | Trend | oil_rate | Minor | ⚠️ Disabled | Monitor production decline |
| Water Cut Trend | Trend | water_cut | Major | ⚠️ Disabled | Monitor water cut increase |

---

## Detailed Rules

### 1️⃣ Oil Rate Range Check
**Type**: Range
**Severity**: CRITICAL (blocks approval if failed)
**Thresholds**: 50 - 5000 bbl/day

```
If oil_rate < 50 or > 5000 → REJECT
```

**Why**:
- Below 50: Well may be dead/blocked
- Above 5000: Equipment capacity exceeded
- Critical for production forecasting

**When triggered**:
- Manual test entry with wrong decimal point
- Separator malfunction
- Blocked flow

---

### 2️⃣ Water Cut Range Check
**Type**: Range
**Severity**: MAJOR
**Thresholds**: 0 - 100%

```
If water_cut < 0 or > 100 → FAIL
```

**Why**:
- Physical impossibility
- Operator error
- Sensor failure

**When triggered**:
- Typo during data entry (e.g., 150%)
- Faulty water meter reading

---

### 3️⃣ Water Cut Change Limit
**Type**: Percentage Change
**Severity**: MAJOR
**Thresholds**:
- Max increase: 15%
- Max decrease: 10%
- Lookback: 90 days
- Min historical: 5 tests

```
If (current_wc - avg_wc) / avg_wc * 100 > 15% → FAIL
If (avg_wc - current_wc) / avg_wc * 100 > 10% → FAIL
```

**Example**:
```
Average water cut: 30%
Current test: 45%
Change: (45-30)/30 = 50%
Threshold: 15%
50% > 15% → REJECT
```

**Why**:
- Water cut should change gradually
- Sudden jump = equipment issue
- Emulsion breaking, separator failure, treater problem

**When triggered**:
- Free water breakthrough
- Separator malfunction
- Heat treater failure
- Demulsifier injection problem

---

### 4️⃣ Water Cut Anomaly Detection
**Type**: Anomaly (Z-Score)
**Severity**: CRITICAL
**Thresholds**:
- Z-score threshold: 2.0
- Lookback: 90 days
- Min tests: 8

```
If |Z-score| > 2.0 → FAIL (and flag as anomaly)
If |Z-score| > 2.5 → CRITICAL ANOMALY
```

**How Z-score works**:
```
Z = (current_value - mean) / std_dev

Examples:
Z = 0: Value exactly at average
Z = 1: Value 1 std dev above average (84th percentile)
Z = 2: Value 2 std dev above average (98th percentile)
Z = 2.5: Value 2.5 std dev above (99.4th percentile)
Z = 10: Essentially impossible naturally
```

**When triggered**:
- Massive jump in water cut (e.g., from 30% to 60%)
- Sudden pressure drop
- Temperature spike
- GOR explosion
- Likely equipment failure or operator error

---

### 5️⃣ GOR Range Check
**Type**: Range
**Severity**: MAJOR
**Thresholds**: 50 - 2000 scf/bbl

```
If gor < 50 or > 2000 → FAIL
```

**Why**:
- GOR defines gas separation efficiency
- Too low: Gas meter failure?
- Too high: Gas separation problem

**When triggered**:
- Gas meter miscalibrated
- Separator not functioning
- Wrong well data entered

---

### 6️⃣ GOR Anomaly Detection
**Type**: Anomaly
**Severity**: MAJOR
**Thresholds**:
- Z-score threshold: 2.0
- Lookback: 90 days
- Min tests: 8

**When triggered**:
- Sudden GOR spike (gas separation failure)
- GOR collapse (back pressure?)

---

### 7️⃣ Pressure Range Check
**Type**: Range
**Severity**: MINOR
**Thresholds**: 300 - 3500 psi

```
If pressure < 300 or > 3500 → WARNING
```

**Why**:
- Confirms wellhead operating conditions
- Safety check
- Equipment monitoring

**When triggered**:
- Pressure reading anomaly
- Flowline blockage
- Production choke issue

---

### 8️⃣ Temperature Range Check
**Type**: Range
**Severity**: MINOR
**Thresholds**: 80 - 280°F

```
If temperature < 80 or > 280 → WARNING
```

**Why**:
- Confirms heat exchanger status
- Treater effectiveness
- Ambient conditions check

**When triggered**:
- Thermometer failure
- Heat exchanger fouling
- Extreme ambient temperature

---

### 9️⃣ Gas Rate Range Check
**Type**: Range
**Severity**: MAJOR
**Thresholds**: 0 - 100 MMSCF/day

```
If gas_rate < 0 or > 100 → FAIL
```

**Why**:
- Equipment gas capacity
- Gas line sizing
- Compressor limitations

**When triggered**:
- Meter readout error
- Equipment capacity exceeded
- Well interference testing

---

### 🔟 Oil Rate Trend Detection
**Type**: Trend (Linear Regression)
**Severity**: MINOR
**Thresholds**:
- Slope threshold: 10 bbl/day per test
- R² threshold: 0.7
- Lookback: 180 days
- Min tests: 10

**Status**: ⚠️ DISABLED (enable per field if needed)

**How it works**:
```
Fit line: oil_rate = slope * test_number + intercept

If slope > 10 AND R² > 0.7 → ALERT

Example:
Test 1: 2500 bbl/day
Test 2: 2490 bbl/day
Test 3: 2480 bbl/day
...
Slope = -10 bbl/day per test
Trend: DECLINING PRODUCTION
R² = 0.85 (strong correlation)
```

**When enabled**:
- Monitor natural well decline
- Detect blockages early
- Forecast production
- Plan workover timing

---

### 1️⃣1️⃣ Water Cut Trend Detection
**Type**: Trend
**Severity**: MAJOR
**Thresholds**:
- Slope threshold: 0.5% per test
- R² threshold: 0.6
- Lookback: 180 days
- Min tests: 10

**Status**: ⚠️ DISABLED (can be enabled)

**When enabled**:
- Monitor water cut increase
- Alert to formation water breakthrough
- Plan enhanced separation upgrade
- Schedule treater maintenance

---

## Severity Levels Explained

### CRITICAL ⛔
- **Blocks test approval** automatically
- Supervisor must override to approve
- Logged as security concern
- Examples: Oil rate 0 or 10000, Water cut 150%, GOR 5000

### MAJOR ⚠️
- **Warns supervisor** but doesn't block
- Suggests "REVIEW" (score 50-79)
- High confidence rule failure
- Examples: Water cut change 20%, Pressure 4000 psi, GOR spike

### MINOR ℹ️
- **Informational only**
- Slight score reduction
- Can be overridden easily
- Examples: Temperature 75°F, Pressure 250 psi

---

## Auto-Suggestion Logic

```
Confidence Score Calculation:
Start: 100%

For each failed rule:
  if severity == CRITICAL: score -= 40
  if severity == MAJOR: score -= 20
  if severity == MINOR: score -= 10

For each warning rule:
  if severity == CRITICAL: score -= 20
  if severity == MAJOR: score -= 10
  if severity == MINOR: score -= 5

Final: max(0, min(100, score))
```

### Score to Suggestion Mapping

```
80-100: APPROVE ✅
  - Test passed all checks
  - Low risk
  - Auto-approve safe

50-79: REVIEW 📋
  - Some failures/warnings
  - Supervisor judgment needed
  - Evaluate context

0-49: REJECT ❌
  - Multiple failures
  - High risk
  - Require retest

Special: If ANY critical failure → ALWAYS REJECT
  (regardless of other rules)
```

---

## How to Customize Rules

### Make Rules Stricter
```php
// Tighten oil rate range
'thresholds' => ['min' => 200, 'max' => 4000]  // was 50-5000

// Lower water cut change tolerance
'thresholds' => ['max_increase_percent' => 8]  // was 15

// Lower anomaly threshold
'thresholds' => ['z_score_threshold' => 1.5]   // was 2.0
```

### Make Rules Looser
```php
// Widen temperature range
'thresholds' => ['min' => 50, 'max' => 300]    // was 80-280

// Allow larger pressure variance
'thresholds' => ['min' => 200, 'max' => 4000]  // was 300-3500
```

### Enable Trend Rules
```php
// In seeder or manually:
$rule = MPFMValidationRule::find(10);  // Oil Rate Trend
$rule->update(['is_active' => true]);
```

### Create Custom Rule
```php
MPFMValidationRule::create([
    'rule_name' => 'Oil Rate Maximum Decrease',
    'rule_type' => 'percentage_change',
    'parameter' => 'oil_rate',
    'oil_string_id' => 5,  // Specific string
    'thresholds' => [
        'max_decrease_percent' => 20,  // Allow 20% decrease
        'lookback_days' => 60,
        'min_historical_tests' => 4,
    ],
    'severity' => 'major',
    'is_active' => true,
]);
```

---

## Typical Threshold Values

### Oil Field Best Practices

#### Conservative (Strict) Fields
```
Oil Rate: 100-4000 bbl/day
Water Cut Change: ±10%
GOR: 100-1500 scf/bbl
```

#### Typical Fields
```
Oil Rate: 50-5000 bbl/day
Water Cut Change: ±15%
GOR: 50-2000 scf/bbl
```

#### High-Variability Fields
```
Oil Rate: 25-6000 bbl/day
Water Cut Change: ±25%
GOR: 20-3000 scf/bbl
```

---

## Command Reference

### View All Rules
```bash
php artisan tinker
>>> App\Models\MPFMValidationRule::where('is_active', true)->get();
```

### Disable a Rule
```bash
php artisan tinker
>>> $rule = App\Models\MPFMValidationRule::find(3);
>>> $rule->update(['is_active' => false]);
```

### Create Rule Programmatically
```bash
php artisan tinker
>>> App\Models\MPFMValidationRule::create([...])
```

### Clear Validation Cache
```bash
php artisan mpfm:clear-expired-cache
```

### Revalidate Old Tests
```bash
php artisan mpfm:validate-tests --force
```

---

## Interpretation Guide

### When You See These Suggestions:

**"APPROVE"** ✅
- Test passed all checks
- Confidence 80-100%
- Safe to approve automatically
- Typical: stable production, normal readings

**"REVIEW"** 📋
- Some rules raised warnings
- Confidence 50-79%
- Supervisor should examine details
- Examples:
  - Water cut up 12% (close to 15% limit)
  - Temperature slightly high but in range
  - New string with <10 historical tests

**"REJECT"** ❌
- Critical failures or low confidence
- Confidence 0-49%
- Retest recommended
- Examples:
  - Water cut jumped 50%
  - Oil rate 0 (well dead?)
  - 10 standard deviations from normal
  - Multiple major rule failures

---

## Support & Troubleshooting

**Q: Why was my test rejected?**
A: Check the "Validation Results" tab - each failed rule shows exactly why.

**Q: Can I override the validation?**
A: Yes, supervisors can override if they check "Override Validation" and add notes.

**Q: How do I adjust the rules?**
A: Login as admin, go to Validation Rules, edit thresholds.

**Q: My tests keep passing even though they look wrong?**
A: Check if you need more historical tests (need 5-10 for comparison rules).

---

