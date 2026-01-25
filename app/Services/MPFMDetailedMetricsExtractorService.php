<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Service to extract detailed metrics from MPFM log files
 * Extracts all flow rates, ratios, PVT data, and other measurements
 */
class MPFMDetailedMetricsExtractorService
{
    /**
     * Extract all detailed metrics from parsed log data
     */
    public function extractDetailedMetrics(array $parsedData, string $rawContent): array
    {
        $rows = $parsedData['test_data']['rows'] ?? [];
        $columns = $parsedData['test_data']['columns'] ?? [];

        if (empty($rows)) {
            return $this->getEmptyMetricsStructure();
        }

        // Extract timing information
        $timingInfo = $this->extractTestTimingInfo($rows, $columns);

        // Extract all metrics grouped by category
        $metrics = [
            'timing' => $timingInfo,
            'flow_rates' => $this->extractFlowRates($rows, $columns),
            'fractions_ratios' => $this->extractFractionsAndRatios($rows, $columns),
            'water_cut_metrics' => $this->extractWaterCutMetrics($rows, $columns),
            'gas_oil_relations' => $this->extractGasOilRelations($rows, $columns),
            'density_mass_flow' => $this->extractDensityAndMassFlow($rows, $columns),
            'cumulative_volumes' => $this->extractCumulativeVolumes($rows, $columns),
            'pvt_relationships' => $this->extractPVTRelationships($rows, $columns),
            'pressure_diagnostics' => $this->extractPressureDiagnostics($rows, $columns),
            'detector_signals' => $this->extractDetectorSignals($rows, $columns),
            'gas_composition' => $this->extractGasComposition($rows, $columns),
            'choke_calculations' => $this->extractChokeCalculations($rows, $columns),
            'averages' => $this->extractAverages($rows, $columns),
            'statistics' => $this->extractStatistics($rows, $columns),
            'data_quality' => $this->assessDataQuality($rows, $columns),
        ];

        return $metrics;
    }

    /**
     * Extract test timing information from log data
     */
    private function extractTestTimingInfo(array $rows, array $columns): array
    {
        $timeColumn = $this->findTimeColumn($columns);

        if (!$timeColumn) {
            return [
                'test_started_at' => null,
                'test_completed_at' => null,
                'test_duration_hours' => 0,
                'test_total_data_points' => count($rows),
                'error' => 'Time column not found'
            ];
        }

        $firstRow = $rows[0] ?? [];
        $lastRow = end($rows) ?? [];

        $startTime = $firstRow[$timeColumn] ?? null;
        $endTime = $lastRow[$timeColumn] ?? null;

        // Parse times
        $startDateTime = $this->parseLogTimestamp($startTime);
        $endDateTime = $this->parseLogTimestamp($endTime);

        $duration = 0;
        if ($startDateTime && $endDateTime) {
            $duration = $startDateTime->diffInMinutes($endDateTime) / 60;
        }

        return [
            'test_started_at' => $startDateTime?->toDateTimeString(),
            'test_completed_at' => $endDateTime?->toDateTimeString(),
            'test_duration_hours' => round($duration, 2),
            'test_total_data_points' => count($rows),
            'test_stable_duration_hours' => $this->estimateStableDuration($rows, $columns),
        ];
    }

