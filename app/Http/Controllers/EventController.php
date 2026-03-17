<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventType;
use App\Models\SportField;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display a listing of user's events
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $events = Auth::user()->member?->events() ?? Event::query()->whereNull('event_id');
        $events = $events
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'), 
                fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('status'), 
                fn($q) => $q->byStatus($request->input('status')))
            ->when($request->filled('type'), 
                fn($q) => $q->byEventType($request->input('type')))
            ->when($request->filled('start_date_from') || $request->filled('start_date_to'),
                fn($q) => $q->byDateRange($request->input('start_date_from'), $request->input('start_date_to')))
            ->with('sportField', 'eventType', 'clubs')
            ->paginate(10);   

        $userClubIds = Auth::user()->member?->activeClubs()->pluck('clubs.club_id')->toArray() ?? [];
        $memberClubIds = Auth::user()->member?->clubMemberships()->active()->pluck('member_club_id') ?? collect();
        $userEventIds = DB::table('event_member')
            ->whereIn('member_club_id', $memberClubIds)
            ->pluck('event_id')
            ->toArray();
        $userHasMember = (bool) Auth::user()->member;

        $userClubLookup = array_flip($userClubIds);
        $registeredEventLookup = array_flip($userEventIds);

        $events->getCollection()->transform(function ($event) use ($userClubLookup, $registeredEventLookup) {
            $eventClubIds = $event->clubs->pluck('club_id')->toArray();
            $eventBelongsToUserClub = !empty(array_intersect(array_keys($userClubLookup), $eventClubIds));
            $isRegistered = isset($registeredEventLookup[$event->event_id]);

            $event->setAttribute('eventBelongsToUserClub', $eventBelongsToUserClub);
            $event->setAttribute('isRegistered', $isRegistered);
            $event->setAttribute('canRegister', $eventBelongsToUserClub && !$isRegistered);
            $event->setAttribute('canUnregister', $eventBelongsToUserClub && $isRegistered);

            return $event;
        });

        if ($request->ajax()) {
            return view('events._table', compact('events', 'userHasMember'));
        }  

        $sportFields = SportField::with('address')->orderBy('name')->get();
        $eventTypes = EventType::orderBy('name')->get();
        $sportFieldOptions = $sportFields
            ->mapWithKeys(fn($field) => [
                $field->sport_field_id => $field->name . ' (' . ($field->address->city ?? '-') . ')',
            ])
            ->toArray();
        $eventTypeOptions = $eventTypes->pluck('name', 'event_type_id')->toArray();

        return view('events.index', compact('events', 'sportFieldOptions', 'eventTypeOptions', 'userHasMember'));
    }

    /**
     * Display a listing of all events for admin
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $sportFields = SportField::with('address')->orderBy('name')->get();
        $eventTypes = EventType::orderBy('name')->get();
        $sportFieldOptions = $sportFields->pluck('name', 'sport_field_id')->toArray();
        $eventTypeOptions = $eventTypes->pluck('name', 'event_type_id')->toArray();
        
        $events = Event::when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'),
                fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('status'),
                fn($q) => $q->byStatus($request->input('status')))
            ->when($request->filled('type'),
                fn($q) => $q->byEventType($request->input('type')))
            ->when($request->filled('start_date_from') || $request->filled('start_date_to'),
                fn($q) => $q->byDateRange($request->input('start_date_from'), $request->input('start_date_to')))
            ->with('sportField', 'eventType')
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.events._table', compact('events'));
        }

        return view('panel.events.index', compact('events', 'sportFieldOptions', 'eventTypeOptions'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Event::class);

        $sportFields = SportField::with('address')->orderBy('name')->get();
        $eventTypes = EventType::orderBy('name')->get();
        $sportFieldOptions = $sportFields
            ->mapWithKeys(fn($field) => [
                $field->sport_field_id => $field->name . ' (' . ($field->address->city ?? '-') . ')',
            ])
            ->toArray();
        $eventTypeOptions = $eventTypes->pluck('name', 'event_type_id')->toArray();

        return view('panel.events.create', compact('sportFieldOptions', 'eventTypeOptions'));
    }

    /**
     * Store new event
     */
    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);

        $event = Event::create($request->validated());
        $event->clubs()->sync($request->validated()['club_ids']);

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
        return $this->renderEventShow($event, 'events.show');
    }

    /**
     * Display event details for admin panel
     */
    public function adminShow(Event $event)
    {
        return $this->renderEventShow($event, 'panel.events.show');
    }

    private function renderEventShow(Event $event, string $view)
    {
        $this->authorize('view', $event);

        $event->load('clubs', 'memberClubs.member.user', 'sportField', 'eventType');

        $activeClubs = $event->activeClubs;
        $activeMembers = $event->activeMembers;
        $activeClubsCount = $activeClubs->count();
        $activeMembersCount = $activeMembers->count();

        $statusValue = $event->status->value;
        $duration = $event->start_date->diff($event->end_date);
        $durationText = $duration->days > 0
            ? $duration->days . ' ' . __('day(s)')
            : $duration->h . 'h ' . $duration->i . 'm';

        $canManageEvent = Auth::user()?->isAdmin() || Auth::user()?->isCoach();

        return view($view, compact(
            'event',
            'activeClubs',
            'activeMembers',
            'activeClubsCount',
            'activeMembersCount',
            'statusValue',
            'durationText',
            'canManageEvent'
        ));
    }

    /**
     * Show edit form
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $sportFields = SportField::orderBy('name')->get();
        $eventTypes = EventType::orderBy('name')->get();
        $sportFieldOptions = $sportFields
            ->mapWithKeys(fn($field) => [
                $field->sport_field_id => $field->name . ' (' . ($field->address->city ?? '-') . ')',
            ])
            ->toArray();
        $eventTypeOptions = $eventTypes->pluck('name', 'event_type_id')->toArray();

        return view('panel.events.edit', compact('event', 'sportFieldOptions', 'eventTypeOptions'));
    }

    /**
     * Update event
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());
        $event->clubs()->sync($request->validated()['club_ids']);

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
        $this->authorize('register', $event);
        $memberClub = Auth::user()->member?->clubMemberships()
            ->whereIn('club_id', $event->clubs->pluck('club_id'))
            ->active()
            ->first();

        if (!$memberClub) {
            return redirect()->back()->with('error', 'You are not a member of any club participating in this event.');
        }

        $memberClub->events()->syncWithoutDetaching([$event->event_id]);
        return redirect()->route('events.index')->with('success', 'You have successfully registered for the event!');
    }

    /**
     * Unregister user from event
     */
    public function unregister(Event $event)
    {
        $this->authorize('unregister', $event);
        $memberClub = Auth::user()->member?->clubMemberships()
            ->whereIn('club_id', $event->clubs->pluck('club_id'))
            ->active()
            ->first();

        if (!$memberClub) {
            return redirect()->back()->with('error', 'You are not registered for this event.');
        }

        $memberClub->events()->detach($event->event_id);
        return redirect()->route('events.index')->with('success', 'You have successfully unregistered from the event!');
    }
}