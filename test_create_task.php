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

// Create a test task
$task = \App\Models\PlanTask::create([
    'title' => 'Test MPFM Task',
    'description' => 'Test task for department dashboard',
    'department_id' => $dept->id,
    'monthly_plan_id' => $plan->id,
    'completion_status' => 'pending',
    'state' => 'draft',
    'priority' => 'high',
]);

echo "✓ Created task ID: " . $task->id . "\n";
echo "✓ Department: " . $task->department->name . "\n";
echo "✓ Completion Status: " . $task->completion_status . "\n";

// Now test the dashboard query
echo "\nTesting dashboard queries:\n";
$stats = [
    'total' => \App\Models\PlanTask::where('department_id', $dept->id)->count(),
    'pending' => \App\Models\PlanTask::where('department_id', $dept->id)->where('completion_status', 'pending')->count(),
];

echo "✓ Total tasks: " . $stats['total'] . "\n";
echo "✓ Pending tasks: " . $stats['pending'] . "\n";
?>
