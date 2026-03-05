<?php

namespace App\Http\Controllers;

use App\Models\MemberClub;
use App\Models\Member;
use App\Models\Club;
use App\Http\Requests\MemberClubRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberClubController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $query = MemberClub::query();

        if ($request->filled('search')) {
            $query->whereHas('member', function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            })->orWhereHas('club', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $memberClubs = $query->with('member', 'club')->paginate(15);
        return view('member-clubs.index', compact('memberClubs'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $members = Member::all();
        $clubs = Club::all();
        return view('member-clubs.create', compact('members', 'clubs'));
    }

    public function store(MemberClubRequest $request)
    {
        $memberClub = MemberClub::create($request->validated());
        return redirect()->route('member-clubs.show', $memberClub)->with('success', 'Member added to club successfully.');
    }

    public function show(MemberClub $memberClub)
    {
        $memberClub->load('member', 'club');
        return view('member-clubs.show', compact('memberClub'));
    }

    public function edit(MemberClub $memberClub)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        return view('member-clubs.edit', compact('memberClub'));
    }

    public function update(MemberClubRequest $request, MemberClub $memberClub)
    {
        $memberClub->update($request->validated());
        return redirect()->route('member-clubs.show', $memberClub)->with('success', 'Member club record updated successfully.');
    }

    public function destroy(MemberClub $memberClub)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $memberClub->delete();
        return redirect()->route('member-clubs.index')->with('success', 'Member club record deleted successfully.');
    }
}
