<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PlanTask;
use App\Models\MPFMTest;

class TestRejectedNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $test;
    protected $remarks;

    /**
     * Create a new notification instance.
     */
    public function __construct(PlanTask $task, MPFMTest $test, $remarks = null)
    {
        $this->task = $task;
        $this->test = $test;
        $this->remarks = $remarks;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
                    ->greeting('مرحباً ' . $notifiable->name)
                    ->subject('التيست اتم رفضه - رفع مره اخري')
                    ->line('التيست الي عملت رفع ليه اتم رفضه من قبل السوبرفايزر')
                    ->line('Well: ' . $this->task->well_number)
                    ->line('Tower: ' . $this->task->tower)
                    ->line('Test Date: ' . $this->test->test_date);

        if ($this->remarks) {
            $mail->line('السبب: ' . $this->remarks);
        }

        return $mail->action('اعد رفع التيست', url('/upload?task_id=' . $this->task->id))
                    ->line('الرجاء اعادة رفع التيست مع الانتباه للملاحظات');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'التيست اتم رفضه',
            'message' => 'التيست بتاع الـ Well ' . $this->task->well_number . ' اتم رفضه. الرجاء الاعادة',
            'type' => 'warning',
            'task_id' => $this->task->id,
            'test_id' => $this->test->id,
            'remarks' => $this->remarks,
        ];
    }
}
