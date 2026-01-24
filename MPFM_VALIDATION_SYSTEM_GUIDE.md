# MPFM Test Validation & Historical Comparison System - Complete Guide

## 📋 Table of Contents
1. [System Overview](#system-overview)
2. [How It Works](#how-it-works)
3. [Validation Rule Types](#validation-rule-types)
4. [Confidence Scoring](#confidence-scoring)
5. [User Workflow](#user-workflow)
6. [Setup & Installation](#setup--installation)
7. [API & Methods](#api--methods)
8. [Database Schema](#database-schema)
9. [Examples](#examples)

---

## System Overview

The MPFM Test Validation System automatically analyzes multiphase flow meter tests submitted by operators and determines their acceptability through:

1. **Configurable Validation Rules** - Range checks, trend detection, anomaly detection
2. **Historical Comparison** - Compare current tests with previous 5-10 approved tests
3. **Statistical Analysis** - Calculate averages, medians, standard deviations, and Z-scores
4. **Intelligent Scoring** - Generate confidence score (0-100) and auto-suggestion
5. **Supervisor Override** - Allow supervisors to override validation with audit logging

**Key Benefit**: Eliminates manual decision-making by automatically flagging suspicious tests while allowing supervisors to review and override when needed.

---

## How It Works

### Step-by-Step Process

```
Operator Submits Test
        ↓
MPFMValidationService.validateTest() called
        ↓
1. Load Applicable Validation Rules (hierarchical priority)
        ↓
2. Execute Each Rule Against Test Data
   ├─ Range Validation: Check if value within min/max
   ├─ Percentage Change: Compare to historical average
   ├─ Trend Detection: Analyze last N tests for patterns
   └─ Anomaly Detection: Flag statistical outliers
        ↓
3. Aggregate Rule Results
   ├─ Count pass/warning/fail results
   └─ Calculate overall_status
        ↓
4. Historical Comparison
   ├─ Fetch last 5-10 approved tests from same string
   ├─ Calculate statistics (avg, median, std_dev)
   ├─ Calculate deviations & Z-scores
   └─ Detect trends using linear regression
        ↓
5. Calculate Confidence Score (0-100)
   ├─ Start at 100
   ├─ Deduct points for critical/major/minor failures
   └─ Deduct points for warnings
        ↓
6. Generate Auto-Suggestion
   ├─ Critical failures → REJECT
   ├─ Score 80-100 → APPROVE
   ├─ Score 50-79 → REVIEW
   └─ Score 0-49 → REJECT
        ↓
7. Detect Anomalies
   ├─ Flag values >2.5 standard deviations from mean
   └─ Classify as critical/warning
        ↓
8. Save Results to Database
   ├─ Create MPFMTestValidation record
   ├─ Update test with denormalized fields
   └─ Fire MPFMTestValidated event
        ↓
Supervisor Reviews Results in Approval Page
        ↓
Supervisor Can:
├─ Approve (if suggestion matches)
├─ Approve with Override (if different from suggestion)
└─ Reject (with notes)
```

---

## Validation Rule Types

### 1. Range Validation
**What**: Checks if a parameter falls within acceptable min/max bounds.

**Example**:
```php
[
    'rule_name' => 'Oil Rate Range Check',
    'rule_type' => 'range',
    'parameter' => 'oil_rate',
    'thresholds' => [
        'min' => 100,
        'max' => 5000,
    ],
    'severity' => 'critical',
]
```

**How It Works**:
- If oil_rate < 100 or > 5000 → FAIL
- Typically most basic and most important check
- Quick performance - no historical data needed

**Use Cases**:
- Oil rate too high/low for equipment
- Water cut physically impossible (>100% or <0%)
- Temperature outside normal operating range

---

### 2. Percentage Change Validation
**What**: Compares current test value to historical average and flags if deviation exceeds threshold.

**Example**:
```php
[
    'rule_name' => 'Water Cut Change Limit',
    'rule_type' => 'percentage_change',
    'parameter' => 'water_cut',
    'thresholds' => [
        'max_increase_percent' => 15,      // Can't increase by >15%
        'max_decrease_percent' => 10,      // Can't decrease by >10%
        'lookback_days' => 90,
        'min_historical_tests' => 5,
    ],
    'severity' => 'major',
]
```

**How It Works**:
1. Fetch all approved tests from same string in last 90 days
2. If <5 tests exist → Skip rule (not enough data)
3. Calculate average of historical water_cut
4. Compare current water_cut to average:
   - If increase > 15% → FAIL
   - If decrease > 10% → FAIL
   - Otherwise → PASS

**Example Calculation**:
```
Historical average: 30%
Current test: 45%
Increase: (45 - 30) / 30 * 100 = 50%
Threshold: 15%
Result: 50% > 15% → FAIL ❌
```

**Use Cases**:
- Sudden water cut jump (equipment issue?)
- Unexpected oil rate drop (blockage?)
- GOR spike (gas separator problem?)

---

### 3. Trend Validation
**What**: Detects increasing/decreasing/stable trends over multiple tests using linear regression.

**Example**:
```php
[
    'rule_name' => 'Water Cut Trend Detection',
    'rule_type' => 'trend',
    'parameter' => 'water_cut',
    'thresholds' => [
        'lookback_days' => 180,
        'slope_threshold' => 0.5,          // Alert if water_cut increasing by >0.5% per test
        'r_squared_threshold' => 0.6,      // Trend must be significant (R² > 0.6)
        'min_tests' => 10,
    ],
    'severity' => 'major',
]
```

**How It Works**:
1. Fetch last 10+ approved tests from last 180 days
2. If <10 tests exist → Skip rule
3. Perform linear regression analysis:
   ```
   y = slope * x + intercept

   Test 1: water_cut = 25%
   Test 2: water_cut = 26%
   Test 3: water_cut = 27%
   Test 4: water_cut = 28%
   Test 5: water_cut = 29%

   Slope = 1% per test
   R² = 0.95 (very significant trend)
   ```
4. Compare slope to threshold:
   - If slope > 0.5% per test AND R² > 0.6 → WARNING/FAIL
   - Direction: increasing/decreasing/stable

**Use Cases**:
- Water cut steadily increasing (emulsion breaking down?)
- Oil rate declining over time (well depletion or blockage?)
- Pressure trending down (separator fouling?)

---

### 4. Anomaly Validation
**What**: Detects statistical outliers using Z-score method.

**Example**:
```php
[
    'rule_name' => 'Water Cut Anomaly Detection',
    'rule_type' => 'anomaly',
    'parameter' => 'water_cut',
    'thresholds' => [
        'z_score_threshold' => 2.0,        // Flag if >2 std dev from mean
        'lookback_days' => 90,
        'min_tests' => 8,
    ],
    'severity' => 'critical',
]
```

**How It Works**:
1. Fetch last 8+ approved tests from last 90 days
2. Calculate statistics:
   ```
   Mean (μ) = 30%
   Std Dev (σ) = 3%
   Current value = 45%

   Z-score = (45 - 30) / 3 = 5.0
   ```
3. Compare to threshold:
   - If |Z-score| > 2.0 → FAIL (statistically significant outlier)
   - If |Z-score| > 2.5 → CRITICAL anomaly

**Z-Score Reference**:
- |Z| < 1.0: Normal (68% of data)
- 1.0 < |Z| < 2.0: Unusual (27% of data)
- 2.0 < |Z| < 2.5: Anomaly (4% of data)
- |Z| > 2.5: Critical anomaly (<1% of data)

**Use Cases**:
- Sudden huge spike in water cut
- Oil rate drops to zero (operator error?)
- Temperature reading unrealistic
- GOR explosion

---

## Confidence Scoring

### How Confidence Score is Calculated

**Starting Point**: 100%

**Deductions** (based on rule failure):
```
Critical Severity:
├─ Failure: -40 points
└─ Warning: -20 points

Major Severity:
├─ Failure: -20 points
└─ Warning: -10 points

Minor Severity:
├─ Failure: -10 points
└─ Warning: -5 points
```

### Example Calculation

**Scenario**:
- 8 rules executed
- 1 critical failure (oil rate out of range)
- 2 major warnings (water cut changed 12%)
- 5 rules passed

**Calculation**:
```
Starting score: 100
Critical failure: -40 → 60
Major warning 1: -10 → 50
Major warning 2: -10 → 40
Final score: 40/100 = 40%
```

### Auto-Suggestion Based on Score

| Score | Suggestion | Meaning |
|-------|------------|---------|
| 80-100 | **APPROVE** | Test looks good, low risk |
| 50-79 | **REVIEW** | Questionable, supervisor should examine |
| 0-49 | **REJECT** | High risk, should not approve |

### Special Case: Critical Failures
If ANY critical failure exists → **ALWAYS REJECT** (regardless of score)

---

## User Workflow

### For Operators

1. **Submit Test**
   - Upload MPFM log file or enter test data manually
   - System automatically triggers validation
   - Test status: "draft" → "pending validation"

2. **View Validation Results** (optional)
   - See confidence score
   - See which rules passed/failed
   - Review supervisor notes if rejected

3. **If Rejected**
   - Recheck equipment
   - Re-run test
   - Resubmit
   - System automatically revalidates

### For Supervisors

1. **View Test Approval Page**
   - Navigate to: `/supervisor/test-approval/{testId}`
   - See validation results with:
     - Confidence score gauge
     - All rule results
     - Historical comparison table
     - Detected anomalies
     - Trends

2. **Review Validation**
   - Examine confidence score (0-100)
   - Check which rules failed/passed
   - Look at historical comparison
   - Compare to trends

3. **Make Decision**
   - If suggestion = "approve" → Click Approve
   - If suggestion = "review" → Review details carefully, then approve/reject
   - If suggestion = "reject" → Can still override if convinced test is valid
     - Must check "Override Validation" checkbox
     - Should add notes explaining reason

4. **Audit Trail**
   - All overrides are logged with:
     - Test ID
     - Validation suggestion vs. actual decision
     - Supervisor ID
     - Timestamp
     - Notes

---

## Setup & Installation

### Step 1: Run Migrations
```bash
php artisan migrate
```

Creates 5 new tables:
- `mpfm_validation_rules`
- `mpfm_test_validations`
- `mpfm_test_comparisons`
- `mpfm_test_statistics_cache`
- Adds columns to `mpfm_tests`

### Step 2: Seed Default Rules
```bash
php artisan db:seed --class=DefaultMPFMValidationRulesSeeder
```

Creates 11 default validation rules:
- Oil Rate Range Check (critical)
- Water Cut Range Check (major)
- Water Cut Change Limit (major)
- Water Cut Anomaly Detection (critical)
- GOR Range Check (major)
- GOR Anomaly Detection (major)
- Pressure Range Check (minor)
- Temperature Range Check (minor)
- Gas Rate Range Check (major)
- Oil Rate Trend Detection (minor) - disabled
- Water Cut Trend Detection (major) - disabled

### Step 3: Add Permissions
If using Spatie Permission:
```bash
php artisan tinker
```

```php
use Spatie\Permission\Models\Permission;

Permission::create(['name' => 'manage-validation-rules']);
Permission::create(['name' => 'review-test']);
Permission::create(['name' => 'view-trending-dashboard']);
```

### Step 4: Assign Roles
```php
$admin = User::find(1);
$admin->givePermissionTo('manage-validation-rules');

$supervisor = User::find(2);
$supervisor->givePermissionTo('review-test');
```

---

## API & Methods

### MPFMValidationService

#### validateTest()
Runs full validation on a test.

```php
$validationService = app(\App\Services\MPFMValidationService::class);

$result = $validationService->validateTest(
    test: $mpfmTest,           // MPFMTest model instance
    saveResults: true          // Save to database
);

// Returns array:
[
    'mpfm_test_id' => 123,
    'validated_at' => '2026-01-25 10:30:00',
    'overall_status' => 'fail',
    'confidence_score' => 45,
    'auto_suggestion' => 'reject',
    'rule_results' => [...],                    // Array of rule execution results
    'historical_comparison' => [...],           // Historical statistics
    'anomalies' => [                            // Detected anomalies
        [
            'parameter' => 'water_cut',
            'anomaly_type' => 'sudden_increase',
            'severity' => 'critical',
            'current_value' => 45,
            'expected_range' => [27, 33],
            'deviation' => '5.0 standard deviations',
            'message' => 'Critical anomaly: Water cut jumped from avg 30% to 45%'
        ]
    ]
]
```

### MPFMHistoricalComparisonService

#### compareWithHistory()
Compares test with historical data.

```php
$comparisonService = app(\App\Services\MPFMHistoricalComparisonService::class);

$comparison = $comparisonService->compareWithHistory(
    test: $mpfmTest,
    lookbackDays: 90,              // How far back to look
    minTests: 5                     // Minimum tests needed
);

// Returns array:
[
    'tests_compared' => 8,
    'date_range' => [
        'from' => '2025-10-25',
        'to' => '2026-01-24'
    ],
    'statistics' => [
        'oil_rate' => [
            'avg' => 2300,
            'median' => 2350,
            'std_dev' => 150,
            'min' => 2000,
            'max' => 2600
        ],
        'water_cut' => [...]        // Same structure
    ],
    'current_deviations' => [
        'oil_rate' => [
            'current_value' => 2500,
            'average' => 2300,
            'percent_diff' => 8.7,
            'z_score' => 1.33,
            'deviation_type' => 'normal'
        ],
        'water_cut' => [...]        // Same structure
    ],
    'trends' => [
        'oil_rate' => [
            'slope' => 10.5,
            'intercept' => 2000,
            'r_squared' => 0.85,
            'direction' => 'increasing',
            'significance' => 'high'
        ],
        'water_cut' => [...]        // Same structure
    ]
]
```

#### getStringTrendingAnalysis()
Gets trending data for a specific string.

```php
$analysis = $comparisonService->getStringTrendingAnalysis(
    oilStringId: 5,
    lookbackDays: 180
);

// Returns array with trend and alert information
[
    'tests_analyzed' => 12,
    'statistics' => [...],
    'trends' => [...],
    'alert_count' => 2,
    'alerts' => [
        [
            'parameter' => 'water_cut',
            'type' => 'increasing_trend',
            'severity' => 'warning',
            'message' => 'Increasing trend detected for water_cut'
        ]
    ]
]
```

### MPFMTestController

#### showWithHistory()
Show test details with historical comparison.

```php
GET /mpfm-tests/{mpfmTest}/history

// Displays: test details + historical comparison table
```

#### stringAnalysis()
Analyze all tests for a specific string.

```php
GET /mpfm-tests/string-analysis/{oilString}

// Displays: all tests + trends + statistics for string
```

#### trendingDashboard()
View trending analysis across all strings.

```php
GET /mpfm-tests/trending-dashboard

// Displays: overview of all strings with alert counts
```

---

## Database Schema

### mpfm_validation_rules Table
```sql
CREATE TABLE mpfm_validation_rules (
    id bigint PRIMARY KEY,
    rule_name varchar(255),
    rule_type enum('range', 'percentage_change', 'trend', 'anomaly'),
    parameter varchar(100),

    -- Hierarchical scoping
    oil_field_id bigint (nullable),
    tower_id bigint (nullable),
    oil_string_id bigint (nullable),

    -- Configuration
    thresholds json,              -- {"min": 100, "max": 5000}
    severity enum('critical', 'major', 'minor'),
    auto_action enum('block', 'warn', 'info'),
    is_active boolean,
    created_by bigint (nullable),

    created_at, updated_at
);
```

### mpfm_test_validations Table
```sql
CREATE TABLE mpfm_test_validations (
    id bigint PRIMARY KEY,
    mpfm_test_id bigint (FOREIGN KEY),
    validated_at timestamp,

    overall_status enum('pass', 'warning', 'fail'),
    confidence_score tinyint unsigned (0-100),
    auto_suggestion enum('approve', 'review', 'reject'),

    rule_results json,             -- Array of rule execution results
    historical_comparison json,     -- Historical statistics
    anomalies json,                 -- Detected anomalies

    created_at, updated_at
);
```

### mpfm_tests Table (Modified)
```sql
ALTER TABLE mpfm_tests ADD COLUMN
    validation_status enum('pass', 'warning', 'fail'),
    validation_score tinyint unsigned,
    validation_suggestion enum('approve', 'review', 'reject'),
    validation_summary text,
    last_validated_at timestamp;
```

---

## Examples

### Example 1: Range Validation in Action

**Scenario**: Testing oil rate range rule

**Data**:
```php
$test = [
    'oil_rate' => 6000,  // Out of range!
];

$rule = [
    'rule_type' => 'range',
    'parameter' => 'oil_rate',
    'thresholds' => ['min' => 100, 'max' => 5000],
    'severity' => 'critical',
];
```

**Execution**:
```
Check: 6000 < 100? NO
Check: 6000 > 5000? YES → FAIL

Result: {
    'result' => 'fail',
    'message' => 'Value 6000 outside range [100, 5000]',
    'severity' => 'critical'
}
```

**Impact on Score**:
- Critical failure: -40 points
- Final confidence score: 60% (if no other issues)
- Auto-suggestion: REJECT

---

### Example 2: Percentage Change Validation

**Scenario**: Water cut increased too much

**Historical Data**:
```
Test 1: 28%
Test 2: 29%
Test 3: 30%
Test 4: 31%
Test 5: 32%
Average: 30%
```

**Current Test**: 47%

**Execution**:
```
Percent change = (47 - 30) / 30 * 100 = 56.67%
Threshold = 15%
56.67% > 15% → FAIL

Rule result: {
    'result' => 'fail',
    'message' => 'water_cut changed by 56.67%, exceeds 15% threshold',
    'severity' => 'major'
}
```

**Impact**:
- Major failure: -20 points
- Confidence score drops to 80
- Auto-suggestion: APPROVE (borderline)
- Supervisor should review carefully

---

### Example 3: Trend Detection with Linear Regression

**Scenario**: Water cut increasing over time

**Historical Tests** (last 12 tests):
```
Test 1:  22%
Test 2:  24%
Test 3:  25%
Test 4:  27%
Test 5:  28%
Test 6:  29%
Test 7:  31%
Test 8:  32%
Test 9:  33%
Test 10: 35%
Test 11: 36%
Test 12: 37%
```

**Linear Regression Analysis**:
```
y = 1.3x + 21.5
Slope = 1.3% per test
R² = 0.92 (very strong correlation)
```

**Threshold**: slope > 0.5% AND R² > 0.6

**Result**: ✓ TREND DETECTED
- Slope 1.3 > 0.5 ✓
- R² 0.92 > 0.6 ✓
- Direction: INCREASING
- Significance: HIGH

**Interpretation**:
- Water cut is steadily increasing
- Strong evidence (92% of variance explained)
- Next test: expect ~37-38% water cut
- Alert supervisor to investigate root cause

---

### Example 4: Anomaly Detection

**Scenario**: Sudden water cut spike

**Historical Tests**:
```
Mean = 30%
Median = 30%
Std Dev = 2%
Min = 27%
Max = 33%
```

**Current Test**: 50% water cut

**Z-Score Calculation**:
```
Z = (50 - 30) / 2 = 10.0
```

**Interpretation**:
- 10 standard deviations from mean!
- Probability: <0.0000001%
- This is NOT random variation

**Result**: CRITICAL ANOMALY
```php
[
    'parameter' => 'water_cut',
    'anomaly_type' => 'sudden_increase',
    'severity' => 'critical',
    'current_value' => 50,
    'expected_range' => [26, 34],
    'deviation' => '10.0 standard deviations',
    'message' => 'Critical anomaly: Water cut jumped from avg 30% to 50%'
]
```

**Actions**:
- System: Auto-reject validation
- Supervisor alert: Review immediately
- Likely causes:
  - Operator error in reading
  - Equipment malfunction
  - Separator failure
  - Test calibration issue

---

## Troubleshooting

### Issue: "Not enough historical data"
**Cause**: Less than 5 approved tests exist for the string
**Solution**:
- Submit at least 5 tests to establish baseline
- Trend and anomaly rules require 8-10 tests
- Wait before enabling trend detection

### Issue: Confidence score always 100
**Cause**: No rules configured or all rules passing
**Solution**:
- Run seeder: `php artisan db:seed --class=DefaultMPFMValidationRulesSeeder`
- Check is_active flag on rules
- Verify rules are scoped correctly

### Issue: Tests keep getting rejected
**Cause**: Rules may be too strict
**Solution**:
- Loosen range thresholds
- Increase percentage_change limits
- Disable minor severity rules temporarily

---

## Performance Optimization

### Caching Strategy
- Historical statistics cached for 24 hours
- Reduces database queries significantly
- Clears expired cache daily via scheduled task:
  ```bash
  php artisan mpfm:clear-expired-cache
  ```

### Indexing
- Fast queries on: validation_status, validation_suggestion, oil_string_id
- All foreign key columns indexed

### Lazy Loading
- Validation results only loaded when needed
- Historical comparison optional
- Trends calculated on-demand

---

## Summary

The MPFM Test Validation System:
✅ Automatically validates tests using configurable rules
✅ Compares with historical data for context
✅ Detects trends using statistical analysis
✅ Flags anomalies as potential equipment issues
✅ Calculates risk score (0-100) for supervisor guidance
✅ Logs all overrides for audit trail
✅ Improves test data quality and reliability

