<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\SportField;
use App\Models\Club;
use App\Models\MemberClub;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);

        $reservations = Reservation::active()
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
    public function create()
    {
        $this->authorize('create', Reservation::class);

        $sportFields = SportField::all();
        $clubs = Club::all();
        $memberClubs = Auth::user()->member?->clubMemberships ?? collect();
        return view('reservations.create', compact('sportFields', 'clubs', 'memberClubs'));
    }

    /**
     * Store new reservation
     */
    public function store(ReservationRequest $request)
    {
        $this->authorize('create', Reservation::class);

        $reservation = Reservation::create($request->validated());
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

        $sportFields = SportField::all();
        $clubs = Club::all();
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
}
