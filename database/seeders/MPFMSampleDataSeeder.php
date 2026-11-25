<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;
use App\Models\MPFMOperationalPlan;
use App\Models\MPFMTest;
use App\Services\MPFMTestService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MPFMSampleDataSeeder extends Seeder
{
    protected $testService;

    public function __construct()
    {
        $this->testService = app(MPFMTestService::class);
    }

    /**
     * Run the database seeds.
     * Creates realistic production-grade operational plans and MPFM tests
     */
    public function run(): void
    {
        // // Get users
        // $supervisor = User::where('email', 'supervisor@oilfield.com')->first();
        // $operators = User::role('mpfm_operator')->get();

        // if (!$supervisor || $operators->isEmpty()) {
        //     $this->command->warn('Supervisor or operators not found. Please run UserSeeder first.');
        //     return;
        // }

        // // Get active assets
        // $oilFields = OilField::active()->with(['towers.oilStrings' => function ($query) {
        //     $query->active();
        // }])->get();

        // if ($oilFields->isEmpty()) {
        //     $this->command->warn('No active oil fields found. Please run OilFieldSeeder first.');
        //     return;
        // }

        // // Create operational plans for the last 7 days and next 3 days
        // $startDate = Carbon::today()->subDays(7);
        // $endDate = Carbon::today()->addDays(3);

        // $planCount = 0;
        // $testCount = 0;

        // for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
        //     // Create 1-3 plans per day
        //     $plansPerDay = rand(1, 3);

        //     for ($p = 0; $p < $plansPerDay; $p++) {
        //         // Select random oil field and tower
        //         $oilField = $oilFields->random();
        //         $tower = $oilField->towers->where('status', 'active')->random();

        //         if (!$tower || $tower->oilStrings->where('status', 'active')->isEmpty()) {
        //             continue;
        //         }

        //         // Select random operator
        //         $operator = $operators->random();

        //         // Select 1-3 oil strings
        //         $availableStrings = $tower->oilStrings->where('status', 'active');
        //         $selectedStrings = $availableStrings->random(min(3, $availableStrings->count()));

        //         // Create operational plan
        //         $plan = MPFMOperationalPlan::firstOrCreate(
        //             [
        //                 'created_by' => $supervisor->id,
        //                 'assigned_user_id' => $operator->id,
        //                 'oil_field_id' => $oilField->id,
        //                 'tower_id' => $tower->id,
        //                 'plan_date' => $date->format('Y-m-d'),
        //                 'name' => 'Daily Plan - ' . $oilField->name . ' / ' . $tower->name . ' - ' . $date->format('Y-m-d'),
        //             ],
        //             [
        //                 'description' => 'Standard daily production testing plan',
        //                 'shift' => ['morning', 'afternoon', 'night'][rand(0, 2)],
        //                 'status' => $date->isPast() ? (rand(0, 1) ? 'completed' : 'active') : 'active',
        //                 'assigned_oil_string_ids' => $selectedStrings->pluck('id')->toArray(),
        //                 'notes' => 'Focus on maintaining optimal production rates',
        //             ]
        //         );

        //         $planCount++;

        //         // Create MPFM tests for this plan (if date is today or past)
        //         if ($date->lte(Carbon::today())) {
        //             $testsPerPlan = rand(1, 3);
        //             $shifts = ['morning', 'afternoon', 'night'];

        //             foreach ($selectedStrings as $oilString) {
        //                 for ($t = 0; $t < $testsPerPlan; $t++) {
        //                     $shift = $shifts[rand(0, 2)];

        //                     // Realistic production data ranges
        //                     $oilRate = rand(800, 2000) + (rand(0, 99) / 100);
        //                     $gasRate = rand(500, 1500) + (rand(0, 99) / 100);
        //                     $waterCut = rand(0, 1000) / 100; // 0-10%
        //                     $pressure = rand(1000, 2000) / 10; // 100-200 psi
        //                     $temperature = rand(800, 950) / 10; // 80-95°F

        //                     $test = MPFMTest::firstOrCreate(
        //                         [
        //                             'user_id' => $operator->id,
        //                             'oil_field_id' => $oilField->id,
        //                             'tower_id' => $tower->id,
        //                             'oil_string_id' => $oilString->id,
        //                             'mpfm_operational_plan_id' => $plan->id,
        //                             'test_date' => $date->format('Y-m-d'),
        //                             'shift' => $shift,
        //                         ],
        //                         [
        //                             'oil_rate' => $oilRate,
        //                             'gas_rate' => $gasRate,
        //                             'water_cut' => $waterCut,
        //                             'pressure' => $pressure,
        //                             'temperature' => $temperature,
        //                             'status' => $date->isPast() ? (rand(0, 1) ? 'submitted' : 'approved') : 'draft',
        //                             'notes' => 'Test performed during ' . $shift . ' shift',
        //                         ]
        //                     );

        //                     // Calculate and store KPIs
        //                     if ($test->wasRecentlyCreated) {
        //                         $kpis = $this->testService->calculateKPIs($test);
        //                         $test->update(['calculated_kpis' => $kpis]);
        //                     }

        //                     $testCount++;
        //                 }
        //             }
        //         }
        //     }
        // }

        // $this->command->info("Created {$planCount} operational plans and {$testCount} MPFM tests!");
    }
}

