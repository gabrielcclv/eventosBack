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
    
    // Endpoint para completar/actualizar el perfil del usuario
    Route::put('/v1/users/profile', [UserApiController::class, 'updateProfile']);
    Route::patch('/v1/users/become-organizer', [UserApiController::class, 'becomeOrganizer']);
    Route::get('/v1/users/tickets', [UserApiController::class, 'myTickets']);

    // Rutas protegidas para Organizadores
    Route::middleware('organizer')->group(function () {
        Route::post('/v1/events', [EventApiController::class, 'store']);
        Route::put('/v1/events/{id}', [EventApiController::class, 'update']);
        Route::delete('/v1/events/{id}', [EventApiController::class, 'destroy']);
        
        // Endpoint dedicado para la imagen de portada
        Route::post('/v1/events/{id}/image', [EventApiController::class, 'updateImage']);
        
        // Endpoint para listar los inscritos de un evento
        Route::get('/v1/events/{id}/registrations', [EventApiController::class, 'getRegistrations']);
    });

    Route::post('/v1/events/{id}/register', [EventApiController::class, 'register']);
    Route::delete('/v1/events/{id}/register', [EventApiController::class, 'cancelRegistration']);
    Route::post('/v1/events/{id}/reviews', [EventApiController::class, 'storeReview']);
});