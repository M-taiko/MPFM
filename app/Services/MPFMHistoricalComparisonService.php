<?php

namespace App\Services;

use App\Models\MPFMTest;
use App\Models\MPFMTestComparison;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MPFMHistoricalComparisonService
{
    public function compareWithHistory(MPFMTest $test, int $lookbackDays = 90, int $minTests = 5): ?array
    {
        // Get historical approved tests for same string
        $historicalTests = $this->getHistoricalTests($test, $lookbackDays);

        if ($historicalTests->count() < $minTests) {
            return null; // Not enough data
        }

        // Calculate statistics
        $statistics = $this->calculateStatistics($historicalTests);

        // Calculate current test deviations
        $deviations = $this->calculateDeviations($test, $statistics);

        // Detect trends
        $trends = $this->detectTrends($historicalTests);

        // Save comparison record
        MPFMTestComparison::create([
            'current_test_id' => $test->id,
            'oil_string_id' => $test->oil_string_id,
            'compared_test_ids' => $historicalTests->pluck('id')->toArray(),
            'comparison_data' => [
                'statistics' => $statistics,
                'deviations' => $deviations,
                'trends' => $trends,
            ],
        ]);

        return [
            'tests_compared' => $historicalTests->count(),
            'date_range' => [
                'from' => $historicalTests->min('test_date'),
                'to' => $historicalTests->max('test_date'),
            ],
            'statistics' => $statistics,
            'current_deviations' => $deviations,
            'trends' => $trends,
        ];
    }

    protected function getHistoricalTests(MPFMTest $test, int $lookbackDays): Collection
    {
        $startDate = Carbon::now()->subDays($lookbackDays);

        return MPFMTest::where('oil_string_id', $test->oil_string_id)
            ->where('id', '!=', $test->id)
            ->where('status', 'approved')
            ->where('test_date', '>=', $startDate)
            ->orderBy('test_date', 'desc')
            ->limit(20)
            ->get();
    }

    protected function calculateStatistics(Collection $tests): array
    {
        $parameters = ['oil_rate', 'gas_rate', 'water_cut', 'pressure', 'temperature', 'gor'];
        $statistics = [];

        foreach ($parameters as $param) {
            $values = $tests->pluck($param)->filter()->values();

            if ($values->isEmpty()) continue;

            $statistics[$param] = [
                'avg' => round($values->avg(), 2),
                'median' => round($this->median($values), 2),
                'std_dev' => round($this->standardDeviation($values), 2),
                'min' => $values->min(),
                'max' => $values->max(),
                'count' => $values->count(),
            ];
        }

        return $statistics;
    }

    protected function calculateDeviations(MPFMTest $currentTest, array $statistics): array
    {
        $parameters = array_keys($statistics);
        $deviations = [];

        foreach ($parameters as $param) {
            $currentValue = $currentTest->$param;
            $stats = $statistics[$param];

            if ($currentValue === null) continue;

            $percentDiff = (($currentValue - $stats['avg']) / $stats['avg']) * 100;
            $zScore = ($currentValue - $stats['avg']) / $stats['std_dev'];

            $deviations[$param] = [
                'current_value' => $currentValue,
                'average' => $stats['avg'],
                'percent_diff' => round($percentDiff, 2),
                'z_score' => round($zScore, 2),
                'deviation_type' => $this->getDeviationType($percentDiff, $zScore),
            ];
        }

        return $deviations;
    }

    protected function detectTrends(Collection $tests): array
    {
        $parameters = ['oil_rate', 'water_cut', 'gor', 'pressure'];
        $trends = [];

        foreach ($parameters as $param) {
            $data = $tests->map(function ($test, $index) use ($param) {
                return [
                    'x' => $index,
                    'y' => $test->$param
                ];
            })->filter(function ($point) {
                return $point['y'] !== null;
            })->values();

            if ($data->count() < 3) continue;

            // Linear regression
            $regression = $this->linearRegression($data);

            $trends[$param] = [
                'slope' => round($regression['slope'], 4),
                'intercept' => round($regression['intercept'], 2),
                'r_squared' => round($regression['r_squared'], 4),
                'direction' => $regression['slope'] > 0.1 ? 'increasing' :
                              ($regression['slope'] < -0.1 ? 'decreasing' : 'stable'),
                'significance' => $regression['r_squared'] > 0.7 ? 'high' :
                                 ($regression['r_squared'] > 0.4 ? 'medium' : 'low'),
            ];
        }

        return $trends;
    }

    public function getStringTrendingAnalysis($oilStringId, int $lookbackDays = 180): array
    {
        $tests = MPFMTest::where('oil_string_id', $oilStringId)
            ->where('status', 'approved')
            ->where('test_date', '>=', Carbon::now()->subDays($lookbackDays))
            ->orderBy('test_date', 'asc')
            ->get();

        if ($tests->count() < 3) {
            return [
                'alert_count' => 0,
                'alerts' => [],
                'message' => 'Insufficient data for trend analysis',
            ];
        }

        $statistics = $this->calculateStatistics($tests);
        $trends = $this->detectTrends($tests);
        $alerts = [];

        // Generate alerts based on trends
        foreach ($trends as $param => $trend) {
            if ($trend['direction'] === 'increasing' && $trend['significance'] === 'high') {
                $alerts[] = [
                    'parameter' => $param,
                    'type' => 'increasing_trend',
                    'severity' => 'warning',
                    'message' => "Increasing trend detected for {$param}",
                ];
            } elseif ($trend['direction'] === 'decreasing' && $trend['significance'] === 'high') {
                $alerts[] = [
                    'parameter' => $param,
                    'type' => 'decreasing_trend',
                    'severity' => 'warning',
                    'message' => "Decreasing trend detected for {$param}",
                ];
            }
        }

        return [
            'tests_analyzed' => $tests->count(),
            'statistics' => $statistics,
            'trends' => $trends,
            'alert_count' => count($alerts),
            'alerts' => $alerts,
        ];
    }

    // Helper methods
    protected function median(Collection $values): float
    {
        $sorted = $values->sort()->values();
        $count = $sorted->count();

        if ($count % 2 === 0) {
            return ($sorted[$count / 2 - 1] + $sorted[$count / 2]) / 2;
        }

        return $sorted[floor($count / 2)];
    }

    protected function standardDeviation(Collection $values): float
    {
        $avg = $values->avg();
        $squareDiffs = $values->map(function ($value) use ($avg) {
            return pow($value - $avg, 2);
        });

        return sqrt($squareDiffs->avg());
    }

    protected function linearRegression(Collection $data): array
    {
        $n = $data->count();
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($data as $point) {
            $sumX += $point['x'];
            $sumY += $point['y'];
            $sumXY += $point['x'] * $point['y'];
            $sumX2 += $point['x'] * $point['x'];
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Calculate R-squared
        $yMean = $sumY / $n;
        $ssRes = 0;
        $ssTot = 0;

        foreach ($data as $point) {
            $yPred = $slope * $point['x'] + $intercept;
            $ssRes += pow($point['y'] - $yPred, 2);
            $ssTot += pow($point['y'] - $yMean, 2);
        }

        $rSquared = $ssTot > 0 ? 1 - ($ssRes / $ssTot) : 0;

        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'r_squared' => max(0, min(1, $rSquared)),
        ];
    }

    protected function getDeviationType(float $percentDiff, float $zScore): string
    {
        if (abs($zScore) > 2.5) return 'critical';
        if (abs($zScore) > 2) return 'anomaly';
        if (abs($percentDiff) > 20) return 'significant';
        if (abs($percentDiff) > 10) return 'notable';
        return 'normal';
    }
}
