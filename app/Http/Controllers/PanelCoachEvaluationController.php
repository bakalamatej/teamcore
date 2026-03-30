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
        $this->authorize('viewAny', CoachEvaluation::class);

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
     * Show all evaluations given by the current coach
     */
    public function receivedIndex(Request $request)
    {
        $this->authorize('viewAny', CoachEvaluation::class);
        $coachMemberId = Auth::user()?->member?->member_id;

        $evaluations = CoachEvaluation::where('coach_member_id', $coachMemberId)
            ->when($request->filled('search'), fn($q) => $q->searchByEvaluator($request->input('search')))
            ->with('coach', 'evaluatedByMember')
            ->orderByDesc('created_at')
            ->paginate(15);

        if ($request->ajax()) {
            return view('panel.coach.received-evaluations._table', ['evaluations' => $evaluations]);
        }

        return view('panel.coach.received-evaluations.index', ['evaluations' => $evaluations]);
    }

    /**
     * Show all evaluations received by the current member
     */
    public function myIndex(Request $request)
    {
        $member = Auth::user()?->member;

        if (!$member) {
            return view('panel.my-evaluations.index', [
                'evaluations' => collect(),
                'averageRating' => null,
            ]);
        }

        $evaluations = CoachEvaluation::where('evaluated_by_member_id', $member->member_id)
            ->when($request->filled('search'), fn($q) => $q->searchByCoach($request->input('search')))
            ->with('coach', 'evaluatedByMember')
            ->orderByDesc('created_at')
            ->paginate(7);

        if ($request->ajax()) {
            return view('panel.my-evaluations._table', compact('evaluations'));
        }

        return view('panel.my-evaluations.index', compact('evaluations'));
    }

    /**
     * Display coach rating detail for selected member.
     */
    public function show(Member $member)
    {
        $this->authorize('view', CoachEvaluation::class);

        $averageRating = CoachEvaluation::where('coach_member_id', $member->member_id)
            ->avg('rating');

        $ratings = CoachEvaluation::where('coach_member_id', $member->member_id)
            ->with(['evaluatedByMember', 'coach'])
            ->orderByDate()
            ->paginate(5);

        $coachMemberships = MemberClub::query()
            ->where('member_id', $member->member_id)
            ->byRole(MemberClubRole::COACH)
            ->with('club')
            ->get();

        $activeCoachClubs = $coachMemberships->filter(fn($memberClub) => $memberClub->left_at === null);

        return view('panel.admin.coach-evaluations.show', compact(
            'member',
            'activeCoachClubs',
            'averageRating',
            'ratings'
        ));
    }

    /**
     * Show form to edit evaluation
     */
    public function editEvaluation(CoachEvaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        return view('panel.my-evaluations.edit', compact('evaluation'));
    }

    /**
     * Update evaluation
     */
    public function updateEvaluation(Request $request, CoachEvaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        $validated = $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $evaluation->update($validated);

        return redirect()->route('panel.my-evaluations.index')->with('success', 'Evaluation updated successfully!');
    }

    /**
     * Delete evaluation
     */
    public function destroyEvaluation(CoachEvaluation $evaluation)
    {
        $this->authorize('delete', $evaluation);

        try {
            $evaluation->delete();
            return redirect()->route('panel.my-evaluations.index')->with('success', 'Evaluation deleted successfully!');

        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to delete evaluation.');
        }

    }
}