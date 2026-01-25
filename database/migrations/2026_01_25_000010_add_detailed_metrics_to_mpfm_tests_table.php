<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            // Test Timing Information
            if (!Schema::hasColumn('mpfm_tests', 'test_started_at')) {
                $table->dateTime('test_started_at')->nullable()->comment('Test start time from log file');
            }
            if (!Schema::hasColumn('mpfm_tests', 'test_completed_at')) {
                $table->dateTime('test_completed_at')->nullable()->comment('Test completion time from log file');
            }

            // Flow Rates (Line Conditions)
            $metricsToAdd = [
                // Flow Rates - Line Conditions
                'qo_lc' => 'decimal:2',  // Oil rate line conditions [bbl/d]
                'qw_lc' => 'decimal:2',  // Water rate line conditions [bbl/d]
                'qg_lc' => 'decimal:2',  // Gas rate line conditions [Mcf/d]
                'ql_lc' => 'decimal:2',  // Liquid rate line conditions [bbl/d]

                // Flow Rates - Standard Conditions
                'qo_sc' => 'decimal:2',  // Oil rate standard conditions [STB/d]
                'qw_sc' => 'decimal:2',  // Water rate standard conditions [STB/d]
                'qg_sc' => 'decimal:2',  // Gas rate standard conditions [MMScf/d]

                // Fractions & Ratios
                'fo_lc' => 'decimal:2',  // Oil fraction line conditions [%]
                'fw_lc' => 'decimal:2',  // Water fraction line conditions [%]
                'fg_lc' => 'decimal:2',  // Gas fraction line conditions [%]

                // Water Cut / BSW / WLR
                'bsw' => 'decimal:2',    // Bottom water sediment [%]
                'wlr' => 'decimal:2',    // Water liquid ratio [%]
                'water_cut' => 'decimal:2', // Water cut [%]

                // Gas-Oil Relations
                'gor' => 'decimal:2',    // Gas-oil ratio [Scf/STB]
                'glr' => 'decimal:2',    // Gas-liquid ratio [Scf/STB]
                'gvf' => 'decimal:2',    // Gas volume fraction [%]

                // Density & Mass Flow
                'po_lc' => 'decimal:3',  // Oil density line conditions [kg/m³]
                'pw_lc' => 'decimal:3',  // Water density line conditions [kg/m³]
                'pg_lc' => 'decimal:3',  // Gas density line conditions [kg/m³]
                'mo_lc' => 'decimal:2',  // Oil mass flow rate [kg/d]
                'mw_lc' => 'decimal:2',  // Water mass flow rate [kg/d]
                'mg_lc' => 'decimal:2',  // Gas mass flow rate [kg/d]

                // Cumulative Volumes (Totals)
                'cvo_lc' => 'decimal:2', // Cumulative oil volume line conditions [bbl]
                'cvw_lc' => 'decimal:2', // Cumulative water volume line conditions [bbl]
                'cvg_lc' => 'decimal:2', // Cumulative gas volume line conditions [cf]
                'cvo_sc' => 'decimal:2', // Cumulative oil volume standard conditions [STB]
                'cvw_sc' => 'decimal:2', // Cumulative water volume standard conditions [STB]
                'cvg_sc' => 'decimal:2', // Cumulative gas volume standard conditions [Scf]

                // PVT Relationships
                'bo' => 'decimal:6',     // Oil formation volume factor [Sm³/m³]
                'bw' => 'decimal:6',     // Water formation volume factor [Sm³/m³]
                'bg' => 'decimal:6',     // Gas formation volume factor [Sm³/m³]
                'z_factor' => 'decimal:4', // Gas compressibility factor
                'rst' => 'decimal:2',    // Solution gas-oil ratio [Scf/STB]

                // Pressure & DP Diagnostics
                'pressure' => 'decimal:2',    // Wellhead pressure [psia]
                'temperature' => 'decimal:2', // Wellhead temperature [°F/°C]
                'dp' => 'decimal:2',          // Differential pressure [mbar]
                'pl' => 'decimal:2',          // Line pressure [bara]
                'tl' => 'decimal:2',          // Line temperature [°C]

                // Pressure & Temperature Averages
                'pressure_avg' => 'decimal:2',
                'pressure_max' => 'decimal:2',
                'pressure_min' => 'decimal:2',
                'temperature_avg' => 'decimal:2',
                'temperature_max' => 'decimal:2',
                'temperature_min' => 'decimal:2',

                // Flow weighted averages
                'qo_flow_weighted' => 'decimal:2',
                'qw_flow_weighted' => 'decimal:2',
                'qg_flow_weighted' => 'decimal:2',

                // Ratios - Standard Conditions
                'bsw_sc' => 'decimal:2',
                'wlr_sc' => 'decimal:2',
                'gor_sc' => 'decimal:2',

                // Gas Breakdown
                'h2s_mfr' => 'decimal:2',   // H2S mole fraction [%]
                'co2_mfr' => 'decimal:2',   // CO2 mole fraction [%]
                'n2_mfr' => 'decimal:2',    // N2 mole fraction [%]

                // Choke Calculations
                'choke_size' => 'decimal:2',
                'choke_dp' => 'decimal:2',

                // Test Duration Metrics
                'test_duration_hours' => 'decimal:2',
                'test_stable_duration_hours' => 'decimal:2',
                'test_total_data_points' => 'integer',

                // Advanced Detector Signals
                'gamma_reading' => 'decimal:2',
                'detector_temp' => 'decimal:2',

                // Data Quality Metrics
                'data_quality_score' => 'decimal:3', // 0-1.0
                'missing_data_percentage' => 'decimal:2',
            ];

            foreach ($metricsToAdd as $column => $type) {
                if (!Schema::hasColumn('mpfm_tests', $column)) {
                    if ($type === 'integer') {
                        $table->integer($column)->nullable();
                    } else {
                        $table->$type($column)->nullable();
                    }
                }
            }

            // JSON columns for detailed metrics storage
            if (!Schema::hasColumn('mpfm_tests', 'detailed_metrics')) {
                $table->json('detailed_metrics')->nullable()->comment('Complete detailed metrics extracted from log file');
            }
            if (!Schema::hasColumn('mpfm_tests', 'flow_rate_metrics')) {
                $table->json('flow_rate_metrics')->nullable()->comment('All flow rate measurements');
            }
            if (!Schema::hasColumn('mpfm_tests', 'pvt_metrics')) {
                $table->json('pvt_metrics')->nullable()->comment('PVT (Pressure-Volume-Temperature) data');
            }
            if (!Schema::hasColumn('mpfm_tests', 'pressure_temperature_metrics')) {
                $table->json('pressure_temperature_metrics')->nullable()->comment('Pressure and temperature statistics');
            }
            if (!Schema::hasColumn('mpfm_tests', 'ratio_metrics')) {
                $table->json('ratio_metrics')->nullable()->comment('All calculated ratios (GOR, WLR, BSW, etc.)');
            }
            if (!Schema::hasColumn('mpfm_tests', 'cumulative_metrics')) {
                $table->json('cumulative_metrics')->nullable()->comment('Cumulative volumes and masses');
            }
            if (!Schema::hasColumn('mpfm_tests', 'data_quality_metrics')) {
                $table->json('data_quality_metrics')->nullable()->comment('Data quality assessment and flags');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            $columnsToRemove = [
                // Timing
                'test_started_at', 'test_completed_at',
                // Flow Rates
                'qo_lc', 'qw_lc', 'qg_lc', 'ql_lc',
                'qo_sc', 'qw_sc', 'qg_sc',
                // Fractions
                'fo_lc', 'fw_lc', 'fg_lc',
                // Ratios
                'bsw', 'wlr', 'water_cut', 'gor', 'glr', 'gvf',
                // Density
                'po_lc', 'pw_lc', 'pg_lc', 'mo_lc', 'mw_lc', 'mg_lc',
                // Cumulative
                'cvo_lc', 'cvw_lc', 'cvg_lc', 'cvo_sc', 'cvw_sc', 'cvg_sc',
                // PVT
                'bo', 'bw', 'bg', 'z_factor', 'rst',
                // Pressure/Temperature
                'pressure', 'temperature', 'dp', 'pl', 'tl',
                'pressure_avg', 'pressure_max', 'pressure_min',
                'temperature_avg', 'temperature_max', 'temperature_min',
                // Flow weighted
                'qo_flow_weighted', 'qw_flow_weighted', 'qg_flow_weighted',
                // Ratios SC
                'bsw_sc', 'wlr_sc', 'gor_sc',
                // Gas breakdown
                'h2s_mfr', 'co2_mfr', 'n2_mfr',
                // Choke
                'choke_size', 'choke_dp',
                // Duration
                'test_duration_hours', 'test_stable_duration_hours', 'test_total_data_points',
                // Advanced
                'gamma_reading', 'detector_temp',
                // Quality
                'data_quality_score', 'missing_data_percentage',
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('mpfm_tests', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Drop JSON columns
            $jsonColumns = ['detailed_metrics', 'flow_rate_metrics', 'pvt_metrics',
                          'pressure_temperature_metrics', 'ratio_metrics', 'cumulative_metrics',
                          'data_quality_metrics'];
            foreach ($jsonColumns as $column) {
                if (Schema::hasColumn('mpfm_tests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
