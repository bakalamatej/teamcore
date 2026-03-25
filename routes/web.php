<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\CoachEventController;
use App\Http\Controllers\CoachReservationController;
use App\Http\Controllers\CoachEvaluationController;
use App\Http\Controllers\CoachClubController;
use App\Http\Controllers\PanelMembershipController;
use App\Http\Controllers\MemberStatisticsController;
use App\Http\Controllers\EventResultsController;    
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\SportFieldController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\FieldTypeController;
use App\Http\Controllers\PanelCoachEvaluationController;
use App\Http\Controllers\CoachTournamentController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\PanelReservationController;
use App\Http\Controllers\ActiveMembershipController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

// --------------------------------------------------
// PUBLIC ROUTES 
// --------------------------------------------------
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// --------------------------------------------------
// PROTECTED ROUTES 
// --------------------------------------------------
Route::middleware(['auth'])->group(function () {
    Route::post('/memberships/active', [ActiveMembershipController::class, 'update'])
        ->name('memberships.active.update');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/{year}/{month}/{day}', [CalendarController::class, 'showDay'])->name('calendar.day');

    Route::get('/gallery', function () {
        return view('gallery');
    })->name('gallery');

    // --------------------------------------------------
    // CLUB ROUTES
    // --------------------------------------------------
    Route::prefix('clubs')->group(function () {
        Route::get('/my-club', [ClubController::class, 'myClub'])->name('clubs.my');
        Route::get('/{club}', [ClubController::class, 'publicShow'])->name('clubs.show');
    });

    // --------------------------------------------------
    // EVENTS ROUTES
    // --------------------------------------------------
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::post('/{event}/register', [EventController::class, 'register'])->name('events.register');
        Route::post('/{event}/unregister', [EventController::class, 'unregister'])->name('events.unregister');
        Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
        Route::post('/{event}/coaches/{memberClubId}/rate', [CoachEvaluationController::class, 'storeFromEvent'])
            ->name('events.coach.rate');
    });

    // --------------------------------------------------
    // FILE ROUTES
    // --------------------------------------------------
    Route::prefix('files')->group(function () {
        Route::post('/{modelType}/{modelId}/upload', [FileController::class, 'upload'])->name('files.upload');
        Route::get('/{modelType}/{modelId}', [FileController::class, 'list'])->name('files.list');
        Route::get('/{modelType}/{modelId}/category/{category}', [FileController::class, 'listByCategory'])->name('files.list.category');
        Route::delete('/{modelType}/{modelId}/{fileId}', [FileController::class, 'delete'])->name('files.delete');
        Route::get('/download/{fileId}', [FileController::class, 'download'])->name('files.download');
    });

    // --------------------------------------------------
    // PANEL ROUTES
    // --------------------------------------------------
    Route::prefix('panel')->name('panel.')->group(function () {
        Route::get('/', [PanelController::class, 'index'])->name('update.index');
        Route::get('/statistics', [MemberStatisticsController::class, 'index'])->name('statistics.index');
        Route::patch('/profile', [PanelController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [PanelController::class, 'destroy'])->name('profile.destroy');
        Route::get('/results', [EventResultsController::class, 'index'])->name('results.index');
        Route::prefix('my-evaluations')->name('my-evaluations.')->group(function () {
            Route::get('/', [PanelCoachEvaluationController::class, 'myIndex'])->name('index');
            Route::get('/{evaluation}/edit', [PanelCoachEvaluationController::class, 'editEvaluation'])->name('edit');
            Route::patch('/{evaluation}', [PanelCoachEvaluationController::class, 'updateEvaluation'])->name('update');
            Route::delete('/{evaluation}', [PanelCoachEvaluationController::class, 'destroyEvaluation'])->name('destroy');
        });
    });

    // --------------------------------------------------
    // COACH ROUTES
    // --------------------------------------------------
    Route::prefix('panel/coach')->name('panel.coach.')->middleware('coach')->group(function () {
        Route::prefix('players')->name('players.')->group(function () {
            Route::get('/', [PlayerController::class, 'index'])->name('index');
            Route::get('/{player}', [PlayerController::class, 'show'])->name('show');
            Route::delete('/{player}', [PlayerController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/create', [CoachEventController::class, 'create'])->name('create');
            Route::post('/', [CoachEventController::class, 'store'])->name('store');
            Route::get('/', [CoachEventController::class, 'index'])->name('index');
            Route::get('/{event}', [CoachEventController::class, 'show'])->name('show');
            Route::get('/{event}/edit', [CoachEventController::class, 'edit'])->name('edit');
            Route::patch('/{event}', [CoachEventController::class, 'update'])->name('update');
            Route::delete('/{event}', [CoachEventController::class, 'destroy'])->name('destroy');
            Route::get('/{event}/results', [EventResultsController::class, 'edit'])->name('results.edit');
            Route::post('/{event}/results', [EventResultsController::class, 'store'])->name('results.store');
        });
        Route::prefix('tournaments')->name('tournaments.')->group(function () {
            Route::get('/', [CoachTournamentController::class, 'index'])->name('index');
            Route::get('/create', [CoachTournamentController::class, 'create'])->name('create');
            Route::post('/', [CoachTournamentController::class, 'store'])->name('store');
            Route::get('/{tournament}', [CoachTournamentController::class, 'show'])->name('show');
            Route::get('/{tournament}/edit', [CoachTournamentController::class, 'edit'])->name('edit');
            Route::patch('/{tournament}', [CoachTournamentController::class, 'update'])->name('update');
            Route::delete('/{tournament}', [CoachTournamentController::class, 'destroy'])->name('destroy');
            Route::post('/{tournament}/children/attach', [CoachTournamentController::class, 'attachChild'])->name('children.attach');
            Route::delete('/{tournament}/children/{event}/detach', [CoachTournamentController::class, 'detachChild'])->name('children.detach');
            Route::get('/{tournament}/children/create', [CoachTournamentController::class, 'createChild'])->name('children.create');
            Route::post('/{tournament}/children', [CoachTournamentController::class, 'storeChild'])->name('children.store');
        });
        Route::prefix('clubs')->name('clubs.')->group(function () {
            Route::get('/', [CoachClubController::class, 'index'])->name('index');
            Route::get('/{club}', [CoachClubController::class, 'show'])->name('show');
            Route::get('/{club}/edit', [CoachClubController::class, 'edit'])->name('edit');
            Route::patch('/{club}', [CoachClubController::class, 'update'])->name('update');
        });
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/', [CoachReservationController::class, 'index'])->name('index');
            Route::get('/create', [CoachReservationController::class, 'create'])->name('create');
            Route::post('/', [CoachReservationController::class, 'store'])->name('store');
            Route::get('/{reservation}', [CoachReservationController::class, 'show'])->name('show');
            Route::get('/{reservation}/edit', [CoachReservationController::class, 'edit'])->name('edit');
            Route::patch('/{reservation}', [CoachReservationController::class, 'update'])->name('update');
            Route::delete('/{reservation}', [CoachReservationController::class, 'destroy'])->name('destroy');
            Route::get('/{reservation}/create-event', [CoachReservationController::class, 'createEventFromReservation'])->name('create-event');
            Route::post('/{reservation}/store-event', [CoachReservationController::class, 'storeEventFromReservation'])->name('store-event');
        });
        Route::prefix('recieved-evaluations')->name('recieved-evaluations.')->group(function () {
            Route::get('/', [PanelCoachEvaluationController::class, 'recievedIndex'])->name('index');
        });
    });

    // --------------------------------------------------
    // ADMIN ROUTES
    // --------------------------------------------------
    Route::prefix('panel/admin')->name('panel.admin.')->middleware('admin')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::patch('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('clubs')->name('clubs.')->group(function () {
            Route::get('/create', [ClubController::class, 'create'])->name('create');
            Route::post('/', [ClubController::class, 'store'])->name('store');
            Route::get('/', [ClubController::class, 'adminIndex'])->name('index');
            Route::get('/{club}', [ClubController::class, 'show'])->name('show');
            Route::get('/{club}/edit', [ClubController::class, 'edit'])->name('edit');
            Route::patch('/{club}', [ClubController::class, 'update'])->name('update');
            Route::delete('/{club}', [ClubController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/create', [EventController::class, 'create'])->name('create');
            Route::post('/', [EventController::class, 'store'])->name('store');
            Route::get('/', [EventController::class, 'adminIndex'])->name('index');
            Route::get('/{event}', [EventController::class, 'adminShow'])->name('show');
            Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
            Route::patch('/{event}', [EventController::class, 'update'])->name('update');
            Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
            Route::get('/{event}/results', [EventResultsController::class, 'adminEdit'])->name('results.edit');
            Route::post('/{event}/results', [EventResultsController::class, 'adminStore'])->name('results.store');
        });

        Route::prefix('tournaments')->name('tournaments.')->group(function () {
            Route::get('/', [TournamentController::class, 'index'])->name('index');
            Route::get('/create', [TournamentController::class, 'create'])->name('create');
            Route::post('/', [TournamentController::class, 'store'])->name('store');
            Route::get('/{tournament}', [TournamentController::class, 'show'])->name('show');
            Route::get('/{tournament}/edit', [TournamentController::class, 'edit'])->name('edit');
            Route::patch('/{tournament}', [TournamentController::class, 'update'])->name('update');
            Route::delete('/{tournament}', [TournamentController::class, 'destroy'])->name('destroy');

            // Child event management
            Route::post('/{tournament}/children/attach', [TournamentController::class, 'attachChild'])->name('children.attach');
            Route::delete('/{tournament}/children/{event}/detach', [TournamentController::class, 'detachChild'])->name('children.detach');
            Route::get('/{tournament}/children/create', [TournamentController::class, 'createChild'])->name('children.create');
            Route::post('/{tournament}/children', [TournamentController::class, 'storeChild'])->name('children.store');
        });

        Route::prefix('sports')->name('sports.')->group(function () {
            Route::get('/', [SportController::class, 'index'])->name('index');
            Route::get('/create', [SportController::class, 'create'])->name('create');
            Route::post('/', [SportController::class, 'store'])->name('store');
            Route::get('/{sport}/edit', [SportController::class, 'edit'])->name('edit');
            Route::patch('/{sport}', [SportController::class, 'update'])->name('update');
            Route::delete('/{sport}', [SportController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('sport-fields')->name('sport-fields.')->group(function () {
            Route::get('/', [SportFieldController::class, 'index'])->name('index');
            Route::get('/create', [SportFieldController::class, 'create'])->name('create');
            Route::post('/', [SportFieldController::class, 'store'])->name('store');
            Route::get('/{sportField}/edit', [SportFieldController::class, 'edit'])->name('edit');
            Route::patch('/{sportField}', [SportFieldController::class, 'update'])->name('update');
            Route::delete('/{sportField}', [SportFieldController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('addresses')->name('addresses.')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->name('index');
            Route::get('/create', [AddressController::class, 'create'])->name('create');
            Route::post('/', [AddressController::class, 'store'])->name('store');
            Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
            Route::patch('/{address}', [AddressController::class, 'update'])->name('update');
            Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('event-types')->name('event-types.')->group(function () {
            Route::get('/', [EventTypeController::class, 'index'])->name('index');
            Route::get('/create', [EventTypeController::class, 'create'])->name('create');
            Route::post('/', [EventTypeController::class, 'store'])->name('store');
            Route::get('/{eventType}/edit', [EventTypeController::class, 'edit'])->name('edit');
            Route::patch('/{eventType}', [EventTypeController::class, 'update'])->name('update');
            Route::delete('/{eventType}', [EventTypeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('field-types')->name('field-types.')->group(function () {
            Route::get('/', [FieldTypeController::class, 'index'])->name('index');
            Route::get('/create', [FieldTypeController::class, 'create'])->name('create');
            Route::post('/', [FieldTypeController::class, 'store'])->name('store');
            Route::get('/{fieldType}/edit', [FieldTypeController::class, 'edit'])->name('edit');
            Route::patch('/{fieldType}', [FieldTypeController::class, 'update'])->name('update');
            Route::delete('/{fieldType}', [FieldTypeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('memberships')->name('memberships.')->group(function () {
            Route::get('/', [PanelMembershipController::class, 'index'])->name('index');
            Route::get('/create', [PanelMembershipController::class, 'create'])->name('create');
            Route::post('/', [PanelMembershipController::class, 'store'])->name('store');
            Route::get('/{memberClub}/edit', [PanelMembershipController::class, 'edit'])->name('edit');
            Route::patch('/{memberClub}', [PanelMembershipController::class, 'update'])->name('update');
            Route::delete('/{memberClub}', [PanelMembershipController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('coach-evaluations')->name('coach-evaluations.')->group(function () {
            Route::get('/', [PanelCoachEvaluationController::class, 'index'])->name('index');
            Route::get('/{member}', [PanelCoachEvaluationController::class, 'show'])->name('show');
        });

        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/', [PanelReservationController::class, 'index'])->name('index');
            Route::get('/create', [PanelReservationController::class, 'create'])->name('create');
            Route::post('/', [PanelReservationController::class, 'store'])->name('store');
            Route::get('/{reservation}', [PanelReservationController::class, 'show'])->name('show');
            Route::get('/{reservation}/edit', [PanelReservationController::class, 'edit'])->name('edit');
            Route::patch('/{reservation}', [PanelReservationController::class, 'update'])->name('update');
            Route::delete('/{reservation}', [PanelReservationController::class, 'destroy'])->name('destroy');
        });
    });    
});

require __DIR__.'/auth.php';
