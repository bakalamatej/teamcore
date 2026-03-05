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
        
        $cities = Address::distinct()->pluck('city')->toArray();
        $cityOptions = array_combine($cities, $cities);
        
        $clubs = $user->member?->clubs() ?? Club::query()->whereRaw('1=0');
        $clubs = $clubs
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('city'), 
                fn($q) => $q->byCity($request->input('city')))
            ->with('address', 'members', 'events')
            ->paginate(10);

        return view('clubs.index', compact('clubs', 'cityOptions'));
    }

    /**
     * Display a listing of all clubs for admin
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Club::class);

        $cities = Address::distinct()->pluck('city')->toArray();
        $cityOptions = array_combine($cities, $cities);
        
        $sports = Sport::whereHas('clubs')->orderBy('name')->pluck('name', 'sport_id')->toArray();
        $sportOptions = $sports;
        
        $clubs = Club::active()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('city'), 
                fn($q) => $q->byCity($request->input('city')))
            ->when($request->filled('sport'), 
                fn($q) => $q->bySport($request->input('sport')))
            ->with('address', 'sport', 'members', 'events')
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

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::all();
        return view('panel.clubs.create', compact('addresses', 'sports'));
    }

    /**
     * Store new club
     */
    public function store(ClubRequest $request)
    {
        $this->authorize('create', Club::class);
        
        Club::create($request->validated());

        return redirect()->route('clubs.index')->with('success', 'Club created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(Club $club)
    {
        $this->authorize('update', $club);

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::all();
        return view('panel.clubs.edit', compact('club', 'addresses', 'sports'));
    }

    /**
     * Update club
     */
    public function update(ClubRequest $request, Club $club)
    {
        $this->authorize('update', $club);
        
        $club->update($request->validated());

        return redirect()->route('clubs.index')->with('success', 'Club updated successfully!');
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
