<?php

namespace App\Http\Controllers;

use App\Models\SportField;
use App\Models\Address;
use App\Models\Sport;
use App\Http\Requests\SportFieldRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SportFieldController extends Controller
{
    // List all sport fields
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        // Get unique cities for filter dropdown
        $cities = Address::distinct()->pluck('city')->toArray();
        $cityOptions = array_combine($cities, $cities);
        
        // Get unique field types for filter dropdown
        $fieldTypes = SportField::distinct()->pluck('field_type')->toArray();
        $fieldTypeOptions = array_combine($fieldTypes, $fieldTypes);

        $sportFields = SportField::query();

        // Search by name
        if ($request->filled('search')) {
            $sportFields->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by location (city)
        if ($request->filled('location')) {
            $sportFields->whereHas('address', function($q) {
                $q->where('city', request('location'));
            });
        }

        // Filter by field type
        if ($request->filled('field_type')) {
            $sportFields->where('field_type', request('field_type'));
        }

        $sportFields = $sportFields->with('address')->paginate(10);

        return view('panel.sport-fields.index', compact('sportFields', 'cityOptions', 'fieldTypeOptions'));
    }

    // Show create form
    public function create()
    {
        $this->authorizeAdmin();

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::all();

        return view('panel.sport-fields.create', compact('addresses', 'sports'));
    }

    // Store new sport field
    public function store(SportFieldRequest $request)
    {
        $sportField = SportField::create($request->validated());

        return redirect()->route('panel.sport-fields.index')->with('success', 'Sport field created successfully!');
    }

    // Show edit form
    public function edit(SportField $sportField)
    {
        $this->authorizeAdmin();

        $addresses = Address::orderBy('city')->get();
        $sports = Sport::all();

        return view('panel.sport-fields.edit', compact('sportField', 'addresses', 'sports'));
    }

    // Update sport field
    public function update(SportFieldRequest $request, SportField $sportField)
    {
        $sportField->update($request->validated());

        return redirect()->route('panel.sport-fields.index')->with('success', 'Sport field updated successfully!');
    }

    // Delete sport field
    public function destroy(SportField $sportField)
    {
        $this->authorizeAdmin();

        $sportField->delete();

        return redirect()->route('panel.sport-fields.index')->with('success', 'Sport field deleted successfully!');
    }

    // Helper: Check if user is admin
    private function authorizeAdmin()
    {
        if (!Auth::user() || Auth::user()->isAdmin() === false) {
            abort(403, 'Unauthorized');
        }
    }
}
