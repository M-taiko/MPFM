<?php

namespace App\Models;

use App\Domains\Task\Models\Task;

/**
 * PlanTask Alias
 *
 * This model serves as a backward compatibility alias for the refactored Task model.
 * Phase 2 moved tasks to a domain-driven design structure at App\Domains\Task\Models\Task
 * but existing code throughout the application still references App\Models\PlanTask.
 *
 * Rather than update 123+ references across the codebase immediately, this alias
 * allows the application to continue functioning while the code is gradually refactored.
 *
 * @deprecated Use App\Domains\Task\Models\Task instead
 * @see App\Domains\Task\Models\Task
 */
class PlanTask extends Task
{
    // This class simply extends the new Task model from the Domains structure
    // All functionality is inherited from the parent Task class
    // Overrides the table name to use the legacy plan_tasks table
    protected $table = 'plan_tasks';
    // Future: Once all references are updated, this alias can be removed
}
