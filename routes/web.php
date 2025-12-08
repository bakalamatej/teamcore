<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// --------------------------------------------------
// PUBLIC ROUTES
// --------------------------------------------------
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/calendar', function () {
    return view('calendar');
})->name('calendar');

Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');

// --------------------------------------------------
// CLUB ROUTES
// --------------------------------------------------
Route::prefix('clubs')->middleware(['auth','admin'])->group(function () {
    Route::get('/create', [ClubController::class, 'create'])->name('clubs.create');
    Route::post('/', [ClubController::class, 'store'])->name('clubs.store');
    Route::get('/{club}/edit', [ClubController::class, 'edit'])->name('clubs.edit');
    Route::patch('/{club}', [ClubController::class, 'update'])->name('clubs.update');
    Route::delete('/{club}', [ClubController::class, 'destroy'])->name('clubs.destroy');
});

Route::prefix('clubs')->group(function () {
    Route::get('/', [ClubController::class, 'index'])->name('clubs.index');
    Route::get('/{club}', [ClubController::class, 'show'])->name('clubs.show');
    Route::get('/my-club', [ClubController::class, 'myClub'])->name('clubs.my');
});

// --------------------------------------------------
// PANEL ROUTES
// --------------------------------------------------
Route::prefix('panel')->middleware('auth')->group(function () {
    Route::get('/', [PanelController::class, 'index'])->name('panel.index');
    Route::get('/stats', [PanelController::class, 'stats'])->name('panel.stats');
    Route::patch('/profile', [PanelController::class, 'update'])->name('panel.profile.update');
    Route::delete('/profile', [PanelController::class, 'destroy'])->name('panel.profile.destroy');
});

// --------------------------------------------------
// COACH ROUTES
// --------------------------------------------------
Route::prefix('coach')->middleware(['auth','coach'])->group(function () {
    Route::get('/players', [CoachController::class, 'players'])->name('coach.players');
    Route::get('/trainings', [CoachController::class, 'trainings'])->name('coach.trainings');
    Route::get('/events', [CoachController::class, 'events'])->name('coach.events');
});

// --------------------------------------------------
// ADMIN ROUTES
// --------------------------------------------------
Route::prefix('admin')->middleware(['auth','admin'])->group(function () {
    Route::get('/clubs', [AdminController::class, 'clubs'])->name('admin.clubs');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/events', [AdminController::class, 'events'])->name('admin.events');
    Route::get('/types', [AdminController::class, 'types'])->name('admin.types');
    Route::get('/fields', [AdminController::class, 'fields'])->name('admin.fields');
});

// --------------------------------------------------
// EVENTS ROUTES
// --------------------------------------------------
Route::prefix('events')->middleware(['auth','admin_or_coach'])->group(function () {
    Route::get('/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/', [EventController::class, 'store'])->name('events.store');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::patch('/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');
});

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
});

require __DIR__.'/auth.php';
