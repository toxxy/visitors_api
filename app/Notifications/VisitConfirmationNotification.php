<?php

namespace App\Notifications;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $visit;

    /**
     * Number of attempts for queued delivery (used when queue connection is not 'sync').
     */
    public $tries = 3;

    /**
     * Backoff delays between retry attempts in seconds.
     * For example: retry after 10s, then 60s, then 120s.
     */
    public function backoff(): array
    {
        return [10, 60, 120];
    }

    /**
     * Create a new notification instance.
     */
    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
        // Ensure the notification is dispatched after any open DB transaction commits.
        $this->afterCommit = true;
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