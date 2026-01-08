<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class MPFMPreviewService
{
    protected $parsingService;
    protected $calculationService;
    protected $chartService;

    public function __construct(
        MPFMParsingService $parsingService,
        MPFMCalculationService $calculationService,
        MPFMChartService $chartService
    ) {
        $this->parsingService = $parsingService;
        $this->calculationService = $calculationService;
        $this->chartService = $chartService;
    }

    /**
     * Process uploaded file and prepare preview data
     */
    public function preparePreviewData($file, $requestData)
    {
        // Read file content
        $content = file_get_contents($file->getRealPath());

        // Handle different encodings
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        // Parse into lines
        $lines = explode("\n", $content);

        // Parse log file
        $parsedData = $this->parsingService->parseDynamicLog($lines);

        // Validate parsed data
        $validationErrors = $this->parsingService->validateParsedData($parsedData);

        if (empty($parsedData['test_data']['rows'])) {
            return [
                'success' => false,
                'error' => 'The log file could not be parsed or contains no test data.',
                'validation_errors' => $validationErrors
            ];
        }

        // Extract data
        $rows = $parsedData['test_data']['rows'];
        $columns = $parsedData['test_data']['columns'];
        $metadata = $parsedData['metadata'];

        // Calculate all metrics
        $calculatedMetrics = $this->calculationService->calculateStandardizedMetrics($rows);

        // Find time column
        $timeColumn = $this->findTimeColumn($columns);

        // Prepare chart data
        $ratesChartData = $this->chartService->prepareRatesChartData($columns, $rows, $timeColumn);
        $gorChartData = $this->chartService->prepareGorChartData($columns, $rows, $timeColumn);

        // Calculate statistics for all numeric columns
        $columnStatistics = $this->calculateColumnStatistics($rows, $columns);

        // Get numeric columns for user selection
        $numericColumns = $this->parsingService->getNumericColumns($parsedData);

        // Prepare full average row
        $fullAverageRow = $this->chartService->prepareFullAverageRow($parsedData);

        // Extract start and end times
        $startTime = $this->extractDateTime($rows[0][$timeColumn] ?? null);
        $endTime = $this->extractDateTime(end($rows)[$timeColumn] ?? null);

        // Calculate test duration
        $duration = null;
        if ($startTime && $endTime) {
            $duration = $startTime->diffInMinutes($endTime);
        }

        // Prepare preview data
        $previewData = [
            'success' => true,
            'file_info' => [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getClientMimeType(),
                'parsed_at' => now()->format('Y-m-d H:i:s'),
            ],
            'metadata' => $metadata,
            'summary' => [
                'client' => $requestData['client'] ?? 'N/A',
                'field' => $requestData['field'] ?? 'N/A',
                'well' => $requestData['well'] ?? 'N/A',
                'operator' => $requestData['operator'] ?? 'N/A',
                'test_date' => $requestData['test_date'] ?? null,
                'test_type' => $requestData['test_type'] ?? 'N/A',
                'wellhead_tower' => $requestData['wellhead_tower'] ?? '',
                'wellhead_string' => $requestData['wellhead_string'] ?? '',
                'choke_size' => $requestData['choke_size'] ?? 'N/A',
                'upstream_choke' => $requestData['upstream_choke'] ?? 'N/A',
                'downstream_choke' => $requestData['downstream_choke'] ?? 'N/A',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $duration,
                'total_rows' => count($rows),
                'total_columns' => count($columns),
            ],
            'calculated_metrics' => $calculatedMetrics,
            'column_statistics' => $columnStatistics,
            'parsed_data' => $parsedData,
            'rates_chart_data' => $ratesChartData,
            'gor_chart_data' => $gorChartData,
            'full_average_row' => $fullAverageRow,
            'numeric_columns' => $numericColumns,
            'validation_errors' => $validationErrors,
            'raw_log_content' => $content,
        ];

        return $previewData;
    }

    /**
     * Store preview data in session for later use
     */
    public function storePreviewInSession($previewData, $filePath)
    {
        $sessionData = [
            'preview_data' => $previewData,
            'file_path' => $filePath,
            'created_at' => now()->timestamp,
        ];

        Session::put('mpfm_preview', $sessionData);

        return true;
    }

    /**
     * Retrieve preview data from session
     */
    public function getPreviewFromSession()
    {
        $sessionData = Session::get('mpfm_preview');

        if (!$sessionData) {
            return null;
        }

        // Check if preview is expired (older than 1 hour)
        $createdAt = $sessionData['created_at'] ?? 0;
        if (time() - $createdAt > 3600) {
            $this->clearPreviewSession();
            return null;
        }

        return $sessionData;
    }

    /**
     * Clear preview session data
     */
    public function clearPreviewSession()
    {
        Session::forget('mpfm_preview');
        return true;
    }

    /**
     * Calculate statistics for all numeric columns
     */
    private function calculateColumnStatistics($rows, $columns)
    {
        $statistics = [];

        foreach ($columns as $column) {
            // Check if column is likely numeric
            $sampleValue = $rows[0][$column] ?? '';
            $parsedSample = $this->parsingService->parseNumeric($sampleValue);

            if (!is_numeric($parsedSample) && $parsedSample == 0 && $sampleValue !== '0') {
                continue; // Skip non-numeric columns
            }

            $values = [];
            foreach ($rows as $row) {
                $value = $this->parsingService->parseNumeric($row[$column] ?? 0);
                if ($this->parsingService->isValidNumber($value)) {
                    $values[] = $value;
                }
            }

            if (!empty($values)) {
                $statistics[$column] = [
                    'min' => min($values),
                    'max' => max($values),
                    'avg' => array_sum($values) / count($values),
                    'sum' => array_sum($values),
                    'count' => count($values),
                    'std_dev' => $this->calculateStandardDeviation($values),
                ];
            }
        }

        return $statistics;
    }

    /**
     * Calculate standard deviation
     */
    private function calculateStandardDeviation($values)
    {
        if (count($values) < 2) {
            return 0;
        }

        $mean = array_sum($values) / count($values);
        $variance = 0;

        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }

        $variance = $variance / count($values);
        return sqrt($variance);
    }

    /**
     * Find time/date column
     */
    private function findTimeColumn($columns)
    {
        foreach ($columns as $column) {
            if (stripos($column, 'time') !== false || stripos($column, 'date') !== false) {
                return $column;
            }
        }

        return $columns[0] ?? 'Date & Time'; // Default to first column
    }

    /**
     * Extract datetime from string
     */
    private function extractDateTime($dateTimeStr)
    {
        if (!$dateTimeStr || trim($dateTimeStr) === '') {
            return null;
        }

        try {
            // Try different datetime formats
            $formats = [
                'Y-m-d H:i:s',
                'd-M-Y H:i:s',
                'd/m/Y H:i:s',
                'm/d/Y H:i:s',
                'Y-m-d H:i',
                'd/m/Y H:i',
                'm/d/Y H:i',
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, trim($dateTimeStr));
                if ($date) {
                    return \Carbon\Carbon::instance($date);
                }
            }

            // Fallback to default parser
            return \Carbon\Carbon::parse(trim($dateTimeStr));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate quality report for preview
     */
    public function generateQualityReport($parsedData, $calculatedMetrics)
    {
        $report = [
            'overall_quality' => 'good',
            'issues' => [],
            'warnings' => [],
            'recommendations' => [],
        ];

        $rows = $parsedData['test_data']['rows'] ?? [];

        // Check for data consistency
        if (count($rows) < 10) {
            $report['warnings'][] = 'Very few data points collected (less than 10 rows)';
        }

        // Check for negative water values
        $hasNegativeWater = false;
        foreach ($rows as $row) {
            foreach ($row as $column => $value) {
                if ($this->parsingService->isWaterRelatedColumn($column)) {
                    $numValue = $this->parsingService->parseNumeric($value);
                    if ($numValue < 0) {
                        $hasNegativeWater = true;
                        break 2;
                    }
                }
            }
        }

        if ($hasNegativeWater) {
            $report['warnings'][] = 'Negative water-related values detected (automatically corrected to 0)';
        }

        // Check for safety issues
        $safetyIssues = $calculatedMetrics['safety_issues'] ?? [];
        if (!empty($safetyIssues)) {
            $report['issues'][] = 'Safety issues detected in instrumentation readings';
            $report['overall_quality'] = 'needs_review';
        }

        // Check for unusual GOR values
        $gor = $calculatedMetrics['gor'] ?? 0;
        if (is_numeric($gor) && ($gor < 0 || $gor > 50000)) {
            $report['warnings'][] = 'Unusual GOR value detected: ' . number_format($gor, 2);
        }

        // Check for unusual BSW values
        $bsw = $calculatedMetrics['bsw'] ?? 0;
        if (is_numeric($bsw) && ($bsw < 0 || $bsw > 100)) {
            $report['warnings'][] = 'Unusual BSW value detected: ' . number_format($bsw, 2) . '%';
        }

        // Recommendations
        if (count($rows) < 30) {
            $report['recommendations'][] = 'Consider longer test duration for more reliable data';
        }

        return $report;
    }
}
