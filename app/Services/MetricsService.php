<?php

namespace App\Services;

use Illuminate\Support\Arr;

/**
 * MetricsService handles petroleum engineering calculations and metrics
 * for well test data analysis
 */
class MetricsService
{
    /**
     * Calculate standard petroleum engineering metrics for a set of rows
     *
     * @param array $rows Array of data rows from the log file
     * @param array $columns Array of column names
     * @return array Calculated metrics
     */
    public function calculateMetrics(array $rows, array $columns): array
    {
        if (empty($rows)) {
            return [
                'basic_stats' => [],
                'petroleum_metrics' => [],
                'cumulative_totals' => [],
                'derived_metrics' => [],
                'anomalies' => [],
            ];
        }

        // Calculate basic statistics for all numeric columns
        $basicStats = $this->calculateBasicStatistics($rows, $columns);

        // Calculate petroleum-specific metrics
        $petroleumMetrics = $this->calculatePetroleumMetrics($rows, $columns);

        // Calculate cumulative totals
        $cumulativeTotals = $this->calculateCumulativeTotals($rows, $columns);

        // Calculate derived metrics
        $derivedMetrics = $this->calculateDerivedMetrics($rows, $columns);

        // Detect anomalies
        $anomalies = $this->detectAnomalies($rows, $columns);

        return [
            'basic_stats' => $basicStats,
            'petroleum_metrics' => $petroleumMetrics,
            'cumulative_totals' => $cumulativeTotals,
            'derived_metrics' => $derivedMetrics,
            'anomalies' => $anomalies,
        ];
    }

    /**
     * Calculate basic statistics for all numeric columns
     */
    private function calculateBasicStatistics(array $rows, array $columns): array
    {
        $stats = [];

        foreach ($columns as $column) {
            $numericValues = [];

            foreach ($rows as $row) {
                $value = $row[$column] ?? null;
                if ($value !== null && is_numeric($value)) {
                    $numericValues[] = (float) $value;
                }
            }

            if (!empty($numericValues)) {
                $stats[$column] = [
                    'count' => count($numericValues),
                    'sum' => array_sum($numericValues),
                    'average' => array_sum($numericValues) / count($numericValues),
                    'min' => min($numericValues),
                    'max' => max($numericValues),
                    'std_dev' => $this->calculateStandardDeviation($numericValues),
                    'median' => $this->calculateMedian($numericValues),
                    'range' => max($numericValues) - min($numericValues),
                ];
            } else {
                $stats[$column] = [
                    'count' => 0,
                    'sum' => 0,
                    'average' => null,
                    'min' => null,
                    'max' => null,
                    'std_dev' => null,
                    'median' => null,
                    'range' => null,
                ];
            }
        }

        return $stats;
    }

    /**
     * Calculate petroleum engineering specific metrics
     */
    private function calculatePetroleumMetrics(array $rows, array $columns): array
    {
        $metrics = [];

        // Calculate Gas Oil Ratio (GOR)
        $gorValues = [];
        $validGorRows = 0;

        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, ['oil_rate', 'qo', 'oil', 'oil_rate_lc']);
            $gasRate = $this->findFlowRate($row, $columns, ['gas_rate', 'qg', 'gas', 'gas_rate_sc']);

