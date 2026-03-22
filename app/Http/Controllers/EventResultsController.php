<?php
namespace App\Http\Controllers;

use App\Http\Requests\EventResultsRequest;
use App\Models\Event;
use App\Models\EventClubResult;
use App\Models\EventMemberResult;
use App\Models\MemberClub;
use Illuminate\Support\Facades\Auth;

class EventResultsController extends Controller
{
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