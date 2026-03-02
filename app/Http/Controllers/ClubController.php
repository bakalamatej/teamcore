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
    // List user's clubs with search and city filtering
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get unique cities for filter dropdown
        $cities = Address::distinct()->pluck('city')->toArray();
        $cityOptions = array_combine($cities, $cities);
        
        // Get user's active clubs (or empty query if user has no member record)
        $clubs = $user->activeClubs() ?? Club::query()->whereRaw('1=0');
        
        // Search by club name
        if ($request->filled('search')) {
            $clubs->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by city
        if ($request->filled('city')) {
            $clubs->whereHas('address', function($q) {
                $q->where('city', request('city'));
            });
        }
        
        $clubs = $clubs->with('address', 'members', 'events')->paginate(10);
        
        return view('clubs.index', compact('clubs', 'cityOptions'));
    }

    // List all clubs for admin panel with search and city filtering
    public function adminIndex(Request $request)
    {
        $this->authorizeAdmin();

        // Get unique cities for filter dropdown
        $cities = Address::distinct()->pluck('city')->toArray();
        $cityOptions = array_combine($cities, $cities);
        
        // Get unique sports for filter dropdown
        $sports = Sport::whereHas('clubs')->orderBy('name')->pluck('name', 'id')->toArray();
        $sportOptions = $sports;
        
        $clubs = Club::query();
        
        // Search by club name
        if ($request->filled('search')) {
            $clubs->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by city
        if ($request->filled('city')) {
            $clubs->whereHas('address', function($q) {
                $q->where('city', request('city'));
            });
        }
        
        // Filter by sport
        if ($request->filled('sport')) {
            $clubs->where('sport_id', request('sport'));
        }
        
        $clubs = $clubs->with('address', 'sport', 'members', 'events')->paginate(10);
        
        return view('panel.clubs.index', compact('clubs', 'cityOptions', 'sportOptions'));
    }

    // List user's clubs (clubs where user is a member)
    public function myClub(Request $request)
    {
        $user = Auth::user();
        
        // Get unique cities for filter dropdown
        $cities = Address::distinct()->pluck('city')->toArray();
        $cityOptions = array_combine($cities, $cities);
        
        // Get user's active clubs (or empty query if user has no member record)
        $clubs = $user->activeClubs() ?? Club::query()->whereRaw('1=0');
        
        // Search by club name
        if ($request->filled('search')) {
            $clubs->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by city
        if ($request->filled('city')) {
            $clubs->whereHas('address', function($q) {
                $q->where('city', request('city'));
            });
        }
        
        $clubs = $clubs->with('address', 'members', 'events')->paginate(10);
        
        return view('clubs.index', compact('clubs', 'cityOptions'));
    }

    // Display club details and members
    public function show(Club $club)
    {
        $club->load('address', 'activeMembers', 'activeEvents');
        return view('clubs.show', compact('club'));
    }

    // Show create club form (admin only)
    public function create()
    {
        $this->authorizeAdmin();

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::all();
        return view('panel.clubs.create', compact('addresses', 'sports'));
    }

    // Store new club in database (admin only)
    public function store(ClubRequest $request)
    {
        Club::create($request->validated());

        return redirect()->route('clubs.index')->with('success', 'Club created successfully!');
    }

    // Show edit club form (admin only)
    public function edit(Club $club)
    {
        $this->authorizeAdmin();

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::all();
        return view('panel.clubs.edit', compact('club', 'addresses', 'sports'));
    }

    // Update club in database (admin only)
    public function update(ClubRequest $request, Club $club)
    {
        $club->update($request->validated());

        return redirect()->route('clubs.index')->with('success', 'Club updated successfully!');
    }

    // Delete club (admin only)
    public function destroy(Club $club)
    {
        $this->authorizeAdmin();

        $club->delete();

        return redirect()->route('clubs.index')->with('success', 'Club deleted successfully!');
    }

    // Helper: Check if user is admin
    private function authorizeAdmin()
    {
        if (!Auth::user() || Auth::user()->isAdmin() === false) {
            abort(403, 'Unauthorized');
        }
    }
}
