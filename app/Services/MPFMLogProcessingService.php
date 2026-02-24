<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;
use App\Services\MPFMParsingService;
use App\Services\MPFMCalculationService;
use App\Services\MPFMChartService;
use App\Services\MPFMDetailedMetricsExtractorService;
use Carbon\Carbon;

/**
 * Robust service for processing MPFM log files with flexible column handling
 * Handles missing keys/columns gracefully and provides comprehensive error handling
 */
class MPFMLogProcessingService
{
    protected $parsingService;
    protected $calculationService;
    protected $chartService;
    protected $metricsExtractor;

    public function __construct(
        MPFMParsingService $parsingService,
        MPFMCalculationService $calculationService,
        MPFMChartService $chartService,
        MPFMDetailedMetricsExtractorService $metricsExtractor
    ) {
        $this->parsingService = $parsingService;
        $this->calculationService = $calculationService;
        $this->chartService = $chartService;
        $this->metricsExtractor = $metricsExtractor;
    }

    /**
     * Process uploaded log file with robust error handling and missing key support
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

        $tempFilePath = null;

        try {
            $file = $request->file('logfile');
            if (!$file->isValid()) {
                throw new \Exception('Invalid file upload - file may be corrupted');
            }

            // Validate file size
            if ($file->getSize() > 10 * 1024 * 1024) { // 10MB
                throw new \Exception('File size exceeds 10MB limit');
            }

            // Store file temporarily
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_.]/', '_', $file->getClientOriginalName());
            $tempFilePath = $file->storeAs('temp', $fileName, 'public');

            // Read and parse file content with encoding detection
            $content = $this->readFileContent($file);

            if (empty(trim($content))) {
                throw new \Exception('Log file is empty or contains no readable content');
            }

            $lines = explode("\n", $content);
            $lines = array_filter($lines, function($line) {
                return !empty(trim($line));
            });

            if (empty($lines)) {
                throw new \Exception('Log file contains no valid data lines');
            }

            // Parse log data with error handling
            $parsedData = $this->parsingService->parseDynamicLog($lines);

            if (empty($parsedData) || !isset($parsedData['test_data'])) {
                throw new \Exception('Failed to parse log file structure');
            }

            $rows = $parsedData['test_data']['rows'] ?? [];

            if (empty($rows)) {
                throw new \Exception('Log file was parsed but contains no measurement data rows');
            }

            // Validate that we have at least some basic data
            $firstRow = $rows[0] ?? [];
            if (empty($firstRow)) {
                throw new \Exception('Parsed data contains empty rows');
            }

            // Process the data with safe extraction
            $processedResult = $this->processParsedData($parsedData, $content);

            return [
                'success' => true,
                'temp_file_path' => $tempFilePath,
                'filename' => $file->getClientOriginalName(),
                'raw_log_content' => $content,
                'file_size' => $file->getSize(),
                'parsed_data' => $parsedData,
                'processed_data' => $processedResult,
            ];

        } catch (\Exception $e) {
            // Clean up temporary file
            if ($tempFilePath && Storage::disk('public')->exists($tempFilePath)) {
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

    /**
     * Process parsed data with safe extraction of MPFM parameters
     */
    private function processParsedData(array $parsedData, string $rawContent): array
    {
        $rows = $parsedData['test_data']['rows'] ?? [];
        $columns = $parsedData['test_data']['columns'] ?? [];

        if (empty($rows)) {
            return [];
        }

        // Safely calculate averages
        $averages = $this->safeCalculateAverages($rows);

        // Prepare chart data with error handling
        $chartData = $this->safePrepareChartData($columns, $rows);

        // Extract MPFM data safely
        $mpfmData = $this->safeExtractMPFMData($averages, $rows);

        // Calculate test metadata
        $metadata = $this->calculateTestMetadata($rows, $rawContent);

        // Extract detailed metrics from log file
        $detailedMetrics = $this->safeExtractDetailedMetrics($parsedData, $rawContent);

        return [
            'averages' => $averages,
            'mpfm_data' => $mpfmData,
            'chart_data' => $chartData,
            'metadata' => $metadata,
            'data_quality' => $this->assessDataQuality($rows, $columns),
            'detailed_metrics' => $detailedMetrics,
        ];
    }

