<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\SportField;
use App\Models\Club;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Enums\ReservationStatus;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);

        $reservations = Reservation::query()
            ->when($request->filled('search'), 
                fn($q) => $q->search($request->input('search')))
            ->when($request->filled('status'), 
                fn($q) => $q->byStatus($request->input('status')))
            ->with('sportField', 'club')
            ->paginate(15);
        
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $this->authorize('create', Reservation::class);

        $sportFields = SportField::orderBy('name')->get();
        $clubs = Club::orderBy('name')->get();
        $memberClubs = $request->user()->member?->clubMemberships()->active()->with('club')->get() ?? collect();
        return view('reservations.create', compact('sportFields', 'clubs', 'memberClubs'));
    }

    /**
     * Store new reservation
     */
    public function store(StoreReservationRequest $request)
    {
        $this->authorize('create', Reservation::class);

        try {
            $reservation = Reservation::create(array_merge(
                $request->validated(),
                ['status' => ReservationStatus::APPROVED->value]
            ));
            return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation created successfully.');

        } catch (QueryException $exception) {
            $error = $this->mapReservationTriggerError($exception);

            if ($error !== null) {
                return back()
                    ->withInput()
                    ->withErrors($error);
            }

            throw $exception;
        }

    }

    /**
     * Display reservation details
     */
    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        $reservation->load('sportField', 'club', 'createdByMemberClub');
        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show edit form
     */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $sportFields = SportField::orderBy('name')->get();
        $clubs = Club::orderBy('name')->get();
        return view('reservations.edit', compact('reservation', 'sportFields', 'clubs'));
    }

    /**
     * Update reservation
     */
    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        try {
            $reservation->update($request->validated());
            return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation updated successfully.');

        } catch (QueryException $exception) {
            $error = $this->mapReservationTriggerError($exception);

            if ($error !== null) {
                return back()
                    ->withInput()
                    ->withErrors($error);
            }

            throw $exception;
        }

    }

    /**
     * Delete reservation
     */
    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        try {
            $reservation->delete();
            return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
        } catch (QueryException $exception) {
            return redirect()->back()->with('error', 'Unable to delete reservation.');
        }
    }

    public function approve(Reservation $reservation)
    {
        $this->authorize('approve', $reservation);

        $reservation->update(['status' => ReservationStatus::APPROVED->value]);
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation approved.');
    }

    public function cancel(Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);

        $reservation->update(['status' => ReservationStatus::CANCELED->value]);
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation cancelled.');
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
