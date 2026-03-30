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

        $countries = Address::distinct()->orderBy('country')->pluck('country');
        $cities = Address::distinct()->orderBy('city')->pluck('city');
        $countryOptions = $countries->combine($countries)->all();
        $cityOptions = $cities->combine($cities)->all();

        $addresses = Address::query()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('country'), 
                fn($q) => $q->byCountry($request->input('country')))
            ->when($request->filled('city'), 
                fn($q) => $q->byCity($request->input('city')))
            ->paginate(8);

        if ($request->ajax()) {
            return view('panel.admin.addresses._table', compact('addresses'));
        }

        return view('panel.admin.addresses.index', compact('addresses', 'countryOptions', 'cityOptions'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Address::class);

        return view('panel.admin.addresses.create');
    }

    /**
     * Store new address
     */
    public function store(AddressRequest $request)
    {
        $this->authorize('create', Address::class);

        try {
            Address::create($request->validated());
            return redirect()->route('panel.admin.addresses.index')->with('success', 'Address created successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Address with these details already exists.');
        }   
    }

    /**
     * Show edit form
     */
    public function edit(Address $address)
    {
        $this->authorize('update', $address);

        return view('panel.admin.addresses.edit', compact('address'));
    }

    /**
     * Update address
     */
    public function update(AddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);

        try {
            $address->update($request->validated());
            return redirect()->route('panel.admin.addresses.index')->with('success', 'Address updated successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Address with these details already exists.');
        }
    }

    /**
     * Delete address
     */
    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        try {
            $address->delete();
            return redirect()->route('panel.admin.addresses.index')->with('success', 'Address deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Cannot delete address because it is associated with other records.');
        }
    }
}
