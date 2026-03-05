<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Http\Requests\MemberRequest;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of members
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Member::class);

        $members = Member::active()
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
        
        $users = User::doesntHave('member')->get();
        return view('members.create', compact('users'));
    }

    /**
     * Store new member
     */
    public function store(MemberRequest $request)
    {
        $this->authorize('create', Member::class);
        
        $member = Member::create($request->validated());
        return redirect()->route('members.show', $member)->with('success', 'Member created successfully.');
    }

    /**
     * Display member details
     */
    public function show(Member $member)
    {
        $this->authorize('view', $member);
        
        $member->load('user', 'clubMemberships.club');
        return view('members.show', compact('member'));
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
        
        $member->update($request->validated());
        return redirect()->route('members.show', $member)->with('success', 'Member updated successfully.');
    }

    /**
     * Delete member
     */
    public function destroy(Member $member)
    {
        $this->authorize('delete', $member);
        
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }
}
