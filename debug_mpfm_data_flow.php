<?php
/**
 * MPFM Data Flow Diagnostic Script
 *
 * This script helps diagnose and verify that MPFM test data is being properly:
 * 1. Extracted from log files
 * 2. Calculated in the services
 * 3. Saved to the database
 * 4. Retrieved and displayed in views
 */

// Set up Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MPFMTest;
use App\Services\MPFMTestService;

// Get the test ID from command line or use most recent
$testId = isset($argv[1]) ? (int)$argv[1] : null;

if (!$testId) {
    // Get the most recent MPFM test
    $test = MPFMTest::latest()->first();
    if (!$test) {
        echo "❌ No MPFM tests found in database\n";
        exit(1);
    }
    $testId = $test->id;
} else {
    $test = MPFMTest::find($testId);
    if (!$test) {
        echo "❌ MPFM test with ID {$testId} not found\n";
        exit(1);
    }
}

echo "\n📋 MPFM Test Data Diagnostic Report\n";
echo "====================================\n\n";

echo "Test ID: {$test->id}\n";
echo "Test Date: " . ($test->test_date ? $test->test_date->format('Y-m-d') : 'N/A') . "\n";
echo "Status: {$test->status}\n\n";

// Check 1: Verify raw data fields are populated
echo "1️⃣ RAW DATA FIELDS CHECK\n";
echo "-" . str_repeat("-", 40) . "\n";

$rawFields = [
    'oil_rate' => $test->oil_rate,
    'gas_rate' => $test->gas_rate,
    'water_cut' => $test->water_cut,
    'pressure' => $test->pressure,
    'temperature' => $test->temperature,
    'qo_sc (standard condition oil rate)' => $test->qo_sc,
    'qo_lc (liquid condition oil rate)' => $test->qo_lc,
    'qg_sc (standard condition gas rate)' => $test->qg_sc,
    'qg_lc (liquid condition gas rate)' => $test->qg_lc,
    'qw_sc (standard condition water rate)' => $test->qw_sc,
    'qw_lc (liquid condition water rate)' => $test->qw_lc,
    'pressure_avg' => $test->pressure_avg,
    'pressure_max' => $test->pressure_max,
    'pressure_min' => $test->pressure_min,
    'temperature_avg' => $test->temperature_avg,
    'temperature_max' => $test->temperature_max,
    'temperature_min' => $test->temperature_min,
    'test_started_at' => $test->test_started_at,
    'test_completed_at' => $test->test_completed_at,
];

$filledFields = 0;
foreach ($rawFields as $fieldName => $value) {
    if ($value !== null && $value !== '') {
        echo "✅ {$fieldName}: {$value}\n";
        $filledFields++;
    } else {
        echo "❌ {$fieldName}: EMPTY/NULL\n";
    }
}

echo "\nSummary: {$filledFields}/" . count($rawFields) . " fields populated\n\n";

// Check 2: Verify calculated KPIs
echo "2️⃣ CALCULATED KPIS CHECK\n";
echo "-" . str_repeat("-", 40) . "\n";

if ($test->calculated_kpis && is_array($test->calculated_kpis)) {
    foreach ($test->calculated_kpis as $key => $value) {
        if ($key !== 'source_fields') {
            echo "✅ {$key}: {$value}\n";
        }
    }

    if (isset($test->calculated_kpis['source_fields'])) {
        echo "\nData Sources Used:\n";
        foreach ($test->calculated_kpis['source_fields'] as $source => $field) {
            echo "  • {$source}: {$field}\n";
        }
    }
} else {
    echo "❌ calculated_kpis field is EMPTY or not an array\n";
}

echo "\n";

// Check 3: Recalculate KPIs using service and compare
echo "3️⃣ RECALCULATE KPIS AND COMPARE\n";
echo "-" . str_repeat("-", 40) . "\n";

$service = new MPFMTestService();
$recalculatedKPIs = $service->calculateKPIs($test);

$differences = [];
$storedKPIs = $test->calculated_kpis ?? [];

foreach ($recalculatedKPIs as $key => $value) {
    if ($key === 'source_fields') continue;

    $storedValue = $storedKPIs[$key] ?? null;
    if ($storedValue != $value) {
        $differences[$key] = [
            'stored' => $storedValue,
            'recalculated' => $value
        ];
    }
}

if (empty($differences)) {
    echo "✅ All KPIs match (no recalculation needed)\n";
} else {
    echo "⚠️ Found differences in KPI calculations:\n";
    foreach ($differences as $key => $values) {
        echo "  • {$key}:\n";
        echo "    Stored: " . ($values['stored'] ?? 'null') . "\n";
        echo "    Recalculated: " . ($values['recalculated'] ?? 'null') . "\n";
    }
}

echo "\n";

// Check 4: Verify JSON fields contain data
echo "4️⃣ JSON METADATA FIELDS CHECK\n";
echo "-" . str_repeat("-", 40) . "\n";

$jsonFields = [
    'detailed_metrics',
    'flow_rate_metrics',
    'pvt_metrics',
    'pressure_temperature_metrics',
    'ratio_metrics',
    'cumulative_metrics',
    'data_quality_metrics',
];

foreach ($jsonFields as $field) {
    $value = $test->{$field};
    if ($value && is_array($value) && !empty($value)) {
        echo "✅ {$field}: " . count($value) . " items\n";
    } else {
        echo "⚠️ {$field}: Empty or null\n";
    }
}

echo "\n";

// Check 5: Recommend next steps
echo "5️⃣ RECOMMENDATIONS\n";
echo "-" . str_repeat("-", 40) . "\n";

$issues = [];

// Check for missing basic data
if (!$test->oil_rate && !$test->qo_sc && !$test->qo_lc) {
    $issues[] = "No oil rate data found. Check if log file was properly processed.";
}
if (!$test->gas_rate && !$test->qg_sc && !$test->qg_lc) {
    $issues[] = "No gas rate data found. Check if log file was properly processed.";
}
if (!$test->pressure_avg && !$test->pressure) {
    $issues[] = "No pressure data found. Check if log file was properly processed.";
}
if (!$test->test_started_at) {
    $issues[] = "No test_started_at timestamp. This affects duration calculations.";
}
if (!$test->test_completed_at) {
    $issues[] = "No test_completed_at timestamp. This affects duration calculations.";
}
if (!$test->calculated_kpis || empty($test->calculated_kpis)) {
    $issues[] = "No calculated KPIs found. Run: php artisan mpfm:recalculate-kpis {$testId}";
}

if (empty($issues)) {
    echo "✅ All checks passed! Data flow appears correct.\n";
    echo "\nNext step: Verify the data displays correctly in the view at:\n";
    echo "   http://localhost:8000/mpfm-tests/{$testId}\n";
} else {
    echo "⚠️ Issues found:\n";
    foreach ($issues as $issue) {
        echo "  • {$issue}\n";
    }
}

echo "\n";
