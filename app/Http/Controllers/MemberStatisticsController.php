<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MemberStatistic;
use App\Models\MemberClub;

class MemberStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        $emptyAggregated = [
            'events_attended' => 0,
            'training_sessions' => 0,
            'matches_played' => 0,
            'tournaments_attended' => 0,
            'total_wins' => 0,
        ];

        if (!$member) {
            return view('panel.statistics.index', [
                'stats' => collect(),
                'aggregated' => $emptyAggregated,
                'totalAggregated' => $emptyAggregated,
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

        // Always total across all memberships
        $allStats = MemberStatistic::whereIn('member_club_id', $memberships->pluck('member_club_id'))->get();
        $totalAggregated = [
            'events_attended'      => $allStats->sum('events_attended'),
            'training_sessions'    => $allStats->sum('training_sessions'),
            'matches_played'       => $allStats->sum('matches_played'),
            'tournaments_attended' => $allStats->sum('tournaments_attended'),
            'total_wins'           => $allStats->sum('total_wins'),
        ];

        // Filtered for table
        $statsQuery = MemberStatistic::whereIn('member_club_id', $memberships->pluck('member_club_id'))
            ->with('memberClub.club');

        if ($selectedMemberClubId) {
            $statsQuery->where('member_club_id', $selectedMemberClubId);
        }

        $stats = $statsQuery->get();

        if ($request->ajax()) {
            return view('panel.statistics._table', compact('stats'));
        }

        return view('panel.statistics.index', compact(
            'stats', 'totalAggregated', 'clubOptions', 'selectedMemberClubId'
        ));
    }
}