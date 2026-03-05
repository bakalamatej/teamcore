<?php

namespace App\Http\Controllers;

use App\Models\MemberStatistic;
use App\Models\MemberClub;
use App\Http\Requests\MemberStatisticRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberStatisticController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $query = MemberStatistic::query();

        if ($request->filled('search')) {
            $query->whereHas('memberClub.member', function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        $statistics = $query->with('memberClub.member', 'memberClub.club')->paginate(15);
        return view('member-statistics.index', compact('statistics'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $memberClubs = MemberClub::all();
        return view('member-statistics.create', compact('memberClubs'));
    }

    public function store(MemberStatisticRequest $request)
    {
        $statistic = MemberStatistic::create($request->validated());
        return redirect()->route('member-statistics.show', $statistic)->with('success', 'Member statistic created successfully.');
    }

    public function show(MemberStatistic $statistic)
    {
        $statistic->load('memberClub.member', 'memberClub.club');
        return view('member-statistics.show', compact('statistic'));
    }

    public function edit(MemberStatistic $statistic)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        return view('member-statistics.edit', compact('statistic'));
    }

    public function update(MemberStatisticRequest $request, MemberStatistic $statistic)
    {
        $statistic->update($request->validated());
        return redirect()->route('member-statistics.show', $statistic)->with('success', 'Member statistic updated successfully.');
    }

    public function destroy(MemberStatistic $statistic)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $statistic->delete();
        return redirect()->route('member-statistics.index')->with('success', 'Member statistic deleted successfully.');
    }
}