    /**
     * Extract flow rates (line and standard conditions)
     */
    private function extractFlowRates(array $rows, array $columns): array
    {
        return [
            'line_conditions' => [
                'qo_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['qo_lc[bbl/d]', 'qo_lc']),
                    'unit' => 'bbl/d',
                    'label' => 'Oil Rate (Line Conditions)',
                ],
                'qw_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['qw_lc[bbl/d]', 'qw_lc']),
                    'unit' => 'bbl/d',
                    'label' => 'Water Rate (Line Conditions)',
                ],
                'qg_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['qg_lc[Mcf/d]', 'qg_lc']),
                    'unit' => 'Mcf/d',
                    'label' => 'Gas Rate (Line Conditions)',
                ],
                'ql_lc' => [
                    'value' => $this->calculateLiquidRate($rows, $columns, 'lc'),
                    'unit' => 'bbl/d',
                    'label' => 'Liquid Rate (Line Conditions)',
                ],
            ],
            'standard_conditions' => [
                'qo_sc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['qo_sc[STB/d]', 'qo_sc']),
                    'unit' => 'STB/d',
                    'label' => 'Oil Rate (Standard Conditions)',
                ],
                'qw_sc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['qw_sc[STB/d]', 'qw_sc']),
                    'unit' => 'STB/d',
                    'label' => 'Water Rate (Standard Conditions)',
                ],
                'qg_sc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['qg_sc[MMScf/d]', 'qg_sc']),
                    'unit' => 'MMScf/d',
                    'label' => 'Gas Rate (Standard Conditions)',
                ],
            ]
        ];
    }

    /**
     * Extract fractions and ratios
     */
    private function extractFractionsAndRatios(array $rows, array $columns): array
    {
        return [
            'line_conditions' => [
                'fo_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['Fo_lc[%]', 'fo_lc']),
                    'unit' => '%',
                    'label' => 'Oil Fraction (Line Conditions)',
                ],
                'fw_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['Fw_lc[%]', 'fw_lc']),
                    'unit' => '%',
                    'label' => 'Water Fraction (Line Conditions)',
                ],
                'fg_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['Fg_lc[%]', 'fg_lc']),
                    'unit' => '%',
                    'label' => 'Gas Fraction (Line Conditions)',
                ],
            ]
        ];
    }

    /**
     * Extract water cut metrics (BSW, WLR)
     */
    private function extractWaterCutMetrics(array $rows, array $columns): array
    {
        return [
            'bsw' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['BSW[%]', 'bsw']),
                'unit' => '%',
                'label' => 'Bottom Sediment & Water',
                'max' => $this->findColumnMax($rows, $columns, ['BSW[%]', 'bsw']),
                'min' => $this->findColumnMin($rows, $columns, ['BSW[%]', 'bsw']),
            ],
            'wlr' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['WLR[%]', 'wlr']),
                'unit' => '%',
                'label' => 'Water Liquid Ratio',
                'max' => $this->findColumnMax($rows, $columns, ['WLR[%]', 'wlr']),
                'min' => $this->findColumnMin($rows, $columns, ['WLR[%]', 'wlr']),
            ],
            'water_cut' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['water_cut', 'WC']),
                'unit' => '%',
                'label' => 'Water Cut',
                'max' => $this->findColumnMax($rows, $columns, ['water_cut', 'WC']),
                'min' => $this->findColumnMin($rows, $columns, ['water_cut', 'WC']),
            ]
        ];
    }

    /**
     * Extract gas-oil relations
     */
    private function extractGasOilRelations(array $rows, array $columns): array
    {
        return [
            'gor' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['GOR[Scf/STB]', 'gor']),
                'unit' => 'Scf/STB',
                'label' => 'Gas-Oil Ratio',
                'max' => $this->findColumnMax($rows, $columns, ['GOR[Scf/STB]', 'gor']),
                'min' => $this->findColumnMin($rows, $columns, ['GOR[Scf/STB]', 'gor']),
            ],
            'glr' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['GLR[m³/m³]', 'glr']),
                'unit' => 'm³/m³',
                'label' => 'Gas-Liquid Ratio',
            ],
            'gvf' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['GVF[%]', 'gvf']),
                'unit' => '%',
                'label' => 'Gas Volume Fraction',
                'max' => $this->findColumnMax($rows, $columns, ['GVF[%]', 'gvf']),
                'min' => $this->findColumnMin($rows, $columns, ['GVF[%]', 'gvf']),
            ]
        ];
    }

    /**
     * Extract density and mass flow data
     */
    private function extractDensityAndMassFlow(array $rows, array $columns): array
    {
        return [
            'line_conditions' => [
                'po_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['Do_lc[kg/m3]', 'po_lc']),
                    'unit' => 'kg/m³',
                    'label' => 'Oil Density (Line Conditions)',
                ],
                'pw_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['Dw_lc[kg/m3]', 'pw_lc']),
                    'unit' => 'kg/m³',
                    'label' => 'Water Density (Line Conditions)',
                ],
                'pg_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['Dg_lc[kg/m3]', 'pg_lc']),
                    'unit' => 'kg/m³',
                    'label' => 'Gas Density (Line Conditions)',
                ],
                'mo_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['mo_lc[kg/d]', 'mo_lc']),
                    'unit' => 'kg/d',
                    'label' => 'Oil Mass Flow Rate',
                ],
                'mw_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['mw_lc[kg/d]', 'mw_lc']),
                    'unit' => 'kg/d',
                    'label' => 'Water Mass Flow Rate',
                ],
                'mg_lc' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['mg_lc[kg/d]', 'mg_lc']),
                    'unit' => 'kg/d',
                    'label' => 'Gas Mass Flow Rate',
                ],
            ]
        ];
    }

    /**
     * Extract cumulative volumes
     */
    private function extractCumulativeVolumes(array $rows, array $columns): array
    {
        return [
            'line_conditions' => [
                'cvo_lc' => [
                    'value' => $this->findLastValue($rows, $columns, ['cvo_lc[bbl]', 'cvo_lc']),
                    'unit' => 'bbl',
                    'label' => 'Cumulative Oil Volume (Line)',
                ],
                'cvw_lc' => [
                    'value' => $this->findLastValue($rows, $columns, ['cvw_lc[bbl]', 'cvw_lc']),
                    'unit' => 'bbl',
                    'label' => 'Cumulative Water Volume (Line)',
                ],
                'cvg_lc' => [
                    'value' => $this->findLastValue($rows, $columns, ['cvg_lc[cf]', 'cvg_lc']),
                    'unit' => 'cf',
                    'label' => 'Cumulative Gas Volume (Line)',
                ],
            ],
            'standard_conditions' => [
                'cvo_sc' => [
                    'value' => $this->findLastValue($rows, $columns, ['cvo_sc[STB]', 'cvo_sc']),
                    'unit' => 'STB',
                    'label' => 'Cumulative Oil Volume (Standard)',
                ],
                'cvw_sc' => [
                    'value' => $this->findLastValue($rows, $columns, ['cvw_sc[STB]', 'cvw_sc']),
                    'unit' => 'STB',
                    'label' => 'Cumulative Water Volume (Standard)',
                ],
                'cvg_sc' => [
                    'value' => $this->findLastValue($rows, $columns, ['cvg_sc[Scf]', 'cvg_sc']),
                    'unit' => 'Scf',
                    'label' => 'Cumulative Gas Volume (Standard)',
                ],
            ]
        ];
    }

    /**
     * Extract PVT relationships
     */
    private function extractPVTRelationships(array $rows, array $columns): array
    {
        return [
            'bo' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['bo[Sm³/m³]', 'bo']),
                'unit' => 'Sm³/m³',
                'label' => 'Oil Formation Volume Factor',
            ],
            'bw' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['bw[Sm³/m³]', 'bw']),
                'unit' => 'Sm³/m³',
                'label' => 'Water Formation Volume Factor',
            ],
            'bg' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['bg[Sm³/m³]', 'bg']),
                'unit' => 'Sm³/m³',
                'label' => 'Gas Formation Volume Factor',
            ],
            'z_factor' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['Z', 'z_factor', 'compressibility']),
                'unit' => 'dimensionless',
                'label' => 'Gas Compressibility Factor (Z-factor)',
            ],
            'rst' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['Rst[Scf/STB]', 'rst']),
                'unit' => 'Scf/STB',
                'label' => 'Solution Gas-Oil Ratio',
            ]
        ];
    }

    /**
     * Extract pressure diagnostics
     */
    private function extractPressureDiagnostics(array $rows, array $columns): array
    {
        $pressureCols = ['PL[bara]', 'pressure', 'wh_pressure'];
        $temperatureCols = ['TL[DegC]', 'temperature', 'wh_temperature'];

        return [
            'pressure' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, $pressureCols),
                'max' => $this->findColumnMax($rows, $columns, $pressureCols),
                'min' => $this->findColumnMin($rows, $columns, $pressureCols),
                'unit' => 'psia/bara',
                'label' => 'Wellhead Pressure',
            ],
            'temperature' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, $temperatureCols),
                'max' => $this->findColumnMax($rows, $columns, $temperatureCols),
                'min' => $this->findColumnMin($rows, $columns, $temperatureCols),
                'unit' => '°C/°F',
                'label' => 'Wellhead Temperature',
            ],
            'dp' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['Dyn_DP[mbar]', 'dp']),
                'unit' => 'mbar',
                'label' => 'Differential Pressure',
            ],
            'dv' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['DPV[mbar]', 'dv']),
                'unit' => 'mbar',
                'label' => 'Volumetric Differential Pressure',
            ]
        ];
    }

    /**
     * Extract detector signals
     */
    private function extractDetectorSignals(array $rows, array $columns): array
    {
        return [
            'gamma_signals' => [
                'n32_avg' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['N32[cps]', 'n32']),
                    'unit' => 'cps',
                    'label' => '32 keV Photopeak Count Rate',
                ],
                'n81_avg' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['N81[cps]', 'n81']),
                    'unit' => 'cps',
                    'label' => '81 keV Photopeak Count Rate',
                ],
                'n356_avg' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['N356[cps]', 'n356']),
                    'unit' => 'cps',
                    'label' => '356 keV Photopeak Count Rate',
                ],
                'ntotal_avg' => [
                    'value' => $this->findAndCalculateAverage($rows, $columns, ['NTotal[cps]', 'ntotal']),
                    'unit' => 'cps',
                    'label' => 'Total Count Rate',
                ],
            ],
            'detector_temp' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['DetectorTemp[DegC]', 'detector_temp']),
                'unit' => '°C',
                'label' => 'Detector Temperature',
            ],
            'hv_gain' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['HVGain[V]', 'hv_gain']),
                'unit' => 'V',
                'label' => 'High Voltage Gain',
            ]
        ];
    }

    /**
     * Extract gas composition
     */
    private function extractGasComposition(array $rows, array $columns): array
    {
        return [
            'h2s' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['mH2S_sc', 'h2s']),
                'unit' => 'ppm/mole fraction',
                'label' => 'Hydrogen Sulfide',
            ],
            'h2s_in_hc' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['H2SMfrInHC[%]', 'h2s_in_hc']),
                'unit' => '%',
                'label' => 'H2S in Hydrocarbons',
            ],
            'co2' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['CO2', 'co2']),
                'unit' => '%',
                'label' => 'Carbon Dioxide',
            ],
            'n2' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['N2', 'n2']),
                'unit' => '%',
                'label' => 'Nitrogen',
            ]
        ];
    }

    /**
     * Extract choke calculations
     */
    private function extractChokeCalculations(array $rows, array $columns): array
    {
        return [
            'choke_size' => [
                'value' => $this->findLastValue($rows, $columns, ['choke_size', 'choke']),
                'unit' => '64ths of inch',
                'label' => 'Choke Size',
            ],
            'choke_dp' => [
                'value' => $this->findAndCalculateAverage($rows, $columns, ['Pthroat', 'choke_dp']),
                'unit' => 'psia',
                'label' => 'Choke Differential Pressure',
            ]
        ];
    }

    /**
     * Extract statistical averages
     */
    private function extractAverages(array $rows, array $columns): array
    {
        return [
            'flow_weighted_averages' => [
                'qo_flow_weighted' => $this->calculateFlowWeightedAverage($rows, $columns, ['qo_sc[STB/d]', 'qo_sc']),
                'qw_flow_weighted' => $this->calculateFlowWeightedAverage($rows, $columns, ['qw_sc[STB/d]', 'qw_sc']),
                'qg_flow_weighted' => $this->calculateFlowWeightedAverage($rows, $columns, ['qg_sc[MMScf/d]', 'qg_sc']),
            ]
        ];
    }

    /**
     * Extract statistics
     */
    private function extractStatistics(array $rows, array $columns): array
    {
        return [
            'data_points' => count($rows),
            'time_span_hours' => $this->calculateTimeSpan($rows, $columns),
            'measurement_interval_seconds' => $this->calculateMeasurementInterval($rows, $columns),
        ];
    }

    /**
     * Assess data quality
     */
    private function assessDataQuality(array $rows, array $columns): array
    {
        $totalRows = count($rows);
        $missingDataCount = 0;

        foreach ($rows as $row) {
            $nullCount = 0;
            foreach ($row as $value) {
                if (empty($value) || $value === null || $value === '') {
                    $nullCount++;
                }
            }
            if ($nullCount > count($columns) * 0.3) { // More than 30% missing
                $missingDataCount++;
            }
        }

        $missingPercentage = ($missingDataCount / max($totalRows, 1)) * 100;
        $qualityScore = max(0, 1 - ($missingPercentage / 100));

        return [
            'quality_score' => round($qualityScore, 3),
            'missing_data_percentage' => round($missingPercentage, 2),
            'total_rows' => $totalRows,
            'status' => $qualityScore >= 0.8 ? 'Good' : ($qualityScore >= 0.6 ? 'Fair' : 'Poor'),
        ];
    }

    // Helper methods

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

    private function findTimeColumn(array $columns): ?string
    {
        $timeColumns = ['Date & Time', 'date & time', 'Datetime', 'datetime', 'Time', 'time', 'Timestamp'];
        foreach ($timeColumns as $col) {
            if (in_array($col, $columns)) {
                return $col;
            }
        }
        return null;
    }

    private function parseLogTimestamp(?string $timestamp): ?Carbon
    {
        if (!$timestamp) return null;

        // Try parsing MM/DD/YYYY HH:MM format
        try {
            return Carbon::createFromFormat('m/d/Y H:i', trim($timestamp));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function findAndCalculateAverage(array $rows, array $columns, array $possibleColumns): ?float
    {
        $columnName = $this->findColumn($columns, $possibleColumns);
        if (!$columnName) return null;

        $values = [];
        foreach ($rows as $row) {
            $value = $row[$columnName] ?? null;
            if ($value !== null && $value !== '') {
                $parsedValue = $this->parseNumeric($value);
                if (is_numeric($parsedValue) && $parsedValue !== null) {
                    $values[] = (float) $parsedValue;
                }
            }
        }

        return !empty($values) ? array_sum($values) / count($values) : null;
    }

    private function findColumnMax(array $rows, array $columns, array $possibleColumns): ?float
    {
        $columnName = $this->findColumn($columns, $possibleColumns);
        if (!$columnName) return null;

        $values = [];
        foreach ($rows as $row) {
            $value = $row[$columnName] ?? null;
            if ($value !== null && $value !== '') {
                $parsedValue = $this->parseNumeric($value);
                if (is_numeric($parsedValue) && $parsedValue !== null) {
                    $values[] = (float) $parsedValue;
                }
            }
        }

        return !empty($values) ? max($values) : null;
    }

    private function findColumnMin(array $rows, array $columns, array $possibleColumns): ?float
    {
        $columnName = $this->findColumn($columns, $possibleColumns);
        if (!$columnName) return null;

        $values = [];
        foreach ($rows as $row) {
            $value = $row[$columnName] ?? null;
            if ($value !== null && $value !== '') {
                $parsedValue = $this->parseNumeric($value);
                if (is_numeric($parsedValue) && $parsedValue !== null) {
                    $values[] = (float) $parsedValue;
                }
            }
        }

        return !empty($values) ? min($values) : null;
    }

    private function findLastValue(array $rows, array $columns, array $possibleColumns): ?float
    {
        $columnName = $this->findColumn($columns, $possibleColumns);
        if (!$columnName || empty($rows)) return null;

        $lastRow = end($rows);
        $value = $lastRow[$columnName] ?? null;

        if ($value !== null && $value !== '') {
            $parsedValue = $this->parseNumeric($value);
            if (is_numeric($parsedValue) && $parsedValue !== null) {
                return (float) $parsedValue;
            }
        }

        return null;
    }

    private function findColumn(array $columns, array $possibleColumns): ?string
    {
        // First try exact match
        foreach ($possibleColumns as $possible) {
            if (in_array($possible, $columns)) {
                return $possible;
            }
        }

        // If no exact match, try case-insensitive partial match (same as MPFMCalculationService)
        foreach ($columns as $col) {
            foreach ($possibleColumns as $possible) {
                // Handle wildcard patterns
                if (strpos($possible, '*') !== false) {
                    $pattern = str_replace('*', '', $possible);
                    if (stripos($col, $pattern) !== false) {
                        return $col;
                    }
                } else {
                    // Case-insensitive partial match
                    if (stripos($col, $possible) !== false) {
                        return $col;
                    }
                }
            }
        }

        return null;
    }

    private function calculateLiquidRate(array $rows, array $columns, string $condition): ?float
    {
        $oilCol = $condition === 'lc' ? $this->findColumn($columns, ['qo_lc[bbl/d]', 'qo_lc']) :
                  $this->findColumn($columns, ['qo_sc[STB/d]', 'qo_sc']);
        $waterCol = $condition === 'lc' ? $this->findColumn($columns, ['qw_lc[bbl/d]', 'qw_lc']) :
                    $this->findColumn($columns, ['qw_sc[STB/d]', 'qw_sc']);

        if (!$oilCol || !$waterCol) return null;

        $liquidRates = [];
        foreach ($rows as $row) {
            $oil = $row[$oilCol] ?? null;
            $water = $row[$waterCol] ?? null;
            if ($oil !== null && $water !== null && $oil !== '' && $water !== '') {
                $oilParsed = $this->parseNumeric($oil);
                $waterParsed = $this->parseNumeric($water);
                if (is_numeric($oilParsed) && is_numeric($waterParsed)) {
                    $liquidRates[] = (float)$oilParsed + (float)$waterParsed;
                }
            }
        }

        return !empty($liquidRates) ? array_sum($liquidRates) / count($liquidRates) : null;
    }

    private function estimateStableDuration(array $rows, array $columns): float
    {
        // Estimate stable duration as 80% of total duration (first 10% and last 10% often contain startup/shutdown transients)
        $totalDuration = $this->calculateTimeSpan($rows, $columns);
        return round($totalDuration * 0.8, 2);
    }

    private function calculateTimeSpan(array $rows, array $columns): float
    {
        $timeColumn = $this->findTimeColumn($columns);
        if (!$timeColumn || empty($rows)) return 0;

        $firstTime = $this->parseLogTimestamp($rows[0][$timeColumn] ?? null);
        $lastTime = $this->parseLogTimestamp((end($rows))[$timeColumn] ?? null);

        if (!$firstTime || !$lastTime) return 0;

        return $firstTime->diffInMinutes($lastTime) / 60;
    }

    private function calculateMeasurementInterval(array $rows, array $columns): float
    {
        if (count($rows) < 2) return 0;

        $timeColumn = $this->findTimeColumn($columns);
        if (!$timeColumn) return 0;

        $firstTime = $this->parseLogTimestamp($rows[0][$timeColumn] ?? null);
        $secondTime = $this->parseLogTimestamp($rows[1][$timeColumn] ?? null);

        if (!$firstTime || !$secondTime) return 0;

        return $firstTime->diffInSeconds($secondTime);
    }

    private function calculateFlowWeightedAverage(array $rows, array $columns, array $rateColumns): ?float
    {
        // This would require additional logic to properly weight by flow rates
        // For now, return simple average
        return $this->findAndCalculateAverage($rows, $columns, $rateColumns);
    }

    private function getEmptyMetricsStructure(): array
    {
        return [
            'timing' => [],
            'flow_rates' => [],
            'fractions_ratios' => [],
            'water_cut_metrics' => [],
            'gas_oil_relations' => [],
            'density_mass_flow' => [],
            'cumulative_volumes' => [],
            'pvt_relationships' => [],
            'pressure_diagnostics' => [],
            'detector_signals' => [],
            'gas_composition' => [],
            'choke_calculations' => [],
            'averages' => [],
            'statistics' => [],
            'data_quality' => [],
        ];
    }
}
