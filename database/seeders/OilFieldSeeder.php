<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;

class OilFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            [
                'name' => 'Lower Zakum West Field',
                'code' => 'ZK',
                'location' => 'Offshore, Abu Dhabi',
                'tower_prefix' => 'zk',
                'tower_start' => 100,
                'tower_count' => 10,
            ],
            [
                'name' => 'Umm Shaif Field',
                'code' => 'UM',
                'location' => 'Offshore, Abu Dhabi',
                'tower_prefix' => 'um',
                'tower_start' => 100,
                'tower_count' => 10,
            ],
        ];

        foreach ($fields as $fieldData) {
            $oilField = OilField::firstOrCreate(
                ['code' => $fieldData['code']],
                [
                    'name' => $fieldData['name'],
                    'location' => $fieldData['location'],
                    'description' => '',
                    'status' => 'active',
                ]
            );

            // إنشاء التاورات
            for ($i = 0; $i < $fieldData['tower_count']; $i++) {
                $towerNumber = $fieldData['tower_start'] + $i;
                $towerCode = $fieldData['tower_prefix'] . '-' . $towerNumber;

                $tower = Tower::firstOrCreate(
                    ['code' => $towerCode],
                    [
                        'oil_field_id' => $oilField->id,
                        'name' => strtoupper($towerCode),
                        'description' => '',
                        'status' => 'active',
                    ]
                );

                // إنشاء 2 Strings لكل تاور (333/1 , 333/2)
                for ($s = 1; $s <= 2; $s++) {
                    OilString::firstOrCreate(
                        [
                            'tower_id' => $tower->id,
                            'code' => $tower->name.'/' . $s ,
                        ],
                        [
                            'name' => $tower->name.'/' . $s,
                            'description' => 'Production string ' . $s,
                            'status' => 'active',
                        ]
                    );
                }
            }
        }

        $this->command->info('Towers and oil strings seeded successfully!');
    }
}
