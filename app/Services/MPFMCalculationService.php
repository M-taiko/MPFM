<?php

namespace App\Services;

class MPFMCalculationService
{
    /**
     * Calculate standardized metrics from test data rows
     */
    public function calculateStandardizedMetrics($rows)
    {
        $averages = [];

        // Oil Rate - check for multiple possible columns
        $oilRateCols = ['qo_sc[STB/d]', 'qo_lc[bbl/d]'];
        $averages['oil_rate'] = $this->findAndCalculateAverage($rows, $oilRateCols);

        // Water Rate - check for multiple possible columns (with negative value correction)
        $waterRateCols = ['qw_sc[STB/d]', 'qw_lc[bbl/d]'];
        $averages['water_rate'] = $this->findAndCalculateAverageWithNegativeCorrection($rows, $waterRateCols);

        // Gas Rate - check for multiple possible columns
        $gasRateCols = ['qg_sc[MMScf/d]', 'qg_lc[Mcf/d]'];
        $averages['gas_rate'] = $this->findAndCalculateAverage($rows, $gasRateCols);

        // GOR
        $gorCols = ['GOR[Scf/STB]'];
        $averages['gor'] = $this->findAndCalculateAverage($rows, $gorCols);

        // BSW (with negative value correction)
        $bswCols = ['BSW[%]'];
        $averages['bsw'] = $this->findAndCalculateAverageWithNegativeCorrection($rows, $bswCols);

        // WLR (with negative value correction)
        $wlrCols = ['WLR[%]'];
        $averages['wlr'] = $this->findAndCalculateAverageWithNegativeCorrection($rows, $wlrCols);

        // GVF
        $gvfCols = ['GVF[%]'];
        $averages['gvf'] = $this->findAndCalculateAverage($rows, $gvfCols);

        // Additional metrics
        $averages['max_pressure'] = $this->calculateMaxForPressureColumns($rows);
        $averages['min_pressure'] = $this->calculateMinForPressureColumns($rows);
        $averages['avg_pressure'] = $this->calculateAvgForPressureColumns($rows);

        $averages['max_temperature'] = $this->calculateMaxForTemperatureColumns($rows);
        $averages['min_temperature'] = $this->calculateMinForTemperatureColumns($rows);
        $averages['avg_temperature'] = $this->calculateAvgForTemperatureColumns($rows);

        // Cumulative volumes
        $averages['cumulative_oil_volume'] = $this->calculateSumForVolumeColumns($rows, ['cvo_*']);
        $averages['cumulative_water_volume'] = $this->calculateSumForVolumeColumnsWithNegativeCorrection($rows, ['cvw_*']);
        $averages['cumulative_gas_volume'] = $this->calculateSumForVolumeColumns($rows, ['cvg_*']);

        // H2S metrics (with negative value correction)
        $h2sCols = ['mH2S_sc', 'H2SMfrInHC%', 'H2SMfrFix%'];
        $averages['h2s_avg'] = $this->findAndCalculateAverageWithNegativeCorrection($rows, $h2sCols);
        $averages['h2s_max'] = $this->findAndCalculateMaxWithNegativeCorrection($rows, $h2sCols);
        $averages['h2s_min'] = $this->findAndCalculateMinWithNegativeCorrection($rows, $h2sCols);

        // Derived metrics
        $derivedMetrics = $this->calculateDerivedMetrics($rows);
        $averages = array_merge($averages, $derivedMetrics);

        // Instrumentation safety checks
        $safetyChecks = $this->checkInstrumentationSafety($rows);
        $averages['safety_issues'] = $safetyChecks;

        return $averages;
    }

