<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventApiController;
use App\Http\Controllers\UserApiController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);

Route::get('/v1/events', [EventApiController::class, 'index']);
Route::get('/v1/events/{id}', [EventApiController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::patch('/v1/users/become-organizer', [UserApiController::class, 'becomeOrganizer']);
    Route::get('/v1/users/tickets', [UserApiController::class, 'myTickets']);

    Route::post('/v1/events', [EventApiController::class, 'store']);
    Route::put('/v1/events/{id}', [EventApiController::class, 'update']);
    Route::delete('/v1/events/{id}', [EventApiController::class, 'destroy']);
    Route::post('/v1/events/{id}/check-in', [EventApiController::class, 'checkIn']);

    Route::post('/v1/events/{id}/register', [EventApiController::class, 'register']);
    Route::delete('/v1/events/{id}/register', [EventApiController::class, 'cancelRegistration']);
    Route::post('/v1/events/{id}/reviews', [EventApiController::class, 'storeReview']);
});