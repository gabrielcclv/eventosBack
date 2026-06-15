<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\EventViewController;
use App\Http\Controllers\Web\AuthViewController;

Route::get('/', [EventViewController::class, 'welcome'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthViewController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthViewController::class, 'login']);
    Route::get('/register', [AuthViewController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthViewController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthViewController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [EventViewController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-tickets', [EventViewController::class, 'myTickets'])->name('tickets');
    
    Route::get('/events', [EventViewController::class, 'index'])->name('events.index');
    
    Route::post('/events/{event}/register', [EventViewController::class, 'register'])->name('events.register');
    Route::post('/events/{event}/cancel-registration', [EventViewController::class, 'cancelRegistration'])->name('events.cancel-registration');
    Route::post('/events/{event}/review', [EventViewController::class, 'storeReview'])->name('events.review');
    
    Route::middleware('organizer')->group(function () {
        Route::get('/events/create', [EventViewController::class, 'create'])->name('events.create');
        Route::post('/events', [EventViewController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [EventViewController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventViewController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventViewController::class, 'destroy'])->name('events.destroy');
        Route::get('/my-events', [EventViewController::class, 'myEvents'])->name('my-events');
        Route::get('/events/{event}/check-in', [EventViewController::class, 'checkInPage'])->name('events.check-in');
        Route::post('/events/{event}/check-in', [EventViewController::class, 'checkIn'])->name('events.check-in-store');
    });
    

    Route::get('/events/{event}', [EventViewController::class, 'show'])->name('events.show');
    
    Route::post('/become-organizer', [EventViewController::class, 'becomeOrganizer'])->name('become-organizer');
});