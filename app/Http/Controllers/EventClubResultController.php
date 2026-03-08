<?php

namespace App\Http\Controllers;

use App\Models\EventClubResult;
use App\Models\Event;
use App\Models\Club;
use App\Http\Requests\EventClubResultRequest;
use Illuminate\Http\Request;

class EventClubResultController extends Controller
{
    /**
     * Display a listing of event club results
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', EventClubResult::class);

        $results = EventClubResult::query()
            ->when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->with('event', 'club')
            ->paginate(15);
        
        return view('event-club-results.index', compact('results'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', EventClubResult::class);

        $events = Event::finished()->orderBy('start_date', 'desc')->limit(100)->get();
        $clubs = Club::orderBy('name')->get();
        return view('event-club-results.create', compact('events', 'clubs'));
    }

    /**
     * Store new event club result
     */
    public function store(EventClubResultRequest $request)
    {
        $this->authorize('create', EventClubResult::class);

        $result = EventClubResult::create($request->validated());
        return redirect()->route('event-club-results.show', $result)->with('success', 'Event club result created successfully.');
    }

    /**
     * Display event club result
     */
    public function show(EventClubResult $result)
    {
        $this->authorize('view', $result);

        $result->load('event', 'club');
        return view('event-club-results.show', compact('result'));
    }

    /**
     * Show edit form
     */
    public function edit(EventClubResult $result)
    {
        $this->authorize('update', $result);

        $events = Event::finished()->orderBy('start_date', 'desc')->limit(100)->get();
        $clubs = Club::orderBy('name')->get();
        return view('event-club-results.edit', compact('result', 'events', 'clubs'));
    }

    /**
     * Update event club result
     */
    public function update(EventClubResultRequest $request, EventClubResult $result)
    {
        $this->authorize('update', $result);

        $result->update($request->validated());
        return redirect()->route('event-club-results.show', $result)->with('success', 'Event club result updated successfully.');
    }

    /**
     * Delete event club result
     */
    public function destroy(EventClubResult $result)
    {
        $this->authorize('delete', $result);

        $result->delete();
        return redirect()->route('event-club-results.index')->with('success', 'Event club result deleted successfully.');
    }
}
