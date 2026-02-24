# MPFM Test Data Display - Issues Found and Fixed

## Executive Summary

The MPFM test data was not displaying correctly because of three main issues in the data flow pipeline:

1. **View not receiving KPI data** - The controller's `show()` method wasn't passing KPI data to the view
2. **KPI calculations using wrong field names** - The `calculateKPIs()` service method referenced fields that didn't exist
3. **No mechanism to debug data flow** - Missing diagnostic tools to identify where data is lost

---

## Issues Identified

### Issue 1: Missing KPI Data in View ❌

**Location:** `app/Http/Controllers/MPFMTestController.php` - `show()` method (line 417)

**Problem:**
The view template (`resources/views/mpfm_tests/show.blade.php`) expects a `$kpis` variable to display production metrics:
```blade
<td>{{ number_format($kpis['oil_rate'] ?? 0, 2) }} STB/d</td>
<td>{{ number_format($kpis['gas_rate'] ?? 0, 2) }} Mscf/d</td>
<td>{{ number_format($kpis['avg_pressure'] ?? $kpis['pressure'] ?? 0, 2) }} psi</td>
```

However, the controller's `show()` method only passed `$mpfmTest` without calculating or preparing `$kpis`.

**Impact:**
- All KPI fields displayed as 0.00
- Water Cut, Pressure, Temperature metrics showed 0.00
- Duration fields were not calculated

**Fix Applied:**
```php
// Before
return view('mpfm_tests.show', compact('mpfmTest'));

// After
$kpis = [];
if ($mpfmTest->calculated_kpis) {
    $kpis = $mpfmTest->calculated_kpis;
}
// Fallback to raw database fields if calculated_kpis is empty
if (empty($kpis['oil_rate']) && $mpfmTest->qo_sc) {
    $kpis['oil_rate'] = $mpfmTest->qo_sc ?? ($mpfmTest->qo_lc ?? 0);
}
// ... similar fallbacks for other metrics

return view('mpfm_tests.show', compact('mpfmTest', 'kpis'));
```

---

### Issue 2: KPI Calculation Using Wrong Field Names ❌

**Location:** `app/Services/MPFMTestService.php` - `calculateKPIs()` method (line 13)

**Problem:**
The service method tried to access simple field names like `$test->oil_rate`, `$test->gas_rate`, `$test->pressure`, but the actual database schema stores detailed metrics with specific naming conventions:

**Correct Field Names (in MPFMTest model):**
- `qo_sc` - Oil rate at standard conditions (STB/d)
- `qo_lc` - Oil rate at liquid conditions (bbl/d)
- `qg_sc` - Gas rate at standard conditions (MMScf/d)
- `qg_lc` - Gas rate at liquid conditions (Mcf/d)
- `qw_sc` - Water rate at standard conditions (STB/d)
- `qw_lc` - Water rate at liquid conditions (bbl/d)
- `pressure_avg` - Average pressure
- `pressure_max` - Maximum pressure
- `pressure_min` - Minimum pressure
- `temperature_avg` - Average temperature
- `temperature_max` - Maximum temperature
- `temperature_min` - Minimum temperature

**Original Code (Wrong):**
```php
public function calculateKPIs(MPFMTest $test): array
{
    // These fields don't exist or are calculated, not stored!
    if ($test->oil_rate > 0) {
        $kpis['gor'] = $test->gas_rate / $test->oil_rate;
    }
    // ...WLR calculation used water_cut directly
    $waterRate = $test->oil_rate * ($test->water_cut / 100);
    // ...
}
```

**Issues with Original Code:**
1. Referenced non-existent aggregated fields
2. Circular logic: tried to get water_rate by calculating it from oil_rate and water_cut
3. Didn't check for SC vs LC rate versions
4. No fallback strategies for missing data

**Fix Applied:**
```php
public function calculateKPIs(MPFMTest $test): array
{
    // Use standard condition rates (SC) if available, fallback to liquid conditions (LC)
    $oilRate = $test->qo_sc ?? $test->qo_lc ?? $test->oil_rate ?? 0;
    $gasRate = $test->qg_sc ?? $test->qg_lc ?? $test->gas_rate ?? 0;
    $waterRate = $test->qw_sc ?? $test->qw_lc ?? 0;

    // Proper GOR calculation: Gas Rate / Oil Rate
    if (is_numeric($oilRate) && $oilRate > 0) {
        $kpis['gor'] = round($gasRate / $oilRate, 4);
    } else {
        $kpis['gor'] = 0;
    }

    // Proper WLR calculation: Water Rate / Total Liquid * 100
    $totalLiquid = $oilRate + $waterRate;
    if (is_numeric($totalLiquid) && $totalLiquid > 0) {
        $kpis['wlr'] = round(($waterRate / $totalLiquid) * 100, 2);
    } else {
        $kpis['wlr'] = 0;
    }

    // Proper BSW calculation: Water / Total Liquid * 100
    if (is_numeric($totalLiquid) && $totalLiquid > 0) {
        $kpis['bsw'] = round(($waterRate / $totalLiquid) * 100, 2);
    } else {
        $kpis['bsw'] = 0;
    }

    // ... additional metrics with proper field references
}
```

