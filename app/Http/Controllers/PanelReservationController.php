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
            ->with(['sport', 'sportField', 'club', 'createdByMemberClub.member'])
            ->orderByDesc('created_at')
            ->paginate(10);
        if ($request->ajax()) {
            return view('panel.admin.reservations._table', compact('reservations'));
        }
        return view('panel.admin.reservations.index', compact('reservations', 'sportFieldOptions', 'clubOptions', 'statusOptions'));
    }

    public function create()
    {
        $this->authorize('create', Reservation::class);
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $sportFieldsBySport = $this->getSportFieldsBySportOptions();
        $clubsBySport = $this->getClubsBySportOptions();
        $membershipsByClub = $this->getMemberClubsByClubOptions();

        return view('panel.admin.reservations.create', compact('sportOptions', 'sportFieldsBySport', 'clubsBySport', 'membershipsByClub'));
    }

    public function store(StoreReservationRequest $request)
    {
        $this->authorize('create', Reservation::class);
        try {
            Reservation::create(array_merge(
                $request->validated(),
                ['status' => ReservationStatus::PENDING->value]
            ));
        } catch (QueryException $exception) {
            $error = $this->mapReservationTriggerError($exception);
            if ($error !== null) {
                return back()->withInput()->withErrors($error);
            }
            throw $exception;
        }
        return redirect()->route('panel.admin.reservations.index')->with('success', 'Reservation created successfully!');
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        $reservation->load(['sport', 'sportField.address', 'club', 'createdByMemberClub.member']);
        return view('panel.admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $sportOptions = Sport::orderBy('name')->pluck('name', 'sport_id')->toArray();
        $sportFieldsBySport = $this->getSportFieldsBySportOptions();
        $clubsBySport = $this->getClubsBySportOptions();
        $membershipsByClub = $this->getMemberClubsByClubOptions();

        return view('panel.admin.reservations.edit', compact('reservation', 'sportOptions', 'sportFieldsBySport', 'clubsBySport', 'membershipsByClub'));
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
        return redirect()->route('panel.admin.reservations.show', $reservation)->with('success', 'Reservation updated successfully!');
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);
        $reservation->delete();
        return redirect()->route('panel.admin.reservations.index')->with('success', 'Reservation deleted successfully!');
    }

    private function getSportFieldsBySportOptions(): array
    {
        return DB::table('sport_fields_sports')
            ->join('sport_fields', 'sport_fields_sports.sport_field_id', '=', 'sport_fields.sport_field_id')
            ->whereNull('sport_fields.deleted_at')
            ->orderBy('sport_fields.name')
            ->get(['sport_fields_sports.sport_id', 'sport_fields.sport_field_id', 'sport_fields.name'])
            ->groupBy('sport_id')
            ->map(fn($rows) => $rows->pluck('name', 'sport_field_id')->toArray())
            ->toArray();
    }

    private function getClubsBySportOptions(): array
    {
        return DB::table('club_sport')
            ->join('clubs', 'club_sport.club_id', '=', 'clubs.club_id')
            ->whereNull('clubs.deleted_at')
            ->orderBy('clubs.name')
            ->get(['club_sport.sport_id', 'clubs.club_id', 'clubs.name'])
            ->groupBy('sport_id')
            ->map(fn($rows) => $rows->pluck('name', 'club_id')->toArray())
            ->toArray();
    }

    private function getMemberClubsByClubOptions(): array
    {
        return MemberClub::query()
            ->leftJoin('members', 'member_club.member_id', '=', 'members.member_id')
            ->leftJoin('clubs', 'member_club.club_id', '=', 'clubs.club_id')
            ->orderBy('members.last_name')
            ->orderBy('members.first_name')
            ->selectRaw(
                "member_club.club_id, member_club.member_club_id, TRIM(CONCAT(COALESCE(members.first_name, ''), ' ', COALESCE(members.last_name, ''))) as label"
            )
            ->get()
            ->groupBy('club_id')
            ->map(fn($rows) => $rows->pluck('label', 'member_club_id')->toArray())
            ->toArray();
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