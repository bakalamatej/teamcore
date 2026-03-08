<?php

namespace App\Http\Controllers;

use App\Models\MemberStatistic;
use Illuminate\Http\Request;

class MemberStatisticController extends Controller
{
    /**
     * Display a listing of member statistics
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MemberStatistic::class);

        $statistics = MemberStatistic::query()
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->with('memberClub.member', 'memberClub.club')
            ->paginate(15);

        return view('member-statistics.index', compact('statistics'));
    }

    /**
     * Display member statistic
     */
    public function show(MemberStatistic $statistic)
    {
        $this->authorize('view', $statistic);

        $statistic->load('memberClub.member', 'memberClub.club');
        return view('member-statistics.show', compact('statistic'));
    }
}
