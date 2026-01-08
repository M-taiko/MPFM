<?php

namespace App\Services;

use App\Models\MPFMPlan;
use App\Models\MPFMStep;
use App\Models\MPFMReading;
use Illuminate\Support\Facades\DB;

class MPFMPlanService
{
    /**
     * Create a new MPFM plan with steps and readings
     */
    public function createPlan($wellTestId, $planData)
    {
        DB::beginTransaction();
        
        try {
            // Create the plan
            $plan = MPFMPlan::create([
                'well_test_id' => $wellTestId,
                'name' => $planData['name'] ?? 'Default Plan',
                'description' => $planData['description'] ?? '',
                'planned_oil_rate' => $planData['planned_oil_rate'] ?? null,
                'planned_gas_rate' => $planData['planned_gas_rate'] ?? null,
                'planned_water_rate' => $planData['planned_water_rate'] ?? null,
                'expected_gor' => $planData['expected_gor'] ?? null,
                'expected_bsw' => $planData['expected_bsw'] ?? null,
                'expected_choke' => $planData['expected_choke'] ?? null,
                'expected_duration' => $planData['expected_duration'] ?? null,
                'test_objective' => $planData['test_objective'] ?? null,
                'settings' => $planData['settings'] ?? [],
            ]);

            // Create steps
            if (isset($planData['steps']) && is_array($planData['steps'])) {
                foreach ($planData['steps'] as $stepData) {
                    $this->createStep($plan->id, $stepData);
                }
            }

            DB::commit();
            return $plan;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Create a step for the plan
     */
    public function createStep($planId, $stepData)
    {
        return MPFMStep::create([
            'plan_id' => $planId,
            'step_number' => $stepData['step_number'] ?? 1,
            'name' => $stepData['name'] ?? 'Step ' . ($stepData['step_number'] ?? 1),
            'choke' => $stepData['choke'] ?? null,
            'time_window' => $stepData['time_window'] ?? null,
            'planned_rates' => $stepData['planned_rates'] ?? [],
            'description' => $stepData['description'] ?? '',
            'settings' => $stepData['settings'] ?? [],
        ]);
    }

    /**
     * Add reading to a step
     */
    public function addReading($stepId, $readingData)
    {
        return MPFMReading::create([
            'step_id' => $stepId,
            'oil_rate' => $readingData['oil_rate'] ?? null,
            'gas_rate' => $readingData['gas_rate'] ?? null,
            'water_rate' => $readingData['water_rate'] ?? null,
            'wlr' => $readingData['wlr'] ?? null,
            'bsw' => $readingData['bsw'] ?? null,
            'pressure' => $readingData['pressure'] ?? null,
            'temperature' => $readingData['temperature'] ?? null,
            'reading_time' => $readingData['reading_time'] ?? now(),
            'notes' => $readingData['notes'] ?? '',
            'additional_data' => $readingData['additional_data'] ?? [],
        ]);
    }

    /**
     * Generate report based on plan vs actual readings
     */
    public function generateReport($planId)
    {
        $plan = MPFMPlan::with(['steps.readings'])->findOrFail($planId);
        
        $reportData = [
            'plan' => $plan,
            'deviations' => [],
            'kpi_summary' => [],
            'conclusions' => [],
        ];

        foreach ($plan->steps as $step) {
            $readings = $step->readings;
            
            if ($readings->count() > 0) {
                // Calculate averages for this step
                $avgOilRate = $readings->avg('oil_rate');
                $avgGasRate = $readings->avg('gas_rate');
                $avgWaterRate = $readings->avg('water_rate');
                
                // Compare with planned rates
                $plannedRates = $step->planned_rates ?? [];
                
                $deviation = [
                    'step_id' => $step->id,
                    'step_name' => $step->name,
                    'oil_rate_deviation' => $this->calculateDeviation($avgOilRate, $plannedRates['oil_rate'] ?? null),
                    'gas_rate_deviation' => $this->calculateDeviation($avgGasRate, $plannedRates['gas_rate'] ?? null),
                    'water_rate_deviation' => $this->calculateDeviation($avgWaterRate, $plannedRates['water_rate'] ?? null),
                    'readings_count' => $readings->count(),
                ];
                
                $reportData['deviations'][] = $deviation;
            }
        }

        // Calculate overall KPIs
        $reportData['kpi_summary'] = $this->calculateKPISummary($planId);
        $reportData['conclusions'] = $this->generateConclusions($reportData);

        return $reportData;
    }

    /**
     * Calculate deviation percentage
     */
    private function calculateDeviation($actual, $planned)
    {
        if ($planned == null || $planned == 0) {
            return null;
        }
        
        if ($actual == null) {
            return null;
        }
        
        return (($actual - $planned) / $planned) * 100;
    }

    /**
     * Calculate KPI summary
     */
    private function calculateKPISummary($planId)
    {
        $readings = MPFMReading::join('mpfm_steps', 'mpfm_readings.step_id', '=', 'mpfm_steps.id')
            ->where('mpfm_steps.plan_id', $planId)
            ->select('mpfm_readings.*')
            ->get();

        if ($readings->isEmpty()) {
            return [];
        }

        $kpiSummary = [
            'total_readings' => $readings->count(),
            'avg_oil_rate' => $readings->avg('oil_rate'),
            'avg_gas_rate' => $readings->avg('gas_rate'),
            'avg_water_rate' => $readings->avg('water_rate'),
            'avg_pressure' => $readings->avg('pressure'),
            'avg_temperature' => $readings->avg('temperature'),
            'max_pressure' => $readings->max('pressure'),
            'min_pressure' => $readings->min('pressure'),
            'max_temperature' => $readings->max('temperature'),
            'min_temperature' => $readings->min('temperature'),
        ];

        // Calculate GOR and other derived metrics
        $totalOil = $readings->sum('oil_rate');
        $totalGas = $readings->sum('gas_rate');
        
        if ($totalOil > 0) {
            $kpiSummary['overall_gor'] = $totalGas / $totalOil;
        }

        return $kpiSummary;
    }

    /**
     * Generate conclusions based on deviations and KPIs
     */
    private function generateConclusions($reportData)
    {
        $conclusions = [];
        
        $highDeviations = array_filter($reportData['deviations'], function($deviation) {
            return abs($deviation['oil_rate_deviation'] ?? 0) > 10 || 
                   abs($deviation['gas_rate_deviation'] ?? 0) > 10 ||
                   abs($deviation['water_rate_deviation'] ?? 0) > 10;
        });
        
        if (count($highDeviations) > 0) {
            $conclusions[] = 'Significant deviations (>10%) detected in ' . count($highDeviations) . ' steps. Review choke settings and well conditions.';
        } else {
            $conclusions[] = 'Test results are within acceptable deviation limits (<10%).';
        }
        
        if (isset($reportData['kpi_summary']['avg_pressure']) && $reportData['kpi_summary']['avg_pressure'] > 1000) {
            $conclusions[] = 'Average pressure is high, indicating good reservoir energy.';
        }
        
        if (isset($reportData['kpi_summary']['avg_temperature']) && $reportData['kpi_summary']['avg_temperature'] > 150) {
            $conclusions[] = 'High temperature readings may indicate thermal effects or reservoir conditions.';
        }

        return $conclusions;
    }

    /**
     * Update plan when readings change (for recalculation)
     */
    public function updatePlanCalculations($planId)
    {
        // This method would recalculate all derived metrics when readings are updated
        return $this->generateReport($planId);
    }
}