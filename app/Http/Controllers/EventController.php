<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Sport;
use App\Models\SportField;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

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

        $sportFieldOptions = $this->getSportFieldOptionsWithCity();
        $eventTypeOptions = EventType::orderBy('name')->pluck('name', 'event_type_id')->toArray();

        return view('events.index', compact('events', 'sportFieldOptions', 'eventTypeOptions', 'userHasMember'));
    }

    /**
     * Display a listing of all events for admin
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $sportFieldOptions = SportField::orderBy('name')->pluck('name', 'sport_field_id')->toArray();
        $eventTypeOptions = EventType::orderBy('name')->pluck('name', 'event_type_id')->toArray();
        
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

        $sportFieldOptions = $this->getSportFieldOptionsWithCity();
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $eventTypesBySport = $this->getEventTypesBySportOptions();
        $clubsBySport = $this->getClubsBySportOptions();

        return view('panel.events.create', compact('sportFieldOptions', 'sportOptions', 'eventTypesBySport', 'clubsBySport'));
    }

    /**
     * Store new event
     */
    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validated();
        $clubIds = $validated['club_ids'] ?? [];
        unset($validated['club_ids']);

        try {
            $event = Event::create($validated);
            $event->clubs()->sync($clubIds);
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);

            if ($error !== null) {
                return back()
                    ->withInput()
                    ->withErrors($error);
            }

            throw $exception;
        }

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

        $event->load('clubs', 'memberClubs.member.user', 'sportField', 'eventType', 'eventStatistic');

        $activeClubs = $event->activeClubs;
        $activeMembers = $event->activeMembers;
        $activeClubsCount = $activeClubs->count();
        $activeMembersCount = $activeMembers->count();
        $statisticsClubsCount = $event->eventStatistic?->total_teams ?? 0;
        $statisticsMembersCount = $event->eventStatistic?->total_participants ?? 0;

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
            'statisticsClubsCount',
            'statisticsMembersCount',
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
        $event->loadMissing('clubs');

        $sportFieldOptions = $this->getSportFieldOptionsWithCity();
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $eventTypesBySport = $this->getEventTypesBySportOptions();
        $clubsBySport = $this->getClubsBySportOptions();
        $selectedClubIds = $event->clubs->pluck('club_id')->map(fn($id) => (string) $id)->values()->toArray();

        return view('panel.events.edit', compact('event', 'sportFieldOptions', 'sportOptions', 'eventTypesBySport', 'clubsBySport', 'selectedClubIds'));
    }

    /**
     * Update event
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validated();
        $clubIds = $validated['club_ids'] ?? [];
        unset($validated['club_ids']);

        try {
            $event->update($validated);
            $event->clubs()->sync($clubIds);
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);

            if ($error !== null) {
                return back()
                    ->withInput()
                    ->withErrors($error);
            }

            throw $exception;
        }

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

    private function getSportFieldOptionsWithCity(): array
    {
        return SportField::query()
            ->leftJoin('addresses', 'sport_fields.address_id', '=', 'addresses.address_id')
            ->orderBy('sport_fields.name')
            ->selectRaw("sport_fields.sport_field_id, CONCAT(sport_fields.name, ' (', COALESCE(addresses.city, '-'), ')') as label")
            ->pluck('label', 'sport_fields.sport_field_id')
            ->toArray();
    }

    private function getClubsBySportOptions(): array
    {
        return DB::table('club_sport')
            ->join('clubs', 'club_sport.club_id', '=', 'clubs.club_id')
            ->whereNull('clubs.deleted_at')
            ->orderBy('clubs.name')
            ->get([
                'club_sport.sport_id',
                'clubs.club_id',
                'clubs.name',
            ])
            ->groupBy('sport_id')
            ->map(fn($rows) => $rows->pluck('name', 'club_id')->toArray())
            ->toArray();
    }

    private function getEventTypesBySportOptions(): array
    {
        return EventType::query()
            ->orderBy('name')
            ->get(['event_type_id', 'sport_id', 'name'])
            ->groupBy('sport_id')
            ->map(fn($rows) => $rows->pluck('name', 'event_type_id')->toArray())
            ->toArray();
    }

    private function mapEventTriggerError(QueryException $exception): ?array
    {
        $message = strtoupper($exception->getMessage());
        $driverErrorCode = (int) ($exception->errorInfo[1] ?? 0);

        if ($driverErrorCode !== 1644 && !str_contains($message, 'SQLSTATE[45000]')) {
            return null;
        }

        if (str_contains($message, 'FIELD DOES NOT SUPPORT THIS SPORT')) {
            return ['sport_field_id' => 'Selected field does not support selected sport.'];
        }

        if (str_contains($message, 'EVENT CANNOT BE ITS OWN PARENT')) {
            return ['parent_event_id' => 'Event cannot be its own parent event.'];
        }

        if (str_contains($message, 'FIELD IS ALREADY RESERVED AT THIS TIME')) {
            return ['start_date' => 'Selected field is already reserved in this time range.'];
        }

        if (str_contains($message, 'FIELD ALREADY HAS AN EVENT AT THIS TIME')) {
            return ['start_date' => 'Selected field already has an event in this time range.'];
        }

        return ['start_date' => 'Unable to save event due to time conflict or unsupported combination.'];
    }
}