    /**
     * Safely calculate averages with error handling
     */
    private function safeCalculateAverages(array $rows): array
    {
        try {
            if (empty($rows)) {
                return [];
            }

            return $this->calculationService->calculateStandardizedMetrics($rows);
        } catch (\Exception $e) {
            // Return empty averages if calculation fails
            return [
                'error' => 'Failed to calculate averages: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Safely prepare comprehensive chart data with error handling
     */
    private function safePrepareChartData(array $columns, array $rows): array
    {
        try {
            $timeColumn = $this->findTimeColumn($columns);

            // Prepare multiple chart datasets
            $ratesChartData = $this->chartService->prepareRatesChartData($columns, $rows, $timeColumn);
            $gorChartData = $this->chartService->prepareGorChartData($columns, $rows, $timeColumn);

            // Additional chart data for enhanced visualization
            $charts = [
                'rates_chart' => $ratesChartData,
                'gor_chart' => $gorChartData,
                'time_column' => $timeColumn,
            ];

            // Prepare water cut chart if data available
            if ($this->hasColumn($columns, ['bsw', 'water_cut', 'wc'])) {
                $charts['water_cut_chart'] = $this->prepareWaterCutChartData($columns, $rows, $timeColumn);
            }

            // Prepare temperature chart if data available
            if ($this->hasColumn($columns, ['temperature', 'temp', 't_wh', 'oil_temp', 'water_temp', 'gas_temp'])) {
                $charts['temperature_chart'] = $this->prepareTemperatureChartData($columns, $rows, $timeColumn);
            }

            // Prepare pressure chart if data available
            if ($this->hasColumn($columns, ['pressure', 'wh_pressure', 'upstream_pressure', 'downstream_pressure'])) {
                $charts['pressure_chart'] = $this->preparePressureChartData($columns, $rows, $timeColumn);
            }

            // Prepare production comparison chart
            $charts['production_comparison_chart'] = $this->prepareProductionComparisonChart($columns, $rows, $timeColumn);

            return $charts;
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to prepare chart data: ' . $e->getMessage(),
                'rates_chart' => null,
                'gor_chart' => null,
                'water_cut_chart' => null,
                'temperature_chart' => null,
                'pressure_chart' => null,
                'production_comparison_chart' => null,
            ];
        }
    }

    /**
     * Safely extract comprehensive MPFM data with enhanced parameter extraction
     */
    private function safeExtractMPFMData(array $averages, array $rows): array
    {
        $firstRow = $rows[0] ?? [];

        // Extract test timing safely
        $startTime = $this->parseDateTimeSafe($this->findTimeValue($firstRow), Carbon::today());
        $endTime = $this->parseDateTimeSafe($this->findTimeValue(end($rows)), Carbon::today());

        // Calculate durations
        $durationMinutes = $startTime && $endTime ? $startTime->diffInMinutes($endTime) : null;
        $durationHours = $durationMinutes ? round($durationMinutes / 60, 2) : null;

        // Build comprehensive MPFM data with multiple fallback strategies
        $mpfmData = [
            // Basic test information
            'test_date' => $startTime ? $startTime->format('Y-m-d') : date('Y-m-d'),
            'shift' => $this->determineShift($startTime),
            'duration_minutes' => $durationMinutes,
            'test_duration_hours' => $durationHours,
            'test_started_at' => $startTime,
            'test_completed_at' => $endTime,
            'test_total_data_points' => count($rows),
            'test_stable_duration_hours' => $this->calculateStableDuration($rows, $startTime),

            // Production rates - comprehensive extraction
            'oil_rate' => $this->safeGetValue($averages, ['oil_rate_avg', 'qo_sc_avg', 'qo_lc_avg']) ??
                         $this->extractNumericValue($firstRow, ['qo_sc[STB/d]', 'qo_lc[bbl/d]', 'oil_rate', 'oil_rate_sc', 'oil_rate_lc']),
            'gas_rate' => $this->safeGetValue($averages, ['gas_rate_avg', 'qg_sc_avg', 'qg_lc_avg']) ??
                         $this->extractNumericValue($firstRow, ['qg_sc[MMScf/d]', 'qg_lc[Mcf/d]', 'gas_rate', 'gas_rate_sc', 'gas_rate_lc']),
            'water_rate' => $this->safeGetValue($averages, ['water_rate_avg', 'qw_sc_avg', 'qw_lc_avg']) ??
                           $this->extractNumericValue($firstRow, ['qw_sc[STB/d]', 'qw_lc[bbl/d]', 'water_rate', 'water_rate_sc', 'water_rate_lc']),

            // Quality parameters - comprehensive
            'water_cut' => $this->safeGetValue($averages, ['bsw_avg', 'water_cut_avg']) ??
                          $this->extractNumericValue($firstRow, ['bsw', 'water_cut', 'wc', 'water_cut_percent']),
            'bsw' => $this->safeGetValue($averages, ['bsw_avg']) ??
                    $this->calculateBSW($averages),

            // Pressure measurements
            'pressure' => $this->safeGetValue($averages, ['pressure_avg']) ??
                         $this->extractNumericValue($firstRow, ['pressure', 'wh_pressure', 'wellhead_pressure', 'p_wh']),
            'upstream_pressure' => $this->safeGetValue($averages, ['upstream_pressure_avg']) ??
                                 $this->extractNumericValue($firstRow, ['upstream_pressure', 'p_up', 'pressure_up']),
            'downstream_pressure' => $this->safeGetValue($averages, ['downstream_pressure_avg']) ??
                                   $this->extractNumericValue($firstRow, ['downstream_pressure', 'p_down', 'pressure_down']),

            // Temperature measurements
            'temperature' => $this->safeGetValue($averages, ['temperature_avg']) ??
                           $this->extractNumericValue($firstRow, ['temperature', 'temp', 't_wh', 'wellhead_temp']),
            'oil_temperature' => $this->safeGetValue($averages, ['oil_temp_avg']) ??
                               $this->extractNumericValue($firstRow, ['oil_temp', 'temp_oil', 't_oil']),
            'water_temperature' => $this->safeGetValue($averages, ['water_temp_avg']) ??
                                 $this->extractNumericValue($firstRow, ['water_temp', 'temp_water', 't_water']),
            'gas_temperature' => $this->safeGetValue($averages, ['gas_temp_avg']) ??
                               $this->extractNumericValue($firstRow, ['gas_temp', 'temp_gas', 't_gas']),

            // Fluid properties
            'oil_density' => $this->safeGetValue($averages, ['oil_density_avg']) ??
                           $this->extractNumericValue($firstRow, ['oil_density', 'density_oil', 'rho_oil']),
            'water_density' => $this->safeGetValue($averages, ['water_density_avg']) ??
                             $this->extractNumericValue($firstRow, ['water_density', 'density_water', 'rho_water']),
            'gas_density' => $this->safeGetValue($averages, ['gas_density_avg']) ??
                           $this->extractNumericValue($firstRow, ['gas_density', 'density_gas', 'rho_gas']),

            // Viscosity measurements
            'oil_viscosity' => $this->safeGetValue($averages, ['oil_viscosity_avg']) ??
                             $this->extractNumericValue($firstRow, ['oil_viscosity', 'viscosity_oil', 'mu_oil']),
            'water_viscosity' => $this->safeGetValue($averages, ['water_viscosity_avg']) ??
                               $this->extractNumericValue($firstRow, ['water_viscosity', 'viscosity_water', 'mu_water']),

            // Calculated KPIs - comprehensive
            'gor' => $this->safeGetValue($averages, ['total_gor_avg', 'gor_avg']) ??
                    $this->calculateGOR($averages),
            'wlr' => $this->safeGetValue($averages, ['total_wlr_avg', 'wlr_avg']) ??
                    $this->calculateWLR($averages),

            // Total production calculations
            'total_liquid_rate' => $this->calculateTotalLiquidRate($averages),
            'total_production_rate' => $this->calculateTotalProductionRate($averages),

            // Additional device-specific fields
            'h2s_ppm' => $this->safeGetValue($averages, ['h2s_avg']) ??
                        $this->extractNumericValue($firstRow, ['h2s', 'h2s_ppm', 'sulphur_h2s']),
            'co2_percent' => $this->safeGetValue($averages, ['co2_avg']) ??
                           $this->extractNumericValue($firstRow, ['co2', 'co2_percent', 'carbon_dioxide']),

            // Flow assurance parameters
            'choke_size' => $this->extractNumericValue($firstRow, ['choke_size', 'choke', 'bean_size']),
            'upstream_choke' => $this->extractNumericValue($firstRow, ['upstream_choke', 'choke_up']),
            'downstream_choke' => $this->extractNumericValue($firstRow, ['downstream_choke', 'choke_down']),

            // Device-specific parameters (may not always be present)
            'differential_pressure' => $this->safeGetValue($averages, ['dp_avg', 'differential_pressure_avg']) ??
                                     $this->extractNumericValue($firstRow, ['dp', 'differential_pressure', 'delta_p']),
            'flow_velocity' => $this->safeGetValue($averages, ['velocity_avg']) ??
                             $this->extractNumericValue($firstRow, ['velocity', 'flow_velocity', 'v_flow']),

            // Line conditions (LC) density fields - stored in database as po_lc, pw_lc, pg_lc
            'po_lc' => $this->safeGetValue($averages, ['po_lc', 'oil_density_lc']) ??
                      $this->extractNumericValue($firstRow, ['po_lc', 'oil_density[kg/m3]', 'rho_oil_lc']),
            'pw_lc' => $this->safeGetValue($averages, ['pw_lc', 'water_density_lc']) ??
                      $this->extractNumericValue($firstRow, ['pw_lc', 'water_density[kg/m3]', 'rho_water_lc']),
            'pg_lc' => $this->safeGetValue($averages, ['pg_lc', 'gas_density_lc']) ??
                      $this->extractNumericValue($firstRow, ['pg_lc', 'gas_density[kg/m3]', 'rho_gas_lc']),

            // Mass flow rates (kg/d) - stored in database as mo_lc, mw_lc, mg_lc
            'mo_lc' => $this->safeGetValue($averages, ['mo_lc', 'oil_mass_flow']) ??
                      $this->extractNumericValue($firstRow, ['mo_lc', 'oil_mass_flow[kg/d]', 'mass_oil_lc']),
            'mw_lc' => $this->safeGetValue($averages, ['mw_lc', 'water_mass_flow']) ??
                      $this->extractNumericValue($firstRow, ['mw_lc', 'water_mass_flow[kg/d]', 'mass_water_lc']),
            'mg_lc' => $this->safeGetValue($averages, ['mg_lc', 'gas_mass_flow']) ??
                      $this->extractNumericValue($firstRow, ['mg_lc', 'gas_mass_flow[kg/d]', 'mass_gas_lc']),

            // Formation volume factors (FVF) - dimensionless
            'bo' => $this->safeGetValue($averages, ['bo', 'bo_avg', 'oil_fvf']) ??
                   $this->extractNumericValue($firstRow, ['bo[Sm3/m3]', 'bo', 'BO', 'formation_oil']),
            'bw' => $this->safeGetValue($averages, ['bw', 'bw_avg', 'water_fvf']) ??
                   $this->extractNumericValue($firstRow, ['bw[Sm3/m3]', 'bw', 'BW', 'formation_water']),
            'bg' => $this->safeGetValue($averages, ['bg', 'bg_avg', 'gas_fvf']) ??
                   $this->extractNumericValue($firstRow, ['bg[Sm3/m3]', 'bg', 'BG', 'formation_gas']),

            // PVT properties
            'z_factor' => $this->safeGetValue($averages, ['z_factor', 'z_factor_avg', 'compressibility']) ??
                         $this->extractNumericValue($firstRow, ['Z', 'Z-factor', 'z_factor', 'compressibility']),
            'rst' => $this->safeGetValue($averages, ['rst', 'rst_avg', 'solution_gor']) ??
                    $this->extractNumericValue($firstRow, ['Rst[Scf/STB]', 'Rst', 'RST', 'solution_gor']),
        ];

        return $mpfmData;
    }

    /**
     * Calculate test metadata and statistics
     */
    private function calculateTestMetadata(array $rows, string $rawContent): array
    {
        $metadata = [
            'total_rows' => count($rows),
            'file_size_kb' => strlen($rawContent) / 1024,
            'data_points' => count($rows),
        ];

        if (!empty($rows)) {
            $firstRow = $rows[0];
            $lastRow = end($rows);

            $startTime = $this->parseDateTimeSafe($this->findTimeValue($firstRow), Carbon::today());
            $endTime = $this->parseDateTimeSafe($this->findTimeValue($lastRow), Carbon::today());

            $metadata = array_merge($metadata, [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $startTime && $endTime ? $startTime->diffInMinutes($endTime) : null,
                'sampling_interval_seconds' => $this->calculateSamplingInterval($rows),
            ]);
        }

        return $metadata;
    }

    /**
     * Assess data quality and completeness
     */
    private function assessDataQuality(array $rows, array $columns): array
    {
        $quality = [
            'score' => 0,
            'issues' => [],
            'completeness' => 0,
        ];

        if (empty($rows)) {
            $quality['issues'][] = 'No data rows found';
            return $quality;
        }

        $totalCells = count($rows) * count($columns);
        $filledCells = 0;

        foreach ($rows as $row) {
            foreach ($columns as $column) {
                if (isset($row[$column]) && $row[$column] !== '' && $row[$column] !== null) {
                    $filledCells++;
                }
            }
        }

        $quality['completeness'] = $totalCells > 0 ? ($filledCells / $totalCells) * 100 : 0;

        // Check for critical missing data
        $criticalColumns = ['Date & Time', 'oil_rate', 'gas_rate'];
        $missingCritical = [];

        foreach ($criticalColumns as $col) {
            if (!in_array($col, $columns)) {
                $missingCritical[] = $col;
            }
        }

        if (!empty($missingCritical)) {
            $quality['issues'][] = 'Missing critical columns: ' . implode(', ', $missingCritical);
        }

        // Calculate quality score (0-100)
        $score = $quality['completeness'];

        // Penalty for missing critical data
        if (!empty($missingCritical)) {
            $score -= 30;
        }

        // Bonus for good data density
        if ($quality['completeness'] > 90) {
            $score += 10;
        }

        $quality['score'] = max(0, min(100, $score));

        return $quality;
    }

    /**
     * Helper: Safely get value from averages array
     */
    private function safeGetValue(array $averages, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($averages[$key]) && $averages[$key] !== null && $averages[$key] !== '') {
                return $averages[$key];
            }
        }
        return null;
    }

