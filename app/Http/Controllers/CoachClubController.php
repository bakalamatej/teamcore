<?php

namespace App\Http\Controllers;

use App\Enums\MemberClubRole;
use App\Models\Club;
use App\Models\Address;
use App\Models\Sport;
use App\Http\Requests\ClubRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoachClubController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Club::class);

        $user = Auth::user();
        $member = $user->member;
        $myClub = $member?->activeClubs()?->first();
        $mySportIds = $myClub ? $myClub->sports->pluck('sport_id')->all() : [];
        $membership = $user?->activeMembership();
        $canManageClubId = ($membership && $membership->role === \App\Enums\MemberClubRole::COACH)
            ? $membership->club_id
            : null;

        $cities = Address::distinct()->orderBy('city')->pluck('city');
        $cityOptions = $cities->combine($cities)->all();

        $sports = Sport::whereHas('clubs')->orderBy('name')->pluck('name', 'sport_id')->toArray();
        $sportOptions = $sports;

        $clubs = Club::whereHas('sports', function($q) use ($mySportIds) {
                $q->whereIn('sports.sport_id', $mySportIds);
            })
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('city'), 
                fn($q) => $q->byCity($request->input('city')))
            ->when($request->filled('sport'), 
                fn($q) => $q->bySport($request->input('sport')))
            ->with('address', 'sports', 'members')
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.coach.clubs._table', compact('clubs', 'myClub', 'canManageClubId'));
        }

        return view('panel.coach.clubs.index', compact('clubs', 'cityOptions', 'sportOptions', 'myClub', 'canManageClubId'));
    }

    /**
     * Display club details
     */
    public function show(Club $club)
    {
        $this->authorize('view', $club);
        return $this->renderClubShow($club, 'panel.coach.clubs.show');
    }

    /**
     * Show edit form
     */
    public function edit(Club $club)
    {
        $this->authorize('update', $club);
        $addressOptions = Address::query()
            ->orderBy('city')
            ->orderBy('street')
            ->selectRaw("address_id, TRIM(CONCAT(COALESCE(CONCAT(street, ', '), ''), COALESCE(city, ''))) as label")
            ->pluck('label', 'address_id')
            ->toArray();
        $countryOptions = Address::query()
            ->select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country', 'country')
            ->toArray();
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $club->load('sports');
        $selectedSportIds = $club->sports->pluck('sport_id')->toArray();

        return view('panel.coach.clubs.edit', compact('club', 'addressOptions', 'countryOptions', 'sportOptions', 'selectedSportIds'));
    }

    /**
     * Update club
     */
    public function update(ClubRequest $request, Club $club)
    {
        $this->authorize('update', $club);

        $validated = $request->validated();
        $primarySportId = (int) ($validated['sport_ids'][0] ?? 0);

        $addressId = $validated['address_id'] ?? null;
        if (!$addressId) {
            $address = Address::firstOrCreate([
                'country'  => $validated['country'],
                'city'     => $validated['city'],
                'street'   => $validated['street'] ?? null,
                'zip_code' => $validated['zip_code'] ?? null,
            ]);
            $addressId = $address->address_id;  
        }

        $club->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'webpage' => $validated['webpage'] ?? null,
            'address_id' => $addressId,
            'sport_id' => $primarySportId,
        ]);

        $club->sports()->sync($validated['sport_ids']);

        return redirect()->route('panel.coach.clubs.index')->with('success', 'Club updated successfully!');
    }

    private function renderClubShow(Club $club, $view = 'panel.clubs.show')
    {
        $club->load('address', 'clubStatistic');
        $stats = $club->clubStatistic;

        $activeMembers = $club->members()
            ->wherePivotNull('left_at')
            ->with('user')
            ->get()
            ->map(function ($member) {
                $role = $member->pivot->role;
                $member->setAttribute(
                    'roleValue',
                    $role instanceof MemberClubRole ? $role->value : (string) $role
                );
                return $member;
            });

        $activeEvents = $club->events()
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($event) {
                $status = $event->status;
                $event->setAttribute('statusValue', is_object($status) && isset($status->value) ? $status->value : (string) $status);
                return $event;
            });

        $activeMembersCount = $activeMembers->count();
        $activeEventsCount = $activeEvents->count();
        $statisticsMembersCount = $club->clubStatistic?->active_members ?? 0;
        $statisticsMatchesPlayedCount = $club->clubStatistic?->matches_played ?? 0;
        $statisticsTotalWinsCount = $club->clubStatistic?->total_wins ?? 0;
        $statisticsTotalLossesCount = $club->clubStatistic?->total_losses ?? 0;
        $recentEvents = $activeEvents->take(5);
        $moreEventsCount = max($activeEventsCount - 5, 0);
        $coaches = $activeMembers->where('roleValue', MemberClubRole::COACH->value)->values();
        $user = Auth::user();
        $canManageClub = false;
        if ($user && $user->member) {
            $membership = $user->activeMembership();
            if ($membership && (int)$membership->club_id === (int)$club->club_id && $membership->role === \App\Enums\MemberClubRole::COACH) {
                $canManageClub = true;
            }
        }

        return view($view, compact(
            'club',
            'activeMembers',
            'activeMembersCount',
            'activeEvents',
            'activeEventsCount',
            'statisticsMembersCount',
            'statisticsMatchesPlayedCount',
            'statisticsTotalWinsCount',
            'statisticsTotalLossesCount',
            'recentEvents',
            'moreEventsCount',
            'coaches',
            'canManageClub',
            'stats'
        ));
    }
}
