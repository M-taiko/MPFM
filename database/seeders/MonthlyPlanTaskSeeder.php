<?php

namespace Database\Seeders;

use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Models\User;
use App\Models\Tower;
use App\Models\OilString;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthlyPlanTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates reliable monthly plan and task data for testing multi-operator assignments.
     */
    public function run(): void
    {
        // DB::transaction(function () {
        //     $this->command->info('Creating monthly plans and tasks...');

        //     // Get operators
        //     $op1 = User::where('email', 'operator1@adnoc.ae')->first();
        //     $op2 = User::where('email', 'operator2@adnoc.ae')->first();
        //     $op3 = User::where('email', 'operator3@adnoc.ae')->first();
        //     $supervisor = User::where('email', 'supervisor@adnoc.ae')->first();

        //     if (!$op1 || !$op2 || !$op3 || !$supervisor) {
        //         $this->command->error('Required users not found. Run: php artisan db:seed --class=UserSeeder');
        //         return;
        //     }

        //     // Get assets
        //     $tower = Tower::first();
        //     $oilString = OilString::first();

        //     if (!$tower || !$oilString) {
        //         $this->command->error('Required assets not found. Run: php artisan db:seed --class=OilFieldSeeder');
        //         return;
        //     }

        //     // Create monthly plan
        //     $plan = MonthlyPlan::create([
        //         'plan_name' => 'January 2026 MPFM Testing Plan',
        //         'plan_month' => '2026-01',
        //         'status' => 'active',
        //         'created_by' => $supervisor->id,
        //     ]);

        //     $this->command->info("Created Monthly Plan: {$plan->plan_name}");

        //     // Create tasks with multi-operator assignments
        //     $tasks = [
        //         [
        //             'sno' => 1,
        //             'well_number' => 'WELL-001',
        //             'string_number' => 'A1',
        //             'well_full_name' => 'Well 375 String A1',
        //             'tower' => 'Tower 1',
        //             'tower_id' => $tower->id,
        //             'oil_string_id' => $oilString->id,
        //             'flow_route' => 'Route-A',
        //             'reservoir_layer' => 'Layer 1',
        //             'test_location' => 'Surface',
        //             'mpfm_type' => 'MPFM Type A',
        //             'dhsv_type' => 'DHSV Type 1',
        //             'category' => 'Standard',
        //             'required_whfp' => 100,
        //             'scheduled_date' => now()->addDays(1),
        //             'priority' => 'high',
        //             'completion_status' => 'pending',
        //             'operators' => [
        //                 [$op1->id, true],  // Operator 1 (primary)
        //                 [$op2->id, false], // Operator 2 (team)
        //             ],
        //         ],
        //         [
        //             'sno' => 2,
        //             'well_number' => 'WELL-002',
        //             'string_number' => 'A2',
        //             'well_full_name' => 'Well 376 String A2',
        //             'tower' => 'Tower 1',
        //             'tower_id' => $tower->id,
        //             'oil_string_id' => $oilString->id,
        //             'flow_route' => 'Route-B',
        //             'reservoir_layer' => 'Layer 2',
        //             'test_location' => 'Subsurface',
        //             'mpfm_type' => 'MPFM Type B',
        //             'dhsv_type' => 'DHSV Type 2',
        //             'category' => 'Standard',
        //             'required_whfp' => 120,
        //             'scheduled_date' => now()->addDays(2),
        //             'priority' => 'normal',
        //             'completion_status' => 'pending',
        //             'operators' => [
        //                 [$op2->id, true],  // Operator 2 (primary)
        //                 [$op3->id, false], // Operator 3 (team)
        //             ],
        //         ],
        //         [
        //             'sno' => 3,
        //             'well_number' => 'WELL-003',
        //             'string_number' => 'A3',
        //             'well_full_name' => 'Well 377 String A3',
        //             'tower' => 'Tower 1',
        //             'tower_id' => $tower->id,
        //             'oil_string_id' => $oilString->id,
        //             'flow_route' => 'Route-C',
        //             'reservoir_layer' => 'Layer 3',
        //             'test_location' => 'Downhole',
        //             'mpfm_type' => 'MPFM Type C',
        //             'dhsv_type' => 'DHSV Type 3',
        //             'category' => 'Complex',
        //             'required_whfp' => 150,
        //             'scheduled_date' => now()->addDays(3),
        //             'priority' => 'top',
        //             'completion_status' => 'pending',
        //             'operators' => [
        //                 [$op1->id, true],  // Operator 1 (primary)
        //                 [$op2->id, false], // Operator 2 (team)
        //                 [$op3->id, false], // Operator 3 (team)
        //             ],
        //         ],
        //         [
        //             'sno' => 4,
        //             'well_number' => 'WELL-004',
        //             'string_number' => 'B1',
        //             'well_full_name' => 'Well 378 String B1',
        //             'tower' => 'Tower 1',
        //             'tower_id' => $tower->id,
        //             'oil_string_id' => $oilString->id,
        //             'flow_route' => 'Route-D',
        //             'reservoir_layer' => 'Layer 4',
        //             'test_location' => 'Surface',
        //             'mpfm_type' => 'MPFM Type D',
        //             'dhsv_type' => 'DHSV Type 4',
        //             'category' => 'Standard',
        //             'required_whfp' => 110,
        //             'scheduled_date' => now()->addDays(4),
        //             'priority' => 'high',
        //             'completion_status' => 'pending',
        //             'operators' => [
        //                 [$op3->id, true],  // Operator 3 (primary)
        //                 [$op1->id, false], // Operator 1 (team)
        //             ],
        //         ],
        //         [
        //             'sno' => 5,
        //             'well_number' => 'WELL-005',
        //             'string_number' => 'B2',
        //             'well_full_name' => 'Well 379 String B2',
        //             'tower' => 'Tower 1',
        //             'tower_id' => $tower->id,
        //             'oil_string_id' => $oilString->id,
        //             'flow_route' => 'Route-E',
        //             'reservoir_layer' => 'Layer 5',
        //             'test_location' => 'Downhole',
        //             'mpfm_type' => 'MPFM Type E',
        //             'dhsv_type' => 'DHSV Type 5',
        //             'category' => 'Complex',
        //             'required_whfp' => 180,
        //             'scheduled_date' => now()->addDays(5),
        //             'priority' => 'high',
        //             'completion_status' => 'pending',
        //             'operators' => [
        //                 [$op2->id, true],  // Operator 2 (primary)
        //                 [$op1->id, false], // Operator 1 (team)
        //                 [$op3->id, false], // Operator 3 (team)
        //             ],
        //         ],
        //     ];

        //     // Create each task
        //     foreach ($tasks as $taskData) {
        //         $operators = $taskData['operators'];
        //         unset($taskData['operators']);

        //         $taskData['monthly_plan_id'] = $plan->id;
        //         $taskData['assigned_operator_id'] = null; // Will set after creating pivot entries
        //         $taskData['assigned_by'] = $supervisor->id;
        //         $taskData['assigned_at'] = now();

        //         $task = PlanTask::create($taskData);

        //         // Attach operators via pivot table
        //         $syncData = [];
        //         $primaryOpId = null;
        //         foreach ($operators as [$opId, $isPrimary]) {
        //             $syncData[$opId] = [
        //                 'assigned_at' => now(),
        //                 'assigned_by' => $supervisor->id,
        //                 'is_primary' => $isPrimary,
        //             ];
        //             if ($isPrimary) {
        //                 $primaryOpId = $opId;
        //             }
        //         }

        //         // Sync operators
        //         $task->operators()->sync($syncData);

        //         // Update assigned_operator_id to primary operator
        //         if ($primaryOpId) {
        //             $task->update(['assigned_operator_id' => $primaryOpId]);
        //         }

        //         // Log the creation
        //         $opNames = collect($operators)->map(fn ($item) => User::find($item[0])->name)->join(', ');
        //         $this->command->info("✅ Task {$task->id}: {$task->well_full_name} → {$opNames}");
        //     }

        //     $this->command->info("\n✅ Created " . count($tasks) . " tasks with multi-operator assignments!");
        //     $this->command->info("✅ Total operators assigned: " . DB::table('plan_task_operators')->count());
        // });
    }
}
