<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use App\Models\Sport;
use App\Http\Requests\EventTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventTypeController extends Controller
{
    // List all event types
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $sports = Sport::all();
        $eventTypes = EventType::query();

        // Search by name
        if ($request->filled('search')) {
            $eventTypes->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by sport
        if ($request->filled('sport_id')) {
            $eventTypes->where('sport_id', $request->sport_id);
        }

        $eventTypes = $eventTypes->with('sport')->paginate(10);

        return view('panel.event-types.index', compact('eventTypes', 'sports'));
    }

    // Show create form
    public function create()
    {
        $this->authorizeAdmin();

        $sports = Sport::all();

        return view('panel.event-types.create', compact('sports'));
    }

    // Store new event type
    public function store(EventTypeRequest $request)
    {
        EventType::create($request->validated());

        return redirect()->route('panel.event-types.index')->with('success', 'Event type created successfully!');
    }

    // Show edit form
    public function edit(EventType $eventType)
    {
        $this->authorizeAdmin();

        $sports = Sport::all();

        return view('panel.event-types.edit', compact('eventType', 'sports'));
    }

    // Update event type
    public function update(EventTypeRequest $request, EventType $eventType)
    {
        $eventType->update($request->validated());

        return redirect()->route('panel.event-types.index')->with('success', 'Event type updated successfully!');
    }

    // Delete event type
    public function destroy(EventType $eventType)
    {
        $this->authorizeAdmin();

        $eventType->delete();

        return redirect()->route('panel.event-types.index')->with('success', 'Event type deleted successfully!');
    }

    // Helper: Check if user is admin
    private function authorizeAdmin()
    {
        if (!Auth::user() || Auth::user()->isAdmin() === false) {
            abort(403, 'Unauthorized');
        }
    }
}
