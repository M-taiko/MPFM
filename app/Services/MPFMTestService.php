<?php

namespace App\Services;

use App\Models\MPFMTest;
use Illuminate\Support\Facades\DB;

class MPFMTestService
{
    /**
     * Calculate KPIs for an MPFM test
     */
    public function calculateKPIs(MPFMTest $test): array
    {
        $kpis = [];

        // Use standard condition rates (SC) if available, fallback to liquid conditions (LC)
        $oilRate = $test->qo_sc ?? $test->qo_lc ?? $test->oil_rate ?? 0;
        $gasRate = $test->qg_sc ?? $test->qg_lc ?? $test->gas_rate ?? 0;
        $waterRate = $test->qw_sc ?? $test->qw_lc ?? 0;

        // Calculate water cut if not directly available
        $waterCut = $test->water_cut ?? 0;
        if ($waterCut == 0 && $oilRate > 0 && $waterRate > 0) {
            $waterCut = ($waterRate / ($oilRate + $waterRate)) * 100;
        }

        // GOR (Gas-Oil Ratio): Gas Rate / Oil Rate
        if (is_numeric($oilRate) && $oilRate > 0) {
            $kpis['gor'] = round($gasRate / $oilRate, 4);
        } else {
            $kpis['gor'] = 0;
        }

        // WLR (Water-Liquid Ratio): Water Rate / (Oil Rate + Water Rate) * 100
        $totalLiquid = $oilRate + $waterRate;
        if (is_numeric($totalLiquid) && $totalLiquid > 0) {
            $kpis['wlr'] = round(($waterRate / $totalLiquid) * 100, 2);
        } else {
            $kpis['wlr'] = 0;
        }

        // BSW (Basic Sediment and Water) - percentage of water in total liquid
        if (is_numeric($totalLiquid) && $totalLiquid > 0) {
            $kpis['bsw'] = round(($waterRate / $totalLiquid) * 100, 2);
        } else {
            $kpis['bsw'] = $waterCut > 0 ? round($waterCut, 2) : 0;
        }

        // Total liquid rate (oil + water)
        $kpis['total_liquid_rate'] = round($totalLiquid, 2);

        // Total production rate (oil + water + gas)
        $kpis['total_production_rate'] = round($oilRate + $waterRate + $gasRate, 2);

        // GVF (Gas Volume Fraction): Gas / (Oil + Water + Gas) * 100
        $totalVolume = $oilRate + $waterRate + $gasRate;
        if (is_numeric($totalVolume) && $totalVolume > 0) {
            $kpis['gvf'] = round(($gasRate / $totalVolume) * 100, 2);
        } else {
            $kpis['gvf'] = 0;
        }

        // Productivity Index (PI): Oil Rate / Pressure differential
        $pressure = $test->pressure_avg ?? $test->pressure ?? 0;
        if (is_numeric($pressure) && $pressure > 0 && $oilRate > 0) {
            $kpis['productivity_index'] = round($oilRate / $pressure, 6);
        } else {
            $kpis['productivity_index'] = 0;
        }

        // Add density fields from database (Line Conditions - LC)
        $kpis['po'] = $test->po_lc ?? 0;  // Oil density (kg/m³)
        $kpis['pw'] = $test->pw_lc ?? 0;  // Water density (kg/m³)
        $kpis['pg'] = $test->pg_lc ?? 0;  // Gas density (kg/m³)

        // Add mass flow rates (kg/d)
        $kpis['mo'] = $test->mo_lc ?? 0;  // Oil mass flow (kg/d)
        $kpis['mw'] = $test->mw_lc ?? 0;  // Water mass flow (kg/d)
        $kpis['mg'] = $test->mg_lc ?? 0;  // Gas mass flow (kg/d)

        // Add formation volume factors (FVF)
        $kpis['bo'] = $test->bo ?? 0;  // Oil FVF (Rm³/Sm³)
        $kpis['bw'] = $test->bw ?? 0;  // Water FVF (Rm³/Sm³)
        $kpis['bg'] = $test->bg ?? 0;  // Gas FVF (Rm³/Sm³)

        // Add PVT properties
        $kpis['z_factor'] = $test->z_factor ?? 0;  // Gas compressibility factor (dimensionless)
        $kpis['rst'] = $test->rst ?? 0;  // Solution GOR (Scf/STB)

        // Add water rate if available
        $kpis['water_rate'] = $waterRate > 0 ? round($waterRate, 2) : 0;

        // Add source field names for debugging
        $kpis['source_fields'] = [
            'oil_rate_source' => !empty($test->qo_sc) ? 'qo_sc' : (!empty($test->qo_lc) ? 'qo_lc' : (!empty($test->oil_rate) ? 'oil_rate' : 'missing')),
            'gas_rate_source' => !empty($test->qg_sc) ? 'qg_sc' : (!empty($test->qg_lc) ? 'qg_lc' : (!empty($test->gas_rate) ? 'gas_rate' : 'missing')),
            'water_rate_source' => !empty($test->qw_sc) ? 'qw_sc' : (!empty($test->qw_lc) ? 'qw_lc' : 'missing'),
            'pressure_source' => !empty($test->pressure_avg) ? 'pressure_avg' : (!empty($test->pressure) ? 'pressure' : 'missing'),
            'density_source' => 'database fields (po_lc, pw_lc, pg_lc)',
            'mass_flow_source' => 'database fields (mo_lc, mw_lc, mg_lc)',
        ];

        return $kpis;
    }

    /**
     * Create or update MPFM test with calculated KPIs
     */
    public function createOrUpdate(array $data, ?MPFMTest $test = null): MPFMTest
    {
        DB::beginTransaction();

        try {
            if ($test) {
                $test->update($data);
                $test->refresh();
            } else {
                $test = MPFMTest::create($data);
            }

            // Calculate and store KPIs
            $kpis = $this->calculateKPIs($test);
            $test->update(['calculated_kpis' => $kpis]);

            DB::commit();
            return $test->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate operator access to create test for given assets
     */
    public function validateOperatorAccess($user, $oilFieldId, $towerId, $oilStringId): bool
    {
        // Admins and supervisors have full access
        if ($user->hasAnyRole(['admin', 'mpfm_supervisor'])) {
            return true;
        }

        // Operators can only create tests for assets assigned to them
        if ($user->hasRole('mpfm_operator')) {
            $hasAccess = \App\Models\MPFMOperationalPlan::where('assigned_user_id', $user->id)
                ->where('oil_field_id', $oilFieldId)
                ->where('tower_id', $towerId)
                ->where(function ($query) use ($oilStringId) {
                    $query->whereJsonContains('assigned_oil_string_ids', $oilStringId);
                })
                ->where('status', 'active')
                ->exists();

            return $hasAccess;
        }

        return false;
    }
}

