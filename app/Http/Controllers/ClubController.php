<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Address;
use App\Models\Sport;
use App\Http\Requests\ClubRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
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

        return view('panel.clubs.index', compact('clubs', 'cityOptions', 'sportOptions'));
    }

    /**
     * Display club details
     */
    public function show(Club $club)
    {
        $this->authorize('view', $club);
        
        $club->load('address', 'members', 'events');
        return view('clubs.show', compact('club'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Club::class);
        $addresses = Address::orderBy('city')->orderBy('street')->get();
        $sports = Sport::orderBy('name')->get();
        return view('panel.clubs.create', compact('addresses', 'sports'));
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
        return view('panel.clubs.edit', compact('club', 'addresses', 'sports'));
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
}
