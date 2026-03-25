<?php
namespace App\Http\Controllers;

use App\Http\Requests\EventResultsRequest;
use App\Models\Event;
use App\Models\EventClubResult;
use App\Models\EventMemberResult;
use App\Models\MemberClub;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\EventType;

class EventResultsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return view('panel.results.index', [
                'results' => collect(),
                'clubOptions' => [],
                'eventTypeOptions' => [],
                'selectedMemberClubId' => null,
                'selectedEventTypeId' => null,
            ]);
        }

        $memberships = MemberClub::where('member_id', $member->member_id)
            ->with('club')
            ->get();

        $sportIds = $memberships->pluck('club.sport_id')->filter()->unique()->values();
        $eventTypeOptions = EventType::whereIn('sport_id', $sportIds)
            ->orderBy('name')
            ->pluck('name', 'event_type_id')
            ->toArray();

        $clubOptions = $memberships->mapWithKeys(fn($m) => [
            $m->member_club_id => $m->club->name . ' (' . ($m->left_at ? __('former') : __('active')) . ')'
        ])->toArray();

        $selectedMemberClubId = $request->input('member_club_id');
        $selectedEventTypeId = $request->input('event_type_id');

        $resultsQuery = EventMemberResult::whereIn('member_club_id', $memberships->pluck('member_club_id'))
            ->with(['event.sportField', 'event.eventType', 'memberClub.club']);

        if ($selectedMemberClubId) {
            $resultsQuery->where('member_club_id', $selectedMemberClubId);
        }
        if ($selectedEventTypeId) {
            $resultsQuery->whereHas('event', fn($q) => $q->where('event_type_id', $selectedEventTypeId));
        }

        $results = $resultsQuery->get()->sortByDesc(fn($r) => $r->event?->start_date);

        if ($request->ajax()) {
            return view('panel.results._table', compact('results'));
        }

        return view('panel.results.index', compact('results', 'clubOptions', 'selectedMemberClubId', 'eventTypeOptions', 'selectedEventTypeId'));
    }

    public function edit(Event $event)
    {
        $this->authorize('editResults', $event);

        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');
        $event->loadMissing('clubs');
        abort_unless($event->clubs->contains('club_id', $club->club_id), 403);

        $clubResult = EventClubResult::where('event_id', $event->event_id)
            ->where('club_id', $club->club_id)
            ->first();

        $memberClubs = MemberClub::where('club_id', $club->club_id)
            ->whereNull('left_at')
            ->whereHas('events', fn($q) => $q->where('events.event_id', $event->event_id))
            ->with('member')
            ->get();

        $memberResults = EventMemberResult::where('event_id', $event->event_id)
            ->whereIn('member_club_id', $memberClubs->pluck('member_club_id'))
            ->get()
            ->keyBy('member_club_id');

        return view('panel.coach.results.edit', compact(
            'event', 'club', 'clubResult', 'memberClubs', 'memberResults'
        ));
    }

    public function adminEdit(Event $event)
    {
        $this->authorize('editResults', $event);

        $clubs = $event->clubs()->get()->filter();

        $clubResults = EventClubResult::where('event_id', $event->event_id)
            ->get()
            ->keyBy('club_id');

        $memberClubs = MemberClub::whereIn('club_id', $clubs->pluck('club_id'))
            ->whereHas('events', fn($q) => $q->where('events.event_id', $event->event_id))
            ->with('member')
            ->get()
            ->groupBy('club_id');

        $memberResults = EventMemberResult::where('event_id', $event->event_id)
            ->whereIn('member_club_id', $memberClubs->flatten()->pluck('member_club_id'))
            ->get()
            ->keyBy('member_club_id');

        return view('panel.admin.results.edit', compact(
            'event', 'clubs', 'clubResults', 'memberClubs', 'memberResults'
        ));
    }

    public function adminStore(EventResultsRequest $request, Event $event)
    {
        $this->authorize('storeResults', $event);

        foreach ($request->input('clubs', []) as $clubId => $data) {
            EventClubResult::updateOrCreate(
                ['event_id' => $event->event_id, 'club_id' => $clubId],
                [
                    'value' => $data['value'] ?? null,
                    'result_type' => $data['result_type'] ?? null,
                    'ranking' => $data['ranking'] ?? null,
                    'note' => $data['note'] ?? null,
                ]
            );
        }

        foreach ($request->input('members', []) as $memberClubId => $result) {
            EventMemberResult::updateOrCreate(
                ['event_id' => $event->event_id, 'member_club_id' => $memberClubId],
                [
                    'value' => $result['value'] ?? null,
                    'result_type' => $result['result_type'] ?? null,
                    'ranking' => $result['ranking'] ?? null,
                    'note' => $result['note'] ?? null,
                ]
            );
        }

        return redirect()->route('panel.admin.events.show', $event)->with('success', 'Results saved successfully!');
    }

    public function store(EventResultsRequest $request, Event $event)
    {
        $this->authorize('storeResults', $event);

        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');
        $event->loadMissing('clubs');
        abort_unless($event->clubs->contains('club_id', $club->club_id), 403);

        EventClubResult::updateOrCreate(
            ['event_id' => $event->event_id, 'club_id' => $club->club_id],
            [
                'value' => $request->input('club_value'),
                'result_type' => $request->input('club_result_type'),
                'ranking' => $request->input('club_ranking'),
                'note' => $request->input('club_note'),
            ]
        );

        foreach ($request->input('members', []) as $memberClubId => $result) {
            EventMemberResult::updateOrCreate(
                ['event_id' => $event->event_id, 'member_club_id' => $memberClubId],
                [
                    'value' => $result['value'] ?? null,
                    'result_type' => $result['result_type'] ?? null,
                    'ranking' => $result['ranking'] ?? null,
                    'note' => $result['note'] ?? null,
                ]
            );
        }

        return redirect()->route('panel.coach.events.show', $event)->with('success', 'Results saved successfully!');
    }
}