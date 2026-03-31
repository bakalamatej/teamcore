<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Http\Requests\MemberRequest;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\CoachEvaluation;
use App\Enums\MemberClubRole;

class MemberController extends Controller
{
    /**
     * Display a listing of members
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Member::class);

        $members = Member::query()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->with('user')
            ->orderByName()
            ->paginate(15);
        
        return view('members.index', compact('members'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Member::class);
        
        $users = User::doesntHave('member')->orderBy('email')->get();
        return view('members.create', compact('users'));
    }

    /**
     * Store new member
     */
    public function store(MemberRequest $request)
    {
        $this->authorize('create', Member::class);
        
        try {
            $member = Member::create($request->validated());
            return redirect()->route('members.show', $member)->with('success', 'Member created successfully.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to create member.');
        }
    }

    /**
     * Display member details
     */
    public function show(Member $member)
    {
        $this->authorize('view', $member);
        
        $activeEventsCount = Event::whereHas('memberClubs', function ($q) use ($member) {
            $q->where('member_club.member_id', $member->member_id)
            ->whereNull('member_club.left_at');
        })->count();
        
        $member->load('user', 'clubMemberships.club');
        return view('members.show', compact('member', 'activeEventsCount'));
    }

    public function showCoach(Member $member)
    {
        $this->authorize('view', $member);

        $member->load([
            'user',
            'clubMemberships.club',
            'receivedEvaluations.evaluatedByMember.user'
        ]);

        $coachMemberships = $member->clubMemberships()
            ->where('role', \App\Enums\MemberClubRole::COACH->value)
            ->whereNull('left_at')
            ->with('club')
            ->get();

        $evaluations = $member->receivedEvaluations()->latest()->paginate(4);

        $averageRating = $evaluations->avg('rating');
        $evaluationsCount = $evaluations->total();

        return view('clubs.show-coach', compact(
            'member',
            'coachMemberships',
            'evaluations',
            'averageRating',
            'evaluationsCount'
        ));
    }

    /**
     * Show edit form
     */
    public function edit(Member $member)
    {
        $this->authorize('update', $member);
        
        return view('members.edit', compact('member'));
    }

    /**
     * Update member
     */
    public function update(MemberRequest $request, Member $member)
    {
        $this->authorize('update', $member);
        
        try {
            $member->update($request->validated());
            return redirect()->route('members.show', $member)->with('success', 'Member updated successfully.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to update member.');
        }
    }

    /**
     * Delete member
     */
    public function destroy(Member $member)
    {
        $this->authorize('delete', $member);
        
        try {
            $member->delete();
            return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to delete member.');
        }
    }
}
