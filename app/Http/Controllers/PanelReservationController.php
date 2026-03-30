<?php

namespace App\Http\Controllers;

use App\Enums\MemberClubRole;
use App\Enums\ReservationStatus;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Club;
use App\Models\MemberClub;
use App\Models\Reservation;
use App\Models\SportField;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelReservationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);

        $sportFieldOptions = SportField::orderBy('name')->pluck('name', 'sport_field_id')->toArray();
        $clubOptions = Club::orderBy('name')->pluck('name', 'club_id')->toArray();
        $statusOptions = collect(ReservationStatus::cases())
            ->mapWithKeys(fn (ReservationStatus $status) => [$status->value => ucfirst($status->value)])
            ->toArray();

        $reservations = Reservation::query()
            ->when($request->filled('search'), fn ($query) => $query->search($request->input('search')))
            ->when($request->filled('status'), fn ($query) => $query->byStatus($request->input('status')))
            ->when($request->filled('club_id'), fn ($query) => $query->byClub($request->input('club_id')))
            ->when($request->filled('sport_field_id'), fn ($query) => $query->bySportField($request->input('sport_field_id')))
            ->when(
                $request->filled('start_date_from') || $request->filled('end_date_to'),
                fn ($query) => $query->byDateRange($request->input('start_date_from'), $request->input('end_date_to'))
            )
            ->with(['sportField', 'createdByMemberClub.member', 'createdByMemberClub.club.sport'])
            ->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString();

        if ($request->ajax()) {
            return view('panel.admin.reservations._table', compact('reservations'));
        }

        return view('panel.admin.reservations.index', compact(
            'reservations',
            'sportFieldOptions',
            'clubOptions',
            'statusOptions'
        ));
    }

    public function create()
    {
        $this->authorize('create', Reservation::class);

        $sportFieldsBySport = $this->getSportFieldsBySportOptions();
        [$membershipOptions, $membershipMeta] = $this->getCoachMembershipData();

        return view('panel.admin.reservations.create', compact(
            'sportFieldsBySport',
            'membershipOptions',
            'membershipMeta'
        ));
    }

    public function store(StoreReservationRequest $request)
    {
        $this->authorize('create', Reservation::class);

        try {
            Reservation::create($request->validated());
            return redirect()->route('panel.admin.reservations.index')->with('success', 'Reservation created successfully!');

        } catch (QueryException $exception) {
            $error = $this->mapReservationTriggerError($exception);

            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }

            throw $exception;
        }

    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        $reservation->load([
            'sportField.address',
            'createdByMemberClub.member',
            'createdByMemberClub.club.sport',
        ]);

        return view('panel.admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $reservation->loadMissing([
            'sportField',
            'createdByMemberClub.member',
            'createdByMemberClub.club.sport',
        ]);

        $sportFieldsBySport = $this->getSportFieldsBySportOptions();
        [$membershipOptions, $membershipMeta] = $this->getCoachMembershipData();

        return view('panel.admin.reservations.edit', compact(
            'reservation',
            'sportFieldsBySport',
            'membershipOptions',
            'membershipMeta'
        ));
    }

    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        try {
            $reservation->update($request->validated());
            return redirect()->route('panel.admin.reservations.show', $reservation)->with('success', 'Reservation updated successfully!');

        } catch (QueryException $exception) {
            $error = $this->mapReservationTriggerError($exception);

            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }

            throw $exception;
        }

    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        try {
            $reservation->delete();
            return redirect()->route('panel.admin.reservations.index')->with('success', 'Reservation deleted successfully!');

        } catch (QueryException $exception) {
            $error = $this->mapReservationTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }

    }

    private function getSportFieldsBySportOptions(): array
    {
        return DB::table('sport_fields_sports')
            ->join('sport_fields', 'sport_fields_sports.sport_field_id', '=', 'sport_fields.sport_field_id')
            ->leftJoin('addresses', 'sport_fields.address_id', '=', 'addresses.address_id')
            ->orderBy('sport_fields.name')
            ->selectRaw("
                sport_fields_sports.sport_id,
                sport_fields.sport_field_id,
                CONCAT(
                    sport_fields.name,
                    ' (',
                    COALESCE(addresses.city, '-'),
                    ')'
                ) as label
            ")
            ->get()
            ->groupBy('sport_id')
            ->map(fn ($rows) => $rows->pluck('label', 'sport_field_id')->toArray())
            ->toArray();
    }

    private function getCoachMembershipData(): array
    {
        $rows = MemberClub::query()
            ->join('members', 'member_club.member_id', '=', 'members.member_id')
            ->join('clubs', 'member_club.club_id', '=', 'clubs.club_id')
            ->where('member_club.role', MemberClubRole::COACH->value)
            ->whereNull('member_club.left_at')
            ->whereNull('clubs.deleted_at')
            ->whereNull('members.deleted_at')
            ->orderBy('members.last_name')
            ->orderBy('members.first_name')
            ->get([
                'member_club.member_club_id',
                'member_club.club_id',
                'clubs.sport_id',
                'members.first_name',
                'members.last_name',
                'clubs.name as club_name',
            ]);

        $membershipOptions = $rows
            ->mapWithKeys(function ($row) {
                $label = trim($row->first_name . ' ' . $row->last_name);

                if (!empty($row->club_name)) {
                    $label .= ' - ' . $row->club_name;
                }

                return [(string) $row->member_club_id => $label];
            })
            ->toArray();

        $membershipMeta = $rows
            ->mapWithKeys(fn ($row) => [
                (string) $row->member_club_id => [
                    'club_id' => (string) $row->club_id,
                    'sport_id' => (string) $row->sport_id,
                ],
            ])
            ->toArray();

        return [$membershipOptions, $membershipMeta];
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
            return ['sport_field_id' => 'Selected field does not support the club sport.'];
        }

        if (str_contains($message, 'RESERVATION MUST BE CREATED BY AN ACTIVE MEMBER OF A CLUB')) {
            return ['created_by_member_club_id' => 'Selected membership must be active.'];
        }

        if (str_contains($message, 'END DATE MUST BE LATER THAN START DATE')) {
            return ['end_date' => 'End date must be later than the start date.'];
        }

        return ['start_date' => 'Unable to save reservation due to time conflict or invalid combination.'];
    }
}