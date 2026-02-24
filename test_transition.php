<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Task Transition\n";
echo "=======================\n\n";

try {
    // Get admin user
    $admin = \App\Models\User::role('admin')->first();
    if (!$admin) {
        echo "✗ No admin user found\n";
        exit;
    }

    echo "✓ Admin user: " . $admin->name . " (ID: " . $admin->id . ")\n";

    // Get task 5
    $task = \App\Domains\Task\Models\Task::find(5);
    if (!$task) {
        echo "✗ Task 5 not found\n";
        exit;
    }

    echo "✓ Task: " . $task->title . " (ID: " . $task->id . ")\n";
    echo "✓ Current State: " . $task->state->value . "\n";
    echo "✓ Task Department: " . ($task->department ? $task->department->name : 'NULL') . "\n";

    // Check available transitions
    $available = $task->getAvailableTransitions();
    echo "✓ Available transitions: " . json_encode(array_map(fn($s) => $s->value, $available)) . "\n";

    // Try to transition to SUBMITTED_TO_SURVEILLANCE
    echo "\n--- Attempting Transition to SUBMITTED_TO_SURVEILLANCE ---\n";

    $stateMachine = app(\App\Domains\Task\Services\TaskStateMachine::class);

    // Check if user can transition
    $canTransition = $stateMachine->canUserTransition($task, \App\Enums\TaskState::SUBMITTED_TO_SURVEILLANCE, $admin);
    echo "✓ Admin can transition: " . ($canTransition ? 'YES' : 'NO') . "\n";

    if ($canTransition) {
        // Check if task can transition
        $taskCanTransition = $task->canTransitionTo(\App\Enums\TaskState::SUBMITTED_TO_SURVEILLANCE);
        echo "✓ Task can transition: " . ($taskCanTransition ? 'YES' : 'NO') . "\n";

        if ($taskCanTransition) {
            // Perform transition
            $history = $stateMachine->transition(
                $task,
                \App\Enums\TaskState::SUBMITTED_TO_SURVEILLANCE,
                $admin,
                'Testing transition'
            );
            echo "✓ Transition successful!\n";
            echo "✓ History ID: " . $history->id . "\n";
            echo "✓ From State: " . $history->from_state . "\n";
            echo "✓ To State: " . $history->to_state . "\n";
        }
    }

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Exception Type: " . get_class($e) . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";
?>
