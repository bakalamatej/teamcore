<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
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
    Route::get('/calendar', function () {
        return view('calendar');
    })->name('calendar');

    Route::get('/gallery', function () {
        return view('gallery');
    })->name('gallery');

    // --------------------------------------------------
    // CLUB ROUTES
    // --------------------------------------------------
    Route::prefix('clubs')->group(function () {
        Route::get('/', [ClubController::class, 'index'])->name('clubs.index');
        Route::get('/my-club', [ClubController::class, 'myClub'])->name('clubs.my');
        
        // Admin only - Edit & Delete
        Route::middleware('admin')->group(function () {
            Route::get('/{club}/edit', [ClubController::class, 'edit'])->name('clubs.edit');
            Route::patch('/{club}', [ClubController::class, 'update'])->name('clubs.update');
            Route::delete('/{club}', [ClubController::class, 'destroy'])->name('clubs.destroy');
        });
        
        // Generic route
        Route::get('/{club}', [ClubController::class, 'show'])->name('clubs.show');
    });

    // --------------------------------------------------
    // PANEL ROUTES
    // --------------------------------------------------
    Route::prefix('panel')->group(function () {
        Route::get('/', [PanelController::class, 'index'])->name('panel.index');
        Route::get('/stats', [PanelController::class, 'stats'])->name('panel.stats');
        Route::patch('/profile', [PanelController::class, 'update'])->name('panel.profile.update');
        Route::delete('/profile', [PanelController::class, 'destroy'])->name('panel.profile.destroy');

        // Panel - Create Event (Admin & Coach only)
        Route::prefix('events')->middleware('admin_or_coach')->group(function () {
            Route::get('/create', [EventController::class, 'create'])->name('events.create');
            Route::post('/', [EventController::class, 'store'])->name('events.store');
        });

        // Panel - Create Club (Admin only)
        Route::prefix('clubs')->middleware('admin')->group(function () {
            Route::get('/create', [ClubController::class, 'create'])->name('clubs.create');
            Route::post('/', [ClubController::class, 'store'])->name('clubs.store');
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
        Route::get('/users', [UserController::class, 'index'])->name('panel.users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('panel.users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('panel.users.edit');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('panel.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('panel.users.destroy');
        
        Route::get('/clubs', [ClubController::class, 'adminIndex'])->name('panel.clubs.index');
        Route::get('/events', [EventController::class, 'adminIndex'])->name('panel.events.index');
        
        Route::get('/sports', [SportController::class, 'index'])->name('panel.sports.index');
        Route::get('/sports/create', [SportController::class, 'create'])->name('panel.sports.create');
        Route::post('/sports', [SportController::class, 'store'])->name('panel.sports.store');
        Route::get('/sports/{sport}/edit', [SportController::class, 'edit'])->name('panel.sports.edit');
        Route::patch('/sports/{sport}', [SportController::class, 'update'])->name('panel.sports.update');
        Route::delete('/sports/{sport}', [SportController::class, 'destroy'])->name('panel.sports.destroy');
        
        Route::get('/sport-fields', [SportFieldController::class, 'index'])->name('panel.sport-fields.index');
        Route::get('/sport-fields/create', [SportFieldController::class, 'create'])->name('panel.sport-fields.create');
        Route::post('/sport-fields', [SportFieldController::class, 'store'])->name('panel.sport-fields.store');
        Route::get('/sport-fields/{sportField}/edit', [SportFieldController::class, 'edit'])->name('panel.sport-fields.edit');
        Route::patch('/sport-fields/{sportField}', [SportFieldController::class, 'update'])->name('panel.sport-fields.update');
        Route::delete('/sport-fields/{sportField}', [SportFieldController::class, 'destroy'])->name('panel.sport-fields.destroy');
        
        Route::get('/addresses', [AddressController::class, 'index'])->name('panel.addresses.index');
        Route::get('/addresses/create', [AddressController::class, 'create'])->name('panel.addresses.create');
        Route::post('/addresses', [AddressController::class, 'store'])->name('panel.addresses.store');
        Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('panel.addresses.edit');
        Route::patch('/addresses/{address}', [AddressController::class, 'update'])->name('panel.addresses.update');
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('panel.addresses.destroy');
        
        Route::get('/event-types', [EventTypeController::class, 'index'])->name('panel.event-types.index');
        Route::get('/event-types/create', [EventTypeController::class, 'create'])->name('panel.event-types.create');
        Route::post('/event-types', [EventTypeController::class, 'store'])->name('panel.event-types.store');
        Route::get('/event-types/{eventType}/edit', [EventTypeController::class, 'edit'])->name('panel.event-types.edit');
        Route::patch('/event-types/{eventType}', [EventTypeController::class, 'update'])->name('panel.event-types.update');
        Route::delete('/event-types/{eventType}', [EventTypeController::class, 'destroy'])->name('panel.event-types.destroy');

        Route::get('/field-types', [FieldTypeController::class, 'index'])->name('panel.field-types.index');
        Route::get('/field-types/create', [FieldTypeController::class, 'create'])->name('panel.field-types.create');
        Route::post('/field-types', [FieldTypeController::class, 'store'])->name('panel.field-types.store');
        Route::get('/field-types/{fieldType}/edit', [FieldTypeController::class, 'edit'])->name('panel.field-types.edit');
        Route::patch('/field-types/{fieldType}', [FieldTypeController::class, 'update'])->name('panel.field-types.update');
        Route::delete('/field-types/{fieldType}', [FieldTypeController::class, 'destroy'])->name('panel.field-types.destroy');

        Route::get('/memberships', [PanelMembershipController::class, 'index'])->name('panel.memberships.index');
        Route::get('/memberships/{member}/edit', [PanelMembershipController::class, 'edit'])->name('panel.memberships.edit');
        Route::patch('/memberships/{member}', [PanelMembershipController::class, 'update'])->name('panel.memberships.update');
        Route::post('/memberships/{member}/clubs', [PanelMembershipController::class, 'storeMemberClub'])->name('panel.memberships.club.store');

        Route::get('/coach-evaluations', [PanelCoachEvaluationController::class, 'index'])->name('panel.coach-evaluations.index');
        Route::get('/coach-evaluations/{member}', [PanelCoachEvaluationController::class, 'show'])->name('panel.coach-evaluations.show');
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
