<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Club;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Sport;
use App\Models\SportField;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TournamentController extends Controller
{
    // -------------------------------------------------------
    // INDEX
    // -------------------------------------------------------
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $sportFieldOptions = $this->getSportFieldOptionsWithCity();
        $statusOptions = collect(EventStatus::cases())
            ->mapWithKeys(fn($case) => [$case->value => __(ucfirst(strtolower($case->name)))])
            ->toArray();

        $tournamentTypeIds = EventType::where('name', 'like', '%Tournament%')
            ->pluck('event_type_id');

        $tournaments = Event::whereIn('event_type_id', $tournamentTypeIds)
            ->whereNull('parent_event_id')
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'), fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('status'), fn($q) => $q->byStatus($request->input('status')))
            ->when($request->filled('start_date_from') || $request->filled('start_date_to'),
                fn($q) => $q->byDateRange($request->input('start_date_from'), $request->input('start_date_to')))
            ->with('sportField', 'eventType', 'clubs', 'childEvents')
            ->latest('start_date')
            ->paginate(6);

        if ($request->ajax()) {
            return view('panel.admin.tournaments._table', compact('tournaments'));
        }

        return view('panel.admin.tournaments.index', compact(
            'tournaments', 'sportFieldOptions', 'statusOptions'
        ));
    }

    // -------------------------------------------------------
    // CREATE
    // -------------------------------------------------------
    public function create()
    {
        $this->authorize('create', Event::class);

        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $eventTypesBySport = $this->getTournamentTypesBySport();
        $clubsBySport = $this->getClubsBySportOptions();
        $sportFieldsBySport = $this->getSportFieldsBySport();

        return view('panel.admin.tournaments.create', compact(
            'sportOptions', 'eventTypesBySport', 'clubsBySport', 'sportFieldsBySport'
        ));
    }

    // -------------------------------------------------------
    // STORE
    // -------------------------------------------------------
    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);

        try {
            $validated = $request->validated();
            $clubIds = $validated['club_ids'] ?? [];
            unset($validated['club_ids']);
            $tournament = Event::create($validated);
            $tournament->clubs()->sync($clubIds);
            return redirect()->route('panel.admin.tournaments.show', $tournament)
                ->with('success', 'Tournament created successfully!');
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }
    }

    // -------------------------------------------------------
    // SHOW
    // -------------------------------------------------------
    public function show(Event $tournament)
    {
        $this->authorize('view', $tournament);

        $tournament->load(
            'clubs', 'sportField', 'eventType', 'eventStatistic',
            'childEvents.clubs', 'childEvents.sportField', 'childEvents.eventType'
        );

        $statusValue = $tournament->status->value;
        $duration = $tournament->start_date->diff($tournament->end_date);
        $durationText = $duration->days > 0
            ? $duration->days . ' ' . __('day(s)')
            : $duration->h . 'h ' . $duration->i . 'm';

        $childEvents = Event::where('parent_event_id', $tournament->event_id)
            ->with('clubs', 'sportField', 'eventType')
            ->latest('start_date')
            ->paginate(3);

        $tournamentClubIds = $tournament->clubs->pluck('club_id');
        $matchTypeIds = EventType::where('name', 'like', '%Match%')
            ->pluck('event_type_id');

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

        return view('panel.admin.tournaments.show', compact(
            'tournament', 'statusValue', 'durationText',
            'childEvents', 'availableEvents', 'availableEventOptions'
        ));
    }

    // -------------------------------------------------------
    // EDIT
    // -------------------------------------------------------
    public function edit(Event $tournament)
    {
        $this->authorize('update', $tournament);
        $tournament->loadMissing('clubs');

        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $eventTypesBySport = $this->getTournamentTypesBySport();
        $clubsBySport = $this->getClubsBySportOptions();
        $sportFieldsBySport = $this->getSportFieldsBySport();
        $selectedClubIds = $tournament->clubs->pluck('club_id')->map(fn($id) => (string) $id)->values()->toArray();
        $selectedSport = (string) (EventType::find($tournament->event_type_id)?->sport_id ?? '');

        return view('panel.admin.tournaments.edit', compact(
            'tournament', 'sportOptions', 'eventTypesBySport',
            'clubsBySport', 'sportFieldsBySport', 'selectedClubIds', 'selectedSport'
        ));
    }

    // -------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------
    public function update(UpdateEventRequest $request, Event $tournament)
    {
        $this->authorize('update', $tournament);

        try {
            $validated = $request->validated();
            $clubIds = $validated['club_ids'] ?? [];
            unset($validated['club_ids']);
            $tournament->update($validated);
            $tournament->clubs()->sync($clubIds);

            return redirect()->route('panel.admin.tournaments.index', $tournament)->with('success', 'Tournament updated successfully!');
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

        
    }

    // -------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------
    public function destroy(Event $tournament)
    {
        $this->authorize('delete', $tournament);

        try {
            Event::where('parent_event_id', $tournament->event_id)
                ->update(['parent_event_id' => null]);

            $tournament->delete();

            return redirect()->route('panel.admin.tournaments.index')->with('success', 'Tournament deleted successfully!');
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

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
            
            return redirect()->route('panel.admin.tournaments.show', $tournament)->with('success', 'Event attached to tournament.');
        } catch (QueryException $e) {
            $message = strtoupper($e->getMessage());
            $driverErrorCode = (int) ($e->errorInfo[1] ?? 0);

            if ($driverErrorCode === 1644 || str_contains($message, 'SQLSTATE[45000]')) {
                if (str_contains($message, 'CHILD EVENT DATES MUST BE WITHIN PARENT EVENT INTERVAL')) {
                    return redirect()->route('panel.admin.tournaments.show', $tournament)
                        ->with('error', 'Event dates must be within tournament interval.');
                }
                if (str_contains($message, 'CHILD EVENT SPORT MUST MATCH PARENT EVENT SPORT')) {
                    return redirect()->route('panel.admin.tournaments.show', $tournament)
                        ->with('error', 'Event sport must match tournament sport.');
                }
            }
            throw $e;
        }

  
    }

    public function detachChild(Event $tournament, Event $event)
    {
        $this->authorize('update', $tournament);

        $event->update(['parent_event_id' => null]);

        return redirect()->route('panel.admin.tournaments.show', $tournament)
            ->with('success', 'Event removed from tournament.');
    }

    public function createChild(Event $tournament)
    {
        $this->authorize('create', Event::class);

        $tournament->loadMissing('clubs');

        $tournamentSportId = EventType::find($tournament->event_type_id)?->sport_id;

        $matchEventType = EventType::where('sport_id', $tournamentSportId)
            ->where('name', 'like', '%Match%')
            ->first();

        $selectedEventTypeId = (string) ($matchEventType?->event_type_id ?? '');

        $eventTypeOptions = EventType::where('sport_id', $tournamentSportId)
            ->where('name', 'like', '%Match%')
            ->orderBy('name')
            ->pluck('name', 'event_type_id')
            ->toArray();

        $clubOptions = $tournament->clubs
            ->pluck('name', 'club_id')
            ->toArray();

        $sportFieldOptions = SportField::whereHas('sports', fn($q) => $q->where('sports.sport_id', $tournamentSportId))
            ->leftJoin('addresses', 'sport_fields.address_id', '=', 'addresses.address_id')
            ->orderBy('sport_fields.name')
            ->selectRaw("sport_fields.sport_field_id, CONCAT(sport_fields.name, ' (', COALESCE(addresses.city, '-'), ')') as label")
            ->pluck('label', 'sport_fields.sport_field_id')
            ->toArray();

        $selectedClubIds = $tournament->clubs->pluck('club_id')->map(fn($id) => (string) $id)->values()->toArray();

        return view('panel.admin.tournaments.create-child', compact(
            'tournament', 'eventTypeOptions', 'clubOptions', 'selectedEventTypeId', 'sportFieldOptions', 'selectedClubIds'
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

        return redirect()->route('panel.admin.tournaments.show', $tournament)
            ->with('success', 'Child event created successfully!');
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------
    private function getSportFieldOptionsWithCity(): array
    {
        return SportField::query()
            ->leftJoin('addresses', 'sport_fields.address_id', '=', 'addresses.address_id')
            ->orderBy('sport_fields.name')
            ->selectRaw("sport_fields.sport_field_id, CONCAT(sport_fields.name, ' (', COALESCE(addresses.city, '-'), ')') as label")
            ->pluck('label', 'sport_fields.sport_field_id')
            ->toArray();
    }

    private function getSportFieldsBySport(): array
    {
        return SportField::with('sports')
            ->get()
            ->flatMap(fn($field) => $field->sports->map(fn($sport) => [
                'sport_id' => (string) $sport->sport_id,
                'sport_field_id' => (string) $field->sport_field_id,
                'name' => $field->name,
            ]))
            ->groupBy('sport_id')
            ->map(fn($rows) => $rows->pluck('name', 'sport_field_id')->toArray())
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

    private function getTournamentTypesBySport(): array
    {
        return EventType::query()
            ->where('name', 'like', '%Tournament%')
            ->orderBy('name')
            ->with('sport')
            ->get(['event_type_id', 'sport_id', 'name'])
            ->groupBy('sport_id')
            ->map(fn($rows) => $rows->mapWithKeys(fn($row) => [
                $row->event_type_id => $row->name . ' - ' . ($row->sport?->name ?? '—')
            ])->toArray())
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
        if (str_contains($message, 'CHILD EVENT SPORT MUST MATCH PARENT EVENT SPORT')) {
            return ['event_type_id' => 'Event type sport must match tournament sport.'];
        }
        if (str_contains($message, 'CHILD EVENT DATES MUST BE WITHIN PARENT EVENT INTERVAL')) {
            return ['start_date' => 'Event dates must be within tournament interval.'];
        }
        return ['start_date' => 'Unable to save event due to time conflict or unsupported combination.'];
    }
}