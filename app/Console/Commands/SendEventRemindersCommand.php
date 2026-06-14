<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;

class SendEventRemindersCommand extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Busca eventos que inician en 24 horas y envía un recordatorio por correo a sus inscritos.';

    public function handle(): int
    {
        $targetDateStart = now()->addDay()->startOfHour();
        $targetDateEnd = now()->addDay()->endOfHour();

        $events = Event::whereBetween('date', [$targetDateStart, $targetDateEnd])
            ->where('status', 'upcoming')
            ->with('users')
            ->get();

        foreach ($events as $event) {
            foreach ($event->users as $asistente) {
                $asistente->notify(new EventReminderNotification($event));
            }
        }

        $this->info('Recordatorios enviados con éxito para ' . $events->count() . ' eventos.');
        return Command::SUCCESS;
    }
}