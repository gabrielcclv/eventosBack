<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(protected Event $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Cancelación del evento: ' . $this->event->title)
            ->line('Lamentamos informarte que el organizador ha cancelado el evento "' . $this->event->title . '".')
            ->line('Tu código de registro ha quedado invalidado de forma automática.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'message' => 'El evento ha sido cancelado.'
        ];
    }
}