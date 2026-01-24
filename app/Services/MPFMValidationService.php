<?php

namespace App\Services;

use App\Models\MPFMTest;
use App\Models\MPFMTestValidation;
use App\Models\MPFMValidationRule;

class MPFMValidationService
{
    public function validateTest(MPFMTest $test, bool $saveResults = true): array
    {
        // Get applicable rules (hierarchical)
        $rules = $this->getApplicableRules($test);

        // Execute each rule
        $ruleResults = [];
        foreach ($rules as $rule) {
            $ruleResults[] = $this->executeRule($test, $rule);
        }

        // Calculate overall status
        $overallStatus = $this->calculateOverallStatus($ruleResults);

        // Calculate confidence score
        $confidenceScore = $this->calculateConfidenceScore($ruleResults, $test);

        // Generate auto-suggestion
        $autoSuggestion = $this->generateAutoSuggestion($overallStatus, $confidenceScore, $ruleResults);

        // Get historical comparison
        $historicalComparison = app(MPFMHistoricalComparisonService::class)
            ->compareWithHistory($test);

        // Detect anomalies
        $anomalies = $this->detectAnomalies($test, $historicalComparison);

        // Build validation data
        $validationData = [
            'mpfm_test_id' => $test->id,
            'validated_at' => now(),
            'overall_status' => $overallStatus,
            'confidence_score' => $confidenceScore,
            'auto_suggestion' => $autoSuggestion,
            'rule_results' => $ruleResults,
            'historical_comparison' => $historicalComparison,
            'anomalies' => $anomalies,
        ];

        // Save results
        if ($saveResults) {
            $validation = MPFMTestValidation::create($validationData);

            // Update denormalized fields on test
            $test->update([
                'validation_status' => $overallStatus,
                'validation_score' => $confidenceScore,
                'validation_suggestion' => $autoSuggestion,
                'validation_summary' => $this->generateSummary($ruleResults, $anomalies),
                'last_validated_at' => now(),
            ]);

            // Fire event
            event(new \App\Events\MPFMTestValidated($test, $validation));
        }

        return $validationData;
    }

    protected function getApplicableRules(MPFMTest $test): \Illuminate\Support\Collection
    {
        return MPFMValidationRule::active()
            ->where(function ($query) use ($test) {
                $query->where('oil_field_id', $test->oil_field_id)
                    ->orWhere('oil_field_id', null);
            })
            ->where(function ($query) use ($test) {
                $query->where('tower_id', $test->tower_id)
                    ->orWhere('tower_id', null);
            })
            ->where(function ($query) use ($test) {
                $query->where('oil_string_id', $test->oil_string_id)
                    ->orWhere('oil_string_id', null);
            })
            ->get()
            ->sortByDesc(function ($rule) {
                return $rule->getSpecificityLevel();
            });
    }

    protected function executeRule(MPFMTest $test, MPFMValidationRule $rule): array
    {
        $parameter = $rule->parameter;
        $currentValue = $test->$parameter;
        $result = 'pass';
        $message = '';
        $details = [];

        if ($currentValue === null) {
            return [
                'rule_id' => $rule->id,
                'rule_name' => $rule->rule_name,
                'parameter' => $parameter,
                'result' => 'skip',
                'severity' => $rule->severity,
                'message' => "No value provided for {$parameter}",
            ];
        }

        match ($rule->rule_type) {
            'range' => [$result, $message, $details] = $this->validateRange($currentValue, $rule->thresholds, $rule->severity),
            'percentage_change' => [$result, $message, $details] = $this->validatePercentageChange($test, $rule),
            'trend' => [$result, $message, $details] = $this->validateTrend($test, $rule),
            'anomaly' => [$result, $message, $details] = $this->validateAnomaly($test, $rule),
            default => [$result, $message, $details] = ['warning', 'Unknown rule type', []],
        };

        return [
            'rule_id' => $rule->id,
            'rule_name' => $rule->rule_name,
            'parameter' => $parameter,
            'result' => $result,
            'severity' => $rule->severity,
            'message' => $message,
            'details' => $details,
        ];
    }

    protected function validateRange($value, array $thresholds, string $severity): array
    {
        $min = $thresholds['min'] ?? null;
        $max = $thresholds['max'] ?? null;

        if ($value < $min || $value > $max) {
            return [
                'fail',
                "Value {$value} outside range [{$min}, {$max}]",
                ['min' => $min, 'max' => $max, 'current' => $value],
            ];
        }

        return ['pass', "Value {$value} within acceptable range", []];
    }

    protected function validatePercentageChange(MPFMTest $test, MPFMValidationRule $rule): array
    {
        $historicalService = app(MPFMHistoricalComparisonService::class);
        $comparison = $historicalService->compareWithHistory($test,
            lookbackDays: $rule->thresholds['lookback_days'] ?? 90,
            minTests: $rule->thresholds['min_historical_tests'] ?? 5
        );

        if ($comparison === null) {
            return ['skip', 'Insufficient historical data', []];
        }

        $deviations = $comparison['current_deviations'];
        $parameter = $rule->parameter;

        if (!isset($deviations[$parameter])) {
            return ['skip', "No deviation data for {$parameter}", []];
        }

        $percentDiff = abs($deviations[$parameter]['percent_diff']);
        $maxChange = $rule->thresholds['max_increase_percent'] ?? 15;

        if ($percentDiff > $maxChange) {
            return [
                'fail',
                "{$parameter} changed by {$percentDiff}%, exceeds {$maxChange}% threshold",
                ['percent_diff' => $percentDiff, 'threshold' => $maxChange],
            ];
        }

        return ['pass', "{$parameter} within acceptable change limit", []];
    }

