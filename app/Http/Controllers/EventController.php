<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Sport;
use App\Models\SportField;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Enums\EventStatus;
use App\Enums\MemberClubRole;
use App\Models\Club;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $activeMembership = Auth::user()?->activeMembership();
        $membershipSportId = $activeMembership?->club?->sport_id;

        $events = $this->buildEventsQuery($activeMembership)
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'), fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('status'), fn($q) => $q->byStatus($request->input('status')))
            ->when($request->filled('type'), fn($q) => $q->byEventType($request->input('type')))
            ->when($request->filled('start_date_from') || $request->filled('start_date_to'),
                fn($q) => $q->byDateRange($request->input('start_date_from'), $request->input('start_date_to')))
            ->with('sportField', 'eventType', 'clubs')
            ->orderByRaw("CASE WHEN start_date >= NOW() THEN 0 ELSE 1 END ASC")
            ->orderByRaw("CASE WHEN start_date >= NOW() THEN start_date END ASC")
            ->orderByRaw("CASE WHEN start_date < NOW() THEN start_date END DESC")
            ->paginate(10);

        $this->decorateEventsWithUserContext($events, $activeMembership);

        $userHasMember = (bool) $activeMembership;

        if ($request->ajax()) {
            return view('events._table', compact('events', 'userHasMember'));
        }

        $sportFieldOptions = $this->getSportFieldOptionsBySport($membershipSportId);
        $eventTypeOptions = $membershipSportId
            ? EventType::where('sport_id', $membershipSportId)->orderBy('name')->pluck('name', 'event_type_id')->toArray()
            : EventType::orderBy('name')->pluck('name', 'event_type_id')->toArray();
        $statusOptions = collect(EventStatus::cases())
            ->mapWithKeys(fn($case) => [$case->value => __(ucfirst(strtolower($case->name)))])
            ->toArray();

        return view('events.index', compact('events', 'sportFieldOptions', 'eventTypeOptions', 'statusOptions', 'userHasMember'));
    }

    private function buildEventsQuery($activeMembership)
    {
        if (!$activeMembership) {
            return Event::query()->whereNull('event_id');
        }

        $clubId = $activeMembership->club_id;
        $sportId = $activeMembership->club?->sport_id;

        return Event::query()->whereHas('clubs', function ($q) use ($clubId, $sportId) {
            $q->where('clubs.club_id', $clubId)
              ->when($sportId, fn($q) => $q->where('clubs.sport_id', $sportId));
        });
    }

    private function decorateEventsWithUserContext($events, $activeMembership)
    {
        $userClubIds = $activeMembership ? [$activeMembership->club_id] : [];
        $memberClubIds = Auth::user()->member?->clubMemberships()->active()->pluck('member_club_id') ?? collect();
        $userEventIds = DB::table('event_member')
            ->whereIn('member_club_id', $memberClubIds)
            ->pluck('event_id')
            ->toArray();

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
    }

    private function getSportFieldOptionsBySport(?int $sportId): array
    {
        $query = SportField::query()
            ->leftJoin('addresses', 'sport_fields.address_id', '=', 'addresses.address_id')
            ->orderBy('sport_fields.name')
            ->selectRaw("sport_fields.sport_field_id, CONCAT(sport_fields.name, ' (', COALESCE(addresses.city, '-'), ')') as label");

        if ($sportId) {
            $query->whereHas('sports', fn($q) => $q->where('sports.sport_id', $sportId));
        }

        return $query->pluck('label', 'sport_fields.sport_field_id')->toArray();
    }

    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $sportFieldOptions = SportField::orderBy('name')->pluck('name', 'sport_field_id')->toArray();
        $eventTypeOptions = EventType::orderBy('name')->pluck('name', 'event_type_id')->toArray();

        $events = Event::when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'), fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('status'), fn($q) => $q->byStatus($request->input('status')))
            ->when($request->filled('type'), fn($q) => $q->byEventType($request->input('type')))
            ->when($request->filled('start_date_from') || $request->filled('start_date_to'),
                fn($q) => $q->byDateRange($request->input('start_date_from'), $request->input('start_date_to')))
            ->with('sportField', 'eventType')
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.admin.events._table', compact('events'));
        }

        return view('panel.admin.events.index', compact('events', 'sportFieldOptions', 'eventTypeOptions'));
    }

    public function create()
    {
        $this->authorize('create', Event::class);

        $sportFieldOptions = $this->getSportFieldOptionsWithCity();
        $eventTypesBySport = $this->getEventTypesBySportOptions();
        $clubsBySport = $this->getClubsBySportOptions();
        $sportFieldsBySport = $this->getSportFieldsGroupedBySport();

        return view('panel.admin.events.create', compact('sportFieldOptions', 'eventTypesBySport', 'clubsBySport', 'sportFieldsBySport'));
    }

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
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'event' => $event], 201);
        }

        return redirect()->route('panel.admin.events.index');
    }

    public function show(Event $event)
    {
        return $this->renderEventShow($event, 'events.show');
    }

    public function adminShow(Event $event)
    {
        return $this->renderAdminEventShow($event, 'panel.admin.events.show');
    }

    private function renderEventShow(Event $event, string $view)
    {
        $this->authorize('view', $event);

        $event->load(
            'clubs',
            'memberClubs.member.user',
            'sportField',
            'eventType',
            'eventStatistic',
            'clubResults.club',
            'memberResults.memberClub.member',
            'memberResults.memberClub.club'
        );

        $activeClubs = $event->activeClubs;
        $clubId = Auth::user()?->activeMembership()?->club_id;

        $activeMembers = $clubId
            ? $event->memberClubs->where('club_id', $clubId)->map(fn($mc) => $mc->member)->filter()->values()
            : collect();

        $activeCoaches = $clubId
            ? $event->memberClubs
                ->where('club_id', $clubId)
                ->filter(fn($mc) => $mc->role === MemberClubRole::COACH && $mc->member !== null)
                ->values()
            : collect();

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
            'event', 'activeClubs', 'activeMembers', 'activeCoaches',
            'activeClubsCount', 'activeMembersCount',
            'statisticsClubsCount', 'statisticsMembersCount',
            'statusValue', 'durationText', 'canManageEvent'
        ));
    }

    private function renderAdminEventShow(Event $event, string $view)
    {
        $this->authorize('view', $event);

        $event->load('clubs', 'memberClubs.member.user', 'memberClubs.club', 'sportField', 'eventType', 'eventStatistic');

        $activeClubs = $event->activeClubs;
        $activeMembers = $event->memberClubs->filter(fn($mc) => $mc->member !== null)->values();

        $activeClubsCount = $activeClubs->count();
        $activeMembersCount = $activeMembers->count();
        $statisticsClubsCount = $event->eventStatistic?->total_teams ?? 0;
        $statisticsMembersCount = $event->eventStatistic?->total_participants ?? 0;
        $statusValue = $event->status->value;
        $duration = $event->start_date->diff($event->end_date);
        $durationText = $duration->days > 0
            ? $duration->days . ' ' . __('day(s)')
            : $duration->h . 'h ' . $duration->i . 'm';
        $canManageEvent = true;

        return view($view, compact(
            'event', 'activeClubs', 'activeMembers',
            'activeClubsCount', 'activeMembersCount',
            'statisticsClubsCount', 'statisticsMembersCount',
            'statusValue', 'durationText', 'canManageEvent'
        ));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $event->loadMissing(['clubs', 'eventType']);

        $sportFieldOptions = $this->getSportFieldOptionsWithCity();
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $eventTypesBySport = $this->getEventTypesBySportOptions();
        $clubsBySport = $this->getClubsBySportOptions();
        $sportFieldsBySport = $this->getSportFieldsGroupedBySport();
        $selectedClubIds = $event->clubs->pluck('club_id')->map(fn($id) => (string) $id)->values()->toArray();
        $selectedSport = $event->eventType?->sport_id;

        return view('panel.admin.events.edit', compact(
            'event', 'sportFieldOptions', 'sportOptions', 'eventTypesBySport',
            'clubsBySport', 'sportFieldsBySport', 'selectedClubIds', 'selectedSport'
        ));
    }

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
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

        return redirect()->route('panel.admin.events.index');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();
        return redirect()->route('panel.admin.events.index');
    }

    public function register(Event $event)
    {
        $this->authorize('register', $event);
        $memberClub = Auth::user()?->activeMembership();
        $eventSportId = $event->sport?->sport_id;

        if ($memberClub && (int) ($memberClub->club?->sport_id ?? null) !== (int) ($eventSportId ?? 0)) {
            $memberClub = null;
        }

        if ($memberClub && !$event->clubs()->where('clubs.club_id', $memberClub->club_id)->exists()) {
            $memberClub = null;
        }

        if (!$memberClub) {
            return redirect()->back()->with('error', 'You are not a member of any club participating in this event.');
        }

        $memberClub->events()->syncWithoutDetaching([$event->event_id]);
        return redirect()->route('events.index')->with('success', 'You have successfully registered for the event!');
    }

    public function unregister(Event $event)
    {
        $this->authorize('unregister', $event);
        $memberClub = Auth::user()?->activeMembership();
        $eventSportId = $event->clubs->first()?->sport_id ?? $event->sportField?->sports->first()?->sport_id;

        if ($memberClub && (int) ($memberClub->club?->sport_id ?? null) !== (int) ($eventSportId ?? 0)) {
            $memberClub = null;
        }

        if ($memberClub && !$event->clubs()->where('clubs.club_id', $memberClub->club_id)->exists()) {
            $memberClub = null;
        }

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
        return Club::query()
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['club_id', 'sport_id', 'name'])
            ->groupBy('sport_id')
            ->map(fn($rows) => $rows->pluck('name', 'club_id')->toArray())
            ->toArray();
    }

    private function getSportFieldsGroupedBySport(): array
    {
        $sportFields = SportField::query()
            ->whereNull('deleted_at')
            ->with(['sports', 'address'])
            ->whereHas('sports')
            ->get();

        $result = [];

        foreach ($sportFields as $sportField) {
            $label = $sportField->name;
            if ($sportField->address?->city) {
                $label .= ' (' . $sportField->address->city . ')';
            }

            foreach ($sportField->sports as $sport) {
                $result[$sport->sport_id][$sportField->sport_field_id] = $label;
            }
        }

        return $result;
    }

    private function getEventTypesBySportOptions(): array
    {
        return EventType::query()
            ->with('sport')
            ->orderBy('name')
            ->get(['event_type_id', 'sport_id', 'name'])
            ->groupBy('sport_id')
            ->map(function ($rows) {
                return $rows->mapWithKeys(function ($eventType) {
                    $sportName = $eventType->sport?->name ?? __('Unknown sport');
                    return [$eventType->event_type_id => $eventType->name . ' - ' . $sportName];
                })->toArray();
            })->toArray();
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