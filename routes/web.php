<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\PanelMembershipController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\SportFieldController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\FieldTypeController;
use App\Http\Controllers\PanelCoachEvaluationController;
use App\Http\Controllers\PanelReservationController;
use App\Http\Controllers\ActiveMembershipController;
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
    // PANEL ROUTES
    // --------------------------------------------------
    Route::prefix('panel')->group(function () {
        Route::get('/', [PanelController::class, 'index'])->name('panel.update.index');
        Route::get('/stats', [PanelController::class, 'stats'])->name('panel.stats');
        Route::patch('/profile', [PanelController::class, 'update'])->name('panel.profile.update');
        Route::delete('/profile', [PanelController::class, 'destroy'])->name('panel.profile.destroy');

        // Panel - Create Event (Admin & Coach only)
        Route::prefix('events')->middleware('admin_or_coach')->group(function () {
            Route::get('/create', [EventController::class, 'create'])->name('panel.events.create');
            Route::post('/', [EventController::class, 'store'])->name('panel.events.store');
        });

    });

    // --------------------------------------------------
    // COACH ROUTES
    // --------------------------------------------------
    Route::prefix('coach')->middleware('coach')->group(function () {
        Route::get('/players', [CoachController::class, 'players'])->name('coach.players');
        Route::get('/trainings', [CoachController::class, 'trainings'])->name('coach.trainings');
        Route::get('/events', [CoachController::class, 'events'])->name('coach.events');
    });

    // --------------------------------------------------
    // ADMIN ROUTES
    // --------------------------------------------------
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::prefix('users')->name('panel.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::patch('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('clubs')->name('panel.clubs.')->group(function () {
            Route::get('/create', [ClubController::class, 'create'])->name('create');
            Route::post('/', [ClubController::class, 'store'])->name('store');
            Route::get('/', [ClubController::class, 'adminIndex'])->name('index');
            Route::get('/{club}', [ClubController::class, 'show'])->name('show');
            Route::get('/{club}/edit', [ClubController::class, 'edit'])->name('edit');
            Route::patch('/{club}', [ClubController::class, 'update'])->name('update');
            Route::delete('/{club}', [ClubController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('events')->name('panel.events.')->group(function () {
            Route::get('/', [EventController::class, 'adminIndex'])->name('index');
            Route::get('/{event}', [EventController::class, 'adminShow'])->name('show');
            Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
            Route::patch('/{event}', [EventController::class, 'update'])->name('update');
            Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('sports')->name('panel.sports.')->group(function () {
            Route::get('/', [SportController::class, 'index'])->name('index');
            Route::get('/create', [SportController::class, 'create'])->name('create');
            Route::post('/', [SportController::class, 'store'])->name('store');
            Route::get('/{sport}/edit', [SportController::class, 'edit'])->name('edit');
            Route::patch('/{sport}', [SportController::class, 'update'])->name('update');
            Route::delete('/{sport}', [SportController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('sport-fields')->name('panel.sport-fields.')->group(function () {
            Route::get('/', [SportFieldController::class, 'index'])->name('index');
            Route::get('/create', [SportFieldController::class, 'create'])->name('create');
            Route::post('/', [SportFieldController::class, 'store'])->name('store');
            Route::get('/{sportField}/edit', [SportFieldController::class, 'edit'])->name('edit');
            Route::patch('/{sportField}', [SportFieldController::class, 'update'])->name('update');
            Route::delete('/{sportField}', [SportFieldController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('addresses')->name('panel.addresses.')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->name('index');
            Route::get('/create', [AddressController::class, 'create'])->name('create');
            Route::post('/', [AddressController::class, 'store'])->name('store');
            Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
            Route::patch('/{address}', [AddressController::class, 'update'])->name('update');
            Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('event-types')->name('panel.event-types.')->group(function () {
            Route::get('/', [EventTypeController::class, 'index'])->name('index');
            Route::get('/create', [EventTypeController::class, 'create'])->name('create');
            Route::post('/', [EventTypeController::class, 'store'])->name('store');
            Route::get('/{eventType}/edit', [EventTypeController::class, 'edit'])->name('edit');
            Route::patch('/{eventType}', [EventTypeController::class, 'update'])->name('update');
            Route::delete('/{eventType}', [EventTypeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('field-types')->name('panel.field-types.')->group(function () {
            Route::get('/', [FieldTypeController::class, 'index'])->name('index');
            Route::get('/create', [FieldTypeController::class, 'create'])->name('create');
            Route::post('/', [FieldTypeController::class, 'store'])->name('store');
            Route::get('/{fieldType}/edit', [FieldTypeController::class, 'edit'])->name('edit');
            Route::patch('/{fieldType}', [FieldTypeController::class, 'update'])->name('update');
            Route::delete('/{fieldType}', [FieldTypeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('memberships')->name('panel.memberships.')->group(function () {
            Route::get('/', [PanelMembershipController::class, 'index'])->name('index');
            Route::get('/create', [PanelMembershipController::class, 'create'])->name('create');
            Route::post('/', [PanelMembershipController::class, 'store'])->name('store');
            Route::get('/{memberClub}/edit', [PanelMembershipController::class, 'edit'])->name('edit');
            Route::patch('/{memberClub}', [PanelMembershipController::class, 'update'])->name('update');
            Route::delete('/{memberClub}', [PanelMembershipController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('coach-evaluations')->name('panel.coach-evaluations.')->group(function () {
            Route::get('/', [PanelCoachEvaluationController::class, 'index'])->name('index');
            Route::get('/{member}', [PanelCoachEvaluationController::class, 'show'])->name('show');
        });

        Route::prefix('reservations')->name('panel.reservations.')->group(function () {
            Route::get('/', [PanelReservationController::class, 'index'])->name('index');
            Route::get('/create', [PanelReservationController::class, 'create'])->name('create');
            Route::post('/', [PanelReservationController::class, 'store'])->name('store');
            Route::get('/{reservation}', [PanelReservationController::class, 'show'])->name('show');
            Route::get('/{reservation}/edit', [PanelReservationController::class, 'edit'])->name('edit');
            Route::patch('/{reservation}', [PanelReservationController::class, 'update'])->name('update');
            Route::delete('/{reservation}', [PanelReservationController::class, 'destroy'])->name('destroy');
        });
    });

    // --------------------------------------------------
    // EVENTS ROUTES
    // --------------------------------------------------
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::post('/{event}/register', [EventController::class, 'register'])->name('events.register');
        Route::post('/{event}/unregister', [EventController::class, 'unregister'])->name('events.unregister');
        
        // Admin & Coach only - Edit & Delete
        Route::middleware('admin_or_coach')->group(function () {
            Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
            Route::patch('/{event}', [EventController::class, 'update'])->name('events.update');
            Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        });
        
        // Generic route
        Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
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
});

require __DIR__.'/auth.php';
