<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * Comprehensive Well Test Log Processing Service
 *
 * Handles dynamic column detection, petroleum engineering calculations,
 * and flexible data processing for various well test log formats
 * (DAFC, MPFM, and similar systems).
 */
class WellTestLogProcessingService
{
    protected $parsingService;
    protected $calculationService;
    protected $chartService;

    // Column mapping patterns for different systems
    protected $columnPatterns = [
        'flow_rates' => [
            'qo_lc', 'qw_lc', 'qg_lc',           // Liquid rates (STB/d)
            'qo_sc', 'qw_sc', 'qg_sc',           // Stock tank rates
            'oil_rate', 'water_rate', 'gas_rate', // Generic rates
            'q_oil', 'q_water', 'q_gas',         // Alternative naming
        ],
        'densities' => [
            'Do_lc', 'Dw_lc', 'Dg_lc',           // Liquid densities
            'density_oil', 'density_water', 'density_gas',
            'rho_oil', 'rho_water', 'rho_gas',
        ],
        'pressure_temp' => [
            'PL', 'TL', 'TAMB', 'WHP', 'Temp',  // Pressure/Temperature
            'pressure', 'temperature', 'wh_pressure',
            'wellhead_pressure', 'ambient_temp',
        ],
        'cumulative' => [
            'cvo_lc', 'cvw_lc', 'cvg_lc',       // Cumulative volumes
            'cum_oil', 'cum_water', 'cum_gas',
            'total_oil', 'total_water', 'total_gas',
        ],
        'ratios' => [
            'GOR', 'GOR1', 'GLR',               // Gas ratios
            'gas_oil_ratio', 'gas_liquid_ratio',
        ],
        'electrical' => [
            'InputVolt', 'HartVolt', '5Volt', '12Volt', 'Neg12Volt',
            'voltage_input', 'voltage_hart', 'voltage_5v', 'voltage_12v',
        ],
        'device_metrics' => [
            'DAFCTemp', 'DAFCHum', 'CrystalTemp', 'ChamberTemp', 'BoardTemp',
            'device_temp', 'humidity', 'crystal_temp', 'chamber_temp',
        ],
        'safety_quality' => [
            'H2S', 'h2s_ppm', 'CO2', 'co2_percent',
            'sulphur_h2s', 'carbon_dioxide',
        ],
    ];

