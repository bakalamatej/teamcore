<?php

namespace App\Http\Controllers;

use App\Enums\MemberClubRole;
use App\Models\Club;
use App\Models\Address;
use App\Models\Sport;
use App\Http\Requests\ClubRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MemberClub;

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
            ->with('address', 'sport', 'members')
            ->paginate(7);

        if ($request->ajax()) {
            return view('panel.admin.clubs._table', compact('clubs'));
        }

        return view('panel.admin.clubs.index', compact('clubs', 'cityOptions', 'sportOptions'));
    }

    /**
     * Display club details
     */
    public function show(Club $club)
    {
        $this->authorize('view', $club);

        return $this->renderClubShow($club, 'panel.admin.clubs.show');
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

        return view('panel.admin.clubs.create', compact('addressOptions', 'countryOptions', 'sportOptions'));
    }

    /**
     * Store new club
     */
    public function store(ClubRequest $request)
    {
        $this->authorize('create', Club::class);

        $validated = $request->validated();
        try {
            

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
                'sport_id' => $validated['sport_id'],
            ]);

            return redirect()->route('panel.admin.clubs.index')->with('success', 'Club created successfully!');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to create club.');
        }
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
        $selectedSportId = $club->sport_id;

        return view('panel.admin.clubs.edit', compact('club', 'addressOptions', 'countryOptions', 'sportOptions', 'selectedSportId'));
    }

    /**
     * Update club
     */
    public function update(ClubRequest $request, Club $club)
    {
        $this->authorize('update', $club);
        $validated = $request->validated();


        try {

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
                'sport_id' => $validated['sport_id'],
            ]);

            return redirect()->route('panel.admin.clubs.index')->with('success', 'Club updated successfully!');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to update club.');
        }
    }

    /**
     * Delete club
     */
    public function destroy(Club $club)
    {
        $this->authorize('delete', $club);

        try {
            $club->delete();
            return redirect()->route('panel.admin.clubs.index')->with('success', 'Club deleted successfully!');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to delete club.');
        }
    }

    private function renderClubShow(Club $club, $view = 'panel.clubs.show')
    {
        $club->load('address', 'clubStatistic');
        $stats = $club->clubStatistic;

        $activeMembers = $club->members()
            ->wherePivotNull('left_at')
            ->with('user')
            ->paginate(5);

        $activeMembers->getCollection()->transform(function ($member) {
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

        $coaches = MemberClub::where('club_id', $club->club_id)
            ->whereNull('left_at')
            ->where('role', MemberClubRole::COACH->value)
            ->with('member.user')
            ->get();

        $activeMembersCount = $activeMembers->count();
        $activeEventsCount = $activeEvents->count();
        $statisticsMembersCount = $club->clubStatistic?->active_members ?? 0;
        $statisticsMatchesPlayedCount = $club->clubStatistic?->matches_played ?? 0;
        $statisticsTotalWinsCount = $club->clubStatistic?->total_wins ?? 0;
        $statisticsTotalLossesCount = $club->clubStatistic?->total_losses ?? 0;
        $recentEvents = $activeEvents->take(5);
        $moreEventsCount = max($activeEventsCount - 5, 0);
        $canManageClub = Auth::user()?->isAdmin() ?? false;
        $fileCategories = \App\Models\FileCategory::orderBy('name')
            ->get(['file_category_id', 'name'])
            ->toArray();

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
            'stats',
            'fileCategories'
        ));
    }
}