    /**
     * Helper: Extract numeric value from row data
     */
    private function extractNumericValue(array $row, array $possibleKeys): ?float
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && is_numeric($row[$key])) {
                $value = (float) $row[$key];
                return $value > 0 ? $value : null; // Filter out negative/invalid values
            }
        }
        return null;
    }

    /**
     * Helper: Find time column in data
     */
    private function findTimeColumn(array $columns): string
    {
        $timeKeywords = ['Date & Time', 'datetime', 'time', 'timestamp', 'DateTime'];
        foreach ($timeKeywords as $keyword) {
            if (in_array($keyword, $columns)) {
                return $keyword;
            }
        }
        return $columns[0] ?? 'Date & Time'; // Fallback
    }

    /**
     * Helper: Find time value in row
     */
    private function findTimeValue(array $row): ?string
    {
        $timeKeys = ['Date & Time', 'datetime', 'time', 'timestamp', 'DateTime'];
        foreach ($timeKeys as $key) {
            if (isset($row[$key])) {
                return $row[$key];
            }
        }
        return null;
    }

    /**
     * Calculate GOR from available data
     */
    private function calculateGOR(array $averages): ?float
    {
        $oilRate = $this->safeGetValue($averages, ['oil_rate_avg', 'qo_sc_avg', 'qo_lc_avg']);
        $gasRate = $this->safeGetValue($averages, ['gas_rate_avg', 'qg_sc_avg', 'qg_lc_avg']);

        if ($oilRate > 0 && $gasRate !== null) {
            return $gasRate / $oilRate;
        }

        return null;
    }

    /**
     * Calculate WLR from available data
     */
    private function calculateWLR(array $averages): ?float
    {
        $oilRate = $this->safeGetValue($averages, ['oil_rate_avg', 'qo_sc_avg', 'qo_lc_avg']);
        $waterRate = $this->safeGetValue($averages, ['water_rate_avg', 'qw_sc_avg', 'qw_lc_avg']);

        $totalLiquid = $oilRate + $waterRate;
        if ($totalLiquid > 0 && $waterRate !== null) {
            return $waterRate / $totalLiquid;
        }

        return null;
    }

    /**
     * Calculate BSW from available data
     */
    private function calculateBSW(array $averages): ?float
    {
        return $this->safeGetValue($averages, ['bsw_avg', 'water_cut_avg']);
    }

    /**
     * Calculate total liquid rate (oil + water)
     */
    private function calculateTotalLiquidRate(array $averages): ?float
    {
        $oilRate = $this->safeGetValue($averages, ['oil_rate_avg', 'qo_sc_avg', 'qo_lc_avg']);
        $waterRate = $this->safeGetValue($averages, ['water_rate_avg', 'qw_sc_avg', 'qw_lc_avg']);

        if ($oilRate !== null && $waterRate !== null) {
            return $oilRate + $waterRate;
        }

        return null;
    }

    /**
     * Calculate total production rate (oil + water + gas equivalent)
     */
    private function calculateTotalProductionRate(array $averages): ?float
    {
        $oilRate = $this->safeGetValue($averages, ['oil_rate_avg', 'qo_sc_avg', 'qo_lc_avg']);
        $waterRate = $this->safeGetValue($averages, ['water_rate_avg', 'qw_sc_avg', 'qw_lc_avg']);
        $gasRate = $this->safeGetValue($averages, ['gas_rate_avg', 'qg_sc_avg', 'qg_lc_avg']);

        // Convert gas rate to equivalent liquid rate (rough approximation)
        $gasEquivalent = $gasRate ? $gasRate * 0.006 : 0; // Rough conversion factor

        if ($oilRate !== null && $waterRate !== null) {
            return $oilRate + $waterRate + $gasEquivalent;
        }

        return null;
    }

    /**
     * Calculate average sampling interval
     */
    private function calculateSamplingInterval(array $rows): ?float
    {
        if (count($rows) < 2) return null;

        $intervals = [];
        for ($i = 1; $i < count($rows); $i++) {
            $time1 = $this->parseDateTimeSafe($this->findTimeValue($rows[$i-1]), Carbon::today());
            $time2 = $this->parseDateTimeSafe($this->findTimeValue($rows[$i]), Carbon::today());

            if ($time1 && $time2) {
                $intervals[] = $time1->diffInSeconds($time2);
            }
        }

        if (empty($intervals)) return null;

        return array_sum($intervals) / count($intervals);
    }

    /**
     * Check if any of the specified columns exist in the data
     */
    private function hasColumn(array $columns, array $possibleNames): bool
    {
        foreach ($possibleNames as $name) {
            if (in_array($name, $columns)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare water cut chart data
     */
    private function prepareWaterCutChartData(array $columns, array $rows, string $timeColumn): array
    {
        $labels = [];
        $data = [];

        $waterCutColumn = $this->findColumnByName($columns, ['bsw', 'water_cut', 'wc']);

        if (!$waterCutColumn) {
            return ['labels' => [], 'datasets' => []];
        }

        foreach ($rows as $row) {
            $timeValue = $this->findTimeValue($row);
            if ($timeValue) {
                $labels[] = $timeValue;
                $data[] = is_numeric($row[$waterCutColumn] ?? null) ? (float) $row[$waterCutColumn] : null;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Water Cut (%)',
                    'data' => $data,
                    'borderColor' => '#17a2b8',
                    'backgroundColor' => 'rgba(23, 162, 184, 0.1)',
                    'fill' => false,
                    'tension' => 0.1,
                ]
            ]
        ];
    }

    /**
     * Prepare temperature chart data
     */
    private function prepareTemperatureChartData(array $columns, array $rows, string $timeColumn): array
    {
        $labels = [];
        $datasets = [];

        $tempColumns = [
            'temperature' => ['temperature', 'temp', 't_wh'],
            'oil_temperature' => ['oil_temp', 'temp_oil', 't_oil'],
            'water_temperature' => ['water_temp', 'temp_water', 't_water'],
            'gas_temperature' => ['gas_temp', 'temp_gas', 't_gas'],
        ];

        $colors = [
            'temperature' => ['#dc3545', 'rgba(220, 53, 69, 0.1)'],
            'oil_temperature' => ['#28a745', 'rgba(40, 167, 69, 0.1)'],
            'water_temperature' => ['#007bff', 'rgba(0, 123, 255, 0.1)'],
            'gas_temperature' => ['#ffc107', 'rgba(255, 193, 7, 0.1)'],
        ];

        foreach ($tempColumns as $key => $columnNames) {
            $column = $this->findColumnByName($columns, $columnNames);
            if ($column) {
                $data = [];
                $validLabels = [];

                foreach ($rows as $row) {
                    $timeValue = $this->findTimeValue($row);
                    if ($timeValue && isset($row[$column]) && is_numeric($row[$column])) {
                        $validLabels[] = $timeValue;
                        $data[] = (float) $row[$column];
                    }
                }

                if (!empty($data)) {
                    $labels = $validLabels;
                    $datasets[] = [
                        'label' => ucfirst(str_replace('_', ' ', $key)) . ' (°F)',
                        'data' => $data,
                        'borderColor' => $colors[$key][0],
                        'backgroundColor' => $colors[$key][1],
                        'fill' => false,
                        'tension' => 0.1,
                    ];
                }
            }
        }

        return [
            'labels' => $labels ?? [],
            'datasets' => $datasets
        ];
    }

    /**
     * Prepare pressure chart data
     */
    private function preparePressureChartData(array $columns, array $rows, string $timeColumn): array
    {
        $labels = [];
        $datasets = [];

        $pressureColumns = [
            'pressure' => ['pressure', 'wh_pressure', 'wellhead_pressure'],
            'upstream_pressure' => ['upstream_pressure', 'p_up'],
            'downstream_pressure' => ['downstream_pressure', 'p_down'],
        ];

        $colors = [
            'pressure' => ['#6f42c1', 'rgba(111, 66, 193, 0.1)'],
            'upstream_pressure' => ['#e83e8c', 'rgba(232, 62, 140, 0.1)'],
            'downstream_pressure' => ['#20c997', 'rgba(32, 201, 151, 0.1)'],
        ];

        foreach ($pressureColumns as $key => $columnNames) {
            $column = $this->findColumnByName($columns, $columnNames);
            if ($column) {
                $data = [];
                $validLabels = [];

                foreach ($rows as $row) {
                    $timeValue = $this->findTimeValue($row);
                    if ($timeValue && isset($row[$column]) && is_numeric($row[$column])) {
                        $validLabels[] = $timeValue;
                        $data[] = (float) $row[$column];
                    }
                }

                if (!empty($data)) {
                    $labels = $validLabels;
                    $datasets[] = [
                        'label' => ucfirst(str_replace('_', ' ', $key)) . ' (psi)',
                        'data' => $data,
                        'borderColor' => $colors[$key][0],
                        'backgroundColor' => $colors[$key][1],
                        'fill' => false,
                        'tension' => 0.1,
                    ];
                }
            }
        }

        return [
            'labels' => $labels ?? [],
            'datasets' => $datasets
        ];
    }

    /**
     * Prepare production comparison chart
     */
    private function prepareProductionComparisonChart(array $columns, array $rows, string $timeColumn): array
    {
        $labels = [];
        $datasets = [];

        $productionColumns = [
            'oil_rate' => ['qo_sc[STB/d]', 'qo_lc[bbl/d]', 'oil_rate'],
            'gas_rate' => ['qg_sc[MMScf/d]', 'qg_lc[Mcf/d]', 'gas_rate'],
            'water_rate' => ['qw_sc[STB/d]', 'qw_lc[bbl/d]', 'water_rate'],
        ];

        $colors = [
            'oil_rate' => ['#28a745', 'rgba(40, 167, 69, 0.1)'],
            'gas_rate' => ['#dc3545', 'rgba(220, 53, 69, 0.1)'],
            'water_rate' => ['#007bff', 'rgba(0, 123, 255, 0.1)'],
        ];

        foreach ($productionColumns as $key => $columnNames) {
            $column = $this->findColumnByName($columns, $columnNames);
            if ($column) {
                $data = [];
                $validLabels = [];

                foreach ($rows as $row) {
                    $timeValue = $this->findTimeValue($row);
                    if ($timeValue && isset($row[$column]) && is_numeric($row[$column])) {
                        $validLabels[] = $timeValue;
                        $data[] = (float) $row[$column];
                    }
                }

                if (!empty($data)) {
                    $labels = $validLabels;
                    $datasets[] = [
                        'label' => ucfirst(str_replace('_', ' ', $key)),
                        'data' => $data,
                        'borderColor' => $colors[$key][0],
                        'backgroundColor' => $colors[$key][1],
                        'fill' => false,
                        'tension' => 0.1,
                    ];
                }
            }
        }

        return [
            'labels' => $labels ?? [],
            'datasets' => $datasets
        ];
    }

    /**
     * Calculate stable duration - the period after the test stabilizes
     * Tries to detect when production rates stabilize and calculates duration from that point
     */
    private function calculateStableDuration(array $rows, ?Carbon $startTime): ?float
    {
        if (count($rows) < 5 || !$startTime) {
            return null;
        }

        // Find the last row which should be the end time
        $lastRow = end($rows);
        $endTime = $this->parseDateTimeSafe($this->findTimeValue($lastRow), Carbon::today());

        if (!$endTime) {
            return null;
        }

        // Try to detect stability by checking oil rate variance
        $oilRateColumn = $this->findColumnByName(array_keys($rows[0] ?? []), ['qo_sc[STB/d]', 'qo_lc[bbl/d]', 'oil_rate']);

        if (!$oilRateColumn) {
            // If we can't find oil rate, assume stability starts at 50% of the test
            $totalMinutes = $startTime->diffInMinutes($endTime);
            return $totalMinutes ? round(($totalMinutes * 0.5) / 60, 2) : null;
        }

        // Calculate oil rates and find when variance stabilizes
        $oilRates = [];
        $timestamps = [];
        foreach ($rows as $row) {
            if (isset($row[$oilRateColumn]) && is_numeric($row[$oilRateColumn])) {
                $oilRates[] = (float) $row[$oilRateColumn];
                $time = $this->parseDateTimeSafe($this->findTimeValue($row), Carbon::today());
                if ($time) {
                    $timestamps[] = $time;
                }
            }
        }

        if (count($oilRates) < 5) {
            // Not enough data, return full duration
            $totalMinutes = $startTime->diffInMinutes($endTime);
            return $totalMinutes ? round($totalMinutes / 60, 2) : null;
        }

        // Find the point where variance stabilizes (using rolling average)
        $windowSize = max(3, (int)(count($oilRates) * 0.1)); // 10% of data points
        $stabilityThreshold = 5; // 5% threshold

        $stabilityStartIndex = null;
        for ($i = $windowSize; $i < count($oilRates); $i++) {
            $window = array_slice($oilRates, $i - $windowSize, $windowSize);
            $avg = array_sum($window) / count($window);
            $variance = array_sum(array_map(function($x) use ($avg) {
                return pow($x - $avg, 2);
            }, $window)) / count($window);
            $stdDev = sqrt($variance);
            $coefficientOfVariation = ($avg > 0) ? ($stdDev / $avg) * 100 : 0;

            if ($coefficientOfVariation < $stabilityThreshold) {
                $stabilityStartIndex = $i;
                break;
            }
        }

        if ($stabilityStartIndex && isset($timestamps[$stabilityStartIndex])) {
            $stableStartTime = $timestamps[$stabilityStartIndex];
            $stableDurationMinutes = $stableStartTime->diffInMinutes($endTime);
            return round($stableDurationMinutes / 60, 2);
        }

        // If we can't detect stability, assume 70% of test is stable
        $totalMinutes = $startTime->diffInMinutes($endTime);
        return $totalMinutes ? round(($totalMinutes * 0.7) / 60, 2) : null;
    }

    /**
     * Find column by name from possible names
     */
    private function findColumnByName(array $columns, array $possibleNames): ?string
    {
        foreach ($possibleNames as $name) {
            if (in_array($name, $columns)) {
                return $name;
            }
        }
        return null;
    }

    /**
     * Move temporary file to permanent storage
     */
    public function moveToPermanentStorage(string $tempFilePath): ?string
    {
        if (!Storage::disk('public')->exists($tempFilePath)) {
            return null;
        }

        $newFileName = basename($tempFilePath);
        $permanentPath = 'mpfm_logs/' . $newFileName;

        Storage::disk('public')->move($tempFilePath, $permanentPath);
        return $permanentPath;
    }

    /**
     * Validate MPFM data completeness
     */
    public function validateMPFMData(array $mpfmData): array
    {
        $validation = [
            'is_valid' => true,
            'warnings' => [],
            'errors' => [],
        ];

        // Check for critical missing data
        if (empty($mpfmData['oil_rate']) && empty($mpfmData['gas_rate'])) {
            $validation['errors'][] = 'No production rate data found (oil or gas)';
            $validation['is_valid'] = false;
        }

        // Check for unrealistic values
        if (($mpfmData['oil_rate'] ?? 0) < 0) {
            $validation['warnings'][] = 'Negative oil rate detected';
        }

        if (($mpfmData['gas_rate'] ?? 0) < 0) {
            $validation['warnings'][] = 'Negative gas rate detected';
        }

        if (($mpfmData['water_cut'] ?? 0) > 100) {
            $validation['warnings'][] = 'Water cut exceeds 100%';
        }

        // Check data quality
        $dataQuality = $mpfmData['data_quality'] ?? [];
        if (($dataQuality['score'] ?? 100) < 50) {
            $validation['warnings'][] = 'Low data quality score detected';
        }

        return $validation;
    }

    /**
     * Helper: Determine shift based on time
     */
    private function determineShift(?Carbon $dateTime): string
    {
        if (!$dateTime) {
            return 'morning';
        }

        $hour = $dateTime->hour;
        if ($hour >= 6 && $hour < 14) {
            return 'morning';
        } elseif ($hour >= 14 && $hour < 22) {
            return 'afternoon';
        } else {
            return 'night';
        }
    }

    /**
     * Helper: Safe datetime parsing
     */
    private function parseDateTimeSafe(?string $dateTimeStr, Carbon $default): ?Carbon
    {
        if (!$dateTimeStr) {
            return null;
        }

        try {
            // Try multiple common formats
            $formats = [
                'Y-m-d H:i:s',
                'm/d/Y H:i:s',
                'd/m/Y H:i:s',
                'Y-m-d H:i',
                'H:i:s',
                'H:i'
            ];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $dateTimeStr);
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Last resort: strtotime
            $timestamp = strtotime($dateTimeStr);
            if ($timestamp) {
                return Carbon::createFromTimestamp($timestamp);
            }

        } catch (\Exception $e) {
            // Ignore and return null
        }

        return null;
    }

    /**
     * Safely extract detailed metrics with error handling
     */
    private function safeExtractDetailedMetrics(array $parsedData, string $rawContent): array
    {
        try {
            return $this->metricsExtractor->extractDetailedMetrics($parsedData, $rawContent);
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to extract detailed metrics: ' . $e->getMessage(),
            ];
        }
    }
}

