<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TournamentResultsController extends Controller
{
    public function show(Event $tournament)
    {
        $this->authorize('view', $tournament);

        $membership = Auth::user()?->activeMembership();

        $tournament->load([
            'eventType',
            'childEvents.clubResults.club',
            'childEvents.memberResults.memberClub.member',
            'childEvents.memberResults.memberClub.club',
            'childEvents.clubs',
            'childEvents.eventType',
        ]);

        return view('events.tournament-results', compact('tournament', 'membership'));
    }

    public function publicShow(Event $tournament)
    {
        $this->authorize('view', $tournament);

        $tournament->load([
            'clubs.address',
            'sportField.address',
            'eventType',
            'childEvents.clubs',
            'childEvents.sportField',
            'childEvents.eventType',
        ]);

        $statusValue = $tournament->status->value;

        $duration = $tournament->start_date->diff($tournament->end_date);
        $durationText = $duration->days > 0
            ? $duration->days . ' ' . __('day(s)')
            : $duration->h . 'h ' . $duration->i . 'm';

        $childEvents = $tournament->getAllChildEvents();

        return view('events.tournament-show', compact(
            'tournament',
            'statusValue',
            'durationText',
            'childEvents'
        ));
    }
}