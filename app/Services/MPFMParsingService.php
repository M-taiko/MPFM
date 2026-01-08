<?php

namespace App\Services;

class MPFMParsingService
{
    /**
     * Enhanced function to parse dynamic log files with improved handling of variable columns
     * This parser can handle:
     * - Any delimiter (tab, comma, semicolon, pipe)
     * - Variable column names and order
     * - Missing or additional columns
     * - Metadata extraction from headers
     * - Different MPFM device formats (Vx, DAFC, etc.)
     */
    public function parseDynamicLog($lines)
    {
        $data = [
            'metadata' => [],
            'test_data' => ['columns' => [], 'rows' => []]
        ];

        // Extract metadata from file header
        $data['metadata'] = $this->extractMetadata($lines);

        // Find header line
        $headerInfo = $this->findHeaderLine($lines);

        if (!$headerInfo) {
            return $data;
        }

        $headerLine = $headerInfo['line'];
        $headerLineIndex = $headerInfo['index'];
        $delimiter = $headerInfo['delimiter'];

        // Parse columns from header
        $columns = explode($delimiter, $headerLine);
        $data['test_data']['columns'] = array_map('trim', $columns);

        // Detect column types (numeric vs text)
        $columnTypes = $this->detectColumnTypes($data['test_data']['columns']);
        $data['metadata']['column_types'] = $columnTypes;

        // Process data rows
        for ($i = $headerLineIndex + 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);

            // Skip empty lines
            if (empty($line)) {
                continue;
            }

            $values = explode($delimiter, $line);

            // Handle cases where row might have different number of columns
            if (count($values) > 0) {
                $row = [];

                // Map values to columns, padding with empty strings if needed
                for ($j = 0; $j < count($data['test_data']['columns']); $j++) {
                    $row[$data['test_data']['columns'][$j]] = $values[$j] ?? '';
                }

                $data['test_data']['rows'][] = $row;
            }
        }

        // Add parsing statistics
        $data['metadata']['total_rows'] = count($data['test_data']['rows']);
        $data['metadata']['total_columns'] = count($data['test_data']['columns']);
        $data['metadata']['delimiter'] = $delimiter;

