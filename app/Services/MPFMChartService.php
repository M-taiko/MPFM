<?php

namespace App\Services;

use Carbon\Carbon;

class MPFMChartService
{
    /**
     * Prepare rates chart data
     */
    public function prepareRatesChartData($columns, $rows, $timeColumn)
    {
        $chartData = ['labels' => [], 'datasets' => []];

        // Define all possible rate columns we want to include
        $possibleRateColumns = [
            'qo_sc[STB/d]' => 'Oil Rate (STB/d)',
            'qo_lc[bbl/d]' => 'Oil Rate (bbl/d)',
            'qw_sc[STB/d]' => 'Water Rate (STB/d)',
            'qw_lc[bbl/d]' => 'Water Rate (bbl/d)',
            'qg_sc[MMScf/d]' => 'Gas Rate (MMScf/d)',
            'qg_lc[Mcf/d]' => 'Gas Rate (Mcf/d)'
        ];

        // Find which rate columns actually exist in the data
        $availableRateColumns = [];
        foreach ($possibleRateColumns as $pattern => $label) {
            $actualCol = $this->findColumnByKeywords($columns, [$pattern]);
            if ($actualCol) {
                $availableRateColumns[$actualCol] = $label;
            }
        }

        // If no rate columns found, return empty chart data
        if (empty($availableRateColumns)) {
            return $chartData;
        }

        // Extract time labels
        $labels = [];
        foreach ($rows as $row) {
            $timeStr = $row[$timeColumn] ?? '';
            // Parse the time string safely
            $parsedTime = $this->parseDateTimeSafe($timeStr, Carbon::today());
            if ($parsedTime) {
                $labels[] = $parsedTime->format('H:i');
            } else {
                $labels[] = 'N/A';
            }
        }
        $chartData['labels'] = $labels;

        // Create datasets for each available rate column
        $colors = ['green', 'blue', 'red', 'orange', 'purple', 'brown'];
        $colorIndex = 0;

        foreach ($availableRateColumns as $column => $label) {
            $data = [];
            foreach ($rows as $row) {
                $data[] = $this->parseNumeric($row[$column] ?? 0);
            }

            $chartData['datasets'][] = [
                'label' => $label,
                'data' => $data,
                'borderColor' => $colors[$colorIndex % count($colors)],
                'tension' => 0.2,
                'fill' => false,
            ];

            $colorIndex++;
        }

        return $chartData;
    }

    /**
     * Prepare GOR chart data
     */
    public function prepareGorChartData($columns, $rows, $timeColumn)
    {
        $chartData = ['labels' => [], 'datasets' => []];

        // Find GOR column
        $gorCol = $this->findColumnByKeywords($columns, ['GOR[Scf/STB]']);
        if (!$gorCol) {
            return $chartData;
        }

        // Extract time labels
        $labels = [];
        foreach ($rows as $row) {
            $timeStr = $row[$timeColumn] ?? '';
            // Parse the time string safely
            $parsedTime = $this->parseDateTimeSafe($timeStr, Carbon::today());
            if ($parsedTime) {
                $labels[] = $parsedTime->format('H:i');
            } else {
                $labels[] = 'N/A';
            }
        }
        $chartData['labels'] = $labels;

        // Extract GOR data
        $data = [];
        foreach ($rows as $row) {
            $data[] = $this->parseNumeric($row[$gorCol] ?? 0);
        }

        $chartData['datasets'][] = [
            'label' => 'GOR (Scf/STB)',
            'data' => $data,
            'borderColor' => 'purple',
            'tension' => 0.2,
            'fill' => false,
        ];

        return $chartData;
    }

