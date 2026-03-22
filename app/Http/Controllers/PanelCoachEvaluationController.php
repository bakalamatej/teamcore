<?php

namespace App\Http\Controllers;

use App\Enums\MemberClubRole;
use App\Models\CoachEvaluation;
use App\Models\Member;
use App\Models\MemberClub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return view('panel.admin.coach-evaluations._table', compact('members'));
        }

        return view('panel.admin.coach-evaluations.index', compact('members'));
    }

    /**
     * Show all evaluations received by the current coach
     */
    public function myIndex(Request $request)
    {
        $this->authorize('viewAny', CoachEvaluation::class);
        $coachMemberId = Auth::user()?->member?->member_id;
        $evaluations = CoachEvaluation::whereHas('coach', fn($q) => $q->where('member_id', $coachMemberId))
            ->when($request->filled('search'), fn($q) => $q->searchByEvaluator($request->input('search')))
            ->with('coach.member', 'evaluatedByMember', 'reservation')
            ->orderByDesc('created_at')
            ->paginate(15);

        if ($request->ajax()) {
            return view('panel.coach.my-evaluations._table', ['evaluations' => $evaluations]);
        }

        return view('panel.coach.my-evaluations.index', ['evaluations' => $evaluations]);
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

        return view('panel.admin.coach-evaluations.show', compact(
            'member',
            'activeCoachClubs',
            'averageRating',
            'ratings'
        ));
    }
}
