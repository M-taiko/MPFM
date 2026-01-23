<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PlanTask;
use App\Models\MPFMTest;

class TestApprovedNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $test;

    /**
     * Create a new notification instance.
     */
    public function __construct(PlanTask $task, MPFMTest $test)
    {
        $this->task = $task;
        $this->test = $test;
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
        return (new MailMessage)
                    ->greeting('مرحباً ' . $notifiable->name)
                    ->subject('التيست اتوافق عليه ✓')
                    ->line('التيست الي عملت رفع ليه اتوافق عليه من قبل السوبرفايزر')
                    ->line('Well: ' . $this->task->well_number)
                    ->line('Tower: ' . $this->task->tower)
                    ->line('Test Date: ' . $this->test->test_date)
                    ->action('شوف تفاصيل التيست', url('/well-tests/' . $this->test->well_test_id))
                    ->line('شكراً على إتمام المهمة!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'التيست اتوافق عليه',
            'message' => 'التيست بتاع الـ Well ' . $this->task->well_number . ' اتوافق عليه من السوبرفايزر',
            'type' => 'success',
            'task_id' => $this->task->id,
            'test_id' => $this->test->id,
        ];
    }
}
