<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Task ID 5\n";
echo "==================\n\n";

$taskInTasks = \App\Domains\Task\Models\Task::find(5);
$taskInPlanTasks = \App\Models\PlanTask::find(5);

echo "In 'tasks' table: " . ($taskInTasks ? "YES (ID: " . $taskInTasks->id . ", title: " . $taskInTasks->title . ")" : "NO") . "\n";
echo "In 'plan_tasks' table: " . ($taskInPlanTasks ? "YES (ID: " . $taskInPlanTasks->id . ")" : "NO") . "\n";

if ($taskInTasks) {
    echo "\nTask from 'tasks' table:\n";
    echo "- Model: " . get_class($taskInTasks) . "\n";
    echo "- Table: " . $taskInTasks->getTable() . "\n";
    echo "- Title: " . $taskInTasks->title . "\n";
}

if ($taskInPlanTasks) {
    echo "\nTask from 'plan_tasks' table:\n";
    echo "- Model: " . get_class($taskInPlanTasks) . "\n";
    echo "- Table: " . $taskInPlanTasks->getTable() . "\n";
    echo "- Well: " . ($taskInPlanTasks->well_full_name ?? $taskInPlanTasks->well_number) . "\n";
}
?>
