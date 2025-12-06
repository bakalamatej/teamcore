<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $query = Club::with('address');

        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('address', function($q) use ($search) {
                      $q->whereRaw('LOWER(city) LIKE ?', ["%{$search}%"]);
                  });
        }

        $clubs = $query->orderBy('name')->paginate(10);

        return view('clubs.index', compact('clubs'));
    }

    public function show(Club $club)
    {
        return view('clubs.show', compact('club'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        $addresses = Address::orderBy('city')->get();
        return view('clubs.create', compact('addresses'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

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

    public function edit(Club $club)
    {
        $this->authorizeAdmin();

        $addresses = Address::orderBy('city')->get();
        return view('clubs.edit', compact('club', 'addresses'));
    }

    public function update(Request $request, Club $club)
    {
        $this->authorizeAdmin();

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

    public function destroy(Club $club)
    {
        $this->authorizeAdmin();

        $club->delete();

        return redirect()->route('clubs.index')->with('success', 'Club deleted successfully!');
    }

    private function authorizeAdmin()
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }
}
