<?php

namespace App\Http\Controllers;

use App\Models\SportField;
use App\Models\Address;
use App\Models\Sport;
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
        $cities = Address::distinct()->pluck('city')->toArray();
        $cityOptions = array_combine($cities, $cities);
        
        // Get unique field types for filter dropdown
        $fieldTypes = SportField::distinct()->pluck('field_type')->toArray();
        $fieldTypeOptions = array_combine($fieldTypes, $fieldTypes);

        $sportFields = SportField::active()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('location'), 
                fn($q) => $q->byCity($request->input('location')))
            ->when($request->filled('field_type'), 
                fn($q) => $q->byFieldType($request->input('field_type')))
            ->with('address')
            ->paginate(10);

        return view('panel.sport-fields.index', compact('sportFields', 'cityOptions', 'fieldTypeOptions'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', SportField::class);

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::all();

        return view('panel.sport-fields.create', compact('addresses', 'sports'));
    }

    /**
     * Store new sport field
     */
    public function store(SportFieldRequest $request)
    {
        $this->authorize('create', SportField::class);

        $sportField = SportField::create($request->validated());

        return redirect()->route('panel.sport-fields.index')->with('success', 'Sport field created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(SportField $sportField)
    {
        $this->authorize('update', $sportField);

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::all();

        return view('panel.sport-fields.edit', compact('sportField', 'addresses', 'sports'));
    }

    /**
     * Update sport field
     */
    public function update(SportFieldRequest $request, SportField $sportField)
    {
        $this->authorize('update', $sportField);

        $sportField->update($request->validated());

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
