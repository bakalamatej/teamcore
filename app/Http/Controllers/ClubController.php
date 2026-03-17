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
        $member = Auth::user()?->member;

        $club = $member?->activeClubs()->first();

        abort_if(!$club, 404, 'You are not part of any active club.');

        $this->authorize('view', $club);

        return $this->renderClubShow($club, 'clubs.my-club');
    }

    /**
     * Display a listing of user's clubs
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $cities = Address::distinct()->orderBy('city')->pluck('city');
        $cityOptions = $cities->combine($cities)->all();
        
        $clubs = $user->member?->visibleClubs() ?? Club::query()->whereRaw('1=0');
        $clubs = $clubs
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('city'), 
                fn($q) => $q->byCity($request->input('city')))
            ->with('address', 'members')
            ->paginate(10);

        return view('clubs.index', compact('clubs', 'cityOptions'));
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
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Club::class);
        $addresses = Address::orderBy('city')->orderBy('street')->get();
        $sports = Sport::orderBy('name')->get();
        $addressOptions = $addresses
            ->mapWithKeys(fn($address) => [
                $address->address_id => trim(($address->street ?? '') . ' ' . ($address->number ?? '') . ', ' . ($address->city ?? '')),
            ])
            ->toArray();
        $sportOptions = $sports->pluck('name', 'sport_id')->toArray();

        return view('panel.clubs.create', compact('addressOptions', 'sportOptions'));
    }

    /**
     * Store new club
     */
    public function store(ClubRequest $request)
    {
        $this->authorize('create', Club::class);

        $validated = $request->validated();

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
        $addresses = Address::orderBy('city')->orderBy('street')->get();
        $sports = Sport::orderBy('name')->get();
        $club->load('sports');
        $addressOptions = $addresses
            ->mapWithKeys(fn($address) => [
                $address->address_id => trim(($address->street ?? '') . ' ' . ($address->number ?? '') . ', ' . ($address->city ?? '')),
            ])
            ->toArray();
        $sportOptions = $sports->pluck('name', 'sport_id')->toArray();
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

        return redirect()->route('clubs.index')->with('success', 'Club deleted successfully!');
    }

    private function renderClubShow(Club $club, $view = 'panel.clubs.show')
    {
        $club->load('address');

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
            'recentEvents',
            'moreEventsCount',
            'coaches',
            'canManageClub'
        ));
    }
}
