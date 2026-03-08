<?php

namespace App\Http\Controllers;

use App\Models\SportField;
use App\Models\Address;
use App\Models\Sport;
use App\Models\FieldType;
use App\Http\Requests\SportFieldRequest;
use Illuminate\Http\Request;

class SportFieldController extends Controller
{
    /**
     * Display a listing of sport fields
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', SportField::class);

        // Get unique cities for filter dropdown
        $cities = Address::distinct()->orderBy('city')->pluck('city');

        // Get field types for filter dropdown
        $fieldTypes = FieldType::orderBy('name')->pluck('name', 'field_type_id');

        $sportFields = SportField::query()
            ->when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('location'),
                fn($q) => $q->byCity($request->input('location')))
            ->when($request->filled('field_type'),
                fn($q) => $q->byFieldType($request->input('field_type')))
            ->with('address', 'fieldType')
            ->paginate(10);

        return view('panel.sport-fields.index', compact('sportFields', 'cities', 'fieldTypes'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', SportField::class);

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::orderBy('name')->get();
        $fieldTypes = FieldType::orderBy('name')->get();

        return view('panel.sport-fields.create', compact('addresses', 'sports', 'fieldTypes'));
    }

    /**
     * Display sport field details
     */
    public function show(SportField $sportField)
    {
        $this->authorize('view', $sportField);

        $sportField->load('address', 'sports');
        return view('panel.sport-fields.show', compact('sportField'));
    }

    /**
     * Store new sport field
     */
    public function store(SportFieldRequest $request)
    {
        $this->authorize('create', SportField::class);

        $data = $request->validated();

        $addressId = $data['address_id'] ?? null;
        if (!$addressId) {
            $address = Address::firstOrCreate([
                'country'  => $data['country'],
                'city'     => $data['city'],
                'street'   => $data['street'] ?? null,
                'zip_code' => $data['zip_code'] ?? null,
            ]);
            $addressId = $address->address_id;
        }

        $sportField = SportField::create([
            'name'          => $data['name'],
            'field_type_id' => $data['field_type_id'],
            'address_id'    => $addressId,
        ]);

        $sportField->sports()->sync($data['sport_ids']);

        return redirect()->route('panel.sport-fields.index')->with('success', 'Sport field created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(SportField $sportField)
    {
        $this->authorize('update', $sportField);

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::orderBy('name')->get();
        $fieldTypes = FieldType::orderBy('name')->get();

        return view('panel.sport-fields.edit', compact('sportField', 'addresses', 'sports', 'fieldTypes'));
    }

    /**
     * Update sport field
     */
    public function update(SportFieldRequest $request, SportField $sportField)
    {
        $this->authorize('update', $sportField);

        $data = $request->validated();

        $addressId = $data['address_id'] ?? null;
        if (!$addressId) {
            $address = Address::firstOrCreate([
                'country'  => $data['country'],
                'city'     => $data['city'],
                'street'   => $data['street'] ?? null,
                'zip_code' => $data['zip_code'] ?? null,
            ]);
            $addressId = $address->address_id;
        }

        $sportField->update([
            'name'          => $data['name'],
            'field_type_id' => $data['field_type_id'],
            'address_id'    => $addressId,
        ]);

        $sportField->sports()->sync($data['sport_ids']);

        return redirect()->route('panel.sport-fields.index')->with('success', 'Sport field updated successfully!');
    }

    /**
     * Delete sport field
     */
    public function destroy(SportField $sportField)
    {
        $this->authorize('delete', $sportField);

        $sportField->delete();

        return redirect()->route('panel.sport-fields.index')->with('success', 'Sport field deleted successfully!');
    }
}
