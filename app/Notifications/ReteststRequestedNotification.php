<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MPFMTest;
use App\Domains\Task\Models\Task;

class ReteststRequestedNotification extends Notification
{
    use Queueable;

    protected $mpfmTest;
    protected $newTask;
    protected $instructions;

    /**
     * Create a new notification instance.
     */
    public function __construct(MPFMTest $mpfmTest, Task $newTask, string $instructions)
    {
        $this->mpfmTest = $mpfmTest;
        $this->newTask = $newTask;
        $this->instructions = $instructions;
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
                    ->subject('طلب إعادة اختبار MPFM - تيست #' . $this->mpfmTest->id)
                    ->line('تم طلب إعادة اختبار لتيست MPFM')
                    ->line('معلومات التيست الأصلي:')
                    ->line('- الحقل: ' . ($this->mpfmTest->oilField->name ?? 'N/A'))
                    ->line('- البرج: ' . ($this->mpfmTest->tower->name ?? 'N/A'))
                    ->line('- الـ String: ' . ($this->mpfmTest->oilString->name ?? 'N/A'))
                    ->line('- تاريخ التيست: ' . ($this->mpfmTest->test_date?->format('Y-m-d') ?? 'N/A'))
                    ->line('')
                    ->line('<strong>تعليمات الإعادة:</strong>')
                    ->line($this->instructions)
                    ->line('')
                    ->line('تاريخ الاستحقاق: ' . ($this->newTask->expected_completion_at?->format('Y-m-d') ?? 'N/A'))
                    ->line('الأولوية: ' . ucfirst($this->newTask->priority ?? 'normal'))
                    ->action('عرض المهمة الجديدة', route('tasks.show', $this->newTask->id))
                    ->line('من فضلك قم بإعادة الاختبار وفقاً للتعليمات المذكورة أعلاه')
                    ->line('شكراً على تعاونك!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'طلب إعادة اختبار MPFM',
            'message' => 'تم طلب إعادة اختبار لتيست #' . $this->mpfmTest->id . ' في الـ ' . ($this->mpfmTest->oilString->name ?? 'N/A'),
            'type' => 'warning',
            'mpfm_test_id' => $this->mpfmTest->id,
            'task_id' => $this->newTask->id,
            'status' => 'retest_requested',
            'action_required' => true,
            'action_url' => route('tasks.show', $this->newTask->id),
            'expected_completion_date' => $this->newTask->expected_completion_at?->format('Y-m-d'),
        ];
    }
}