            if ($oilRate !== null && $gasRate !== null && $oilRate > 0) {
                $gor = $gasRate / $oilRate;
                $gorValues[] = $gor;
                $validGorRows++;
            }
        }

        if (!empty($gorValues)) {
            $metrics['gor'] = [
                'values' => $gorValues,
                'average' => array_sum($gorValues) / count($gorValues),
                'min' => min($gorValues),
                'max' => max($gorValues),
                'valid_rows' => $validGorRows,
                'total_rows' => count($rows),
            ];
        }

        // Calculate Water Liquid Ratio (WLR)
        $wlrValues = [];
        $validWlrRows = 0;

        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, ['oil_rate', 'qo', 'oil', 'oil_rate_lc']);
            $waterRate = $this->findFlowRate($row, $columns, ['water_rate', 'qw', 'water', 'water_rate_lc']);

            if ($oilRate !== null && $waterRate !== null) {
                $totalLiquid = $oilRate + $waterRate;
                if ($totalLiquid > 0) {
                    $wlr = $waterRate / $totalLiquid;
                    $wlrValues[] = $wlr;
                    $validWlrRows++;
                }
            }
        }

        if (!empty($wlrValues)) {
            $metrics['wlr'] = [
                'values' => $wlrValues,
                'average' => array_sum($wlrValues) / count($wlrValues),
                'min' => min($wlrValues),
                'max' => max($wlrValues),
                'valid_rows' => $validWlrRows,
                'total_rows' => count($rows),
            ];
        }

        // Calculate Basic Sediment and Water (BSW)
        $bswValues = [];
        $validBswRows = 0;

        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, ['oil_rate', 'qo', 'oil', 'oil_rate_lc']);
            $waterRate = $this->findFlowRate($row, $columns, ['water_rate', 'qw', 'water', 'water_rate_lc']);

            if ($oilRate !== null && $waterRate !== null) {
                $totalLiquid = $oilRate + $waterRate;
                if ($totalLiquid > 0) {
                    $bsw = ($waterRate / $totalLiquid) * 100; // Percentage
                    $bswValues[] = $bsw;
                    $validBswRows++;
                }
            }
        }

        if (!empty($bswValues)) {
            $metrics['bsw'] = [
                'values' => $bswValues,
                'average' => array_sum($bswValues) / count($bswValues),
                'min' => min($bswValues),
                'max' => max($bswValues),
                'valid_rows' => $validBswRows,
                'total_rows' => count($rows),
            ];
        }

        return $metrics;
    }

    /**
     * Calculate cumulative totals
     */
    private function calculateCumulativeTotals(array $rows, array $columns): array
    {
        $cumulatives = [
            'oil' => 0,
            'water' => 0,
            'gas' => 0,
            'total_liquid' => 0,
            'total_fluid' => 0,
        ];

        $timeInterval = $this->estimateTimeInterval($rows, $columns);

        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, ['oil_rate', 'qo', 'oil', 'oil_rate_lc']) ?: 0;
            $waterRate = $this->findFlowRate($row, $columns, ['water_rate', 'qw', 'water', 'water_rate_lc']) ?: 0;
            $gasRate = $this->findFlowRate($row, $columns, ['gas_rate', 'qg', 'gas', 'gas_rate_sc']) ?: 0;

            // Add to cumulative totals based on time interval
            $cumulatives['oil'] += $oilRate * $timeInterval;
            $cumulatives['water'] += $waterRate * $timeInterval;
            $cumulatives['gas'] += $gasRate * $timeInterval;
            $cumulatives['total_liquid'] += ($oilRate + $waterRate) * $timeInterval;
            $cumulatives['total_fluid'] += ($oilRate + $waterRate + $gasRate) * $timeInterval;
        }

        return $cumulatives;
    }

    /**
     * Calculate derived metrics
     */
    private function calculateDerivedMetrics(array $rows, array $columns): array
    {
        $metrics = [];

        // Calculate Gas Void Fraction (GVF)
        $gvfValues = [];
        $validGvfRows = 0;

        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, ['oil_rate', 'qo', 'oil', 'oil_rate_lc']) ?: 0;
            $waterRate = $this->findFlowRate($row, $columns, ['water_rate', 'qw', 'water', 'water_rate_lc']) ?: 0;
            $gasRate = $this->findFlowRate($row, $columns, ['gas_rate', 'qg', 'gas', 'gas_rate_sc']) ?: 0;

            $totalVolume = $oilRate + $waterRate + $gasRate;
            if ($totalVolume > 0) {
                $gvf = ($gasRate / $totalVolume) * 100; // Percentage
                $gvfValues[] = $gvf;
                $validGvfRows++;
            }
        }

        if (!empty($gvfValues)) {
            $metrics['gvf'] = [
                'values' => $gvfValues,
                'average' => array_sum($gvfValues) / count($gvfValues),
                'min' => min($gvfValues),
                'max' => max($gvfValues),
                'valid_rows' => $validGvfRows,
                'total_rows' => count($rows),
                'unit' => '%',
            ];
        }

        // Calculate Gas Liquid Ratio (GLR)
        $glrValues = [];
        $validGlrRows = 0;

        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, ['oil_rate', 'qo', 'oil', 'oil_rate_lc']) ?: 0;
            $waterRate = $this->findFlowRate($row, $columns, ['water_rate', 'qw', 'water', 'water_rate_lc']) ?: 0;
            $gasRate = $this->findFlowRate($row, $columns, ['gas_rate', 'qg', 'gas', 'gas_rate_sc']) ?: 0;

            $liquidRate = $oilRate + $waterRate;
            if ($liquidRate > 0 && $gasRate > 0) {
                $glr = $gasRate / $liquidRate;
                $glrValues[] = $glr;
                $validGlrRows++;
            }
        }

        if (!empty($glrValues)) {
            $metrics['glr'] = [
                'values' => $glrValues,
                'average' => array_sum($glrValues) / count($glrValues),
                'min' => min($glrValues),
                'max' => max($glrValues),
                'valid_rows' => $validGlrRows,
                'total_rows' => count($rows),
                'unit' => 'scf/STB',
            ];
        }

        return $metrics;
    }

    /**
     * Detect anomalies based on thresholds
     */
    private function detectAnomalies(array $rows, array $columns): array
    {
        $anomalies = [];

        // Define thresholds for common petroleum engineering parameters
        $thresholds = [
            'pressure' => ['min' => 0, 'max' => 15000, 'pattern' => '/(pressure|pres|p|whp|bhfp|upstream|downstream)/i'],
            'temperature' => ['min' => -50, 'max' => 300, 'pattern' => '/(temperature|temp|t|bhft)/i'],
            'flow_rate' => ['min' => 0, 'max' => 50000, 'pattern' => '/(rate|flow|qo|qw|qg|oil|water|gas)/i'],
            'gor' => ['min' => 0, 'max' => 10000, 'pattern' => '/gor/i'],
            'bsw' => ['min' => 0, 'max' => 100, 'pattern' => '/(bsw|water_cut|watercut)/i'],
            'voltage' => ['min' => 0, 'max' => 30, 'pattern' => '/(voltage|volt|v)/i'],
        ];

        foreach ($rows as $rowIndex => $row) {
            foreach ($columns as $column) {
                $value = $row[$column] ?? null;

                if ($value === null || !is_numeric($value)) {
                    continue;
                }

                $value = (float) $value;

                // Check against thresholds
                foreach ($thresholds as $thresholdName => $threshold) {
                    if (preg_match($threshold['pattern'], $column)) {
                        if ($value < $threshold['min'] || $value > $threshold['max']) {
                            $anomalies[] = [
                                'row_index' => $rowIndex,
                                'column' => $column,
                                'value' => $value,
                                'threshold' => $threshold,
                                'type' => $thresholdName,
                                'message' => "Value {$value} is outside the normal range ({$threshold['min']}-{$threshold['max']}) for {$thresholdName}",
                            ];
                        }
                    }
                }
            }
        }

        return $anomalies;
    }

    /**
     * Find flow rate by searching for common column names
     */
    private function findFlowRate(array $row, array $columns, array $possibleNames): ?float
    {
        foreach ($possibleNames as $name) {
            // Direct match
            if (isset($row[$name]) && is_numeric($row[$name])) {
                return (float) $row[$name];
            }

            // Case-insensitive match
            foreach ($columns as $column) {
                if (strtolower($column) === strtolower($name) && isset($row[$column]) && is_numeric($row[$column])) {
                    return (float) $row[$column];
                }
            }

            // Partial match
            foreach ($columns as $column) {
                if (stripos($column, $name) !== false && isset($row[$column]) && is_numeric($row[$column])) {
                    return (float) $row[$column];
                }
            }
        }

        return null;
    }

    /**
     * Estimate time interval between measurements based on timestamps
     */
    private function estimateTimeInterval(array $rows, array $columns): float
    {
        // Look for time-related columns
        $timeColumns = array_filter($columns, function($col) {
            return preg_match('/time|date|timestamp|interval|duration/i', $col);
        });

        if (!empty($timeColumns)) {
            // If we have time columns, try to calculate interval
            $firstTime = null;
            $secondTime = null;

            foreach ($timeColumns as $timeCol) {
                if (isset($rows[0][$timeCol])) {
                    $firstTime = $this->parseTimeValue($rows[0][$timeCol]);
                }
                if (isset($rows[1][$timeCol])) {
                    $secondTime = $this->parseTimeValue($rows[1][$timeCol]);
                }

                if ($firstTime && $secondTime) {
                    $interval = abs($secondTime - $firstTime);
                    return $interval > 0 ? $interval : 1; // Default to 1 if interval is 0
                }
            }
        }

        // Default to 1 hour if no time data available
        return 1;
    }

    /**
     * Parse time value from various formats
     */
    private function parseTimeValue($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Try to parse as date/time string
        if (is_string($value)) {
            $timestamp = strtotime($value);
            if ($timestamp !== false) {
                return $timestamp;
            }
        }

        return 0;
    }

    /**
     * Calculate standard deviation
     */
    private function calculateStandardDeviation(array $values): float
    {
        if (count($values) <= 1) {
            return 0;
        }

        $mean = array_sum($values) / count($values);
        $sumSquaredDifferences = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values));

        return sqrt($sumSquaredDifferences / (count($values) - 1));
    }

    /**
     * Calculate median
     */
    private function calculateMedian(array $values): float
    {
        if (empty($values)) {
            return 0;
        }

        sort($values);
        $count = count($values);
        $middle = floor($count / 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return $values[$middle];
    }

    /**
     * Calculate comprehensive summary metrics
     */
    public function calculateSummaryMetrics(array $rows, array $columns): array
    {
        $metrics = $this->calculateMetrics($rows, $columns);

        // Calculate additional summary metrics
        $summary = [
            'total_rows' => count($rows),
            'total_columns' => count($columns),
            'numeric_columns' => $this->countNumericColumns($rows, $columns),
            'data_quality_score' => $this->calculateDataQualityScore($rows, $columns),
            'completeness' => $this->calculateCompleteness($rows, $columns),
            'basic_statistics' => $metrics['basic_stats'],
            'petroleum_metrics' => $metrics['petroleum_metrics'],
            'derived_metrics' => $metrics['derived_metrics'],
            'cumulative_totals' => $metrics['cumulative_totals'],
            'anomalies_count' => count($metrics['anomalies']),
        ];

        return $summary;
    }

    /**
     * Count numeric columns
     */
    private function countNumericColumns(array $rows, array $columns): int
    {
        $numericCount = 0;

        foreach ($columns as $column) {
            $isNumeric = false;
            foreach ($rows as $row) {
                if (isset($row[$column]) && is_numeric($row[$column])) {
                    $isNumeric = true;
                    break;
                }
            }
            if ($isNumeric) {
                $numericCount++;
            }
        }

        return $numericCount;
    }

    /**
     * Calculate data quality score
     */
    private function calculateDataQualityScore(array $rows, array $columns): float
    {
        if (empty($rows) || empty($columns)) {
            return 0;
        }

        $totalCells = count($rows) * count($columns);
        $validCells = 0;

        foreach ($rows as $row) {
            foreach ($columns as $column) {
                if (isset($row[$column]) && $row[$column] !== null && $row[$column] !== '') {
                    $validCells++;
                }
            }
        }

        return ($validCells / $totalCells) * 100;
    }

    /**
     * Calculate completeness percentage
     */
    private function calculateCompleteness(array $rows, array $columns): float
    {
        if (empty($rows) || empty($columns)) {
            return 0;
        }

        $totalCells = count($rows) * count($columns);
        $filledCells = 0;

        foreach ($rows as $row) {
            foreach ($columns as $column) {
                if (isset($row[$column]) && $row[$column] !== null && trim($row[$column]) !== '') {
                    $filledCells++;
                }
            }
        }

        return ($filledCells / $totalCells) * 100;
    }
}
