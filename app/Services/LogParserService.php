<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * LogParserService handles dynamic parsing of various log file formats
 * including CSV, tab-delimited, and other text-based formats
 */
class LogParserService
{
    /**
     * Parse a log file and extract column headers and data rows
     *
     * @param UploadedFile $file The uploaded log file
     * @return array Array containing columns, rows, and metadata
     */
    public function parseLogFile(UploadedFile $file): array
    {
        $content = $this->readFileContent($file);

        // Detect file format and delimiter
        $delimiter = $this->detectDelimiter($content);

        // Split content into lines
        $lines = $this->splitContentIntoLines($content);

        if (empty($lines)) {
            throw new \Exception('File is empty or contains no valid data');
        }

        // Parse header row (first line)
        $headerLine = trim($lines[0]);
        $columns = $this->parseHeaderRow($headerLine, $delimiter);

        if (empty($columns)) {
            throw new \Exception('No valid column headers found in the file');
        }

        // Parse data rows
        $rows = [];
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) {
                continue; // Skip empty lines
            }

            $row = $this->parseDataRow($line, $columns, $delimiter);
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        // Validate that we have both headers and data
        if (empty($rows)) {
            throw new \Exception('File was parsed but contains no data rows');
        }

        return [
            'columns' => $columns,
            'rows' => $rows,
            'delimiter' => $delimiter,
            'total_rows' => count($rows),
            'total_columns' => count($columns),
            'metadata' => [
                'file_size' => $file->getSize(),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'parsed_at' => now()->toISOString(),
            ]
        ];
    }

    /**
     * Read file content with proper encoding detection
     */
    private function readFileContent(UploadedFile $file): string
    {
        $content = file_get_contents($file->getRealPath());

        if ($content === false) {
            throw new \Exception('Could not read file content');
        }

        // Detect encoding and convert to UTF-8 if needed
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);

        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        return $content;
    }

    /**
     * Detect the delimiter used in the file
     */
    private function detectDelimiter(string $content): string
    {
        $firstLine = explode("\n", $content)[0] ?? '';

        // Count occurrences of potential delimiters
        $delimiters = [
            ',' => substr_count($firstLine, ','),
            "\t" => substr_count($firstLine, "\t"),
            ';' => substr_count($firstLine, ';'),
            '|' => substr_count($firstLine, '|'),
        ];

        // Find the delimiter with the most occurrences
        $maxCount = 0;
        $detectedDelimiter = ',';

        foreach ($delimiters as $delimiter => $count) {
            if ($count > $maxCount) {
                $maxCount = $count;
                $detectedDelimiter = $delimiter;
            }
        }

        // If no delimiter found, default to comma
        return $maxCount > 0 ? $detectedDelimiter : ',';
    }

    /**
     * Split content into lines, handling different line ending styles
     */
    private function splitContentIntoLines(string $content): array
    {
        // Normalize line endings
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Split into lines
        $lines = explode("\n", $content);

        // Remove empty lines from the beginning and end
        $lines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });

        return array_values($lines); // Re-index array
    }

    /**
     * Parse the header row to extract column names
     */
    private function parseHeaderRow(string $headerLine, string $delimiter): array
    {
        $rawHeaders = str_getcsv($headerLine, $delimiter);

        // Clean and normalize headers
        $headers = [];
        foreach ($rawHeaders as $header) {
            $cleanHeader = trim($header);
            if (!empty($cleanHeader)) {
                $headers[] = $cleanHeader;
            }
        }

        return $headers;
    }

    /**
     * Parse a data row into an associative array using column names
     */
    private function parseDataRow(string $line, array $columns, string $delimiter): ?array
    {
        $values = str_getcsv($line, $delimiter);

        // If the number of values doesn't match the number of columns,
        // try to handle it gracefully
        if (count($values) !== count($columns)) {
            // Pad values array if too few values
            while (count($values) < count($columns)) {
                $values[] = null;
            }

            // Truncate values array if too many values
            $values = array_slice($values, 0, count($columns));
        }

        // Create associative array
        $row = [];
        foreach ($columns as $index => $column) {
            $value = $values[$index] ?? null;
            $row[$column] = $this->parseValue($value);
        }

        return $row;
    }

    /**
     * Parse individual values, handling special cases
     */
    private function parseValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Clean the value
        $value = trim($value);

        // Handle common "empty" representations
        $emptyValues = ['N/A', 'NA', 'NULL', 'null', 'undefined', 'NaN', '#N/A', ''];
        if (in_array(strtoupper($value), array_map('strtoupper', $emptyValues))) {
            return null;
        }

        // Try to convert to appropriate type
        if (is_numeric($value)) {
            // Return as integer if it's a whole number, otherwise as float
            return (float) $value == (int) $value ? (int) $value : (float) $value;
        }

        // Return as string if not numeric
        return $value;
    }

    /**
     * Categorize columns based on naming patterns
     */
    public function categorizeColumns(array $columns): array
    {
        $categories = [];

        // Define patterns for different types of columns
        $patterns = [
            'flow_rates' => [
                'oil_rate', 'water_rate', 'gas_rate', 'qo', 'qw', 'qg', 'oil', 'water', 'gas',
                'oil_rate_lc', 'water_rate_lc', 'gas_rate_lc', 'oil_rate_sc', 'gas_rate_sc',
                'qo_lc', 'qw_lc', 'qg_lc', 'qo_sc', 'qw_sc', 'qg_sc'
            ],
            'densities' => [
                'density', 'rho', 'do', 'dw', 'dg', 'density_oil', 'density_water', 'density_gas',
                'rho_oil', 'rho_water', 'rho_gas', 'do_lc', 'dw_lc', 'dg_lc'
            ],
            'pressure_temp' => [
                'pressure', 'temp', 'temperature', 'p', 't', 'whp', 'wht', 'bhfp', 'bhft',
                'pressure_1', 'pressure_2', 'temp_1', 'temp_2', 'upstream_pressure', 'downstream_pressure'
            ],
            'cumulative' => [
                'cum', 'total', 'cumulative', 'cum_oil', 'cum_water', 'cum_gas',
                'total_oil', 'total_water', 'total_gas', 'cvo', 'cvw', 'cvg'
            ],
            'ratios' => [
                'gor', 'glr', 'wlr', 'bsw', 'gas_oil_ratio', 'gas_liquid_ratio', 'water_cut',
                'gas_liquid_ratio', 'oil_cut', 'gas_fraction', 'liquid_fraction'
            ],
            'electrical' => [
                'voltage', 'current', 'power', 'input_volt', 'hart_volt', '5v', '12v', 'neg12v',
                'voltage_input', 'voltage_hart', 'voltage_5v', 'voltage_12v'
            ],
            'device_metrics' => [
                'temp', 'humidity', 'crystal_temp', 'chamber_temp', 'board_temp', 'dtec',
                'device_temp', 'dahc_temp', 'dahc_hum', 'dahc_pres', 'dafc_temp', 'dafc_hum'
            ],
            'safety_quality' => [
                'h2s', 'co2', 'h2s_ppm', 'co2_percent', 'sulphur', 'carbon_dioxide',
                'h2s_concentration', 'co2_concentration'
            ],
            'time' => [
                'time', 'date', 'datetime', 'timestamp', 'date_time', 'utc', 'local_time'
            ],
        ];

        foreach ($columns as $column) {
            $category = 'other';
            $columnLower = strtolower($column);

            foreach ($patterns as $patternName => $patternList) {
                foreach ($patternList as $pattern) {
                    if (strpos($columnLower, strtolower($pattern)) !== false) {
                        $category = $patternName;
                        break 2; // Break both loops
                    }
                }
            }

            $categories[$column] = $category;
        }

        return $categories;
    }

    /**
     * Validate the parsed data structure
     */
    public function validateParsedData(array $parsedData): array
    {
        $errors = [];

        if (!isset($parsedData['columns']) || !is_array($parsedData['columns']) || empty($parsedData['columns'])) {
            $errors[] = 'Missing or invalid columns in parsed data';
        }

        if (!isset($parsedData['rows']) || !is_array($parsedData['rows']) || empty($parsedData['rows'])) {
            $errors[] = 'Missing or invalid rows in parsed data';
        }

        if (isset($parsedData['rows']) && !empty($parsedData['rows'])) {
            // Check that each row has the correct number of columns
            $expectedCols = count($parsedData['columns']);
            foreach ($parsedData['rows'] as $index => $row) {
                if (!is_array($row)) {
                    $errors[] = "Row $index is not an array";
                    continue;
                }

                if (count($row) !== $expectedCols) {
                    $errors[] = "Row $index has " . count($row) . " columns, expected $expectedCols";
                }
            }
        }

        return $errors;
    }
}
