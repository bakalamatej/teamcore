<?php

namespace App\Http\Controllers;

use App\Models\MemberStatistic;
use App\Models\MemberClub;
use App\Http\Requests\MemberStatisticRequest;
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
            ->when($request->filled('search'), function($q) use ($request) {
                return $q->whereHas('memberClub.member', function($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
                });
            })
            ->with('memberClub.member', 'memberClub.club')
            ->paginate(15);
        
        return view('member-statistics.index', compact('statistics'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', MemberStatistic::class);

        $memberClubs = MemberClub::all();
        return view('member-statistics.create', compact('memberClubs'));
    }

    /**
     * Store new member statistic
     */
    public function store(MemberStatisticRequest $request)
    {
        $this->authorize('create', MemberStatistic::class);

        $statistic = MemberStatistic::create($request->validated());
        return redirect()->route('member-statistics.show', $statistic)->with('success', 'Member statistic created successfully.');
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

    /**
     * Show edit form
     */
    public function edit(MemberStatistic $statistic)
    {
        $this->authorize('update', $statistic);

        return view('member-statistics.edit', compact('statistic'));
    }

    /**
     * Update member statistic
     */
    public function update(MemberStatisticRequest $request, MemberStatistic $statistic)
    {
        $this->authorize('update', $statistic);

        $statistic->update($request->validated());
        return redirect()->route('member-statistics.show', $statistic)->with('success', 'Member statistic updated successfully.');
    }

    /**
     * Delete member statistic
     */
    public function destroy(MemberStatistic $statistic)
    {
        $this->authorize('delete', $statistic);

        $statistic->delete();
        return redirect()->route('member-statistics.index')->with('success', 'Member statistic deleted successfully.');
    }
}
