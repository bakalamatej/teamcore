<?php

namespace App\Http\Controllers;

use App\Models\MemberClub;
use App\Enums\MemberClubRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FileCategory;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $membership = $user?->activeMembership();
        $club = $membership?->club;
        abort_if(!$club, 403, 'No club context.');

        $roleOptions = collect(MemberClubRole::cases())
            ->mapWithKeys(fn($role) => [$role->value => __(ucfirst($role->value))])
            ->toArray();

        $query = MemberClub::with(['member.user'])
            ->where('club_id', $club->club_id)
            ->active();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('email', 'like', "%$search%") ;
                  });
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $players = $query->orderByDesc('joined_at')->paginate(15);
        if ($request->ajax()) {
            return view('panel.coach.players._table', compact('players'));
        }
        return view('panel.coach.players.index', compact('players', 'roleOptions'));
    }

    public function show(MemberClub $player)
    {
        $this->authorize('view', $player);
        $player->load(['member.user', 'member.memberStatistics']);
        $primaryRole = $player->role->value ?? (string) $player->role;
        $fileCategories = \App\Models\FileCategory::orderBy('name')
            ->get(['file_category_id', 'name'])
            ->toArray();

        $clubStat = $player->member?->memberStatistics->firstWhere('member_club_id', $player->member_club_id);
        return view('panel.coach.players.show', compact('player', 'primaryRole', 'clubStat', 'fileCategories'));
    }

    public function destroy(MemberClub $player)
    {
        $this->authorize('delete', $player);
        $player->left_at = now();
        $player->save();
        return redirect()->route('panel.coach.players.index')->with('success', __('Player membership ended.'));
    }
}