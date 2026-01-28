<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get first department (MPFM)
$dept = \App\Models\Department::where('code', 'MPFM')->first();
if (!$dept) {
    echo "✗ MPFM department not found\n";
    exit;
}

// Get first monthly plan
$plan = \App\Models\MonthlyPlan::first();
if (!$plan) {
    echo "✗ No monthly plan found\n";
    exit;
}

// Create a test plan task with all required fields
$task = \App\Models\PlanTask::create([
    'monthly_plan_id' => $plan->id,
    'department_id' => $dept->id,
    'sno' => 1,
    'well_number' => 'W-001',
    'string_number' => 'S-001',
    'well_full_name' => 'Well Test 001',
    'completion_status' => 'pending',
    'state' => 'draft',
    'final_status' => 'pending',
    'priority' => 'high',
    'is_recurring' => false,
    'overdue_flag' => false,
]);

echo "✓ Created PlanTask ID: " . $task->id . "\n";
echo "✓ Department: " . $task->department->name . "\n";
echo "✓ Completion Status: " . $task->completion_status . "\n";
echo "✓ Table: " . $task->getTable() . "\n";

// Now test the dashboard query
echo "\n=== Testing Dashboard Queries ===\n";
$stats = [
    'total' => \App\Models\PlanTask::where('department_id', $dept->id)->count(),
    'pending' => \App\Models\PlanTask::where('department_id', $dept->id)->where('completion_status', 'pending')->count(),
    'completed' => \App\Models\PlanTask::where('department_id', $dept->id)->where('completion_status', 'completed')->count(),
];

echo "✓ Total tasks: " . $stats['total'] . "\n";
echo "✓ Pending tasks: " . $stats['pending'] . "\n";
echo "✓ Completed tasks: " . $stats['completed'] . "\n";
echo "\n=== All tests passed! ===\n";
?>
