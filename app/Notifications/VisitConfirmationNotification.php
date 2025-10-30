<?php

namespace App\Notifications;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitConfirmationNotification extends Notification
{
    use Queueable;

    protected $visit;

    /**
     * Create a new notification instance.
     */
    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmación de Visita - ' . $this->visit->site->name)
            ->view('emails.visit-confirmation', ['visit' => $this->visit]);
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'visit_id' => $this->visit->id,
            'visitor_name' => $this->visit->visitor_name,
            'site_name' => $this->visit->site->name,
            'scheduled_at' => $this->visit->scheduled_at,
        ];
    }
}