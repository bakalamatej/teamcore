<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventType;
use App\Models\SportField; 

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query()->latest();

        if ($request->has('my_events') && Auth::check() && Auth::user()->member) {
            $member = Auth::user()->member;

            $clubIds = $member->activeClubs->pluck('id');

            $query->where(function($q) use ($member, $clubIds) {
                $q->whereHas('members', function($q2) use ($member) {
                    $q2->where('member_id', $member->id)
                    ->whereNull('member_event.deleted_at');
                })
                ->orWhereHas('clubs', function($q2) use ($clubIds) {
                    $q2->whereIn('clubs.id', $clubIds);
                });
            });
        }

        $events = $query->paginate(10);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $sportFields = SportField::all();
        $eventTypes = EventType::all();
        return view('events.create', compact('sportFields', 'eventTypes'));
    }

    public function store(Request $request)
    {
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

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'event' => $event], 201);
        }

        return redirect()->route('events.index');
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
        {
            $sportFields = SportField::all();
            $eventTypes = EventType::all();

            return view('events.edit', compact('event', 'sportFields', 'eventTypes'));
        }


    public function update(Request $request, Event $event)
    {
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

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index');
    }
}
