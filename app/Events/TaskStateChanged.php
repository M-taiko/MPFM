<?php

namespace App\Events;

use App\Models\PlanTask;
use App\Models\User;
use App\Enums\TaskState;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStateChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param PlanTask $task
     * @param TaskState $oldState
     * @param TaskState $newState
     * @param User $user
     */
    public function __construct(
        public PlanTask $task,
        public TaskState $oldState,
        public TaskState $newState,
        public User $user
    ) {
        //
    }
}
