<?php

namespace App\Services;

/**
 * ChartService prepares data for dynamic chart generation
 * supporting various chart types for well test data visualization
 */
class ChartService
{
    /**
     * Prepare chart data for flow rates over time
     */
    public function prepareFlowRatesChartData(array $rows, array $columns): array
    {
        // Find time column (if exists)
        $timeColumn = $this->findTimeColumn($columns);

        // Find flow rate columns
        $oilColumn = $this->findColumnByPattern($columns, ['oil_rate', 'qo', 'oil', 'oil_rate_lc']);
        $waterColumn = $this->findColumnByPattern($columns, ['water_rate', 'qw', 'water', 'water_rate_lc']);
        $gasColumn = $this->findColumnByPattern($columns, ['gas_rate', 'qg', 'gas', 'gas_rate_sc']);

        $labels = [];
        $oilData = [];
        $waterData = [];
        $gasData = [];

        foreach ($rows as $row) {
            // Add time to labels
            $labels[] = $timeColumn ? ($row[$timeColumn] ?? 'Time ' . (count($labels) + 1)) : 'Point ' . (count($labels) + 1);

            // Add flow rate data
            $oilData[] = $oilColumn && isset($row[$oilColumn]) && is_numeric($row[$oilColumn]) ? (float) $row[$oilColumn] : null;
            $waterData[] = $waterColumn && isset($row[$waterColumn]) && is_numeric($row[$waterColumn]) ? (float) $row[$waterColumn] : null;
            $gasData[] = $gasColumn && isset($row[$gasColumn]) && is_numeric($row[$gasColumn]) ? (float) $row[$gasColumn] : null;
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $oilColumn ?: 'Oil Rate',
                        'data' => $oilData,
                        'borderColor' => 'rgb(255, 99, 132)',
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                        'fill' => false,
                    ],
                    [
                        'label' => $waterColumn ?: 'Water Rate',
                        'data' => $waterData,
                        'borderColor' => 'rgb(54, 162, 235)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        'fill' => false,
                    ],
                    [
                        'label' => $gasColumn ?: 'Gas Rate',
                        'data' => $gasData,
                        'borderColor' => 'rgb(255, 205, 86)',
                        'backgroundColor' => 'rgba(255, 205, 86, 0.2)',
                        'fill' => false,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Flow Rates Over Time',
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => false,
                        'title' => [
                            'display' => true,
                            'text' => 'Rate (STB/d or Mscf/d)',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare chart data for GOR over time
     */
    public function prepareGorChartData(array $rows, array $columns): array
    {
        $timeColumn = $this->findTimeColumn($columns);
        $oilColumn = $this->findColumnByPattern($columns, ['oil_rate', 'qo', 'oil', 'oil_rate_lc']);
        $gasColumn = $this->findColumnByPattern($columns, ['gas_rate', 'qg', 'gas', 'gas_rate_sc']);

        $labels = [];
        $gorData = [];

        foreach ($rows as $row) {
            $labels[] = $timeColumn ? ($row[$timeColumn] ?? 'Time ' . (count($labels) + 1)) : 'Point ' . (count($labels) + 1);

            $oilRate = $oilColumn && isset($row[$oilColumn]) && is_numeric($row[$oilColumn]) ? (float) $row[$oilColumn] : 0;
            $gasRate = $gasColumn && isset($row[$gasColumn]) && is_numeric($row[$gasColumn]) ? (float) $row[$gasColumn] : 0;

            $gor = $oilRate > 0 ? $gasRate / $oilRate : null;
            $gorData[] = $gor;
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'GOR (Gas/Oil Ratio)',
                        'data' => $gorData,
                        'borderColor' => 'rgb(75, 192, 192)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'fill' => false,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Gas Oil Ratio Over Time',
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => false,
                        'title' => [
                            'display' => true,
                            'text' => 'GOR (scf/STB)',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare chart data for pressure and temperature
     */
    public function preparePressureTempChartData(array $rows, array $columns): array
    {
        $timeColumn = $this->findTimeColumn($columns);
        $pressureColumn = $this->findColumnByPattern($columns, ['pressure', 'pres', 'p', 'whp', 'bhfp']);
        $tempColumn = $this->findColumnByPattern($columns, ['temperature', 'temp', 't', 'bht', 'bhft']);

        $labels = [];
        $pressureData = [];
        $tempData = [];

        foreach ($rows as $row) {
            $labels[] = $timeColumn ? ($row[$timeColumn] ?? 'Time ' . (count($labels) + 1)) : 'Point ' . (count($labels) + 1);

            $pressureData[] = $pressureColumn && isset($row[$pressureColumn]) && is_numeric($row[$pressureColumn]) ? (float) $row[$pressureColumn] : null;
            $tempData[] = $tempColumn && isset($row[$tempColumn]) && is_numeric($row[$tempColumn]) ? (float) $row[$tempColumn] : null;
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $pressureColumn ?: 'Pressure',
                        'data' => $pressureData,
                        'borderColor' => 'rgb(255, 99, 132)',
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                        'fill' => false,
                        'yAxisID' => 'y',
                    ],
                    [
                        'label' => $tempColumn ?: 'Temperature',
                        'data' => $tempData,
                        'borderColor' => 'rgb(54, 162, 235)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        'fill' => false,
                        'yAxisID' => 'y1',
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Pressure and Temperature Over Time',
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => false,
                        'position' => 'left',
                        'title' => [
                            'display' => true,
                            'text' => 'Pressure (psi)',
                        ],
                    ],
                    'y1' => [
                        'beginAtZero' => false,
                        'position' => 'right',
                        'title' => [
                            'display' => true,
                            'text' => 'Temperature (°F)',
                        ],
                        'grid' => [
                            'drawOnChartArea' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare chart data for all numeric columns
     */
    public function prepareAllNumericChartData(array $rows, array $columns): array
    {
        $numericColumns = $this->getNumericColumns($rows, $columns);
        $timeColumn = $this->findTimeColumn($columns);

        $charts = [];

        foreach ($numericColumns as $column) {
            if ($column === $timeColumn) {
                continue; // Skip time column as it's used for labels
            }

            $labels = [];
            $data = [];

            foreach ($rows as $row) {
                $labels[] = $timeColumn ? ($row[$timeColumn] ?? 'Time ' . (count($labels) + 1)) : 'Point ' . (count($labels) + 1);
                $data[] = isset($row[$column]) && is_numeric($row[$column]) ? (float) $row[$column] : null;
            }

            $charts[$column] = [
                'type' => 'line',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => $column,
                            'data' => $data,
                            'borderColor' => $this->getRandomColor(),
                            'backgroundColor' => $this->getRandomColor(0.2),
                            'fill' => false,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'plugins' => [
                        'title' => [
                            'display' => true,
                            'text' => $column,
                        ],
                    ],
                    'scales' => [
                        'y' => [
                            'beginAtZero' => false,
                        ],
                    ],
                ],
            ];
        }

        return $charts;
    }

    /**
     * Prepare scatter plot for correlation analysis
     */
    public function prepareCorrelationChartData(array $rows, array $columns, string $xColumn, string $yColumn): ?array
    {
        if (!in_array($xColumn, $columns) || !in_array($yColumn, $columns)) {
            return null;
        }

        $data = [];

        foreach ($rows as $row) {
            $xValue = isset($row[$xColumn]) && is_numeric($row[$xColumn]) ? (float) $row[$xColumn] : null;
            $yValue = isset($row[$yColumn]) && is_numeric($row[$yColumn]) ? (float) $row[$yColumn] : null;

            if ($xValue !== null && $yValue !== null) {
                $data[] = ['x' => $xValue, 'y' => $yValue];
            }
        }

        if (empty($data)) {
            return null;
        }

        return [
            'type' => 'scatter',
            'data' => [
                'datasets' => [
                    [
                        'label' => "{$xColumn} vs {$yColumn}",
                        'data' => $data,
                        'backgroundColor' => 'rgba(75, 192, 192, 0.4)',
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => "{$xColumn} vs {$yColumn} Correlation",
                    ],
                ],
                'scales' => [
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => $xColumn,
                        ],
                    ],
                    'y' => [
                        'title' => [
                            'display' => true,
                            'text' => $yColumn,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare histogram for a specific column
     */
    public function prepareHistogramChartData(array $rows, array $columns, string $column): ?array
    {
        if (!in_array($column, $columns)) {
            return null;
        }

        $values = [];
        foreach ($rows as $row) {
            if (isset($row[$column]) && is_numeric($row[$column])) {
                $values[] = (float) $row[$column];
            }
        }

        if (empty($values)) {
            return null;
        }

        // Create bins for histogram
        $min = min($values);
        $max = max($values);
        $binCount = min(20, max(5, count($values) > 10 ? 10 : count($values)));
        $binSize = ($max - $min) / $binCount;

        $bins = array_fill(0, $binCount, 0);
        $binLabels = [];

        for ($i = 0; $i < $binCount; $i++) {
            $binLabels[] = round($min + ($i * $binSize), 2) . ' - ' . round($min + (($i + 1) * $binSize), 2);
        }

        foreach ($values as $value) {
            $binIndex = min($binCount - 1, floor(($value - $min) / $binSize));
            $bins[$binIndex]++;
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $binLabels,
                'datasets' => [
                    [
                        'label' => 'Frequency',
                        'data' => $bins,
                        'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => "Histogram of {$column}",
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'title' => [
                            'display' => true,
                            'text' => 'Frequency',
                        ],
                    ],
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => $column,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare pie chart for categorical data
     */
    public function preparePieChartData(array $rows, array $columns, string $column): ?array
    {
        if (!in_array($column, $columns)) {
            return null;
        }

        $counts = [];
        foreach ($rows as $row) {
            $value = $row[$column] ?? 'Unknown';
            if (!isset($counts[$value])) {
                $counts[$value] = 0;
            }
            $counts[$value]++;
        }

        if (empty($counts)) {
            return null;
        }

        $labels = array_keys($counts);
        $data = array_values($counts);
        $colors = $this->generateColorPalette(count($labels));

        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $data,
                        'backgroundColor' => $colors,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => "Distribution of {$column}",
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare summary statistics chart
     */
    public function prepareSummaryStatisticsChart(array $basicStats): array
    {
        $labels = [];
        $averages = [];
        $mins = [];
        $maxes = [];

        foreach ($basicStats as $column => $stats) {
            if ($stats['average'] !== null) {
                $labels[] = $column;
                $averages[] = round($stats['average'], 2);
                $mins[] = $stats['min'];
                $maxes[] = $stats['max'];
            }
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Average',
                        'data' => $averages,
                        'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    ],
                    [
                        'label' => 'Min',
                        'data' => $mins,
                        'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    ],
                    [
                        'label' => 'Max',
                        'data' => $maxes,
                        'backgroundColor' => 'rgba(255, 205, 86, 0.6)',
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Summary Statistics',
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Find time-related column
     */
    private function findTimeColumn(array $columns): ?string
    {
        $timePatterns = ['time', 'date', 'timestamp', 'datetime', 'utc', 'local'];

        foreach ($columns as $column) {
            foreach ($timePatterns as $pattern) {
                if (stripos($column, $pattern) !== false) {
                    return $column;
                }
            }
        }

        return null;
    }

    /**
     * Find column by pattern
     */
    private function findColumnByPattern(array $columns, array $patterns): ?string
    {
        foreach ($patterns as $pattern) {
            foreach ($columns as $column) {
                if (stripos($column, $pattern) !== false) {
                    return $column;
                }
            }
        }

        return null;
    }

    /**
     * Get all numeric columns
     */
    private function getNumericColumns(array $rows, array $columns): array
    {
        $numericColumns = [];

        foreach ($columns as $column) {
            $isNumeric = false;
            foreach ($rows as $row) {
                if (isset($row[$column]) && is_numeric($row[$column])) {
                    $isNumeric = true;
                    break;
                }
            }
            if ($isNumeric) {
                $numericColumns[] = $column;
            }
        }

        return $numericColumns;
    }

    /**
     * Generate random color
     */
    private function getRandomColor(float $alpha = 1): string
    {
        $r = rand(0, 255);
        $g = rand(0, 255);
        $b = rand(0, 255);

        if ($alpha < 1) {
            return "rgba($r, $g, $b, $alpha)";
        }

        return "rgb($r, $g, $b)";
    }

    /**
     * Generate color palette
     */
    private function generateColorPalette(int $count): array
    {
        $colors = [];
        $hueStep = 360 / max(1, $count);

        for ($i = 0; $i < $count; $i++) {
            $hue = $i * $hueStep;
            $hsl = "hsl($hue, 70%, 60%)";
            $colors[] = $hsl;
        }

        return $colors;
    }
}
