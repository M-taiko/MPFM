<?php

namespace App\Notifications;

use App\Models\PlanTask;
use App\Models\MPFMTest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCompletedNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $mpfmTest;

    /**
     * Create a new notification instance.
     */
    public function __construct(PlanTask $task, MPFMTest $mpfmTest)
    {
        $this->task = $task;
        $this->mpfmTest = $mpfmTest;
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
            'task_identifier' => $this->task->task_identifier,
            'mpfm_test_id' => $this->mpfmTest->id,
            'operator_name' => $this->task->operator->name,
            'oil_string' => $this->task->oilString->name,
            'tower' => $this->task->oilString->tower->name,
            'oil_field' => $this->task->oilString->tower->oilField->name,
            'completed_at' => $this->task->completed_at,
            'duration_minutes' => $this->task->duration_minutes,
            'oil_rate' => $this->mpfmTest->oil_rate,
            'gas_rate' => $this->mpfmTest->gas_rate,
            'water_cut' => $this->mpfmTest->water_cut,
            'message' => "Task {$this->task->task_identifier} completed by {$this->task->operator->name}",
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
