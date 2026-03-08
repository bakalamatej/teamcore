<?php

namespace App\Http\Controllers;

use App\Models\CoachEvaluation;
use App\Models\Member;
use App\Models\Reservation;
use App\Http\Requests\CoachEvaluationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\MemberClubRole;

class CoachEvaluationController extends Controller
{
    /**
     * Display a listing of coach evaluations
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CoachEvaluation::class);

        $evaluations = CoachEvaluation::when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->with('coach.member', 'evaluatedByMember', 'reservation')
            ->paginate(15);
        
        return view('coach-evaluations.index', compact('evaluations'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', CoachEvaluation::class);

        $coaches = Member::whereHas('clubMemberships', fn($q) =>
            $q->byRole(MemberClubRole::COACH)->active()
        )->get();
        $reservations = Reservation::approved()->orderBy('start_date', 'desc')->limit(100)->get();
        return view('coach-evaluations.create', compact('coaches', 'reservations'));
    }

    /**
     * Store new coach evaluation
     */
    public function store(CoachEvaluationRequest $request)
    {
        $this->authorize('create', CoachEvaluation::class);

        $data = $request->validated();
        $data['evaluated_by_member_id'] = Auth::user()->member?->member_id;

        $evaluation = CoachEvaluation::create($data);
        return redirect()->route('coach-evaluations.show', $evaluation)
            ->with('success', 'Coach evaluation created successfully.');
    }

    /**
     * Display coach evaluation details
     */
    public function show(CoachEvaluation $evaluation)
    {
        $this->authorize('view', $evaluation);

        $evaluation->load('coach', 'evaluatedByMember', 'reservation');
        return view('coach-evaluations.show', compact('evaluation'));
    }

    /**
     * Show edit form
     */
    public function edit(CoachEvaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        return view('coach-evaluations.edit', compact('evaluation'));
    }

    /**
     * Update coach evaluation
     */
    public function update(CoachEvaluationRequest $request, CoachEvaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        $evaluation->update($request->validated());
        return redirect()->route('coach-evaluations.show', $evaluation)->with('success', 'Coach evaluation updated successfully.');
    }

    /**
     * Delete coach evaluation
     */
    public function destroy(CoachEvaluation $evaluation)
    {
        $this->authorize('delete', $evaluation);

        $evaluation->delete();
        return redirect()->route('coach-evaluations.index')->with('success', 'Coach evaluation deleted successfully.');
    }
}
