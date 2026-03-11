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
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.sports._table', compact('sports'));
        }

        return view('panel.sports.index', compact('sports'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Sport::class);

        return view('panel.sports.create');
    }

    /**
     * Display sport details
     */
    public function show(Sport $sport)
    {
        $this->authorize('view', $sport);

        return view('panel.sports.show', compact('sport'));
    }

    /**
     * Store new sport
     */
    public function store(SportRequest $request)
    {
        $this->authorize('create', Sport::class);

        Sport::create($request->validated());

        return redirect()->route('panel.sports.index')->with('success', 'Sport created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(Sport $sport)
    {
        $this->authorize('update', $sport);

        return view('panel.sports.edit', compact('sport'));
    }

    /**
     * Update sport
     */
    public function update(SportRequest $request, Sport $sport)
    {
        $this->authorize('update', $sport);

        $sport->update($request->validated());

        return redirect()->route('panel.sports.index')->with('success', 'Sport updated successfully!');
    }

    /**
     * Delete sport
     */
    public function destroy(Sport $sport)
    {
        $this->authorize('delete', $sport);

        $sport->delete();

        return redirect()->route('panel.sports.index')->with('success', 'Sport deleted successfully!');
    }
}
