<?php

namespace App\Http\Controllers;

use App\Models\MemberClub;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\MemberClubRole;
use App\Enums\EventStatus;
use App\Enums\ReservationStatus;
use App\Models\Club;
use App\Models\Reservation;

class CoachController extends Controller
{
    /**
     * Display players managed by the coach
     */
    public function players(Request $request)
    {
        $this->authorize('viewAny', MemberClub::class);

        $coachClubs = $this->getCoachClubs();

        // Get all players (non-coaches) in coach's clubs
        $players = MemberClub::query()
            ->whereIn('club_id', $coachClubs)
            ->where('role', MemberClubRole::PLAYER->value)
            ->whereNull('left_at')
            ->with('member.user', 'club')
            ->when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('club_id'),
                fn($q) => $q->byClub($request->input('club_id')))
            ->paginate(15);

        $clubs = Club::whereIn('club_id', $coachClubs)->get();

        return view('coach.players', compact('players', 'clubs'));
    }

    /**
     * Display trainings scheduled by the coach
     */
    public function trainings(Request $request)
    {
        $this->authorize('viewAny', MemberClub::class);

        $coachClubs = $this->getCoachClubs();

        // Get reservations/trainings for coach's clubs
        $trainings = Reservation::query()
            ->whereIn('club_id', $coachClubs)
            ->with('sportField', 'club')
            ->when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('status'),
                fn($q) => $q->byStatus($request->input('status')))
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        $statuses = ReservationStatus::cases();

        return view('coach.trainings', compact('trainings', 'statuses'));
    }

    /**
     * Display events managed by the coach
     */
    public function events(Request $request)
    {
        $this->authorize('viewAny', MemberClub::class);

        $coachClubs = $this->getCoachClubs();

        // Get events for coach's clubs
        $events = Event::query()
            ->whereHas('clubs', function ($q) use ($coachClubs) {
                $q->whereIn('club_id', $coachClubs);
            })
            ->with('eventType', 'sportField')
            ->when($request->filled('search'),
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('status'),
                fn($q) => $q->byStatus($request->input('status')))
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        $eventStatuses = EventStatus::cases();

        return view('coach.events', compact('events', 'eventStatuses'));
    }

    private function getCoachClubs(): array
    {
        $membership = Auth::user()?->activeMembership();

        if (!$membership) {
            return [];
        }

        $role = $membership->role;
        $roleValue = is_object($role) && isset($role->value) ? $role->value : (string) $role;

        if ($roleValue !== MemberClubRole::COACH->value) {
            return [];
        }

        return [(int) $membership->club_id];
    }
}
