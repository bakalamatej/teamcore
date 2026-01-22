<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    // List all clubs with search and city filtering
    public function index(Request $request)
    {
        // Get unique cities for filter dropdown
        $cities = \App\Models\Address::distinct()->pluck('city')->toArray();
        $cityOptions = array_combine($cities, $cities);
        
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
        return view('panel.clubs.create', compact('addresses'));
    }

    // Store new club in database (admin only)
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        // Validate club data
        $request->validate([
            'name' => 'required|string|max:30|unique:clubs,name',
            'phone' => 'required|string|max:20|unique:clubs,phone',
            'email' => 'required|email|max:56|unique:clubs,email',
            'webpage' => 'nullable|url|max:255',
            'address_id' => 'nullable|exists:addresses,id',
        ]);

        Club::create($request->only(['name','phone','email','webpage','address_id']));

        return redirect()->route('clubs.index')->with('success', 'Club created successfully!');
    }

    // Show edit club form (admin only)
    public function edit(Club $club)
    {
        $this->authorizeAdmin();

        $addresses = Address::orderBy('city')->get();
        return view('panel.clubs.edit', compact('club', 'addresses'));
    }

    // Update club in database (admin only)
    public function update(Request $request, Club $club)
    {
        $this->authorizeAdmin();

        // Validate club data (unique excluding current club)
        $request->validate([
            'name' => 'required|string|max:30|unique:clubs,name,' . $club->id,
            'phone' => 'required|string|max:20|unique:clubs,phone,' . $club->id,
            'email' => 'required|email|max:56|unique:clubs,email,' . $club->id,
            'webpage' => 'nullable|url|max:255',
            'address_id' => 'nullable|exists:addresses,id',
        ]);

        $club->update($request->only(['name','phone','email','webpage','address_id']));

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
