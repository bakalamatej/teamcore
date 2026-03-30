<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Http\Requests\SportRequest;
use Illuminate\Http\Request;

class SportController extends Controller
{
    /**
     * Display a listing of sports
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Sport::class);

        $sports = Sport::query()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->paginate(8);

        if ($request->ajax()) {
            return view('panel.admin.sports._table', compact('sports'));
        }

        return view('panel.admin.sports.index', compact('sports'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Sport::class);

        return view('panel.admin.sports.create');
    }

    /**
     * Display sport details
     */
    public function show(Sport $sport)
    {
        $this->authorize('view', $sport);

        return view('panel.admin.sports.show', compact('sport'));
    }

    /**
     * Store new sport
     */
    public function store(SportRequest $request)
    {
        $this->authorize('create', Sport::class);

        try {
            Sport::create($request->validated());
            return redirect()->route('panel.admin.sports.index')->with('success', 'Sport created successfully!');

        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to create sport.');
        }

    }

    /**
     * Show edit form
     */
    public function edit(Sport $sport)
    {
        $this->authorize('update', $sport);

        return view('panel.admin.sports.edit', compact('sport'));
    }

    /**
     * Update sport
     */
    public function update(SportRequest $request, Sport $sport)
    {
        $this->authorize('update', $sport);

        try {
            $sport->update($request->validated());
            return redirect()->route('panel.admin.sports.index')->with('success', 'Sport updated successfully!');

        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to update sport.');
        }

    }

    /**
     * Delete sport
     */
    public function destroy(Sport $sport)
    {
        $this->authorize('delete', $sport);

        try {
            $sport->delete();
            return redirect()->route('panel.admin.sports.index')->with('success', 'Sport deleted successfully!');

        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to delete sport.');
        }

    }
}
