<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\MemberStatistic;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user?->member;
        $activeMembership = $user?->activeMembership();

        if (!$member || !$activeMembership) {
            return view('dashboard.index', [
                'upcomingEvent' => null,
                'latestResults' => collect(),
                'myResults' => collect(),
                'memberStatistics' => null,
            ]);
        }

        $upcomingEvent = Event::whereHas('memberClubs', function($q) use ($activeMembership) {
            $q->where('event_member.member_club_id', $activeMembership->member_club_id);
        })
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->with(['clubs', 'eventType', 'sportField'])
            ->first();

        $latestEvent = Event::whereHas('memberClubs', function($q) use ($activeMembership) {
        $q->where('event_member.member_club_id', $activeMembership->member_club_id);
            })
            ->where('end_date', '<', now())
            ->orderBy('end_date', 'desc')
            ->with([
                'clubResults.club',
                'memberResults.memberClub.club',
            ])
            ->first();

        $latestResults = $latestEvent?->clubResults
            ->filter(fn($result) => $result->club_id === $activeMembership->club_id)
            ?? collect();

        $myResults = $latestEvent?->memberResults
            ->filter(fn($result) => $result->member_club_id === $activeMembership->member_club_id)
            ?? collect();

        $memberStatistics = MemberStatistic::where('member_club_id', $activeMembership->member_club_id)
            ->first();

        return view('dashboard.index', compact(
            'upcomingEvent',
            'latestEvent',
            'latestResults',
            'myResults',
            'memberStatistics',
            'activeMembership'
        ));
    }
}