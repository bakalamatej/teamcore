<?php
namespace App\Http\Controllers;
use App\Enums\ReservationStatus;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Club;
use App\Models\MemberClub;
use App\Models\Reservation;
use App\Models\Sport;
use App\Models\SportField;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CoachReservationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);
        $user = Auth::user();
        $membership = $user?->activeMembership();
        $mySportId = $membership?->sport_id;
        $myMemberClubId = $membership?->member_club_id;

        $sportFieldIds = [];
        if ($mySportId) {
            $sportFieldIds = SportField::whereHas('sports', function($q) use ($mySportId) {
                $q->where('sports.sport_id', $mySportId);
            })->pluck('sport_field_id')->all();
        }

        $sportFieldOptions = SportField::orderBy('name')->pluck('name', 'sport_field_id')->toArray();
        $clubOptions = Club::orderBy('name')->pluck('name', 'club_id')->toArray();

        $reservations = Reservation::query()
            ->when($request->filled('search'), fn ($query) => $query->search($request->input('search')))
            ->when($request->filled('club_id'), fn ($query) => $query->byClub($request->input('club_id')))
            ->when($request->filled('sport_field_id'), fn ($query) => $query->bySportField($request->input('sport_field_id')))
            ->whereIn('sport_field_id', $sportFieldIds)
            ->with(['sport', 'sportField', 'club', 'createdByMemberClub.member'])
            ->orderByDesc('created_at')
            ->paginate(10);
        if ($request->ajax()) {
            return view('panel.coach.reservations._table', compact('reservations', 'myMemberClubId'));
        }
        return view('panel.coach.reservations.index', compact('reservations', 'sportFieldOptions', 'clubOptions', 'myMemberClubId'));
    }
    
    public function create()
    {
        $this->authorize('create', Reservation::class);
        $user = Auth::user();
        $membership = $user?->activeMembership();
        abort_if(!$membership, 403, 'No club context.');
        $sportId = $membership->sport_id;
        $clubId = $membership->club_id;
        $memberClubId = $membership->member_club_id;

        $sportFieldOptions = SportField::whereHas('sports', function($q) use ($sportId) {
            $q->where('sports.sport_id', $sportId);
        })->orderBy('name')->pluck('name', 'sport_field_id')->toArray();

        return view('panel.coach.reservations.create', compact('sportFieldOptions', 'sportId', 'clubId', 'memberClubId'));
    }

    public function store(StoreReservationRequest $request)
    {
        $this->authorize('create', Reservation::class);
        $user = Auth::user();
        $membership = $user?->activeMembership();
        abort_if(!$membership, 403, 'No club context.');
        $sportId = $membership->sport_id;
        $clubId = $membership->club_id;
        $memberClubId = $membership->member_club_id;
        try {
            Reservation::create(array_merge(
                $request->validated(),
                [
                    'sport_id' => $sportId,
                    'club_id' => $clubId,
                    'created_by_member_club_id' => $memberClubId,
                    'status' => ReservationStatus::PENDING->value
                ]
            ));
        } catch (QueryException $exception) {
            $error = $this->mapReservationTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }
        return redirect()->route('panel.coach.reservations.index')->with('success', 'Reservation created successfully!');
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        $reservation->load(['sport', 'sportField.address', 'club', 'createdByMemberClub.member']);
        $myMemberClubId = Auth::user()?->activeMembership()?->member_club_id;
        return view('panel.coach.reservations.show', compact('reservation', 'myMemberClubId'));
    }

    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $user = Auth::user();
        $membership = $user?->activeMembership();
        abort_if(!$membership, 403, 'No club context.');
        $sportId = $membership->sport_id;
        $clubId = $membership->club_id;
        $memberClubId = $membership->member_club_id;

        $sportFieldOptions = SportField::whereHas('sports', function($q) use ($sportId) {
            $q->where('sports.sport_id', $sportId);
        })->orderBy('name')->pluck('name', 'sport_field_id')->toArray();

        return view('panel.coach.reservations.edit', compact('reservation', 'sportFieldOptions', 'sportId', 'clubId', 'memberClubId'));
    }

    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        try {
            $reservation->update($request->validated());
        } catch (QueryException $exception) {
            $error = $this->mapReservationTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }
        return redirect()->route('panel.coach.reservations.show', $reservation)->with('success', 'Reservation updated successfully!');
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);
        $reservation->delete();
        return redirect()->route('panel.coach.reservations.index')->with('success', 'Reservation deleted successfully!');
    }

    private function mapReservationTriggerError(QueryException $exception): ?array
    {
        $message = strtoupper($exception->getMessage());
        $driverErrorCode = (int) ($exception->errorInfo[1] ?? 0);
        if ($driverErrorCode !== 1644 && !str_contains($message, 'SQLSTATE[45000]')) {
            return null;
        }
        if (str_contains($message, 'FIELD IS ALREADY RESERVED AT THIS TIME')) {
            return ['start_date' => 'Selected field is already reserved in this time range.'];
        }
        if (str_contains($message, 'FIELD HAS AN EVENT AT THIS TIME')) {
            return ['start_date' => 'Selected field already has an event in this time range.'];
        }
        if (str_contains($message, 'FIELD DOES NOT SUPPORT THIS SPORT')) {
            return ['sport_field_id' => 'Selected field does not support selected sport.'];
        }
        if (str_contains($message, 'CLUB DOES NOT HAVE THIS SPORT ASSIGNED')) {
            return ['club_id' => 'Selected club does not have selected sport assigned.'];
        }
        return ['start_date' => 'Unable to save reservation due to time conflict or unsupported combination.'];
    }
}