**Key Improvements:**
- Uses actual database field names with fallback chains
- Proper formula implementations:
  - GOR = Gas Rate / Oil Rate (Scf/STB)
  - WLR = Water Rate / (Oil + Water) × 100 (%)
  - BSW = Water / (Oil + Water) × 100 (%)
  - GVF = Gas / (Oil + Water + Gas) × 100 (%)
  - PI = Oil Rate / Pressure (STB/d/psi)
- Added source field tracking for debugging
- Proper rounding and type checking

---

## Data Flow Architecture

### How MPFM Data Flows Through the System:

```
1. User uploads log file
   ↓
2. MPFMTestController::processUpload()
   ↓
3. MPFMLogProcessingService::processLogFile()
   - Reads and parses log file content
   - Extracts rows of measurement data
   ↓
4. MPFMLogProcessingService::processParsedData()
   - Calls calculationService->calculateStandardizedMetrics($rows)
   - Extracts comprehensive MPFM data
   ↓
5. MPFMCalculationService::calculateStandardizedMetrics()
   - Processes raw data rows
   - Calculates averages for each metric
   - Returns: oil_rate_avg, gas_rate_avg, pressure_avg, temperature_avg, etc.
   ↓
6. MPFMLogProcessingService::safeExtractMPFMData()
   - Takes averaged metrics
   - Maps them to database field names
   - Returns mpfm_data array
   ↓
7. Controller calls storeFromLog()
   - Receives mpfm_data array
   - Merges with form data (oil_field_id, tower_id, etc.)
   ↓
8. MPFMTestService::createOrUpdate()
   - Creates or updates MPFMTest record in database
   - Calls calculateKPIs() to compute derived metrics
   - Stores: qo_sc, qo_lc, qg_sc, qg_lc, etc. in database
   ↓
9. Controller calls show()
   - Retrieves test from database
   - Prepares KPIs for display
   ↓
10. View displays KPIs from $kpis variable
```

### Critical Data Mapping Points:

**From Log File Parsing** → **Database Fields**
- `averages['oil_rate_avg']` → `qo_sc` (or `oil_rate`)
- `averages['gas_rate_avg']` → `qg_sc` (or `gas_rate`)
- `averages['water_rate_avg']` → `qw_sc` (or calculated)
- `averages['pressure_avg']` → `pressure_avg`
- `averages['temperature_avg']` → `temperature_avg`
- `averages['bsw_avg']` → `water_cut` or `bsw`

---

## Field Reference Guide

### Production Rate Fields
| Database Field | Description | Unit | Source |
|---|---|---|---|
| `qo_sc` | Oil rate at standard conditions | STB/d | Log file |
| `qo_lc` | Oil rate at liquid conditions | bbl/d | Log file |
| `qg_sc` | Gas rate at standard conditions | MMScf/d | Log file |
| `qg_lc` | Gas rate at liquid conditions | Mcf/d | Log file |
| `qw_sc` | Water rate at standard conditions | STB/d | Log file |
| `qw_lc` | Water rate at liquid conditions | bbl/d | Log file |
| `oil_rate` | Aggregated oil rate (fallback) | Mixed | Calculated |
| `gas_rate` | Aggregated gas rate (fallback) | Mixed | Calculated |

### Pressure & Temperature Fields
| Database Field | Description | Unit |
|---|---|---|
| `pressure` | Single pressure value | psi |
| `pressure_avg` | Average pressure | psi |
| `pressure_max` | Maximum pressure | psi |
| `pressure_min` | Minimum pressure | psi |
| `temperature` | Single temperature value | °F |
| `temperature_avg` | Average temperature | °F |
| `temperature_max` | Maximum temperature | °F |
| `temperature_min` | Minimum temperature | °F |

### Quality & Ratio Fields
| Database Field | Description | Unit |
|---|---|---|
| `water_cut` | Water percentage (BSW equivalent) | % |
| `bsw` | Basic Sediment and Water | % |
| `wlr` | Water-Liquid Ratio | % |
| `gor` | Gas-Oil Ratio | Scf/STB |
| `gvf` | Gas Volume Fraction | % |

### Duration Fields
| Database Field | Description | Unit |
|---|---|---|
| `test_started_at` | Test start timestamp | datetime |
| `test_completed_at` | Test end timestamp | datetime |
| `test_duration_hours` | Total test duration | hours |
| `test_stable_duration_hours` | Stable period duration | hours |

---

## Formulas Reference

### Gas-Oil Ratio (GOR)
```
GOR = Gas Rate (Scf/STB) / Oil Rate (STB/d)
```
- Indicates volume of gas produced per volume of oil
- Higher values = more gas production

