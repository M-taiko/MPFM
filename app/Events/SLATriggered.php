<?php

namespace App\Events;

use App\Models\PlanTask;
use App\Models\TaskSLAHistory;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SLATriggered
{
    use Dispatchable, SerializesModels;

    /**
     * The task that triggered the SLA event
     *
     * @var PlanTask
     */
    public PlanTask $task;

    /**
     * The SLA history record for this event
     *
     * @var TaskSLAHistory
     */
    public TaskSLAHistory $slaHistory;

    /**
     * The user who triggered the event (or system if null)
     *
     * @var User|null
     */
    public ?User $triggeredBy;

    /**
     * The reason for the SLA event
     *
     * @var string
     */
    public string $reason;

    /**
     * Create a new event instance
     *
     * @param PlanTask $task
     * @param TaskSLAHistory $slaHistory
     * @param User|null $triggeredBy
     * @param string $reason
     */
    public function __construct(
        PlanTask $task,
        TaskSLAHistory $slaHistory,
        ?User $triggeredBy = null,
        string $reason = 'SLA triggered'
    )
    {
        $this->task = $task;
        $this->slaHistory = $slaHistory;
        $this->triggeredBy = $triggeredBy;
        $this->reason = $reason;
    }
}
