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
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $events = $this->getEventsForMonth($year, $month);

        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $firstDay = Carbon::create($year, $month, 1)->dayOfWeekIso;
        $weekdays = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
        $yearOptions = collect(range(now()->year - 3, now()->year + 1))
            ->mapWithKeys(fn($y) => [$y => $y])
            ->toArray();
        $monthOptions = collect(range(1, 12))
            ->mapWithKeys(fn($m) => [$m => Carbon::create($year, $m, 1)->format('F')])
            ->toArray();

        return view('calendar.index', compact(
            'year', 'month', 'events',
            'daysInMonth', 'firstDay', 'weekdays',
            'yearOptions', 'monthOptions'
        ));
    }

    public function showDay(Request $request, $year, $month, $day)
    {
        $date = Carbon::create($year, $month, $day);

        $events = $this->getRegisteredEvents()
            ->whereDate('start_date', $date)
            ->orderBy('start_date')
            ->get();

        return view('calendar.day', compact('year', 'month', 'day', 'events', 'date'));
    }

    private function getRegisteredEvents()
    {
        $membership = Auth::user()?->activeMembership();
        abort_if(!$membership, 403);

        return Event::query()
            ->whereHas('memberClubs', fn($q) =>
                $q->where('event_member.member_club_id', $membership->member_club_id)
            );
    }

    private function getEventsForMonth(int $year, int $month)
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return $this->getRegisteredEvents()
            ->whereBetween('start_date', [$start, $end])
            ->orderBy('start_date')
            ->get();
    }
}