<?php

namespace App\Notifications;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVisitForDepartmentNotification extends Notification
{
    use Queueable;

    protected Visit $visit;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Nueva visita programada - ' . ($this->visit->department->name ?? 'Departamento');

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.new-visit-for-department', [
                'visit' => $this->visit,
                'recipient' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'visit_id' => $this->visit->id,
            'department_id' => $this->visit->department_id,
            'site_id' => $this->visit->site_id,
        ];
    }
}
