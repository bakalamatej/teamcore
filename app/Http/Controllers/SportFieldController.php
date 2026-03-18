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
        $cityOptions = $cities->combine($cities)->all();

        // Get field types for filter dropdown
        $fieldTypeOptions = FieldType::orderBy('name')->pluck('name', 'field_type_id')->toArray();

        $sportFields = SportField::query()
            ->when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('location'),
                fn($q) => $q->byCity($request->input('location')))
            ->when($request->filled('field_type'),
                fn($q) => $q->byFieldType($request->input('field_type')))
            ->with('address', 'fieldType')
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.sport-fields._table', compact('sportFields'));
        }

        return view('panel.sport-fields.index', compact('sportFields', 'cityOptions', 'fieldTypeOptions'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', SportField::class);

        $addressOptions = Address::query()
            ->orderBy('city')
            ->selectRaw("address_id, TRIM(CONCAT(COALESCE(street, ''), ', ', COALESCE(zip_code, ''), ' ', COALESCE(city, ''))) as label")
            ->pluck('label', 'address_id')
            ->toArray();
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $fieldTypeOptions = FieldType::orderBy('name')->pluck('name', 'field_type_id')->toArray();

        return view('panel.sport-fields.create', compact('sportOptions', 'fieldTypeOptions', 'addressOptions'));
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

        $addressOptions = Address::query()
            ->orderBy('city')
            ->selectRaw("address_id, TRIM(CONCAT(COALESCE(street, ''), ', ', COALESCE(zip_code, ''), ' ', COALESCE(city, ''))) as label")
            ->pluck('label', 'address_id')
            ->toArray();
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $fieldTypeOptions = FieldType::orderBy('name')->pluck('name', 'field_type_id')->toArray();
        $selectedSportIds = $sportField->sports()->pluck('sports.sport_id')->toArray();

        return view('panel.sport-fields.edit', compact('sportField', 'sportOptions', 'fieldTypeOptions', 'addressOptions', 'selectedSportIds'));
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
