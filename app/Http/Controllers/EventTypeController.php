<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use App\Models\Sport;
use App\Http\Requests\EventTypeRequest;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    /**
     * Display a listing of event types
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', EventType::class);

        $sports = Sport::orderBy('name')->get();
        $sportOptions = $sports->pluck('name', 'sport_id')->toArray();
        $eventTypes = EventType::query()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('sport_id'), 
                fn($q) => $q->bySport($request->input('sport_id')))
            ->with('sport')
            ->paginate(10);

        if ($request->ajax()) {
            return view('panel.admin.event-types._table', compact('eventTypes'));
        }

        return view('panel.admin.event-types.index', compact('eventTypes', 'sportOptions'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', EventType::class);

        $sports = Sport::orderBy('name')->get();
        $sportOptions = $sports->pluck('name', 'sport_id')->toArray();

        return view('panel.admin.event-types.create', compact('sportOptions'));
    }

    /**
     * Display event type details
     */
    public function show(EventType $eventType)
    {
        $this->authorize('view', $eventType);

        $eventType->load('sport');
        return view('panel.admin.event-types.show', compact('eventType'));
    }

    /**
     * Store new event type
     */
    public function store(EventTypeRequest $request)
    {
        $this->authorize('create', EventType::class);

        EventType::create($request->validated());

        return redirect()->route('panel.admin.event-types.index')->with('success', 'Event type created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(EventType $eventType)
    {
        $this->authorize('update', $eventType);

        $sports = Sport::orderBy('name')->get();
        $sportOptions = $sports->pluck('name', 'sport_id')->toArray();

        return view('panel.admin.event-types.edit', compact('eventType', 'sportOptions'));
    }

    /**
     * Update event type
     */
    public function update(EventTypeRequest $request, EventType $eventType)
    {
        $this->authorize('update', $eventType);

        $eventType->update($request->validated());

        return redirect()->route('panel.admin.event-types.index')->with('success', 'Event type updated successfully!');
    }

    /**
     * Delete event type
     */
    public function destroy(EventType $eventType)
    {
        $this->authorize('delete', $eventType);

        $eventType->delete();

        return redirect()->route('panel.admin.event-types.index')->with('success', 'Event type deleted successfully!');
    }
}
