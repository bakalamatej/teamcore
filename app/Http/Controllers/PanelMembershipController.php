<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberClubRequest;
use App\Http\Requests\UpdateMembershipRequest;
use App\Models\Club;
use App\Models\Member;
use App\Models\MemberClub;
use App\Models\Sport;
use Illuminate\Http\Request;

class PanelMembershipController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', MemberClub::class);

        $clubs = Club::orderBy('name')->get();
        $clubOptions = $clubs->pluck('name', 'club_id')->toArray();

        $members = Member::with([
                'user',
                'clubMemberships' => fn($q) => $q->whereNull('left_at')->with('club', 'sport'),
            ])
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->when($request->filled('club_id'), fn($q) => $q->whereHas('clubMemberships', fn($q2) =>
                $q2->whereNull('left_at')->where('club_id', $request->input('club_id'))
            ))
            ->when($request->filled('role'), fn($q) => $q->whereHas('clubMemberships', fn($q2) =>
                $q2->whereNull('left_at')->where('role', $request->input('role'))
            ))
            ->orderByName()
            ->paginate(15);

        if ($request->ajax()) {
            return view('panel.memberships._table', compact('members'));
        }

        return view('panel.memberships.index', compact('members', 'clubOptions'));
    }

    public function edit(Member $member)
    {
        $this->authorize('viewAny', MemberClub::class);

        $member->load('user');

        $allMemberships = $member->clubMemberships()
            ->whereNull('left_at')
            ->with(['club.sports', 'sport'])
            ->get();

        $memberSportIds = $allMemberships->pluck('sport_id')->filter()->unique()->values();

        $allSports = Sport::orderBy('name')->get();
        $allSportOptions = $allSports->pluck('name', 'sport_id')->toArray();

        $membershipSportOptions = $allMemberships
            ->mapWithKeys(fn($membership) => [
                $membership->member_club_id => $membership->club->sports->pluck('name', 'sport_id')->toArray(),
            ])
            ->toArray();

        $allClubsWithSports = Club::with('sports')->orderBy('name')->get()
            ->mapWithKeys(fn($club) => [
                (string) $club->club_id => [
                    'name'   => $club->name,
                    'sports' => $club->sports->pluck('sport_id')->values()->toArray(),
                ],
            ]);

        return view('panel.memberships.edit', compact(
            'member',
            'allMemberships',
            'memberSportIds',
            'allSports',
            'allSportOptions',
            'allClubsWithSports',
            'membershipSportOptions',
        ));
    }

    public function update(UpdateMembershipRequest $request, Member $member)
    {
        $validated = $request->validated();

        foreach ($validated['memberships'] ?? [] as $mcId => $data) {
            MemberClub::where('member_club_id', $mcId)
                ->where('member_id', $member->member_id)
                ->update([
                    'role'     => $data['role'],
                    'sport_id' => $data['sport_id'],
                ]);
        }

        return redirect()->route('panel.memberships.edit', $member)->with('success', 'Memberships updated.');
    }

    public function storeMemberClub(StoreMemberClubRequest $request, Member $member)
    {
        $this->authorize('create', MemberClub::class);

        MemberClub::create([
            'member_id' => $member->member_id,
            'club_id'   => $request->validated('club_id'),
            'sport_id'  => $request->validated('sport_id'),
            'role'      => $request->validated('role'),
            'joined_at' => $request->validated('joined_at'),
        ]);

        return redirect()->route('panel.memberships.edit', $member)->with('success', 'Membership added.');
    }
}
