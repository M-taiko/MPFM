<?php

namespace App\Services;

class DepartmentConfig
{
    /**
     * Full configuration for each department.
     * Each config has:
     *   - label, color, icon
     *   - fields: shown in Create Task form (dynamic section)
     *   - completion_fields: shown in the completion form
     *   - dashboard_stats: which KPIs to show on dashboard
     */
    private static array $config = [

        // ─────────────────────────────────────────────
        'MPFM' => [
            'label'       => 'MPFM Operations',
            'color'       => '#2563eb',
            'bg'          => '#eff6ff',
            'icon'        => 'fa-gauge-high',
            'description' => 'Multi-Phase Flow Meter testing and calibration',
            // Fields shown when creating/editing a task for this department
            'fields' => [
                [
                    'key'      => 'mpfm_type',
                    'label'    => 'MPFM Device Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Phase Watcher 1', 'Phase Watcher 2', 'VX Spectra', 'Other'],
                ],
                [
                    'key'      => 'electrification_status',
                    'label'    => 'Electrification Status',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Electrified', 'Non-Electrified'],
                ],
                [
                    'key'      => 'dhsv_type',
                    'label'    => 'DHSV Type',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. Hydraulic, Electric',
                ],
                [
                    'key'      => 'flow_route',
                    'label'    => 'Flow Route',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. Main, Test',
                ],
                [
                    'key'      => 'reservoir_layer',
                    'label'    => 'Reservoir Layer',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. Layer A, Layer B',
                ],
                [
                    'key'      => 'required_whfp',
                    'label'    => 'Required WHFP (psi)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
            ],
            // Fields shown in the completion form (after task starts)
            'completion_fields' => [], // MPFM uses existing well test upload flow
            'completion_type'   => 'mpfm_upload', // special flag → redirect to MPFM upload
        ],

        // ─────────────────────────────────────────────
        'WIRELINE' => [
            'label'       => 'Wire Line Services',
            'color'       => '#7c3aed',
            'bg'          => '#faf5ff',
            'icon'        => 'fa-circle-nodes',
            'description' => 'Wireline logging, perforating, and intervention operations',
            'fields' => [
                [
                    'key'      => 'operation_type',
                    'label'    => 'Operation Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Logging', 'Perforating', 'Plug Setting', 'Fishing', 'Casing Inspection', 'Pressure Testing', 'Other'],
                ],
                [
                    'key'      => 'tool_string',
                    'label'    => 'Tool String',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. GR + CCL + CBL',
                ],
                [
                    'key'      => 'depth_from',
                    'label'    => 'Depth From (m)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'depth_to',
                    'label'    => 'Depth To (m)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'run_number',
                    'label'    => 'Run Number',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '1',
                ],
            ],
            'completion_fields' => [
                [
                    'key'      => 'operation_type',
                    'label'    => 'Operation Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Logging', 'Perforating', 'Plug Setting', 'Fishing', 'Casing Inspection', 'Pressure Testing', 'Other'],
                ],
                [
                    'key'      => 'tool_string',
                    'label'    => 'Tool String Used',
                    'type'     => 'text',
                    'required' => true,
                    'placeholder' => 'e.g. GR + CCL + CBL',
                ],
                [
                    'key'      => 'depth_from',
                    'label'    => 'Depth From (m)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'depth_to',
                    'label'    => 'Depth To (m)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'run_number',
                    'label'    => 'Run Number',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '1',
                ],
                [
                    'key'      => 'result',
                    'label'    => 'Operation Result',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Success', 'Partial Success', 'Failed', 'Inconclusive'],
                ],
                [
                    'key'      => 'issues_encountered',
                    'label'    => 'Issues / Remarks',
                    'type'     => 'textarea',
                    'required' => false,
                    'placeholder' => 'Describe any issues or observations...',
                ],
            ],
            'completion_type' => 'form',
        ],

        // ─────────────────────────────────────────────
        'PRODUCTION' => [
            'label'       => 'Production Operations',
            'color'       => '#059669',
            'bg'          => '#f0fdf4',
            'icon'        => 'fa-oil-well',
            'description' => 'Daily production monitoring and operations',
            'fields' => [
                [
                    'key'      => 'choke_size',
                    'label'    => 'Choke Size (64ths inch)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => 'e.g. 32',
                ],
                [
                    'key'      => 'whfp',
                    'label'    => 'WHFP (psi)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'separator_pressure',
                    'label'    => 'Separator Pressure (psi)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
            ],
            'completion_fields' => [
                [
                    'key'      => 'choke_size',
                    'label'    => 'Choke Size (64ths inch)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => 'e.g. 32',
                ],
                [
                    'key'      => 'whfp',
                    'label'    => 'WHFP (psi)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'oil_rate',
                    'label'    => 'Oil Rate (STB/d)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'gas_rate',
                    'label'    => 'Gas Rate (MMSCF/d)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'water_cut',
                    'label'    => 'Water Cut (%)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'separator_pressure',
                    'label'    => 'Separator Pressure (psi)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'separator_temperature',
                    'label'    => 'Separator Temperature (°C)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'remarks',
                    'label'    => 'Remarks',
                    'type'     => 'textarea',
                    'required' => false,
                    'placeholder' => 'Operational notes...',
                ],
            ],
            'completion_type' => 'form',
        ],

        // ─────────────────────────────────────────────
        'DRILLING' => [
            'label'       => 'Drilling Services',
            'color'       => '#d97706',
            'bg'          => '#fffbeb',
            'icon'        => 'fa-drill',
            'description' => 'Well drilling and borehole operations',
            'fields' => [
                [
                    'key'      => 'drilling_phase',
                    'label'    => 'Drilling Phase',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Surface', 'Intermediate', 'Production', 'Completion'],
                ],
                [
                    'key'      => 'bit_size',
                    'label'    => 'Bit Size (inches)',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. 12.25',
                ],
                [
                    'key'      => 'depth_from',
                    'label'    => 'Depth From (m)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'depth_to',
                    'label'    => 'Depth To (m)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
            ],
            'completion_fields' => [
                [
                    'key'      => 'drilling_phase',
                    'label'    => 'Drilling Phase',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Surface', 'Intermediate', 'Production', 'Completion'],
                ],
                [
                    'key'      => 'bit_size',
                    'label'    => 'Bit Size (inches)',
                    'type'     => 'text',
                    'required' => true,
                    'placeholder' => 'e.g. 12.25',
                ],
                [
                    'key'      => 'bit_type',
                    'label'    => 'Bit Type',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. PDC, Tricone',
                ],
                [
                    'key'      => 'mud_weight',
                    'label'    => 'Mud Weight (ppg)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'mud_type',
                    'label'    => 'Mud Type',
                    'type'     => 'select',
                    'required' => false,
                    'options'  => ['Water Based', 'Oil Based', 'Synthetic', 'Air/Foam'],
                ],
                [
                    'key'      => 'depth_from',
                    'label'    => 'Depth From (m)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'depth_to',
                    'label'    => 'Depth To (m)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'rop',
                    'label'    => 'ROP - Rate of Penetration (m/hr)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'remarks',
                    'label'    => 'Remarks',
                    'type'     => 'textarea',
                    'required' => false,
                    'placeholder' => 'Drilling notes...',
                ],
            ],
            'completion_type' => 'form',
        ],

        // ─────────────────────────────────────────────
        'WELL_COMPLETION' => [
            'label'       => 'Well Completion',
            'color'       => '#0891b2',
            'bg'          => '#ecfeff',
            'icon'        => 'fa-pipe-valve',
            'description' => 'Well completion and perforation operations',
            'fields' => [
                [
                    'key'      => 'operation_type',
                    'label'    => 'Completion Operation Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Perforation', 'Gravel Pack', 'Acidizing', 'Cementing', 'Sand Control', 'Other'],
                ],
                [
                    'key'      => 'completion_string_type',
                    'label'    => 'Completion String Type',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. Single String, Dual String',
                ],
                [
                    'key'      => 'depth_from',
                    'label'    => 'Depth From (m)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'depth_to',
                    'label'    => 'Depth To (m)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
            ],
            'completion_fields' => [
                [
                    'key'      => 'operation_type',
                    'label'    => 'Completion Operation Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Perforation', 'Gravel Pack', 'Acidizing', 'Cementing', 'Sand Control', 'Other'],
                ],
                [
                    'key'      => 'completion_string_type',
                    'label'    => 'Completion String Type',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. Single String, Dual String',
                ],
                [
                    'key'      => 'packer_type',
                    'label'    => 'Packer Type',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. Retrievable, Permanent',
                ],
                [
                    'key'      => 'depth_from',
                    'label'    => 'Depth From (m)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'depth_to',
                    'label'    => 'Depth To (m)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'result',
                    'label'    => 'Operation Result',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Successful', 'Partial', 'Failed', 'Postponed'],
                ],
                [
                    'key'      => 'remarks',
                    'label'    => 'Remarks',
                    'type'     => 'textarea',
                    'required' => false,
                    'placeholder' => 'Completion notes...',
                ],
            ],
            'completion_type' => 'form',
        ],

        // ─────────────────────────────────────────────
        'WORKOVER' => [
            'label'       => 'Workover Services',
            'color'       => '#dc2626',
            'bg'          => '#fef2f2',
            'icon'        => 'fa-screwdriver-wrench',
            'description' => 'Well workover and intervention operations',
            'fields' => [
                [
                    'key'      => 'operation_type',
                    'label'    => 'Workover Operation Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Pump Change', 'Tubing Replacement', 'Fishing', 'Scale Removal', 'Sand Cleanout', 'Stimulation', 'Other'],
                ],
                [
                    'key'      => 'rig_id',
                    'label'    => 'Workover Rig ID',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. WO-Rig-01',
                ],
                [
                    'key'      => 'depth',
                    'label'    => 'Working Depth (m)',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
            ],
            'completion_fields' => [
                [
                    'key'      => 'operation_type',
                    'label'    => 'Workover Operation Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Pump Change', 'Tubing Replacement', 'Fishing', 'Scale Removal', 'Sand Cleanout', 'Stimulation', 'Other'],
                ],
                [
                    'key'      => 'rig_id',
                    'label'    => 'Workover Rig ID',
                    'type'     => 'text',
                    'required' => true,
                    'placeholder' => 'e.g. WO-Rig-01',
                ],
                [
                    'key'      => 'depth',
                    'label'    => 'Working Depth (m)',
                    'type'     => 'number',
                    'required' => true,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'crew_count',
                    'label'    => 'Crew Count',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'result',
                    'label'    => 'Operation Result',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Successful', 'Partial', 'Failed', 'Ongoing'],
                ],
                [
                    'key'      => 'equipment_used',
                    'label'    => 'Equipment Used',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'List main equipment used',
                ],
                [
                    'key'      => 'remarks',
                    'label'    => 'Remarks',
                    'type'     => 'textarea',
                    'required' => false,
                    'placeholder' => 'Workover notes...',
                ],
            ],
            'completion_type' => 'form',
        ],

        // ─────────────────────────────────────────────
        'MAINTENANCE' => [
            'label'       => 'Maintenance Services',
            'color'       => '#64748b',
            'bg'          => '#f8fafc',
            'icon'        => 'fa-gear',
            'description' => 'Preventive and corrective equipment maintenance',
            'fields' => [
                [
                    'key'      => 'equipment_type',
                    'label'    => 'Equipment Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['MPFM', 'Separator', 'Pump', 'Valve', 'Compressor', 'Pipeline', 'Electrical', 'Instrumentation', 'Other'],
                ],
                [
                    'key'      => 'maintenance_type',
                    'label'    => 'Maintenance Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Preventive', 'Corrective', 'Emergency', 'Shutdown', 'Inspection'],
                ],
                [
                    'key'      => 'work_order_number',
                    'label'    => 'Work Order Number',
                    'type'     => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. WO-2026-001',
                ],
            ],
            'completion_fields' => [
                [
                    'key'      => 'equipment_type',
                    'label'    => 'Equipment Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['MPFM', 'Separator', 'Pump', 'Valve', 'Compressor', 'Pipeline', 'Electrical', 'Instrumentation', 'Other'],
                ],
                [
                    'key'      => 'maintenance_type',
                    'label'    => 'Maintenance Type',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Preventive', 'Corrective', 'Emergency', 'Shutdown', 'Inspection'],
                ],
                [
                    'key'      => 'work_order_number',
                    'label'    => 'Work Order Number',
                    'type'     => 'text',
                    'required' => true,
                    'placeholder' => 'e.g. WO-2026-001',
                ],
                [
                    'key'      => 'parts_used',
                    'label'    => 'Parts / Materials Used',
                    'type'     => 'textarea',
                    'required' => false,
                    'placeholder' => 'List parts and quantities...',
                ],
                [
                    'key'      => 'labor_hours',
                    'label'    => 'Labor Hours',
                    'type'     => 'number',
                    'required' => false,
                    'placeholder' => '0',
                ],
                [
                    'key'      => 'next_maintenance_date',
                    'label'    => 'Next Maintenance Date',
                    'type'     => 'date',
                    'required' => false,
                ],
                [
                    'key'      => 'result',
                    'label'    => 'Maintenance Result',
                    'type'     => 'select',
                    'required' => true,
                    'options'  => ['Completed', 'Partially Completed', 'Deferred', 'Requires Follow-up'],
                ],
                [
                    'key'      => 'remarks',
                    'label'    => 'Remarks',
                    'type'     => 'textarea',
                    'required' => false,
                    'placeholder' => 'Maintenance notes...',
                ],
            ],
            'completion_type' => 'form',
        ],
    ];

    public static function all(): array
    {
        return self::$config;
    }

    public static function get(string $code): ?array
    {
        return self::$config[strtoupper($code)] ?? null;
    }

    public static function getFields(string $code): array
    {
        return self::$config[strtoupper($code)]['fields'] ?? [];
    }

    public static function getCompletionFields(string $code): array
    {
        return self::$config[strtoupper($code)]['completion_fields'] ?? [];
    }

    public static function getCompletionType(string $code): string
    {
        return self::$config[strtoupper($code)]['completion_type'] ?? 'form';
    }

    public static function getLabel(string $code): string
    {
        return self::$config[strtoupper($code)]['label'] ?? $code;
    }

    public static function getColor(string $code): string
    {
        return self::$config[strtoupper($code)]['color'] ?? '#64748b';
    }

    public static function codes(): array
    {
        return array_keys(self::$config);
    }

    /** Build validation rules from completion_fields config */
    public static function buildValidationRules(string $code): array
    {
        $rules = [];
        foreach (self::getCompletionFields($code) as $field) {
            $key = 'data.' . $field['key'];
            $rule = $field['required'] ? 'required' : 'nullable';

            if ($field['type'] === 'number') {
                $rule .= '|numeric';
            } elseif ($field['type'] === 'date') {
                $rule .= '|date';
            } elseif ($field['type'] === 'select') {
                $optionValues = array_map(fn($o) => strtolower(str_replace(' ', '_', $o)), $field['options']);
                // Don't strictly enforce select values to allow flexibility
            } else {
                $rule .= '|string|max:1000';
            }

            $rules[$key] = $rule;
        }
        return $rules;
    }
}
