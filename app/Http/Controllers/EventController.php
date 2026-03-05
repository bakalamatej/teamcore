<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\EventType;
use App\Models\SportField;
use App\Http\Requests\EventRequest; 

class EventController extends Controller
{
    /**
     * Display a listing of user's events
     */
    /**
     * Display a listing of user's events
     */
    public function index(Request $request)
    {
        $sportFields = SportField::all();
        $eventTypes = EventType::all();
        
        $events = Auth::user()->member?->myEvents() ?? Event::query()->whereRaw('1=0');
        $events = $events
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'), 
                fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('status'), 
                fn($q) => $q->byStatus($request->input('status')))
            ->when($request->filled('type'), 
                fn($q) => $q->byEventType($request->input('type')))
            ->with('sportField', 'eventType')
            ->paginate(10);

        return view('events.index', compact('events', 'sportFields', 'eventTypes'));
    }

    /**
     * Display a listing of all events for admin
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $sportFields = SportField::all();
        $eventTypes = EventType::all();
        
        $events = Event::active()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'), 
                fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('status'), 
                fn($q) => $q->byStatus($request->input('status')))
            ->when($request->filled('type'), 
                fn($q) => $q->byEventType($request->input('type')))
            ->with('sportField', 'eventType')
            ->paginate(10);

        return view('panel.events.index', compact('events', 'sportFields', 'eventTypes'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Event::class);

        $sportFields = SportField::all();
        $eventTypes = EventType::all();
        return view('panel.events.create', compact('sportFields', 'eventTypes'));
    }

    /**
     * Store new event
     */
    public function store(EventRequest $request)
    {
        $this->authorize('create', Event::class);

        $event = Event::create($request->validated());

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'event' => $event], 201);
        }

        return redirect()->route('events.index');
    }

    /**
     * Display event details
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event->load('clubs', 'members', 'sportField', 'eventType');
        return view('events.show', compact('event'));
    }

    /**
     * Show edit form
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $sportFields = SportField::all();
        $eventTypes = EventType::all();

        return view('panel.events.edit', compact('event', 'sportFields', 'eventTypes'));
    }

    /**
     * Update event
     */
    public function update(EventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        return redirect()->route('events.index');
    }

    /**
     * Delete event
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();
        return redirect()->route('events.index');
    }

    /**
     * Register user to event
     */
    public function register(Event $event)
    {
        $user = Auth::user();
        if (!$user || !$user->member) {
            abort(403);
        }

        $member = $user->member;
        
        $userClubIds = $member->activeClubs()->pluck('club_id')->toArray();
        $eventClubIds = $event->clubs()->pluck('club_id')->toArray();
        
        $eventBelongsToUserClub = !empty(array_intersect($userClubIds, $eventClubIds));
        
        if (!$eventBelongsToUserClub) {
            abort(403, 'This event does not belong to your club');
        }

        $alreadyRegistered = $member->events()
            ->where('event_id', $event->event_id)
            ->exists();
        
        if ($alreadyRegistered) {
            return redirect()->route('events.index')->with('info', 'You are already registered for this event');
        }

        $existingEntry = DB::table('member_event')
            ->where('member_club_id', $member->clubMemberships->first()?->member_club_id)
            ->where('event_id', $event->event_id)
            ->first();

        if ($existingEntry) {
            DB::table('member_event')
                ->where('member_club_id', $member->clubMemberships->first()?->member_club_id)
                ->where('event_id', $event->event_id)
                ->update(['updated_at' => now()]);
        } else {
            $member->clubMemberships->first()?->events()->attach($event->event_id);
        }

        return redirect()->route('events.index')->with('success', 'You have successfully registered for the event!');
    }

    /**
     * Unregister user from event
     */
    public function unregister(Event $event)
    {
        $user = Auth::user();
        if (!$user || !$user->member) {
            abort(403);
        }

        $member = $user->member;

        // Check if user is registered for this event
        $isRegistered = $member->events()
            ->where('event_id', $event->event_id)
            ->exists();

        if (!$isRegistered) {
            return redirect()->route('events.index')->with('info', 'You are not registered for this event');
        }

        // Soft delete the registration by setting deleted_at
        DB::table('member_event')
            ->where('member_club_id', $member->clubMemberships->first()?->member_club_id)
            ->where('event_id', $event->event_id)
            ->update(['deleted_at' => now(), 'updated_at' => now()]);

        return redirect()->route('events.index')->with('success', 'You have successfully unregistered from the event!');
    }
}