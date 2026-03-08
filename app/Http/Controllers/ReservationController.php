<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\SportField;
use App\Models\Club;
use App\Http\Requests\ReservationRequest;
use App\Enums\ReservationStatus;
use Illuminate\Http\Request;

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
    public function store(ReservationRequest $request)
    {
        $this->authorize('create', Reservation::class);

        $reservation = Reservation::create(array_merge(
            $request->validated(),
            ['status' => ReservationStatus::PENDING->value]
        ));
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation created successfully.');
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

        $reservation->update($request->validated());
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation updated successfully.');
    }

    /**
     * Delete reservation
     */
    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
    }

    public function approve(Reservation $reservation)
    {
        $this->authorize('approve', $reservation);

        $reservation->update(['status' => ReservationStatus::APPROVED->value]);
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation approved.');
    }

    public function reject(Reservation $reservation)
    {
        $this->authorize('reject', $reservation);

        $reservation->update(['status' => ReservationStatus::REJECTED->value]);
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation rejected.');
    }

    public function cancel(Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);

        $reservation->update(['status' => ReservationStatus::CANCELED->value]);
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation cancelled.');
    }
}
