<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventViewController extends Controller
{
    public function __construct(protected EventService $eventService) {}

    public function welcome()
    {
        return view('welcome');
    }

    public function index(Request $request)
    {
        $query = Event::where('status', 'upcoming')
            ->with(['organizer', 'category', 'reviews', 'users']);

        if ($request->has('city') && $request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $events = $query->paginate(12);
        $categories = Category::all();
        $cities = Event::select('city')->distinct()->pluck('city');

        return view('events.index', compact('events', 'categories', 'cities'));
    }

    public function show(Event $event)
    {
        $event->load(['organizer', 'category', 'reviews' => function ($q) {
            $q->with('user');
        }, 'users']);

        $avgRating = $event->reviews()->avg('rating') ?? 0;
        $isRegistered = auth()->check() && $event->users()->where('user_id', auth()->id())->exists();

        return view('events.show', compact('event', 'avgRating', 'isRegistered'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'city' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1|max:100000',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url',
        ]);

        $validated['organizer_id'] = auth()->id();

        $event = $this->eventService->createEvent($validated);

        return redirect()->route('events.show', $event)->with('success', 'Evento creado exitosamente');
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $categories = Category::all();
        return view('events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'city' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1|max:100000',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url',
        ]);

        $this->eventService->updateEvent($event, $validated);

        return redirect()->route('events.show', $event)->with('success', 'Evento actualizado exitosamente');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $this->eventService->cancelEvent($event);

        return redirect()->route('events.index')->with('success', 'Evento cancelado y se notificó a los participantes');
    }

    public function register(Request $request, Event $event)
    {
        try {
            $this->eventService->registerToEvent(auth()->user(), $event);
            return redirect()->back()->with('success', 'Te has inscrito al evento exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancelRegistration(Event $event)
    {
        try {
            $this->eventService->cancelRegistration(auth()->user(), $event);
            return redirect()->back()->with('success', 'Te has desinscrito del evento');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeReview(Request $request, Event $event)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        try {
            $this->eventService->leaveReview(auth()->user(), $event, $validated['rating'], $validated['comment'] ?? null);
            return redirect()->back()->with('success', 'Reseña publicada exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function myTickets()
    {
        $user = auth()->user();
        $registrations = $user->events()
            ->with('category', 'organizer')
            ->wherePivot('checked_in', false)
            ->get();

        $attended = $user->events()
            ->with('category', 'organizer')
            ->wherePivot('checked_in', true)
            ->get();

        return view('events.my-tickets', compact('registrations', 'attended'));
    }

    public function myEvents()
    {
        $events = auth()->user()->createdEvents()
            ->with('category')
            ->paginate(10);

        return view('events.my-events', compact('events'));
    }

    public function checkInPage(Event $event)
    {
        $this->authorize('update', $event);

        $registrations = $event->users()
            ->select('users.id', 'users.name', 'users.email')
            ->withPivot('unique_code', 'checked_in')
            ->get();

        return view('events.check-in', compact('event', 'registrations'));
    }

    public function checkIn(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        try {
            $this->eventService->checkInTicket($event, $validated['code']);
            return redirect()->back()->with('success', 'Check-in realizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function dashboard()
    {
        $user = auth()->user();
        $upcomingEvents = $user->events()
            ->where('status', 'upcoming')
            ->where('date', '>', now())
            ->count();

        $attendedEvents = $user->events()
            ->wherePivot('checked_in', true)
            ->count();

        if ($user->is_organizer) {
            $myEvents = $user->createdEvents()->where('status', 'upcoming')->count();
            $totalRegistrations = DB::table('registrations')
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->where('events.organizer_id', $user->id)
                ->count();

            return view('events.dashboard', compact('upcomingEvents', 'attendedEvents', 'myEvents', 'totalRegistrations'));
        }

        return view('events.dashboard', compact('upcomingEvents', 'attendedEvents'));
    }

    public function becomeOrganizer(Request $request)
    {
        auth()->user()->update(['is_organizer' => true]);
        return redirect()->route('dashboard')->with('success', '¡Ahora eres organizador! Puedes crear eventos.');
    }
}
