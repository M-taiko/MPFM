<?php

namespace App\Console\Commands;

use App\Models\PlanTask;
use Illuminate\Console\Command;

class MigrateTaskAssignments extends Command
{
    protected $signature = 'tasks:migrate-assignments';
    protected $description = 'Migrate existing task assignments from assigned_operator_id to pivot table';

    public function handle()
    {
        $count = 0;

        PlanTask::whereNotNull('assigned_operator_id')->chunk(100, function($tasks) use (&$count) {
            foreach ($tasks as $task) {
                $task->operators()->syncWithoutDetaching([
                    $task->assigned_operator_id => [
                        'assigned_at' => $task->assigned_at ?? now(),
                        'assigned_by' => $task->assigned_by,
                        'is_primary' => true,
                    ]
                ]);
                $count++;
                $this->line("Migrated task {$task->id}");
            }
        });

        $this->info("Successfully migrated {$count} task assignments to pivot table!");
    }
}
