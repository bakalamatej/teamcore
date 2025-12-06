<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Routes setup
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

Route::get('/calendar', function () {
    return view('calendar');
})->name('calendar');

Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');

// Auth only routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:admin')->group(function () {
        Route::get('/clubs/create', [ClubController::class, 'create'])->name('clubs.create');
        Route::post('/clubs', [ClubController::class, 'store'])->name('clubs.store');

        Route::get('/clubs/{club}/edit', [ClubController::class, 'edit'])->name('clubs.edit');
        Route::patch('/clubs/{club}', [ClubController::class, 'update'])->name('clubs.update');

        Route::delete('/clubs/{club}', [ClubController::class, 'destroy'])->name('clubs.destroy');
    });
});

Route::resource('events', EventController::class);

require __DIR__.'/auth.php';