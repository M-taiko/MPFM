<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MPFMTest;

class TestApprovedBySurveillanceNotification extends Notification
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
                    ->subject('تيست MPFM موافق عليه من السورفيلنس ✓')
                    ->line('تيست MPFM بتاع الـ ' . ($this->mpfmTest->oilString->name ?? 'N/A') . ' موافق عليه من السورفيلنس')
                    ->line('معلومات التيست:')
                    ->line('- الحقل: ' . ($this->mpfmTest->oilField->name ?? 'N/A'))
                    ->line('- البرج: ' . ($this->mpfmTest->tower->name ?? 'N/A'))
                    ->line('- الـ String: ' . ($this->mpfmTest->oilString->name ?? 'N/A'))
                    ->line('- تاريخ التيست: ' . ($this->mpfmTest->test_date?->format('Y-m-d') ?? 'N/A'))
                    ->action('عرض التيست', route('mpfm_tests.show', $this->mpfmTest->id))
                    ->line('التيست الآن موافق عليه من جميع المستويات وأُضيف لقائمة الجوب أوفيسر')
                    ->line('شكراً على مراجعتك!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تيست MPFM موافق عليه من السورفيلنس',
            'message' => 'التيست بتاع ' . ($this->mpfmTest->oilString->name ?? 'N/A') . ' موافق عليه من السورفيلنس والآن مع الجوب أوفيسر',
            'type' => 'success',
            'mpfm_test_id' => $this->mpfmTest->id,
            'status' => 'approved_by_surveillance',
        ];
    }
}
