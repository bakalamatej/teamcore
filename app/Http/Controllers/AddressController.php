<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    // List all addresses
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $addresses = Address::query();

        // Search by city or street
        if ($request->filled('search')) {
            $addresses->where('city', 'like', '%' . $request->search . '%')
                     ->orWhere('street', 'like', '%' . $request->search . '%');
        }

        // Filter by country
        if ($request->filled('country')) {
            $addresses->where('country', $request->country);
        }

        // Filter by city
        if ($request->filled('city')) {
            $addresses->where('city', $request->city);
        }

        // Get unique countries and cities for filter dropdowns
        $countries = Address::distinct()->pluck('country')->sort()->values();
        $cities = Address::distinct()->pluck('city')->sort()->values();

        $addresses = $addresses->paginate(10);

        return view('panel.addresses.index', compact('addresses', 'countries', 'cities'));
    }

    // Show create form
    public function create()
    {
        $this->authorizeAdmin();

        return view('panel.addresses.create');
    }

    // Store new address
    public function store(AddressRequest $request)
    {
        Address::create($request->validated());

        return redirect()->route('panel.addresses.index')->with('success', 'Address created successfully!');
    }

    // Show edit form
    public function edit(Address $address)
    {
        $this->authorizeAdmin();

        return view('panel.addresses.edit', compact('address'));
    }

    // Update address
    public function update(AddressRequest $request, Address $address)
    {
        $address->update($request->validated());

        return redirect()->route('panel.addresses.index')->with('success', 'Address updated successfully!');
    }

    // Delete address
    public function destroy(Address $address)
    {
        $this->authorizeAdmin();

        $address->delete();

        return redirect()->route('panel.addresses.index')->with('success', 'Address deleted successfully!');
    }

    // Helper: Check if user is admin
    private function authorizeAdmin()
    {
        if (!Auth::user() || Auth::user()->isAdmin() === false) {
            abort(403, 'Unauthorized');
        }
    }
}
