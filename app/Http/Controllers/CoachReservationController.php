<?php
namespace App\Http\Controllers;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Club;
use App\Http\Requests\StoreEventRequest;
use App\Models\Reservation;
use App\Models\Event;
use App\Models\SportField;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\EventType;
use Illuminate\Support\Facades\DB;
use App\Enums\ReservationStatus;
use Illuminate\Support\Facades\Auth;

class CoachReservationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);
        $user = Auth::user();
        $membership = $user?->activeMembership();
        $mySportId = $membership?->club?->sport_id;
        $myMemberClubId = $membership?->member_club_id;

        $sportFieldIds = [];
        if ($mySportId) {
            $sportFieldIds = SportField::whereHas('sports', function($q) use ($mySportId) {
                $q->where('sports.sport_id', $mySportId);
            })->pluck('sport_field_id')->all();
        }

        $sportFieldOptions = SportField::whereHas('sports', function($q) use ($mySportId) {
            $q->where('sports.sport_id', $mySportId);
        })->orderBy('name')->pluck('name', 'sport_field_id')->toArray();
        $clubOptions = Club::where('sport_id', $mySportId)
            ->orderBy('name')
            ->pluck('name', 'club_id')
            ->toArray();

        $reservations = Reservation::query()
            ->when($request->filled('search'), fn($query) => $query->search($request->input('search')))
            ->when($request->filled('club_id'), fn($query) => $query->byClub($request->input('club_id')))
            ->when($request->filled('sport_field_id'), fn($query) => $query->bySportField($request->input('sport_field_id')))
            ->whereIn('sport_field_id', $sportFieldIds)
            ->with(['sportField', 'createdByMemberClub.club', 'createdByMemberClub.member'])
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
        $sportId = $membership->club?->sport_id;
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

        try {
            $user = Auth::user();
            $membership = $user?->activeMembership();
            abort_if(!$membership, 403, 'No club context.');
            $memberClubId = $membership->member_club_id;
            Reservation::create(array_merge(
                $request->validated(),
                [
                    'created_by_member_club_id' => $memberClubId,
                ]
            ));
            return redirect()->route('panel.coach.reservations.index')->with('success', 'Reservation created successfully!');
        } catch (QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to create reservation.');
        }
    }

    public function createEventFromReservation(Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        $membership = Auth::user()?->activeMembership();
        abort_if(!$membership, 403, 'No club context.');

        $sportId = $membership->club?->sport_id;

        $eventTypeOptions = EventType::where('sport_id', $sportId)
            ->orderBy('name')
            ->pluck('name', 'event_type_id')
            ->toArray();

        $clubOptions = Club::where('sport_id', $sportId)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->pluck('name', 'club_id')
            ->toArray();

        $selectedClubIds = [(string) $membership->club_id];

        return view('panel.coach.reservations.create-event', compact(
            'reservation', 'eventTypeOptions', 'clubOptions', 'selectedClubIds'
        ));
    }

    public function storeEventFromReservation(StoreEventRequest $request, Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        $membership = Auth::user()?->activeMembership();
        abort_if(!$membership, 403, 'No club context.');

        abort_if(
            in_array($reservation->status, [
                ReservationStatus::CANCELED,
                ReservationStatus::CONVERTED,
            ], true),
            422,
            'This reservation can no longer be converted into an event.'
        );

        $validated = $request->validated();
        $clubIds = $validated['club_ids'] ?? [];
        unset($validated['club_ids']);

        $validated['reservation_id'] = $reservation->reservation_id;
        $validated['sport_field_id'] = $reservation->sport_field_id;
        $validated['start_date'] = $reservation->start_date;
        $validated['end_date'] = $reservation->end_date;

        try {
            $event = DB::transaction(function () use ($validated, $clubIds, $reservation) {
                $event = Event::create($validated);
                $event->clubs()->sync($clubIds);

                $reservation->update([
                    'status' => ReservationStatus::CONVERTED->value,
                ]);

                return $event;
            });
        } catch (QueryException $exception) {
            $error = $this->mapEventTriggerError($exception);

            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }

            throw $exception;
        }

        return redirect()
            ->route('panel.coach.events.show', $event)
            ->with('success', 'Event created from reservation successfully!');
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        $reservation->load(['sportField.address', 'createdByMemberClub.member', 'createdByMemberClub.club']);
        $myMemberClubId = Auth::user()?->activeMembership()?->member_club_id;
        return view('panel.coach.reservations.show', compact('reservation', 'myMemberClubId'));
    }

    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $user = Auth::user();
        $membership = $user?->activeMembership();
        abort_if(!$membership, 403, 'No club context.');
        $sportId = $membership->club?->sport_id;
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
            return redirect()->route('panel.coach.reservations.show', $reservation)->with('success', 'Reservation updated successfully!');
        } catch (QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to update reservation.');
        }
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        try {
            $reservation->delete();
            return redirect()->route('panel.coach.reservations.index')->with('success', 'Reservation deleted successfully!');
        } catch (QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to delete reservation.');
        }
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
        if (str_contains($message, 'FIELD DOES NOT SUPPORT THE CLUB SPORT')) {
            return ['sport_field_id' => 'Selected field does not support selected sport.'];
        }
        if (str_contains($message, 'RESERVATION MUST BE CREATED BY AN ACTIVE MEMBER')) {
            return ['start_date' => 'You must be an active member to create a reservation.'];
        }
        return ['start_date' => 'Unable to save reservation due to time conflict or unsupported combination.'];
    }

    private function mapEventTriggerError(QueryException $exception): ?array
    {
        $message = strtoupper($exception->getMessage());
        $driverErrorCode = (int) ($exception->errorInfo[1] ?? 0);

        if ($driverErrorCode !== 1644 && !str_contains($message, 'SQLSTATE[45000]')) {
            return null;
        }

        if (str_contains($message, 'FIELD DOES NOT SUPPORT THIS SPORT')) {
            return ['sport_field_id' => 'Selected field does not support selected sport.'];
        }

        if (str_contains($message, 'EVENT CANNOT BE ITS OWN PARENT')) {
            return ['parent_event_id' => 'Event cannot be its own parent event.'];
        }
 
        if (str_contains($message, 'EVENT DOES NOT MATCH THE SELECTED RESERVATION')) {
            return ['reservation_id' => 'Event data must match the selected reservation.'];
        }

        if (str_contains($message, 'FIELD IS ALREADY RESERVED AT THIS TIME')) {
            return ['start_date' => 'Selected field is already reserved in this time range.'];
        }

        if (str_contains($message, 'FIELD ALREADY HAS AN EVENT AT THIS TIME')) {
            return ['start_date' => 'Selected field already has an event in this time range.'];
        }

        return ['start_date' => 'Unable to save event due to time conflict or unsupported combination.'];
    }
}