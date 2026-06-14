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

    public function __construct(
        protected EventService $eventService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['category', 'organizer']);

        if ($request->has('city')) {
            $query->where('city', $request->input('city'));
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $events = $query->paginate(10);
        return $this->successResponse(EventResource::collection($events));
    }

    public function show(int $id): JsonResponse
    {
        $event = Event::with(['category', 'organizer', 'reviews.user'])->findOrFail($id);
        return $this->successResponse(new EventResource($event));
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || !$user->is_organizer) {
            return $this->errorResponse('No tienes permisos de organizador', 'FORBIDDEN_ROLE', 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'city' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
        ]);

        $event = $this->eventService->createEvent($validated, $user->id);
        return $this->successResponse(new EventResource($event), 'Evento creado con éxito', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        
        if ($event->organizer_id !== $request->user()->id) {
            return $this->errorResponse('No eres el dueño de este evento', 'UNAUTHORIZED_RESOURCE', 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date',
            'city' => 'sometimes|string|max:100',
            'capacity' => 'sometimes|integer|min:1',
            'imagen' => 'nullable|image|max:2048'
        ]);

        $updatedEvent = $this->eventService->updateEvent($event, $validated, $request->file('imagen'));
        return $this->successResponse(new EventResource($updatedEvent), 'Evento actualizado con éxito');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== $request->user()->id) {
            return $this->errorResponse('No eres el dueño de este evento', 'UNAUTHORIZED_RESOURCE', 403);
        }

        $this->eventService->cancelEvent($event);
        return $this->successResponse(null, 'Evento cancelado y asistentes notificados.');
    }

    public function register(Request $request, int $id): JsonResponse
    {
        try {
            $registration = $this->eventService->registerToEvent($id, $request->user());
            return $this->successResponse([
                'ticket_code' => $registration->unique_code
            ], 'Inscripción completada exitosamente', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 'REGISTRATION_FAILED', 422);
        }
    }

    public function cancelRegistration(Request $request, int $id): JsonResponse
    {
        try {
            $this->eventService->cancelRegistration($id, $request->user());
            return $this->successResponse(null, 'Inscripción cancelada correctamente.');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 'CANCEL_FAILED', 422);
        }
    }

    public function storeReview(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500'
        ]);

        try {
            $review = $this->eventService->leaveReview($id, $request->user(), $validated);
            return $this->successResponse($review, 'Reseña publicada correctamente', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 'REVIEW_FAILED', 422);
        }
    }

    public function checkIn(Request $request, int $id): JsonResponse
    {
        $request->validate(['unique_code' => 'required|string']);

        try {
            $this->eventService->checkInTicket($id, $request->input('unique_code'), $request->user()->id);
            return $this->successResponse(null, 'Check-in realizado correctamente.');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 'CHECKIN_FAILED', 422);
        }
    }
}