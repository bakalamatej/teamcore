<?php

namespace App\Http\Controllers;

use App\Models\MemberClub;
use App\Models\Member;
use App\Models\Club;
use App\Models\Sport;
use App\Http\Requests\MemberClubRequest;
use Illuminate\Http\Request;

class MemberClubController extends Controller
{
    /**
     * Display a listing of member club records
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MemberClub::class);

        $memberClubs = MemberClub::active()
            ->when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->with('member.user', 'club', 'sport')
            ->paginate(15);
        
        return view('member-clubs.index', compact('memberClubs'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', MemberClub::class);

        $members = Member::orderByName()->get();
        $clubs = Club::orderBy('name')->get();
        $sports = Sport::orderBy('name')->get();
        return view('member-clubs.create', compact('members', 'clubs', 'sports'));
    }

    /**
     * Store new member club record
     */
    public function store(MemberClubRequest $request)
    {
        $this->authorize('create', MemberClub::class);

        $memberClub = MemberClub::create($request->validated());
        return redirect()->route('member-clubs.show', $memberClub)->with('success', 'Member added to club successfully.');
    }

    /**
     * Display member club record
     */
    public function show(MemberClub $memberClub)
    {
        $this->authorize('view', $memberClub);

        $memberClub->load('member.user', 'club', 'sport');
        return view('member-clubs.show', compact('memberClub'));
    }

    /**
     * Show edit form
     */
    public function edit(MemberClub $memberClub)
    {
        $this->authorize('update', $memberClub);

        $members = Member::orderByName()->get();
        $clubs = Club::orderBy('name')->get();
        $sports = Sport::orderBy('name')->get();
        return view('member-clubs.edit', compact('memberClub', 'members', 'clubs', 'sports'));
    }

    /**
     * Update member club record
     */
    public function update(MemberClubRequest $request, MemberClub $memberClub)
    {
        $this->authorize('update', $memberClub);

        $memberClub->update($request->validated());
        return redirect()->route('member-clubs.show', $memberClub)->with('success', 'Member club record updated successfully.');
    }

    /**
     * Delete member club record
     */
    public function destroy(MemberClub $memberClub)
    {
        $this->authorize('delete', $memberClub);

        $memberClub->delete();
        return redirect()->route('member-clubs.index')->with('success', 'Member club record deleted successfully.');
    }
}
