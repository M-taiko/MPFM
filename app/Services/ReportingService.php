<?php

namespace App\Services;

use App\Models\MPFMTest;
use App\Models\MPFMOperationalPlan;
use App\Models\User;
use App\Models\Tower;
use App\Models\OilField;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * Get operator performance report
     */
    public function getOperatorPerformanceReport($startDate = null, $endDate = null, $userId = null)
    {
        $query = MPFMTest::with(['user', 'oilField', 'tower', 'oilString']);

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $tests = $query->get();

        $operators = $tests->groupBy('user_id')->map(function ($operatorTests, $userId) {
            $operator = $operatorTests->first()->user;
            $totalTests = $operatorTests->count();
            
            $totalOilRate = $operatorTests->sum('oil_rate');
            $totalGasRate = $operatorTests->sum('gas_rate');
            $avgWaterCut = $operatorTests->avg('water_cut');
            
            $avgGOR = $operatorTests->map(function ($test) {
                $kpis = $test->calculated_kpis ?? [];
                return $kpis['gor'] ?? 0;
            })->filter()->avg();

            return [
                'operator_id' => $userId,
                'operator_name' => $operator->name ?? 'Unknown',
                'total_tests' => $totalTests,
                'total_oil_rate' => round($totalOilRate, 2),
                'total_gas_rate' => round($totalGasRate, 2),
                'avg_water_cut' => round($avgWaterCut, 2),
                'avg_gor' => round($avgGOR, 2),
            ];
        })->values();

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'operators' => $operators,
            'summary' => [
                'total_operators' => $operators->count(),
                'total_tests' => $tests->count(),
                'total_oil_rate' => round($tests->sum('oil_rate'), 2),
                'total_gas_rate' => round($tests->sum('gas_rate'), 2),
            ],
        ];
    }

    /**
     * Get MPFM tests count report
     */
    public function getMPFMTestsCountReport($startDate = null, $endDate = null, $groupBy = 'date')
    {
        $query = MPFMTest::query();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $tests = $query->get();

        $grouped = match($groupBy) {
            'date' => $tests->groupBy(function ($test) {
                return $test->test_date->format('Y-m-d');
            }),
            'shift' => $tests->groupBy('shift'),
            'tower' => $tests->groupBy('tower_id'),
            'oil_field' => $tests->groupBy('oil_field_id'),
            default => $tests->groupBy(function ($test) {
                return $test->test_date->format('Y-m-d');
            }),
        };

        $report = $grouped->map(function ($groupTests, $key) use ($groupBy) {
            return [
                'group_key' => $key,
                'count' => $groupTests->count(),
                'total_oil_rate' => round($groupTests->sum('oil_rate'), 2),
                'total_gas_rate' => round($groupTests->sum('gas_rate'), 2),
                'avg_water_cut' => round($groupTests->avg('water_cut'), 2),
            ];
        })->values();

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'group_by' => $groupBy,
            'data' => $report,
            'summary' => [
                'total_tests' => $tests->count(),
                'total_oil_rate' => round($tests->sum('oil_rate'), 2),
                'total_gas_rate' => round($tests->sum('gas_rate'), 2),
            ],
        ];
    }

    /**
     * Get tower efficiency report
     */
    public function getTowerEfficiencyReport($startDate = null, $endDate = null, $towerId = null)
    {
        $query = MPFMTest::with(['tower', 'oilString']);

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        if ($towerId) {
            $query->where('tower_id', $towerId);
        }

        $tests = $query->get();

        $towers = $tests->groupBy('tower_id')->map(function ($towerTests, $towerId) {
            $tower = $towerTests->first()->tower;
            $totalTests = $towerTests->count();
            
            $totalOilRate = $towerTests->sum('oil_rate');
            $totalGasRate = $towerTests->sum('gas_rate');
            $avgWaterCut = $towerTests->avg('water_cut');
            
            $avgPressure = $towerTests->avg('pressure');
            $avgTemperature = $towerTests->avg('temperature');

            // Calculate efficiency metrics
            $avgGOR = $towerTests->map(function ($test) {
                $kpis = $test->calculated_kpis ?? [];
                return $kpis['gor'] ?? 0;
            })->filter()->avg();

            $stringsCount = $towerTests->pluck('oil_string_id')->unique()->count();

            return [
                'tower_id' => $towerId,
                'tower_name' => $tower->name ?? 'Unknown',
                'tower_code' => $tower->code ?? '-',
                'total_tests' => $totalTests,
                'strings_count' => $stringsCount,
                'total_oil_rate' => round($totalOilRate, 2),
                'total_gas_rate' => round($totalGasRate, 2),
                'avg_water_cut' => round($avgWaterCut, 2),
                'avg_gor' => round($avgGOR, 2),
                'avg_pressure' => round($avgPressure, 2),
                'avg_temperature' => round($avgTemperature, 2),
            ];
        })->values();

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'towers' => $towers,
            'summary' => [
                'total_towers' => $towers->count(),
                'total_tests' => $tests->count(),
            ],
        ];
    }

    /**
     * Get field production summary report
     */
    public function getFieldProductionSummaryReport($startDate = null, $endDate = null, $oilFieldId = null)
    {
        $query = MPFMTest::with(['oilField', 'tower']);

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        if ($oilFieldId) {
            $query->where('oil_field_id', $oilFieldId);
        }

        $tests = $query->get();

        $fields = $tests->groupBy('oil_field_id')->map(function ($fieldTests, $fieldId) {
            $field = $fieldTests->first()->oilField;
            $totalTests = $fieldTests->count();
            
            $totalOilRate = $fieldTests->sum('oil_rate');
            $totalGasRate = $fieldTests->sum('gas_rate');
            $avgWaterCut = $fieldTests->avg('water_cut');
            
            $towersCount = $fieldTests->pluck('tower_id')->unique()->count();
            $stringsCount = $fieldTests->pluck('oil_string_id')->unique()->count();

            return [
                'oil_field_id' => $fieldId,
                'oil_field_name' => $field->name ?? 'Unknown',
                'oil_field_code' => $field->code ?? '-',
                'total_tests' => $totalTests,
                'towers_count' => $towersCount,
                'strings_count' => $stringsCount,
                'total_oil_rate' => round($totalOilRate, 2),
                'total_gas_rate' => round($totalGasRate, 2),
                'avg_water_cut' => round($avgWaterCut, 2),
            ];
        })->values();

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'fields' => $fields,
            'summary' => [
                'total_fields' => $fields->count(),
                'total_tests' => $tests->count(),
                'grand_total_oil_rate' => round($tests->sum('oil_rate'), 2),
                'grand_total_gas_rate' => round($tests->sum('gas_rate'), 2),
            ],
        ];
    }

    /**
     * Get date and shift reports
     */
    public function getDateShiftReport($date = null, $shift = null)
    {
        $query = MPFMTest::with(['user', 'oilField', 'tower', 'oilString']);

        if ($date) {
            $query->whereDate('test_date', $date);
        }

        if ($shift) {
            $query->where('shift', $shift);
        }

        $tests = $query->get();

        $byShift = $tests->groupBy('shift')->map(function ($shiftTests, $shiftName) {
            return [
                'shift' => $shiftName,
                'count' => $shiftTests->count(),
                'total_oil_rate' => round($shiftTests->sum('oil_rate'), 2),
                'total_gas_rate' => round($shiftTests->sum('gas_rate'), 2),
                'avg_water_cut' => round($shiftTests->avg('water_cut'), 2),
            ];
        })->values();

        return [
            'date' => $date,
            'shift' => $shift,
            'by_shift' => $byShift,
            'summary' => [
                'total_tests' => $tests->count(),
                'total_oil_rate' => round($tests->sum('oil_rate'), 2),
                'total_gas_rate' => round($tests->sum('gas_rate'), 2),
            ],
            'tests' => $tests->map(function ($test) {
                return [
                    'id' => $test->id,
                    'test_date' => $test->test_date->format('Y-m-d'),
                    'shift' => $test->shift,
                    'operator' => $test->user->name ?? 'Unknown',
                    'oil_field' => $test->oilField->name ?? 'Unknown',
                    'tower' => $test->tower->name ?? 'Unknown',
                    'oil_string' => $test->oilString->name ?? 'Unknown',
                    'oil_rate' => $test->oil_rate,
                    'gas_rate' => $test->gas_rate,
                    'water_cut' => $test->water_cut,
                ];
            }),
        ];
    }
}

