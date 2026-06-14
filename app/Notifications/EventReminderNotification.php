<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
{
    use Queueable;

    public function __construct(protected Event $event) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recordatorio: Tu evento es mañana')
            ->line('Te recordamos que faltan 24 horas para el inicio del evento: ' . $this->event->title)
            ->line('Ciudad: ' . $this->event->city)
            ->line('No olvides tener tu código de ticket listo para el Check-in.');
    }
}