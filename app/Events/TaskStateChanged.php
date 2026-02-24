<?php

namespace App\Events;

use App\Models\User;
use App\Enums\TaskState;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class TaskStateChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Model $task (Can be PlanTask or Task)
     * @param TaskState $oldState
     * @param TaskState $newState
     * @param User $user
     */
    public function __construct(
        public Model $task,
        public TaskState $oldState,
        public TaskState $newState,
        public User $user
    ) {
        //
    }
}
