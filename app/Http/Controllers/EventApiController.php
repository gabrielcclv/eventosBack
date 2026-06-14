<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected EventService $eventService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['category', 'organizer']);

        if ($request->has('city')) {
            $query->where('city', $request->input('city'));
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->has('date')) {
            $query->whereDate('date', $request->input('date'));
        }
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', 'upcoming');
        }

        $events = $query->orderBy('date', 'asc')->paginate(10);
        return $this->successResponse(EventResource::collection($events));
    }

    public function show(int $id): JsonResponse
    {
        $event = Event::with(['category', 'organizer', 'reviews.user'])->findOrFail($id);
        return $this->successResponse(new EventResource($event));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'city' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
        ]);

        $event = $this->eventService->createEvent($validated, $request->user()->id);
        return $this->successResponse(new EventResource($event), 'Evento creado con éxito', 201);
    }

    public function updateImage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'imagen' => 'required|image|max:2048'
        ]);

        $event = Event::findOrFail($id);

        if ($event->organizer_id !== $request->user()->id) {
            return $this->errorResponse('No eres el dueño de este evento.', 'UNAUTHORIZED_RESOURCE', 403);
        }

        $updatedEvent = $this->eventService->updateEventCoverImage($event, $request->file('imagen'));
        return $this->successResponse(new EventResource($updatedEvent), 'Imagen de portada actualizada con éxito.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        
        if ($event->organizer_id !== $request->user()->id) {
            return $this->errorResponse('No eres el dueño de este evento.', 'UNAUTHORIZED_RESOURCE', 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date',
            'city' => 'sometimes|string|max:100',
            'capacity' => 'sometimes|integer|min:1',
        ]);

        $updatedEvent = $this->eventService->updateEvent($event, $validated);
        return $this->successResponse(new EventResource($updatedEvent), 'Datos del evento actualizados.');
    }


    public function getRegistrations(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== $request->user()->id) {
            return $this->errorResponse('Acceso denegado. No eres el organizador de este evento.', 'FORBIDDEN', 403);
        }

        $inscritos = $event->users()->select('users.id', 'users.name', 'users.email')->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'ticket_code' => $user->inscripciones->unique_code,
                'checked_in' => $user->inscripciones->checked_in,
                'registered_at' => $user->inscripciones->created_at
            ];
    });

        return $this->successResponse($inscritos, 'Listado de inscritos recuperado.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        if ($event->organizer_id !== $request->user()->id) {
            return $this->errorResponse('No eres el dueño.', 'UNAUTHORIZED', 403);
        }
        $this->eventService->cancelEvent($event);
        return $this->successResponse(null, 'Evento cancelado.');
    }

    public function register(Request $request, int $id): JsonResponse
    {
        try {
            $registration = $this->eventService->registerToEvent($id, $request->user());
            return $this->successResponse(['unique_code' => $registration->unique_code], 'Inscripción procesada.', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 'REGISTRATION_FAILED', 422);
        }
    }

    public function cancelRegistration(Request $request, int $id): JsonResponse
    {
        try {
            $this->eventService->cancelRegistration($id, $request->user());
            return $this->successResponse(null, 'Inscripción eliminada.');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 'CANCEL_FAILED', 422);
        }
    }

    public function storeReview(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000'
        ]);

        try {
            $review = $this->eventService->leaveReview($id, $request->user(), $validated);
            return $this->successResponse($review, 'Reseña guardada.', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 'REVIEW_FAILED', 422);
        }
    }
}