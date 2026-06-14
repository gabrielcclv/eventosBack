<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Event $event,
        protected string $ticketCode
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmación de Inscripción: ' . $this->event->title)
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('Te has inscrito correctamente al evento "' . $this->event->title . '".')
            ->line('Tu código único de acceso obligatorio para el check-in es: **' . $this->ticketCode . '**')
            ->line('Fecha del evento: ' . $this->event->date->format('d/m/Y H:i'))
            ->line('Ciudad: ' . $this->event->city)
            ->line('¡Disfruta del evento!');
    }
}