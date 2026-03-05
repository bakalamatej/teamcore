<?php

namespace App\Http\Controllers;

use App\Models\CoachEvaluation;
use App\Models\Member;
use App\Models\Reservation;
use App\Http\Requests\CoachEvaluationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoachEvaluationController extends Controller
{
    /**
     * Display a listing of coach evaluations
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CoachEvaluation::class);

        $evaluations = CoachEvaluation::active()
            ->when($request->filled('search'), function($q) use ($request) {
                return $q->whereHas('coach', function($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
                });
            })
            ->with('coach', 'evaluatedByMember', 'reservation')
            ->paginate(15);
        
        return view('coach-evaluations.index', compact('evaluations'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', CoachEvaluation::class);

        $coaches = Member::whereHas('clubMemberships', function($q) {
            $q->where('role', 'coach');
        })->get();
        $reservations = Reservation::all();
        return view('coach-evaluations.create', compact('coaches', 'reservations'));
    }

    /**
     * Store new coach evaluation
     */
    public function store(CoachEvaluationRequest $request)
    {
        $this->authorize('create', CoachEvaluation::class);

        $evaluation = CoachEvaluation::create($request->validated());
        return redirect()->route('coach-evaluations.show', $evaluation)->with('success', 'Coach evaluation created successfully.');
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
