<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of addresses
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Address::class);

        $countries = Address::distinct()->pluck('country')->sort()->values();
        $cities = Address::distinct()->pluck('city')->sort()->values();

        $addresses = Address::active()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('country'), 
                fn($q) => $q->byCountry($request->input('country')))
            ->when($request->filled('city'), 
                fn($q) => $q->byCity($request->input('city')))
            ->paginate(10);

        return view('panel.addresses.index', compact('addresses', 'countries', 'cities'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Address::class);

        return view('panel.addresses.create');
    }

    /**
     * Store new address
     */
    public function store(AddressRequest $request)
    {
        $this->authorize('create', Address::class);

        Address::create($request->validated());

        return redirect()->route('panel.addresses.index')->with('success', 'Address created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(Address $address)
    {
        $this->authorize('update', $address);

        return view('panel.addresses.edit', compact('address'));
    }

    /**
     * Update address
     */
    public function update(AddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);

        $address->update($request->validated());

        return redirect()->route('panel.addresses.index')->with('success', 'Address updated successfully!');
    }

    /**
     * Delete address
     */
    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        return redirect()->route('panel.addresses.index')->with('success', 'Address deleted successfully!');
    }
}
