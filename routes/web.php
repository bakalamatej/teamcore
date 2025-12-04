<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Routes setup
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/events', function () {
    return view('events');
})->name('events');

Route::get('/club', function () {
    return view('club');
})->name('club');

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
});

require __DIR__.'/auth.php';