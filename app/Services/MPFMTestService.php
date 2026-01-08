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

        // GOR (Gas-Oil Ratio)
        if ($test->oil_rate > 0) {
            $kpis['gor'] = $test->gas_rate / $test->oil_rate;
        } else {
            $kpis['gor'] = 0;
        }

        // WLR (Water-Liquid Ratio) - if we have water rate
        $totalLiquid = $test->oil_rate + ($test->water_cut ? ($test->oil_rate * $test->water_cut / 100) : 0);
        if ($totalLiquid > 0) {
            $waterRate = $test->oil_rate * ($test->water_cut / 100);
            $kpis['wlr'] = $waterRate / $totalLiquid;
        } else {
            $kpis['wlr'] = 0;
        }

        // BSW (Basic Sediment and Water) - same as water_cut percentage
        $kpis['bsw'] = $test->water_cut ?? 0;

        // Total production rate
        $kpis['total_production_rate'] = $test->oil_rate + $test->gas_rate + ($test->oil_rate * ($test->water_cut / 100));

        // Efficiency metrics
        if ($test->pressure && $test->oil_rate > 0) {
            $kpis['productivity_index'] = $test->oil_rate / $test->pressure;
        } else {
            $kpis['productivity_index'] = 0;
        }

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

