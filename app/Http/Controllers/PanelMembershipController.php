<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberClubRequest;
use App\Models\Club;
use App\Models\Member;
use App\Models\MemberClub;
use App\Enums\MemberClubRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelMembershipController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', MemberClub::class);

        $clubOptions = Club::orderBy('name')->pluck('name', 'club_id')->toArray();
        $roleOptions = collect(MemberClubRole::cases())
            ->mapWithKeys(fn($role) => [$role->value => __(ucfirst($role->value))])
            ->toArray();

        $memberships = MemberClub::query()
            ->with(['member.user', 'club', 'sport'])
            ->whereNull('left_at')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->whereHas('member', function ($memberQuery) use ($search) {
                    $memberQuery
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($userQuery) => $userQuery->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('club_id'), fn($query) => $query->where('club_id', $request->input('club_id')))
            ->when($request->filled('role'), fn($query) => $query->where('role', $request->input('role')))
            ->latest('member_club_id')
            ->paginate(15);

        if ($request->ajax()) {
            return view('panel.admin.memberships._table', compact('memberships'));
        }

        return view('panel.admin.memberships.index', compact('memberships', 'clubOptions', 'roleOptions'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', MemberClub::class);

        $memberOptions = $this->memberOptions();
        $clubOptions = Club::orderBy('name')->pluck('name', 'club_id')->toArray();
        $sportsByClub = $this->sportsByClub();
        $selectedMemberId = old('member_id', $request->input('member_id'));

        return view('panel.admin.memberships.create', compact(
            'memberOptions',
            'clubOptions',
            'sportsByClub',
            'selectedMemberId'
        ));
    }

    public function store(MemberClubRequest $request)
    {
        $this->authorize('create', MemberClub::class);

        MemberClub::create($request->validated());

        return redirect()->route('panel.admin.memberships.index')->with('success', 'Membership created successfully.');
    }

    public function edit(MemberClub $memberClub)
    {
        $this->authorize('update', $memberClub);

        $memberClub->load(['member.user', 'club', 'sport']);
        $clubOptions = Club::orderBy('name')->pluck('name', 'club_id')->toArray();
        $sportsByClub = $this->sportsByClub();

        return view('panel.admin.memberships.edit', compact('memberClub', 'clubOptions', 'sportsByClub'));
    }

    public function update(MemberClubRequest $request, MemberClub $memberClub)
    {
        $this->authorize('update', $memberClub);

        $memberClub->update($request->validated());

        return redirect()->route('panel.admin.memberships.edit', $memberClub)->with('success', 'Membership updated.');
    }

    public function destroy(MemberClub $memberClub)
    {
        $this->authorize('delete', $memberClub);

        $memberClub->update([
            'left_at' => now(),
        ]);

        return redirect()->route('panel.admin.memberships.index')->with('success', 'Membership ended successfully.');
    }

    private function memberOptions(): array
    {
        return Member::query()
            ->with('user:user_id,email')
            ->orderByName()
            ->get()
            ->mapWithKeys(function (Member $member) {
                $label = $member->full_name;

                if ($member->user?->email) {
                    $label .= ' (' . $member->user->email . ')';
                }

                return [(string) $member->member_id => $label];
            })
            ->toArray();
    }

    private function sportsByClub(): array
    {
        $rows = DB::table('club_sport')
            ->join('sports', 'club_sport.sport_id', '=', 'sports.sport_id')
            ->orderBy('sports.name')
            ->get([
                'club_sport.club_id',
                'sports.sport_id',
                'sports.name',
            ]);

        $result = [];

        foreach ($rows as $row) {
            $result[(string) $row->club_id][(string) $row->sport_id] = $row->name;
        }

        return $result;
    }
}
