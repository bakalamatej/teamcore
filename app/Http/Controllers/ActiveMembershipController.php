<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActiveMembershipController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_club_id' => ['required', 'integer'],
        ]);

        $user = $request->user();
        $member = $user?->member;

        if (!$member) {
            return back();
        }

        $memberClubId = (int) $validated['member_club_id'];

        $allowed = $member->clubMemberships()
            ->active()
            ->where('member_club_id', $memberClubId)
            ->exists();

        if (!$allowed) {
            return back()->with('error', 'Selected membership is not available.');
        }

        $request->session()->put('active_member_club_id', $memberClubId);

        return back();
    }
}