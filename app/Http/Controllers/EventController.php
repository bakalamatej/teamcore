<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventType;
use App\Models\SportField; 

class EventController extends Controller
{
    // List all events with optional filtering and pagination
    public function index(Request $request)
    {
        $sportFields = SportField::all();
        $eventTypes = EventType::all();
        
        $query = Event::query();

        // Show user's events if requested, otherwise latest events
        if ($request->has('my_events') && Auth::check() && Auth::user()->member) {
            $query = Auth::user()->member->myEvents();
        } else {
            $query = $query->latest();
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by sport field location
        if ($request->filled('sport_field_id')) {
            $query->where('sport_field_id', $request->sport_field_id);
        }

        // Filter by event status (scheduled/cancelled/finished)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by event type (training/match/competition)
        if ($request->filled('type')) {
            $query->where('event_type_id', $request->type);
        }

        $events = $query->with('sportField', 'eventType')->paginate(10);

        return view('events.index', compact('events', 'sportFields', 'eventTypes'));
    }

    // Show create event form
    public function create()
    {
        $sportFields = SportField::all();
        $eventTypes = EventType::all();
        return view('panel.events.create', compact('sportFields', 'eventTypes'));
    }

    // Store new event in database
    public function store(Request $request)
    {
        // Validate event data
        $request->validate([
            'title' => 'required|min:5|max:80',
            'sport_field_id' => 'required|exists:sport_fields,id',
            'event_type_id' => 'nullable|exists:event_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|min:10'
        ]);

        $event = Event::create([
            'title' => $request->title,
            'sport_field_id' => $request->sport_field_id,
            'event_type_id' => $request->event_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
        ]);

        // Return JSON for AJAX or redirect
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'event' => $event], 201);
        }

        return redirect()->route('events.index');
    }

    // Display event details
    public function show(Event $event)
    {
        $event->load('clubs', 'members', 'sportField', 'eventType');
        return view('events.show', compact('event'));
    }

    // Show edit event form
    public function edit(Event $event)
    {
        $sportFields = SportField::all();
        $eventTypes = EventType::all();

        return view('panel.events.edit', compact('event', 'sportFields', 'eventTypes'));
    }

    // Update event in database
    public function update(Request $request, Event $event)
    {
        // Validate event data
        $request->validate([
            'title' => 'required|min:5|max:80',
            'sport_field_id' => 'required|exists:sport_fields,id',
            'event_type_id' => 'nullable|exists:event_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|min:10'
        ]);

        $event->update([
            'title' => $request->title,
            'sport_field_id' => $request->sport_field_id,
            'event_type_id' => $request->event_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
        ]);

        return redirect()->route('events.index');
    }

    // Delete event (admin/coach only)
    public function destroy(Event $event)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->isCoach())) {
            abort(403);
        }

        $event->delete();
        return redirect()->route('events.index');
    }
}