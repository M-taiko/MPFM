<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Service to parse plan.txt file (Excel/TSV format) into database structure
 * Supports the MPFM Flow Testing monthly plan format
 */
class PlanTxtParserService
{
    /**
     * Parse plan.txt file and return structured data
     *
     * @param string $filePath Path to plan.txt file
     * @return array Structured plan data
     */
    public function parsePlanFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Plan file not found: {$filePath}");
        }

        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);

        // Parse header information (first 10 lines)
        $header = $this->parseHeader(array_slice($lines, 0, 10));

        // Parse task rows (starting from line 11)
        $tasks = $this->parseTasks(array_slice($lines, 10));

        return [
            'header' => $header,
            'tasks' => $tasks,
            'summary' => $this->calculateSummary($tasks)
        ];
    }

    /**
     * Parse header section with plan metadata
     */
    private function parseHeader(array $headerLines)
    {
        $header = [];

        foreach ($headerLines as $line) {
            // Extract key metrics from header
            if (str_contains($line, 'TOTAL TESTS')) {
                $parts = explode("\t", $line);
                $header['total_tests'] = (int) trim($parts[1] ?? 0);
            }

            if (str_contains($line, 'Monthly Target per KPI')) {
                $parts = explode("\t", $line);
                $header['monthly_target_kpi'] = (int) trim($parts[1] ?? 0);
            }

            if (str_contains($line, 'LAST UPDATE')) {
                $parts = explode("\t", $line);
                $header['last_update'] = trim($parts[3] ?? '');
            }

            if (str_contains($line, 'Total Approved Tests')) {
                $parts = explode("\t", $line);
                $header['total_approved'] = (int) trim($parts[1] ?? 0);
            }

            if (str_contains($line, 'Total Rejected Tests')) {
                $parts = explode("\t", $line);
                $header['total_rejected'] = (int) trim($parts[1] ?? 0);
            }

            if (str_contains($line, 'Total Pending Tests')) {
                $parts = explode("\t", $line);
                $header['total_pending'] = (int) trim($parts[1] ?? 0);
            }
        }

        return $header;
    }

    /**
     * Parse task rows from plan file
     */
    private function parseTasks(array $taskLines)
    {
        $tasks = [];
        $headerParsed = false;
        $columnIndexes = [];

        foreach ($taskLines as $lineNum => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $columns = explode("\t", $line);

            // First non-empty line is the column header
            if (!$headerParsed) {
                $columnIndexes = $this->identifyColumns($columns);
                $headerParsed = true;
                continue;
            }

            // Parse task row
            $task = $this->parseTaskRow($columns, $columnIndexes);

            if ($task && !empty($task['sno'])) {
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    /**
     * Identify column positions from header row
     */
    private function identifyColumns(array $columns)
    {
        $indexes = [];

        foreach ($columns as $index => $column) {
            $col = strtolower(trim($column));

            if (str_contains($col, 'sno')) $indexes['sno'] = $index;
            if (str_contains($col, 'well')) $indexes['well'] = $index;
            if (str_contains($col, 'tower')) $indexes['tower'] = $index;
            if (str_contains($col, 'flow route')) $indexes['flow_route'] = $index;
            if (str_contains($col, 'reservior') || str_contains($col, 'reservoir')) $indexes['reservoir_layer'] = $index;
            if (str_contains($col, 'locally/scada')) $indexes['test_location'] = $index;
            if (str_contains($col, 'mpfm type')) $indexes['mpfm_type'] = $index;
            if (str_contains($col, 'dhsv')) $indexes['dhsv'] = $index;
            if (str_contains($col, 'sub category')) $indexes['sub_category'] = $index;
            if (str_contains($col, 'category')) $indexes['category'] = $index;
            if (str_contains($col, 'req. whfp')) $indexes['required_whfp'] = $index;
            if (str_contains($col, 'date')) $indexes['test_date'] = $index;
            if (str_contains($col, 'done by')) $indexes['operator'] = $index;
            if (str_contains($col, 'completed')) $indexes['completed'] = $index;
            if (str_contains($col, 'final status')) $indexes['final_status'] = $index;
            if (str_contains($col, 'tested whfp')) $indexes['tested_whfp'] = $index;
            if (str_contains($col, 'remarks')) $indexes['remarks'] = $index;
        }

        return $indexes;
    }

    /**
     * Parse individual task row
     */
    private function parseTaskRow(array $columns, array $columnIndexes)
    {
        if (empty($columnIndexes)) {
            return null;
        }

        $task = [
            'sno' => $this->getColumnValue($columns, $columnIndexes, 'sno'),
            'well_name' => $this->getColumnValue($columns, $columnIndexes, 'well'),
            'tower' => $this->getColumnValue($columns, $columnIndexes, 'tower'),
            'flow_route' => $this->getColumnValue($columns, $columnIndexes, 'flow_route'),
            'reservoir_layer' => $this->getColumnValue($columns, $columnIndexes, 'reservoir_layer'),
            'test_location' => $this->getColumnValue($columns, $columnIndexes, 'test_location'),
            'mpfm_type' => $this->getColumnValue($columns, $columnIndexes, 'mpfm_type'),
            'dhsv' => $this->getColumnValue($columns, $columnIndexes, 'dhsv'),
            'sub_category' => $this->getColumnValue($columns, $columnIndexes, 'sub_category'),
            'category' => $this->getColumnValue($columns, $columnIndexes, 'category'),
            'required_whfp' => $this->parseNumeric($this->getColumnValue($columns, $columnIndexes, 'required_whfp')),
            'test_date' => $this->parseDate($this->getColumnValue($columns, $columnIndexes, 'test_date')),
            'operator' => $this->getColumnValue($columns, $columnIndexes, 'operator'),
            'completed' => $this->getColumnValue($columns, $columnIndexes, 'completed'),
            'final_status' => $this->getColumnValue($columns, $columnIndexes, 'final_status'),
            'tested_whfp' => $this->parseNumeric($this->getColumnValue($columns, $columnIndexes, 'tested_whfp')),
            'remarks' => $this->getColumnValue($columns, $columnIndexes, 'remarks'),
        ];

        // Determine priority
        $task['priority'] = $this->determinePriority($task);

        // Determine status
        $task['status'] = $this->determineStatus($task);

        return $task;
    }

    /**
     * Get column value by key
     */
    private function getColumnValue(array $columns, array $columnIndexes, string $key)
    {
        $index = $columnIndexes[$key] ?? null;
        if ($index === null || !isset($columns[$index])) {
            return null;
        }

        return trim($columns[$index]);
    }

    /**
     * Parse numeric value
     */
    private function parseNumeric($value)
    {
        if (empty($value) || !is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    /**
     * Parse date value
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Try parsing common date formats
            $formats = [
                'd-M-y',     // 7-Dec-25
                'd-M-Y',     // 7-Dec-2025
                'd/m/Y',     // 07/12/2025
                'Y-m-d',     // 2025-12-07
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $value);
                if ($date) {
                    return Carbon::instance($date)->format('Y-m-d');
                }
            }

            // Fallback to Carbon parser
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Determine task priority based on remarks and category
     */
    private function determinePriority(array $task)
    {
        $remarks = strtolower($task['remarks'] ?? '');

        if (str_contains($remarks, 'top priority')) {
            return 'high';
        }

        if (str_contains($remarks, 'urgent')) {
            return 'high';
        }

        if ($task['category'] === 'Active' && $task['sub_category'] === 'Healthy') {
            return 'medium';
        }

        return 'normal';
    }

    /**
     * Determine task status
     */
    private function determineStatus(array $task)
    {
        $completed = strtoupper($task['completed'] ?? '');

        if ($completed === 'YES') {
            return 'completed';
        }

        if ($completed === 'NO') {
            return 'rejected';
        }

        if (!empty($task['operator'])) {
            return 'in_progress';
        }

        return 'pending';
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary(array $tasks)
    {
        $summary = [
            'total_tasks' => count($tasks),
            'completed' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'rejected' => 0,
            'by_operator' => [],
            'by_tower' => [],
            'by_priority' => [
                'high' => 0,
                'medium' => 0,
                'normal' => 0
            ]
        ];

        foreach ($tasks as $task) {
            // Count by status
            $status = $task['status'];
            if (isset($summary[$status])) {
                $summary[$status]++;
            }

            // Count by operator
            $operator = $task['operator'] ?? 'Unassigned';
            if (!isset($summary['by_operator'][$operator])) {
                $summary['by_operator'][$operator] = 0;
            }
            $summary['by_operator'][$operator]++;

            // Count by tower
            $tower = $task['tower'] ?? 'Unknown';
            if (!isset($summary['by_tower'][$tower])) {
                $summary['by_tower'][$tower] = 0;
            }
            $summary['by_tower'][$tower]++;

            // Count by priority
            $priority = $task['priority'] ?? 'normal';
            if (isset($summary['by_priority'][$priority])) {
                $summary['by_priority'][$priority]++;
            }
        }

        return $summary;
    }
}