    protected function validateTrend(MPFMTest $test, MPFMValidationRule $rule): array
    {
        $historicalService = app(MPFMHistoricalComparisonService::class);
        $analysis = $historicalService->getStringTrendingAnalysis($test->oil_string_id,
            lookbackDays: $rule->thresholds['lookback_days'] ?? 180
        );

        if (count($analysis['alerts']) === 0) {
            return ['pass', 'No concerning trends detected', []];
        }

        $parameter = $rule->parameter;
        $parameterAlerts = array_filter($analysis['alerts'], function ($alert) use ($parameter) {
            return $alert['parameter'] === $parameter;
        });

        if (empty($parameterAlerts)) {
            return ['pass', "No trend detected for {$parameter}", []];
        }

        return [
            'warning',
            "Potential trend detected for {$parameter}",
            ['alerts' => array_values($parameterAlerts)],
        ];
    }

    protected function validateAnomaly(MPFMTest $test, MPFMValidationRule $rule): array
    {
        $historicalService = app(MPFMHistoricalComparisonService::class);
        $comparison = $historicalService->compareWithHistory($test,
            lookbackDays: $rule->thresholds['lookback_days'] ?? 90,
            minTests: $rule->thresholds['min_tests'] ?? 8
        );

        if ($comparison === null) {
            return ['skip', 'Insufficient historical data', []];
        }

        $deviations = $comparison['current_deviations'];
        $parameter = $rule->parameter;
        $zScoreThreshold = $rule->thresholds['z_score_threshold'] ?? 2.0;

        if (!isset($deviations[$parameter])) {
            return ['skip', "No deviation data for {$parameter}", []];
        }

        $zScore = abs($deviations[$parameter]['z_score']);

        if ($zScore > $zScoreThreshold) {
            return [
                'fail',
                "{$parameter} is {$zScore} standard deviations from mean (anomaly)",
                ['z_score' => $zScore, 'threshold' => $zScoreThreshold],
            ];
        }

        return ['pass', "{$parameter} within normal range", []];
    }

    protected function calculateOverallStatus(array $ruleResults): string
    {
        $failures = collect($ruleResults)->where('result', 'fail')->count();
        $warnings = collect($ruleResults)->where('result', 'warning')->count();

        if ($failures > 0) return 'fail';
        if ($warnings > 0) return 'warning';
        return 'pass';
    }

    protected function calculateConfidenceScore(array $ruleResults, MPFMTest $test): int
    {
        $score = 100;

        foreach ($ruleResults as $result) {
            if ($result['result'] === 'fail') {
                $deduction = match ($result['severity']) {
                    'critical' => 40,
                    'major' => 20,
                    'minor' => 10,
                    default => 15
                };
                $score -= $deduction;
            } elseif ($result['result'] === 'warning') {
                $deduction = match ($result['severity']) {
                    'critical' => 20,
                    'major' => 10,
                    'minor' => 5,
                    default => 7
                };
                $score -= $deduction;
            }
        }

        return max(0, min(100, $score));
    }

    protected function generateAutoSuggestion(string $status, int $score, array $ruleResults): string
    {
        // Critical failures always reject
        $hasCriticalFailure = collect($ruleResults)->contains(function ($result) {
            return $result['result'] === 'fail' && $result['severity'] === 'critical';
        });

        if ($hasCriticalFailure) {
            return 'reject';
        }

        // Score-based suggestion
        if ($score >= 80) {
            return 'approve';
        } elseif ($score >= 50) {
            return 'review';
        } else {
            return 'reject';
        }
    }

    protected function detectAnomalies(MPFMTest $test, ?array $historicalComparison): ?array
    {
        if ($historicalComparison === null) {
            return null;
        }

        $deviations = $historicalComparison['current_deviations'] ?? [];
        $anomalies = [];

        foreach ($deviations as $param => $deviation) {
            $zScore = $deviation['z_score'];

            if (abs($zScore) > 2.5) {
                $anomalies[] = [
                    'parameter' => $param,
                    'anomaly_type' => $zScore > 0 ? 'sudden_increase' : 'sudden_decrease',
                    'severity' => abs($zScore) > 3 ? 'critical' : 'warning',
                    'current_value' => $deviation['current_value'],
                    'expected_range' => [
                        round($deviation['average'] * 0.9, 2),
                        round($deviation['average'] * 1.1, 2),
                    ],
                    'deviation' => round(abs($zScore), 1) . ' standard deviations',
                    'message' => ucfirst(str_replace('_', ' ', $param)) . ' ' .
                                ($zScore > 0 ? 'increased' : 'decreased') . ' significantly',
                ];
            }
        }

        return empty($anomalies) ? null : $anomalies;
    }

    protected function generateSummary(array $ruleResults, ?array $anomalies): string
    {
        $failCount = collect($ruleResults)->where('result', 'fail')->count();
        $warningCount = collect($ruleResults)->where('result', 'warning')->count();
        $passCount = collect($ruleResults)->where('result', 'pass')->count();

        $summary = "Validation: {$passCount} passed";

        if ($warningCount > 0) {
            $summary .= ", {$warningCount} warnings";
        }

        if ($failCount > 0) {
            $summary .= ", {$failCount} failures";
        }

        if ($anomalies !== null && count($anomalies) > 0) {
            $summary .= ", " . count($anomalies) . " anomalies detected";
        }

        return $summary . ".";
    }
}
