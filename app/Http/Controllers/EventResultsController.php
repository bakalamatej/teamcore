<?php
namespace App\Http\Controllers;

use App\Http\Requests\EventResultsRequest;
use App\Models\Event;
use App\Models\EventClubResult;
use App\Models\EventMemberResult;
use App\Models\MemberClub;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
                'selectedMemberClubId' => null,
            ]);
        }

        $memberships = MemberClub::where('member_id', $member->member_id)
            ->with('club')
            ->get();

        $clubOptions = $memberships->mapWithKeys(fn($m) => [
            $m->member_club_id => $m->club->name . ' (' . ($m->left_at ? __('former') : __('active')) . ')'
        ])->toArray();

        $selectedMemberClubId = $request->input('member_club_id');

        $resultsQuery = EventMemberResult::whereIn('member_club_id', $memberships->pluck('member_club_id'))
            ->with(['event.sportField', 'event.eventType', 'memberClub.club']);

        if ($selectedMemberClubId) {
            $resultsQuery->where('member_club_id', $selectedMemberClubId);
        }

        $results = $resultsQuery->get()->sortByDesc(fn($r) => $r->event?->start_date);

        if ($request->ajax()) {
            return view('panel.results._table', compact('results'));
        }

        return view('panel.results.index', compact('results', 'clubOptions', 'selectedMemberClubId'));
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

    // ADMIN: Edit results for all clubs and members
    public function adminEdit(Event $event)
    {
        $this->authorize('editResults', $event);

        $clubs = $event->clubs()->get()->filter();

        $clubResults = EventClubResult::where('event_id', $event->event_id)
            ->get()
            ->keyBy('club_id');

        // Get all member clubs that participated in the event (across all clubs)
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

    // ADMIN: Store results for all clubs and members
    public function adminStore(EventResultsRequest $request, Event $event)
    {
        $this->authorize('storeResults', $event);

        // Save all club results
        foreach ($request->input('clubs', []) as $clubId => $data) {
                EventClubResult::updateOrCreate(
                ['event_id' => $event->event_id, 'club_id' => $clubId],
                [
                    'score' => $data['score'] ?? null,
                    'ranking' => $data['ranking'] ?? null,
                    'note' => $data['note'] ?? null,
                ]
            );
        }

        // Save all member results
        foreach ($request->input('members', []) as $memberClubId => $result) {
                EventMemberResult::updateOrCreate(
                ['event_id' => $event->event_id, 'member_club_id' => $memberClubId],
                [
                    'score' => $result['score'] ?? null,
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
                'score' => $request->input('club_score'),
                'ranking' => $request->input('club_ranking'),
                'note' => $request->input('club_note'),
            ]
        );

        foreach ($request->input('members', []) as $memberClubId => $result) {
            EventMemberResult::updateOrCreate(
                ['event_id' => $event->event_id, 'member_club_id' => $memberClubId],
                [
                    'score' => $result['score'] ?? null,
                    'ranking' => $result['ranking'] ?? null,
                    'note' => $result['note'] ?? null,
                ]
            );
        }

        return redirect()->route('panel.coach.events.show', $event)->with('success', 'Results saved successfully!');
    }
}