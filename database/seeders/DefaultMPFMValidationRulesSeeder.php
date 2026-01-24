<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MPFMValidationRule;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;

class DefaultMPFMValidationRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing oil fields, towers, and strings for context
        // Rules can be created globally or scoped to specific assets

        $rules = [
            // ===== OIL RATE VALIDATION =====
            [
                'rule_name' => 'Oil Rate Range Check',
                'rule_type' => 'range',
                'parameter' => 'oil_rate',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'min' => 50,      // Minimum oil rate in bbl/day
                    'max' => 5000,    // Maximum oil rate in bbl/day
                ],
                'severity' => 'critical',  // High priority - blocks approval
                'auto_action' => 'block',
                'is_active' => true,
                'created_by' => null,
            ],

            // ===== WATER CUT VALIDATION =====
            [
                'rule_name' => 'Water Cut Range Check',
                'rule_type' => 'range',
                'parameter' => 'water_cut',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'min' => 0,       // Minimum water cut percentage
                    'max' => 100,     // Maximum water cut percentage
                ],
                'severity' => 'major',
                'auto_action' => 'warn',
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'rule_name' => 'Water Cut Change Limit',
                'rule_type' => 'percentage_change',
                'parameter' => 'water_cut',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'max_increase_percent' => 15,     // Cannot increase by more than 15%
                    'max_decrease_percent' => 10,     // Cannot decrease by more than 10%
                    'lookback_days' => 90,            // Look at last 90 days
                    'min_historical_tests' => 5,      // Need at least 5 historical tests
                ],
                'severity' => 'major',
                'auto_action' => 'warn',
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'rule_name' => 'Water Cut Anomaly Detection',
                'rule_type' => 'anomaly',
                'parameter' => 'water_cut',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'z_score_threshold' => 2.0,       // Flag if >2 std dev from mean
                    'lookback_days' => 90,
                    'min_tests' => 8,
                ],
                'severity' => 'critical',
                'auto_action' => 'block',
                'is_active' => true,
                'created_by' => null,
            ],

            // ===== GOR (Gas-Oil Ratio) VALIDATION =====
            [
                'rule_name' => 'GOR Range Check',
                'rule_type' => 'range',
                'parameter' => 'gor',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'min' => 50,      // Minimum GOR in scf/bbl
                    'max' => 2000,    // Maximum GOR in scf/bbl
                ],
                'severity' => 'major',
                'auto_action' => 'warn',
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'rule_name' => 'GOR Anomaly Detection',
                'rule_type' => 'anomaly',
                'parameter' => 'gor',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'z_score_threshold' => 2.0,
                    'lookback_days' => 90,
                    'min_tests' => 8,
                ],
                'severity' => 'major',
                'auto_action' => 'warn',
                'is_active' => true,
                'created_by' => null,
            ],

            // ===== PRESSURE VALIDATION =====
            [
                'rule_name' => 'Pressure Range Check',
                'rule_type' => 'range',
                'parameter' => 'pressure',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'min' => 300,     // Minimum pressure in psi
                    'max' => 3500,    // Maximum pressure in psi
                ],
                'severity' => 'minor',
                'auto_action' => 'info',
                'is_active' => true,
                'created_by' => null,
            ],

            // ===== TEMPERATURE VALIDATION =====
            [
                'rule_name' => 'Temperature Range Check',
                'rule_type' => 'range',
                'parameter' => 'temperature',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'min' => 80,      // Minimum temperature in °F
                    'max' => 280,     // Maximum temperature in °F
                ],
                'severity' => 'minor',
                'auto_action' => 'info',
                'is_active' => true,
                'created_by' => null,
            ],

            // ===== GAS RATE VALIDATION =====
            [
                'rule_name' => 'Gas Rate Range Check',
                'rule_type' => 'range',
                'parameter' => 'gas_rate',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'min' => 0,
                    'max' => 100,     // Maximum gas rate in MMSCF/day
                ],
                'severity' => 'major',
                'auto_action' => 'warn',
                'is_active' => true,
                'created_by' => null,
            ],

            // ===== TREND VALIDATION =====
            [
                'rule_name' => 'Oil Rate Trend Detection',
                'rule_type' => 'trend',
                'parameter' => 'oil_rate',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'lookback_days' => 180,           // Look at last 180 days
                    'slope_threshold' => 10,          // Alert if slope > 10 bbl/day per test
                    'r_squared_threshold' => 0.7,     // Need high correlation
                    'min_tests' => 10,
                ],
                'severity' => 'minor',
                'auto_action' => 'info',
                'is_active' => false,  // Disabled by default - can be enabled per field
                'created_by' => null,
            ],

            [
                'rule_name' => 'Water Cut Trend Detection',
                'rule_type' => 'trend',
                'parameter' => 'water_cut',
                'oil_field_id' => null,
                'tower_id' => null,
                'oil_string_id' => null,
                'thresholds' => [
                    'lookback_days' => 180,
                    'slope_threshold' => 0.5,         // Alert if water cut increasing by >0.5% per test
                    'r_squared_threshold' => 0.6,
                    'min_tests' => 10,
                ],
                'severity' => 'major',
                'auto_action' => 'warn',
                'is_active' => false,
                'created_by' => null,
            ],
        ];

        // Insert rules into database
        foreach ($rules as $ruleData) {
            // Check if rule already exists to avoid duplicates
            $existing = MPFMValidationRule::where('rule_name', $ruleData['rule_name'])
                ->where('oil_field_id', $ruleData['oil_field_id'])
                ->where('tower_id', $ruleData['tower_id'])
                ->where('oil_string_id', $ruleData['oil_string_id'])
                ->first();

            if (!$existing) {
                MPFMValidationRule::create($ruleData);
                $this->command->info('Created rule: ' . $ruleData['rule_name']);
            } else {
                $this->command->warn('Rule already exists: ' . $ruleData['rule_name']);
            }
        }

        $this->command->info('Default MPFM validation rules seeded successfully!');
    }
}