        return $data;
    }

    /**
     * Extract metadata from file header (device info, versions, configuration)
     */
    private function extractMetadata($lines)
    {
        $metadata = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Stop at header line (contains Date & Time)
            if (stripos($line, 'Date & Time') !== false) {
                break;
            }

            // Skip empty lines
            if (empty($line)) {
                continue;
            }

            // Parse key-value pairs (separated by tab or colon)
            if (strpos($line, "\t") !== false) {
                $parts = explode("\t", $line, 2);
                if (count($parts) == 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    if (!empty($key) && !empty($value)) {
                        $metadata[$key] = $value;
                    }
                }
            } elseif (strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);
                if (count($parts) == 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    if (!empty($key) && !empty($value)) {
                        $metadata[$key] = $value;
                    }
                }
            }
        }

        return $metadata;
    }

    /**
     * Find header line that contains column names
     * Looks for common MPFM column indicators
     */
    private function findHeaderLine($lines)
    {
        $possibleIndicators = [
            'Date & Time',
            'qo_lc',
            'qo_sc',
            'Oil Rate',
            'Time',
            'DateTime'
        ];

        foreach ($lines as $index => $line) {
            $line = trim($line);

            // Check if line contains any of the indicators
            foreach ($possibleIndicators as $indicator) {
                if (stripos($line, $indicator) !== false) {
                    // Detect delimiter for this line
                    $delimiter = $this->detectDelimiter($line);

                    // Verify it's actually a header by checking if it has multiple columns
                    $columns = explode($delimiter, $line);
                    if (count($columns) > 3) { // At least 3 columns
                        return [
                            'line' => $line,
                            'index' => $index,
                            'delimiter' => $delimiter
                        ];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Auto-detect the delimiter used in the file
     * Checks for tab, comma, semicolon, pipe in order of likelihood
     */
    private function detectDelimiter($line)
    {
        $delimiters = [
            "\t" => 'tab',
            ',' => 'comma',
            ';' => 'semicolon',
            '|' => 'pipe'
        ];

        $maxCount = 0;
        $bestDelimiter = "\t"; // Default to tab

        foreach ($delimiters as $delimiter => $name) {
            $count = substr_count($line, $delimiter);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delimiter;
            }
        }

        return $bestDelimiter;
    }

    /**
     * Detect which columns are numeric vs text
     * This helps with calculations and chart generation
     */
    private function detectColumnTypes($columns)
    {
        $columnTypes = [];

        foreach ($columns as $column) {
            $columnLower = strtolower($column);

            // Indicators for numeric columns
            $numericIndicators = [
                'rate', 'qo', 'qw', 'qg', 'gor', 'glr', 'gvf', 'bsw', 'wlr',
                'pressure', 'temp', 'volume', 'density', 'mass', 'flow',
                '[bbl/d]', '[STB/d]', '[Mcf/d]', '[MMScf/d]', '[%]',
                '[psi]', '[bara]', '[mbar]', '[DegC]', '[F]', '[kg/d]',
                '[kg/m3]', '[cP]', '[m3/m3]', '[Scf/STB]', '[V]', '[mV]',
                'n32', 'n81', 'n356', 'ntotal', 'cps', 'volt', 'fo_', 'fw_',
                'fg_', 'mo_', 'mw_', 'mg_', 'do_', 'dw_', 'dg_', 'dm_',
                'cvo_', 'cvw_', 'cvg_', 'cmo_', 'cmw_', 'cmg_'
            ];

            // Indicators for text columns
            $textIndicators = ['date', 'time', 'name', 'mode', 'status', 'type'];

            $isNumeric = false;
            $isText = false;

            // Check numeric indicators
            foreach ($numericIndicators as $indicator) {
                if (stripos($columnLower, $indicator) !== false) {
                    $isNumeric = true;
                    break;
                }
            }

            // Check text indicators
            foreach ($textIndicators as $indicator) {
                if (stripos($columnLower, $indicator) !== false) {
                    $isText = true;
                    break;
                }
            }

            // Determine type
            if ($isText) {
                $columnTypes[$column] = 'text';
            } elseif ($isNumeric) {
                $columnTypes[$column] = 'numeric';
            } else {
                $columnTypes[$column] = 'unknown';
            }
        }

        return $columnTypes;
    }

    /**
     * Validate parsed data integrity
     */
    public function validateParsedData($parsedData)
    {
        $errors = [];

        // Check if we have columns
        if (empty($parsedData['test_data']['columns'])) {
            $errors[] = 'No columns found in the log file';
        }

        // Check if we have rows
        if (empty($parsedData['test_data']['rows'])) {
            $errors[] = 'No data rows found in the log file';
        }

        // Check for minimum required columns (at least a time column and some data)
        $hasTimeColumn = false;
        $columns = $parsedData['test_data']['columns'] ?? [];

        foreach ($columns as $column) {
            if (stripos($column, 'time') !== false || stripos($column, 'date') !== false) {
                $hasTimeColumn = true;
                break;
            }
        }

        if (!$hasTimeColumn && count($columns) > 0) {
            $errors[] = 'Warning: No time/date column detected';
        }

        // Check for completely empty rows
        $emptyRowCount = 0;
        foreach ($parsedData['test_data']['rows'] ?? [] as $row) {
            $nonEmptyValues = array_filter($row, function($value) {
                return !empty(trim($value));
            });

            if (empty($nonEmptyValues)) {
                $emptyRowCount++;
            }
        }

        if ($emptyRowCount > 0) {
            $errors[] = "Found {$emptyRowCount} completely empty rows";
        }

        return $errors;
    }

    /**
     * Get numeric columns from parsed data
     */
    public function getNumericColumns($parsedData)
    {
        $columnTypes = $parsedData['metadata']['column_types'] ?? [];
        $numericColumns = [];

        foreach ($columnTypes as $column => $type) {
            if ($type === 'numeric') {
                $numericColumns[] = $column;
            }
        }

        return $numericColumns;
    }

    /**
     * Enhanced numeric parsing function with better handling of special values
     */
    public function parseNumeric($value)
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
    public function parseNumericWithNegativeCorrection($value)
    {
        $parsedValue = $this->parseNumeric($value);
        // For water-related metrics, treat negative values as 0
        return $parsedValue < 0 ? 0 : $parsedValue;
    }

    /**
     * Check if a value is a valid numeric value (not N/A or empty)
     */
    public function isValidNumber($value)
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
    public function isWaterRelatedColumn($columnName)
    {
        $waterIndicators = ['water', 'wlr', 'bsw', 'h2s', 'qw_', 'fw_', 'cvw_', 'cmw_'];
        $lowerColumnName = strtolower($columnName);

        foreach ($waterIndicators as $indicator) {
            if (strpos($lowerColumnName, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if column is a rate column
     */
    public function isRateColumn($columnName)
    {
        $rateIndicators = ['rate', 'qo_', 'qw_', 'qg_', '[bbl/d]', '[STB/d]', '[Mcf/d]', '[MMScf/d]'];
        $lowerColumnName = strtolower($columnName);

        foreach ($rateIndicators as $indicator) {
            if (stripos($lowerColumnName, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if column is a pressure column
     */
    public function isPressureColumn($columnName)
    {
        $pressureIndicators = ['pressure', '[psi]', '[bara]', '[mbar]', 'dyn_dp', 'pl['];
        $lowerColumnName = strtolower($columnName);

        foreach ($pressureIndicators as $indicator) {
            if (stripos($lowerColumnName, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if column is a temperature column
     */
    public function isTemperatureColumn($columnName)
    {
        $tempIndicators = ['temp', '[degc]', '[f]', 'tl[', 'tamb'];
        $lowerColumnName = strtolower($columnName);

        foreach ($tempIndicators as $indicator) {
            if (stripos($lowerColumnName, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }
}
