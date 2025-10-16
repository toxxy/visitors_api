<?php

namespace App\Notifications;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnplannedVisitAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Visit $visit;

    public $tries = 3;

    public function backoff(): array
    {
        return [10, 60, 120];
    }

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
        $this->afterCommit = true;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Alerta persona sin cita previa - ' . ($this->visit->site->name ?? 'Sitio');

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.unplanned-visit-alert', [
                'visit' => $this->visit,
                'recipient' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'visit_id' => $this->visit->id,
            'is_unplanned' => $this->visit->is_unplanned,
            'site_id' => $this->visit->site_id,
        ];
    }
}
