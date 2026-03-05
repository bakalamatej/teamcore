<?php

namespace App\Http\Controllers;

use App\Models\EventStatistic;
use App\Models\Event;
use App\Http\Requests\EventStatisticRequest;
use Illuminate\Http\Request;

class EventStatisticController extends Controller
{
    /**
     * Display a listing of event statistics
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', EventStatistic::class);

        $statistics = EventStatistic::query()
            ->when($request->filled('search'), function($q) use ($request) {
                return $q->whereHas('event', function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->input('search') . '%');
                });
            })
            ->with('event')
            ->paginate(15);
        
        return view('event-statistics.index', compact('statistics'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', EventStatistic::class);

        $events = Event::all();
        return view('event-statistics.create', compact('events'));
    }

    /**
     * Store new event statistic
     */
    public function store(EventStatisticRequest $request)
    {
        $this->authorize('create', EventStatistic::class);

        $statistic = EventStatistic::create($request->validated());
        return redirect()->route('event-statistics.show', $statistic)->with('success', 'Event statistic created successfully.');
    }

    /**
     * Display event statistic
     */
    public function show(EventStatistic $statistic)
    {
        $this->authorize('view', $statistic);

        $statistic->load('event');
        return view('event-statistics.show', compact('statistic'));
    }

    /**
     * Show edit form
     */
    public function edit(EventStatistic $statistic)
    {
        $this->authorize('update', $statistic);

        return view('event-statistics.edit', compact('statistic'));
    }

    /**
     * Update event statistic
     */
    public function update(EventStatisticRequest $request, EventStatistic $statistic)
    {
        $this->authorize('update', $statistic);

        $statistic->update($request->validated());
        return redirect()->route('event-statistics.show', $statistic)->with('success', 'Event statistic updated successfully.');
    }

    /**
     * Delete event statistic
     */
    public function destroy(EventStatistic $statistic)
    {
        $this->authorize('delete', $statistic);

        $statistic->delete();
        return redirect()->route('event-statistics.index')->with('success', 'Event statistic deleted successfully.');
    }
}
