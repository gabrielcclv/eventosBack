<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Review;
use App\Models\User;
use App\Notifications\EventCancelledNotification;
use App\Notifications\EventRegisteredNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EventService
{
    public function createEvent(array $data, int $organizerId): Event
    {
        $data['organizer_id'] = $organizerId;
        $data['status'] = 'upcoming';
        return Event::create($data);
    }


    public function updateEventCoverImage(Event $event, mixed $imageFile): Event
    {
        if ($event->image_url) {
            $oldFilename = basename($event->image_url);
            Storage::disk('local')->delete($oldFilename);
        }

        $filename = 'cover_' . Str::random(12) . '.' . $imageFile->getClientOriginalExtension();
        Storage::disk('local')->put($filename, file_get_contents($imageFile->getRealPath()));
        
        $event->update([
            'image_url' => Storage::url($filename)
        ]);

        return $event;
    }

    public function updateEvent(Event $event, array $data): Event
    {
        $event->update($data);
        return $event;
    }

    public function cancelEvent(Event $event): void
    {
        DB::transaction(function () use ($event) {
            $event->update(['status' => 'cancelled']);
            foreach ($event->users as $asistente) {
                $asistente->notify(new EventCancelledNotification($event));
            }
            $event->delete();
        });
    }


    public function registerToEvent(int $eventId, User $user): Registration
    {
        return DB::transaction(function () use ($eventId, $user) {
            $event = Event::lockForUpdate()->findOrFail($eventId);

            if ($event->status === 'cancelled' || $event->status === 'past') {
                throw ValidationException::withMessages(['event' => 'No puedes inscribirte a un evento pasado o cancelado.']);
            }
            
            if ($event->status === 'sold_out' || $event->users()->count() >= $event->capacity) {
                throw ValidationException::withMessages(['event' => 'El evento se encuentra completamente agotado.']);
            }

            if ($event->users()->where('user_id', $user->id)->exists()) {
                throw ValidationException::withMessages(['event' => 'Ya te encuentras inscrito en este evento.']);
            }

            $uniqueCode = 'TKT-' . strtoupper(Str::random(10));

            $registration = Registration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'unique_code' => $uniqueCode,
                'checked_in' => false,
            ]);

            if ($event->users()->count() >= $event->capacity) {
                $event->update(['status' => 'sold_out']);
            }

            $user->notify(new EventRegisteredNotification($event, $uniqueCode));

            return $registration;
        });
    }

    public function cancelRegistration(int $eventId, User $user): void
    {
        DB::transaction(function () use ($eventId, $user) {
            $event = Event::lockForUpdate()->findOrFail($eventId);
            
            $registration = Registration::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if ($registration->checked_in) {
                throw ValidationException::withMessages(['registration' => 'No puedes cancelar un ticket que ya ha sido validado.']);
            }

            $registration->delete();

            if ($event->status === 'sold_out' && $event->users()->count() < $event->capacity) {
                $event->update(['status' => 'upcoming']);
            }
        });
    }

    public function leaveReview(int $eventId, User $user, array $reviewData): Review
    {
        $event = Event::findOrFail($eventId);

        $alreadyReviewed = Review::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            throw ValidationException::withMessages(['review' => 'Ya has dejado una valoración previa para este evento. No se permiten duplicados.']);
        }

        $registration = Registration::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->where('checked_in', true)
            ->first();

        if (!$registration) {
            throw ValidationException::withMessages(['review' => 'Solo puedes valorar eventos a los que hayas asistido físicamente (Check-in requerido).']);
        }

        return Review::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rating' => $reviewData['rating'],
            'comment' => $reviewData['comment'] ?? null,
        ]);
    }

    public function checkInTicket(int $eventId, string $uniqueCode, int $organizerId): Registration
    {
        $event = Event::where('id', $eventId)->where('organizer_id', $organizerId)->firstOrFail();
        $registration = Registration::where('event_id', $event->id)->where('unique_code', $uniqueCode)->first();

        if (!$registration) {
            throw ValidationException::withMessages(['ticket' => 'El código de ticket no es válido para este evento.']);
        }
        if ($registration->checked_in) {
            throw ValidationException::withMessages(['ticket' => 'Este ticket ya ha sido utilizado.']);
        }

        $registration->update(['checked_in' => true]);
        return $registration;
    }
}