    /**
     * Calculate derived metrics (WLR, GOR, BSW, GVF) based on formulas
     */
    public function calculateDerivedMetrics($rows)
    {
        $derivedMetrics = [];

        // Get oil rate
        $oilRate = $this->findAndCalculateAverage($rows, ['qo_sc[STB/d]', 'qo_lc[bbl/d]']);

        // Get water rate with negative correction
        $waterRate = $this->findAndCalculateAverageWithNegativeCorrection($rows, ['qw_sc[STB/d]', 'qw_lc[bbl/d]']);

        // Get gas rate
        $gasRate = $this->findAndCalculateAverage($rows, ['qg_sc[MMScf/d]', 'qg_lc[Mcf/d]']);

        // Calculate WLR: Water Rate / Oil Rate * 100 (using corrected water rate)
        if (is_numeric($oilRate) && $oilRate > 0 && is_numeric($waterRate)) {
            $derivedMetrics['wlr_formula'] = ($waterRate / $oilRate) * 100;
        } else {
            $derivedMetrics['wlr_formula'] = 'N/A';
        }

        // Calculate GOR: Gas Rate / Oil Rate
        if (is_numeric($oilRate) && $oilRate > 0 && is_numeric($gasRate)) {
            $derivedMetrics['gor_formula'] = ($gasRate / $oilRate);
        } else {
            $derivedMetrics['gor_formula'] = 'N/A';
        }

        // Calculate BSW: Water Volume / (Oil + Water) * 100 (using corrected water rate)
        if (is_numeric($waterRate) && is_numeric($oilRate) && ($oilRate + $waterRate) > 0) {
            $derivedMetrics['bsw_formula'] = ($waterRate / ($oilRate + $waterRate)) * 100;
        } else {
            $derivedMetrics['bsw_formula'] = 'N/A';
        }

        // Calculate GVF: Gas Volume / Total Volume * 100 (using corrected water rate)
        $totalVolume = $oilRate + $waterRate + $gasRate;
        if (is_numeric($gasRate) && $totalVolume > 0) {
            $derivedMetrics['gvf_formula'] = ($gasRate / $totalVolume) * 100;
        } else {
            $derivedMetrics['gvf_formula'] = 'N/A';
        }

        return $derivedMetrics;
    }

    /**
     * Enhanced function to find a column and calculate its average
     */
    private function findAndCalculateAverage($rows, $keywords)
    {
        if (empty($rows)) {
            return 'N/A';
        }
        $columns = array_keys($rows[0]);

        foreach ($columns as $col) {
            foreach ($keywords as $keyword) {
                // Handle wildcard patterns
                if (strpos($keyword, '*') !== false) {
                    $pattern = str_replace('*', '', $keyword);
                    if (stripos($col, $pattern) !== false) {
                        return $this->calculateAverageForColumn($rows, $col);
                    }
                } else {
                    if (stripos($col, $keyword) !== false) {
                        return $this->calculateAverageForColumn($rows, $col);
                    }
                }
            }
        }

        return 'N/A';
    }

    /**
     * Find a column and calculate its maximum value
     */
    private function findAndCalculateMax($rows, $keywords)
    {
        if (empty($rows)) {
            return 'N/A';
        }
        $columns = array_keys($rows[0]);

        foreach ($columns as $col) {
            foreach ($keywords as $keyword) {
                if (stripos($col, $keyword) !== false) {
                    return $this->calculateMaxForColumn($rows, $col);
                }
            }
        }

        return 'N/A';
    }

    /**
     * Find a column and calculate its minimum value
     */
    private function findAndCalculateMin($rows, $keywords)
    {
        if (empty($rows)) {
            return 'N/A';
        }
        $columns = array_keys($rows[0]);

        foreach ($columns as $col) {
            foreach ($keywords as $keyword) {
                if (stripos($col, $keyword) !== false) {
                    return $this->calculateMinForColumn($rows, $col);
                }
            }
        }

        return 'N/A';
    }

    /**
     * Calculate average for a specific column
     */
    private function calculateAverageForColumn($rows, $column)
    {
        $values = [];
        foreach ($rows as $row) {
            // Skip rows where the column doesn't exist
            if (!isset($row[$column])) {
                continue;
            }

            $parsedValue = $this->parseNumeric($row[$column]);
            // Only include valid numeric values
            if ($this->isValidNumber($parsedValue) && ($parsedValue != 0 || ($parsedValue == 0 && is_numeric($row[$column])))) {
                $values[] = $parsedValue;
            }
        }
        return count($values) > 0 ? array_sum($values) / count($values) : 0;
    }