### Water-Liquid Ratio (WLR)
```
WLR (%) = [Water Rate / (Oil Rate + Water Rate)] × 100
```
- Percentage of water in total liquid production
- Used for fluid separation design

### Basic Sediment and Water (BSW)
```
BSW (%) = [Water Rate / (Oil Rate + Water Rate)] × 100
```
- Same calculation as WLR
- Indicates water and sediment content in crude

### Gas Volume Fraction (GVF)
```
GVF (%) = [Gas Rate / (Oil Rate + Water Rate + Gas Rate)] × 100
```
- Percentage of gas in total production
- Critical for flow assurance

### Productivity Index (PI)
```
PI (STB/d/psi) = Oil Rate / Pressure Differential
```
- Measures well's ability to produce
- Higher PI = better well performance

---

## Tools Provided for Debugging

### 1. Debug Script
**File:** `debug_mpfm_data_flow.php`

**Usage:**
```bash
# Check most recent test
php debug_mpfm_data_flow.php

# Check specific test
php debug_mpfm_data_flow.php 5
```

**Output:**
- Displays all populated database fields
- Shows current calculated KPIs
- Compares stored vs recalculated KPIs
- Identifies missing data
- Provides recommendations

### 2. Artisan Command
**File:** `app/Console/Commands/RecalculateMPFMKPIs.php`

**Usage:**
```bash
# Recalculate all tests
php artisan mpfm:recalculate-kpis

# Recalculate specific test
php artisan mpfm:recalculate-kpis 5
```

**Purpose:**
- Updates calculated_kpis for all or specific tests
- Useful after updating calculation logic
- Shows progress bar for batch operations

---

## What Was Displaying 0.00?

### Oil Rate, Gas Rate, Water Cut, Pressure, Temperature
**Reason:** The `$kpis` variable was not being passed to the view, so it used the `?? 0` fallback

**Solution:** Pass `$kpis` from controller after preparing from `calculated_kpis`

### Duration Calculations (Total Duration, Stable Duration, Started At, Completed At)
**Reason:** Not visible in the provided view excerpt, but would require:
1. `test_started_at` and `test_completed_at` to be saved from log file
2. Duration to be calculated as the difference between these timestamps

**Solution:** Ensure MPFMLogProcessingService saves these timestamp fields when processing log files

---

## Verification Checklist

After applying these fixes, verify the data displays correctly:

- [ ] Navigate to MPFM test page
- [ ] Check Oil Rate shows actual value (not 0.00)
- [ ] Check Gas Rate shows actual value (not 0.00)
- [ ] Check Water Cut shows actual percentage
- [ ] Check Pressure shows actual value
- [ ] Check Temperature shows actual value
- [ ] Check GOR, WLR, BSW in Calculated KPIs section
- [ ] Check Total Production Rate calculation
- [ ] Check duration fields if applicable

### If data still shows 0.00:

1. **Run the diagnostic script:**
   ```bash
   php debug_mpfm_data_flow.php
   ```

2. **Check which fields are populated:**
   - If all fields are empty → Log file not being processed
   - If some fields populated → Issue with specific field mapping
   - If all fields populated but KPIs wrong → Issue with calculation formula

3. **Recalculate KPIs:**
   ```bash
   php artisan mpfm:recalculate-kpis <test_id>
   ```

4. **Review the calculated_kpis in database:**
   - Check if the array contains values
   - Check source_fields to see which database fields were used

---

## Code Changes Summary

### Files Modified:
1. **MPFMTestController.php** - Fixed `show()` method to pass KPIs
2. **MPFMTestService.php** - Fixed `calculateKPIs()` method with correct field names and formulas

### Files Created:
1. **debug_mpfm_data_flow.php** - Diagnostic script
2. **RecalculateMPFMKPIs.php** - Artisan command
3. **MPFM_DATA_FLOW_FIX_REPORT.md** - This documentation

---

## Next Steps for Complete Fix

### Optional Improvements:
1. Update view to use more descriptive KPI names
2. Add unit labels to all displayed metrics
3. Add data quality indicators
4. Create validation rules for acceptable data ranges
5. Add historical comparison charts
6. Implement data caching for performance

### Testing Recommendations:
1. Upload a fresh log file and verify data displays
2. Test with log files from different MPFM devices
3. Verify calculations match expected values
4. Test edge cases (zero production, missing fields)
5. Load test with large log files

---

## Questions Answered

**Q: Why were all values showing 0.00?**
A: The view expected `$kpis` variable but the controller wasn't passing it, causing fallback to `?? 0`.

**Q: Why wasn't the data being saved?**
A: The data WAS being saved (it's in the database), but it wasn't being:
1. Retrieved and prepared in the controller
2. Calculated correctly due to wrong field name references
3. Displayed in the view due to missing variable

**Q: How do I know if the data flow is working?**
A: Run the diagnostic script to verify each step of the pipeline.

**Q: What if data is still not showing after fixes?**
A: The issue would be in the log file parsing service, not the calculation or display layers.
