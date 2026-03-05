<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Http\Requests\MemberRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $query = Member::query();

        if ($request->filled('search')) {
            $query->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
        }

        $members = $query->with('user')->paginate(15);
        return view('members.index', compact('members'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $users = User::doesntHave('member')->get();
        return view('members.create', compact('users'));
    }

    public function store(MemberRequest $request)
    {
        $member = Member::create($request->validated());
        return redirect()->route('members.show', $member)->with('success', 'Member created successfully.');
    }

    public function show(Member $member)
    {
        $member->load('user', 'clubMemberships.club');
        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        return view('members.edit', compact('member'));
    }

    public function update(MemberRequest $request, Member $member)
    {
        $member->update($request->validated());
        return redirect()->route('members.show', $member)->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }
}
