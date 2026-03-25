<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Club;
use App\Models\Event;
use App\Models\EventType;
use App\Models\SportField;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoachTournamentController extends Controller
{
    private function getCoachContext()
    {
        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        return [$membership, $club];
    }

    // -------------------------------------------------------
    // INDEX
    // -------------------------------------------------------
    public function index(Request $request)
    {
        [, $club] = $this->getCoachContext();
        abort_if(!$club, 403, 'No club context.');

        $sportId = $club->sport_id;

        $tournamentTypeIds = EventType::where('name', 'like', '%Tournament%')
            ->where('sport_id', $sportId)
            ->pluck('event_type_id');

        $statusOptions = collect(EventStatus::cases())
            ->mapWithKeys(fn($case) => [$case->value => __(ucfirst(strtolower($case->name)))])
            ->toArray();

        $sportFieldOptions = $this->getSportFieldOptionsBySport($sportId);

        $tournaments = Event::whereIn('event_type_id', $tournamentTypeIds)
            ->whereNull('parent_event_id')
            ->whereHas('clubs', fn($q) => $q->where('clubs.sport_id', $sportId))
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'), fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('status'), fn($q) => $q->byStatus($request->input('status')))
            ->with('sportField', 'eventType', 'clubs', 'childEvents')
            ->latest('start_date')
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.coach.tournaments._table', compact('tournaments', 'club'));
        }

        return view('panel.coach.tournaments.index', compact(
            'tournaments', 'sportFieldOptions', 'statusOptions', 'club'
        ));
    }

    // -------------------------------------------------------
    // CREATE
    // -------------------------------------------------------
    public function create()
    {
        $this->authorize('create', Event::class);
        [, $club] = $this->getCoachContext();
        abort_if(!$club, 403, 'No club context.');

        $sportId = $club->sport_id;

        $eventTypeOptions = EventType::where('name', 'like', '%Tournament%')
            ->where('sport_id', $sportId)
            ->orderBy('name')
            ->pluck('name', 'event_type_id')
            ->toArray();

        $clubOptions = Club::where('sport_id', $sportId)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->pluck('name', 'club_id')
            ->toArray();

        $sportFieldOptions = $this->getSportFieldOptionsBySport($sportId);

        $selectedClubIds = [(string) $club->club_id];

        return view('panel.coach.tournaments.create', compact(
            'club', 'eventTypeOptions', 'clubOptions', 'sportFieldOptions', 'selectedClubIds'
        ));
    }

    // -------------------------------------------------------
    // STORE
    // -------------------------------------------------------
    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);
        [, $club] = $this->getCoachContext();
        abort_if(!$club, 403, 'No club context.');

        $validated = $request->validated();
        $clubIds = $validated['club_ids'] ?? [];
        unset($validated['club_ids']);

        try {
            $tournament = Event::create($validated);
            $tournament->clubs()->sync($clubIds);
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

        return redirect()->route('panel.coach.tournaments.show', $tournament)
            ->with('success', 'Tournament created successfully!');
    }

    // -------------------------------------------------------
    // SHOW
    // -------------------------------------------------------
    public function show(Event $tournament)
    {
        $this->authorize('view', $tournament);
        [, $club] = $this->getCoachContext();
        abort_if(!$club, 403, 'No club context.');

        $tournament->load('clubs', 'sportField', 'eventType', 'eventStatistic');

        $statusValue = $tournament->status->value;
        $duration = $tournament->start_date->diff($tournament->end_date);
        $durationText = $duration->days > 0
            ? $duration->days . ' ' . __('day(s)')
            : $duration->h . 'h ' . $duration->i . 'm';

        $childEvents = Event::where('parent_event_id', $tournament->event_id)
            ->with('clubs', 'sportField', 'eventType')
            ->latest('start_date')
            ->paginate(5);

        $canManage = $tournament->clubs->contains('club_id', $club->club_id);

        $availableEventOptions = [];
        if ($canManage) {
            $matchTypeIds = EventType::where('name', 'like', '%Match%')
                ->where('sport_id', $club->sport_id)
                ->pluck('event_type_id');

            $tournamentClubIds = $tournament->clubs->pluck('club_id');

            $availableEvents = Event::whereNull('parent_event_id')
                ->whereIn('event_type_id', $matchTypeIds)
                ->where('event_id', '!=', $tournament->event_id)
                ->whereHas('clubs', fn($q) => $q->whereIn('clubs.club_id', $tournamentClubIds))
                ->whereNotIn('event_id', $childEvents->pluck('event_id'))
                ->with('sportField', 'eventType')
                ->get();

            $availableEventOptions = $availableEvents->mapWithKeys(fn($e) => [
                $e->event_id => $e->title . ' (' . $e->start_date->format('d.m.Y') . ')'
            ])->toArray();
        } else {
            $availableEvents = collect();
        }

        return view('panel.coach.tournaments.show', compact(
            'tournament', 'statusValue', 'durationText',
            'childEvents', 'availableEvents', 'availableEventOptions', 'canManage', 'club'
        ));
    }

    // -------------------------------------------------------
    // EDIT
    // -------------------------------------------------------
    public function edit(Event $tournament)
    {
        $this->authorize('update', $tournament);
        [, $club] = $this->getCoachContext();
        abort_if(!$club, 403, 'No club context.');
        abort_unless($tournament->clubs->contains('club_id', $club->club_id), 403);

        $sportId = $club->sport_id;

        $eventTypeOptions = EventType::where('name', 'like', '%Tournament%')
            ->where('sport_id', $sportId)
            ->orderBy('name')
            ->pluck('name', 'event_type_id')
            ->toArray();

        $clubOptions = Club::where('sport_id', $sportId)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->pluck('name', 'club_id')
            ->toArray();

        $sportFieldOptions = $this->getSportFieldOptionsBySport($sportId);
        $selectedClubIds = $tournament->clubs->pluck('club_id')->map(fn($id) => (string) $id)->values()->toArray();
        $selectedEventTypeId = (string) $tournament->event_type_id;

        return view('panel.coach.tournaments.edit', compact(
            'tournament', 'club', 'eventTypeOptions', 'clubOptions',
            'sportFieldOptions', 'selectedClubIds', 'selectedEventTypeId'
        ));
    }

    // -------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------
    public function update(UpdateEventRequest $request, Event $tournament)
    {
        $this->authorize('update', $tournament);
        [, $club] = $this->getCoachContext();
        abort_if(!$club, 403, 'No club context.');

        $validated = $request->validated();
        $clubIds = $validated['club_ids'] ?? [];
        unset($validated['club_ids']);

        try {
            $tournament->update($validated);
            $tournament->clubs()->sync($clubIds);
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

        return redirect()->route('panel.coach.tournaments.show', $tournament)
            ->with('success', 'Tournament updated successfully!');
    }

    // -------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------
    public function destroy(Event $tournament)
    {
        $this->authorize('delete', $tournament);
        [, $club] = $this->getCoachContext();
        abort_if(!$club, 403, 'No club context.');
        abort_unless($tournament->clubs->contains('club_id', $club->club_id), 403);

        Event::where('parent_event_id', $tournament->event_id)
            ->update(['parent_event_id' => null]);

        $tournament->delete();

        return redirect()->route('panel.coach.tournaments.index')
            ->with('success', 'Tournament deleted successfully!');
    }

    // -------------------------------------------------------
    // CHILD EVENT MANAGEMENT
    // -------------------------------------------------------
    public function attachChild(Request $request, Event $tournament)
    {
        $this->authorize('update', $tournament);

        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,event_id'],
        ]);

        try {
            Event::where('event_id', $validated['event_id'])
                ->update(['parent_event_id' => $tournament->event_id]);
        } catch (QueryException $e) {
            $message = strtoupper($e->getMessage());
            $driverErrorCode = (int) ($e->errorInfo[1] ?? 0);

            if ($driverErrorCode === 1644 || str_contains($message, 'SQLSTATE[45000]')) {
                if (str_contains($message, 'CHILD EVENT DATES MUST BE WITHIN PARENT EVENT INTERVAL')) {
                    return redirect()->route('panel.coach.tournaments.show', $tournament)
                        ->with('error', 'Event dates must be within tournament interval.');
                }
                if (str_contains($message, 'CHILD EVENT SPORT MUST MATCH PARENT EVENT SPORT')) {
                    return redirect()->route('panel.coach.tournaments.show', $tournament)
                        ->with('error', 'Event sport must match tournament sport.');
                }
            }
            throw $e;
        }

        return redirect()->route('panel.coach.tournaments.show', $tournament)
            ->with('success', 'Event attached to tournament.');
    }

    public function detachChild(Event $tournament, Event $event)
    {
        $this->authorize('update', $tournament);
        $event->update(['parent_event_id' => null]);

        return redirect()->route('panel.coach.tournaments.show', $tournament)
            ->with('success', 'Event removed from tournament.');
    }

    public function createChild(Event $tournament)
    {
        $this->authorize('create', Event::class);
        [, $club] = $this->getCoachContext();
        abort_if(!$club, 403, 'No club context.');

        $tournament->loadMissing('clubs');
        $tournamentSportId = EventType::find($tournament->event_type_id)?->sport_id;

        $matchEventType = EventType::where('sport_id', $tournamentSportId)
            ->where('name', 'like', '%Match%')
            ->first();

        $selectedEventTypeId = (string) ($matchEventType?->event_type_id ?? '');

        $eventTypeOptions = EventType::where('sport_id', $tournamentSportId)
            ->orderBy('name')
            ->pluck('name', 'event_type_id')
            ->toArray();

        $clubOptions = $tournament->clubs->pluck('name', 'club_id')->toArray();
        $sportFieldOptions = $this->getSportFieldOptionsBySport($tournamentSportId);
        $selectedClubIds = $tournament->clubs->pluck('club_id')->map(fn($id) => (string) $id)->values()->toArray();

        return view('panel.coach.tournaments.create-child', compact(
            'tournament', 'club', 'eventTypeOptions', 'clubOptions',
            'sportFieldOptions', 'selectedClubIds', 'selectedEventTypeId'
        ));
    }

    public function storeChild(StoreEventRequest $request, Event $tournament)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validated();
        $clubIds = $validated['club_ids'] ?? [];
        unset($validated['club_ids']);
        $validated['parent_event_id'] = $tournament->event_id;

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

        return redirect()->route('panel.coach.tournaments.show', $tournament)
            ->with('success', 'Child event created successfully!');
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------
    private function getSportFieldOptionsBySport(int $sportId): array
    {
        return SportField::whereHas('sports', fn($q) => $q->where('sports.sport_id', $sportId))
            ->leftJoin('addresses', 'sport_fields.address_id', '=', 'addresses.address_id')
            ->orderBy('sport_fields.name')
            ->selectRaw("sport_fields.sport_field_id, CONCAT(sport_fields.name, ' (', COALESCE(addresses.city, '-'), ')') as label")
            ->pluck('label', 'sport_fields.sport_field_id')
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
        if (str_contains($message, 'CHILD EVENT SPORT MUST MATCH PARENT EVENT SPORT')) {
            return ['event_type_id' => 'Event type sport must match tournament sport.'];
        }
        if (str_contains($message, 'CHILD EVENT DATES MUST BE WITHIN PARENT EVENT INTERVAL')) {
            return ['start_date' => 'Event dates must be within tournament interval.'];
        }
        return ['start_date' => 'Unable to save event due to time conflict or unsupported combination.'];
    }
}