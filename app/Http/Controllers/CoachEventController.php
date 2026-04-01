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
use App\Enums\EventStatus;

class CoachEventController extends Controller
{
    public function index(Request $request)
    {
        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');

        $events = Event::whereHas('clubs', fn($q) => $q->where('clubs.club_id', $club->club_id))
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_field_id'), fn($q) => $q->bySportField($request->input('sport_field_id')))
            ->when($request->filled('event_type_id'), fn($q) => $q->where('event_type_id', $request->input('event_type_id')))
            ->with('sportField', 'eventType', 'clubs')
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.coach.events._table', compact('events'));
        }

        $sportFieldOptions = $this->getSportFieldOptionsBySport($club->sport_id);
        $eventTypeOptions = $this->getEventTypesBySportOptions()[$club->sport_id] ?? [];

        return view('panel.coach.events.index', compact('events', 'sportFieldOptions', 'eventTypeOptions'));
    }

    public function create()
    {
        $this->authorize('create', Event::class);

        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');

        $sportFieldOptions = $this->getSportFieldOptionsBySport($club->sport_id);
        $eventTypeOptions = $this->getEventTypesBySportOptions()[$club->sport_id] ?? [];
        $clubOptions = $this->getClubsBySportOptions($club->club_id)[$club->sport_id] ?? [];

        return view('panel.coach.events.create', compact('sportFieldOptions', 'eventTypeOptions', 'clubOptions', 'club'));
    }

    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);

        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');

        $validated = $request->validated();
        $clubIds = $validated['club_ids'] ?? [$club->club_id];
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

        return redirect()->route('panel.coach.events.index');
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');
        $event->loadMissing('clubs');
        abort_unless($event->clubs->contains('club_id', $club->club_id), 403);

        $event->load('clubs', 'memberClubs.member.user', 'sportField', 'eventType', 'eventStatistic');
        $activeClubs = $event->activeClubs;
        $activeMembers = $event->memberClubs
            ->where('club_id', $club->club_id)
            ->map(fn($mc) => $mc->member)
            ->filter()
            ->values();
        $activeClubsCount = $activeClubs->count();
        $activeMembersCount = $activeMembers->count();
        $statisticsClubsCount = $event->eventStatistic?->total_teams ?? 0;
        $statisticsMembersCount = $event->eventStatistic?->total_participants ?? 0;
        $statusValue = $event->status->value;
        $duration = $event->start_date->diff($event->end_date);
        $durationText = $duration->days > 0
            ? $duration->days . ' ' . __('day(s)')
            : $duration->h . 'h ' . $duration->i . 'm';

        $canManageEvent = $event->clubs->contains('club_id', $club->club_id);

        return view('panel.coach.events.show', compact(
            'event', 'activeClubs', 'activeMembers', 'activeClubsCount',
            'activeMembersCount', 'statisticsClubsCount', 'statisticsMembersCount',
            'statusValue', 'durationText', 'canManageEvent'
        ));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');
        $event->loadMissing('clubs');
        abort_unless($event->clubs->contains('club_id', $club->club_id), 403);

        $sportFieldOptions = $this->getSportFieldOptionsBySport($club->sport_id);
        $eventTypeOptions = $this->getEventTypesBySportOptions()[$club->sport_id] ?? [];
        $clubOptions = $this->getClubsBySportOptions($club->club_id)[$club->sport_id] ?? [];
        $selectedClubIds = $event->clubs->pluck('club_id')->map(fn($id) => (string) $id)->values()->toArray();

        $memberOptions = \App\Models\MemberClub::where('club_id', $club->club_id)
            ->whereNull('left_at')
            ->with('member')
            ->get()
            ->mapWithKeys(fn($mc) => [(string) $mc->member_club_id => $mc->member?->full_name ?? '-'])
            ->toArray();

        $selectedMemberIds = $event->memberClubs()
            ->where('member_club.club_id', $club->club_id)
            ->pluck('event_member.member_club_id')
            ->map(fn($id) => (string) $id)
            ->values()
            ->toArray();

        return view('panel.coach.events.edit', compact(
            'event', 'sportFieldOptions', 'eventTypeOptions', 'clubOptions',
            'selectedClubIds', 'memberOptions', 'selectedMemberIds'
        ));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');
        $event->loadMissing('clubs');
        abort_unless($event->clubs->contains('club_id', $club->club_id), 403);

        $validated = $request->validated();
        $clubIds = $validated['club_ids'] ?? [$club->club_id];
        $memberClubIds = $request->input('member_club_ids', []);
        unset($validated['club_ids']);

        try {
            $event->update($validated);
            $event->clubs()->sync($clubIds);

            // Sync členov — len z tohto klubu
            $currentClubMemberIds = $event->memberClubs()
                ->where('member_club.club_id', $club->club_id)
                ->pluck('event_member.member_club_id')
                ->toArray();

            $event->memberClubs()->detach($currentClubMemberIds);
            if (!empty($memberClubIds)) {
                $event->memberClubs()->syncWithoutDetaching($memberClubIds);
            }
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

        return redirect()->route('panel.coach.events.index');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');
        $event->loadMissing('clubs');
        abort_unless($event->clubs->contains('club_id', $club->club_id), 403);

        try {
            $event->delete();
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

        return redirect()->route('panel.coach.events.index');
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

    private function getEventTypesBySportOptions(): array
    {
        return EventType::query()
            ->orderBy('name')
            ->get(['event_type_id', 'sport_id', 'name'])
            ->groupBy('sport_id')
            ->map(fn($rows) => $rows->pluck('name', 'event_type_id')->toArray())
            ->toArray();
    }

    private function getSportFieldOptionsBySport(int $sportId): array
    {
        return SportField::whereHas('sports', fn($q) => $q->where('sports.sport_id', $sportId))
            ->leftJoin('addresses', 'sport_fields.address_id', '=', 'addresses.address_id')
            ->orderBy('sport_fields.name')
            ->selectRaw("sport_fields.sport_field_id, CONCAT(sport_fields.name, ' (', COALESCE(addresses.city, '-'), ')') as label")
            ->pluck('label', 'sport_fields.sport_field_id')
            ->toArray();
    }

    private function getClubsBySportOptions(int $clubId): array
    {
        $club = Club::find($clubId);
        if (!$club || !$club->sport) return [];

        $result = [];
        $clubs = Club::where('sport_id', $club->sport_id)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['club_id', 'name']);

        $result[$club->sport_id] = $clubs->pluck('name', 'club_id')->toArray();

        return $result;
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
        if (str_contains($message, 'FIELD IS ALREADY RESERVED AT THIS TIME')) {
            return ['start_date' => 'Selected field is already reserved in this time range.'];
        }
        if (str_contains($message, 'FIELD ALREADY HAS AN EVENT AT THIS TIME')) {
            return ['start_date' => 'Selected field already has an event in this time range.'];
        }
        return ['start_date' => 'Unable to save event due to time conflict or unsupported combination.'];
    }
}