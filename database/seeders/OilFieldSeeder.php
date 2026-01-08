<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;

class OilFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates ADNOC-grade oil fields, towers, and oil strings
     */
    public function run(): void
    {
        // Onshore Fields
        $onshoreFields = [
            [
                'name' => 'ADNOC Onshore Field Alpha',
                'code' => 'ADNOC-ONS-ALPHA',
                'location' => 'Onshore, Abu Dhabi',
                'description' => 'Primary onshore production field',
                'towers' => [
                    ['name' => 'Tower A-1', 'code' => 'TWR-A1', 'description' => 'Main production tower'],
                    ['name' => 'Tower A-2', 'code' => 'TWR-A2', 'description' => 'Secondary production tower'],
                    ['name' => 'Tower A-3', 'code' => 'TWR-A3', 'description' => 'Tertiary production tower'],
                ],
            ],
            [
                'name' => 'ADNOC Onshore Field Beta',
                'code' => 'ADNOC-ONS-BETA',
                'location' => 'Onshore, Abu Dhabi',
                'description' => 'Secondary onshore production field',
                'towers' => [
                    ['name' => 'Tower B-1', 'code' => 'TWR-B1', 'description' => 'Beta field main tower'],
                    ['name' => 'Tower B-2', 'code' => 'TWR-B2', 'description' => 'Beta field secondary tower'],
                ],
            ],
        ];

        // Offshore Fields
        $offshoreFields = [
            [
                'name' => 'ADNOC Offshore Field Gamma',
                'code' => 'ADNOC-OFF-GAMMA',
                'location' => 'Offshore, Arabian Gulf',
                'description' => 'Primary offshore production field',
                'towers' => [
                    ['name' => 'Platform G-1', 'code' => 'PLT-G1', 'description' => 'Gamma platform main'],
                    ['name' => 'Platform G-2', 'code' => 'PLT-G2', 'description' => 'Gamma platform secondary'],
                ],
            ],
        ];

        $allFields = array_merge($onshoreFields, $offshoreFields);

        foreach ($allFields as $fieldData) {
            $oilField = OilField::firstOrCreate(
                ['code' => $fieldData['code']],
                [
                    'name' => $fieldData['name'],
                    'location' => $fieldData['location'],
                    'description' => $fieldData['description'],
                    'status' => 'active',
                ]
            );

            foreach ($fieldData['towers'] as $towerData) {
                $tower = Tower::firstOrCreate(
                    ['code' => $towerData['code']],
                    [
                        'oil_field_id' => $oilField->id,
                        'name' => $towerData['name'],
                        'description' => $towerData['description'],
                        'status' => 'active',
                    ]
                );

                // Create 2-4 oil strings per tower
                $stringCount = rand(2, 4);
                for ($i = 1; $i <= $stringCount; $i++) {
                    OilString::firstOrCreate(
                        [
                            'tower_id' => $tower->id,
                            'code' => $tower->code . '-STR-' . $i,
                        ],
                        [
                            'name' => 'String ' . $i,
                            'description' => 'Production string ' . $i . ' for ' . $tower->name,
                            'status' => 'active',
                        ]
                    );
                }
            }
        }

        $this->command->info('Oil fields, towers, and oil strings seeded successfully!');
    }
}

