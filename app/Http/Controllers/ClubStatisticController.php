<?php

namespace App\Http\Controllers;

use App\Models\ClubStatistic;
use Illuminate\Http\Request;

class ClubStatisticController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ClubStatistic::class);

        $statistics = ClubStatistic::query()
            ->when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->with('club')
            ->paginate(15);
        
        return view('club-statistics.index', compact('statistics'));
    }

    public function show(ClubStatistic $statistic)
    {
        $this->authorize('view', $statistic);
        
        $statistic->load('club');
        return view('club-statistics.show', compact('statistic'));
    }
}
