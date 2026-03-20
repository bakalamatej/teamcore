<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $activeMembership = Auth::user()?->activeMembership();
        $clubId = $activeMembership?->club_id;
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $events = Event::query()
            ->whereHas('clubs', fn($q) => $q->where('clubs.club_id', $clubId))
            ->whereBetween('start_date', [$start, $end])
            ->orderBy('start_date')
            ->get();

        return view('calendar.index', compact('year', 'month', 'events'));
    }

    public function showDay(Request $request, $year, $month, $day)
    {
        $activeMembership = Auth::user()?->activeMembership();
        $clubId = $activeMembership?->club_id;
        $date = Carbon::create($year, $month, $day);

        $events = Event::query()
            ->whereHas('clubs', fn($q) => $q->where('clubs.club_id', $clubId))
            ->whereDate('start_date', $date)
            ->orderBy('start_date')
            ->get();

        return view('calendar.day', compact('year', 'month', 'day', 'events', 'date'));
    }
}
