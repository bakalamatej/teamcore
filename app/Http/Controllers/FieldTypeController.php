<?php

namespace App\Http\Controllers;

use App\Http\Requests\FieldTypeRequest;
use App\Models\FieldType;
use Illuminate\Http\Request;

class FieldTypeController extends Controller
{
    /**
     * Display a listing of field types.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', FieldType::class);

        $fieldTypes = FieldType::query()
            ->search($request->input('search'))
            ->orderByName()
            ->paginate(8);

        if ($request->ajax()) {
            return view('panel.admin.field-types._table', compact('fieldTypes'));
        }

        return view('panel.admin.field-types.index', compact('fieldTypes'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $this->authorize('create', FieldType::class);

        return view('panel.admin.field-types.create');
    }

    /**
     * Store newly created field type.
     */
    public function store(FieldTypeRequest $request)
    {
        $this->authorize('create', FieldType::class);

        try {
            FieldType::create($request->validated());
        return redirect()->route('panel.admin.field-types.index')->with('success', 'Field type created successfully!');
            
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to create field type.');
        }
    }

    /**
     * Show edit form.
     */
    public function edit(FieldType $fieldType)
    {
        $this->authorize('update', $fieldType);

        return view('panel.admin.field-types.edit', compact('fieldType'));
    }

    /**
     * Update the specified field type.
     */
    public function update(FieldTypeRequest $request, FieldType $fieldType)
    {
        $this->authorize('update', $fieldType);

        try {
            $fieldType->update($request->validated());
            return redirect()->route('panel.admin.field-types.index')->with('success', 'Field type updated successfully!');

        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to update field type.');
        }

    }

    /**
     * Remove the specified field type.
     */
    public function destroy(FieldType $fieldType)
    {
        $this->authorize('delete', $fieldType);

        try {
            $fieldType->delete();
            return redirect()->route('panel.admin.field-types.index')->with('success', 'Field type deleted successfully!');

        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to delete field type.');
        }
    }
}