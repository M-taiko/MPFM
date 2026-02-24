<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MPFMTest;
use App\Domains\Task\Models\Task;

class RetestTaskCreatedNotification extends Notification
{
    use Queueable;

    protected $newTask;
    protected $originalTest;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $newTask, MPFMTest $originalTest)
    {
        $this->newTask = $newTask;
        $this->originalTest = $originalTest;
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
                    ->subject('مهمة إعادة اختبار MPFM جديدة')
                    ->line('تم إنشاء مهمة جديدة لإعادة اختبار MPFM')
                    ->line('معلومات التيست الأصلي:')
                    ->line('- الحقل: ' . ($this->originalTest->oilField->name ?? 'N/A'))
                    ->line('- البرج: ' . ($this->originalTest->tower->name ?? 'N/A'))
                    ->line('- الـ String: ' . ($this->originalTest->oilString->name ?? 'N/A'))
                    ->line('- تاريخ التيست الأصلي: ' . ($this->originalTest->test_date?->format('Y-m-d') ?? 'N/A'))
                    ->line('')
                    ->line('تفاصيل المهمة الجديدة:')
                    ->line('- عنوان المهمة: ' . $this->newTask->title)
                    ->line('- تاريخ الاستحقاق: ' . ($this->newTask->expected_completion_at?->format('Y-m-d') ?? 'N/A'))
                    ->line('- الأولوية: ' . ucfirst($this->newTask->priority ?? 'normal'))
                    ->action('عرض المهمة', route('tasks.show', $this->newTask->id))
                    ->line('من فضلك تأكد من متابعة المهمة الجديدة بشكل صحيح')
                    ->line('شكراً على تعاونك!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'مهمة إعادة اختبار MPFM جديدة',
            'message' => 'تم إنشاء مهمة جديدة لإعادة اختبار في الـ ' . ($this->originalTest->oilString->name ?? 'N/A'),
            'type' => 'info',
            'task_id' => $this->newTask->id,
            'mpfm_test_id' => $this->originalTest->id,
            'status' => 'retest_requested',
            'action_url' => route('tasks.show', $this->newTask->id),
        ];
    }
}
