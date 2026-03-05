<?php

namespace App\Http\Controllers;

use App\Models\EventClubResult;
use App\Models\Event;
use App\Models\Club;
use App\Http\Requests\EventClubResultRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventClubResultController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);

        $query = EventClubResult::query();

        if ($request->filled('search')) {
            $query->whereHas('event', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })->orWhereHas('club', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $results = $query->with('event', 'club')->paginate(15);
        return view('event-club-results.index', compact('results'));
    }

    public function create()
    {
        $events = Event::all();
        $clubs = Club::all();
        return view('event-club-results.create', compact('events', 'clubs'));
    }

    public function store(EventClubResultRequest $request)
    {
        $result = EventClubResult::create($request->validated());
        return redirect()->route('event-club-results.show', $result)->with('success', 'Event club result created successfully.');
    }

    public function show(EventClubResult $result)
    {
        $result->load('event', 'club');
        return view('event-club-results.show', compact('result'));
    }

    public function edit(EventClubResult $result)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);
        $events = Event::all();
        $clubs = Club::all();
        return view('event-club-results.edit', compact('result', 'events', 'clubs'));
    }

    public function update(EventClubResultRequest $request, EventClubResult $result)
    {
        $result->update($request->validated());
        return redirect()->route('event-club-results.show', $result)->with('success', 'Event club result updated successfully.');
    }

    public function destroy(EventClubResult $result)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);
        $result->delete();
        return redirect()->route('event-club-results.index')->with('success', 'Event club result deleted successfully.');
    }
}
