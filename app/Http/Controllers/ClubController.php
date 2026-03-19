<?php

namespace App\Http\Controllers;

use App\Enums\MemberClubRole;
use App\Models\Club;
use App\Models\Address;
use App\Models\Sport;
use App\Http\Requests\ClubRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    /**
     * Display clubs where the authenticated member is currently active.
     */
    public function myClub()
    {
        $membership = Auth::user()?->activeMembership();
        $club = $membership?->club;

        abort_if(!$club, 404, 'You are not part of any active club.');

        $this->authorize('view', $club);

        return $this->renderClubShow($club, 'clubs.my-club');
    }

    /**
     * Display a listing of all clubs for admin
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Club::class);

        $cities = Address::distinct()->orderBy('city')->pluck('city');
        $cityOptions = $cities->combine($cities)->all();

        $sports = Sport::whereHas('clubs')->orderBy('name')->pluck('name', 'sport_id')->toArray();
        $sportOptions = $sports;
        
        $clubs = Club::when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('city'), 
                fn($q) => $q->byCity($request->input('city')))
            ->when($request->filled('sport'), 
                fn($q) => $q->bySport($request->input('sport')))
            ->with('address', 'sports', 'members')
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.clubs._table', compact('clubs'));
        }

        return view('panel.clubs.index', compact('clubs', 'cityOptions', 'sportOptions'));
    }

    /**
     * Display club details
     */
    public function show(Club $club)
    {
        $this->authorize('view', $club);

        return $this->renderClubShow($club, 'panel.clubs.show');
    }

    /**
     * Display club details outside admin panel.
     */
    public function publicShow(Club $club)
    {
        $this->authorize('view', $club);

        return $this->renderClubShow($club, 'clubs.my-club');
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Club::class);
        $addressOptions = Address::query()
            ->orderBy('city')
            ->orderBy('street')
            ->selectRaw("address_id, TRIM(CONCAT(COALESCE(street, ''), ', ', COALESCE(city, ''))) as label")
            ->pluck('label', 'address_id')
            ->toArray();
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();

        return view('panel.clubs.create', compact('addressOptions', 'sportOptions'));
    }

    /**
     * Store new club
     */
    public function store(ClubRequest $request)
    {
        $this->authorize('create', Club::class);

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

        $club = Club::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'webpage' => $validated['webpage'] ?? null,
            'address_id' => $addressId,
            'sport_id' => $primarySportId,
        ]);

        $club->sports()->sync($validated['sport_ids']);

        return redirect()->route('panel.clubs.index')->with('success', 'Club created successfully!');
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
            ->selectRaw("address_id, TRIM(CONCAT(COALESCE(street, ''), ', ', COALESCE(city, ''))) as label")
            ->pluck('label', 'address_id')
            ->toArray();
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $club->load('sports');
        $selectedSportIds = $club->sports->pluck('sport_id')->toArray();

        return view('panel.clubs.edit', compact('club', 'addressOptions', 'sportOptions', 'selectedSportIds'));
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

        return redirect()->route('panel.clubs.index')->with('success', 'Club updated successfully!');
    }

    /**
     * Delete club
     */
    public function destroy(Club $club)
    {
        $this->authorize('delete', $club);

        $club->delete();

        return redirect()->route('panel.clubs.index')->with('success', 'Club deleted successfully!');
    }

    private function renderClubShow(Club $club, $view = 'panel.clubs.show')
    {
        $club->load('address', 'clubStatistic');

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
        $canManageClub = Auth::user()?->isAdmin() ?? false;

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
            'canManageClub'
        ));
    }
}
