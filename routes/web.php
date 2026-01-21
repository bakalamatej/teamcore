<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
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
    // Calendar & Gallery
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
        
        // Generic route at the end
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
        Route::get('/clubs', [AdminController::class, 'clubs'])->name('admin.clubs');
        Route::get('/events', [AdminController::class, 'events'])->name('admin.events');
        Route::get('/types', [AdminController::class, 'types'])->name('admin.types');
        Route::get('/fields', [AdminController::class, 'fields'])->name('admin.fields');
    });

    // --------------------------------------------------
    // EVENTS ROUTES
    // --------------------------------------------------
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        
        // Admin & Coach only - Edit & Delete
        Route::middleware('admin_or_coach')->group(function () {
            Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
            Route::patch('/{event}', [EventController::class, 'update'])->name('events.update');
            Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        });
        
        // Generic route at the end
        Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
    });
});

require __DIR__.'/auth.php';
