<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MPFMTest;

class TestAwaitingSurveillanceApprovalNotification extends Notification
{
    use Queueable;

    protected $mpfmTest;

    /**
     * Create a new notification instance.
     */
    public function __construct(MPFMTest $mpfmTest)
    {
        $this->mpfmTest = $mpfmTest;
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
                    ->subject('تيست MPFM بانتظار الموافقة من السورفيلنس')
                    ->line('تيست MPFM جديد بانتظار مراجعتك والموافقة عليه')
                    ->line('معلومات التيست:')
                    ->line('- الحقل: ' . ($this->mpfmTest->oilField->name ?? 'N/A'))
                    ->line('- البرج: ' . ($this->mpfmTest->tower->name ?? 'N/A'))
                    ->line('- الـ String: ' . ($this->mpfmTest->oilString->name ?? 'N/A'))
                    ->line('- تاريخ التيست: ' . ($this->mpfmTest->test_date?->format('Y-m-d') ?? 'N/A'))
                    ->line('معدل الزيت: ' . number_format($this->mpfmTest->calculated_kpis['oil_rate'] ?? 0, 2) . ' STB/d')
                    ->line('معدل الغاز: ' . number_format($this->mpfmTest->calculated_kpis['gas_rate'] ?? 0, 2) . ' Mscf/d')
                    ->action('مراجعة التيست', route('mpfm_tests.show', $this->mpfmTest->id))
                    ->line('من فضلك قم بمراجعة البيانات والموافقة أو الرفض')
                    ->line('شكراً على تعاونك!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تيست MPFM بانتظار الموافقة من السورفيلنس',
            'message' => 'تيست MPFM للـ ' . ($this->mpfmTest->oilString->name ?? 'N/A') . ' بانتظار مراجعتك',
            'type' => 'info',
            'mpfm_test_id' => $this->mpfmTest->id,
            'status' => 'approved_by_department',
            'action_required' => true,
            'action_url' => route('mpfm_tests.show', $this->mpfmTest->id),
        ];
    }
}
