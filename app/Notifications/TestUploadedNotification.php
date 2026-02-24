<?php

namespace App\Notifications;

use App\Models\PlanTask;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestUploadedNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $operator;

    /**
     * Create a new notification instance.
     */
    public function __construct(PlanTask $task, $operator)
    {
        $this->task = $task;
        $this->operator = $operator;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_sno' => $this->task->sno,
            'task_identifier' => $this->task->task_identifier,
            'operator_name' => $this->operator->name,
            'operator_id' => $this->operator->id,
            'well_name' => $this->task->well_full_name,
            'message' => "New test uploaded by {$this->operator->name} for task {$this->task->task_identifier}",
            'type' => 'test_uploaded',
        ];
    }
}
