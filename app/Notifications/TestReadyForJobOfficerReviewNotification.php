<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MPFMTest;

class TestReadyForJobOfficerReviewNotification extends Notification
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
                    ->subject('تيست MPFM جاهز للمراجعة - الجوب أوفيسر')
                    ->line('تيست MPFM موافق عليه من جميع المستويات وجاهز لمراجعتك')
                    ->line('معلومات التيست:')
                    ->line('- الحقل: ' . ($this->mpfmTest->oilField->name ?? 'N/A'))
                    ->line('- البرج: ' . ($this->mpfmTest->tower->name ?? 'N/A'))
                    ->line('- الـ String: ' . ($this->mpfmTest->oilString->name ?? 'N/A'))
                    ->line('- تاريخ التيست: ' . ($this->mpfmTest->test_date?->format('Y-m-d') ?? 'N/A'))
                    ->line('معدل الزيت: ' . number_format($this->mpfmTest->calculated_kpis['oil_rate'] ?? 0, 2) . ' STB/d')
                    ->line('معدل الغاز: ' . number_format($this->mpfmTest->calculated_kpis['gas_rate'] ?? 0, 2) . ' Mscf/d')
                    ->line('الضغط: ' . number_format($this->mpfmTest->pressure_avg ?? $this->mpfmTest->pressure ?? 0, 2) . ' psi')
                    ->action('مراجعة التيست', route('mpfm_tests.show', $this->mpfmTest->id))
                    ->line('التيست موافق عليه من Department Supervisor والسورفيلنس')
                    ->line('من فضلك قم بمراجعة البيانات واتخاذ الخطوة التالية')
                    ->line('شكراً لك!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تيست MPFM جاهز للمراجعة',
            'message' => 'تيست MPFM للـ ' . ($this->mpfmTest->oilString->name ?? 'N/A') . ' جاهز لمراجعتك كـ Job Officer',
            'type' => 'info',
            'mpfm_test_id' => $this->mpfmTest->id,
            'status' => 'approved_by_surveillance',
            'action_required' => true,
            'action_url' => route('mpfm_tests.show', $this->mpfmTest->id),
        ];
    }
}
