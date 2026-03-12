<?php

namespace App\Http\Controllers;

use App\Enums\MemberClubRole;
use App\Models\CoachEvaluation;
use App\Models\Member;
use App\Models\MemberClub;
use Illuminate\Http\Request;

class PanelCoachEvaluationController extends Controller
{
    /**
     * Display coaches list (members that are coach in at least one club membership).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Member::class);

        $members = Member::query()
            ->whereHas('clubMemberships', fn($query) => $query->byRole(MemberClubRole::COACH))
            ->when(
                $request->filled('search'),
                fn($query) => $query->search($request->input('search'))
            )
            ->withAvg('coachEvaluations as average_rating', 'rating')
            ->orderByName()
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.coach-evaluations._table', compact('members'));
        }

        return view('panel.coach-evaluations.index', compact('members'));
    }

    /**
     * Display coach rating detail for selected member.
     */
    public function show(Member $member)
    {
        $this->authorize('view', $member);

        $coachMemberships = MemberClub::query()
            ->byMember($member->member_id)
            ->byRole(MemberClubRole::COACH)
            ->with('club')
            ->get();

        $coachMembershipIds = $coachMemberships->pluck('member_club_id');

        $activeCoachClubs = $coachMemberships->filter(fn($memberClub) => $memberClub->left_at === null);

        $averageRating = CoachEvaluation::query()
            ->whereIn('coach_member_club_id', $coachMembershipIds)
            ->avg('rating');

        $ratings = CoachEvaluation::query()
            ->whereIn('coach_member_club_id', $coachMembershipIds)
            ->with(['evaluatedByMember', 'coach.club'])
            ->orderByDate()
            ->get();

        return view('panel.coach-evaluations.show', compact(
            'member',
            'activeCoachClubs',
            'averageRating',
            'ratings'
        ));
    }
}