    // Thresholds for anomaly detection
    protected $thresholds = [
        'pressure' => ['min' => 0, 'max' => 10000, 'unit' => 'psi'],
        'temperature' => ['min' => -50, 'max' => 300, 'unit' => '°F'],
        'voltage' => ['min' => 4.5, 'max' => 15, 'unit' => 'V'],
        'flow_rate' => ['min' => 0, 'max' => 50000, 'unit' => 'STB/d'],
        'gor' => ['min' => 0, 'max' => 10000, 'unit' => 'scf/STB'],
        'bsw' => ['min' => 0, 'max' => 100, 'unit' => '%'],
    ];

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
     * Process well test log file with comprehensive analysis
     */
    public function processLogFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logfile' => 'required|file|mimes:txt,vxlog,log,csv|max:10240',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'error' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
            ];
        }

        try {
            $file = $request->file('logfile');
            if (!$file->isValid()) {
                throw new \Exception('Invalid file upload - file may be corrupted');
            }

            // Validate file size (10MB)
            if ($file->getSize() > 10 * 1024 * 1024) {
                throw new \Exception('File size exceeds 10MB limit');
            }

            // Store file temporarily
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_.]/', '_', $file->getClientOriginalName());
            $tempFilePath = $file->storeAs('temp', $fileName, 'public');

            // Read and parse file content
            $content = $this->readFileContent($file);

            if (empty(trim($content))) {
                throw new \Exception('Log file is empty or contains no readable content');
            }

            // Parse log data with comprehensive analysis
            $parsedData = $this->parseComprehensiveLogData($content);

            if (empty($parsedData['rows'])) {
                throw new \Exception('Log file was parsed but contains no measurement data rows');
            }

            // Perform comprehensive calculations
            $calculatedData = $this->performComprehensiveCalculations($parsedData);

            // Detect anomalies
            $anomalies = $this->detectAnomalies($parsedData['rows'], $parsedData['columns']);

            // Generate chart data
            $chartData = $this->generateComprehensiveChartData($parsedData, $calculatedData);

            // Prepare final result
            $result = [
                'success' => true,
                'temp_file_path' => $tempFilePath,
                'filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'raw_content' => $content,
                'parsed_data' => $parsedData,
                'calculated_data' => $calculatedData,
                'anomalies' => $anomalies,
                'chart_data' => $chartData,
                'metadata' => [
                    'total_rows' => count($parsedData['rows']),
                    'total_columns' => count($parsedData['columns']),
                    'file_size_kb' => strlen($content) / 1024,
                    'processing_timestamp' => now()->toISOString(),
                ],
            ];

            return $result;

        } catch (\Exception $e) {
            // Clean up temporary file
            if (isset($tempFilePath) && Storage::disk('public')->exists($tempFilePath)) {
                Storage::disk('public')->delete($tempFilePath);
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }

    /**
     * Parse comprehensive log data with dynamic column detection
     */
    private function parseComprehensiveLogData(string $content): array
    {
        $lines = explode("\n", $content);
        $lines = array_filter($lines, function($line) {
            return !empty(trim($line));
        });

        if (empty($lines)) {
            throw new \Exception('No valid data lines found in log file');
        }

        // Detect delimiter and parse header
        $headerLine = trim($lines[0]);
        $delimiter = $this->detectDelimiter($headerLine);

        // Parse header row
        $headers = str_getcsv($headerLine, $delimiter);
        $headers = array_map('trim', $headers);

        // Remove empty headers
        $headers = array_filter($headers, function($header) {
            return !empty($header);
        });

        $columns = array_values($headers);
        $rows = [];

        // Parse data rows
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;

            $values = str_getcsv($line, $delimiter);
            $values = array_map('trim', $values);

            if (count($values) >= count($columns)) {
                $row = [];
                foreach ($columns as $index => $column) {
                    $value = $values[$index] ?? null;
                    $row[$column] = $this->parseNumericValue($value);
                }
                $rows[] = $row;
            }
        }

        return [
            'columns' => $columns,
            'rows' => $rows,
            'column_categories' => $this->categorizeColumns($columns),
            'delimiter' => $delimiter,
        ];
    }

    /**
     * Detect CSV delimiter from header line
     */
    private function detectDelimiter(string $headerLine): string
    {
        $delimiters = [',', '\t', ';', '|'];

        foreach ($delimiters as $delimiter) {
            $parts = explode($delimiter, $headerLine);
            if (count($parts) > 1) {
                return $delimiter;
            }
        }

        return ','; // Default to comma
    }

    /**
     * Parse numeric value safely
     */
    private function parseNumericValue($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Handle special cases
        $value = str_replace(['N/A', 'NULL', '-'], ['', '', ''], strtoupper($value));
        if (is_numeric($value)) {
            return (float) $value;
        }

        return $value; // Keep as string for non-numeric values
    }

    /**
     * Categorize columns based on naming patterns
     */
    private function categorizeColumns(array $columns): array
    {
        $categories = [];

        foreach ($columns as $column) {
            $category = 'other';
            $columnLower = strtolower($column);

            foreach ($this->columnPatterns as $patternName => $patterns) {
                foreach ($patterns as $pattern) {
                    if (stripos($columnLower, strtolower($pattern)) !== false) {
                        $category = $patternName;
                        break 2;
                    }
                }
            }

            $categories[$column] = $category;
        }

        return $categories;
    }

    /**
     * Perform comprehensive petroleum engineering calculations
     */
    private function performComprehensiveCalculations(array $parsedData): array
    {
        $rows = $parsedData['rows'];
        $columns = $parsedData['columns'];

        $calculations = [
            'column_stats' => $this->calculateColumnStatistics($rows, $columns),
            'petroleum_metrics' => $this->calculatePetroleumMetrics($rows, $columns),
            'cumulative_totals' => $this->calculateCumulativeTotals($rows, $columns),
            'derived_metrics' => $this->calculateDerivedMetrics($rows, $columns),
        ];

        return $calculations;
    }

    /**
     * Calculate statistics for all numeric columns
     */
    private function calculateColumnStatistics(array $rows, array $columns): array
    {
        $stats = [];

        foreach ($columns as $column) {
            $numericValues = [];

            foreach ($rows as $row) {
                $value = $row[$column] ?? null;
                if (is_numeric($value)) {
                    $numericValues[] = (float) $value;
                }
            }

            if (!empty($numericValues)) {
                $stats[$column] = [
                    'count' => count($numericValues),
                    'average' => round(array_sum($numericValues) / count($numericValues), 4),
                    'min' => min($numericValues),
                    'max' => max($numericValues),
                    'sum' => array_sum($numericValues),
                    'std_dev' => $this->calculateStandardDeviation($numericValues),
                    'median' => $this->calculateMedian($numericValues),
                ];
            } else {
                $stats[$column] = [
                    'count' => 0,
                    'average' => null,
                    'min' => null,
                    'max' => null,
                    'sum' => null,
                    'std_dev' => null,
                    'median' => null,
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

        // Calculate GOR for each row
        $gorValues = [];
        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, 'oil');
            $gasRate = $this->findFlowRate($row, $columns, 'gas');

            if ($oilRate > 0 && $gasRate !== null) {
                $gor = $gasRate / $oilRate;
                $gorValues[] = $gor;
            }
        }

        if (!empty($gorValues)) {
            $metrics['gor'] = [
                'values' => $gorValues,
                'average' => round(array_sum($gorValues) / count($gorValues), 2),
                'min' => min($gorValues),
                'max' => max($gorValues),
            ];
        }

        // Calculate WLR (Water-Liquid Ratio)
        $wlrValues = [];
        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, 'oil');
            $waterRate = $this->findFlowRate($row, $columns, 'water');

            $totalLiquid = ($oilRate ?: 0) + ($waterRate ?: 0);
            if ($totalLiquid > 0 && $waterRate !== null) {
                $wlr = ($waterRate / $totalLiquid) * 100; // Percentage
                $wlrValues[] = $wlr;
            }
        }

        if (!empty($wlrValues)) {
            $metrics['wlr'] = [
                'values' => $wlrValues,
                'average' => round(array_sum($wlrValues) / count($wlrValues), 2),
                'min' => min($wlrValues),
                'max' => max($wlrValues),
            ];
        }

        // Calculate BSW (Basic Sediment & Water)
        $bswValues = [];
        foreach ($rows as $row) {
            $bsw = $row['BSW'] ?? $row['bsw'] ?? $row['water_cut'] ?? null;
            if (is_numeric($bsw)) {
                $bswValues[] = (float) $bsw;
            }
        }

        if (!empty($bswValues)) {
            $metrics['bsw'] = [
                'values' => $bswValues,
                'average' => round(array_sum($bswValues) / count($bswValues), 2),
                'min' => min($bswValues),
                'max' => max($bswValues),
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
        ];

        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, 'oil') ?: 0;
            $waterRate = $this->findFlowRate($row, $columns, 'water') ?: 0;
            $gasRate = $this->findFlowRate($row, $columns, 'gas') ?: 0;

            // Assuming rates are per hour, convert to daily totals
            $cumulatives['oil'] += $oilRate * 24;
            $cumulatives['water'] += $waterRate * 24;
            $cumulatives['gas'] += $gasRate * 24;
            $cumulatives['total_liquid'] += ($oilRate + $waterRate) * 24;
        }

        return $cumulatives;
    }

    /**
     * Calculate derived metrics
     */
    private function calculateDerivedMetrics(array $rows, array $columns): array
    {
        $metrics = [];

        // GVF (Gas Void Fraction)
        $gvfValues = [];
        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, 'oil') ?: 0;
            $waterRate = $this->findFlowRate($row, $columns, 'water') ?: 0;
            $gasRate = $this->findFlowRate($row, $columns, 'gas') ?: 0;

            $totalVolume = $oilRate + $waterRate + $gasRate;
            if ($totalVolume > 0) {
                $gvf = ($gasRate / $totalVolume) * 100;
                $gvfValues[] = $gvf;
            }
        }

        if (!empty($gvfValues)) {
            $metrics['gvf'] = [
                'values' => $gvfValues,
                'average' => round(array_sum($gvfValues) / count($gvfValues), 2),
                'unit' => '%',
            ];
        }

        // GLR (Gas-Liquid Ratio)
        $glrValues = [];
        foreach ($rows as $row) {
            $oilRate = $this->findFlowRate($row, $columns, 'oil') ?: 0;
            $waterRate = $this->findFlowRate($row, $columns, 'water') ?: 0;
            $gasRate = $this->findFlowRate($row, $columns, 'gas') ?: 0;

            $liquidRate = $oilRate + $waterRate;
            if ($liquidRate > 0 && $gasRate > 0) {
                $glr = $gasRate / $liquidRate;
                $glrValues[] = $glr;
            }
        }

        if (!empty($glrValues)) {
            $metrics['glr'] = [
                'values' => $glrValues,
                'average' => round(array_sum($glrValues) / count($glrValues), 2),
                'unit' => 'scf/STB',
            ];
        }

        return $metrics;
    }

    /**
     * Find flow rate by type from available columns
     */
    private function findFlowRate(array $row, array $columns, string $type): ?float
    {
        $patterns = $this->columnPatterns['flow_rates'];

        foreach ($patterns as $pattern) {
            if (stripos($pattern, 'q' . $type) !== false || stripos($pattern, $type . '_rate') !== false) {
                if (isset($row[$pattern]) && is_numeric($row[$pattern])) {
                    return (float) $row[$pattern];
                }
            }
        }

        // Direct column name matching
        $directNames = [$type . '_rate', 'q_' . $type, 'rate_' . $type];
        foreach ($directNames as $name) {
            if (isset($row[$name]) && is_numeric($row[$name])) {
                return (float) $row[$name];
            }
        }

        return null;
    }

    /**
     * Detect anomalies based on thresholds
     */
    private function detectAnomalies(array $rows, array $columns): array
    {
        $anomalies = [];

        foreach ($rows as $rowIndex => $row) {
            foreach ($columns as $column) {
                $value = $row[$column] ?? null;

                if (!is_numeric($value)) continue;

                $value = (float) $value;
                $threshold = $this->getThresholdForColumn($column);

                if ($threshold) {
                    if ($value < $threshold['min'] || $value > $threshold['max']) {
                        $anomalies[] = [
                            'row' => $rowIndex + 1,
                            'column' => $column,
                            'value' => $value,
                            'threshold' => $threshold,
                            'type' => $value < $threshold['min'] ? 'below_minimum' : 'above_maximum',
                        ];
                    }
                }
            }
        }

        return $anomalies;
    }

    /**
     * Get threshold for column based on its category
     */
    private function getThresholdForColumn(string $column): ?array
    {
        $columnLower = strtolower($column);

        foreach ($this->thresholds as $category => $threshold) {
            if (stripos($columnLower, $category) !== false) {
                return $threshold;
            }
        }

        // Check against known patterns
        foreach ($this->columnPatterns as $category => $patterns) {
            if (isset($this->thresholds[$category])) {
                foreach ($patterns as $pattern) {
                    if (stripos($columnLower, strtolower($pattern)) !== false) {
                        return $this->thresholds[$category];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Generate comprehensive chart data
     */
    private function generateComprehensiveChartData(array $parsedData, array $calculatedData): array
    {
        $rows = $parsedData['rows'];
        $columns = $parsedData['columns'];

        $charts = [
            'time_series' => $this->generateTimeSeriesCharts($rows, $columns),
            'distributions' => $this->generateDistributionCharts($calculatedData['column_stats']),
            'correlations' => $this->generateCorrelationCharts($rows, $columns),
            'petroleum_metrics' => $this->generatePetroleumMetricsCharts($calculatedData['petroleum_metrics']),
        ];

        return $charts;
    }

    /**
     * Generate time series charts for numeric columns
     */
    private function generateTimeSeriesCharts(array $rows, array $columns): array
    {
        $charts = [];

        // Identify numeric columns
        $numericColumns = [];
        foreach ($columns as $column) {
            $hasNumeric = false;
            foreach ($rows as $row) {
                if (isset($row[$column]) && is_numeric($row[$column])) {
                    $hasNumeric = true;
                    break;
                }
            }
            if ($hasNumeric) {
                $numericColumns[] = $column;
            }
        }

        // Create time series for each numeric column
        foreach ($numericColumns as $column) {
            $labels = [];
            $data = [];

            foreach ($rows as $index => $row) {
                $labels[] = 'Row ' . ($index + 1);
                $data[] = isset($row[$column]) && is_numeric($row[$column]) ? (float) $row[$column] : null;
            }

            $charts[$column] = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $column,
                        'data' => $data,
                        'borderColor' => $this->getRandomColor(),
                        'backgroundColor' => $this->getRandomColor(0.1),
                        'fill' => false,
                        'tension' => 0.1,
                    ]
                ]
            ];
        }

        return $charts;
    }

    /**
     * Generate distribution charts for column statistics
     */
    private function generateDistributionCharts(array $columnStats): array
    {
        $charts = [];

        foreach ($columnStats as $column => $stats) {
            if ($stats['count'] > 0) {
                $charts[$column] = [
                    'type' => 'bar',
                    'data' => [
                        'labels' => ['Min', 'Average', 'Max'],
                        'datasets' => [
                            [
                                'label' => $column,
                                'data' => [$stats['min'], $stats['average'], $stats['max']],
                                'backgroundColor' => [
                                    'rgba(255, 99, 132, 0.8)',
                                    'rgba(54, 162, 235, 0.8)',
                                    'rgba(255, 205, 86, 0.8)',
                                ],
                            ]
                        ]
                    ],
                    'options' => [
                        'scales' => [
                            'y' => [
                                'beginAtZero' => true
                            ]
                        ]
                    ]
                ];
            }
        }

        return $charts;
    }

    /**
     * Generate correlation charts between related parameters
     */
    private function generateCorrelationCharts(array $rows, array $columns): array
    {
        $charts = [];

        // Oil vs Gas correlation
        $oilGasData = $this->extractCorrelationData($rows, $columns, 'oil', 'gas');
        if (!empty($oilGasData)) {
            $charts['oil_vs_gas'] = [
                'type' => 'scatter',
                'data' => [
                    'datasets' => [
                        [
                            'label' => 'Oil vs Gas Rate',
                            'data' => $oilGasData,
                            'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                        ]
                    ]
                ],
                'options' => [
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Oil Rate (STB/d)']],
                        'y' => ['title' => ['display' => true, 'text' => 'Gas Rate (Mscf/d)']],
                    ]
                ]
            ];
        }

        return $charts;
    }

    /**
     * Generate charts for petroleum engineering metrics
     */
    private function generatePetroleumMetricsCharts(array $petroleumMetrics): array
    {
        $charts = [];

        if (isset($petroleumMetrics['gor'])) {
            $charts['gor_trend'] = [
                'labels' => array_map(fn($i) => 'Point ' . ($i + 1), array_keys($petroleumMetrics['gor']['values'])),
                'datasets' => [
                    [
                        'label' => 'GOR (scf/STB)',
                        'data' => $petroleumMetrics['gor']['values'],
                        'borderColor' => 'rgba(255, 159, 64, 1)',
                        'backgroundColor' => 'rgba(255, 159, 64, 0.1)',
                        'fill' => false,
                    ]
                ]
            ];
        }

        if (isset($petroleumMetrics['wlr'])) {
            $charts['wlr_trend'] = [
                'labels' => array_map(fn($i) => 'Point ' . ($i + 1), array_keys($petroleumMetrics['wlr']['values'])),
                'datasets' => [
                    [
                        'label' => 'WLR (%)',
                        'data' => $petroleumMetrics['wlr']['values'],
                        'borderColor' => 'rgba(153, 102, 255, 1)',
                        'backgroundColor' => 'rgba(153, 102, 255, 0.1)',
                        'fill' => false,
                    ]
                ]
            ];
        }

        return $charts;
    }

    /**
     * Extract correlation data points
     */
    private function extractCorrelationData(array $rows, array $columns, string $xType, string $yType): array
    {
        $data = [];

        foreach ($rows as $row) {
            $xValue = $this->findFlowRate($row, $columns, $xType);
            $yValue = $this->findFlowRate($row, $columns, $yType);

            if ($xValue !== null && $yValue !== null) {
                $data[] = ['x' => $xValue, 'y' => $yValue];
            }
        }

        return $data;
    }

    /**
     * Utility functions
     */
    private function calculateStandardDeviation(array $values): float
    {
        if (count($values) <= 1) return 0;

        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $values)) / (count($values) - 1);

        return sqrt($variance);
    }

    private function calculateMedian(array $values): float
    {
        sort($values);
        $count = count($values);
        $middle = floor($count / 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return $values[$middle];
    }

    private function getRandomColor($alpha = 1): string
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
     * Safely read file content with encoding detection
     */
    private function readFileContent($file): string
    {
        $content = file_get_contents($file->getRealPath());

        // Detect encoding and convert to UTF-8 if needed
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);

        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        return $content;
    }
}
