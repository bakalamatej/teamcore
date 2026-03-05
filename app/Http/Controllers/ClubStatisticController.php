<?php

namespace App\Http\Controllers;

use App\Models\ClubStatistic;
use App\Models\Club;
use App\Http\Requests\ClubStatisticRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubStatisticController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ClubStatistic::class);

        $statistics = ClubStatistic::query()
            ->when($request->filled('search'), function($q) use ($request) {
                return $q->whereHas('club', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->input('search') . '%');
                });
            })
            ->with('club')
            ->paginate(15);
        
        return view('club-statistics.index', compact('statistics'));
    }

    public function create()
    {
        $this->authorize('create', ClubStatistic::class);
        $clubs = Club::all();
        return view('club-statistics.create', compact('clubs'));
    }

    public function store(ClubStatisticRequest $request)
    {
        $statistic = ClubStatistic::create($request->validated());
        return redirect()->route('club-statistics.show', $statistic)->with('success', 'Club statistic created successfully.');
    }

    public function show(ClubStatistic $statistic)
    {
        $statistic->load('club');
        return view('club-statistics.show', compact('statistic'));
    }

    public function edit(ClubStatistic $statistic)
    {
        $this->authorize('update', $statistic);
        return view('club-statistics.edit', compact('statistic'));
    }

    public function update(ClubStatisticRequest $request, ClubStatistic $statistic)
    {
        $statistic->update($request->validated());
        return redirect()->route('club-statistics.show', $statistic)->with('success', 'Club statistic updated successfully.');
    }

    public function destroy(ClubStatistic $statistic)
    {
        $this->authorize('delete', $statistic);
        $statistic->delete();
        return redirect()->route('club-statistics.index')->with('success', 'Club statistic deleted successfully.');
    }
}
