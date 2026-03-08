<?php

namespace App\Http\Controllers;

use App\Models\EventMemberResult;
use App\Models\Event;
use App\Models\MemberClub;
use App\Http\Requests\EventMemberResultRequest;
use Illuminate\Http\Request;

class EventMemberResultController extends Controller
{
    /**
     * Display a listing of event member results
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', EventMemberResult::class);

        $results = EventMemberResult::query()
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->with('event', 'memberClub.member')
            ->paginate(15);
        
        return view('event-member-results.index', compact('results'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', EventMemberResult::class);

        $events = Event::orderBy('title')->get();
        $memberClubs = MemberClub::with('member.user')->orderBy('member_club_id')->get();
        return view('event-member-results.create', compact('events', 'memberClubs'));
    }

    /**
     * Store new event member result
     */
    public function store(EventMemberResultRequest $request)
    {
        $this->authorize('create', EventMemberResult::class);

        $result = EventMemberResult::create($request->validated());
        return redirect()->route('event-member-results.show', $result)->with('success', 'Event member result created successfully.');
    }

    /**
     * Display event member result
     */
    public function show(EventMemberResult $result)
    {
        $this->authorize('view', $result);

        $result->load('event', 'memberClub.member');
        return view('event-member-results.show', compact('result'));
    }

    /**
     * Show edit form
     */
    public function edit(EventMemberResult $result)
    {
        $this->authorize('update', $result);

        $events = Event::orderBy('title')->get();
        $memberClubs = MemberClub::with('member.user')->orderBy('member_club_id')->get();
        return view('event-member-results.edit', compact('result', 'events', 'memberClubs'));
    }

    /**
     * Update event member result
     */
    public function update(EventMemberResultRequest $request, EventMemberResult $result)
    {
        $this->authorize('update', $result);

        $result->update($request->validated());
        return redirect()->route('event-member-results.show', $result)->with('success', 'Event member result updated successfully.');
    }

    /**
     * Delete event member result
     */
    public function destroy(EventMemberResult $result)
    {
        $this->authorize('delete', $result);

        $result->delete();
        return redirect()->route('event-member-results.index')->with('success', 'Event member result deleted successfully.');
    }
}