    /**
     * Calculate maximum for a specific column
     */
    private function calculateMaxForColumn($rows, $column)
    {
        $values = [];
        foreach ($rows as $row) {
            // Skip rows where the column doesn't exist
            if (!isset($row[$column])) {
                continue;
            }

            $parsedValue = $this->parseNumeric($row[$column]);
            // Only include valid numeric values
            if ($this->isValidNumber($parsedValue) && ($parsedValue != 0 || ($parsedValue == 0 && is_numeric($row[$column])))) {
                $values[] = $parsedValue;
            }
        }
        return count($values) > 0 ? max($values) : 0;
    }

    /**
     * Calculate minimum for a specific column
     */
    private function calculateMinForColumn($rows, $column)
    {
        $values = [];
        foreach ($rows as $row) {
            // Skip rows where the column doesn't exist
            if (!isset($row[$column])) {
                continue;
            }

            $parsedValue = $this->parseNumeric($row[$column]);
            // Only include valid numeric values
            if ($this->isValidNumber($parsedValue) && ($parsedValue != 0 || ($parsedValue == 0 && is_numeric($row[$column])))) {
                $values[] = $parsedValue;
            }
        }
        return count($values) > 0 ? min($values) : 0;
    }

    /**
     * Calculate maximum for pressure columns
     */
    private function calculateMaxForPressureColumns($rows)
    {
        $pressureCols = ['Dyn_DP[mbar]', 'PL[bara]', 'WHP[psi]'];
        $maxValues = [];

        foreach ($pressureCols as $colPattern) {
            $actualCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$colPattern]);
            if ($actualCol) {
                $maxValues[] = $this->calculateMaxForColumn($rows, $actualCol);
            }
        }

        return count($maxValues) > 0 ? max($maxValues) : 'N/A';
    }

    /**
     * Calculate minimum for pressure columns
     */
    private function calculateMinForPressureColumns($rows)
    {
        $pressureCols = ['Dyn_DP[mbar]', 'PL[bara]', 'WHP[psi]'];
        $minValues = [];

        foreach ($pressureCols as $colPattern) {
            $actualCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$colPattern]);
            if ($actualCol) {
                $minValues[] = $this->calculateMinForColumn($rows, $actualCol);
            }
        }

        return count($minValues) > 0 ? min($minValues) : 'N/A';
    }

    /**
     * Calculate average for pressure columns
     */
    private function calculateAvgForPressureColumns($rows)
    {
        $pressureCols = ['Dyn_DP[mbar]', 'PL[bara]', 'WHP[psi]'];
        $avgValues = [];

        foreach ($pressureCols as $colPattern) {
            $actualCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$colPattern]);
            if ($actualCol) {
                $avgValues[] = $this->calculateAverageForColumn($rows, $actualCol);
            }
        }

        return count($avgValues) > 0 ? array_sum($avgValues) / count($avgValues) : 'N/A';
    }

    /**
     * Calculate maximum for temperature columns
     */
    private function calculateMaxForTemperatureColumns($rows)
    {
        $tempCols = ['TL[DegC]', 'DAFC_Temp'];
        $maxValues = [];

        foreach ($tempCols as $colPattern) {
            $actualCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$colPattern]);
            if ($actualCol) {
                $maxValues[] = $this->calculateMaxForColumn($rows, $actualCol);
            }
        }

        return count($maxValues) > 0 ? max($maxValues) : 'N/A';
    }

    /**
     * Calculate minimum for temperature columns
     */
    private function calculateMinForTemperatureColumns($rows)
    {
        $tempCols = ['TL[DegC]', 'DAFC_Temp'];
        $minValues = [];

        foreach ($tempCols as $colPattern) {
            $actualCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$colPattern]);
            if ($actualCol) {
                $minValues[] = $this->calculateMinForColumn($rows, $actualCol);
            }
        }

        return count($minValues) > 0 ? min($minValues) : 'N/A';
    }

    /**
     * Calculate average for temperature columns
     */
    private function calculateAvgForTemperatureColumns($rows)
    {
        $tempCols = ['TL[DegC]', 'DAFC_Temp'];
        $avgValues = [];

        foreach ($tempCols as $colPattern) {
            $actualCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$colPattern]);
            if ($actualCol) {
                $avgValues[] = $this->calculateAverageForColumn($rows, $actualCol);
            }
        }

        return count($avgValues) > 0 ? array_sum($avgValues) / count($avgValues) : 'N/A';
    }

    /**
     * Calculate sum for volume columns with wildcard support
     */
    private function calculateSumForVolumeColumns($rows, $patterns)
    {
        $sum = 0;
        $columns = array_keys($rows[0] ?? []);

        foreach ($patterns as $pattern) {
            if (strpos($pattern, '*') !== false) {
                $prefix = str_replace('*', '', $pattern);
                // Find all columns that match the prefix
                foreach ($columns as $col) {
                    if (stripos($col, $prefix) !== false) {
                        foreach ($rows as $row) {
                            $sum += $this->parseNumeric($row[$col] ?? 0);
                        }
                    }
                }
            } else {
                $actualCol = $this->findColumnByKeywords($columns, [$pattern]);
                if ($actualCol) {
                    foreach ($rows as $row) {
                        $sum += $this->parseNumeric($row[$actualCol] ?? 0);
                    }
                }
            }
        }

        return $sum > 0 ? $sum : 'N/A';
    }

    /**
     * Enhanced function to find a column and calculate its average with negative value correction
     */
    private function findAndCalculateAverageWithNegativeCorrection($rows, $keywords)
    {
        if (empty($rows)) {
            return 'N/A';
        }
        $columns = array_keys($rows[0]);

        foreach ($columns as $col) {
            foreach ($keywords as $keyword) {
                // Handle wildcard patterns
                if (strpos($keyword, '*') !== false) {
                    $pattern = str_replace('*', '', $keyword);
                    if (stripos($col, $pattern) !== false) {
                        return $this->calculateAverageForColumnWithNegativeCorrection($rows, $col);
                    }
                } else {
                    if (stripos($col, $keyword) !== false) {
                        return $this->calculateAverageForColumnWithNegativeCorrection($rows, $col);
                    }
                }
            }
        }

        return 'N/A';
    }

    /**
     * Find a column and calculate its maximum value with negative value correction
     */
    private function findAndCalculateMaxWithNegativeCorrection($rows, $keywords)
    {
        if (empty($rows)) {
            return 'N/A';
        }
        $columns = array_keys($rows[0]);

        foreach ($columns as $col) {
            foreach ($keywords as $keyword) {
                if (stripos($col, $keyword) !== false) {
                    return $this->calculateMaxForColumnWithNegativeCorrection($rows, $col);
                }
            }
        }

        return 'N/A';
    }

    /**
     * Find a column and calculate its minimum value with negative value correction
     */
    private function findAndCalculateMinWithNegativeCorrection($rows, $keywords)
    {
        if (empty($rows)) {
            return 'N/A';
        }
        $columns = array_keys($rows[0]);

        foreach ($columns as $col) {
            foreach ($keywords as $keyword) {
                if (stripos($col, $keyword) !== false) {
                    return $this->calculateMinForColumnWithNegativeCorrection($rows, $col);
                }
            }
        }

        return 'N/A';
    }

    /**
     * Calculate average for a specific column with negative value correction
     */
    private function calculateAverageForColumnWithNegativeCorrection($rows, $column)
    {
        $values = [];
        foreach ($rows as $row) {
            // Skip rows where the column doesn't exist
            if (!isset($row[$column])) {
                continue;
            }

            $parsedValue = $this->parseNumericWithNegativeCorrection($row[$column]);
            // Only include valid numeric values
            if ($this->isValidNumber($parsedValue) && ($parsedValue != 0 || ($parsedValue == 0 && is_numeric($row[$column])))) {
                $values[] = $parsedValue;
            }
        }
        return count($values) > 0 ? array_sum($values) / count($values) : 0;
    }

    /**
     * Calculate maximum for a specific column with negative value correction
     */
    private function calculateMaxForColumnWithNegativeCorrection($rows, $column)
    {
        $values = [];
        foreach ($rows as $row) {
            // Skip rows where the column doesn't exist
            if (!isset($row[$column])) {
                continue;
            }

            $parsedValue = $this->parseNumericWithNegativeCorrection($row[$column]);
            // Only include valid numeric values
            if ($this->isValidNumber($parsedValue) && ($parsedValue != 0 || ($parsedValue == 0 && is_numeric($row[$column])))) {
                $values[] = $parsedValue;
            }
        }
        return count($values) > 0 ? max($values) : 0;
    }

    /**
     * Calculate minimum for a specific column with negative value correction
     */
    private function calculateMinForColumnWithNegativeCorrection($rows, $column)
    {
        $values = [];
        foreach ($rows as $row) {
            // Skip rows where the column doesn't exist
            if (!isset($row[$column])) {
                continue;
            }

            $parsedValue = $this->parseNumericWithNegativeCorrection($row[$column]);
            // Only include valid numeric values
            if ($this->isValidNumber($parsedValue) && ($parsedValue != 0 || ($parsedValue == 0 && is_numeric($row[$column])))) {
                $values[] = $parsedValue;
            }
        }
        return count($values) > 0 ? min($values) : 0;
    }

    /**
     * Calculate sum for volume columns with negative value correction
     */
    private function calculateSumForVolumeColumnsWithNegativeCorrection($rows, $patterns)
    {
        $sum = 0;
        $columns = array_keys($rows[0] ?? []);

        foreach ($patterns as $pattern) {
            if (strpos($pattern, '*') !== false) {
                $prefix = str_replace('*', '', $pattern);
                // Find all columns that match the prefix
                foreach ($columns as $col) {
                    if (stripos($col, $prefix) !== false) {
                        foreach ($rows as $row) {
                            $sum += $this->parseNumericWithNegativeCorrection($row[$col] ?? 0);
                        }
                    }
                }
            } else {
                $actualCol = $this->findColumnByKeywords($columns, [$pattern]);
                if ($actualCol) {
                    foreach ($rows as $row) {
                        $sum += $this->parseNumericWithNegativeCorrection($row[$actualCol] ?? 0);
                    }
                }
            }
        }

        return $sum;
    }

    /**
     * Check for voltage and instrumentation readings outside safe ranges
     */
    private function checkInstrumentationSafety($rows)
    {
        $safetyIssues = [];

        // Check voltage columns (typically V or Voltage in column name)
        $voltageCols = ['V', 'Voltage', 'Volts'];
        foreach ($voltageCols as $pattern) {
            $voltageCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$pattern]);
            if ($voltageCol) {
                // Check if any voltage readings are outside normal range (e.g., 110-240V)
                foreach ($rows as $row) {
                    $voltage = $this->parseNumeric($row[$voltageCol] ?? 0);
                    if ($voltage > 0 && ($voltage < 100 || $voltage > 250)) {
                        $safetyIssues['voltage_issues'][] = [
                            'column' => $voltageCol,
                            'value' => $voltage,
                            'row_index' => array_search($row, $rows)
                        ];
                    }
                }
            }
        }

        // Check pressure columns for unsafe readings
        $pressureCols = ['Dyn_DP[mbar]', 'PL[bara]', 'WHP[psi]'];
        foreach ($pressureCols as $pattern) {
            $pressureCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$pattern]);
            if ($pressureCol) {
                // Check if any pressure readings are outside safe range
                foreach ($rows as $row) {
                    $pressure = $this->parseNumeric($row[$pressureCol] ?? 0);
                    if ($pressure > 0 && $pressure < 50) { // Example threshold
                        $safetyIssues['pressure_issues'][] = [
                            'column' => $pressureCol,
                            'value' => $pressure,
                            'row_index' => array_search($row, $rows)
                        ];
                    }
                }
            }
        }

        // Check temperature columns for unsafe readings
        $tempCols = ['TL[DegC]', 'DAFC_Temp', 'Temp[F]'];
        foreach ($tempCols as $pattern) {
            $tempCol = $this->findColumnByKeywords(array_keys($rows[0] ?? []), [$pattern]);
            if ($tempCol) {
                // Check if any temperature readings are outside safe range
                foreach ($rows as $row) {
                    $temperature = $this->parseNumeric($row[$tempCol] ?? 0);
                    if ($temperature > 200) { // Example threshold
                        $safetyIssues['temperature_issues'][] = [
                            'column' => $tempCol,
                            'value' => $temperature,
                            'row_index' => array_search($row, $rows)
                        ];
                    }
                }
            }
        }

        return $safetyIssues;
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
}