    /**
     * Prepare full average row
     */
    public function prepareFullAverageRow($parsedData)
    {
        $avgRow = [];
        if (!empty($parsedData['test_data']['rows'])) {
            $columns = $parsedData['test_data']['columns'];
            foreach ($columns as $col) {
                // Only calculate averages for numeric columns
                $sampleValue = $parsedData['test_data']['rows'][0][$col] ?? '';
                if (is_numeric($this->parseNumeric($sampleValue)) || $this->isLikelyNumericColumn($col)) {
                    // Check if this is a water-related column that needs negative correction
                    $isWaterColumn = $this->isWaterRelatedColumn($col);

                    if ($isWaterColumn) {
                        // Use negative correction for water-related columns
                        $values = array_map(fn($r) => $this->parseNumericWithNegativeCorrection($r[$col] ?? 0), $parsedData['test_data']['rows']);
                    } else {
                        // Use regular parsing for non-water columns
                        $values = array_map(fn($r) => $this->parseNumeric($r[$col] ?? 0), $parsedData['test_data']['rows']);
                    }

                    // Filter out zero values that might be N/A placeholders
                    $numericValues = array_filter($values, fn($v) => $this->isValidNumber($v) && ($v != 0 || ($v == 0 && isset($parsedData['test_data']['rows'][0][$col]) && is_numeric($parsedData['test_data']['rows'][0][$col]))));
                    $avgRow[$col] = count($numericValues) > 0 ? array_sum($numericValues) / count($numericValues) : 0;
                } else {
                    $avgRow[$col] = '#N/A';
                }
            }
        }
        return $avgRow;
    }

    /**
     * Find column by keywords
     */
    private function findColumnByKeywords($columns, $keywords)
    {
        foreach ($columns as $col) {
            foreach ($keywords as $keyword) {
                if (stripos($col, $keyword) !== false) {
                    return $col;
                }
            }
        }
        return null;
    }

    /**
     * Enhanced numeric parsing function with better handling of special values
     */
    private function parseNumeric($value)
    {
        if (is_null($value) || $value === '' ||
            stripos($value, 'N/A') !== false ||
            stripos($value, '#VALUE!') !== false ||
            stripos($value, 'NULL') !== false) {
            return 0;
        }

        // Clean the value by removing non-numeric characters except decimal point and minus sign
        $cleanedValue = preg_replace('/[^\d\.\-\+]/', '', $value);

        // Handle cases where multiple decimal points might exist
        if (substr_count($cleanedValue, '.') > 1) {
            // Keep only the first decimal point
            $parts = explode('.', $cleanedValue);
            $cleanedValue = $parts[0] . '.' . implode('', array_slice($parts, 1));
        }

        // Handle cases where plus/minus signs are not at the beginning
        if (preg_match('/^[\d\.\-]+$/', $cleanedValue)) {
            return floatval($cleanedValue);
        }

        return 0;
    }

    /**
     * Parse numeric value and treat negative values as 0 for water-related metrics
     */
    private function parseNumericWithNegativeCorrection($value)
    {
        $parsedValue = $this->parseNumeric($value);
        // For water-related metrics, treat negative values as 0
        return $parsedValue < 0 ? 0 : $parsedValue;
    }

    /**
     * Enhanced datetime parsing with fallback
     */
    private function parseDateTimeSafe($dateTimeStr, $defaultDate)
    {
        if (!$dateTimeStr || trim($dateTimeStr) === '') return null;

        try {
            // Try different datetime formats
            $formats = [
                'Y-m-d H:i:s',
                'd/m/Y H:i:s',
                'm/d/Y H:i:s',
                'Y-m-d H:i',
                'd/m/Y H:i',
                'm/d/Y H:i',
                'Y-m-d',
                'd/m/Y',
                'm/d/Y'
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, trim($dateTimeStr));
                if ($date) {
                    return Carbon::instance($date);
                }
            }

            // Fallback to default parser
            return Carbon::parse(trim($dateTimeStr));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper function to determine if a column is likely to contain numeric data
     */
    private function isLikelyNumericColumn($columnName)
    {
        $numericIndicators = ['rate', 'pressure', 'temp', 'volume', 'gor', 'bsw', 'wlr', 'gvf', 'h2s'];
        $lowerColumnName = strtolower($columnName);

        foreach ($numericIndicators as $indicator) {
            if (strpos($lowerColumnName, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Helper function to determine if a column is water-related and needs negative value correction
     */
    private function isWaterRelatedColumn($columnName)
    {
        $waterIndicators = ['water', 'wlr', 'bsw', 'h2s'];
        $lowerColumnName = strtolower($columnName);

        foreach ($waterIndicators as $indicator) {
            if (strpos($lowerColumnName, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a value is a valid numeric value (not N/A or empty)
     */
    private function isValidNumber($value)
    {
        if ($value === null || $value === '' || is_nan($value)) {
            return false;
        }

        $strValue = strtolower(strval($value));
        return !(strpos($strValue, 'n/a') !== false ||
                strpos($strValue, '#value!') !== false ||
                strpos($strValue, 'null') !== false);
    }
}