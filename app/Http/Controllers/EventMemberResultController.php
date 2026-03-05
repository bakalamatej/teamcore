<?php

namespace App\Http\Controllers;

use App\Models\EventMemberResult;
use App\Models\Event;
use App\Models\MemberClub;
use App\Http\Requests\EventMemberResultRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventMemberResultController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);

        $query = EventMemberResult::query();

        if ($request->filled('search')) {
            $query->whereHas('event', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })->orWhereHas('memberClub', function($q) use ($request) {
                $q->whereHas('member', function($sq) use ($request) {
                    $sq->where('first_name', 'like', '%' . $request->search . '%')
                       ->orWhere('last_name', 'like', '%' . $request->search . '%');
                });
            });
        }

        $results = $query->with('event', 'memberClub.member')->paginate(15);
        return view('event-member-results.index', compact('results'));
    }

    public function create()
    {
        $events = Event::all();
        $memberClubs = MemberClub::all();
        return view('event-member-results.create', compact('events', 'memberClubs'));
    }

    public function store(EventMemberResultRequest $request)
    {
        $result = EventMemberResult::create($request->validated());
        return redirect()->route('event-member-results.show', $result)->with('success', 'Event member result created successfully.');
    }

    public function show(EventMemberResult $result)
    {
        $result->load('event', 'memberClub.member');
        return view('event-member-results.show', compact('result'));
    }

    public function edit(EventMemberResult $result)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);
        $events = Event::all();
        $memberClubs = MemberClub::all();
        return view('event-member-results.edit', compact('result', 'events', 'memberClubs'));
    }

    public function update(EventMemberResultRequest $request, EventMemberResult $result)
    {
        $result->update($request->validated());
        return redirect()->route('event-member-results.show', $result)->with('success', 'Event member result updated successfully.');
    }

    public function destroy(EventMemberResult $result)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);
        $result->delete();
        return redirect()->route('event-member-results.index')->with('success', 'Event member result deleted successfully.');
    }
}
