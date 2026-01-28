<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$task = \App\Models\PlanTask::first();
if ($task) {
    echo "✓ PlanTask table: " . $task->getTable() . "\n";
    echo "✓ Task ID: " . $task->id . "\n";
    echo "✓ Task Title: " . $task->title . "\n";
    if ($task->department) {
        echo "✓ Department: " . $task->department->name . "\n";
    }
    echo "✓ Completion Status: " . ($task->completion_status ?? 'NULL') . "\n";
} else {
    echo "✗ No tasks found in database\n";
}
?>
