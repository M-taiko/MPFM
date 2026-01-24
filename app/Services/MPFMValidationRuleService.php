<?php

namespace App\Services;

use App\Models\MPFMValidationRule;

class MPFMValidationRuleService
{
    public function createDefaultRules($oilStringId = null)
    {
        $defaultRules = [
            [
                'rule_name' => 'Oil Rate Range Check',
                'rule_type' => 'range',
                'parameter' => 'oil_rate',
                'thresholds' => ['min' => 100, 'max' => 5000],
                'severity' => 'critical',
            ],
            [
                'rule_name' => 'Water Cut Range Check',
                'rule_type' => 'range',
                'parameter' => 'water_cut',
                'thresholds' => ['min' => 0, 'max' => 100],
                'severity' => 'major',
            ],
            [
                'rule_name' => 'Water Cut Change Limit',
                'rule_type' => 'percentage_change',
                'parameter' => 'water_cut',
                'thresholds' => [
                    'max_increase_percent' => 15,
                    'max_decrease_percent' => 10,
                    'lookback_days' => 90,
                    'min_historical_tests' => 5,
                ],
                'severity' => 'major',
            ],
            [
                'rule_name' => 'GOR Range Check',
                'rule_type' => 'range',
                'parameter' => 'gor',
                'thresholds' => ['min' => 50, 'max' => 2000],
                'severity' => 'major',
            ],
            [
                'rule_name' => 'Pressure Range Check',
                'rule_type' => 'range',
                'parameter' => 'pressure',
                'thresholds' => ['min' => 500, 'max' => 3000],
                'severity' => 'minor',
            ],
            [
                'rule_name' => 'Temperature Range Check',
                'rule_type' => 'range',
                'parameter' => 'temperature',
                'thresholds' => ['min' => 100, 'max' => 250],
                'severity' => 'minor',
            ],
            [
                'rule_name' => 'Water Cut Anomaly Detection',
                'rule_type' => 'anomaly',
                'parameter' => 'water_cut',
                'thresholds' => [
                    'z_score_threshold' => 2.0,
                    'lookback_days' => 90,
                    'min_tests' => 8,
                ],
                'severity' => 'critical',
            ],
            [
                'rule_name' => 'GOR Anomaly Detection',
                'rule_type' => 'anomaly',
                'parameter' => 'gor',
                'thresholds' => [
                    'z_score_threshold' => 2.0,
                    'lookback_days' => 90,
                    'min_tests' => 8,
                ],
                'severity' => 'major',
            ],
        ];

        $created = [];
        foreach ($defaultRules as $ruleData) {
            if ($oilStringId) {
                $ruleData['oil_string_id'] = $oilStringId;
            }

            // Check if rule already exists
            $existing = MPFMValidationRule::where('rule_name', $ruleData['rule_name'])
                ->where('oil_string_id', $ruleData['oil_string_id'] ?? null)
                ->first();

            if (!$existing) {
                $created[] = MPFMValidationRule::create($ruleData);
            }
        }

        return $created;
    }

    public function cloneRules($fromStringId, $toStringId)
    {
        $rules = MPFMValidationRule::where('oil_string_id', $fromStringId)->get();

        $cloned = [];
        foreach ($rules as $rule) {
            $cloned[] = MPFMValidationRule::create([
                'rule_name' => $rule->rule_name,
                'rule_type' => $rule->rule_type,
                'parameter' => $rule->parameter,
                'oil_string_id' => $toStringId,
                'thresholds' => $rule->thresholds,
                'severity' => $rule->severity,
                'auto_action' => $rule->auto_action,
                'is_active' => $rule->is_active,
            ]);
        }

        return $cloned;
    }

    public function getApplicableRules($oilFieldId = null, $towerId = null, $stringId = null)
    {
        $query = MPFMValidationRule::active();

        if ($oilFieldId) {
            $query->where(function ($q) use ($oilFieldId) {
                $q->where('oil_field_id', $oilFieldId)
                    ->orWhere('oil_field_id', null);
            });
        }

        if ($towerId) {
            $query->where(function ($q) use ($towerId) {
                $q->where('tower_id', $towerId)
                    ->orWhere('tower_id', null);
            });
        }

        if ($stringId) {
            $query->where(function ($q) use ($stringId) {
                $q->where('oil_string_id', $stringId)
                    ->orWhere('oil_string_id', null);
            });
        }

        return $query->get()->sortByDesc(function ($rule) {
            return $rule->getSpecificityLevel();
        });
    }
}
