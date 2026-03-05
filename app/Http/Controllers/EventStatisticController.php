<?php

namespace App\Http\Controllers;

use App\Models\EventStatistic;
use App\Models\Event;
use App\Http\Requests\EventStatisticRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventStatisticController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $query = EventStatistic::query();

        if ($request->filled('search')) {
            $query->whereHas('event', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        $statistics = $query->with('event')->paginate(15);
        return view('event-statistics.index', compact('statistics'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $events = Event::all();
        return view('event-statistics.create', compact('events'));
    }

    public function store(EventStatisticRequest $request)
    {
        $statistic = EventStatistic::create($request->validated());
        return redirect()->route('event-statistics.show', $statistic)->with('success', 'Event statistic created successfully.');
    }

    public function show(EventStatistic $statistic)
    {
        $statistic->load('event');
        return view('event-statistics.show', compact('statistic'));
    }

    public function edit(EventStatistic $statistic)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        return view('event-statistics.edit', compact('statistic'));
    }

    public function update(EventStatisticRequest $request, EventStatistic $statistic)
    {
        $statistic->update($request->validated());
        return redirect()->route('event-statistics.show', $statistic)->with('success', 'Event statistic updated successfully.');
    }

    public function destroy(EventStatistic $statistic)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $statistic->delete();
        return redirect()->route('event-statistics.index')->with('success', 'Event statistic deleted successfully.');
    }
}
