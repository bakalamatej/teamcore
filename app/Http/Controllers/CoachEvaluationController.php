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
    public function index(Request $request)
    {
        if (!Auth::user()->member) abort(403);

        $query = CoachEvaluation::query();

        if ($request->filled('search')) {
            $query->whereHas('coach', function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        $evaluations = $query->with('coach', 'evaluatedByMember', 'reservation')->paginate(15);
        return view('coach-evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $coaches = Member::whereHas('clubMemberships', function($q) {
            $q->where('role', 'coach');
        })->get();
        $reservations = Reservation::all();
        return view('coach-evaluations.create', compact('coaches', 'reservations'));
    }

    public function store(CoachEvaluationRequest $request)
    {
        $evaluation = CoachEvaluation::create($request->validated());
        return redirect()->route('coach-evaluations.show', $evaluation)->with('success', 'Coach evaluation created successfully.');
    }

    public function show(CoachEvaluation $evaluation)
    {
        $evaluation->load('coach', 'evaluatedByMember', 'reservation');
        return view('coach-evaluations.show', compact('evaluation'));
    }

    public function edit(CoachEvaluation $evaluation)
    {
        if (!Auth::user()->member || (Auth::user()->member->member_id !== $evaluation->evaluated_by_member_id && !Auth::user()->isAdmin())) {
            abort(403);
        }
        return view('coach-evaluations.edit', compact('evaluation'));
    }

    public function update(CoachEvaluationRequest $request, CoachEvaluation $evaluation)
    {
        $evaluation->update($request->validated());
        return redirect()->route('coach-evaluations.show', $evaluation)->with('success', 'Coach evaluation updated successfully.');
    }

    public function destroy(CoachEvaluation $evaluation)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $evaluation->delete();
        return redirect()->route('coach-evaluations.index')->with('success', 'Coach evaluation deleted successfully.');
    }
}
