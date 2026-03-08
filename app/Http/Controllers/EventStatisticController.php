<?php

namespace App\Http\Controllers;

use App\Models\EventStatistic;
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
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->with('event')
            ->paginate(15);

        return view('event-statistics.index', compact('statistics'));
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
}
